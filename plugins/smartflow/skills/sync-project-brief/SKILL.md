---
name: sync-project-brief
description: Refresh the local mirror of the project's anchored Confluence page (the PL "Umsetzung" page) on demand - re-read the page over the Atlassian MCP, show the diff against the Confluence mirror block in PROJECT-CONTEXT.md, and overwrite only after user confirmation, never silently and never mid-work. Use when the project lead announces changes on the Confluence page, when a work-package run reports that the page and the mirror disagree, or when the user asks to "sync the brief" / "Confluence-Seite neu einlesen".
---

# Sync Project Brief

Re-read the project's anchored Confluence page and reconcile it with the mirror in `PROJECT-CONTEXT.md`. This Skill is the **only** controlled sync point between the three read points defined by the `confluence-source` Rule: setup mirrors once, work packages read fresh at run start, and everything in between works from the committed mirror. When the PL page changes after setup, this Skill brings the mirror up to date — with the user watching the diff, never behind their back.

This Skill runs in the main chat and is never delegated to a subagent (see the `agent-routing` Rule, "Deliberately Not Routed"): its value is the interactive diff review, and runners never call Confluence themselves.

## Preconditions

1. **Anchor:** read the Confluence block in `PROJECT-CONTEXT.md` (page ID, URL, last mirror timestamp). If no anchor is recorded, this counts as "the first Skill that needs it" per the `confluence-source` Rule: ask the user for the project's Confluence link once, derive the page ID from the URL (`.../pages/<page_id>/<title>`), and record the anchor as part of this run. If the user has no link, stop — a project without a Confluence page has nothing to sync.
2. **MCP:** discover the workspace's Atlassian MCP surface and map the read operations by schema — never guess tool names. Needed here: get a page by ID (community `confluence_get_page` / legacy Rovo `getConfluencePage`). If no Atlassian MCP is available or it needs auth, stop and guide the user through the community `mcp-atlassian` install from the `setup-local-project` preflight (version floor 0.22.0, token in the env block); there is no fallback read path.
3. **Never mid-work:** if a work-package run (Section, CPT, page build, Jira ticket) is currently in progress in this session, finish or pause it first. A mirror overwrite must not change the working basis of a running task; the run keeps its run-start extract until it completes.

## Workflow

### 1. Re-Read The Anchored Page

Fetch the page by ID. If the ID cannot be found (moved, deleted, permission change), stop, report the drift, and re-anchor only with explicit user confirmation — never silently guess a replacement page (per the `confluence-source` Rule).

Extract per the `confluence-source` Rule: URLs, Figma links (global and per module/section), Jira keys, module/section lists, interfaces and content sources, constraints and out-of-scope notes. Honor the optional PL conventions when present (text status markers `OFFEN:` / `ENTSCHIEDEN (<Datum>):` / `ENTFÄLLT:`, the "Für Cursor" header block); resolve decision chains to the newest statement and flag remaining ambiguity.

### 2. Show The Diff

Compare the fresh extract against the current Confluence mirror block in `PROJECT-CONTEXT.md` and present a compact, human-readable diff before touching anything:

```text
Confluence-Sync — Diff gegen den Mirror (Stand: <last-mirror-timestamp>):

Geändert:
- <field>: "<mirror value>" -> "<page value>"

Neu auf der Seite:
- <fact the mirror does not carry>

Nicht mehr auf der Seite:
- <mirror fact the page no longer states>

Unverändert: <n> Werte.
```

Group by impact: values that existing work records rely on (Figma links, Jira keys, URLs, module scope) come first. When a removed or changed value is referenced by a work record in the project `docs/` layer, name that record in the diff line — the user must see what the change touches.

If the diff is empty, report "Mirror ist aktuell", update only the mirror timestamp after a short confirmation, and stop.

### 3. Confirm, Then Overwrite

Ask one compact confirmation: apply the whole diff, apply it with named exceptions, or abort. **Nothing is written before the user confirms.**

On confirmation:

- Overwrite the Confluence mirror block in `PROJECT-CONTEXT.md` with the fresh extract and set the mirror timestamp to now.
- Keep explicit `<unresolved: ...>` markers for gaps the page does not answer — never fill a gap by assumption.
- **Secrets are never mirrored** (per the `confluence-source` Rule): a credential found on the page becomes a placeholder plus pointer, and the finding is reported to the user once.
- Do not rewrite other `PROJECT-CONTEXT.md` sections; the sync owns only the Confluence mirror block. When a synced value contradicts a value recorded elsewhere in `PROJECT-CONTEXT.md` (for example a dev URL), report the contradiction instead of silently editing the other section.

### 4. Hand Back

Report in one short block: what changed, which work records are affected and may need a follow-up read, and the new mirror timestamp. `PROJECT-CONTEXT.md` is tracked source — the change is committed with the SmartFlow trailer as part of the session's normal commit flow (`deploy-and-branches` Rule; `PROJECT-CONTEXT.md` lives outside the deploy path, so no deploy is needed for the sync itself).

## Scope Boundaries

- Read-only toward Confluence: this Skill never writes, comments on, or restructures the PL page.
- It never starts, modifies, or re-scopes work-package runs; affected work records are named in the hand-back, not edited.
- It syncs exactly one page: the anchored project page. Subpages are reached from there during work-package reads, not mirrored here.
