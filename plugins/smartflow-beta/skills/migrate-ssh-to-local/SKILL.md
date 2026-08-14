---
name: migrate-ssh-to-local
description: One-time migration of an existing WESEO WordPress/WST project from the legacy Remote-SSH plus WP Pusher model to the local SmartFlow model. Use when a project still works over a server workspace with WP-CLI and WP Pusher and must move to the wp-content-level local repository with weseo-git-installer deploys. Covers the export stage in the SSH workspace (inventory, ACF export to PHP field groups for the Page-Builder Flexible Content and shared TMPL groups, deletion of the database groups, migration bundle) and the import stage in the local workspace (setup-local-project, value translation, WP Pusher teardown, git-installer build-up, bridge-verified acceptance). Self-contained; requires no legacy plugin.
---

# Migrate SSH To Local

Run this Skill once per project to move it from the legacy model (Cursor Remote-SSH into the WordPress root, mutations over WP-CLI, WP Pusher pull-deploys, two workspaces joined by Git handoffs) to the SmartFlow model (one local workspace on a wp-content-level repository, push-based deploys through `weseo-git-installer`, server access only through the status bridge, REST, and read-only FTP).

This Skill is self-contained. It assumes none of the retired legacy packages (`wordpress-server-ops`, `wst-builder`, `frontend-design-qa`, or their beta twins) is installed; every command it needs is written out here. It relies only on content bundled with SmartFlow: the `setup-local-project` and `install-status-bridge` Skills and the `status-bridge`, `acf-php-field-groups`, `deploy-and-branches`, and `secrets` Rules.

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
- The list of admin-created ACF field groups: the Page-Builder Flexible Content group and the shared `[TMPL]` clone-source groups, plus any other project-owned groups:

```sh
php wp-cli.phar post list --post_type=acf-field-group --fields=ID,post_title,post_name,post_status
```

### 1.2 Export ACF Field Groups To PHP

This is the one-time migration that the `acf-php-field-groups` Rule presumes. ACF cannot add PHP-registered fields to an admin-created Flexible Content field, so the Flexible Content container, all of its layouts, and the shared TMPL groups move to PHP together, keeping every existing key and name exactly (stored content references both).

Generate the export with a temporary PHP script outside the webroot (never inside the served tree), run through WP-CLI. The script must read definitions strictly read-only — `acf_get_field_group()` and `acf_get_fields()` only, never a write-back — and emit one PHP file per group in ACF's export shape:

```sh
php wp-cli.phar eval-file <temp-path>/export-acf-groups.php
```

The script's core, per group key:

```php
$group = acf_get_field_group( $key );
$group['fields'] = acf_get_fields( $key );
$export = acf_prepare_field_group_for_export( $group );
// var_export $export into a file that calls acf_add_local_field_group( $export )
// inside an add_action( 'acf/init', ... ) callback.
```

Write the generated files to `wp-content/themes/<child-theme>/smart-template-builder/acf/field-groups/`, one file per group, named after the group. Then wire a loader:

- If the child theme already loads PHP from `smart-template-builder/acf/`, reuse that mechanism.
- Otherwise create `smart-template-builder/acf/field-groups-loader.php` (a `require` loop over `field-groups/*.php`) and include it from a project-owned include that the theme already loads. `functions.php` is forbidden; `theme-functions.php` only with explicit prior user confirmation, per the file-edit boundary.

Diff-verify the export before going further: every group key, field key, field name, and Flexible Content layout from the database export appears in the PHP files unchanged.

### 1.3 Verify PHP Registration, Then Delete The Database Groups

Deleting the admin-created definitions is the destructive core of the migration. Order is mandatory:

1. **Backup first.** Dump the definition posts (or the full database) to a temp path outside the webroot:

```sh
php wp-cli.phar db export <temp-path>/pre-acf-migration-dump.sql
```

2. **Verify the PHP registration is live** while the DB groups still exist: in the WordPress admin, confirm the groups are present and the Page-Builder editor still renders all Flexible Content layouts; on the frontend, spot-check pages that use the layouts and a clone of a TMPL group.
3. **Ask the user for explicit confirmation** to delete the admin-created originals. Name the exact groups. Without confirmation, stop here and record the open step.
4. **Trash, do not force-delete.** For each group, trash its field posts and then the group post so recovery stays possible:

```sh
php wp-cli.phar post list --post_type=acf-field --post_parent=<group-post-id> --field=ID
php wp-cli.phar post delete <field-ids...>
php wp-cli.phar post delete <group-post-id>
```

   Nested fields (children of children, as in Flexible Content layouts) must be collected recursively before deleting.
5. **Re-verify after deletion:** editor renders all layouts, field values load and save, frontend renders, and no group appears twice in the admin. Flush the cache once (`php wp-cli.phar cache flush`).
6. Only after the import stage's bridge verification confirms the groups with PHP origin (Step 2.5) may the trashed posts be emptied for good.

This deletion is the sanctioned one-time exception behind the `acf-php-field-groups` Rule; after it, structural ACF database writes are forbidden entirely. Never run any reorder or write-back loop over `acf_get_fields()` during the migration — reading is safe, writing back destroyed shared clone sources in the legacy incident this rule exists for.

### 1.4 Assemble The Migration Bundle And Hand Over

The child theme itself — including the new `acf/field-groups/` files — travels as the theme state. Everything else goes into a bundle at a temp path outside the webroot, for example `$HOME/.weseo-tmp/smartflow-migration-<YYYYMMDD>/`:

- Old `PROJECT-CONTEXT.md`, `LEARNINGS.md`, `MIGRATION.md`.
- Section and CPT handoffs still in flight.
- The ACF database export list and the WP Pusher configuration facts from 1.1.
- The pre-migration database dump location (stays on the server; note the path).

If the old repository tracks the theme and docs, commit the export-stage changes there with the trailer `Made with: SmartFlow`, then hard stop: the user pushes (the agent never pushes) so the final theme state is safely in the old remote. Otherwise the user downloads the bundle and the child theme over SFTP. Confirm the user has the bundle and the theme state locally before leaving the SSH workspace; from here on it is a read-only reference that is retired at the end of the import stage.

## Stage 2: Import (Local Workspace)

### 2.1 Build The Local Foundation

Run the bundled `setup-local-project` Skill first if the local workspace does not exist yet: fork clone at wp-content level, folder named exactly after the server hostname, `.env` with the application password, REST test, read-only FTP user, Playwright MCP, Atlassian MCP, Figma MCP. Defer its git-installer and bridge steps if they would deploy before the theme state is imported — the first deploy should deliver the migrated theme.

### 2.2 Import The Theme State And The Docs

- Copy the child theme from the old repository (or the bundle) into `wp-content/themes/<child-theme>/` in the new repository, including `smart-template-builder/acf/field-groups/` and the loader wiring. Diff against the server state over the read-only FTP user to confirm nothing was lost in transit.
- Move handoffs and learnings into the project's documented `docs/` layer location in the new repository.
- Delete the migration bundle from the server temp path once its content is safely in the new repository; report it if it must stay temporarily.

### 2.3 Translate PROJECT-CONTEXT.md

Create the new `PROJECT-CONTEXT.md` from the old one plus the inventory:

- Carry over: project name, URLs, child theme path, WST source path, WPGB IDs, key page IDs, quirks, brand facts.
- Remove the SSH-era fields: Remote-SSH host and workspace facts, WP-CLI command shapes, server cache-flush commands, server temp paths, the two-workspace handoff storage contract, WP Pusher as deploy path.
- Add the SmartFlow fields: working branch and deploy branch, deploy path `weseo-git-installer` with the child theme target directory, bridge base URL and version (after 2.4), credential environment variable names, FTP read facts, `playwright_mcp` status, `atlassian_mcp` status, `figma_mcp` status, `git_installer_guide`, `docs/` layer location.

### 2.4 WP Pusher Teardown And git-installer Build-Up

Order matters: the two deploy mechanisms must never race on the same theme directory.

1. **Disconnect WP Pusher first.** The user unlinks the theme's push-to-deploy in the WP Pusher admin so pushes stop triggering it. Do not uninstall yet — keep a fallback until the new chain is verified.
2. **Configure `weseo-git-installer`** per `setup-local-project` Step 6: search Confluence for the current git-installer guide, then apply repository URL, deploy branch, target directory `./wp-content/themes/<child-theme>/`, Bitbucket registration, deployed-commit contract.
3. **Install the status bridge** through the bundled `install-status-bridge` Skill if not yet present in the imported theme.
4. **Commit and hand over.** Commit the imported theme state, docs, and `PROJECT-CONTEXT.md` with the trailer `Made with: SmartFlow`. Hard stop; the user pushes; the git installer performs the first deploy.
5. **Verify over the bridge** per the `status-bridge` Rule: `bridge_version` matches the bundled template and `deployed_commit` equals the local `git rev-parse HEAD`, with the bounded retry and abort budget. Never record the migration as done while the hashes differ.
6. **Uninstall WP Pusher.** Only after the bridge-verified deploy, the user deactivates and deletes the WP Pusher plugin in the WordPress admin.

### 2.5 Acceptance And Retirement

- `GET <site-url>/wp-json/wso/v1/status` lists the migrated ACF field groups with `local: "php"`; none of them appears as a database group anymore.
- In the admin: the Page-Builder editor renders all Flexible Content layouts; field values save and load; the groups are read-only definitions as expected for PHP registration.
- On the frontend: pages using the layouts and TMPL clones render correctly; run a Playwright spot check on one Section-bearing page per template family.
- The trashed definition posts from 1.3 may now be emptied (user confirmation; via the admin trash or the old workspace one last time).
- Record in `PROJECT-CONTEXT.md`: `migration: done <YYYY-MM-DD>`, the retired SSH workspace, and the pre-migration dump location with a deletion date.
- This completes the acceptance criterion for the "ACF fully in PHP" open validation; report the result so the validation status can be updated where the team tracks it.

## Failure Handling

- PHP registration incomplete (missing layout, missing field, editor breaks): stop before any deletion, fix the export, re-verify. The DB groups stay untouched until verification passes.
- Deletion already happened and a defect appears: restore from the trash first; only if the trash is gone, restore the definition posts from the pre-migration dump. Never hand-rebuild definitions in the admin.
- First git-installer deploy does not arrive (bridge 404 or stale `deployed_commit`): follow the `status-bridge` retry and abort budget; check push, Bitbucket registration, branch, and target directory. Do not re-enable WP Pusher as a workaround without an explicit user decision.
- Migration interrupted: resume from `MIGRATION.md` at the first unfinished step; never re-run the deletion of 1.3 blindly.

## Checklist

- [ ] Stage 1 inventory recorded in `MIGRATION.md` (theme, WP Pusher config, WPGB IDs, handoffs, ACF group list).
- [ ] ACF export generated read-only to `smart-template-builder/acf/field-groups/`, loader wired, keys diff-verified.
- [ ] Database backup taken; PHP registration verified live; user confirmed; DB groups trashed; post-deletion verification passed.
- [ ] Migration bundle assembled outside the webroot; old-repo commit handed over or bundle downloaded.
- [ ] Local foundation built via `setup-local-project`; theme state and docs imported and FTP-diffed.
- [ ] `PROJECT-CONTEXT.md` translated: SSH-era fields removed, deploy branch, bridge version, and env var names recorded.
- [ ] WP Pusher unlinked, git installer configured, bridge installed, commit handed over, deploy bridge-verified, WP Pusher uninstalled.
- [ ] Acceptance done: bridge lists groups as PHP, admin and frontend verified, trash emptied, `migration: done` recorded, open validation reported.
