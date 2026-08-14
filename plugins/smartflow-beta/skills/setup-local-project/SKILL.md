---
name: setup-local-project
description: Guided wizard for the complete first setup of a local SmartFlow workspace for a WESEO WordPress/WST project. Use when starting a new project, re-orienting a partially set up local workspace, cloning the wp-content-level repository, naming the folder after the server hostname, filling .env with the application password, configuring the weseo-git-installer deploy to the child theme, creating the read-only FTP user with .ftpaccess, running the REST test, installing the status bridge, exposing post types, taxonomies, ACF field groups, and options pages over REST, configuring Playwright MCP, or creating PROJECT-CONTEXT.md with deploy branch and bridge version. Successor to the legacy Remote-SSH setup-orientation.
---

# Setup Local Project

Run this Skill as a guided wizard for the first setup of a WESEO WordPress/WST project in the SmartFlow model: one local Cursor workspace on the developer's machine, opened on a wp-content-level repository that contains essentially the child theme. There is no Remote-SSH workspace and no server shell; the server is reached only through the deploy path (push by the user, delivery by `weseo-git-installer`), the status bridge (`wso/v1`), the WordPress REST API, and a read-only FTP user.

The wizard must work from any starting state. Re-read `PROJECT-CONTEXT.md` on every invocation, find the first gate whose status is missing, `pending`, or unverified, and resume there. Setup is complete only when every gate in the final checklist has a recorded outcome in `PROJECT-CONTEXT.md`.

The target user is a frontend or design colleague. Communicate in German for all user-facing steps; keep commands, file names, and external UI labels in their original language. For each step, lead with **Was passiert** (what the wizard is doing), **Warum** (why it matters), and **Du musst** (the exact user action, or `Nichts tun`), and end with `Erledigt: <result>` or `Offen: <open point> - nächster Schritt: <action>`. Run read-only, reversible, and routine setup writes automatically; ask only for missing concrete inputs, choices between ambiguous options, secret entry in the correct location, or a short confirmation before credential and live-site-affecting actions.

## Inputs

Ask the user or the maintainer for:

- The Bitbucket repository of the project (a fork of `website-repo-structure-template` prepared for this project) and the deploy branch that `weseo-git-installer` will watch.
- The server hostname of the target WordPress environment (the exact host string, for example `<www-host>.<server>.example`).
- The target environment base URL `<site-url>` and the child theme name.
- Access to the WordPress admin (to create the application password and configure `weseo-git-installer`) and to the hosting panel (to create the FTP user).

Do not invent repository names, hostnames, URLs, branches, or theme names. If a value is unknown, stop and ask.

## Step 1: Clone The Repository At wp-content Level

Clone the project repository to the local machine. The repository root is the wp-content level of the project: it contains `wp-content/themes/<child-theme>/` plus repo-level layers such as `docs/` and `tmp/`, and only the child theme subdirectory is ever deployed.

```sh
git clone <repo-url> <server-hostname>
```

Verify after cloning:

- `wp-content/themes/<child-theme>/` exists.
- The repository has a deny-all `.gitignore` from the template; do not weaken it.
- Note the default branch and confirm the deploy branch with the user (they may differ; the agent works on the branch recorded in `PROJECT-CONTEXT.md` per the `deploy-and-branches` Rule and never pushes).

## Step 2: Name The Folder After The Server Hostname

The local folder name must be exactly the server hostname, character for character. Chrome DevTools Local Overrides use the hostname as the mapping folder name; only with the exact name do DevTools and Cursor operate on the same files.

If the clone in Step 1 already used the hostname as target folder, nothing to do. Otherwise rename the folder now, then open it in Cursor as the workspace root. All later steps run in this workspace.

## Step 3: Create The `.env` With The Application Password

Server write access runs over the WordPress REST API, authenticated with a WordPress application password for an administrator account.

1. Guide the user: WordPress admin -> `Benutzer` -> `Profil` -> `Application Passwords`, create one named for this workspace (for example `smartflow-<hostname>`).
2. Ask the user to write the values into the repo-root `.env` themselves. Default variable names:

```text
WSO_SITE_URL=<site-url>
WSO_BRIDGE_USER=<admin-user>
WSO_BRIDGE_APP_PASSWORD=<application-password>
```

Per the `secrets` Rule: the tracked repo-root `.env` pushed to the private remote is a deliberate, accepted decision — do not gitignore or delete it. The secret values themselves still never appear in chat, commit messages, `PROJECT-CONTEXT.md`, the `docs/` layer, logs, diagnostics, or screenshots. Record only the variable names and their purpose in `PROJECT-CONTEXT.md`. Secrets enter commands exclusively through environment variables.

## Step 4: Run The REST Test

With the `.env` values loaded as environment variables, verify that the REST API accepts the application password:

```sh
curl -sS -u "$WSO_BRIDGE_USER:$WSO_BRIDGE_APP_PASSWORD" "<site-url>/wp-json/wp/v2/users/me?context=edit"
```

Expected: a JSON user object whose `capabilities` include `manage_options`. On 401/403, the application password or user is wrong — ask the user to re-issue it; never guess credentials. On 404 or HTML, the REST API is blocked or the URL is wrong; record the blocker with a next action and do not continue to the bridge step until resolved.

Record `rest_access: verified` (or `pending: <reason>`) in `PROJECT-CONTEXT.md`.

For the full connection walkthrough (post types, sample content, options pages), run `reference/rest-connection-test.md` — either now for the basic checks, or after Step 8 once the content exposure is complete.

## Step 5: Configure weseo-git-installer

Deployment is push-based: the user pushes to Bitbucket, and the `weseo-git-installer` WordPress plugin delivers only the child theme subdirectory to the server. The configuration happens in the WordPress admin, so guide the user:

1. Install and activate the `weseo-git-installer` plugin on the target WordPress site (if not already active).
2. Configure it with:
   - Repository URL: the project repository from Step 1.
   - Branch: the deploy branch.
   - Target directory: `./wp-content/themes/<child-theme>/`.
   - Register the repository in Bitbucket so pushes trigger the deploy (push-to-deploy).
3. Confirm with the user that the installer also writes the deployed commit hash to `.wso-deployed-commit` in the child theme root on every deploy (the deployed-commit contract of the status bridge). If it cannot yet, record that as an open item — deploy verification over the bridge cannot pass until it is wired.

Record in `PROJECT-CONTEXT.md`: deploy path `weseo-git-installer`, the deploy branch, the target directory, the Bitbucket registration status, and the deployed-commit mechanism (or its open item).

Go-live note for `PROJECT-CONTEXT.md`: before go-live the repository registration in Bitbucket is removed so push-to-deploy is switched off. Record this as a pending go-live step, not as a setup task.

## Step 6: Create The Read-Only FTP User With `.ftpaccess`

Server read access (inspecting served files, parent theme, plugin templates) runs over a dedicated FTP user that is scoped to `wp-content` and hard-limited to read.

Guide the user through the hosting panel:

1. Create an additional FTP user whose root directory is the server's `wp-content` directory.
2. Place a `.ftpaccess` file in that FTP user's root directory that denies writes and allows reads for exactly this user (writing this file requires the main FTP/admin access, not the new user):

```text
<Limit WRITE>
  DenyUser <read-only-ftp-user>
</Limit>
<Limit READ>
  AllowUser <read-only-ftp-user>
</Limit>
```

3. Ask the user to add the credentials to the repo-root `.env`, default names `WSO_FTP_USER` and `WSO_FTP_PASSWORD`; the FTP host is a non-secret fact for `PROJECT-CONTEXT.md`.

Verify both directions before recording the gate:

- Read works: list a directory and download one known file over the new user.
- Write is denied: attempt to upload a small scratch file and confirm the server rejects it. If the upload succeeds, the limit is not effective — stop, fix the `.ftpaccess` placement or syntax with the user, delete the scratch file, and re-test.

Record `ftp_read_access: verified read-only` (or `pending: <reason>`) plus the FTP host and variable names in `PROJECT-CONTEXT.md`.

## Step 7: Install The Status Bridge

Run the bundled `install-status-bridge` Skill. It installs the versioned managed block in the child theme's `js-snippets.php`, wires the deployed-commit contract from Step 5, commits with the SmartFlow trailer, and makes the hard stop so the user pushes and the git installer deploys.

After the user confirms the push and the deploy ran, verify over `GET <site-url>/wp-json/wso/v1/status` per the `status-bridge` Rule: `bridge_version` equals `WSO_BRIDGE_VERSION` from the bundled template, and `deployed_commit` equals the local `git rev-parse HEAD`. This doubles as the first end-to-end test of the whole deploy chain: commit -> user push -> git installer -> bridge.

## Step 8: Expose WordPress Content Over REST

Server write access runs entirely over the REST API, so the project's content types and options pages must actually be reachable there. Absorbed from the legacy `setup-wordpress-cursor` skill.

1. **Post types and taxonomies:** guide the user through the CPT UI settings to enable the REST API (`show_in_rest`) for all relevant post types and taxonomies. Confirm each part before continuing.
2. **ACF field groups:** guide the user to expose the field groups belonging to those posts in the REST API ("Show in REST API" in the ACF field group settings).
3. **ACF options pages:** options pages are not reachable over the core REST API. Probe the project endpoint with the auth header from Step 4:

```text
GET <site-url>/wp-json/wso/v1/options/wso-website-settings
```

   - **Reachable:** confirm to the user and continue.
   - **404 or error:** install the endpoints from `reference/acf-options-rest-endpoints.php` into the child theme's `js-snippets.php` — read the existing file first and match its style, adapt the `$options_pages` map to the project's actual options pages, and respect the `file-edit-boundary` Rule (never `functions.php`). This is tracked source: commit with the SmartFlow trailer and make the hard stop so the user pushes and the deploy delivers it (per the `deploy-and-branches` Rule, bundle it with the Step 7 bridge commit when both run in one sitting). Re-probe after the bridge confirms the deployed commit. As a fallback (no `js-snippets.php`, or the project manages snippets in the admin), hand the code to the user for the Code Snippets plugin instead.

4. Verify with the options-pages part of `reference/rest-connection-test.md` and record `rest_exposure: verified` (or `pending: <reason>` with the open post types or options slugs) in `PROJECT-CONTEXT.md`.

## Step 9: Configure Playwright MCP

Playwright QA runs locally against the target URLs, so the local workspace needs Playwright MCP.

1. Verify the local Node.js runtime: `node --version` (18.17+ required, LTS recommended), `npm --version`, `npx --version`. If missing, ask the user to install Node.js LTS and restart Cursor before continuing.
2. Add the Playwright MCP server to the untracked `.cursor/mcp.json` in this workspace (keep existing entries):

```json
{
  "mcpServers": {
    "playwright": {
      "command": "npx",
      "args": ["-y", "<playwright-mcp-package>"]
    }
  }
}
```

3. Ask the user to restart Cursor, then confirm the `playwright` server is active under `Settings` -> `Tools & MCP` and browser tools are listed.
4. Run a short verification loop: navigate to `<site-url>`, take a snapshot and a screenshot, switch viewport once.

`.cursor/mcp.json` stays untracked; never add credentials, cookies, or session tokens to it or to tracked examples. Record `playwright_mcp: ready` (or `pending: <reason>` with next action) in `PROJECT-CONTEXT.md`.

## Step 10: Fill PROJECT-CONTEXT.md

`PROJECT-CONTEXT.md` at the repository root is the project's non-secret context contract; later SmartFlow work reads it first. Create it if missing, update stale values if present. At minimum record:

- Project name, live URL, and dev/staging URL.
- Server hostname (equals the local folder name) and the reason for the naming (DevTools Local Overrides).
- Child theme path and WST source path (`wp-content/themes/<child-theme>/smart-template-builder/`).
- Working branch and deploy branch; deploy path `weseo-git-installer` with target directory and Bitbucket registration status; the pending go-live deregistration step.
- Bridge base URL (`<site-url>/wp-json/wso/v1/`), installed bridge version, and the deployed-commit write mechanism (or its open item).
- Credential environment variable names (`WSO_BRIDGE_USER`, `WSO_BRIDGE_APP_PASSWORD`, `WSO_FTP_USER`, `WSO_FTP_PASSWORD`) with purposes — names only, never values.
- FTP host and the verified read-only status.
- REST exposure state: post types and taxonomies with `show_in_rest`, REST-exposed ACF field groups, and the reachable options-page slugs under `wso/v1/options/` (or the open item).
- `rest_access`, `rest_exposure`, `ftp_read_access`, and `playwright_mcp` gate statuses.
- Location of the project `docs/` layer and `tmp/` policy (gitignored scratch space).
- Setup completion status per step (`done`, `pending: <reason>`, `skipped: <reason>`).

Never store real tokens, application passwords, token-bearing URLs, or any secret values.

## Final Verification

Walk the gates once more and confirm each has a recorded outcome:

- [ ] Repository cloned at wp-content level; child theme path present; deny-all `.gitignore` intact.
- [ ] Local folder name equals the server hostname exactly; workspace opened on it.
- [ ] `.env` filled by the user; variable names recorded, no values leaked.
- [ ] REST test passed with `manage_options`.
- [ ] `weseo-git-installer` configured (repo, deploy branch, child theme target, Bitbucket registration); deployed-commit mechanism recorded.
- [ ] Read-only FTP user verified: read works, write denied by `.ftpaccess`.
- [ ] Status bridge installed and bridge-verified after the first deploy (`bridge_version` and `deployed_commit` match).
- [ ] REST exposure done: relevant post types, taxonomies, and ACF field groups reachable over REST; options-page endpoints probed and installed if needed.
- [ ] Playwright MCP ready, verification loop done.
- [ ] `PROJECT-CONTEXT.md` complete, including deploy branch and bridge version.

If a required gate is unresolved, ask the user whether to fix it now, consciously record it as `pending` with reason and next action, or stop. Do not declare setup complete while required gates are unresolved. End with a short German summary: what the project is ready for now, and which points remain open.

## Stop Conditions

Stop and ask before:

- Replacing an existing Git remote or renaming a folder that already contains uncommitted work.
- Any commit (the agent never pushes; pushes are always the user's step).
- Storing or displaying a credential, or anything that would put a secret value outside `.env`.
- Continuing past a failed REST test, a writable "read-only" FTP user, or a bridge version/commit mismatch.

## Scope Boundaries

This Skill does not migrate an existing project off the legacy Remote-SSH model — that is the bundled `migrate-ssh-to-local` Skill, which runs this Skill as its import-stage foundation. It also does not build Sections, CPTs, or CSS; those follow after setup through the SmartFlow workflow Skills.
