---
name: migrate-ssh-to-local
description: One-time migration of an existing WESEO WordPress/WST project from the legacy Remote-SSH plus WP Pusher model to the local SmartFlow model. Use when a project still works over a server workspace with WP-CLI and WP Pusher and must move to the wp-content-level local repository with weseo-git-installer deploys. Covers the export stage in the SSH workspace (inventory, ACF admin JSON export as the Local JSON seed source, migration bundle) and the import stage in the local workspace (setup-local-project, value translation, WP Pusher teardown, git-installer build-up, ACF Local JSON setup via setup-acf-local-json, bridge-verified acceptance). Self-contained; requires no legacy plugin.
---

# Migrate SSH To Local

Run this Skill once per project to move it from the legacy model (Cursor Remote-SSH into the WordPress root, mutations over WP-CLI, WP Pusher pull-deploys, two workspaces joined by Git handoffs) to the SmartFlow model (one local workspace on a wp-content-level repository, push-based deploys through `weseo-git-installer`, server access only through the status bridge, REST, and read-only FTP).

This Skill is self-contained. It assumes none of the retired legacy packages (`wordpress-server-ops`, `wst-builder`, `frontend-design-qa`, or their beta twins) is installed; every command it needs is written out here. It relies only on content bundled with SmartFlow: the `setup-local-project`, `install-status-bridge`, and `setup-acf-local-json` Skills and the `status-bridge`, `acf-local-json`, `deploy-and-branches`, and `secrets` Rules.

The migration has two stages in fixed order:

1. **Export stage**, in the old Remote-SSH workspace, while WP-CLI and direct server file access still exist.
2. **Import stage**, in the new local workspace, after which the SSH workspace is retired.

Do not interleave the stages. Track progress in a `MIGRATION.md` note (repo root of whichever workspace is active) so an interrupted migration can resume at the first unfinished step instead of guessing.

## Stage 1: Export (Remote-SSH Workspace)

Open the legacy Remote-SSH workspace at the WordPress root one last time. WP-CLI is used here because the old model is still active; after this stage it is never needed again.

### 1.1 Inventory

Collect the non-secret facts the local model must inherit. Read the old `PROJECT-CONTEXT.md`, then verify against the installation:

```sh
php wp-cli.phar theme list --status=active --field=name
php wp-cli.phar plugin list --status=active
php wp-cli.phar option get home
```

Record in `MIGRATION.md`:

- Child theme name and path; WST source path inside it.
- WP Pusher configuration: repository, branch, repository subdirectory setting. These facts configure the git installer later and are needed for the teardown.
- Site URLs, WPGB grid/card/facet IDs, key page IDs, known quirks, and `LEARNINGS.md` content.
- Locations of Section and CPT handoffs still in flight.
- The list of admin-created ACF field groups (the Page-Builder Flexible Content group, the shared `[TMPL]` clone-source groups, and any other project-owned groups):

```sh
php wp-cli.phar post list --post_type=acf-field-group --fields=ID,post_title,post_name,post_status
```

### 1.2 ACF Field Definitions: Export Only, No Structural Change

The ACF field definitions stay in the database and the admin stays editable — the SmartFlow model versions them as **Local JSON with sync** (`acf-local-json` Rule), set up in the import stage through the bundled `setup-acf-local-json` Skill. Nothing is deleted, converted to PHP, or rewired in this stage.

Prepare only the seed source now, while the admin is at hand:

1. The user exports all project-owned groups in the WordPress admin over ACF → Tools → "Export as JSON" (one combined file).
2. Store the export file at a temp path outside the webroot; it travels with the migration bundle.
3. Cross-check the export against the group list from 1.1 (every project-owned group present) and record the result in `MIGRATION.md`.

Never run any structural write against the definitions during the migration: no `acf_update_field()` on definitions, no `$wpdb` writes to `acf-field`/`acf-field-group` posts, no reorder or write-back loop over `acf_get_fields()` — reading is safe, writing back destroyed shared clone sources in the legacy incident the `acf-local-json` Rule inherits its ban from.

<!-- acf-safety-reviewed: export-only stage; forbids the read-then-write-back idiom explicitly; definition changes go through acf-json per the acf-local-json rule -->

### 1.3 Assemble The Migration Bundle And Hand Over

The child theme travels as the theme state. Everything else goes into a bundle at a temp path outside the webroot, for example `$HOME/.weseo-tmp/smartflow-migration-<YYYYMMDD>/`:

- Old `PROJECT-CONTEXT.md`, `LEARNINGS.md`, `MIGRATION.md`.
- Section and CPT handoffs still in flight.
- The ACF admin JSON export from 1.2 and the WP Pusher configuration facts from 1.1.

If the old repository tracks the theme and docs, commit the export-stage changes there with the trailer `Made with: SmartFlow`, then hard stop: the user pushes (the agent never pushes) so the final theme state is safely in the old remote. Otherwise the user downloads the bundle and the child theme over SFTP. Confirm the user has the bundle and the theme state locally before leaving the SSH workspace; from here on it is a read-only reference that is retired at the end of the import stage.

## Stage 2: Import (Local Workspace)

### 2.1 Build The Local Foundation

Run the bundled `setup-local-project` Skill first if the local workspace does not exist yet: fork clone at wp-content level, folder named exactly after the server hostname, `.env` with the application password, REST test, read-only FTP user, Playwright MCP, Atlassian MCP, Figma MCP. Defer its git-installer and bridge steps if they would deploy before the theme state is imported — the first deploy should deliver the migrated theme.

### 2.2 Import The Theme State And The Docs

- Copy the child theme from the old repository (or the bundle) into `wp-content/themes/<child-theme>/` in the new repository. Diff against the server state over the read-only FTP user to confirm nothing was lost in transit.
- Move handoffs and learnings into the project's documented `docs/` layer location in the new repository.
- Delete the migration bundle from the server temp path once its content is safely in the new repository; report it if it must stay temporarily.

### 2.3 Translate PROJECT-CONTEXT.md

Create the new `PROJECT-CONTEXT.md` from the old one plus the inventory:

- Carry over: project name, URLs, child theme path, WST source path, WPGB IDs, key page IDs, quirks, brand facts.
- Remove the SSH-era fields: Remote-SSH host and workspace facts, WP-CLI command shapes, server cache-flush commands, server temp paths, the two-workspace handoff storage contract, WP Pusher as deploy path.
- Add the SmartFlow fields: working branch and deploy branch, deploy path `weseo-git-installer` with the child theme target directory, bridge base URL and version (after 2.4), credential environment variable names, FTP read facts, `playwright_mcp` status, `atlassian_mcp` status, `figma_mcp` status, `git_installer_guide`, `docs/` layer location. The ACF model fields (`acf-local-json`, filename convention) follow in 2.5.

### 2.4 WP Pusher Teardown And git-installer Build-Up

Order matters: the two deploy mechanisms must never race on the same theme directory.

1. **Disconnect WP Pusher first.** The user unlinks the theme's push-to-deploy in the WP Pusher admin so pushes stop triggering it. Do not uninstall yet — keep a fallback until the new chain is verified.
2. **Configure `weseo-git-installer`** per `setup-local-project` Step 6: search Confluence for the current git-installer guide, then apply repository URL, deploy branch, target directory `./wp-content/themes/<child-theme>/`, Bitbucket registration, deployed-commit contract.
3. **Install the status bridge** through the bundled `install-status-bridge` Skill if not yet present in the imported theme.
4. **Commit and hand over.** Commit the imported theme state, docs, and `PROJECT-CONTEXT.md` with the trailer `Made with: SmartFlow`. Hard stop; the user pushes; the git installer performs the first deploy.
5. **Verify over the bridge** per the `status-bridge` Rule: `bridge_version` matches the bundled template and `deployed_commit` equals the local `git rev-parse HEAD`, with the bounded retry and abort budget. Never record the migration as done while the hashes differ.
6. **Uninstall WP Pusher.** Only after the bridge-verified deploy, the user deactivates and deletes the WP Pusher plugin in the WordPress admin.

### 2.5 Set Up ACF Local JSON

With the deploy chain and the bridge working, run the bundled `setup-acf-local-json` Skill: bridge inventory, `acf-json/` in the child theme, seed split from the admin JSON export saved in 1.2, the ACF Extended `acfe_autosync` opt-in fix with a one-time collective sync, empirical filename-convention determination, both-direction proofs, and the FTP hash acceptance. Record the ACF model and the filename convention in `PROJECT-CONTEXT.md`.

### 2.6 Acceptance And Retirement

- `GET <site-url>/wp-json/wso/v1/status` lists the project-owned ACF field groups with `local: "json"`; plugin-registered groups keep `local: "php"` and are untouched.
- In the admin: the Page-Builder editor renders all Flexible Content layouts; field values save and load; groups stay editable in the admin as before — that is the point of the Local JSON model.
- On the frontend: pages using the layouts and TMPL clones render correctly; run a Playwright spot check on one Section-bearing page per template family.
- Record in `PROJECT-CONTEXT.md`: `migration: done <YYYY-MM-DD>` and the retired SSH workspace.

## Failure Handling

- ACF setup problems (admin save writes nothing, missing sync hint, shadowing duplicates): follow the failure handling of the `setup-acf-local-json` Skill; the database groups are never at risk because the migration deletes nothing.
- First git-installer deploy does not arrive (bridge 404 or stale `deployed_commit`): follow the `status-bridge` retry and abort budget; check push, Bitbucket registration, branch, and target directory. Do not re-enable WP Pusher as a workaround without an explicit user decision.
- Migration interrupted: resume from `MIGRATION.md` at the first unfinished step.

## Checklist

- [ ] Stage 1 inventory recorded in `MIGRATION.md` (theme, WP Pusher config, WPGB IDs, handoffs, ACF group list).
- [ ] ACF admin JSON export taken, cross-checked against the group list, stored outside the webroot in the bundle.
- [ ] Migration bundle assembled outside the webroot; old-repo commit handed over or bundle downloaded.
- [ ] Local foundation built via `setup-local-project`; theme state and docs imported and FTP-diffed.
- [ ] `PROJECT-CONTEXT.md` translated: SSH-era fields removed, deploy branch, bridge version, and env var names recorded.
- [ ] WP Pusher unlinked, git installer configured, bridge installed, commit handed over, deploy bridge-verified, WP Pusher uninstalled.
- [ ] ACF Local JSON set up via `setup-acf-local-json`: seed, ACFE fix, collective sync, filename convention, FTP hash acceptance.
- [ ] Acceptance done: bridge lists project groups as `local: "json"`, admin and frontend verified, `migration: done` recorded.
