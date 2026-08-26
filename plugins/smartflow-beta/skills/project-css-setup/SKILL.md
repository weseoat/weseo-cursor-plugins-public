---
name: project-css-setup
description: Reconcile a project's global theme CSS values with its Figma design in one resumable pass - content widths, fonts, colors/brand tokens, buttons and global spacing. Use when PROJECT-CONTEXT.md carries css_setup pending (theme still serves master-install values), when frontend Section QA measured a container mismatch and routed here, when a new project needs its CSS setup shortly before the first Section, or when global tokens must be re-derived from the design on a grown project. Per-value confirmation with an impact list from the docs layer plus var() consumer search, one-anchor rem derivation with full viewport-ladder measurement, injection-proofed writes, one bundled commit with hard stop.
---

# Project CSS Setup

Run this Skill to perform the CSS setup of a whole project in one pass: extract the global values from the design and reconcile them with what the theme currently carries. It owns the project-level CSS values — container content widths, font families and type scale, colors and brand tokens, button and global spacing defaults — and the `css_setup` marker in `PROJECT-CONTEXT.md`. It does not own Section-local CSS; that stays with `frontend-section-qa`.

Every WESEO project starts as a clone of a fully loaded master WordPress installation: all Sections present, a few basic CPTs, and the master's CSS values. There is no greenfield project. "New project" therefore means "the CSS values are still the master's, never reconciled with this project's design" — and because the Section inventory exists from day one, every global value change has consumers from day one. That is why this Skill pairs every change with an impact map, and why a global container mismatch must never be compensated inside a Section (the incident that produced this Skill).

The right moment for this pass is shortly before the first Section is built — at infrastructure-setup time the Figma design is often not final. `setup-local-project` writes the `css_setup: pending` marker so the pass cannot be forgotten; `frontend-section-qa` routes here when its layout preflight measures an actual container mismatch.

This Skill runs in the main chat by design and is deliberately not routed to a runner (`agent-routing` Rule): the per-value confirmations are interactive and the user decides per value.

The target user is a frontend or design colleague. Communicate in German for all user-facing steps; keep commands, file names, selectors, and token names in their original language. Every per-value confirmation shows: the theme value, the design value, the derivation, and the affected Sections. End each block with `Erledigt: <result>` or `Offen: <open point> - nächster Schritt: <action>`.

## Entry: Read The Marker And Pick The Mode

Re-read `PROJECT-CONTEXT.md` on every invocation:

1. **`css_setup: pending`** — the theme still carries master values. Master-values mode: values are set directly from the design. The per-value confirmation still runs (with the impact list), but there is no theme-vs-design negotiation — the master's value is not a project decision worth defending.
2. **`css_setup: reconciled (<date>)`** — the values were already adapted for this project. Adapted mode: show the theme-vs-design diff per value and let the user decide per value, because a content-width change affects every built Section.
3. **Marker missing** — a project set up before the marker existed. Ask the user whether the CSS values were ever reconciled with this project's design; when unsure, use adapted mode (the safer per-value diff) and add the marker rows to `PROJECT-CONTEXT.md`.

Then read the per-block status lines and resume at the first block that is not `done` or `skipped`:

```text
css_setup: pending | reconciled (<date>)
css_setup_widths: pending | in-progress | done (<date>) | skipped: <reason>
css_setup_fonts: pending | in-progress | done (<date>) | skipped: <reason>
css_setup_colors: pending | in-progress | done (<date>) | skipped: <reason>
css_setup_buttons: pending | in-progress | done (<date>) | skipped: <reason>
```

Blocks are individually completable: an unfinished color topic does not block finished widths. A `skipped` block needs a recorded reason (for example `design uses theme defaults`).

## Inputs

Read before any value work:

- `PROJECT-CONTEXT.md`: the `css_setup` marker and block statuses, `Cloned from` (the master installation), theme tokens, container widths, breakpoints and QA viewport rungs, rem scale and per-band rem bases, style paths and loader, working branch, bridge base URL and credential env var names.
- The project's Figma design: the desktop frames (1920 anchor) and the mobile frames (375 anchor). If the design is not final or not available, stop — record `Offen` with the reason; running this pass against a draft design produces values that will be re-litigated.
- The project docs layer (`docs/sections/`, `docs/cpt/`, per `auto-docs`/`cpt-docs`): work records with CSS hooks and used tokens — the primary source for the impact map.
- The gating Rules: `css-guideline` (proof modes, tokens, selectors), `figma-to-code`, `frontend-section-qa-tablet-band` (the viewport ladder shape), `playwright-browser-claim` (when the project runs parallel Playwright servers).
- The bundled measurement reference `reference/viewport-measurement.md` — all viewport measurements in this Skill run through that snippet, so every project measures the same way.

Do not invent design values, token names, container widths, or rem bases. If a value is not readable from the design, the theme, or the project context, ask.

## The Block Loop

Each of the four blocks runs the same loop:

1. **Extract from the design:** read the block's values from the Figma frames (1920 desktop anchor, 375 mobile anchor) via Figma MCP.
2. **Compare with the theme:** read what the theme CSS currently carries for the same values (token files, container/typography/button rules).
3. **Impact map, then user confirms per value** (next section): master-values mode confirms the design value; adapted mode decides between theme and design value.
4. **Write:** injection-proof the change on the served page first, then write the tracked theme CSS and the `PROJECT-CONTEXT.md` value block.
5. **Verify in the browser:** walk the viewport ladder with the measurement reference; set per-breakpoint overrides only where a rung visually fails.

Record the block status when the loop closes; then move to the next block.

## Impact Map Before Every Value Change

A fixed step before any value is changed — the Skill must know which Sections hang on the value:

1. **Build the affected list:** read the docs-layer work records (CSS hooks, used tokens) and additionally search the theme CSS for consumers of the token (`var(--<token>)`) as the net for anything the docs miss. For container widths, every Section inside the container is affected by definition.
2. **Show the list:** the per-value confirmation shows not only "theme says X, design says Y" but "betrifft: diese N Sections" with the list.
3. **The list becomes the check plan:** the post-change Playwright verification spot-checks exactly those Sections (or a sensible sample when the list is long — say which).
4. **Write gaps back:** consumers found via CSS search but missing in the docs layer are added to the work records — the reconciliation improves the docs as a side effect.

## Value Derivation: Calculate Once At The Anchor, Measure Everywhere Else

WESEO themes use a fluid rem basis that steps per breakpoint band. That basis already encodes proportional behavior, so:

1. **One anchor:** convert the design value at 1920 into rem at the anchor rem basis and set it. Non-normative example shape: design content width 1640px at a 20px anchor basis → `82rem`.
2. **The fluid rem basis does the scaling:** the same rem number already yields fewer px on smaller viewports. Do **not** re-derive percentages or ratios per breakpoint — that shrinks twice. Viewport caps (for example `90vw`) stay, because the basis steps instead of scaling continuously.
3. **Walk the full viewport ladder in the browser** (1920 → 1440 → 1024 → 991 → 921 → 767 → 575 → 375, per the `frontend-section-qa-tablet-band` Rule and the project's recorded rungs): measure the real px result per rung with `reference/viewport-measurement.md`. Check nothing is broken or unintentional. Note the 991 execution caveat: in Playwright MCP, verify the 991 row at **990** (fractional devicePixelRatio); real devices are unaffected.
4. **375 is the second design anchor:** check the measured result against the mobile design frame, not against a calculation.
5. **Targeted overrides only:** where a rung visually fails, set a per-breakpoint override for exactly that band — and record it in `PROJECT-CONTEXT.md` with a short reason.

`PROJECT-CONTEXT.md` records which values come from the design (with the design px and the anchor basis) and which per-breakpoint overrides exist, with reasons.

## Block 1: Content Widths

The most important block — an unreconciled content width masquerades as Section-level bugs on every page. Extract the design's content width and side margins at 1920, derive the rem value at the anchor basis, compare with the theme's container tokens and viewport caps, run the impact map (every Section is affected), confirm, injection-proof, write, and walk the ladder measuring the container inner width per rung. Flipping the `css_setup` marker requires at minimum this block (see the close-out section).

## Block 2: Fonts

Font families and the size scale for headings and body text. Compare the design's families against the theme's font tokens and loaded fonts (a missing font file or `@font-face` is an open item for the user, not something this Skill downloads ad hoc). Derive heading and body sizes at the 1920 anchor, verify the mobile scale against the 375 frame, and respect the theme's existing typography token structure — adjust token values, do not invent a parallel scale.

## Block 3: Colors And Brand Tokens

Master colors out, project colors in. Map the design's palette onto the theme's color tokens; where the project uses `.brand-` scoped blocks, fill them per the project pattern. Watch for master colors hardcoded outside the token files (the `var()` search catches token consumers; a hex search catches hardcoded leftovers) and route hardcoded findings into the impact list.

## Block 4: Buttons And Global Spacing

Only where the design clearly deviates from the theme defaults: button shape, padding, radius, hover behavior, and global spacing tokens (section gaps, default vertical rhythm). If the design matches the theme defaults, record the block as `done` with a one-line note instead of rewriting matching values.

## Writes, Marker Flip, And The Deploy Pass

The write mode is the standard SmartFlow chain — no extra approval layer on top of the per-value confirmations:

1. **Injection-proof before writing** (`css-guideline` Rule): inject the planned value change into the served page through Playwright MCP (claim a browser per `playwright-browser-claim` when the project runs parallel servers), confirm it wins the cascade, and run the impact-list spot checks plus the ladder measurements there.
2. **Write tracked source:** the theme CSS (content-width file, token file, typography — the project's actual style paths from `PROJECT-CONTEXT.md`) and the `PROJECT-CONTEXT.md` value blocks (Theme Tokens, Container Widths, Breakpoints & QA-Viewports), plus the docs-layer writebacks from the impact map.
3. **Flip the marker** when the widths block (at minimum) is done: `css_setup: reconciled (<date>)`. The per-block lines keep the fine-grained state for unfinished blocks.
4. **One bundled deploy pass** (`deploy-and-branches` Rule): commit everything from the session together with the `Made with: SmartFlow` trailer. HARD STOP — hand over with the commit hash and what the deploy will deliver; the user pushes.
5. **Bridge verification and served spot check:** after the user reports pushing, verify `deployed_commit` over the status bridge per the `status-bridge` Rule (bounded retries), then run one served spot check of the impact list. Until that succeeds, the result is `implementation pass, deployed verification pending`, never a final pass. Release any claimed Playwright browser lock when the browser work is finished.

## Stop Conditions

Stop and ask before or when:

- The Figma design is missing, clearly a draft, or the user says it is not final — record the open point; the pass runs shortly before the first Section, not earlier.
- A value cannot be read from the design or the theme and would have to be invented.
- An affected-Sections list is surprisingly large or contains Sections with recorded custom compensations — those compensations may need removal in the same pass, which the user must decide.
- Any commit (the agent never pushes; the user pushes).

## Scope Boundaries

This Skill changes project-global CSS values and their `PROJECT-CONTEXT.md` records. It does not build or style individual Sections (`frontend-section-qa`), does not touch WST templates, ACF JSON, or Flexible Content (`wst-section-workflow`), and does not perform the initial workspace setup (`setup-local-project` — which writes the `css_setup: pending` marker this Skill consumes). Section-local compensation for a global mismatch is out of bounds everywhere: the fix belongs here, at the project level.
