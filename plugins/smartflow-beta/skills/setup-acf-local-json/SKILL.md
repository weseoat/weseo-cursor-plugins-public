---
name: setup-acf-local-json
description: One-time setup of the ACF Local JSON workflow in a SmartFlow project - create acf-json/ in the child theme, seed it from a user-driven ACF admin export split into one file per group, apply the ACF Extended autosync opt-in fix, determine the installation's canonical JSON write format empirically at the first admin save (filename, unicode escaping, autosync shape, newlines), prove both sync directions, and accept over a byte-level FTP comparison plus the bridge listing all target groups as local json. Use when a project still keeps its ACF field definitions only in the database, or when migrate-ssh-to-local reaches its ACF step.
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

If the project repository has no `.gitattributes` entry for the folder yet, recommend adding `acf-json/*.json text eol=lf` (repo-level file): ACF writes LF with a trailing newline, and the entry keeps Windows checkouts from breaking the byte comparison.

## Step 3: Seed From The Admin Export

1. The user exports **all participant groups** in the WordPress admin over ACF → Tools → "Export as JSON" (one combined file) and hands the file over (temp path outside the deploy path).
2. Split the export into **one file per group** under `acf-json/`.
3. Format each file in the style ACF 6.x writes itself (`JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE`): 4-space indentation, `\/` escaping for slashes, **raw UTF-8 for non-ASCII** (umlauts stay literal, no `\uXXXX`), LF line endings with a trailing newline. This keeps future diffs minimal when ACF rewrites a file after an admin save. Step 5 verifies the installation's actual write format empirically — if it deviates, the seeds are aligned once there.
4. **Serialize in one pass:** exactly one `json_decode` of the export followed by one `json_encode` per file. A two-stage pipeline (encode an already-encoded string) produces triple-escaped slashes (`6\\\/12` instead of `6\/12`) that corrupt stored values.
5. **No `modified` timestamps in the seed** — seeds without `modified` produce no false sync hints after the deploy.
6. Reconcile the export keys exactly against the Step 1 inventory: every participant present, no extra groups, no duplicate keys. Duplicates are dangerous — ACF loads the last file read, so a stale file can shadow a fresh one.

## Step 4: ACF Extended Opt-In Fix

With ACF Extended installed, JSON writing is **opt-in per group**: the group's autosync setting must contain `"json"` ("Json Sync" checkbox). Groups created before `acf-json/` existed have it empty — saving them then silently writes nothing. (New groups get the checkbox automatically while the folder exists.)

The JSON shape of that setting depends on the ACFE version. Current ACFE writes it **nested**: `"acfe": { "autosync": ["json"] }` — this is the default to seed. Older ACFE versions write a top-level `"acfe_autosync": ["json"]` instead. Do not guess the shape: seed the nested default, and Step 5 reads the installation's actual shape from the first admin save (same discipline as the filename). The semantics are the same either way — `"json"` must appear in the autosync value, otherwise saving silently writes nothing.

1. Set the autosync opt-in (nested `"acfe": { "autosync": ["json"] }` by default) in **all** seeded group files.
2. Bump `modified` in the same edit (Unix timestamp greater than the database state) so the admin offers the sync.
3. Commit and hand over per the `deploy-and-branches` Rule (the agent never pushes). After the user pushes, verify `deployed_commit` over the bridge.
4. The user (or a colleague) performs a **one-time collective sync** in the admin: review the diff, sync all offered groups.

## Step 5: Determine The Canonical Write Format

The canonical write format can differ per installation: the filename convention (an `acf/json/save_file_name` transformation may write `group-<hex>.json` with a hyphen instead of `group_<hex>.json`), the unicode escaping (ACF 6.x writes raw UTF-8; older stacks may write `\uXXXX`), and the autosync shape (nested `acfe.autosync` vs legacy top-level `acfe_autosync`). Determine all of it empirically from one real save:

1. Have the user save one participant group in the admin.
2. Pull the **complete** `acf-json/` directory listing from the server over read-only FTP — never only known filenames, otherwise newly named files stay invisible.
3. Read the written file: filename pattern, unicode escaping (raw UTF-8 vs `\uXXXX`), autosync shape, indentation, and newline behavior (LF, trailing newline). Confirm the filename pattern with a second group.
4. If the server format differs from the seeds: align the seeds once to the server format — rename files to the server convention, re-serialize content to the observed encoding (one-pass, per Step 3), delete the old names, and verify after the next deploy that the deploy tool removed the old files from the server (`weseo-git-installer` does; any other deploy tool must be verified once). Never leave two files carrying the same group key.
5. Record the canonical write format in `PROJECT-CONTEXT.md`: filename convention, encode style, autosync shape.

## Step 6: Prove Both Directions

Both directions must be proven before acceptance:

- **Admin → JSON:** a group saved in the admin rewrites the server file (Step 5 already shows this). Pull the file into the repository and confirm the content change.
- **JSON → Admin:** the collective sync from Step 4 proves this direction (agent-edited JSON appeared as "Sync available" and applied cleanly).

## Step 7: Acceptance

- **FTP full comparison:** every file under `acf-json/` is byte-identical between repository and server, and no foreign files exist on either side. Compare **content hashes, never timestamps** — directory mtimes do not change on overwrite, and FTP listing modes can mix UTC and local time. A byte mismatch on a group that was saved in the admin means the seed serialization deviates from the canonical write format: re-read the server file per Step 5 and re-serialize the seeds once before accepting.
- **Bridge check:** `GET /wp-json/wso/v1/status` lists every participant group as `local: "json"`. A group still reported `local: false` means its sync is incomplete.
- Record in `PROJECT-CONTEXT.md`: the ACF model (`acf-local-json`), the canonical write format (filename convention, encode style, autosync shape), and the team rule "sync only after diff review" (communicate it once to the colleagues who edit fields in the admin). If the `.gitattributes` entry from Step 2 is still missing, recommend it again here.
- From now on the binding operating rules of the `acf-local-json` Rule apply, most importantly **Pull-before-Deploy**: before every deploying commit, pull the complete `acf-json/` listing from the server into the repository.

## Failure Handling

- Admin save writes no server file: check the autosync opt-in on that group (Step 4, nested or legacy shape) before suspecting the folder or permissions.
- No sync hint after a deploy: check the `modified` bump (must exceed the database state).
- A fresh definition seems ignored: look for a duplicate file carrying the same group key (stale file shadows fresh one) and for old filenames the deploy tool failed to remove.
- Byte comparison fails after an admin save although the content is semantically identical: the seed serialization deviates from the canonical write format (unicode escaping, autosync shape, newlines) — re-read the server file per Step 5 and re-serialize the seeds once in a single pass.
- Values contain literal backslashes (for example `6\/12` decoding to `6\/12` with a backslash instead of `6/12`): symptom of a two-stage seed generation that encoded twice. Regenerate the affected files in one pass from the original export.
- Interrupted setup: resume from `MIGRATION.md` at the first unfinished step; never re-split the export blindly over an already-reconciled folder.

## Checklist

- [ ] Bridge inventory recorded: participants (`local: false`), excluded plugin groups (`local: "php"`).
- [ ] `acf-json/` created in the child theme; no code change made; `.gitattributes` entry (`acf-json/*.json text eol=lf`) recommended if missing.
- [ ] Seed split one file per group, ACF 6 encode style (raw UTF-8, `\/` escaped, 4-space indent, LF + trailing newline), serialized in one pass, no `modified`, keys reconciled against the inventory.
- [ ] Autosync opt-in set in all group files (nested `acfe.autosync` default, legacy top-level shape only if the install writes it) with a `modified` bump; deploy bridge-verified; collective sync done.
- [ ] Canonical write format determined empirically from the first admin save (filename, unicode escaping, autosync shape, newlines), seeds aligned once if needed, format recorded in `PROJECT-CONTEXT.md`.
- [ ] Both directions proven (admin save rewrites server JSON; JSON change synced in admin).
- [ ] Acceptance: FTP hash comparison byte-identical, bridge lists all participants `local: "json"`, operating rules recorded.
