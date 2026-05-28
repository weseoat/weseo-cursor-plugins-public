---
name: project-rules-cleanup
description: Guided German cleanup wizard for legacy and project-local Cursor Rules in a WESEO WordPress/WST project. Use when a project still carries old SmartFlow rules from `weseo-smartflow-frontend-guide` or has accumulated locally copied `.cursor/rules` that overlap with the co-installed `wordpress-server-ops`, `wst-builder`, and `frontend-design-qa` plugins, or when too many rules use `alwaysApply: true`. Audits Rules first, classifies them, migrates clearly non-secret project values into `PROJECT-CONTEXT.md`, and deletes generic legacy rules only after explicit confirmation. `.cursor/skills` is audited read-only and never deleted by this Skill.
---

# Project Rules Cleanup

Run this Skill in a WESEO WordPress/WST project that already went through `setup-orientation` (or that should have, but inherited an older `.cursor/rules` setup) and now needs to bring its `.cursor/rules` in line with the co-installed plugin guidance.

The expected outcome is a project where:

- Generic WESEO guidance comes from the installed Cursor plugins (`wordpress-server-ops`, `wst-builder`, `frontend-design-qa`).
- Non-secret project facts live in `PROJECT-CONTEXT.md`, not inside Rule files.
- `.cursor/rules` contains only genuinely project-specific Rules, with at most one or two small `alwaysApply: true` Rules.
- Legacy or copied `.cursor/skills` are flagged but never deleted by this Skill.
- The cleanup itself is documented in `PROJECT-CONTEXT.md` with date, checked files, applied changes, and any remaining `pending` points.

The target user is a WESEO frontend or WordPress colleague who installs the three plugins on an older project. They may not know which old Rules are still safe and which are now redundant. Communicate with the user in German throughout the wizard. Keep file names, command names, frontmatter keys (`description`, `alwaysApply`, `globs`), and external UI labels in their original language.

Never write tokens, application passwords, SSH keys, token-bearing URLs, REST credentials, dumps, or media inventories into chat, `PROJECT-CONTEXT.md`, Git, commits, tracked files, diagnostics, or screenshots.

## Guided Wizard Contract

For every user-facing cleanup step, lead with a short plain-language frame before technical details:

- **Was passiert:** What the wizard is checking or proposing, in everyday German.
- **Warum:** Why this matters for the project or later handoffs.
- **Du musst:** The exact user action, or `Nichts tun` when the wizard can continue automatically.

End each step with a one-line progress note: `Erledigt: <confirmed result>` or `Offen: <open cleanup point> - nächster Schritt: <action>`.

The wizard is **audit-first**. It must:

1. Read `.cursor/rules`, `.cursor/skills`, and `PROJECT-CONTEXT.md` (if present).
2. Classify findings.
3. Produce a bundled change plan grouped by section.
4. Wait for explicit confirmation before changing anything on disk.
5. Apply only the changes the user did not opt out of.
6. Record a short cleanup status in `PROJECT-CONTEXT.md`.

Run safe, reversible reads automatically (`ls`, file reads, frontmatter parsing). Ask only for short confirmations before: writing or creating `PROJECT-CONTEXT.md`, editing any `.mdc` file, deleting any `.mdc` file, or recording the cleanup status. Do not ask broad "is this okay" questions for findings the user cannot reasonably evaluate without the bundled change plan.

When the wizard is invoked after an interruption (long pause, terminal action, chat reset), do not assume previous proposals are still accepted. Re-read the actual state on disk, regenerate the change plan, and ask for fresh confirmation.

## Jargon Watchlist

- `Rule`: tracked Markdown-with-frontmatter file under `.cursor/rules/<name>.mdc`.
- `alwaysApply`: frontmatter key that decides whether Cursor loads a Rule for every request without trigger.
- `globs`: frontmatter key that scopes a Rule to specific file patterns instead of loading globally.
- `PROJECT-CONTEXT.md`: the project's non-secret context file at the WordPress root.
- `Plugin guidance`: Rules and Skills shipped with `wordpress-server-ops`, `wst-builder`, and `frontend-design-qa`. Already loaded through the user's personal Cursor account.
- `Legacy SmartFlow rule`: a `.mdc` originally copied from `weseo-smartflow-frontend-guide/.cursor/rules/`.
- `Project-specific rule`: a Rule whose content describes only this project (paths, IDs, non-generic conventions) and is not covered by plugin guidance.
- `Project-value carrier`: an old Rule whose content is generic but contains filled-in project values (URLs, IDs, paths, ACF keys) that belong in `PROJECT-CONTEXT.md`.

## Required Inputs

Before producing the change plan, confirm:

- WordPress root path.
- Whether `PROJECT-CONTEXT.md` exists at the WordPress root.
- Cursor plugin guidance status from `PROJECT-CONTEXT.md` (active, fallback, or unknown).
- Section handoff storage and CPT handoff storage status from `PROJECT-CONTEXT.md`.

If `PROJECT-CONTEXT.md` is missing entirely, the wizard offers to create a minimal version after confirmation. It does not migrate Rule values into a non-existent context file.

## Step 1: Bestand erfassen

**Was passiert:** Der Wizard liest `.cursor/rules`, `.cursor/skills` und (falls vorhanden) `PROJECT-CONTEXT.md` und sammelt nicht-geheime Fakten über die aktuelle Lage. Es werden keine Dateien geändert.
**Warum:** Bevor wir alte Regeln entfernen oder Werte verschieben, muss klar sein, was im Projekt liegt und was die installierten Plugins ohnehin schon abdecken.
**Du musst:** Nichts tun. Bei mehrdeutigen Funden fragt der Wizard gezielt nach.

Background read-only checks:

```sh
ls .cursor 2>/dev/null
ls .cursor/rules 2>/dev/null
ls .cursor/skills 2>/dev/null
test -f PROJECT-CONTEXT.md && echo "project-context-present"
```

For each file in `.cursor/rules/*.mdc`:

- Parse the frontmatter (`description`, `alwaysApply`, `globs`).
- Capture the rule body length and any obvious project-value markers (URLs, hostnames, post IDs, field keys, button variants, container widths, clamp values, ACF references, repository names).
- Note any value that resembles a secret (token-bearing URLs, application passwords, REST credentials, SSH keys, dumps).

`.cursor/rules/.gitkeep` is the expected skeleton placeholder and is not a Rule. Treat it as part of the skeleton, not as a finding.

If the project has no `.cursor/rules` directory, exit early with the result `Erledigt: keine Rules zum Aufräumen gefunden`.

## Step 2: Funde klassifizieren

**Was passiert:** Der Wizard ordnet jede gefundene Rule einer Kategorie zu.
**Warum:** Die Kategorie bestimmt, was passieren darf: löschen, umstellen, behalten oder nur melden.
**Du musst:** Nichts tun. Der Wizard zeigt die Klassifizierung im Plan in Step 5.

Categories:

| Category | Definition | Default action |
|---|---|---|
| `legacy-smartflow-generic` | `.mdc` whose content matches a generic Rule shipped with `wordpress-server-ops`, `wst-builder`, or `frontend-design-qa` (for example `webroot-safety`, `file-edit-boundary`, `wordpress-content-editing`, `wp-cli-cache`, `css-guideline`, `css-theme-styles`, `figma-to-code`, `acf-wst-patterns`). | Propose deletion after Project Context migration. |
| `legacy-smartflow-project-values` | `.mdc` whose body contains filled-in project values that should live in `PROJECT-CONTEXT.md` (for example `project-overview.mdc`, the project-specific parts of `development-conventions.mdc`). | Migrate clear non-secret values to `PROJECT-CONTEXT.md`, mark ambiguous or sensitive values `pending`, then propose deletion of the now-empty Rule. |
| `project-specific` | Rule whose content is genuinely project-only and not duplicated by plugin guidance (for example a project-internal naming convention not covered elsewhere). | Keep. Audit only: frontmatter shape, `alwaysApply` use, secret risks, redundancy with plugin guidance. Suggest patches; never auto-delete. |
| `third-party-or-custom` | Rule that is neither legacy SmartFlow nor obviously project-specific (for example imported from another plugin/repo or hand-written by a colleague). | Audit only: frontmatter, `alwaysApply`, `globs`, overlap with plugin guidance, secret risks. Suggest patches; never auto-delete. |
| `suspicious-or-unsafe` | Rule containing tokens, application passwords, token-bearing URLs, dumps, or other secret-shaped values. | Stop. Do not propose changes that move the value elsewhere. Ask the user how to redact and store the secret outside tracked files. |

Known-name hints for the legacy categories (use as hints, not absolute proof; always confirm against content):

- `project-overview.mdc`, `development-conventions.mdc` -> usually `legacy-smartflow-project-values`.
- `learnings.mdc`, `commit-trailer.mdc` -> usually `legacy-smartflow-generic`.
- `webroot-safety.mdc`, `file-edit-boundary.mdc`, `wordpress-content-editing.mdc`, `acf-wst-patterns.mdc`, `css-guideline.mdc`, `css-theme-styles.mdc`, `figma-to-code.mdc` -> `legacy-smartflow-generic` because the plugins now own these.

A Rule that started as legacy SmartFlow but has been edited substantially with project-only content is `legacy-smartflow-project-values`, not `legacy-smartflow-generic`. When in doubt, classify as `legacy-smartflow-project-values` so values are migrated before any deletion.

## Step 3: `alwaysApply` Hygiene prüfen

**Was passiert:** Der Wizard zählt alle Rules mit `alwaysApply: true` und vergleicht mit der Empfehlung "maximal 1-2 kleine globale Rules".
**Warum:** Zu viele globale Rules verrauschen den Cursor-Kontext und überschneiden sich oft mit Plugin-Guidance.
**Du musst:** Nichts tun. Der Wizard schlägt im Plan vor, welche Rules auf `alwaysApply: false` mit `globs` oder Skill-Trigger umgestellt werden sollten.

Threshold:

- 0 to 2 globally applied Rules: keine Warnung.
- 3 oder mehr globally applied Rules: in den Änderungsplan unter `Rules ändern` aufnehmen, mit konkretem Vorschlag pro Rule (zum Beispiel `alwaysApply: false` plus `globs: ["wp-content/themes/<child-theme>/**/*.php"]`, oder Umzug der Inhalte in einen Skill-Trigger).

The threshold counts every rule classified as `project-specific` or `third-party-or-custom`. Legacy SmartFlow rules are not counted in this hygiene check because they are already on the deletion or migration path.

A Rule should keep `alwaysApply: true` only when its content is small, truly project-global, and not covered by plugin guidance. Workflow-, role-, technology-, file-type-, or task-specific guidance should move to `globs` or a Skill.

## Step 4: Werte aus Legacy-Rules nach `PROJECT-CONTEXT.md` migrieren

**Was passiert:** Für jede Rule in `legacy-smartflow-project-values` extrahiert der Wizard eindeutige nicht-geheime Projektwerte und bereitet einen Vorschlag für `PROJECT-CONTEXT.md` vor.
**Warum:** Plugin-Guidance bleibt nur sauber, wenn Projektwerte zentral in `PROJECT-CONTEXT.md` stehen, nicht in lokalen Rules. Erst nach der Migration darf eine generische Legacy-Rule gelöscht werden.
**Du musst:** Nichts tun. Du bestätigst die Migration im Änderungsplan in Step 5.

Migration policy:

- Eindeutige nicht-geheime Werte werden vorgeschlagen, in den passenden `PROJECT-CONTEXT.md`-Abschnitt einzutragen (z. B. WordPress root, Theme-Pfad, Repository-Name, WP-CLI-Befehl, Cache-Befehl, CPTs, Key Page IDs, WP Grid Builder Grids, FC Field Keys, Clone Group Keys, ACF IDs, Button-Varianten, Container-Widths, Clamp-Werte).
- Platzhalter wie `<live-url>`, `<repo-name>`, `<wp-root>`, leere Tabellenzeilen oder TODO-Texte werden nicht als Wert extrahiert. Sie tauchen unter `Offene Punkte` als `pending: <Beschreibung> - nächster Schritt: <Aktion>` auf.
- Werte, die wie ein Secret wirken (Token, Application Password, token-bearing URLs, REST-Credentials, SSH-Pfade), werden nie nach `PROJECT-CONTEXT.md` übernommen. Sie werden als `pending: secret nicht in PROJECT-CONTEXT.md übernehmen` mit dem Hinweis, sie nur im Passwortmanager / OS-Keychain / `.cursor/mcp.json` zu speichern, gemeldet.

Conflict policy when `PROJECT-CONTEXT.md` already has a value for the same field:

- Beide Werte zeigen (Quelle Rule vs. Quelle Project Context).
- Eintrag als `pending: Konflikt zwischen <Rule> und PROJECT-CONTEXT.md - nächster Schritt: Maintainer entscheidet, welcher Wert korrekt ist`.
- Der Wizard ändert weder den Rule-Wert noch den Context-Wert automatisch.

If `PROJECT-CONTEXT.md` does not exist yet:

- Vorschlag: minimale Datei nach Bestätigung anlegen, mit den vom Setup-Orientation-Skill bekannten Pflichtfeldern und einem Abschnitt `Project specifics` für die migrierten Werte.
- Ohne Bestätigung wird keine Datei erstellt; in dem Fall werden alle Migrationen als `pending` markiert.

## Step 5: Änderungsplan zeigen und bestätigen lassen

**Was passiert:** Der Wizard zeigt einen gebündelten Plan in fünf Abschnitten. Du kannst einzelne Punkte abwählen.
**Warum:** Cleanup darf nie blind passieren. Du sollst pro Punkt entscheiden können, ohne Datei für Datei einzeln gefragt zu werden.
**Du musst:** Den Plan lesen und bestätigen. Wenn du einzelne Punkte nicht möchtest, sagst du es; der Wizard markiert sie als `Offen` mit Grund.

Plan sections (use these German section titles in chat):

1. **PROJECT-CONTEXT.md aktualisieren** - Liste der Felder, die ergänzt oder erstellt werden, jeweils mit Quelle (Rule-Datei und Zeile, falls hilfreich).
2. **Rules ändern** - Liste der `.mdc`, die der Wizard editieren würde (Frontmatter umstellen, `alwaysApply` reduzieren, `globs` ergänzen, einzelne Werte entfernen, weil sie in den Context wandern).
3. **Rules löschen** - Liste der `.mdc`, die der Wizard nach Bestätigung löscht. Standard sind nur `legacy-smartflow-generic` Rules und `legacy-smartflow-project-values` Rules, deren Inhalt vollständig in den Context migriert wurde.
4. **Skills nur melden** - Liste der `.cursor/skills/<name>/`, die wahrscheinlich überholt sind (zum Beispiel lokale `wst-new-fc-section`, lokale `wst-new-post-type`, lokale `wp-media-import`), mit Hinweis auf das passende Plugin-Skill. Keine Löschvorschläge.
5. **Offene Punkte (`pending`)** - Konflikte, Platzhalter, mehrdeutige Werte, vermutete Secrets, fehlendes `PROJECT-CONTEXT.md`. Jeder Punkt mit konkretem nächstem Schritt.

Confirmation rules:

- Default ist `Plan vollständig anwenden`.
- Der User darf einzelne Punkte aus 1-3 abwählen. Abgewählte Punkte landen automatisch in Abschnitt 5 als `pending` mit Grund `vom User in Cleanup übersprungen` und einem nächsten Schritt.
- Sektion 4 ist immer read-only; sie wird nicht angewendet, sondern nur dokumentiert.
- Wenn Sektion 5 Funde aus `suspicious-or-unsafe` enthält, stoppt der Wizard und fragt, wie der vermutete Secret-Wert behandelt werden soll, bevor er den Rest anwendet.

If the user rejects the entire plan, record the audit result in `PROJECT-CONTEXT.md` (Step 7) anyway, but apply nothing.

## Step 6: Plan anwenden

**Was passiert:** Der Wizard wendet die bestätigten Punkte aus Sektion 1-3 an.
**Warum:** Erst nach der Bestätigung dürfen Dateien geschrieben oder gelöscht werden.
**Du musst:** Nichts tun, solange keine zusätzlichen Rückfragen kommen. Bei einer scheinbaren Secret-Spur fragt der Wizard, bevor er weitermacht.

Apply order:

1. `PROJECT-CONTEXT.md` updates first, so values are safely persisted before any Rule is removed.
   - If the file is created from scratch, also propose adding it to the project's `.gitignore` allowlist if `setup-orientation` Step 5 has not already done so. Setup remains the source of truth for `.gitignore` baselines; this Skill only flags missing allowlist entries.
2. Rule edits in `.cursor/rules/`. Use minimal edits: frontmatter updates, `globs` additions, removal of migrated project-value paragraphs.
3. Rule deletions in `.cursor/rules/`. Delete only files that the plan explicitly listed under `Rules löschen`.
4. After each subsection, perform a short verification:

```sh
ls .cursor/rules
test -f PROJECT-CONTEXT.md && echo "project-context-present"
```

If any apply step fails (write error, file unexpectedly missing, content drift), stop, report what was done so far, and ask before continuing.

Do not run `git add`, `git commit`, or `git push` from this Skill. Final commits and the trailer policy belong to the project's normal Git workflow, not to the cleanup wizard.

## Step 7: Cleanup-Status dokumentieren

**Was passiert:** Der Wizard schreibt einen kurzen Cleanup-Status in `PROJECT-CONTEXT.md`.
**Warum:** Spätere Agents und Kollegen sollen sehen, dass der Cleanup gelaufen ist, was geprüft wurde, was angewendet wurde und was offen blieb.
**Du musst:** Nichts tun. Es wird keine separate Report-Datei erstellt.

Recommended block in `PROJECT-CONTEXT.md` (German content, English keys):

```md
## Cursor Rules Cleanup Status

- date: <YYYY-MM-DD>
- skill: project-rules-cleanup (wordpress-server-ops)
- checked:
  - .cursor/rules: <count>
  - .cursor/skills: <count>
  - PROJECT-CONTEXT.md: <existing | created>
- applied:
  - context_updates: <count>
  - rules_edited: <count>
  - rules_deleted: <count>
- read_only_notices:
  - skills_flagged: <count>
- pending:
  - <kurze Beschreibung> - nächster Schritt: <Aktion>
```

If `PROJECT-CONTEXT.md` was not created (because the user did not confirm), record the cleanup status in chat and add a `pending: PROJECT-CONTEXT.md fehlt - nächster Schritt: setup-orientation Step 3 ausführen` so the next agent sees the open setup gap.

## Outputs

When the wizard finishes, leave behind:

- A `.cursor/rules/` directory that contains only `project-specific` and `third-party-or-custom` Rules, with the `.gitkeep` skeleton intact.
- An updated `PROJECT-CONTEXT.md` with migrated non-secret project values, conflict markers as `pending`, and a `Cursor Rules Cleanup Status` block.
- An untouched `.cursor/skills/` directory, plus a list of flagged legacy local skills with their plugin counterparts (read-only).
- A clear chat summary that distinguishes `Erledigt`, `Offen (pending)`, and `Übersprungen (user choice)`.

## Stop Conditions

Stop and ask before:

- Creating `PROJECT-CONTEXT.md` from scratch.
- Editing or deleting any `.mdc` file.
- Migrating a value that could be a secret.
- Resolving a conflict between Rule content and `PROJECT-CONTEXT.md`.
- Acting on a Rule classified as `suspicious-or-unsafe`.
- Touching `.cursor/skills/` content (this Skill never deletes it).
- Running any Git command (`git add`, `git commit`, `git push`).

## Scope Boundaries

This Skill does not:

- Migrate, install, or modify the three Cursor plugins (`wordpress-server-ops`, `wst-builder`, `frontend-design-qa`). Plugin installation lives with the user's personal Cursor account.
- Replace `setup-orientation`. If `PROJECT-CONTEXT.md` is missing or `setup-orientation` setup gates are unresolved, point back to `setup-orientation` instead of duplicating its work.
- Re-implement frontend Section, CPT, or QA workflows. Those belong to `wst-builder` and `frontend-design-qa`.
- Deduplicate or rewrite `.cursor/skills/` content. Read-only audit only; deletion or rewriting of local Skills is out of scope by design.
- Manage Git remotes, commits, branches, or hooks.
- Touch `.cursor/mcp.json` or any other untracked credential file.

This Skill must remain self-contained inside `plugins/wordpress-server-ops` and must not depend on `weseo-smartflow-frontend-guide`, `.agents`, `.scratch`, `.cursor/plugins/cache`, or any other development-only path. Detection hints reference legacy SmartFlow rule shapes by name and content patterns, not by reading the legacy repository directly.
