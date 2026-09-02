---
name: jira-ticket-workflow
description: Processes a single WESEO Jira ticket (WP-xxxxx) for the current WordPress/WST project end to end - fetch the issue via the Atlassian MCP including mandatory screenshot viewing, combine it with the user's extra instructions, triage/route the work (direct fix, subagent, or bundled workflow Skill), implement a minimal fix inside the child theme, verify injection-proof via Playwright, report in German, and after user approval commit per ticket (never push) and write a Jira solution comment. Use whenever the user shares a Jira ticket URL or WP-key ("Schau dir das Ticket an", "fixe das", ticket abarbeiten).
---

# Jira Ticket Workflow

One ticket = one run of this workflow. Input is always a Jira link/key
plus extra instructions from the user in chat. The user's chat
instructions take precedence over the ticket text when they conflict
or narrow the scope.

The workspace is the local SmartFlow repository (wp-content level, no
server shell, no served docroot). Saved edits are never live: they reach
the server only through the commit-and-hand-over flow
(`deploy-and-branches` Rule). Read the Jira site URL and project key from
`PROJECT-CONTEXT.md`; never hardcode them.

## 1. Intake

1. Extract the issue key (e.g. `WP-45755`) from the URL or message.
2. Fetch the ticket via the workspace's Atlassian MCP server, tool
   `jira_get_issue`. **Always request the attachment field**:
   `fields: "summary,description,comment,attachment"` — the default
   field set omits attachments entirely.
3. **Screenshots are mandatory context.** Marker.io tickets carry the
   actual visual complaint only in the attached screenshot; the text is
   often just boilerplate ("Überschrift fehlt"). Before triage, view
   every image attachment:
   - **Primary path:** call `jira_get_issue_images` with the issue key.
     This returns image attachments as inline vision content over MCP —
     no browser login, no filesystem. Do this immediately after step 2
     when `attachment[]` contains any PNG/JPEG/GIF/WebP/SVG/BMP.
   - **Do not use** anonymous `curl`, `WebFetch`, or raw
     `attachment[].content` URLs for screenshots — they need Jira auth
     and will 401/403. A failed fetch is not "looked at the screenshot".
   - **Playwright fallback only:** if `jira_get_issue_images` fails or
     returns zero images while step 2 lists image evidence the ticket
     depends on, claim a Playwright server per the
     `playwright-browser-claim` Rule and try the Jira ticket page or
     attachment URL in a profile that is actually logged in to the Jira
     site. Do not assume the Playwright profile is logged in — verify
     before relying on it.
   - **Last resort:** if MCP and Playwright both fail, STOP and ask the
     user to paste/attach the screenshot or describe it. Never diagnose
     a visual ticket blind against the screenshot.
   - In the diagnosis, state explicitly what each screenshot shows
     (element, page area, viewport if visible) before naming a root cause.
4. When `PROJECT-CONTEXT.md` records a Confluence anchor, re-read the
   anchored PL page fresh over the Atlassian MCP and pull only the
   section relevant to this ticket — the matching task row (often
   carrying the same WP-key), its notes, and any per-module Figma link —
   into the run context (`confluence-source` Rule). No anchor, or no
   usable Atlassian MCP: skip cleanly, note `confluence-source: no
   anchor` (or `MCP unavailable`), and continue from the mirror. Never
   re-read mid-run; the `jira-ticket-runner` receives the distilled
   extract in its worker prompt and never calls Confluence.
5. In parallel, start reading the code the user pointed at (file
   references in the message) — do not wait for Jira to explore.

## 2. Triage and routing

Classify the ticket before editing anything. Route larger work instead
of absorbing it:

| Ticket shape | Route |
|---|---|
| Small param/config/JS/CSS fix, single surface | Direct in main chat |
| WST template PHP writes (`smart-template-builder/**/*.php`) beyond a trivial shortcode-param change | `wst-shortcode-implementer` subagent per the `wst-php-authoring-route` Rule |
| WPGB grid/card/facet configuration | `wpgb-specialist` subagent |
| Visual-only Section/CPT styling with real QA depth | bundled `frontend-section-qa` / `cpt-frontend-qa` Skill |
| New Section or Section remodel | bundled `wst-section-workflow` Skill |
| CPT foundation/taxonomy/ACF changes | bundled `wst-new-post-type` Skill |
| Multi-surface CPT package | package flow per the `agent-routing` Rule |

When routing to a Skill or subagent, pass the ticket key, the fetched
ticket summary, the user's extra instructions, and the diagnosis so
far. This Skill keeps ownership of the closing steps (report, approval
gate, Git, Jira comment).

## 3. Diagnosis

- Read the relevant Section/CPT work record in the project `docs/`
  layer first. Work records document verified contracts (e.g. swiper
  parameters, selectors, runtime behavior) — a deviation between
  current code and the documented contract is the prime suspect, and
  restoring the documented state is the preferred fix.
- Check `PROJECT-CONTEXT.md` and the project learnings notes for known
  caveats (cache/Delay-JS behavior, rem bands, viewport ladder, brand
  tokens) before inventing a new mechanism.
- Inspect the served page directly against the project dev/staging URL
  (extract `data-*` options, markup, classes) to confirm what actually
  renders. Remember the served page reflects the last deployed commit,
  not the local working tree.
- Anchor the diagnosis on the ticket screenshot (intake step 3): the
  screenshot defines which element, page state, and viewport the
  reporter means. State explicitly what the screenshot shows before
  naming the root cause.
- Name the root cause explicitly before editing. If the cause cannot
  be established, report findings and stop instead of speculative
  fixing.

## 4. Implementation

- Minimal fix, scoped to the ticket. Stay inside the `file-edit-boundary`
  Rule: child theme only, never `functions.php`, `theme-functions.php`
  and MU plugins only with explicit prior user confirmation, WST plugin
  folder off-limits.
- Follow existing conventions (`css-guideline` Rule: tokens/variables,
  style-loader registration for new CSS files, `wso-` selectors, comment
  style of the touched file).
- Reference the ticket key in code comments only where future readers
  need the why, not as change narration.
- Run lints on edited files.

## 5. Verification

Claim one Playwright server per the `playwright-browser-claim` Rule,
verify in the real browser, release the lock.

- Local proof mode is **injection-proof**: inject the planned change into
  the rendered served page and verify computed styles and behavior on the
  real DOM. Report the result honestly as
  `implementation pass, deployed verification pending`.
- **Source-served** proof exists only after the user has pushed, the
  deploy ran, and the status bridge confirms `deployed_commit` matches
  the local commit (`status-bridge` Rule). Flush caches through the
  bridge when the served read is stale.
- Use Section preview pages (`/section-preview/<section>/<variant>/`)
  when a page-independent check is enough — but remember previews are
  nocache and skip cache/Delay-JS bug classes.
- For layout tickets use the viewport steps relevant to the ticket; the
  full project viewport ladder from `PROJECT-CONTEXT.md` only when the
  ticket is a layout/responsive change across bands.

State plainly which proof mode was achieved (injection-proof vs
bridge-verified served vs code-only).

## 6. Report and approval gate

Report in German: link to the ticket
(`[WP-xxxxx](<jira-site-url>/browse/WP-xxxxx)`), Ursache, Fix (with code
reference), Verifikation (proof mode, what was checked, remaining
caveats like Delay-JS first-touch behavior), and any open follow-ups.

**Stop here and wait for the user's approval ("passt").** Do not
commit or write to Jira before approval. If the user reports issues,
iterate from step 3.

## 7. Closing (only after approval)

1. **Git:** one commit per ticket on the recorded working branch.
   Message references the WP-key and the fix, e.g.
   `Fix - WP-45755 image-boxes swiper slidesPerView auto (mobile left
   alignment)`, with the trailer per the `commit-trailer` Rule.
   Never push; hand over per the `deploy-and-branches` Rule.
2. **Jira:** write a short German solution comment via
   `jira_add_comment` (cause, fix, where verified — including that the
   deploy is pending until the push — commit hash). Never transition
   the ticket status.
3. **Records:** if the ticket closes or changes an item tracked in the
   project backlog or an affected work record's QA/status section,
   update it.
4. If browser work happened, confirm the Playwright lock was released.

## Hard rules

- Never invent ACF keys, WPGB IDs, selectors, URLs, or paths — read
  them from the work records, `PROJECT-CONTEXT.md`, or the live install.
- No temp artifacts in the deploy path; use repo-level `tmp/` if a
  scratch file is unavoidable (`webroot-safety` Rule).
- One ticket per run; if the user hands several tickets at once, run
  them sequentially with separate commits and Jira comments each — or
  route a parent task's subtasks to the bundled `jira-batch-workflow`
  Skill.
