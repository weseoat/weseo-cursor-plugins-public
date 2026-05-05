# Section Handoffs

Section handoffs are the checkpoint between the server-side WordPress/WST phase and the local frontend phase.

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

## Create A Handoff

1. Copy `section-handoffs/section-handoff.template.md`.
2. Save it as `section-handoffs/<section-slug>.md` or inside the project-local handoff directory used by the current branch.
3. Fill every required table value before starting local frontend work.
4. Keep the file on the same Git branch or PR as the Section code.
5. Run `python scripts/validate-section-handoffs.py`.

## Required Contract

Each handoff must record:

- Handoff carrier: project, branch or PR, owner, server phase status, local phase status.
- Section identity: Section name, layout name, page URL, source design/reference.
- WordPress and WST references: template file, CSS file, ACF section field group, Flexible Content layout, generated field or layout keys, and content setup notes.
- CSS hooks: primary section class, expected wrapper/classes, custom properties, and selectors the local phase should use.
- Expected visual behavior: responsive behavior, states, content variations, and design constraints.
- QA notes: cache state, Playwright target URL, checks to run, and known risks.
- Responsibility split: server-phase checklist and local frontend checklist.

## Responsibility Split

Server phase responsibilities include WST template creation, ACF field group and Flexible Content wiring, content setup, section registration, and cache flushing.

Local frontend responsibilities include CSS/SCSS implementation, Chrome Local Overrides spikes if needed, Playwright checks, responsive verification, final commits, and updating the handoff with QA results.

## Validation

Run:

```sh
python scripts/validate-section-handoffs.py
```

The validator checks filled handoff documents and examples for required headings, required field values, CSS hooks, QA notes, and both server/local responsibility sections. It ignores the template because the template intentionally contains placeholders.
