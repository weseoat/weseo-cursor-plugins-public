---
name: wst-shortcodes
description: Look up WST shortcode and SmartTag forms in the bundled Smart Template Gesamthilfe catalog snapshot and run the four-source proof before any new WST shortcode form is used in a template. Use when authoring or reviewing WST template markup, when a shortcode form or attribute is unknown, when catalog and runtime seem to disagree, or when another Skill asks for the four-source proof.
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

## Known catalog discrepancies (unverified at runtime)

Treat these as open questions, not as settled facts, until a runtime test on a real project decides them; then record the outcome in the project docs layer and report it back:

- **`{{post.id}}` vs `{{post_id}}` in WP Grid Builder cards.** The catalog names `{{post.id}}` in three places; a working project implementation builds on `{{post_id}}`. Only the WPGB card runtime can settle which spelling resolves.
- **Suffix chain for nested conditionals.** The catalog's example nests `wst_if_b` directly inside `wst_if`; the `wst-conditional-nesting` Rule requires the strict chain `wst_if` -> `wst_if_a` -> `wst_if_b` and warns that same-named nesting fails silently. Until runtime validation says otherwise, the Rule wins: never nest the same tag name, follow the named-level chain, prefer `wst_include` for shared partials.

## Working method

1. Check the project's templates first: an existing verified usage is the fastest correct answer and already satisfies sources 3 and 4.
2. Search the snapshot for the shortcode name or the relevant `### 5.x` heading; read only that section.
3. For a new form, complete the four-source proof before writing it into a template. A preview-harness fixture is the cheapest controlled test render (see the bundled `section-preview-harness` Skill).
4. Never invent attributes, SmartTag paths, or nesting shapes to "try out" in tracked templates; unproven experiments belong in a test render, not in a commit.
