---
name: auto-docs
description: Check whether the open SmartFlow project has documentation under docs/ at the repository root, determine its completeness against a target inventory built from the repo, and generate missing or outdated doc files (sections, elements, post-types, field-groups, coding-standard) as LLM-readable German Markdown with YAML frontmatter. Use when the user asks to document the project, generate or update the docs, or runs /auto-docs (with or without an element parameter) — and automatically as the final step after building or substantially changing a WST Section, CPT, element, or plugin feature, then scoped to just that element. Writes only under docs/ and, when needed, the .gitignore allowlist.
---

# Auto Docs

Generates and maintains the project documentation under `docs/` at the repository root. In the SmartFlow workspace the docs layer is also where the Section and CPT work records live: `docs/sections/<slug>.md` and `docs/post-types/<resource>.md` are one file per element that serves both as the durable work record of the WST run (including the `Visual QA Targets` matrix the frontend QA pass fills) and as the permanent documentation. There is no separate handoff file; the former handoff content lives in these docs.

Content generation always runs through worker subagents — the main agent orchestrates, one worker per element — so the main context stays small, even for a single element. `docs/` is outside the child theme deploy path and never reaches the server.

Submitted by Tobias Uhl (Confluence skill intake); adapted for the SmartFlow single-workspace model.

## Invocation Modes

| Mode | Behavior |
|---|---|
| `/auto-docs` without parameter | Full run: generate all missing and outdated docs |
| `/auto-docs <name>` (for example `/auto-docs intro`) | Only this element. Match `<name>` fuzzily across the target inventory (sections, elements, CPTs, categories); ask briefly on ambiguity |
| Auto-trigger after a Section/CPT/element build or a frontend QA pass | Document only the element that was just built or changed (plus affected README indexes) |

## Target Structure

```text
docs/
├── README.md            # Overall entry point (generated index of all categories)
├── sections/            # one .md per WST Section (= Section work record) + README.md index
├── elements/            # one .md per element + README.md index
├── post-types/          # one .md per CPT (= CPT work record) + README.md index
├── field-groups/        # one .md per ACF field group + README.md index
└── coding-standard/     # php.md, js.md, css.md, scss.md, html.md + README.md
```

Canonical categories: `sections`, `elements`, `post-types`, `field-groups`, `coding-standard`. Create additional categories only when the project clearly provides them (for example project-owned plugin code) — never invent them. Projects migrated from the submitted original may still use the legacy folder names (`sektionen`, `elemente`, `cpt`, `fieldgroups`); read the project convention from `PROJECT-CONTEXT.md` and do not rename existing folders without an explicit user decision.

## Frontmatter Required Fields

Every generated file starts with YAML frontmatter. Required:

```yaml
title: "<Titel>"
category: <sections|elements|post-types|field-groups|coding-standard|...>
slug: <slug>
source_files: [<repo-relative paths of the documented source files>]
related_docs: [<relative links to other docs/ files>]
generated: <YYYY-MM-DD>
status: <complete|partial|todo>
```

Category-specific fields are added on top (see the bundled templates and the `cpt-docs` Skill). Content language of the generated docs: **German**.

## Workflow (Main Agent)

```text
- [ ] 0. Read PROJECT-CONTEXT.md (child theme, paths, conventions, docs layer location)
- [ ] 1. Current state: read docs/ (file list plus frontmatter only, no bodies)
- [ ] 2. Build the target inventory from the repo
- [ ] 3. Determine the delta: missing plus outdated
- [ ] 4. field-groups: generate from the PHP field-group sources (legacy JSON export via the bundled script)
- [ ] 5. Generate LLM files through worker subagents (one subtask per element)
- [ ] 6. Update the README indexes and docs/README.md
- [ ] 7. Verify tracking (.gitignore allowlist when the project uses one) and report
```

### 2. Target Inventory

| Category | Source in the repo | Doc file |
|---|---|---|
| sections | `themes/<child-theme>/smart-template-builder/sections/*.php` | `docs/sections/<slug>.md` |
| elements | `smart-template-builder/elements/` and/or `styles/elements/` (per element: template plus its CSS) | `docs/elements/<slug>.md` |
| post-types | `smart-template-builder/post-types/*/` | `docs/post-types/<resource>.md` |
| field-groups | `smart-template-builder/acf/field-groups/*.php` (SmartFlow standard) or the newest `acf_export/acf-export-*.json` (legacy) | `docs/field-groups/<slug>.md` |
| coding-standard | Project code per language (php, js, css, scss, html) | `docs/coding-standard/<language>.md` |

### 3. Delta (Completeness Check)

An element counts as incomplete when:

- the doc file is **missing**, or
- it is **outdated**: the last change of a source file is newer than the doc. Compare through Git, not file mtime: `git log -1 --format=%ci -- <path>` for the source(s) versus the doc file. Untracked source files always count as newer.

A doc whose only newer content is work-record progress (QA results, deploy state) is not outdated. Report the delta to the user before generating: what is missing, what is outdated, what is current (and skipped).

### 4. field-groups

The SmartFlow standard registers all ACF field definitions as PHP field groups under `smart-template-builder/acf/field-groups/` (`acf-php-field-groups` Rule). Those PHP files are the documentation source:

1. One worker subtask per PHP field-group file, using [templates/field-group.md](templates/field-group.md): overview (group key, active, location rules), the flat field table, and the per-field detail (key, type, required, conditional logic, choices, clone targets with `parent_layout`). Everything comes from the PHP source; nothing is invented.
2. Cross-check over the status bridge when available: `GET /wp-json/wso/v1/status` lists the registered groups with `local: php`. A documented group missing there is recorded as an open point (`status: partial`), not silently ignored.

Legacy fallback — only when the project still maintains an admin JSON export instead of PHP groups: copy [scripts/generate.js](scripts/generate.js) to `docs/field-groups/generate.js` (when missing or older than the Skill copy) and run `node docs/field-groups/generate.js` from the repository root; it picks the newest `acf_export/acf-export-*.json`, writes one file per group, and generates its own `docs/field-groups/README.md` — do not overwrite that one in step 6. When neither PHP groups nor a JSON export exist, inform the user immediately and skip field-groups without aborting the other categories.

### 5. LLM Generation Through Worker Subagents

- **One subtask per element** (each Section, element, CPT, coding-standard language separately). Launch independent subtasks in parallel. Worker reports go to the project's gitignored scratch space (default `tmp/auto-docs/`), not into `docs/`.
- **Even a single element** runs through a worker, so the main context stays small.
- The main agent reads no source files itself — only file lists, frontmatter, and worker reports.

Include in every worker prompt:

- The concrete subtask: element, category, target file, source files.
- The path to the matching template (below) with the instruction to fill it exactly; frontmatter required fields as above.
- **Linking duty:** body and `source_files`/`related_docs` must reference all relevant files (template PHP, CSS/SCSS, field-group doc, embedding Sections, assets) — as relative links, only existing paths.
- **Merge rule:** when the target file exists, read it first. Update the generated documentation sections, but preserve manually added content (extra sections, notes, TODOs) in place.
- **Work-record protection (hard):** in Section and CPT docs, the work-record sections are owned by the WST and frontend QA workflows and are preserved verbatim: `Work type`, `Discovery and safety status`, `Discovery sources`, `Protected existing artifacts`, `Preview URLs`, `Visual QA Targets` (including per-row `Result` cells), `Frontend QA Brief`, `QA notes`, deploy state, open questions, and blockers. Auto-docs may add a missing `Visual QA Targets` skeleton from the template, but never edits or regenerates filled rows.
- **Invent nothing:** mark unknown values as `unbekannt` or `TODO:` (no guessed ACF keys, WPGB IDs, URLs, paths); then `status: partial`.
- Standing prohibitions: no commits or pushes from the worker, no secrets in prompts or files, writes only under `docs/`.

Templates per category:

| Category | Template |
|---|---|
| sections | [templates/section.md](templates/section.md) — includes the `Visual QA Targets` section |
| elements | [templates/element.md](templates/element.md) |
| post-types | Workflow and doc template of the bundled **cpt-docs** Skill ([`../cpt-docs/SKILL.md`](../cpt-docs/SKILL.md)); add the auto-docs frontmatter required fields |
| field-groups | [templates/field-group.md](templates/field-group.md) |
| coding-standard | [templates/coding-standard.md](templates/coding-standard.md) |
| README indexes | [templates/readme-index.md](templates/readme-index.md) |

For coding-standard: derive the actual state from representative project code (indentation, naming conventions, `wso-` prefixes, selector patterns, variable usage) **and** work in the applicable WESEO rules (for example the `css-guideline` Rule).

### 6. README Indexes

After all element subtasks complete:

- One `README.md` per category folder as a generated index ([templates/readme-index.md](templates/readme-index.md)): a table of all files with a short description and status. Exception: a script-generated `field-groups/README.md` from the legacy path is kept.
- `docs/README.md` as the overall entry point: category overview with links, the work-record convention (Section and CPT docs carry the QA matrix), and the date of the last run.

### 7. Tracking And Verification

- When the project uses an allowlist `.gitignore`, make sure `docs/` is fully released:

```gitignore
!/docs/
!/docs/**
```

- Check with `git status` that the generated files appear as tracked or new.
- Do not commit from the generation run itself. When auto-docs runs as the closing step of a workflow that commits (per the `deploy-and-branches` Rule), the docs changes ride in that commit; a docs-only change needs no deploy pass and no bridge verification because `docs/` never reaches the server.
- Report to the user at the end: generated and updated files with paths, skipped (current) elements, and open points (`status: partial`, missing field-group source).

## Boundaries

- Writes exclusively under `docs/` and, when needed, the `.gitignore` allowlist.
- Server access is read-only (REST GET per the `cpt-docs` workflow and `GET /status` on the bridge); no writes, no cache flush.
- Never overwrite filled work-record sections; the QA matrix results belong to the frontend QA pass.
- No secrets in docs, prompts, or worker reports.
