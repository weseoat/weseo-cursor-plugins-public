---
name: jira-batch-workflow
description: Orchestrates a batch of open Jira subtasks (default 10) of one parent task in the local SmartFlow workspace - JQL intake, screenshot-based triage, statusboard, up to three parallel jira-ticket-runner subagents with file-disjoint write ownership, feedback relay via resume, serialized per-ticket commits under an explicit batch commit mandate (never push, never without the mandate or an individual confirmation), working-tree baseline and discipline for foreign changes, final batch review, Jira solution comments, and optional ticket transition to Done on request. Use when the user hands over a parent task key and wants its open subtasks processed as a batch ("Subtasks abarbeiten", "Ticket-Batch").
---

# Jira Batch Workflow (local orchestrator)

One run = one parent task's open subtasks, processed in the local
SmartFlow workspace (no server shell, no served docroot). This Skill is a
**router and closer**, not a second ticket workflow: all per-ticket
semantics (intake, screenshot rule, diagnosis, implementation, Playwright
verification, report format) live in the bundled `jira-ticket-workflow`
Skill. The main chat owns only: ticket selection, triage, the
statusboard, slot scheduling, feedback relay, the per-ticket commits
under the batch commit mandate, and the final batch review with Jira
closing.

Delegation is strictly two levels: main chat → `jira-ticket-runner`
leaf agents. Runners never spawn further agents, never commit, never
write to Jira.

## 1. Intake

1. Required input: the **parent task key** (e.g. `WP-45800`). Optional:
   batch size (default 10) and extra user instructions; chat
   instructions take precedence over ticket text.
2. Fetch candidates via the workspace's Atlassian MCP server →
   `jira_search` with JQL
   `parent = <KEY> AND statusCategory = "To Do" ORDER BY rank`,
   capped at the batch size. **Only genuinely open tickets** ("Offen"
   / To-Do status category) enter the batch. Tickets that are
   "In Arbeit" (In Progress), in review, done, or in any other status
   are never touched — someone else may own them. If the JQL returns
   a ticket whose live status turns out not to be open at spawn time,
   skip it and note the skip on the board.
3. Ask for the **batch commit mandate** once, in German: may the main
   chat commit each ticket on the recorded working branch as soon as
   its runner returns a pass? Per the commit gate in the
   `deploy-and-branches` Rule this is the scoped standing confirmation
   for exactly these per-ticket commits, valid for this run only —
   never a push, never anything outside the batch. Record the answer
   in the board header. Without the mandate, finished tickets end at
   `implemented` and every commit is proposed individually at the
   final review (step 6).
4. Ask the **transition question** right next to the mandate, in
   German, yes/no, default no: „Tickets nach Review-Freigabe auf
   Fertig setzen?" Record `transition: ja | nein` in the board header.
   Yes means: after the user releases the final review and after the
   solution comment, the main chat moves each commented ticket to the
   project's Done-category status (step 6). No means the tickets stay
   open with the solution comment, as before.
5. Take the **working-tree baseline**: `git status --short` at intake,
   recorded verbatim in the board header as `pre-existing working-tree
   changes`. A dirty start is not a blocker — the colleague may have
   own work open — but this list is the reference for step 5: anything
   on it is user work and is never staged, touched, or discarded.
6. Create the statusboard at `.cursor/jira-batch/<parent-key>.md`
   (untracked, never committed, deleted at batch closure). One row per
   ticket: key (linked), short title, status, suspected target
   file(s), slot/agent, proof mode, open question / user feedback.
   Statuses: `queued` / `needs-routing` / `running` / `iterating` /
   `implemented` / `committed` / `blocked` / `skipped`.
7. If the board file already exists for this parent, this is a
   **resume**: reconstruct the batch state from the board instead of
   restarting finished tickets (including the recorded mandate answer,
   the transition answer, and the baseline; if the header carries
   none, ask or take them now).

## 2. Triage round (main chat, read-only)

Before any spawn, for every ticket: `jira_get_issue` (fields
`summary,description,comment,attachment`) plus `jira_get_issue_images`
for all image attachments — the screenshot rule of the ticket workflow
applies to triage too; never classify a visual ticket blind.

Per ticket, record on the board:

- What the screenshot shows (element, page area, viewport if visible).
- Suspected target file(s) in the child theme.
- Classification:
  - `direct-fix`: locally doable small fix (CSS/SCSS, small JS,
    trivial shortcode parameter) → eligible for a runner.
  - `needs-routing`: WST PHP work beyond trivial params, WPGB
    configuration, Section remodel, CPT/ACF changes, anything that
    needs its own workflow. Stays on the board with a reason; the user
    decides whether it goes into a separate run of the matching Skill
    (`wst-section-workflow`, `wst-new-post-type`) or subagent
    (`wpgb-specialist`, …). Never absorbed into this batch.
  - `unklar`: ask the user before spawning; never send a runner in
    blind.

Then compute the schedule: group `direct-fix` tickets by target
file(s). Tickets with disjoint files may run in parallel; tickets
sharing a file form a **chain** on one slot, strictly serialized.

## 3. Spawning and slots

- Spawning follows the `agent-routing` Rule: the model routing for the
  `jira-ticket-runner` (including the pre-spawn availability check),
  the fixed return contract (STATUS, EVIDENCE, OWN CHANGES, GATES,
  OPEN DECISION, NEXT OWNER), and the handoff schema for context-full
  runners come from that Rule instead of being restated here. Only the
  batch-specific semantics (statusboard, slots, feedback relay) live
  in this Skill.
- Maximum **3 concurrent runners** (= the Playwright server capacity of
  the parallel setup from the `playwright-browser-claim` Rule; each
  runner claims its own lock).
- Runners are launched as **background** `jira-ticket-runner`
  subagents so the main chat stays responsive for user messages.
- Spawn prompt per runner contains: ticket key, summary, the user's
  extra instructions, the triage note (screenshot description,
  suspected target files), the binding write scope (exactly the
  assigned file group), the no-push/no-commit prohibitions and the
  Working Tree Discipline per the `deploy-and-branches` Rule (never
  restore, stash, or clean foreign changes; report unexpected files by
  path), and the pointer to load the bundled `jira-ticket-workflow`
  Skill steps 3–5.
- When a runner returns with a pass and the batch commit mandate is
  granted, the main chat **commits the ticket right away** (step 5),
  then frees the slot and starts the next queued ticket with disjoint
  files. **Chain rule:** within a file chain, ticket n+1 starts only
  after ticket n's commit exists, otherwise the diffs of two tickets
  mix in one file. Under the mandate, chains never wait on the user;
  without it, ticket n+1 in a chain waits until the user confirms
  ticket n's proposed commit — parallel slots on disjoint files keep
  running either way.

## 4. Reports, feedback relay, iteration

- After each runner return, update the board and post the compact
  German per-ticket report in chat (Ursache, Fix mit Code-Referenz,
  Verifikation mit Proof-Mode, offene Punkte). Every few tickets, post
  the board overview.
- Local runs end at **injection-proof**; every pass is honestly
  reported as `implementation pass, deployed verification pending`.
- User feedback is addressed by ticket key ("WP-45812: …"). Default
  relay: **resume** the responsible runner with the feedback plus
  minimal context; the runner iterates (workflow steps 3–5) and
  returns a new report. Board status → `iterating`, feedback noted on
  the board row.
- If the runner is still running, hold the feedback and deliver it on
  completion. **Interrupt only when the user explicitly asks** —
  interrupts mid-QA risk orphaned Playwright locks.
- Exception: trivial one-line corrections (token swap, single value)
  may be fixed directly by the main chat with a short re-check; as
  soon as multi-viewport browser QA is needed again, it goes back to
  the runner via resume.
- A runner `route-back` (mid-ticket discovery that the ticket is
  not a local direct fix or out of scope) sets the board row to
  `needs-routing` with the runner's diagnosis attached; the slot is
  refilled.

## 5. Per-ticket commit under the batch mandate (main chat only)

The commit authorization is the **batch commit mandate from intake**
(the scoped standing confirmation per the `deploy-and-branches` commit
gate): with the mandate, the main chat commits as soon as a runner
returns a pass — the user reviews everything at the end (step 6) and
can re-steer individual agents then. Without the mandate, the board
row goes to `implemented` and the commit is proposed at the final
review; nothing is ever committed silently. Commits run **centrally
and serially** in the main chat, per ticket:

1. **Git:** stage **only the files of this ticket** (from the runner's
   `OWN CHANGES`; never `git add -A` — other tickets' edits and the
   colleague's own work may sit in the working tree). One commit per
   ticket on the recorded working branch, message with the WP-key and
   the fix, trailer per the `commit-trailer` Rule. Never push
   (`deploy-and-branches` Rule).
2. **Unexpected files:** when `git status --short` shows a changed path
   that is neither in this ticket's `OWN CHANGES` nor in another
   running ticket's scope, check it against the intake baseline. On the
   baseline → user work: leave it, mention it in the final report. New
   and unexplained → do not stage, do not touch; report the path, and
   when a runner plausibly wrote it, ask that runner via `resume`
   („hast du `<path>` angefasst?") instead of concluding from a silent
   report. **Never `git restore`, `git checkout --`, `git stash`, or
   `git clean` a file the agent did not write in this run** (Working
   Tree Discipline, `deploy-and-branches`). A discard happens only on
   the user's explicit, file-naming instruction.
3. Update the project backlog / affected work-record QA sections in the
   project `docs/` layer when the ticket touches a tracked item.
4. Board row → `committed (<hash>)`.

Feedback after a commit (step 4) yields a follow-up commit for the
same ticket; the board row collects all hashes. Jira comments are NOT
written here — they wait for the final review.

## 6. Final review, Jira closing, batch closure

- When every row is terminal (`committed`, `implemented`,
  `needs-routing`, `blocked`, `skipped`), post the final board overview
  in German: per ticket the compact result (Ursache, Fix,
  Commit-Hashes, Proof-Mode) plus the reminder that all fixes are
  deploy-pending until the user pushes and the status bridge confirms
  the deployed commit.
- Rows still at `implemented` (batch ran without the mandate): propose
  each pending commit — files, summary, message — and commit on the
  user's confirmation, serialized per ticket in chain order.
- The user reviews the results and may still steer individual tickets
  ("WP-x: …") — those iterate via step 4 and get follow-up commits.
- Once the user closes the review (e.g. "passt", "fertig"), write per
  committed ticket the short German solution comment via
  `jira_add_comment` (Ursache, Fix, wo verifiziert — lokal
  injection-proof, Deploy ausstehend — Commit-Hashes), with the
  signature line per the `jira-comment-signature` Rule.
- **Transition (only when the header says `transition: ja`):** after
  the solution comment, per commented ticket read
  `jira_get_transitions` and pick the transition whose target status
  is in the status category **Done** (name varies per project:
  „Fertig", „Done", „Erledigt" — never guess the name). Exactly one
  candidate → execute it. Several → ask once and reuse the answer for
  the batch. None → report it, no transition, the ticket stays open
  with its comment. Only tickets that received a solution comment are
  transitioned — `implemented` without commit, `blocked`,
  `route-back`, `skipped` never. In the legacy state „master — user
  commits" the same applies: comment written → transition allowed,
  even though the colleague makes the commit. The transition needs no
  second comment.
- Report pre-existing or unexplained working-tree changes (step 5) in
  the closing summary so the colleague knows what was deliberately
  left untouched.
- Verify all Playwright locks are released, then delete
  `.cursor/jira-batch/<parent-key>.md`.

## Hard rules

- Per-ticket semantics are never restated or overridden here; the
  single-ticket Skill is authoritative for steps 3–5 and the report.
- One write owner per file group at any time; the schedule from the
  triage round is binding.
- Runners never commit, never push, never write to Jira, never spawn
  agents.
- The main chat never commits without the batch commit mandate or an
  individual per-commit confirmation (`deploy-and-branches` commit
  gate); pushes only under the explicit per-request push exception of
  the `deploy-and-branches` Rule — the batch mandate never covers a
  push.
- Never transition ticket statuses unless the intake answer was yes;
  then only after review release, only for tickets that received a
  solution comment, only via the Done-category transition read from
  `jira_get_transitions`.
- Never discard, restore, stash, or clean working-tree changes the
  agent did not make in this run; unexpected files are reported by
  path, not resolved (Working Tree Discipline, `deploy-and-branches`).
- Never invent JQL beyond the parent-key pattern, ACF keys, WPGB IDs,
  selectors, URLs, or paths.
- No temp artifacts in the deploy path; the board file in `.cursor/`
  is the only run artifact and is deleted at closure.
