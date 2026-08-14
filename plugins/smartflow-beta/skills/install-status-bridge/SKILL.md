---
name: install-status-bridge
description: Install or update the versioned SmartFlow status bridge in the child theme's js-snippets.php from the bundled PHP template, wire up the deployed-commit contract, commit with a hand-over stop, and verify the bridge over wso/v1/status after the user has pushed and the deploy ran.
---

# Install Status Bridge

Use this Skill when a project needs the SmartFlow status bridge for the first time, or when the installed bridge version is older than the bundled template version (the `status-bridge` Rule routes here on every version mismatch).

The bridge is a managed PHP block inside the child theme's `js-snippets.php`. It registers the REST namespace `wso/v1` with three routes:

| Route | Method | Purpose |
|---|---|---|
| `/wp-json/wso/v1/status` | GET | `bridge_version`, `deployed_commit`, registered ACF field groups, WP Grid Builder grids, cache state |
| `/wp-json/wso/v1/flush-cache` | POST | Object cache plus detected page cache plugins, best effort |
| `/wp-json/wso/v1/flush-permalinks` | POST | Soft rewrite-rules flush |

Every route requires an authenticated user with `manage_options`.

The canonical template is bundled with this Skill: `reference/js-snippets-status-bridge.php`. Its `WSO_BRIDGE_VERSION` constant is the expected bridge version that the `status-bridge` Rule compares against.

## Inputs

Read these from `PROJECT-CONTEXT.md` or ask the user:

- Child theme path, for example `wp-content/themes/<child-theme>/`.
- Location of `js-snippets.php` inside the child theme.
- Target environment base URL: `<site-url>`.
- How the project deploy path (for example the git installer) can write the deployed commit hash to a file.
- Names of the environment variables that carry the bridge credentials (see Authentication below).

## Step 1: Locate js-snippets.php

Find `js-snippets.php` inside the child theme and confirm it is a project-owned include that the theme actually loads.

- Do not create the file blindly. If it does not exist or nothing loads it, stop and ask how the project wires theme includes. `functions.php` is off-limits, and `theme-functions.php` needs explicit user confirmation per the file-edit boundary.
- Never register the bridge in the WST plugin folder or any other third-party code.

## Step 2: Detect Install State

Search `js-snippets.php` for the marker `WSO STATUS BRIDGE BEGIN`.

- No marker: this is a first install.
- Marker present: read the version from the marker line (`WSO STATUS BRIDGE BEGIN v<version>`) and compare it with `WSO_BRIDGE_VERSION` in the bundled template. Equal versions mean nothing to do; report and stop. A lower installed version means an update.

## Step 3: Install Or Update The Managed Block

Copy the managed block from `reference/js-snippets-status-bridge.php` — everything from `WSO STATUS BRIDGE BEGIN` to `WSO STATUS BRIDGE END`, markers included.

- First install: append the block at the end of `js-snippets.php`.
- Update: replace the existing block between the old markers with the new block. Never merge by hand and never edit individual lines inside the block; the template is the only source.
- Do not change anything else in `js-snippets.php`.

## Step 4: Wire The Deployed-Commit Contract

The status route reports `deployed_commit` from the file `.wso-deployed-commit` in the child theme root (one line, the full or abbreviated hash of the deployed commit).

- The project deploy path must write this file on every deploy. Record the concrete mechanism in `PROJECT-CONTEXT.md` (for example a post-deploy step of the git installer).
- The file is written on the server by the deploy path. Do not commit a `.wso-deployed-commit` file from the local repo; a locally committed hash would be stale by definition (a commit cannot contain its own hash).
- If the deploy path cannot write the file yet, record that as an open item in `PROJECT-CONTEXT.md`: `deployed_commit` stays `null` and deploy verification over the bridge cannot pass until it is wired.

## Step 5: Commit And Hand Over

Commit the `js-snippets.php` change (commit trailer `Made with: SmartFlow`), then make a hard stop and hand over to the user.

The agent never pushes. The user pushes, and the project deploy path delivers the child theme to the server.

## Step 6: Verify After The Deploy

Only after the user confirms the push and the deploy ran:

```sh
curl -sS -u "$WSO_BRIDGE_USER:$WSO_BRIDGE_APP_PASSWORD" "<site-url>/wp-json/wso/v1/status"
```

Check in the response:

- `bridge_version` equals `WSO_BRIDGE_VERSION` from the bundled template.
- `deployed_commit` equals the local `git rev-parse HEAD`.

Follow the retry and abort budget from the `status-bridge` Rule. On a 404, the deploy has not delivered the change or the REST API is blocked; on 401/403, the credentials or the `manage_options` capability are wrong.

## Authentication

The bridge authenticates with a WordPress application password for an administrator account.

- The user creates the application password in the WordPress admin (profile page) and provides it through environment variables, never in chat, tracked files, or reusable plugin content.
- Record only the environment variable names in `PROJECT-CONTEXT.md`, for example `WSO_BRIDGE_USER` and `WSO_BRIDGE_APP_PASSWORD`. Secrets enter commands only through those variables.

## Step 7: Record Bridge Facts

Update `PROJECT-CONTEXT.md` with:

- Bridge base URL: `<site-url>/wp-json/wso/v1/`.
- Installed bridge version.
- Credential environment variable names.
- The deployed-commit write mechanism (or the open item if it is not wired yet).

## Checklist

- [ ] `js-snippets.php` located and confirmed as a loaded project-owned include.
- [ ] Install state detected via the `WSO STATUS BRIDGE BEGIN` marker.
- [ ] Managed block installed or replaced from the bundled template, nothing else changed.
- [ ] Deployed-commit write mechanism recorded in `PROJECT-CONTEXT.md` (or recorded as open).
- [ ] Change committed with the SmartFlow trailer; hard stop; user pushes.
- [ ] After the deploy: `bridge_version` and `deployed_commit` verified over `wso/v1/status`.
- [ ] Bridge facts recorded in `PROJECT-CONTEXT.md`.
