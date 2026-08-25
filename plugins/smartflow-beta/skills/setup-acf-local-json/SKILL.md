---
name: setup-acf-local-json
description: One-time setup of the ACF Local JSON workflow in a SmartFlow project - create acf-json/ in the child theme, seed it from a user-driven ACF admin export split into one file per group, apply the ACF Extended autosync opt-in fix, determine the installation's JSON filename convention empirically, prove both sync directions, and accept over a byte-level FTP comparison plus the bridge listing all target groups as local json. Use when a project still keeps its ACF field definitions only in the database, or when migrate-ssh-to-local reaches its ACF step.
---

# Setup ACF Local JSON

Run this Skill once per project to move ACF field definitions from database-only (admin GUI) into the versioned Local JSON workflow of the `acf-local-json` Rule: `acf-json/` in the child theme, admin stays editable, agents write JSON in the repository, a human confirms every structural change with a sync click.

Preconditions: a working local SmartFlow workspace (`setup-local-project`), an installed status bridge (`install-status-bridge`), and the read-only FTP user. Progress and decisions go into a `MIGRATION.md` note at the repository root (or the project's existing migration note) so an interrupted setup resumes at the first unfinished step.

## Step 1: Inventory Over The Bridge

Read `GET <site-url>/wp-json/wso/v1/status` (per the `status-bridge` Rule) and list `acf.groups`:

- Groups with `local: false` (database groups) are the participants.
- Groups with `local: "php"` (plugin-registered, for example WST plugin groups) do not take part and are never edited.
- Groups already `local: "json"` mean a partial setup exists; reconcile instead of re-seeding.

Record the participant list with group keys and titles in `MIGRATION.md`.

## Step 2: Create The Folder

Create `themes/<child-theme>/acf-json/` in the repository. It lives inside the deploy path on purpose: ACF detects the folder automatically once deployed — **no code change** (`functions.php` and `theme-functions.php` stay untouched).

## Step 3: Seed From The Admin Export

1. The user exports **all participant groups** in the WordPress admin over ACF → Tools → "Export as JSON" (one combined file) and hands the file over (temp path outside the deploy path).
2. Split the export into **one file per group** under `acf-json/`.
3. Format each file in the PHP `json_encode` style ACF writes itself: 4-space indentation, `\/` escaping, `\uXXXX` for non-ASCII. This keeps future diffs minimal when ACF rewrites a file after an admin save.
4. **No `modified` timestamps in the seed** — seeds without `modified` produce no false sync hints after the deploy.
5. Reconcile the export keys exactly against the Step 1 inventory: every participant present, no extra groups, no duplicate keys. Duplicates are dangerous — ACF loads the last file read, so a stale file can shadow a fresh one.

## Step 4: ACF Extended Opt-In Fix

With ACF Extended installed, JSON writing is **opt-in per group**: the group setting `acfe_autosync` must contain `"json"` ("Json Sync" checkbox). Groups created before `acf-json/` existed have it empty — saving them then silently writes nothing. (New groups get the checkbox automatically while the folder exists.)

1. Set `acfe_autosync: ["json"]` in **all** seeded group files.
2. Bump `modified` in the same edit (Unix timestamp greater than the database state) so the admin offers the sync.
3. Commit and hand over per the `deploy-and-branches` Rule (the agent never pushes). After the user pushes, verify `deployed_commit` over the bridge.
4. The user (or a colleague) performs a **one-time collective sync** in the admin: review the diff, sync all offered groups.

## Step 5: Determine The Filename Convention

The JSON filename convention can differ per installation (an `acf/json/save_file_name` transformation may write `group-<hex>.json` with a hyphen instead of `group_<hex>.json`). Determine it empirically:

1. Have the user save one participant group in the admin.
2. Pull the **complete** `acf-json/` directory listing from the server over read-only FTP — never only known filenames, otherwise newly named files stay invisible.
3. Read the written filename; confirm the pattern with a second group.
4. If the server convention differs from the seed names: rename the seed files to the server convention, delete the old names, and verify after the next deploy that the deploy tool removed the old files from the server (`weseo-git-installer` does; any other deploy tool must be verified once). Never leave two files carrying the same group key.
5. Record the convention in `PROJECT-CONTEXT.md`.

## Step 6: Prove Both Directions

Both directions must be proven before acceptance:

- **Admin → JSON:** a group saved in the admin rewrites the server file (Step 5 already shows this). Pull the file into the repository and confirm the content change.
- **JSON → Admin:** the collective sync from Step 4 proves this direction (agent-edited JSON appeared as "Sync available" and applied cleanly).

## Step 7: Acceptance

- **FTP full comparison:** every file under `acf-json/` is byte-identical between repository and server, and no foreign files exist on either side. Compare **content hashes, never timestamps** — directory mtimes do not change on overwrite, and FTP listing modes can mix UTC and local time.
- **Bridge check:** `GET /wp-json/wso/v1/status` lists every participant group as `local: "json"`. A group still reported `local: false` means its sync is incomplete.
- Record in `PROJECT-CONTEXT.md`: the ACF model (`acf-local-json`), the filename convention, and the team rule "sync only after diff review" (communicate it once to the colleagues who edit fields in the admin).
- From now on the binding operating rules of the `acf-local-json` Rule apply, most importantly **Pull-before-Deploy**: before every deploying commit, pull the complete `acf-json/` listing from the server into the repository.

## Failure Handling

- Admin save writes no server file: check `acfe_autosync` on that group (Step 4) before suspecting the folder or permissions.
- No sync hint after a deploy: check the `modified` bump (must exceed the database state).
- A fresh definition seems ignored: look for a duplicate file carrying the same group key (stale file shadows fresh one) and for old filenames the deploy tool failed to remove.
- Interrupted setup: resume from `MIGRATION.md` at the first unfinished step; never re-split the export blindly over an already-reconciled folder.

## Checklist

- [ ] Bridge inventory recorded: participants (`local: false`), excluded plugin groups (`local: "php"`).
- [ ] `acf-json/` created in the child theme; no code change made.
- [ ] Seed split one file per group, PHP `json_encode` style, no `modified`, keys reconciled against the inventory.
- [ ] `acfe_autosync: ["json"]` set in all group files with a `modified` bump; deploy bridge-verified; collective sync done.
- [ ] Filename convention determined empirically, seeds renamed if needed, convention recorded in `PROJECT-CONTEXT.md`.
- [ ] Both directions proven (admin save rewrites server JSON; JSON change synced in admin).
- [ ] Acceptance: FTP hash comparison byte-identical, bridge lists all participants `local: "json"`, operating rules recorded.
