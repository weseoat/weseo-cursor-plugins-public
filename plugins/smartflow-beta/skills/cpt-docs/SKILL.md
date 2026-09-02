---
name: cpt-docs
description: Create or update the structured, LLM-readable German Markdown documentation for one WordPress/WST Custom Post Type of the current SmartFlow project at docs/post-types/<resource>.md — which is also the CPT work record. Documents registration, taxonomies, ACF fields, connected templates, WP Grid Builder grids and cards, embedding Sections, frontend URLs, and the relevant project files. Use when the user wants to document a CPT, asks for "CPT-Doku", "dokumentiere den <name> CPT", or runs /cpt-docs, and as the post-types worker template inside an auto-docs run. Read-only toward the server (REST GET and bridge status only); writes only the doc file and, when needed, the .gitignore allowlist.
---

# CPT Docs

Produces a consistent, machine- and LLM-friendly documentation file for exactly one CPT of this project and stores it as `docs/post-types/<resource>.md`.

In the SmartFlow workspace this file is also the **CPT work record**: `wst-new-post-type` creates and fills it during structural work, and `cpt-frontend-qa` writes QA results into it. When the file already exists, this Skill updates the documentation sections around the work-record sections — it never regenerates or overwrites them (hard rule below). The doc answers four questions:

1. **Structure** of the CPT (registration, taxonomies, ACF fields).
2. **Purpose** of the CPT (which content problem it solves).
3. **Connections**: templates, WPGB grids, cards, embedding Sections, pages.
4. **Relevant files** in the project (path and role).

Content language of the generated doc: **German**. Inside an `auto-docs` full run, this workflow is what the post-types worker subagents execute per CPT.

## Boundaries (Important)

- **Read-only toward the server:** REST `GET` and `GET /wp-json/wso/v1/status` on the bridge only. No CPT/taxonomy/ACF/WPGB/content writes, no cache flush.
- **Writes only:** the doc file under `docs/post-types/` and, when needed, an allowlist entry in `.gitignore`.
- **Work-record protection (hard):** existing work-record sections are preserved verbatim: `Work type`, `Discovery and safety status`, `Discovery sources`, `Protected existing artifacts`, detail-page decision notes, the `Content model` decision (`typed-only` | `flexible-content-only` | `hybrid` | `not-applicable`), `Visual QA Targets` (including filled `Result` cells), `Frontend QA Brief`, `QA notes`, deploy state, open questions, and blockers. A missing `Visual QA Targets` skeleton may be added from the template; filled rows are never edited.
- **Invent nothing.** Mark unknown values as `unbekannt` or `TODO: …`, never guess (no post_type names, ACF keys, WPGB IDs, paths, URLs, selectors). Then set `status: partial`.
- **No secrets.** REST and bridge credentials come from the project env vars named in `PROJECT-CONTEXT.md` (default `WSO_BRIDGE_USER`, `WSO_BRIDGE_APP_PASSWORD`); never write the application password into chat, terminals visible in docs, or the doc itself.

## Required Input

The CPT to document — as one of:

- the template folder name under `smart-template-builder/post-types/` (for example `downloads`),
- the registered `post_type` (for example `wso_download`), or
- a plain-language name (for example "Downloads", "Team").

If missing, ask briefly and list the existing CPT folders.

## Workflow

```text
CPT-Doku:
- [ ] 1. Identify the CPT (map folder <-> post_type)
- [ ] 2. Server discovery via REST + bridge status (read-only)
- [ ] 3. Repo discovery (templates, CSS, Sections, assets, ACF JSON groups)
- [ ] 4. Derive ACF fields + CSS hooks
- [ ] 5. Fill the doc template and write docs/post-types/<resource>.md (merge, preserve work record)
- [ ] 6. Check tracking (.gitignore) + verify the doc
```

### 1. Identify The CPT

The folder name under `post-types/` is usually **plural**, the `post_type` usually **singular** with prefix. Example: folder `downloads/` ↔ post_type `wso_download`.

- Existing CPT folders: `themes/<child-theme>/smart-template-builder/post-types/*/`
- Convention (confirm against `PROJECT-CONTEXT.md`): `post_type = wso_<resource>`, taxonomy `wso_tax_<resource>`, CSS hooks `.wso-<resource>-card` / `.wso-<resource>-single`.

`<resource>` is the short name for the doc file (normally the folder name).

### 2. Server Discovery (Read-Only)

Registration, taxonomies, and entries live in the DB; read them over REST with the project credentials:

| Purpose | Endpoint |
|---|---|
| All types (overview/check) | `GET /wp-json/wp/v2/types` |
| CPT details (labels, supports, `rest_base`, `taxonomies`, `hierarchical`, `viewable`, `has_archive`) | `GET /wp-json/wp/v2/types/<post_type>` |
| Connected taxonomies | `GET /wp-json/wp/v2/taxonomies?type=<post_type>` |
| Count + sample entry | `GET /wp-json/wp/v2/<rest_base>?per_page=1` (header `X-WP-Total` = count) |
| Taxonomy terms | `GET /wp-json/wp/v2/<taxonomy_rest_base>` |
| ACF field groups (`local: json`) and WPGB grids | `GET /wp-json/wso/v1/status` (per the `status-bridge` Rule) |

Note: `public`/`viewable`, `has_archive`, `supports`, `rest_base`, detail pages (yes/no), entry count, taxonomy names plus term counts, and the bridge-listed field groups and WPGB grid/card IDs for this CPT.

### 3. Repo Discovery

Search for every file with `<resource>` relevance (paths per `PROJECT-CONTEXT.md`; typical layout):

| Role | Path pattern |
|---|---|
| WST card templates | `smart-template-builder/post-types/<resource>/cards/*.php` |
| WST single template (optional) | `smart-template-builder/post-types/<resource>/singles/*.php` |
| Frontend CSS | `styles/post-type/<resource>/*.css` |
| CSS registration (style loader) | `styles.json` (check the entry) |
| Embedding WST Sections | `smart-template-builder/sections/*.php` (search for grid ID / `<resource>`) |
| CPT assets (icons/SVG) | `assets/<resource>/**` |
| ACF JSON field groups | `themes/<child-theme>/acf-json/*.json` (location `post_type == <post_type>`) |
| Legacy PHP field groups (fallback) | `smart-template-builder/acf/field-groups/*.php` |
| Legacy ACF JSON export (fallback) | `acf_export/*.json` |
| Field-group docs | `docs/field-groups/*.md` (link as `related_docs`) |

WPGB grid/card IDs are project-local values — take them from the existing work record or the bridge status; otherwise mark them `TODO: WPGB-ID prüfen`. Never guess them.

### 4. Derive ACF Fields And CSS Hooks

ACF fields are not reliably available over plain REST. Sources in this order:

1. ACF JSON groups under `themes/<child-theme>/acf-json/` with location `post_type == <post_type>` → field name, `key`, `type`, `return_format`. Cross-check that the group appears in the bridge status with `local: json`. A `clone` field pointing at the project's `[TMPL] Flexible Inhalte` group marks the content model as `flexible-content-only` (no typed detail fields, no single partial) or `hybrid` (typed fields plus the clone); typed fields without a clone on a detail-page CPT mean `typed-only`; CPTs without detail pages get `not-applicable`. Take an existing `Content model` value from the work record; derive it only when the record has none.
2. Legacy sources when the project has no `acf-json/` yet: PHP field groups under `smart-template-builder/acf/field-groups/`, or the newest `acf_export/*.json` admin export.
3. Card/single templates → fields actually used, derived from WST shortcodes (`[wst_acf field='…']`, `[wst_acf_file field='…']`, `[wst_post_title]`, `[wst_if field='…']`).

Read the CSS hooks directly from the card markup (`.wso-<resource>-card …`) and list them in the doc — these selectors connect template and CSS.

### 5. Write The Doc

Fill the template below completely and write it to `docs/post-types/<resource>.md`. The YAML frontmatter carries the machine-readable core facts (including the `auto-docs` required fields); the body explains purpose and connections. Mark empty/unknown values clearly as `unbekannt` / `TODO:`.

Merge rule when the file exists: read it first, update the documentation sections, keep manually added content in place, and apply the work-record protection above. Add the `Visual QA Targets` skeleton only when the section is missing entirely.

### 6. Tracking And Verification

- When the project uses an allowlist `.gitignore`, make sure `docs/` is released:

```gitignore
!/docs/
!/docs/**
```

- Check with `git status` that `docs/post-types/<resource>.md` appears as tracked or new.
- Verify that no invented values are included and every referenced repo path exists.
- `docs/` lives at the repository root, outside the child theme deploy path: it never reaches the server, so no deploy pass or bridge verification applies. Markdown only, no secrets, no dumps.
- Do not commit from this Skill; docs changes ride with the commit of the workflow they belong to (per the `deploy-and-branches` Rule).

## Doc Template

```markdown
---
title: "<Plural-Label>"
category: post-types
slug: <resource>
source_files:
  - themes/<child-theme>/smart-template-builder/post-types/<resource>/
related_docs:
  - ../field-groups/<group-slug>.md
generated: <YYYY-MM-DD>
status: <complete|partial|todo>
post_type: wso_<resource>
singular: <Singular-Label>
plural: <Plural-Label>
public: <true|false>
has_archive: <true|false>
detail_pages: <true|false>
content_model: <typed-only|flexible-content-only|hybrid|not-applicable>
rest_base: <rest_base>
entry_count: <n|unbekannt>
taxonomies: [<wso_tax_...>]
acf_field_groups: [<group_key>]
wpgb_grids: [<id|TODO>]
wpgb_cards: [<id|TODO>]
display_targets: [<grid|carousel|section|single>]
frontend_urls: [<url|unbekannt>]
sources: [REST, Bridge, Repo]
---

# CPT: <Plural> (`wso_<resource>`)

## Zweck

<1–3 Sätze: Wofür existiert der CPT, welches Content-Problem löst er, wo wird
er im Frontend sichtbar.>

## Aufbau

### Registrierung

| Eigenschaft | Wert |
|---|---|
| Post Type | `wso_<resource>` |
| Labels | <Singular> / <Plural> |
| Public / sichtbar | <true|false> |
| Detailseiten (Single) | <ja|nein> |
| Content-Modell | <typed-only|flexible-content-only|hybrid|not-applicable> |
| Archiv | <ja|nein> |
| Supports | <title, thumbnail, editor, …> |
| REST base | `<rest_base>` |
| Einträge | <n|unbekannt> |

### Taxonomien

| Taxonomie | Hierarchie | Öffentliches Archiv | Zweck | Begriffe |
|---|---|---|---|---|
| `wso_tax_<resource>` | <ja|nein> | <ja|nein> | <Filter/Gruppierung> | <n> |

### ACF-Felder

| Feld | Key | Typ | Zweck |
|---|---|---|---|
| `<name>` | `<field_key>` | `<type>` | <Beschreibung> |

Feldgruppe: `<group_key>` (Local JSON: `acf-json/<datei>.json`,
Location `post_type == wso_<resource>`) — Doku: [`<group-slug>.md`](../field-groups/<group-slug>.md)

## Verbundene Templates, Grids, Cards, Seiten

### WST Card-Templates

| Variante | Datei | CSS-Hook |
|---|---|---|
| <Liste/Standard> | `smart-template-builder/post-types/<resource>/cards/<...>.php` | `.wso-<resource>-card` |

### WP Grid Builder (projekt-lokale IDs, aus Work Record oder Bridge)

| Zweck | Grid-ID | Card-ID |
|---|---|---|
| <Liste/Slider> | <id|TODO> | <id|TODO> |

### Einbettende Sections / Seiten

| Kontext | Datei / URL |
|---|---|
| <Section-Name> | `smart-template-builder/sections/<...>.php` |
| Frontend | <url> |

### Single-Template

<Pfad oder „nicht vorhanden". Bei `hybrid`: Partial plus `[wst_include template="flexible-content.php"]`
nach dem Wrapper; bei `flexible-content-only`: kein Partial, Smart-Template-Include ist `flexible-content.php`.>

## Visual QA Targets

<!-- Work-Record-Abschnitt: wird von wst-new-post-type befüllt und von
cpt-frontend-qa je Zeile mit Result beschrieben. cpt-docs legt nur das leere
Gerüst an und ändert befüllte Zeilen nie. -->

Viewport-Zuordnung (Pixelwerte aus `PROJECT-CONTEXT.md`):

| Rolle | Breite (px) |
|---|---|
| desktop | <1920|unbekannt> |
| tablet | <768–991|unbekannt> |
| mobile | <375|unbekannt> |

Eine Zeile = eine ja/nein-prüfbare Erwartung, je Oberfläche (Card,
Grid/Archiv, Filter/Carousel, Single). Pflicht-Basisvarianten (erwartete
Card-Anzahl, lange Labels/Excerpts, fehlende Bilder, leere optionale Felder,
Filterzustände, Pagination, Mobile-Stack, Interaktionszustände) beantworten
oder als `n/a: <Grund>` eintragen.

| Variante | Oberfläche | Viewport | Erwartung | Result |
|---|---|---|---|---|
| default | card | desktop | <Erwartung mit stabilem Selektor> | <pass|fail: Notiz|offen> |
| <variante> | <card|grid|filter|single> | <viewport> | <Erwartung> | <Result> |

## QA-Notizen

<!-- Work-Record-Abschnitt: Statusfelder, Deploy-Stand (Commit-Hash,
Bridge-Verifikation), Injection-Proof- und Served-Check-Ergebnisse je
Oberfläche, offene Fragen, Blocker. Gehört den WST-/Frontend-QA-Workflows. -->

<Statusfelder und Ergebnisse des letzten Durchlaufs — oder „noch kein Durchlauf".>

## Relevante Dateien

| Pfad | Rolle |
|---|---|
| `smart-template-builder/post-types/<resource>/cards/<...>.php` | Card-Template |
| `acf-json/<datei>.json` | ACF-JSON-Feldgruppe |
| `styles/post-type/<resource>/<...>.css` | Frontend-CSS |
| `styles.json` | CSS-Registrierung |
| `smart-template-builder/sections/<...>.php` | Einbettende Section |
| `assets/<resource>/**` | CPT-Assets |

## Offene Punkte / TODOs

- <unbekannte oder zu prüfende Werte — oder „keine">
```

## Example

For an existing CPT **Downloads**:

- Input: `downloads` → post_type `wso_download`.
- REST: `types/wso_download` (labels, `rest_base`, supports), `taxonomies?type=wso_download`; bridge status lists the `wso_download` field group (`local: json`) and the WPGB grids.
- Repo: `post-types/downloads/cards/download-list-card.php` plus `download-highlight-card.php`, the ACF JSON group in `acf-json/`, `assets/download/**`.
- WPGB grid/card IDs from the existing work record `docs/post-types/downloads.md` (or the bridge status).
- Output: the updated `docs/post-types/downloads.md`, with the filled `Visual QA Targets` rows and QA notes preserved.

Submitted by Tobias Uhl (Confluence skill intake); adapted for the SmartFlow single-workspace model.
