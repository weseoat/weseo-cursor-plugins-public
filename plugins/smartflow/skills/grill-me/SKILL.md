---
name: grill-me
description: Interview the user relentlessly about a plan or design until reaching shared understanding, resolving each branch of the decision tree. Use before a wst-section-workflow or wst-new-post-type run starts, or whenever the user wants to stress-test a plan ("grill mich", "zerpflück den Plan"). Output is a prefilled work-record draft in the project docs layer (or a written decision log for non-WST plans), never an implementation.
---

# Grill Me

Interview the user relentlessly about every aspect of the plan until you reach shared understanding. Walk down each branch of the design tree and resolve dependencies between decisions one by one.

This Skill runs in the main chat and is never delegated to a subagent (see the `agent-routing` Rule, "Deliberately Not Routed"): its whole value is the interactive back-and-forth with the user.

## Ground Rules

- Ask **one question at a time**. For each question, provide your recommended answer first, then the alternatives.
- **Explore before asking.** If a question can be answered from `PROJECT-CONTEXT.md`, the project `docs/` layer (work records, `LEARNINGS.md`), the codebase, an issue brief, or the status bridge (`GET /wp-json/wso/v1/status` for ACF groups, WPGB grids, and cache state), answer it yourself and confirm the finding instead of asking.
- Resolve dependencies in order: settle the decisions that other decisions hang on first (identity and scope before structure, structure before presentation, presentation before edge cases).
- Keep going until every branch is either resolved, explicitly deferred with a reason, or recorded as an unresolved placeholder. Shared understanding means no silent assumptions remain.
- Do not invent project values. Missing paths, URLs, ACF keys, WPGB IDs, selectors, taxonomy names, or branch names become explicit `<unresolved: ...>` markers, never guesses.

## Output

The grilling output is a written artifact, not only a chat summary:

- **Section work:** create or update the prefilled Section work-record draft at the project convention path (default `docs/sections/<section-slug>.md`) so the subsequent `wst-section-workflow` run starts from answers instead of questions — classification hypothesis, variants, ACF shape, CSS hooks, Visual QA Targets candidates, open placeholders.
- **CPT work:** create or update the prefilled CPT work-record draft (default `docs/post-types/<resource>.md`) — identity, detail-page decision, taxonomy, ACF shape, display targets, WPGB expectations, open placeholders.
- **Non-WST plans** (plugin work, tooling, process): write a compact decision log — each resolved branch with its decision and reasoning, plus the explicit list of deferred and unresolved points — to a location the user names, or repo-level `tmp/` scratch if it is throwaway (`webroot-safety` Rule).

## Boundary To The Workflow Preflights

`wst-section-workflow` and `wst-new-post-type` own their preflights, gates, and hard stops; this Skill never replaces or restates them. Grill-me is the optional deep interview **before** a workflow run: it prefills the work record so the workflow's own preflight finds recorded decisions instead of open questions. Whatever this Skill leaves as `<unresolved: ...>` the workflow preflight will stop on — that is intended, not a defect.
