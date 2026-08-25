# Section Handoffs

Section handoffs start as the `grill-me` preflight draft before server-side WordPress/WST Section implementation begins, then remain the checkpoint between the server phase and the local frontend phase.

Use one handoff document per Flexible Content Section. The document travels with the same Git branch or PR as the Section work, even when the same person or agent performs both phases. Server work writes the WordPress, WST, ACF, template, content, and cache context. Local work reads that context, implements CSS/SCSS, runs visual checks, and commits the final code.

## Files

```text
section-handoffs/
  README.md
  section-handoff.template.md
  examples/
    feature-cards-section.md
schemas/
  section-handoff.schema.json
scripts/
  validate-section-handoffs.py
```

## Canonical Template

`section-handoffs/section-handoff.template.md` is an exact copy of the canonical plugin template (`plugins/wst-builder/handoffs/section-handoff.template.md`; the legacy beta twin was removed in the 2026-08 cleanup). Never edit the repo copy directly; edit the plugin template and copy it over. `scripts/validate-section-handoffs.py` fails when the two drift apart.

## Create A Handoff

1. Copy `section-handoffs/section-handoff.template.md`.
2. Save the concrete handoff in the project-configured handoff storage location from Project Context.
3. Search Project Context for URLs, paths, IDs, selectors, ACF references, and storage before asking the maintainer.
4. Fill every required table value before creating or modifying Section files, ACF structures, or local frontend work.
5. Use explicit `<unresolved: ...>` placeholders for missing blocking values instead of inventing technical values.
6. Keep the file on the same Git branch or PR as the Section code.
7. Run `python scripts/validate-section-handoffs.py` when the filled handoff lives in this repository.

## Required Contract

Each handoff must record:

- Handoff carrier: project, branch or PR, handoff filename, project-configured storage location, owner, server phase status, local phase status.
- Workflow routing: work type, environment, server write scope, frontend route, discovery and safety status, live/unknown write confirmation, CSS status.
- Section identity: Section name, slug, layout name, page URL, design desktop and design mobile references (or an explicit `no-mobile-design: derived-from-desktop` note), source design status, variants/states.
- Discovery sources: original Figma/source links (desktop and mobile), test placement, similar Section patterns inspected, applied WST/ACF rules, media lookup, project-local examples, and recorded assumptions.
- WordPress and WST references: template file, CSS file, ACF section field group, Flexible Content field, Flexible Content layout, clone child field, generated field or layout keys, and content setup notes.
- Protected existing artifacts: filled for `existing-section-remodel` work types, `not-applicable` otherwise.
- CSS hooks: primary section class, expected wrapper/classes, custom properties, and selectors the local phase should use.
- Visual QA targets: a viewport-role mapping plus a matrix with one row per verifiable expectation (variant, viewport, expectation, result), covering the mandatory base variants or marking them `n/a` with a reason.
- Frontend QA brief: the verifiable starting point `wst-section-workflow` writes before routing to `frontend-section-qa`.
- QA notes: browser QA target URL, local Playwright MCP status, required viewports, access blockers, screenshot policy, checks to run, project-local Playwright command, server verification result, cache state, known risks, and QA result.
- Responsibility split: server-phase checklist and local frontend checklist.

## Responsibility Split

Server phase responsibilities include WST template creation, ACF field group and Flexible Content wiring, content setup, section registration, and cache flushing.

Local frontend responsibilities include CSS/SCSS implementation, Chrome Local Overrides spikes if needed, Playwright checks, responsive verification, final commits, and updating the handoff with QA results.

## Validation

Run:

```sh
python scripts/validate-section-handoffs.py
```

The validator checks filled handoff documents and examples for required headings, required field values, CSS hooks, the Visual QA Targets matrix, QA notes, and both server/local responsibility sections. It ignores the template's placeholder values but verifies that the repo template is an exact copy of a canonical plugin template, so the contract cannot drift into a third variant.
