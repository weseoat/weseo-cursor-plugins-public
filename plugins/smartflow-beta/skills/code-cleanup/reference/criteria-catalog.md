# Cleanup Criteria Catalog

Lookup reference for the `code-cleanup` Skill. Each criterion has an ID (used in finding lines and the ledger), the file types it applies to, tags, and the tool that detects it when the project has the optional tooling installed. Without tooling, every criterion is an agent check.

Tags:

- `[auto]` a linter detects it deterministically; `[judgment]` the agent must reason about intent.
- `[safe]` the fix is mechanical and behavior-neutral; `[confirm]` the user decides per finding, and the fix needs a proof.

Precedence reminder: `docs/coding-standard/<language>.md` (the project's real conventions) wins over this catalog; this catalog wins over silence. A documented project deviation from the WordPress handbook is not a finding.

File-type keys: **PHP** = WST Section templates, partials, card templates, singles under `smart-template-builder/`; **CSS** = section, element, CPT, and global style files (SCSS where the project uses it); **SNIP** = `theme-functions.php`, `js-snippets.php` (audit always, edit only with the `file-edit-boundary` confirmation); **ACF** = `acf-json/*.json` (audit only, never written by this Skill).

---

## Pass 1 — Format (mechanical)

| ID | Criterion | Files | Tags | Tool |
|---|---|---|---|---|
| F-01 | Indentation matches the project standard (WordPress default: real tabs in PHP and CSS; a documented project deviation wins); no mixed tabs/spaces in one file | PHP CSS SNIP | auto, safe | `phpcbf`, `stylelint --fix`, `editorconfig-checker` |
| F-02 | No trailing whitespace; final newline present; LF line endings | all | auto, safe | `editorconfig-checker`, `.gitattributes` |
| F-03 | One selector per line, one declaration per line, space after colon, closing brace on its own line | CSS | auto, safe | `stylelint --fix` |
| F-04 | PHP spacing per WPCS: `if ( $x )`, `foo( $a, $b )`, `$a['k']` vs `$a[ $k ]`, `elseif` not `else if`, braces on every block | PHP SNIP | auto, safe | `phpcbf` |
| F-05 | No closing `?>` at end of a pure-PHP file; no short open tags | PHP SNIP | auto, safe | `phpcbf` |
| F-06 | Property order follows the project's documented order (`css-guideline`: positioning → layout → typography → visual → motion) **only when** `docs/coding-standard/css.md` or the project stylelint config declares it | CSS | auto, safe | `stylelint-order` |
| F-07 | Numeric font weights, unitless zero, leading zero, unitless line-height, lowercase hex shortened where identical | CSS | auto, safe | `stylelint --fix` |
| F-08 | Comparison direction (Yoda or not) and array syntax (`array()` or `[]`) match the project convention; when the docs are silent, do **not** flip existing code — record the open convention | PHP SNIP | auto, confirm | `phpcbf` once the ruleset is decided |

## Pass 2 — Leftovers

| ID | Criterion | Files | Tags | Tool |
|---|---|---|---|---|
| L-01 | Debug calls: `var_dump`, `print_r`, `var_export`, `error_log`, `debug_backtrace`, `wp_debug_backtrace_summary`, `die`/`exit` in templates | PHP SNIP | auto, safe | WPCS `WordPress.PHP.DevelopmentFunctions` |
| L-02 | `console.log` / `debugger` in inline scripts | SNIP PHP | auto, safe | grep |
| L-03 | Commented-out code blocks (PHP and CSS; more than a line or two that parses as code). Git remembers — delete, do not keep | PHP CSS SNIP | auto detect, confirm | `Squiz.PHP.CommentedOutCode` |
| L-04 | Unused local variables and unused function parameters | PHP SNIP | auto, safe | PHPCS `VariableAnalysis`, PHPStan |
| L-05 | Custom property defined but never consumed via `var()` anywhere in the theme (search all style files **and** templates with inline `style=` before deciding) | CSS | semi-auto, confirm | `custom-property-no-missing-var-function` + theme-wide grep |
| L-06 | CSS file not registered in the style loader (dead file) or loader entry pointing at a missing file | CSS | semi-auto, confirm | loader read + file list |
| L-07 | Partial or template referenced by no `[wst_include …]`, `include`/`require`, `get_template_part()`, `flexible-content.php` registration, Smart Template assignment, or WPGB card config — **candidate**: needs the full reference search (templates, snippets, work records, bridge WPGB read) before it may be deleted | PHP | semi-auto, confirm | reference graph |
| L-08 | Selector never matching any markup: no class in templates, scripts, WPGB config, `body_class`/`post_class` output, or state toggles (`.is-*`, `.has-*`, `.ast-*`, `.wpgb-*`); CSS coverage across the full QA rung ladder and states as second evidence — never coverage alone | CSS | semi-auto, confirm | Playwright CSS coverage + grep |
| L-09 | Stale `TODO`/`FIXME` without a ticket reference or owner; older than the last cleanup tag | PHP CSS SNIP | auto detect, confirm | grep |
| L-10 | Non-source artifacts inside the deploy path (`*.bak`, `*.orig`, `*.log`, `*.sql`, screenshots, scratch files) — report; deleting untracked files needs the user's file-naming confirmation | all | auto detect, confirm | file list, `webroot-safety` Rule |

## Pass 3 — Comments and filler

| ID | Criterion | Files | Tags | Tool |
|---|---|---|---|---|
| C-01 | Narration comments that restate what the next line does (`// loop over items`, `/* set the color */`) — delete; keep every comment that explains **why** (constraint, workaround, cascade reason) | PHP CSS SNIP | judgment, safe | — |
| C-02 | Docblocks that only restate the signature or are empty templates (`@param mixed $x`, `@return void` with nothing else) | PHP SNIP | judgment, safe | — |
| C-03 | Placeholder comments left in: `// TODO: implement`, `// add logic here`, `// ...` | PHP CSS SNIP | auto detect, safe | grep |
| C-04 | Closing-brace labels (`} // end if`) and section dividers that duplicate structure without adding orientation; **keep** the project's file and section banners (`css-guideline` pattern) | PHP CSS | judgment, safe | — |
| C-05 | LLM filler: conversational comments ("Let's…", "Here we…"), emoji, explanatory prose inside code, redundant inline type annotations in comments | all | judgment, safe | — |
| C-06 | Missing file header banner or `@package` where the project uses them; missing `!important` explanation next to an `!important` that stays | PHP CSS | auto detect, safe | WPCS Docs / grep |

## Pass 4 — Conventions

Most Pass 4 fixes change identifiers or output and therefore carry `[confirm]`; detection is largely automatic.

### PHP templates, partials, snippets

| ID | Criterion | Tags | Tool |
|---|---|---|---|
| K-P01 | Dynamic output escaped **at output** with the context function (`esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`); `get_field()`/`get_sub_field()` are unescaped, `the_field()` escapes since ACF 6.2.7; `do_shortcode()` is not escaping. Adding escaping may change output — render proof mandatory | auto detect, confirm | WPCS `WordPress.Security.EscapeOutput` |
| K-P02 | Inline `style="…"` built from field values wraps `esc_attr()` / `esc_url()` | auto detect, safe | grep |
| K-P03 | Unprefixed globals in snippets: functions, hooks, options, transients, constants, enqueue handles, image sizes (project prefix from `PROJECT-CONTEXT.md` / `docs/coding-standard/php.md`) — a rename is a planned change, report only | auto detect, confirm | WPCS `PrefixAllGlobals` |
| K-P04 | Hard-coded `<script>`, `<link rel="stylesheet">`, `<style>` in templates instead of enqueue / inline-style APIs (project exceptions documented in the docs layer stay) | auto detect, confirm | grep |
| K-P05 | Hard-coded user-facing strings that belong in a field, an options value, or gettext with the project text domain | semi-auto, confirm | grep + judgment |
| K-P06 | WST nesting: no same-named enclosing shortcode nested directly; nested levels carry the `_a`/`_b` suffix chain (`wst-conditional-nesting` Rule, `wst-nested-shortcodes` Skill) | auto detect, confirm | grep |
| K-P07 | Card templates: every `wst_` shortcode that reads a field carries `id='{{post_id}}'` (`grid-cards` Skill) | auto detect, confirm | grep |
| K-P08 | Heading fields: Format sets the tag, Style sets the look; no skipped heading levels in a template (`headline-filling` Skill) | judgment, confirm | — |
| K-P09 | Relative include paths; includes with user-influenced paths | auto, safe/confirm | WPCS |
| K-P10 | Closures registered as hook callbacks (cannot be removed by other code) | auto detect, confirm | grep |
| K-P11 | PHP features above the project's minimum PHP version | auto, confirm | `PHPCompatibilityWP` |
| K-P12 | Lowercase-hyphen file names; class files `class-*.php` (template directories may be excluded by project decision) | auto, confirm | WPCS `Files.FileName` |

### CSS / SCSS

| ID | Criterion | Tags | Tool |
|---|---|---|---|
| K-C01 | Class names follow the project pattern (WESEO default: `wso-` prefix, lowercase, hyphen words; state classes `.is-*`); templates use the same names — a rename touches PHP and is a planned change | auto detect, confirm | `selector-class-pattern` |
| K-C02 | Custom property names kebab-case, scoped per `css-guideline` (component tokens on the component root, global tokens only for shared values) | auto detect, confirm | `custom-property-pattern` |
| K-C03 | Hardcoded palette/font/spacing values where a project token exists (`var(--token)`), including Astra palette values (`--ast-global-color-*`) and brand tokens (`wordpress-brand-tokens` Rule) | semi-auto, safe once the mapping is confirmed | `stylelint-declaration-strict-value`, grep |
| K-C04 | `!important` without an adjacent reason comment, or where a better-scoped selector wins the cascade (verify on the real DOM per `css-guideline`) | auto detect, confirm | `declaration-no-important` |
| K-C05 | Specificity above the project budget, ID selectors, nesting deeper than 2 | auto detect, confirm | `selector-max-specificity`, `selector-max-id`, `max-nesting-depth` |
| K-C06 | Mixed `min-width` / `max-width` direction in one file; breakpoints outside the documented set in `PROJECT-CONTEXT.md` | auto detect, confirm | `media-feature-name-value-allowed-list`, grep |
| K-C07 | Media queries not at the end of the file/section per the project pattern; responsive variable overrides instead of duplicated rule bodies (`css-guideline` file structure) | judgment, confirm | — |
| K-C08 | Hand-written vendor prefixes covered by the project's browser support | auto, safe | `property-no-vendor-prefix` |
| K-C09 | Duplicate selectors inside one file (merge) | auto, safe | `no-duplicate-selectors` |
| K-C10 | Shorthand resetting sub-properties unintentionally (`background:` to set one color, `margin:` to set one side) | judgment, confirm | — |
| K-C11 | Section-level compensation for a global value (container width, type scale) — never fixed here; route to `project-css-setup` | judgment, route | — |
| K-C12 | Selectors that templates, scripts, or Playwright checks rely on are preserved (work-record `CSS Hooks`) — a finding that would touch them is `[confirm]` by definition | judgment, confirm | work record |

### ACF Local JSON (report only — findings go to the WST workflows)

| ID | Criterion | Tags | Tool |
|---|---|---|---|
| K-A01 | Field names `^[a-z0-9_]+$`; CPT fields carry the `wso_<resource>_` prefix (`acf-local-json` Rule) — renames change meta keys, always a migration | auto detect, confirm | `acf-lint`, script |
| K-A02 | Duplicate `key` across all JSON files | auto detect, confirm | script |
| K-A03 | Clone with `display: group` and `prefix_name: 0`; `conditional_logic` on a seamless clone (forbidden pattern) | auto detect, confirm | script |
| K-A04 | Group with empty `location`; `active: false` groups lingering; `private` vs `active` confusion | auto detect, confirm | script |
| K-A05 | `modified` in the future or missing (a future value makes the sync hint permanent) | auto detect, confirm | `acf-lint`, script |
| K-A06 | Fields referenced in templates (`get_field('x')`, `[wst_acf field="x"]`) that exist in no JSON; JSON fields no template reads | semi-auto, confirm | cross-reference |
| K-A07 | Serialization drift from the recorded write format (unicode escaping, indent, trailing newline) | auto detect, confirm | diff against a file the install wrote |

### Repo hygiene (report, fix only with confirmation)

| ID | Criterion | Tags |
|---|---|---|
| K-R01 | `.editorconfig` present (WordPress core shape: tabs, LF, UTF-8, final newline, trim trailing whitespace; YAML 2 spaces) | auto, safe |
| K-R02 | `.gitattributes` normalizes line endings (`* text=auto eol=lf`; `acf-json/*.json text eol=lf`) | auto, safe |
| K-R03 | Minified assets committed without source | auto detect, confirm |
| K-R04 | `docs/`, `.cursor/`, `tmp/`, `acf-json/` located inside/outside the deploy path as designed | auto, safe |

## Pass 5 — Duplication → reuse

Apply the **deletion ladder** to every duplication finding, top to bottom; only what lands on the last rung is simplified in place:

1. **Delete** — the copy is not needed at all (dead branch, second identical rule set).
2. **Reuse** — an existing token, utility class, mixin, partial, or shortcode already does it (search the theme first; name the target in the finding).
3. **Platform** — a WordPress, WST, or ACF function already does it (`wp_kses_post` instead of a hand-rolled strip, `[wst_include]` instead of a pasted partial, `shortcode_atts()` instead of manual defaults).
4. **Simplify in place** — keep, but reduce.

| ID | Criterion | Files | Tags |
|---|---|---|---|
| D-01 | Identical or near-identical declaration blocks across section files → one token/utility or a shared element style; incidental look-alikes that serve different intents stay separate | CSS | judgment, confirm |
| D-02 | The same literal value (color, size, gap, radius) repeated across files where a token exists or the project would introduce one — introducing a **new** token is a proposal, not a cleanup change | CSS | semi-auto, confirm |
| D-03 | Copy-pasted partial fragments (same markup with one attribute changed) → `[wst_include]` of the existing partial with attributes; never a new partial inside this Skill | PHP | judgment, confirm |
| D-04 | Hand-rolled logic that duplicates a core/WST function | PHP SNIP | judgment, confirm |
| D-05 | Repeated inline media queries with the same breakpoint and the same body → variable override per `css-guideline` | CSS | judgment, confirm |
| D-06 | Validation or fallback re-implemented at several call sites → the existing shared check (wrong-altitude fix: prefer one root-cause seam over repeated guards) | PHP SNIP | judgment, confirm |

## Pass 6 — Simplification

One finding at a time; every one `[confirm]` with a before/after proof. Test for each: "Would a new colleague understand the result faster than the original?" If not, revert.

| ID | Criterion | Files | Tags |
|---|---|---|---|
| S-01 | Nesting deeper than three levels → guard clauses / early returns (do not over-apply: only when the guard reads naturally) | PHP SNIP | judgment, confirm |
| S-02 | Over-defensive checks on values that cannot be missing at that point (`isset` on a required field that the layout guarantees, `is_array` after a typed getter, `empty()` chains on constants) — validate at boundaries, not everywhere | PHP SNIP | judgment, confirm |
| S-03 | Single-use wrapper or pass-through helper that only forwards arguments; single-implementation "abstractions" | PHP SNIP | judgment, confirm |
| S-04 | Nested ternaries, long boolean chains → explaining variables or a lookup array | PHP SNIP | judgment, confirm |
| S-05 | Redundant state: a value stored and recomputed, or cached where the source is cheap | PHP SNIP | judgment, confirm |
| S-06 | Over-broad `catch`/silencing (`@`), empty catch blocks — narrow or remove **only** with a proof that nothing depends on the swallow | PHP SNIP | judgment, confirm |
| S-07 | Rule sets that override each other within one file (later rule undoes the earlier one) → single rule with the final value | CSS | judgment, confirm |
| S-08 | Magic numbers explained by a named custom property or a comment — only where the number is not a design measurement already documented in the work record | CSS PHP | judgment, confirm |

---

## AI-Generated Code Smell Checklist

The typical residue of agent-written code; use it as the mental scan during the audit and map each hit to a criterion above.

- Duplication instead of reuse: a new helper or rule that duplicates an existing one under a different name (D-01, D-03, D-04).
- Copy-paste with slight variation: near-identical branches or blocks (D-01, D-03, S-04).
- Hand-rolled logic where a platform function exists (D-04).
- Narration comments, docblocks restating signatures, placeholder TODOs, closing-brace labels, commented-out code (C-01…C-05, L-03).
- Over-defensive code: null checks on guaranteed values, fallbacks for required fields, backward-compatibility shims nobody asked for (S-02).
- Over-broad catch / silenced errors (S-06).
- Dead code: unused variables, unreachable branches, debug leftovers, unreferenced files (L-01…L-08).
- Unnecessary abstraction: single-use helpers, pass-through wrappers, premature interfaces (S-03).
- Deep nesting, nested ternaries, long parameter lists (S-01, S-04).
- Generic or inconsistent naming (`data`, `result`, `temp`; mixed prefixes) (K-C01, K-P03).
- Magic numbers and repeated literals (D-02, S-08).
- Wrong-altitude fixes: a special case at one caller instead of the shared mechanism (D-06).
- Inefficiency that is also complexity: repeated queries or reads in a loop (S-05).
- LLM filler: emoji, conversational comments, template-shaped code (C-05).
- WordPress-specific: unescaped `get_field()` output, escaping too early, `do_shortcode()` treated as escaping, hardcoded palette values where tokens exist, unprefixed globals (K-P01, K-C03, K-P03).

## Safety Rails Behind The Catalog

These are the reasons the tags are what they are; they are not optional per finding.

- Behavior preservation is absolute; a proof diff decides, not confidence.
- Lock behavior before touching: screenshot / HTML snapshot baseline first; no baseline → no change.
- One smell category per pass, safest first, verified and committed per pass.
- Scope to what changed; report the rest.
- Understand before removing (who calls it, who toggles it, why it was written — `git log -S`/`git blame` when unclear).
- Guard removal needs proof that nothing depends on the guard.
- Keep incidental duplication apart; do not force a shared abstraction.
- No new abstractions, dependencies, renames, or public-signature changes during cleanup.
- Clarity over brevity; a shorter but harder-to-read result is reverted.
- Default action when in doubt is skip, not guess.
- Three occurrences of the same finding are a convention gap → propose a rule, do not keep fixing by hand.

## Sources

Consolidated from the WordPress PHP and CSS coding standards (handbook, 2025/2026 revisions), WordPressCS 3.x sniff set, the WordPress theme review requirements, the ACF security and Local JSON documentation (escaping behavior since 6.2.5–6.2.7), Astra theme developer references (palette variables, filterable breakpoints, enqueue order), the 10up / Human Made / WordPress VIP engineering standards, the stylelint 17 rule set, Kent Beck's *Tidy First?* tidyings, Martin Fowler's refactoring definition and two-hats rule, Google's small-CL guidance, Anthropic's `/simplify` and `code-simplifier` cleanup angles (reuse, simplification, efficiency, altitude), the community `clean-ai-slop` / `remove-ai-slops` pass orders and deletion ladder, Addy Osmani's code-simplification checklist, and the GitClear 2025/2026 code-quality reports on duplication and refactoring trends in AI-assisted repositories.
