---
name: setup-local-project
description: Guided wizard for the complete first setup of a local SmartFlow workspace for a WESEO WordPress/WST project. Use when starting a new project, re-orienting a partially set up local workspace, cloning the wp-content-level repository, naming the folder after the server hostname, filling .env with the application password, configuring the weseo-git-installer deploy to the child theme from the live Confluence guide, creating the read-only FTP user with .ftpaccess, running the REST test, installing the status bridge, exposing post types, taxonomies, ACF field groups, and options pages over REST, configuring Playwright MCP, verifying that Atlassian MCP (Rovo preflight) and Figma MCP are running, anchoring the project's Confluence page and mirroring its extract into PROJECT-CONTEXT.md, or creating PROJECT-CONTEXT.md with deploy branch and bridge version. Successor to the legacy Remote-SSH setup-orientation.
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

For the full connection walkthrough (post types, sample content, options pages), run `reference/rest-connection-test.md` — either now for the basic checks, or after Step 10 once the content exposure is complete.

## Step 5: Verify Atlassian MCP (Rovo Preflight)

The `weseo-git-installer` install walkthrough and its current status live on Confluence and are updated there, and the project's Confluence page is anchored in the next step. The wizard must confirm Atlassian MCP is actually running in this Cursor session before those steps — listing it under Settings is not enough.

The team standard is the **official Atlassian Rovo MCP**, installed from the Cursor marketplace ([cursor.com/marketplace/atlassian](https://cursor.com/marketplace/atlassian)) with an OAuth 2.1 browser flow: no API token, no `mcp.json` editing. Guide a colleague without any Atlassian MCP through exactly that route. A community `mcp-atlassian` server that is already configured and working stays a valid variant — do not tear it down; but never guide a new install toward the token-based community server, and flag an API token sitting in a server's command line as an anti-pattern to correct (secrets belong in env blocks, never in `args`).

Do not write Atlassian credentials, OAuth tokens, or cloud IDs into `.cursor/mcp.json`, `.env`, chat, or `PROJECT-CONTEXT.md`. Prefer a **user-level** server so every project workspace inherits it; a project-level server whose name contains `atlassian` is also acceptable. Record the observed server identifier, never secrets.

1. Probe the MCP catalog for any server whose name contains `atlassian`. Treat `needsAuth`, `error`, and `loading` as not ready. If `needsAuth`, ask the user to complete OAuth under `Settings` -> `Tools & MCP`, then re-probe. If the server is missing, guide the user through the marketplace install + OAuth above, then restart or reconnect.
2. When the server is usable, discover the tool schema and map the operations by name — the Rovo server (`searchConfluenceUsingCql`, `getConfluencePage`, ...) and the community server (`confluence_search`, `confluence_get_page`, ...) expose different tool names. Then run cheap live reads — not writes:
   - **Confluence (required for this setup):** a CQL/text search with a short query such as `git-installer` (space `Frontend` when the tool allows a space filter). Success is a search response, not a specific page ID. Do not copy page bodies into `PROJECT-CONTEXT.md` during this health check; the only sanctioned page-content mirror is the controlled extract of Step 6.
   - **Jira:** a one-result issue search, or a project listing.
3. Record in `PROJECT-CONTEXT.md`:
   - `atlassian_mcp: ready` when the Confluence search succeeded, plus the server identifier and whether Jira also responded.
   - `atlassian_mcp: pending: <reason>` with the next action when the server is missing, still in `needsAuth`/`error`, or Confluence search failed.

Confluence is the setup-blocking surface: do not continue to the anchor and git-installer steps while it is unresolved, unless the user consciously records `pending` and accepts that the installer cannot be guided from Confluence and the project page cannot be anchored. A Jira-only result is not enough. Jira failure with a working Confluence search may be recorded as a note; it does not block the following steps.

## Step 6: Anchor The Project's Confluence Page And Mirror The Extract

Confluence is the leading input source for project implementation facts (`confluence-source` Rule): the project lead (PL) maintains a central project page — typically the "Umsetzung" page — and the SmartFlow workflow reads it at controlled points. This step creates the anchor and the first mirror.

1. Ask the user once for the project's Confluence link (the PL's central project page). The page ID is in the URL (`.../pages/<page_id>/<title>`).
   - **No page exists or the user has none:** that is a valid state. Record `confluence_anchor: none` in `PROJECT-CONTEXT.md`; every later read point skips cleanly. Do not block setup.
2. Fetch the page by ID over the Atlassian MCP and extract per the `confluence-source` Rule: dev URL and final domain, reference sites, Figma links (global and per module/section), Jira keys and the Jira parent task, module/section lists, interfaces and content sources, constraints and out-of-scope notes. Honor the optional PL conventions when present (text status markers `OFFEN:` / `ENTSCHIEDEN (<Datum>):` / `ENTFÄLLT:`, the compact "Für Cursor" header block as the first extraction target). Do not impose a template on the PL; extract what the page offers and mark every gap explicitly as `<unresolved: ...>`.
3. Write the Confluence block into `PROJECT-CONTEXT.md`: page ID, full URL, mirror timestamp, and the extracted values. This controlled extract mirror is deliberate and sanctioned — it is not the "page bodies" copying that Step 5 forbids: mirror distilled facts, never the raw storage-format body.
4. **Secrets are never mirrored** (`confluence-source` and `secrets` Rules): a credential found in plaintext on the page becomes a placeholder plus pointer (`<on the PL page — belongs in .env>`), and the wizard reports the finding to the user once — the value belongs in the vault/`.env`, not on a page that agents read automatically.
5. Record the gate: `confluence_anchor: mirrored` (with the page ID), `none`, or `pending: <reason>`.

After setup, the mirror is refreshed only through the dedicated `sync-project-brief` Skill (diff shown, user confirms); the four work-package Skills re-read the anchored page fresh at their run start per the `confluence-source` Rule.

## Step 7: Configure weseo-git-installer From Confluence

Deployment is push-based: the user pushes to Bitbucket, and the `weseo-git-installer` WordPress plugin delivers only the child theme subdirectory to the server. The install and configure UI, and the current status of that process, live on Confluence. **Do not bake a page ID or a parallel install recipe into this Skill.** Find the current guide with Atlassian MCP and follow that page.

1. Locate the guide:
   - If `PROJECT-CONTEXT.md` already records a `git_installer_guide` page ID from a previous run, try `confluence_get_page` on that ID first.
   - Otherwise — and whenever that ID 404s — `confluence_search` in space `Frontend` for `weseo-git-installer` (and `git-installer` if needed). Prefer the hit whose title is about Bitbucket / WESEO git-installer, not an unrelated mention.
   - One clear hit: `confluence_get_page` on that ID and continue.
   - Several plausible hits: list title + URL in German and ask which one to use. Do not guess.
   - No hit: stop. Ask the user for the current Confluence URL. Do not invent install steps and do not continue this step without a page.
2. Tell the user the current page title and status as Confluence has it, then guide them through the current installer and go-live sections of **that** page (plugin download, WordPress install, Bitbucket access fields, repository add, branch, directory, hooks, Bitbucket registration / removal, go-live deregistration). If WP Pusher is still installed, follow the Confluence uninstall-first instruction; a full Remote-SSH migration is the bundled `migrate-ssh-to-local` Skill.
3. Apply these SmartFlow workspace contracts on top of the Confluence UI steps — they are not a substitute for the page:
   - Repository URL: the project repository from Step 1.
   - Target directory: `./wp-content/themes/<child-theme>/`.
   - The installer must write the deployed commit hash to `.wso-deployed-commit` in the child theme root on every deploy. If the Confluence page or the plugin cannot yet do that, record it as an open item — deploy verification over the bridge cannot pass until it is wired.
   - The agent never pushes; the user pushes.
4. Credentials named on the Confluence page (LastPass notes, Bitbucket API keys) are entered by the user in the WordPress admin. Never retrieve, paste, or record those values in chat, `.env`, or `PROJECT-CONTEXT.md`.

Record in `PROJECT-CONTEXT.md`: deploy path `weseo-git-installer`, the deploy branch, the target directory, the Bitbucket registration status, the deployed-commit mechanism (or its open item), and `git_installer_guide` as the Confluence page ID, URL, and title **as found this run**.

Go-live: follow the go-live section of the found page as currently written. Record that as a pending go-live step, not as a setup task.

## Step 8: Create The Read-Only FTP User With `.ftpaccess`

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

## Step 9: Install The Status Bridge

Run the bundled `install-status-bridge` Skill. It installs the versioned managed block in the child theme's `js-snippets.php`, wires the deployed-commit contract from Step 7, commits with the SmartFlow trailer, and makes the hard stop so the user pushes and the git installer deploys.

After the user confirms the push and the deploy ran, verify over `GET <site-url>/wp-json/wso/v1/status` per the `status-bridge` Rule: `bridge_version` equals `WSO_BRIDGE_VERSION` from the bundled template, and `deployed_commit` equals the local `git rev-parse HEAD`. This doubles as the first end-to-end test of the whole deploy chain: commit -> user push -> git installer -> bridge.

## Step 10: Expose WordPress Content Over REST

Server write access runs entirely over the REST API, so the project's content types and options pages must actually be reachable there. Absorbed from the legacy `setup-wordpress-cursor` skill.

1. **Post types and taxonomies:** guide the user through the CPT UI settings to enable the REST API (`show_in_rest`) for all relevant post types and taxonomies. Confirm each part before continuing.
2. **ACF field groups:** guide the user to expose the field groups belonging to those posts in the REST API ("Show in REST API" in the ACF field group settings).
3. **ACF options pages:** options pages are not reachable over the core REST API. Probe the project endpoint with the auth header from Step 4:

```text
GET <site-url>/wp-json/wso/v1/options/wso-website-settings
```

   - **Reachable:** confirm to the user and continue.
   - **404 or error:** install the endpoints from `reference/acf-options-rest-endpoints.php` into the child theme's `js-snippets.php` — read the existing file first and match its style, adapt the `$options_pages` map to the project's actual options pages, and respect the `file-edit-boundary` Rule (never `functions.php`). This is tracked source: commit with the SmartFlow trailer and make the hard stop so the user pushes and the deploy delivers it (per the `deploy-and-branches` Rule, bundle it with the Step 9 bridge commit when both run in one sitting). Re-probe after the bridge confirms the deployed commit. As a fallback (no `js-snippets.php`, or the project manages snippets in the admin), hand the code to the user for the Code Snippets plugin instead.

4. Verify with the options-pages part of `reference/rest-connection-test.md` and record `rest_exposure: verified` (or `pending: <reason>` with the open post types or options slugs) in `PROJECT-CONTEXT.md`.

## Step 11: Configure Playwright MCP

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

## Step 12: Verify Figma MCP

Section and CPT work reads Figma through the Figma MCP, not through the browser. The wizard must check that the server is actually running in this Cursor session — listing it under Settings is not enough.

Do not write Figma credentials, OAuth tokens, or personal access tokens into `.cursor/mcp.json`, `.env`, chat, or `PROJECT-CONTEXT.md`. The official Cursor Figma plugin authenticates with OAuth. Prefer a **user-level** server so every project workspace inherits it; a project-level server whose name contains `figma` is also acceptable. Record the observed server identifier, never secrets, emails, or account handles.

1. Probe the MCP catalog for any server whose name contains `figma` (covers `plugin-figma-figma`, `figma`, and similar). Treat `needsAuth`, `error`, and `loading` as not ready. If `needsAuth`, ask the user to complete OAuth under `Settings` -> `Tools & MCP`, then re-probe. If the server is missing, ask the user to add the official Figma Cursor plugin there (user-level) and restart or reconnect.
2. When the server is usable, discover the tool schema and run a cheap live read: `whoami` (no arguments). Do not call `get_design_context` or `use_figma` during setup — those need a file, skills, and are not a health check.
3. Record in `PROJECT-CONTEXT.md`:
   - `figma_mcp: ready` when `whoami` succeeded, plus the server identifier. Do not copy the returned email, handle, or plan names.
   - `figma_mcp: pending: <reason>` with the next action when the server is missing, still in `needsAuth`/`error`, or `whoami` failed.

Do not declare setup complete while this gate is unresolved unless the user chooses to record it as `pending`.

## Step 13: Fill PROJECT-CONTEXT.md

`PROJECT-CONTEXT.md` at the repository root is the project's non-secret context contract; later SmartFlow work reads it first. Create it if missing, update stale values if present. At minimum record:

- Project name, live URL, and dev/staging URL.
- Server hostname (equals the local folder name) and the reason for the naming (DevTools Local Overrides).
- Child theme path and WST source path (`wp-content/themes/<child-theme>/smart-template-builder/`).
- Working branch and deploy branch; deploy path `weseo-git-installer` with target directory and Bitbucket registration status; the pending go-live deregistration step; `git_installer_guide` (Confluence page ID, URL, and title as fetched).
- Bridge base URL (`<site-url>/wp-json/wso/v1/`), installed bridge version, and the deployed-commit write mechanism (or its open item).
- The Confluence block from Step 6: page ID, URL, mirror timestamp, and the extracted values (or `confluence_anchor: none`).
- Credential environment variable names (`WSO_BRIDGE_USER`, `WSO_BRIDGE_APP_PASSWORD`, `WSO_FTP_USER`, `WSO_FTP_PASSWORD`) with purposes — names only, never values.
- FTP host and the verified read-only status.
- REST exposure state: post types and taxonomies with `show_in_rest`, REST-exposed ACF field groups, and the reachable options-page slugs under `wso/v1/options/` (or the open item).
- `rest_access`, `rest_exposure`, `ftp_read_access`, `playwright_mcp`, `atlassian_mcp`, `confluence_anchor`, and `figma_mcp` gate statuses.
- Location of the project `docs/` layer and `tmp/` policy (gitignored scratch space).
- Setup completion status per step (`done`, `pending: <reason>`, `skipped: <reason>`).

Never store real tokens, application passwords, token-bearing URLs, or any secret values.

## Final Verification

Walk the gates once more and confirm each has a recorded outcome:

- [ ] Repository cloned at wp-content level; child theme path present; deny-all `.gitignore` intact.
- [ ] Local folder name equals the server hostname exactly; workspace opened on it.
- [ ] `.env` filled by the user; variable names recorded, no values leaked.
- [ ] REST test passed with `manage_options`.
- [ ] Atlassian MCP running: server identifier recorded, Confluence search succeeded.
- [ ] Confluence anchor resolved: page anchored and extract mirrored into `PROJECT-CONTEXT.md`, or `confluence_anchor: none` consciously recorded.
- [ ] `weseo-git-installer` configured from the Confluence guide found this run (repo, deploy branch, child theme target, Bitbucket registration); deployed-commit mechanism recorded.
- [ ] Read-only FTP user verified: read works, write denied by `.ftpaccess`.
- [ ] Status bridge installed and bridge-verified after the first deploy (`bridge_version` and `deployed_commit` match).
- [ ] REST exposure done: relevant post types, taxonomies, and ACF field groups reachable over REST; options-page endpoints probed and installed if needed.
- [ ] Playwright MCP ready, verification loop done.
- [ ] Figma MCP running: server identifier recorded, `whoami` succeeded.
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
