---
name: project-rule-authoring
description: Guided German wizard for authoring a new project-local Cursor Rule in a WESEO WordPress/WST project. Use when a colleague wants to add a `.cursor/rules/*.mdc` file, create a Cursor Rule, define project-specific agent guidance, or asks about `AGENTS.md` in a project that uses the co-installed `wordpress-server-ops`, `wst-builder`, and `frontend-design-qa` plugins. Runs a short grill before any file is written, rejects content already covered by plugin guidance, routes project values to `PROJECT-CONTEXT.md` and workflow guidance to Skills, and validates the `.mdc` frontmatter and `alwaysApply` hygiene before saving. Never writes secrets into Rules.
---

# Project Rule Authoring

Run this Skill in a WESEO WordPress/WST project before creating any new `.cursor/rules/*.mdc` file. The Skill is the WESEO-specific authoring gate that wraps the generic Cursor `create-rule` flow with a short grill, a reuse check against the co-installed plugin guidance (`wordpress-server-ops`, `wst-builder`, `frontend-design-qa`), and a format validation step.

The expected outcome is a project where:

- New Rules exist only when project-specific agent behavior is genuinely needed and not already covered by plugin guidance.
- Project values stay in `PROJECT-CONTEXT.md`, not inside Rule files.
- Workflow- or task-specific behavior lives in Skills, not in `alwaysApply: true` Rules.
- Every new `.mdc` has a concise frontmatter (`description`, `alwaysApply`, optional `globs`) and a body under the size budget of the generic Cursor `create-rule` guidance.
- No tokens, application passwords, SSH keys, token-bearing URLs, REST credentials, or dumps are written into Rules.

The target user is a WESEO frontend or WordPress colleague who is about to create a Cursor Rule and may not yet know which guidance is already covered by the three installed plugins. Communicate with the user in German throughout the wizard. Keep file names, command names, frontmatter keys (`description`, `alwaysApply`, `globs`), Skill names, and external UI labels in their original language.

Never write tokens, application passwords, SSH keys, token-bearing URLs, REST credentials, dumps, or media inventories into chat, `PROJECT-CONTEXT.md`, Git, commits, tracked files, diagnostics, screenshots, or any `.cursor/rules/*.mdc` file.

## Guided Wizard Contract

For every user-facing authoring step, lead with a short plain-language frame before technical details:

- **Was passiert:** What the wizard is checking, asking, or proposing, in everyday German.
- **Warum:** Why this matters for the project, for plugin guidance reuse, or for later handoffs.
- **Du musst:** The exact user action, or `Nichts tun` when the wizard can continue automatically.

End each step with a one-line progress note: `Erledigt: <confirmed result>` or `Offen: <open point> - nächster Schritt: <action>`.

The wizard is **grill-first and audit-first**. It must:

1. Read `PROJECT-CONTEXT.md`, `.cursor/rules`, and the installed plugin guidance hints.
2. Run a short grill that asks one question at a time, with a recommended answer.
3. Decide whether a new Rule is the right carrier at all, or whether the content belongs in `PROJECT-CONTEXT.md`, in a Skill, or stays with existing plugin guidance.
4. Produce a single bundled change plan with the proposed `.mdc` file path, frontmatter, body outline, and any side updates (for example a `PROJECT-CONTEXT.md` entry).
5. Wait for explicit confirmation before changing anything on disk.
6. Validate the final `.mdc` against the format and hygiene checklist before writing.

Run safe, reversible reads automatically (`ls`, file reads, frontmatter parsing). Ask only for short confirmations before: writing a new `.mdc`, editing an existing `.mdc`, creating or editing `PROJECT-CONTEXT.md`, or proposing a new Skill instead of a Rule. Do not ask broad "is this okay" questions for findings the user cannot reasonably evaluate without the bundled change plan.

When the wizard is invoked after an interruption (long pause, terminal action, chat reset), do not assume previous grill answers are still valid. Re-read the actual state on disk and ask the user to reconfirm the still-pending decisions before producing a fresh change plan.

## Jargon Watchlist

- `Rule`: tracked Markdown-with-frontmatter file under `.cursor/rules/<name>.mdc`.
- `alwaysApply`: frontmatter key that decides whether Cursor loads a Rule for every request without trigger.
- `globs`: frontmatter key that scopes a Rule to specific file patterns instead of loading globally.
- `PROJECT-CONTEXT.md`: the project's non-secret context file at the WordPress root.
- `Plugin guidance`: Rules and Skills shipped with `wordpress-server-ops`, `wst-builder`, and `frontend-design-qa`. Already loaded through the user's personal Cursor account.
- `Skill`: agent skill under `.cursor/skills/<name>/SKILL.md` (project-local) or shipped with a plugin. Carries workflow- or task-specific behavior.
- `Project-specific rule`: a Rule whose content describes only this project and is not covered by plugin guidance, `PROJECT-CONTEXT.md`, or a Skill.

## Required Inputs

Before producing the change plan, confirm:

- WordPress root path.
- Whether `PROJECT-CONTEXT.md` exists at the WordPress root.
- Cursor plugin guidance status from `PROJECT-CONTEXT.md` (active, fallback, or unknown).
- `project_rules_cleanup` status from `PROJECT-CONTEXT.md` (`not needed`, `pending`, or `done <YYYY-MM-DD>`).

If `PROJECT-CONTEXT.md` is missing entirely, the wizard does not create one. It points the user back to `setup-orientation` Step 3 and records the new Rule request as `Offen: PROJECT-CONTEXT.md fehlt - nächster Schritt: setup-orientation Step 3 ausführen, dann project-rule-authoring erneut starten`.

If `project_rules_cleanup: pending` is recorded, the wizard recommends running `project-rules-cleanup` first so that the new Rule does not collide with legacy SmartFlow Rules that are about to be removed. The user can explicitly choose to author the new Rule first; in that case the wizard logs an `Offen: cleanup folgt nach Authoring` note.

## Step 1: Bestand und Plugin-Guidance erfassen

**Was passiert:** Der Wizard liest `.cursor/rules`, `PROJECT-CONTEXT.md` (falls vorhanden) und sammelt nicht-geheime Fakten über bereits vorhandene Rules und die installierte Plugin-Guidance. Es werden keine Dateien geändert.
**Warum:** Bevor wir eine neue Rule planen, muss klar sein, was im Projekt liegt und was die installierten Plugins ohnehin schon abdecken.
**Du musst:** Nichts tun. Bei mehrdeutigen Funden fragt der Wizard gezielt nach.

Background read-only checks:

```sh
ls .cursor 2>/dev/null
ls .cursor/rules 2>/dev/null
test -f PROJECT-CONTEXT.md && echo "project-context-present"
```

For each file in `.cursor/rules/*.mdc`:

- Parse the frontmatter (`description`, `alwaysApply`, `globs`).
- Note the rule name and short body topic.

Reference the known plugin guidance carriers as hints (no remote reads required):

- `wordpress-server-ops`: `server-phase-boundary`, `webroot-safety`, `file-edit-boundary`, `plugin-package-boundary`, `wp-cli-cache`, `wordpress-content-editing`. Skills: `setup-orientation`, `wp-media-import`, `project-rules-cleanup`.
- `wst-builder`: WST Section and CPT workflows, handoff templates, `grill-me`.
- `frontend-design-qa`: `frontend-section-qa`, `cpt-frontend-qa`, `setup-playwright-mcp`, `css-guideline`, `figma-to-code`.

`.cursor/rules/.gitkeep` is the expected skeleton placeholder and is not a Rule.

## Step 2: Grill durchführen

**Was passiert:** Der Wizard stellt die entscheidenden Fragen zur geplanten Rule, eine nach der anderen, jeweils mit empfohlener Antwort.
**Warum:** Eine gute Rule braucht klaren Zweck, klaren Scope und einen guten Grund, warum sie nicht in `PROJECT-CONTEXT.md` oder in einem Skill leben sollte.
**Du musst:** Jede Frage kurz beantworten oder die Empfehlung bestätigen.

Grill questions, asked one at a time. Skip a question when the answer is already obvious from `PROJECT-CONTEXT.md`, an existing handoff, or the user's initial request. Provide a recommended answer with every question.

1. **Zweck:** Welches konkrete Agent-Verhalten soll die Rule erzwingen oder lehren? Empfehlung: ein Satz, kein "Diverses".
2. **Auslöser:** Soll die Rule immer geladen werden, oder nur bei bestimmten Dateien? Empfehlung: nur bei bestimmten Dateien über `globs`.
3. **Scope:** Welche konkreten `globs` passen? Empfehlung: möglichst eng, zum Beispiel `wp-content/themes/<child-theme>/**/*.php` statt `**/*`.
4. **Carrier-Wahl:** Ist der Inhalt ein dauerhaftes Agent-Verhalten (Rule), eine Workflow-Anleitung (Skill), oder ein Projektwert (Kontext)? Empfehlung: Verhalten -> Rule, Workflow -> Skill, Wert -> `PROJECT-CONTEXT.md`.
5. **Redundanz:** Wird das gleiche Anliegen schon von einer Plugin-Rule oder einem Plugin-Skill abgedeckt? Empfehlung: konkrete Plugin-Quelle benennen statt duplizieren.
6. **Geheimnis-Risiko:** Enthält der geplante Inhalt Tokens, Application Passwords, SSH-Pfade, token-haltige URLs, REST-Credentials, Dumps oder Medien-Inventare? Empfehlung: nein. Falls doch, sofort stoppen und im Passwortmanager / OS-Keychain / `.cursor/mcp.json` ablegen.
7. **Größe:** Lässt sich der Inhalt in unter 50 Zeilen mit höchstens einem konkreten Beispiel ausdrücken? Empfehlung: ja. Sonst in mehrere fokussierte Rules splitten oder einen Skill anlegen.

If the user mentions multiple distinct concerns, propose splitting them into separate Rules instead of one large Rule.

## Step 3: Carrier-Entscheidung treffen

**Was passiert:** Der Wizard ordnet das Anliegen einer Carrier-Kategorie zu.
**Warum:** Nicht jedes Anliegen gehört in eine Rule. Die richtige Kategorie spart Kontext, vermeidet Duplikate und schützt vor `alwaysApply`-Wildwuchs.
**Du musst:** Nichts tun. Der Wizard zeigt die Entscheidung im Plan in Step 5.

Carrier categories:

| Category | Definition | Default action |
|---|---|---|
| `project-rule` | Dauerhaftes, projekt-spezifisches Agent-Verhalten, das nicht in Plugin-Guidance, `PROJECT-CONTEXT.md` oder einem Skill besser aufgehoben ist. | Neue `.mdc` in `.cursor/rules/` vorschlagen, eng mit `globs`, ohne `alwaysApply: true` wenn möglich. |
| `project-context-value` | Projekt-Fakt oder -Wert (URL, Pfad, ID, ACF-Key, Container-Width, Clamp-Wert). | In `PROJECT-CONTEXT.md` ergänzen, keine neue Rule. |
| `project-skill` | Workflow- oder Task-Anleitung mit klarem Trigger (zum Beispiel "Wenn ein Section-Handoff angekommen ist, mache X"). | Als Skill unter `.cursor/skills/<name>/SKILL.md` vorschlagen, keine neue Rule. |
| `plugin-guidance-existing` | Anliegen ist schon durch `wordpress-server-ops`, `wst-builder` oder `frontend-design-qa` abgedeckt. | Quelle nennen, keine neue Rule. Optional in `PROJECT-CONTEXT.md` einen Verweis ergänzen. |
| `plugin-guidance-gap` | Anliegen wäre generisch für mehrere WESEO-Projekte sinnvoll und gehört eher in ein Plugin als in das einzelne Projekt. | Als `pending` melden: "gehört in Plugin X, dort als Rule oder Skill vorschlagen". Keine neue Projekt-Rule. |
| `suspicious-or-unsafe` | Inhalt enthält Tokens, Application Passwords, token-haltige URLs, Dumps oder andere geheim wirkende Werte. | Stoppen. Nicht in eine Rule schreiben. Den Wert nur außerhalb von Tracked Files speichern. |

When in doubt between `project-rule` and `project-skill`, prefer `project-skill`. Rules should describe a stable preference or constraint, not a multi-step workflow.

## Step 4: `alwaysApply`-Hygiene prüfen

**Was passiert:** Der Wizard zählt die bestehenden `alwaysApply: true` Rules und bewertet, ob die neue Rule global oder per `globs` geladen werden soll.
**Warum:** Zu viele globale Rules verrauschen den Cursor-Kontext und überschneiden sich oft mit Plugin-Guidance.
**Du musst:** Nichts tun. Der Wizard schlägt im Plan vor, wie die neue Rule getriggert wird.

Threshold and recommendation:

- 0 bis 1 bestehende `alwaysApply: true` Rules: neue Rule darf `alwaysApply: true` sein, wenn ihr Inhalt klein und wirklich projekt-global ist.
- 2 oder mehr bestehende `alwaysApply: true` Rules: neue Rule muss `alwaysApply: false` mit konkreten `globs` haben, oder als Skill vorgeschlagen werden.

A Rule should use `alwaysApply: true` only when its content is small, truly project-global, and not covered by plugin guidance. Workflow-, role-, technology-, file-type-, or task-specific guidance should move to `globs` or a Skill.

## Step 5: Änderungsplan zeigen und bestätigen lassen

**Was passiert:** Der Wizard zeigt einen gebündelten Plan in vier Abschnitten. Du kannst einzelne Punkte abwählen.
**Warum:** Authoring darf nie blind passieren. Du sollst pro Punkt entscheiden können, ohne Datei für Datei einzeln gefragt zu werden.
**Du musst:** Den Plan lesen und bestätigen. Wenn du einzelne Punkte nicht möchtest, sagst du es; der Wizard markiert sie als `Offen` mit Grund.

Plan sections (use these German section titles in chat):

1. **Carrier-Entscheidung** - Kategorie aus Step 3 mit kurzer Begründung, plus Hinweis auf das passende Plugin oder den Skill, falls die Antwort nicht `project-rule` ist.
2. **Rule erstellen oder ändern** - Bei `project-rule`: vorgeschlagener Dateipfad in `.cursor/rules/`, kompletter Frontmatter-Block (`description`, `alwaysApply`, optional `globs`) und ein kurzer Body-Outline.
3. **PROJECT-CONTEXT.md aktualisieren** - Bei `project-context-value` oder Plugin-Verweisen: Liste der Felder, die ergänzt werden, jeweils mit Quelle.
4. **Offene Punkte (`pending`)** - Vermutete Secrets, fehlendes `PROJECT-CONTEXT.md`, ausstehender `project-rules-cleanup`, Plugin-Gaps, Größenüberschreitungen, Konflikte mit bestehenden Rules.

Confirmation rules:

- Default ist `Plan vollständig anwenden`.
- Der User darf einzelne Punkte aus 2-3 abwählen. Abgewählte Punkte landen automatisch in Abschnitt 4 als `pending` mit Grund `vom User in Authoring übersprungen` und einem nächsten Schritt.
- Wenn Sektion 4 Funde aus `suspicious-or-unsafe` enthält, stoppt der Wizard und fragt, wie der vermutete Secret-Wert behandelt werden soll, bevor er den Rest anwendet.

If the user rejects the entire plan, record the decision in chat as `Offen: Rule-Authoring abgebrochen - nächster Schritt: <Grund>` and apply nothing.

## Step 6: `.mdc` validieren und schreiben

**Was passiert:** Der Wizard validiert die geplante `.mdc` gegen die Format- und Hygiene-Checkliste und schreibt sie erst danach.
**Warum:** Erst nach der Validierung darf eine Datei in `.cursor/rules/` entstehen.
**Du musst:** Nichts tun, solange keine zusätzlichen Rückfragen kommen. Bei einer scheinbaren Secret-Spur fragt der Wizard, bevor er weitermacht.

Validation checklist before writing:

- Pfad liegt unter `.cursor/rules/` und endet auf `.mdc`. Dateiname ist kebab-case ohne Leerzeichen.
- Frontmatter enthält `description` (eine Zeile, dritte Person, was und wann). `alwaysApply` ist gesetzt (`true` oder `false`). `globs` ist gesetzt, wenn `alwaysApply: false` und Scope dateibezogen ist.
- Body ist unter 50 Zeilen, ein Anliegen, optional ein konkretes Beispiel.
- Keine Tokens, Application Passwords, SSH-Pfade, token-haltige URLs, REST-Credentials, Dumps oder Medien-Inventare im Frontmatter oder Body.
- Keine projekt-spezifischen Werte, die in `PROJECT-CONTEXT.md` gehören (URLs, Hostnames, Repository-Namen, Post-IDs, ACF-Keys, Cache-Befehle, WP-CLI-Befehle).
- Keine generische WESEO-Guidance, die schon in `wordpress-server-ops`, `wst-builder` oder `frontend-design-qa` liegt.
- Bei `project-context-value`: zuerst `PROJECT-CONTEXT.md` aktualisieren, danach erst entscheiden, ob überhaupt noch eine Rule nötig ist.

Apply order:

1. Optional `PROJECT-CONTEXT.md` updates first (when the plan contains them).
2. Write the new `.mdc` in `.cursor/rules/`.
3. Short verification:

```sh
ls .cursor/rules
test -f PROJECT-CONTEXT.md && echo "project-context-present"
```

If any apply step fails (write error, file unexpectedly missing, content drift), stop, report what was done so far, and ask before continuing.

Do not run `git add`, `git commit`, or `git push` from this Skill. Final commits belong to the project's normal Git workflow, not to the authoring wizard.

## Step 7: Authoring-Status dokumentieren

**Was passiert:** Der Wizard ergänzt einen kurzen Authoring-Status in `PROJECT-CONTEXT.md`, wenn die Datei existiert.
**Warum:** Spätere Agents und Kollegen sollen sehen, welche projekt-spezifischen Rules bewusst angelegt wurden und warum.
**Du musst:** Nichts tun. Es wird keine separate Report-Datei erstellt.

Recommended block in `PROJECT-CONTEXT.md` (German content, English keys), appended or updated under a single section:

```md
## Cursor Rules Authoring Log

- <YYYY-MM-DD> - <rule-name>.mdc - carrier: project-rule - scope: globs `<pattern>` - Begründung: <ein Satz>
- <YYYY-MM-DD> - <thema> - carrier: project-context-value - Eintrag: <Feldname> in PROJECT-CONTEXT.md
- <YYYY-MM-DD> - <thema> - carrier: project-skill - vorgeschlagen: `.cursor/skills/<name>/SKILL.md`
- <YYYY-MM-DD> - <thema> - carrier: plugin-guidance-existing - Quelle: <plugin>/<rule oder skill>
```

If `PROJECT-CONTEXT.md` is missing, record the authoring decision in chat only and add a `pending: PROJECT-CONTEXT.md fehlt - nächster Schritt: setup-orientation Step 3 ausführen` so the next agent sees the open setup gap.

## Outputs

When the wizard finishes, leave behind:

- At most one new `.mdc` in `.cursor/rules/`, with concise frontmatter and a focused body.
- An optional `PROJECT-CONTEXT.md` update for migrated project values, plugin references, and the Authoring Log entry.
- A clear chat summary that distinguishes `Erledigt`, `Offen (pending)`, and `Übersprungen (user choice)`.

## Stop Conditions

Stop and ask before:

- Writing or editing any `.mdc` file.
- Creating or editing `PROJECT-CONTEXT.md`.
- Proposing a new Skill in `.cursor/skills/` instead of a Rule.
- Acting on content classified as `suspicious-or-unsafe`.
- Authoring a new Rule while `project_rules_cleanup: pending` is recorded.
- Running any Git command (`git add`, `git commit`, `git push`).

## Scope Boundaries

This Skill does not:

- Replace the generic Cursor built-in `create-rule` guidance. It wraps the WESEO-specific authoring decisions around it.
- Replace `setup-orientation`. If `PROJECT-CONTEXT.md` is missing or setup gates are unresolved, point back to `setup-orientation` instead of duplicating its work.
- Replace `project-rules-cleanup`. Audit, classification, migration, and deletion of legacy or accumulated Rules remain owned by `project-rules-cleanup`.
- Re-implement frontend Section, CPT, or QA workflows. Those belong to `wst-builder` and `frontend-design-qa`.
- Author plugin-level Rules or Skills. Suggestions for plugin gaps are reported as `pending` only.
- Manage Git remotes, commits, branches, or hooks.
- Touch `.cursor/mcp.json` or any other untracked credential file.

This Skill must remain self-contained inside `plugins/wordpress-server-ops` and must not depend on `weseo-smartflow-frontend-guide`, `.agents`, `.scratch`, `.cursor/plugins/cache`, or any other development-only path.
