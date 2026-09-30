# Optional Tooling, Hooks, And Recurrence Options

Supporting reference for the `code-cleanup` Skill. Nothing here is required: the Skill runs fully as agent checks. Tooling makes the mechanical passes deterministic and cheap, and it is the only way to get `[auto]` findings that do not depend on the agent's attention. Every installation is an **opt-in step confirmed by the user** and recorded in `PROJECT-CONTEXT.md`; the cleanup run itself never installs anything.

State as of September 2026 — re-verify versions before installing; the pins below exist for a reason.

## Where Tooling Lives

The repository root is the wp-content level and is never served; only the child theme is deployed (`webroot-safety` Rule). Tooling therefore lives **at the repository root**, outside the deploy path:

```text
<repo-root>/
├── composer.json, composer.lock, vendor/      # PHP tooling (never inside themes/)
├── package.json, node_modules/                # CSS tooling (never inside themes/)
├── phpcs.xml.dist, stylelint.config.mjs, .editorconfig, .gitattributes
├── .githooks/pre-commit                       # exists when the ACF pull hook is installed
└── themes/<child-theme>/                      # deploy path — no tooling files here
```

Projects with an allowlist `.gitignore` must release the config files and ignore `vendor/` and `node_modules/`. Check the project's ignore model before adding files.

`PROJECT-CONTEXT.md` markers written by the setup step:

```text
cleanup_tooling: none | phpcs | phpcs+stylelint | phpcs+stylelint+editorconfig
cleanup_hook: none | pre-commit (report) | pre-commit (fix)
cleanup_last_tag: cleanup/<YYYY-MM-DD>
```

## Tooling Ladder (Detection In The Audit Step)

```powershell
Test-Path vendor/bin/phpcs; Test-Path vendor/bin/phpcbf
Test-Path node_modules/.bin/stylelint
Get-Command editorconfig-checker -ErrorAction SilentlyContinue
Get-Command acf-lint -ErrorAction SilentlyContinue
```

Present → run in report mode on the scope files; absent → agent checks. Never mix a fixer run into the audit step.

## PHP: PHP_CodeSniffer + WordPressCS

- **WordPressCS 3.4.1** (July 2026, security release — update below that) still requires **PHP_CodeSniffer 3.13.x**; the 4.x line is not usable with WPCS via Composer yet. Pin accordingly and expect a WPCS major when the switch happens.
- **PHPCompatibilityWP 2.1.x** is the stable line (PHP 8 sniff coverage is incomplete; the 3.0 alpha on PHPCompatibility 10 covers PHP 8.0–8.5 but is alpha).
- Composer is the only supported install path.

```powershell
composer config allow-plugins.dealerdirect/phpcodesniffer-composer-installer true
composer require --dev "wp-coding-standards/wpcs:^3.4.1" "phpcompatibility/phpcompatibility-wp:^2.1.8" "sirbrillig/phpcs-variable-analysis"
```

Minimal `phpcs.xml.dist` tuned for WST template code (placeholders in angle brackets come from `PROJECT-CONTEXT.md` / `docs/coding-standard/php.md`):

```xml
<?xml version="1.0"?>
<ruleset name="<project> child theme">
	<file>themes/<child-theme></file>
	<exclude-pattern>*/vendor/*</exclude-pattern>
	<exclude-pattern>*/node_modules/*</exclude-pattern>
	<exclude-pattern>*.min.js</exclude-pattern>

	<arg name="basepath" value="."/>
	<arg name="extensions" value="php"/>
	<arg name="parallel" value="8"/>
	<arg value="ps"/>

	<rule ref="WordPress-Extra">
		<!-- WST template and partial files are not class files -->
		<exclude name="WordPress.Files.FileName"/>
	</rule>
	<rule ref="VariableAnalysis"/>

	<config name="minimum_wp_version" value="<min-wp-version>"/>
	<config name="testVersion" value="<min-php>-"/>
	<rule ref="PHPCompatibilityWP"/>

	<rule ref="WordPress.NamingConventions.PrefixAllGlobals">
		<properties>
			<property name="prefixes" type="array">
				<element value="<project-prefix>"/>
			</property>
		</properties>
	</rule>
	<rule ref="WordPress.WP.I18n">
		<properties>
			<property name="text_domain" type="array">
				<element value="<text-domain>"/>
			</property>
		</properties>
	</rule>
</ruleset>
```

Project-convention adjustments — decide once with the user, record in `docs/coding-standard/php.md`, mirror in the ruleset:

- Short array syntax allowed → exclude `Universal.Arrays.DisallowShortArraySyntax`.
- Non-Yoda comparisons → exclude `WordPress.PHP.YodaConditions`, add `Generic.ControlStructures.DisallowYodaConditions`.
- Docblock requirements → add `WordPress-Docs` only if the project wants it (noisy on template code).

Known noise on WST code: `WordPress.Security.EscapeOutput` flags `echo do_shortcode(...)` and echoed pre-built HTML — correct in principle (`do_shortcode()` does not escape), but the fix is a per-finding decision with a render proof (criterion K-P01), never a blanket `wp_kses_post()`.

Commands used by the Skill:

```powershell
vendor/bin/phpcs <files>              # audit (report mode)
vendor/bin/phpcbf <files>             # Pass 1 fixer on scope files only — on child-theme PHP this runs inside the wst-shortcode-implementer spawn (wst-php-authoring-route), not in the main chat
vendor/bin/phpcs --filter=GitStaged . # staged files (case-sensitive filter name; lints working-tree content)
```

## PHP: PHPStan (optional, second step)

`phpstan/phpstan ^2.2` with `szepeviktor/phpstan-wordpress ^2.0` (WordPress stubs) and `php-stubs/acf-pro-stubs` in `scanFiles`; start at level 5, baseline once, ratchet up. Catches real defects no sniff sees (wrong ACF return handling, undefined functions). Report-only; it never fixes. Skip it until the WPCS layer is routine.

## CSS / SCSS: stylelint

- **stylelint 17** (ESM-only, Node ≥ 20.19, nesting-aware specificity rules). `@wordpress/stylelint-config` still peers on stylelint 16 — choose one line: stylelint 16 + the WordPress config for handbook parity, or stylelint 17 + `stylelint-config-standard-scss` for a project with its own `wso-` conventions (the usual choice).

```powershell
npm i -D stylelint@^17 stylelint-config-standard-scss@^17 stylelint-order stylelint-declaration-strict-value
```

Minimal `stylelint.config.mjs`:

```js
export default {
	extends: ['stylelint-config-standard-scss'],
	plugins: ['stylelint-order', 'stylelint-declaration-strict-value'],
	rules: {
		'selector-class-pattern': ['^(wso|is|has|js)-[a-z0-9]+(-[a-z0-9]+)*$', { resolveNestedSelectors: true }],
		'custom-property-pattern': '^[a-z0-9]+(-[a-z0-9]+)*$',
		'selector-max-id': 0,
		'selector-max-specificity': '<project-budget, e.g. 0,4,0>',
		'max-nesting-depth': 2,
		'declaration-no-important': [true, { severity: 'warning' }],
		'declaration-block-no-duplicate-properties': [true, { ignore: ['consecutive-duplicates-with-different-values'] }],
		'no-duplicate-selectors': true,
		'property-no-vendor-prefix': true,
		'order/properties-order': [ /* positioning, layout, typography, visual, motion — from docs/coding-standard/css.md */ ],
		'scale-unlimited/declaration-strict-value': [
			['/color$/', 'font-family', 'z-index'],
			{ ignoreValues: ['transparent', 'inherit', 'currentColor', 'initial', 'unset'] },
		],
	},
};
```

Adjust the class pattern to the project's documented prefixes (Astra `ast-*`, WPGB `wpgb-*`, and theme-level classes are matched by templates, not by this pattern — only lint the project's own files). `declaration-no-important` and the specificity rules stay report-only: the fix is a better-scoped selector verified on the real cascade, never automatic.

Commands:

```powershell
npx stylelint <files>          # audit
npx stylelint --fix <files>    # Pass 1 fixer on scope files only
```

## Line Endings And Indentation

- `.editorconfig` at the repository root in the WordPress core shape (tabs for all files, LF, UTF-8, final newline, trim trailing whitespace; YAML 2 spaces; Markdown keeps trailing whitespace). A project that indents CSS with spaces records that in `docs/coding-standard/css.md` and overrides `[*.css]` accordingly — the Skill follows the project, not the handbook.
- `.gitattributes`: `* text=auto eol=lf` plus `acf-json/*.json text eol=lf` (per the `acf-local-json` Rule). On Windows this beats any `autocrlf` setting.
- `editorconfig-checker` (single binary) verifies both; mixed indentation and mixed line endings inside one file are its main catches.

## ACF Local JSON

`acf-lint` (`parisek/acf-json-schema`) validates the JSON shape and types (`--strict`); duplicate keys, orphaned fields, and clone/prefix rules are agent checks (criteria K-A01…K-A07). Findings are report-only in this Skill — every change to `acf-json/` is a structural change that deploys and triggers the human sync click, so it runs through the WST workflows under the `acf-local-json` Rule.

## Pre-Commit Hook (Optional, `cleanup_hook`)

The commit gate already puts a human at every commit; a staged-files-only hook is therefore the natural recurring check that never runs unattended. Two constraints:

1. **Chain with the existing hook.** `setup-acf-local-json` Step 8 installs `.githooks/pre-commit` (the ACF pull hook) with `core.hooksPath .githooks`. A lint hook is a second script called from that file — never a replacement, never a second `core.hooksPath`.
2. **Report by default, fast, escapable.** Staged files only (`--diff-filter=ACMR`), errors block, warnings pass; escape hatch `git commit -n` and an environment variable.

Appended to `.githooks/pre-commit` (POSIX `sh`, LF line endings — Git for Windows ships `sh`):

```sh
# --- code-cleanup: convention check on staged child-theme files (report mode) ---
[ "$CLEANUP_HOOK_SKIP" = "1" ] && exit 0
PHP_FILES=$(git diff --cached --name-only --diff-filter=ACMR -- 'themes/<child-theme>/*.php' 'themes/<child-theme>/**/*.php')
CSS_FILES=$(git diff --cached --name-only --diff-filter=ACMR -- 'themes/<child-theme>/**/*.css' 'themes/<child-theme>/**/*.scss')
if [ -n "$PHP_FILES" ] && [ -x vendor/bin/phpcs ]; then
	vendor/bin/phpcs -n $PHP_FILES || exit 1
fi
if [ -n "$CSS_FILES" ] && [ -x node_modules/.bin/stylelint ]; then
	node_modules/.bin/stylelint $CSS_FILES || exit 1
fi
```

`cleanup_hook: pre-commit (fix)` additionally runs `phpcbf` / `stylelint --fix` on the staged files and re-stages them; only for teams that accept a hook rewriting their commit. Alternatives with the same semantics: `lefthook` (native Windows binary, `stage_fixed: true`, `LEFTHOOK=0` to skip) or `husky` + `lint-staged` (needs Node and a POSIX shell). Note that PHPCS 3.x `phpcbf` exits 1 when it fixed something — wrap accordingly if you use fix mode.

## Cursor-Side Options

- **`afterFileEdit` hook** in `.cursor/hooks.json` running `phpcbf` / `stylelint --fix` on the edited file — a continuous "tidy after" at minute scale, no commits. On Windows call `pwsh -NoProfile -File …` explicitly (PowerShell 7; PowerShell 5.1 has stdin encoding issues), keep it fail-open. Optional; set it up only when the project already runs the fixers.
- **`stop` hook** with a `followup_message` that triggers a convention check — works, but the follow-up capture is unreliable on Windows; treat as best-effort.
- **Bugbot** on PRs: Cursor `.mdc` rules do not apply to Bugbot — conventions worth enforcing in review need a `.cursor/BUGBOT.md` copy. Only relevant for teams that review through PRs.

## Recurrence: What Fits The SmartFlow Model

| Mechanism | Runs where | Pushes / opens PRs? | Fit |
|---|---|---|---|
| Post-run offer after each Section/CPT/ticket run | main chat | no | best — tightest loop, human present |
| Pre-commit hook on staged files | local, at the commit gate | no | very good — fires exactly when a human commits |
| Weekly `/code-cleanup` on a calendar reminder | main chat | no | very good — bounded by the budget, zero infrastructure |
| Cursor `afterFileEdit` formatter hook | local, per agent edit | no | good for mechanical passes, Windows caveats |
| Cursor Automations (cron / SCM events) | Cursor cloud | **yes by default** | only report-only (findings to Slack / a comment); never for applying changes — conflicts with the never-push rule |
| Cursor `/loop` | in-session | no | poor — session-bound, shells die on idle; not a scheduler |
| Claude Code Routines / Copilot automations / Devin schedules / GitHub Agentic Workflows | vendor cloud or CI | yes | not applicable — other products, push output, some need CI minutes |

Vendors converge on the same cadence for maintenance agents: daily for "my own last-24h changes", weekly for repo-wide simplification, one bounded task per run. The Skill mirrors that as `post-run` and `recurring` with the finding budget.

## Scope And Ledger Mechanics

```powershell
# files since the last cleanup tag
git diff --name-only --diff-filter=ACMR cleanup/<last>..HEAD -- "themes/<child-theme>/"
# fallback without a tag
git log --since=30.days --name-only --pretty=format: -- "themes/<child-theme>/" | Where-Object { $_ } | Sort-Object -Unique
# hotspots (last 90 days)
git log --since=90.days --name-only --pretty=format: -- "themes/<child-theme>/" | Where-Object { $_ } | Group-Object | Sort-Object Count -Descending | Select-Object -First 3
# scope check after a pass
git diff --stat
# tag the run (local; the user pushes tags with the branch if wanted)
git tag cleanup/<YYYY-MM-DD>
```

The ledger `docs/cleanup-log.md` (template in `../templates/cleanup-log.md`) is the memory between runs: deferred findings, recurring counts, rejected findings with reasons (so they are not re-proposed), and the last tag. It lives in the docs layer and never deploys.
