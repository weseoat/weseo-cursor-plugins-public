---
name: smartflow-feedback
description: Report SmartFlow feedback to the team's Confluence feedback hub directly from the working chat - a bug ("der Workflow lief schief", a Skill misbehaved, a hard stop was missed), a question, an improvement idea, or a skill/rule proposal. Classifies the entry, pre-fills the report from the session, runs a confirmation-gated duplicate search, and creates a German entry subpage (or an occurrence comment on an existing entry) under the matching hub page. Use on "/smartflow-feedback", "das melde ich", "soll ich das melden?" (from the feedback-capture Rule), or whenever a colleague wants to hand something to the SmartFlow maintainers.
---

# SmartFlow Feedback

Report bugs, questions, improvement ideas, and skill/rule proposals to the SmartFlow Confluence feedback hub from inside the working chat. The psychological core: **the reporter never provides structure, only a yes.** The agent classifies, pre-fills, searches for duplicates, and writes; the reporter confirms.

This Skill runs in the main chat and is never delegated to a subagent (see the `agent-routing` Rule, "Deliberately Not Routed"): it needs the session context for autofill and the reporter for confirmations.

## Entry Types

| Type | Title prefix | Target hub page |
| --- | --- | --- |
| Bug (workflow deviated from its promise) | `[Bug] <skill/rule>: <symptom>` | `Probleme / Bugs` |
| Frage | `[Frage] <topic>` | `Fragen & Antworten` |
| Verbesserungsidee | `[Idee] <topic>` | `Verbesserungsideen` |
| Skill-Vorschlag / Rule-Vorschlag | `[Skill] <name>` / `[Rule] <name>` | `Skill-Einreichungen` / `Rule-Einreichungen` |

When the type is unclear, propose one classification with a one-sentence reason and let the reporter confirm — do not ask an open question.

## Bundled Reference: Hub Pages

Space key `Frontend`. IDs re-verified against the live space tree on 2026-08-26.

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

If a hub page cannot be found by ID, re-orient over the space page tree or a CQL title search before creating anything, and report the drift as its own finding.

## MCP Surface

The team standard is the **official Atlassian Rovo MCP** (Cursor marketplace install, OAuth). Discover the workspace's Atlassian MCP surface at run start and map the five operations this Skill needs; never guess tool names.

| Operation | Rovo MCP (documented) | Community `mcp-atlassian` |
| --- | --- | --- |
| Read a page | `getConfluencePage` | `confluence_get_page` |
| List subpages of a hub page | `getConfluencePageDescendants` | `confluence_get_page_children` |
| Create the entry subpage | `createConfluencePage` | `confluence_create_page` |
| Add an occurrence comment | `createConfluenceFooterComment` | `confluence_add_comment` |
| Duplicate search (CQL) | `searchConfluenceUsingCql` | `confluence_search` |

The Rovo server exposes **no label and no attachment tools**, and its page update is whole-body only. Therefore: no labels (the metadata block below replaces them), no attachments (v1 is text-only), and **never edit the hub pages' overview tables** — table rows, labels, and status changes are maintainer curation, not intake.

If no Atlassian MCP is available or it needs auth, stop and guide the reporter to the marketplace install + OAuth (`Settings` -> `Tools & MCP`); do not fall back to REST calls or manual copy-paste pages.

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

Show the filled draft to the reporter for a yes/edit. Do not ask for fields the session already answers.

### 2. Redaction Check (Hard, Before Any Upload)

Check every part of the draft — report and Technischer Anhang — against these rules. They are not optional and not confirmable-away:

1. **Never secrets.** No tokens, passwords, application passwords, API keys — not even prefixes or recognizable fragments. Replace with `<secret entfernt>` placeholders.
2. **No raw chat or terminal dumps.** Only the distilled relevant excerpt, rewritten as terse steps.
3. Project name and dev URL are allowed (internal Confluence); **customer credentials never**.
4. **No screenshots or attachments** in v1.

If the report only works with content that the rules forbid, describe the content in words and placeholders instead.

### 3. Duplicate Search (Confirmation-Gated)

Search before creating. Because entry pages carry no labels, narrow by text:

- Scoped candidate search: CQL `ancestor = 1727889413 AND type = page AND (title ~ "<symptom keywords>" OR text ~ "<skill name and symptom keywords>")` (for skill/rule proposals: `parent = 1827405826` or `parent = 1867612178` plus title match).
- Optionally list the target hub page's subpages when the candidate set is small.

Present the top candidates as **title + one-liner + status** (status read from the candidate's metadata block) and ask: "Ist es eine davon?" **Attach only after the reporter confirms a match — never automatically.** No candidates or a "no" means: create a new entry. A false duplicate merge is costlier than an occasional duplicate page.

### 4a. On Match: Occurrence Comment

Add a German footer comment to the existing entry page with occurrence context: date, project, plugin version, one-sentence finding. If the entry's status is `erledigt` and the deviation reappeared in a **newer** plugin version, state the regression explicitly ("Regression in <version>"). **The comment never changes the entry's status and never edits the entry page** — status transitions stay maintainer curation.

### 4b. On No-Match: New Entry Subpage

Create the entry as an **own subpage under the matching hub page** (parent ID from the reference table), page content in **German**, with the fixed structure below. For skill/rule proposals, read the matching Vorlage page live and mirror its structure instead; note in the hand-back that the hub overview-table row is a maintainer curation step (the intake cannot safely edit tables).

Every entry page starts with the **metadata block as plain text** — this replaces Confluence labels:

```text
## Meta

- **Typ:** Bug | Frage | Idee | Skill-Vorschlag | Rule-Vorschlag
- **Betroffener Skill/Rule:** <skill-oder-rule-name>
- **Status:** offen
- **Plugin-Version:** <version>
- **Projekt:** <projektname / dev-URL>
```

`Status: offen` is the only status this Skill ever writes. Real Confluence labels and hub-table rows are added later by the maintainer.

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

Report the created page URL (or the commented entry's URL) in one line. Mention open curation steps (labels, hub-table row) only as a note — they are the maintainer's, not the reporter's.

## Scope Boundaries

- Never edit hub pages, overview tables, other entries' bodies, or any entry's status; never delete pages.
- Never write into `plugin-feedback/` — that folder exists only in the maintainer workspace, not in project workspaces.
- Occurrence and regression comments never change status.
- No attachments, no screenshots, no labels (v1).
- One entry per run; a second finding is a second run.
