---
name: wst-shortcodes
description: Look up WST shortcode and SmartTag forms in the bundled Smart Template Gesamthilfe catalog snapshot and run the four-source proof - per form and nesting context (flat, loop-1, loop-2+), with the result vocabulary proven / proven except runtime - before any new WST shortcode form is used in a template; carries the project-verified runtime constraints for deep loops (wst_string_replace, wst_variable getters, loop post IDs). Use when authoring or reviewing WST template markup, when a shortcode form or attribute is unknown, when catalog and runtime seem to disagree, or when another Skill asks for the four-source proof.
---

# WST Shortcodes (catalog entry)

This Skill is the entry point into the WST shortcode catalog. It owns two things: how to read the bundled `SMART-TEMPLATE-HILFE.md` snapshot efficiently, and the four-source proof that gates every new WST shortcode form before it lands in a project template.

WST templates are a shortcode DSL (`[wst_*]` shortcodes plus `{{smarttag}}` placeholders) parsed by `wst_do_shortcode()`. Inventing or misremembering a form fails silently more often than loudly, so unproven forms never go straight into customer-facing templates.

## The catalog snapshot

`reference/SMART-TEMPLATE-HILFE.md` is the versioned Smart Template / Smart Template Builder Gesamthilfe snapshot (German; exported 2026-07-16; Smart Template 6.19.4, Builder 1.6.2). It is the single catalog source for WST shortcode forms in SmartFlow — the 159 individual per-shortcode skills from the legacy skill zip are deliberately not maintained. Cross-cutting mechanics live in dedicated bundled Skills instead: `smarttags` (SmartTag placeholder syntax), `wst-nested-shortcodes` (suffix rules for nesting), and `grid-cards` (WPGB card templates).

Usage rules:

- **Load only the needed sections** — the file is ~394 KB. Search for the shortcode name or jump by heading; never read the whole file.
- Chapter 5 (`## 5. Vollständiger Shortcode- und SmartTag-Katalog`) is the catalog: `### 5.1 Allgemein` through `### 5.30 CMB2 Layout`, including `### 5.10 Page Builder`, `### 5.11 Grid Plugins`, the ACF chapters `5.12`-`5.18`, `### 5.19 Bedingte Logik` (conditionals), and `### 5.24 SmartTags`.
- Chapter 6 documents the PHP helper functions, chapter 8 the Smart Template Builder internals (rendering flow, template resolution and overrides, ACF behavior, Polylang behavior).
- The snapshot is a readable reference, **not the runtime truth**. The installed plugin version, an existing project usage, and the rendered HTML remain authoritative; on conflict they win.
- Re-export the snapshot only when the installed plugin version changes; the export date and covered versions above must be updated with it. If a project runs a different plugin version than the snapshot, treat any behavioral difference as a runtime question, not a catalog fact.

## The four-source proof

Every WST shortcode form that is new to the project (a shortcode, attribute, SmartTag, or nesting shape not already used in the project's templates) must be proven from four sources before it ships in a template:

1. **Catalog:** the targeted section of the bundled snapshot documents the form.
2. **Installed runtime:** the form is supported by the plugin version actually installed on the project (version recorded in `PROJECT-CONTEXT.md` at setup, or confirmed by the user from the admin). A snapshot entry newer or older than the installed version proves nothing by itself.
3. **Project precedent:** an existing usage in the project's templates (search `smart-template-builder/`), or — when the form is genuinely new to the project — the nearest verified precedent plus an explicit note that none exists.
4. **Rendered HTML:** the form demonstrably resolves in this install — on an existing served page for precedented forms, or through a minimal test render (Section preview page fixture or test page row) for new forms, before it lands in a customer-facing template.

On any conflict between the sources, runtime evidence (2 and 4) wins over the catalog (1). Record the conflict and its resolution in the project docs layer, and report the delta so the catalog snapshot or the affected Rule can be corrected.

The WST workflow Skills (`wst-section-workflow`, `wst-new-post-type`) require this proof for every new shortcode form, and the `wst-shortcode-implementer` runner treats it as a gate in its return format.

### The proof is per form × nesting context

WST shortcodes resolve differently depending on where they sit: the order of shortcode resolution, the loop context SmartTags read from, and the visibility of `wst_variable` values all change with nesting depth. Sources 3 and 4 therefore count only for the **same context class** as the intended usage:

| Context class | Meaning |
|---|---|
| `flat` | template level, outside any WST loop |
| `loop-1` | inside one WST loop (`wst_acf_repeater`, `wst_posts`, `wst_acf_post_object_*`, WPGB card) |
| `loop-2+` | inside nested loops, **or** as shortcode content inside another shortcode's content (for example inside `[wst_string_replace]…[/wst_string_replace]`) |

A precedent or a served render from `flat` proves nothing for `loop-2+`. When the project has no precedent in the target context and no test render is possible before the deploy — the normal case in a shell-less SmartFlow workspace, because preview fixtures and test-page rows themselves need a deploy — the proof result is **not** `passed`. The result vocabulary is:

- `proven` — all four sources in the target context.
- `proven except runtime (<context>)` — sources 1–3 in the target context, source 4 missing; the first deploy is the runtime test.

`proven except runtime` is a named runtime risk, not a pass. It goes into the work record, into the runner's `GATES` line, and into the deploy hand-over as a concrete check request ("erster Deploy ist der Runtime-Test — bitte Ausgabe X auf Seite Y prüfen"). After the served check confirms the output, upgrade the record to `proven` and add the usage to the project's precedent inventory.

## Known runtime constraints (verified in projects)

Unlike the discrepancies below, these are **settled** by runtime evidence in a real project. Treat them as facts for the named WST version and check the constraint before choosing a form:

- **(a) `wst_string_replace` cannot manipulate dynamically resolved content in `loop-2+`.** Neither an inner shortcode (`[wst_acf]`, `[wst_post_meta]`) nor a SmartTag loop read (`{{field/loop}}`) inside its content is stripped or replaced there; the raw value (for example `2.00`) survives. For number formatting use a self-formatting shortcode instead: `[wst_acf_number field='<field-key>' id='{{post_id/<loop>}}' decimals='0']` or `wst_number_format`. In `flat` context the `string_replace` wrapper works as documented. (Quality Austria, 2026-09-24, WST 6.19.x)
- **(b) `[wst_variable]` getters inside shortcode content in a deep loop return empty**, and `{{$var}}` getters resolve before the `SET` that should feed them. Use direct field access (`wst_acf_number`, `{{count_posts|…}}`) instead of variable indirection in `loop-2+`. (Quality Austria, 2026-09-24, WST 6.19.x)
- **(c) `{{loopname}}` without a field is invalid.** The loop's post ID is `{{post_id/loopname}}`. (Quality Austria, 2026-09-24, WST 6.19.x)

Record new constraints here with project, date, and WST version once a served check settles them; the catalog snapshot itself stays an untouched export of the vendor help.

## Known catalog discrepancies (unverified at runtime)

Treat these as open questions, not as settled facts, until a runtime test on a real project decides them; then record the outcome in the project docs layer and report it back:

- **`{{post.id}}` vs `{{post_id}}` in WP Grid Builder cards.** The catalog names `{{post.id}}` in three places; a working project implementation builds on `{{post_id}}`. Only the WPGB card runtime can settle which spelling resolves.
- **Suffix chain for nested conditionals.** The catalog's example nests `wst_if_b` directly inside `wst_if`; the `wst-conditional-nesting` Rule requires the strict chain `wst_if` -> `wst_if_a` -> `wst_if_b` and warns that same-named nesting fails silently. Until runtime validation says otherwise, the Rule wins: never nest the same tag name, follow the named-level chain, prefer `wst_include` for shared partials.

## Working method

1. Check the project's templates first: an existing verified usage **in the same nesting context** is the fastest correct answer and already satisfies sources 3 and 4.
2. Search the snapshot for the shortcode name or the relevant `### 5.x` heading; read only that section. Check "Known runtime constraints" for the target context.
3. For a new form, complete the four-source proof before writing it into a template. A preview-harness fixture is the cheapest controlled test render (see the bundled `section-preview-harness` Skill). When no test render is possible before the deploy, name the result `proven except runtime (<context>)` and choose the form with the least runtime dependency (self-formatting shortcode over string manipulation, field key over field name for imported data, direct field access over variable indirection); name the riskier form as an alternative in the work record, do not ship it.
4. Never invent attributes, SmartTag paths, or nesting shapes to "try out" in tracked templates; unproven experiments belong in a test render, not in a commit.
