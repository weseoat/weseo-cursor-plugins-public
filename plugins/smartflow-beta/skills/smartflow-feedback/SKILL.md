---
name: smartflow-feedback
description: Report SmartFlow bugs, questions, ideas, and Skill/Rule proposals from the working chat to Confluence. For locally developed Skills or Rules, prepare a reviewed ZIP with supporting files and test evidence for owner integration. Use on /smartflow-feedback, "das melde ich", or "reiche meinen lokalen Skill für SmartFlow ein". Creates entries or occurrence comments after confirmation; never integrates into the plugin from a customer project.
---

# SmartFlow Feedback

Report bugs, questions, improvement ideas, and skill/rule proposals to the SmartFlow Confluence feedback hub from inside the working chat. The psychological core: **the reporter never provides structure, only a yes.** The agent classifies, pre-fills, searches for duplicates, and writes; the reporter confirms.

This Skill runs in the main chat and is never delegated to a subagent (see the `agent-routing` Rule, "Deliberately Not Routed"): it needs the session context for autofill and the reporter for confirmations.

When the request includes a locally developed Skill, Rule, or working fix to transfer, read [Local contribution transfer](references/local-contribution.md) before preparing the draft. It extends this intake with a complete reviewed ZIP and a verified attachment receipt. Ordinary feedback stays text-only; neither a bug report nor a proposal without source files requires a ZIP. Intake does not accept/reject submissions or create branches/PRs: owners do that later in the development repository.

## Entry Types

| Type | Title prefix | Target hub page |
| --- | --- | --- |
| Bug (workflow deviated from its promise) | `[Bug] <skill/rule>: <symptom>` | `Probleme / Bugs` |
| Frage | `[Frage] <topic>` | `Fragen & Antworten` |
| Verbesserungsidee | `[Idee] <topic>` | `Verbesserungsideen` |
| Skill-Vorschlag / Rule-Vorschlag | `[Skill] <name>` / `[Rule] <name>` | `Skill-Einreichungen` / `Rule-Einreichungen` |

When the type is unclear, propose one classification with a one-sentence reason and let the reporter confirm — do not ask an open question.

## Bundled Reference: Hub Pages

Space key `Frontend`. Hub/template IDs re-verified against the live space tree on 2026-08-26; ownership page added on 2026-10-14.

| Page | ID |
| --- | --- |
| WESEO Smartflow (SmartFlow hub) | `1507098626` |
| SmartFlow Feedback & Fragen (feedback hub parent) | `1727889413` |
| Fragen & Antworten | `1727627304` |
| Probleme / Bugs | `1728380929` |
| Verbesserungsideen | `1727889440` |
| Skill-Einreichungen | `1827405826` |
| Vorlage: Skill-Einreichung | `1826390049` |
| Rule-Einreichungen | `1867612178` |
| Vorlage: Rule-Einreichung | `1868562438` |
| SmartFlow Ownership – wer betreut was? | `2065334303` |

If a hub page cannot be found by ID, re-orient over the space page tree or a CQL title search before creating anything, and report the drift as its own finding.

## MCP Surface

The team standard is the **community `mcp-atlassian` server** (version 0.22.0 or newer, per the `setup-local-project` preflight). Discover the workspace's Atlassian MCP surface at run start and map the operations this run needs; never assume a version floor guarantees attachment support or guess tool names.

| Operation | Community `mcp-atlassian` | Legacy Rovo MCP |
| --- | --- | --- |
| Read a page | `confluence_get_page` | `getConfluencePage` |
| List subpages of a hub page | `confluence_get_page_children` | `getConfluencePageDescendants` |
| Create the entry subpage | `confluence_create_page` | `createConfluencePage` |
| Add an occurrence comment | `confluence_add_comment` | `createConfluenceFooterComment` |
| Add an area label to a new entry | `confluence_add_label` (discover schema) | Not available — skip and report missing labels |
| Duplicate search (CQL) | `confluence_search` | `searchConfluenceUsingCql` |

Intake sets ownership fields and only the derived area labels on new entries; **never edit the hub pages' overview tables**. Table rows, status changes, `sf-abgeschlossen`, `sf-vorlage` and label corrections belong to owner/maintainer curation. No @mentions or notifications. The only attachment exception is the reviewed contribution ZIP in the linked reference; discover upload, list and binary-download capabilities before creating its page. Legacy Rovo may support text feedback but cannot be assumed to support this transfer.

If no Atlassian MCP is available or it needs auth, stop and guide the reporter through the community `mcp-atlassian` install from the `setup-local-project` preflight (version floor 0.22.0, token in the env block); do not fall back to REST calls or manual copy-paste pages.

## Workflow

### 1. Classify And Pre-Fill

Determine the type. For the bug case, pre-fill **everything** from the session before asking the reporter anything:

- which Skill/Rule ran and what it promised,
- expected vs. actual behavior,
- the distilled step sequence that led there,
- what was corrected and how (if the session contains the fix),
- a pre-formulated solution proposal,
- the plugin version, read from `.cursor-plugin/plugin.json` in the installed plugin package (next to this Skill's plugin root); if unreadable, `<unbekannt>`,
- the project name and dev URL from `PROJECT-CONTEXT.md` (allowed on internal Confluence; if the workspace has no project context, use the workspace name).

For every entry type, pre-fill ownership using [Ownership areas](references/ownership-areas.md):

- Map affected Skill/Rule/Agent names to their areas; list all distinct areas, comma-separated, and derive one `sf-bereich-<slug>` label per area. No match (unknown name, `keiner`, or an unmapped new artifact) means `Bereich: <ergänzt die Kuration>` and no area label. Proposals changing existing artifacts use their areas; new artifacts get an area only when the submitter's answer or related existing artifacts make it unambiguous.
- Read owner name(s) for each derived area live from `SmartFlow Ownership – wer betreut was?` (`2065334303`). Use the owner(s), not supporting members or deputies as substitute owners. If the page or any required area row cannot be read, use `Zuständig: <ergänzt die Kuration>` and note the gap for hand-back; known areas and their labels remain valid. With no area, use the same owner placeholder. Never invent names or copy them from bundled content.

Show the filled draft, including `Bereich` and `Zuständig`, to the reporter for a yes/edit. The reporter may correct the classification; reconcile area labels and re-read the corresponding live owner row before writing. Do not ask for fields the session already answers or make the reporter supply structure.

### 2. Redaction Check (Hard, Before Any Upload)

Check every part of the draft — report and Technischer Anhang — against these rules. They are not optional and not confirmable-away:

1. **Never secrets.** No tokens, passwords, application passwords, API keys — not even prefixes or recognizable fragments. Replace with `<secret entfernt>` placeholders.
2. **No raw chat or terminal dumps.** Only the distilled relevant excerpt, rewritten as terse steps.
3. Project name and dev URL are allowed (internal Confluence); **customer credentials never**.
4. **No screenshots or arbitrary attachments.** A reviewed contribution ZIP is allowed only through [Local contribution transfer](references/local-contribution.md). Apply redaction to every included file and its metadata; an archive is not an exemption.

If the report only works with content that the rules forbid, describe the content in words and placeholders instead.

### 3. Duplicate Search (Confirmation-Gated)

Search before creating. Narrow by text so entries without area labels remain discoverable:

- Scoped candidate search: CQL `ancestor = 1727889413 AND type = page AND (title ~ "<symptom keywords>" OR text ~ "<skill name and symptom keywords>")` (for skill/rule proposals: `parent = 1827405826` or `parent = 1867612178` plus title match).
- Optionally list the target hub page's subpages when the candidate set is small.

Present the top candidates as **title + one-liner + status** (status read from the candidate's metadata block) and ask: "Ist es eine davon?" **Attach only after the reporter confirms a match — never automatically.** No candidates or a "no" means: create a new entry. A false duplicate merge is costlier than an occasional duplicate page.

### 4a. On Match: Occurrence Comment

Add a German footer comment to the existing entry page with occurrence context: date, project, plugin version, one-sentence finding. If the entry's status is `erledigt` and the deviation reappeared in a **newer** plugin version, state the regression explicitly ("Regression in <version>"). **The comment never changes the entry's status, edits the entry page or adds labels** — status transitions stay maintainer curation.

For a contribution ZIP, retain this confirmed entry as the target and follow the attachment/revision procedure in the reference. A matching title never authorizes replacing an older submitted package or silently choosing its latest revision.

### 4b. On No-Match: New Entry Subpage

Create the entry as an **own subpage under the matching hub page** (parent ID from the reference table), page content in **German**, with the fixed structure below. For skill/rule proposals, read the matching Vorlage page live and mirror its headings; fill `Bereich` and `Zuständig` in its `Bereich und Zuständigkeit` section directly after the author section. Use `smartflow` plus the affected area instead of any frozen legacy plugin choices. Keep one authoritative `Status` section with `eingereicht`; do not add a contradictory `offen` block. Add the contribution sections from the reference when transferring files. The overview-table row is owner/maintainer curation (intake cannot edit tables).

Bug/Frage/Idee pages start with the **metadata block as plain text**, with ownership directly after `Betroffener Skill/Rule`:

```text
## Meta

- **Typ:** Bug | Frage | Idee | Skill-Vorschlag | Rule-Vorschlag
- **Betroffener Skill/Rule:** <skill-oder-rule-name>
- **Bereich:** <Bereich(e) oder ergänzt die Kuration>
- **Zuständig:** <live gelesene Owner-Namen oder ergänzt die Kuration>
- **Status:** offen
- **Plugin-Version:** <version>
- **Projekt:** <projektname / dev-URL>
```

Initial status is `offen` for Bug/Frage/Idee and `eingereicht` for Skill/Rule proposals. This Skill never changes an existing status.

After creation, keep the returned page ID/URL and add exactly the derived `sf-bereich-<slug>` labels through the discovered add-label operation and its schema, once per distinct area. Never add `sf-abgeschlossen`, `sf-vorlage` or any other label. With no derived area, skip labeling; the page belongs under `Neu, noch ohne Bereich`. If the operation is missing or fails, keep the page and hand back `Labels fehlen, ergänzt die Kuration` (name any missing areas after partial success); do not recreate the page or retry labels in this run. Hub rows and later label corrections remain curation; the community MCP has no remove-label operation.

**Bug structure** (all agent-prefilled in the session case):

```text
Titel: [Bug] <skill/rule>: <Symptom in einem Satz>

## Meta
(Metadaten-Block wie oben)

## Bericht

- **Erwartet:** <was der Workflow laut Skill/Rule hätte tun sollen>
- **Passiert:** <was tatsächlich passiert ist>
- **Kontext/Reproduktion:** <relevante vorherige Schritte, so knapp wie möglich>
- **Lösungsvorschlag:** <agent-vorformuliert, vom Melder editierbar>
- **Workaround:** <workaround oder "keiner">

## Technischer Anhang
(siehe unten)
```

**Frage/Idee structure** (lighter):

```text
Titel: [Frage] <topic>  bzw.  [Idee] <topic>

## Meta
(Metadaten-Block wie oben)

## Anliegen

<die Frage oder Idee in 1-3 Sätzen>

## Kontext

<Projekt-/Workflow-Kontext, so knapp wie möglich>

## Vorschlag

<eigener Lösungs- oder Umsetzungsvorschlag, oder "keiner">
```

### 5. Technischer Anhang (Bug Entries)

Below the human-readable report, fill a `## Technischer Anhang` section **generously** — colleagues skip it, for the maintainer it is the handoff:

- concrete step sequence of the failed run,
- involved Skills/Rules with plugin versions,
- affected files, selectors, ACF keys, WPGB IDs — with placeholders instead of secrets,
- what was corrected and how (the working fix from the session is the most valuable content),
- environment facts that mattered (MCP servers involved, proof mode, branch state).

The redaction rules from step 2 apply to the annex without exception.

### 6. Hand Back

Report the created page URL (or the commented entry's URL). For new entries, briefly report the area(s) and label result; if assignment is unresolved, say `Bereich und Zuständigkeit ergänzt die Kuration`. If live owner lookup failed, say `Zuständig: <ergänzt die Kuration>` and name the lookup gap. If derived labels could not be added, say `Labels fehlen, ergänzt die Kuration`. Mention the hub-table row only as a curation note, never as a reporter task. Occurrence comments need only the entry URL; no ownership or labeling changes.

For a contribution, also report the verified ZIP receipt and the next step: open the SmartFlow development checkout and give its optional repository-local integration workflow the entry link and selected package hash. That workflow is not bundled in customer installations and is not required to submit. Distinguish `text submitted, package transfer incomplete` from a verified full handoff.

## Scope Boundaries

- Never edit hub pages, overview tables, other entries' bodies, or any entry's status; never delete pages.
- Never write into `plugin-feedback/` — that folder exists only in the maintainer workspace, not in project workspaces.
- Occurrence and regression comments never change status, page bodies or labels.
- Only derived area labels on newly created entries are allowed; no other labels, @mentions or notifications.
- Only the reviewed contribution ZIP is allowed as an attachment; no screenshots or unrelated files.
- One entry per run; a second finding is a second run.
