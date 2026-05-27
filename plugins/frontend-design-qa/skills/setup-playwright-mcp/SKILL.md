---
name: setup-playwright-mcp
description: Configure Playwright MCP in the local Cursor workspace so Frontend Design QA can drive a real browser for CSS/SCSS QA. Use before frontend-section-qa or cpt-frontend-qa when browser-driven QA is needed and Playwright MCP is missing, pending, or unverified for the active local workspace. Local Cursor workspace only; never run over Remote-SSH.
---

# Setup Playwright MCP

Use this Skill once per local Cursor workspace to make Playwright MCP available for Frontend Design QA browser work. It guides the user through adding the Playwright MCP server to the untracked local `.cursor/mcp.json` file (or the Cursor `Settings -> Tools & MCP` UI), verifies that the browser tools work against a dev or staging URL, and records the readiness status so later Skills can rely on it.

This Skill belongs to the local frontend phase. WordPress MCP and Figma MCP are configured in the server workspace through `wordpress-server-ops` `setup-orientation`. Playwright MCP is a local browser tool and must never be configured inside a Remote-SSH workspace.

## Why This Skill Exists

LLM-driven CSS and SCSS work needs a real browser the agent can drive. Pure project-local Playwright test scripts are not enough on their own: they cannot inspect spacing, computed style, viewport behavior, or visual differences during implementation. Playwright MCP gives Frontend Design QA a reproducible browser loop for navigation, accessibility snapshots, screenshots, viewport changes, and selector/style inspection, and project-local Playwright specs remain available as optional persistent regression checks.

## Workspace Scope

- Run this Skill only in a local Cursor workspace, opened on the developer's own machine.
- Do not run it inside a Cursor Remote-SSH workspace that is connected to a WordPress server. The Playwright MCP server must launch a browser process locally, not on the WordPress host.
- If the current workspace is the Remote-SSH server workspace, stop and ask the user to switch to their local Frontend Design QA workspace before continuing.
- `.cursor/mcp.json` stays untracked. Do not commit it. Do not paste real tokens or browser session cookies into chat, tracked files, diagnostics, or screenshots.

## Inputs

Read these from `PROJECT-CONTEXT.md`, the active Section or CPT handoff, or the user:

- Local frontend workspace path.
- Local Node.js runtime status (`node`, `npm`, and `npx` available in the local Cursor environment).
- Target dev or staging URL for the active Section or CPT QA.
- Default viewports for desktop, tablet, and mobile.
- Any blockers for browser access (HTTP basic auth, cookie banner, login wall, geo-restriction, IP allowlist, self-signed cert).
- Screenshot policy: whether the project workflow uses screenshots in reviews or handoff QA notes.
- Existing Playwright MCP status in `PROJECT-CONTEXT.md`, if recorded.

If the target dev or staging URL or required viewports are unknown, stop and ask. Do not invent URLs or viewport sizes.

## Workflow

Track progress with this checklist:

```text
Setup Playwright MCP:
- [ ] Confirm the current Cursor workspace is local (not Remote-SSH)
- [ ] Verify local Node.js, npm, and npx are installed and visible to Cursor
- [ ] Read PROJECT-CONTEXT.md and the active handoff for target URL and viewports
- [ ] Add Playwright MCP to the local untracked .cursor/mcp.json
- [ ] Restart Cursor and verify the Playwright MCP server is active in Settings -> Tools & MCP
- [ ] Run a verification browser loop against the target URL
- [ ] Record playwright_mcp status in PROJECT-CONTEXT.md and the active handoff
```

## 1. Confirm The Local Workspace

Before any MCP write, verify the workspace is local and not Remote-SSH.

- Ask the user: `Bist du gerade in deinem lokalen Cursor-Workspace (nicht Remote-SSH)?` and wait for an explicit answer.
- If unsure, check whether the active workspace path looks like a local Git checkout or like a Remote-SSH WordPress root (for example `/usr/home/.../public_html/`). A Remote-SSH WordPress root is the wrong workspace for this Skill.
- If the workspace is Remote-SSH, stop and ask the user to open the local Frontend Design QA workspace. Record the gate as `playwright_mcp: pending - wrong workspace (Remote-SSH detected), next action: open local frontend workspace and re-run setup-playwright-mcp` in `PROJECT-CONTEXT.md`.

## 2. Verify Local Node Runtime

Playwright MCP is launched from the local Cursor workspace through `npx`. That means the developer's local machine must have Node.js with `npm` and `npx` installed, and Cursor must see those commands in its local PATH. Do this check before writing `.cursor/mcp.json`.

On Windows PowerShell:

```powershell
node --version
npm --version
npx --version
where.exe node
where.exe npm
where.exe npx
```

On macOS/Linux shells:

```sh
node --version
npm --version
npx --version
command -v node
command -v npm
command -v npx
```

If all commands succeed, continue and record the detected versions as non-secret setup context.

If `node`, `npm`, or `npx` is missing:

- Stop before editing `.cursor/mcp.json`.
- Tell the user that Playwright MCP cannot start until Node.js LTS is installed locally and visible to Cursor.
- Ask the user to install Node.js LTS through their normal local setup path, then fully restart Cursor so the PATH is refreshed.
- Record `playwright_mcp: pending: local Node.js/npm/npx missing - next action: install Node.js LTS locally and restart Cursor` in `PROJECT-CONTEXT.md` and, if an active handoff exists, in that handoff.
- Do not install Node.js on a Remote-SSH WordPress server for this Skill.

If Node.js is installed in a regular terminal but Cursor cannot see `node` or `npx`, treat it as a local PATH/Cursor restart issue. Ask the user to restart Cursor first; if still missing, route to the local machine setup owner instead of guessing a PATH mutation.

## 3. Read Project Context And The Active Handoff

Read the local `PROJECT-CONTEXT.md` for:

- `playwright_mcp` field, if it already exists.
- Default viewports for desktop, tablet, and mobile.
- Target dev or staging URL fields and any documented browser access blockers.

Read the active Section or CPT handoff for:

- Browser QA target URL (Section page URL, CPT display URL, or representative single URL).
- Required viewports and screenshot policy.

If `playwright_mcp: ready` already exists and a quick verification loop still works against the target URL, the Skill can skip ahead to recording the verified status. If the status is missing, `pending`, or older than the current local frontend workspace, continue with the configuration steps.

## 4. Add Playwright MCP To Local `.cursor/mcp.json`

The Playwright MCP server lives in the untracked `.cursor/mcp.json` of the local Cursor workspace. Tracked docs only show the placeholder shape.

Only write this entry after the local Node runtime preflight has confirmed that `npx` works in the local Cursor environment.

Either edit the file directly (preferred for agents) or guide the user through `Settings -> Tools & MCP` -> add new MCP server. Use a single entry for `playwright`. Keep any existing `wordpress` or `figma` entries if they exist in this local workspace.

Placeholder shape for tracked docs:

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

When writing the real file in the local workspace:

- Use the documented package name from the active Cursor environment or the user's instruction. Do not hardcode an arbitrary version.
- Do not add credentials, cookies, basic-auth headers, or session tokens to tracked examples. If the target URL needs authentication, document it under known browser access blockers in the active handoff and use a per-run interactive login through the browser, not stored secrets.
- Confirm the user is editing the file in the local workspace, not a Remote-SSH workspace.

After writing the file, ask the user to fully restart Cursor and re-open the local workspace so the MCP server registers.

## 5. Verify The Playwright MCP Server Is Active

After restart:

1. Open `Settings -> Tools & MCP` and confirm the `playwright` server shows as active or green.
2. From chat in the local workspace, list available MCP tools and confirm Playwright-style browser tools are present (navigate, snapshot, screenshot, viewport, click/type, evaluate or similar).
3. If the server is missing, error red, or has no tools, stop and inspect the local `.cursor/mcp.json`, restart Cursor again, and re-check.

If a corporate proxy, antivirus, or sandbox blocks the Playwright browser download or process spawn, record the symptom and route it to the local IT/setup owner. Do not invent a workaround that disables sandboxing or commits credentials.

## 6. Run A Verification Browser Loop

Run a small loop against the active handoff's target URL to confirm the browser actually works for QA. Use the active handoff for the URL and viewports.

Recommended verification steps:

1. Navigate to the handoff target URL.
2. Take an accessibility snapshot or DOM snapshot.
3. Take a screenshot at desktop viewport.
4. Switch to tablet viewport, screenshot again.
5. Switch to mobile viewport, screenshot again.
6. Locate the primary Section class or CPT grid/card selector from the handoff and confirm it is visible.

If the verification loop fails for an environmental reason (login wall, geo block, cookie banner overlay, self-signed cert, missing display, headless restriction), record the blocker concretely:

- URL.
- Step that failed.
- Observed message.
- Whether the same URL loads in a regular browser.
- Suggested next action (user login flow, cookie consent dismiss, IT allowlist request, accept cert, switch URL).

## 7. Record Status In PROJECT-CONTEXT.md And The Active Handoff

After verification, write a short, non-secret status entry in `PROJECT-CONTEXT.md`:

- `playwright_mcp: ready` when the server is active and the verification loop succeeds.
- `playwright_mcp: pending: <short reason> - next action: <concrete step>` when the server is missing, errors, or the verification loop is blocked.

Add a matching line to the active Section or CPT handoff so the next QA Skill can pick it up without re-asking:

- Local Playwright MCP status.
- Verified browser QA target URL.
- Verified viewports.
- Any browser access blocker noted during verification.
- Screenshot policy decision for this handoff.

Do not write tokens, cookies, basic-auth credentials, session IDs, or token-bearing URLs into `PROJECT-CONTEXT.md`, handoffs, chat, tracked files, diagnostics, or screenshots.

## 8. Handoff Back To Frontend QA Skills

When `playwright_mcp: ready` is recorded, the active handoff is unblocked for browser-driven QA. Continue with:

- `frontend-section-qa` for Section-level browser QA and CSS/SCSS work.
- `cpt-frontend-qa` for CPT card, archive/grid, carousel/filter, and optional single-template browser QA.

If `playwright_mcp` is `pending`, those Skills must either document a focused manual acceptance path in the handoff with the blocker, or stop and route back to this Skill once the blocker is resolved. Do not silently skip browser QA.

## Concise Example

A developer opens a local Frontend Design QA workspace, with an active Section handoff for `Feature Cards` at a staging URL:

1. Confirms the workspace is local, not Remote-SSH.
2. Verifies `node --version`, `npm --version`, and `npx --version` in the local Cursor environment.
3. Reads `PROJECT-CONTEXT.md`: `playwright_mcp` is missing. Reads the handoff: target URL is `https://staging.example/<page>`, viewports are 1440, 1024, 390.
4. Adds a `playwright` entry to the untracked local `.cursor/mcp.json` and restarts Cursor.
5. Verifies the `playwright` server is active in `Settings -> Tools & MCP` and that browser tools are listed.
6. Navigates to the staging URL, snapshots the page, screenshots at desktop, tablet, and mobile, and locates `.wso-section-feature-cards`.
7. Records `playwright_mcp: ready` in `PROJECT-CONTEXT.md` and adds local Playwright MCP status and verified viewports to the Section handoff.
8. Continues with `frontend-section-qa` for the actual CSS or SCSS work and QA writeback.

## Not In Scope

- Configuring WordPress MCP or Figma MCP. Those live in the Remote-SSH server workspace and are owned by `wordpress-server-ops` `setup-orientation`.
- Writing project-local Playwright test files or running a project test runner. Those remain optional persistent regression checks owned by the project and used from `frontend-section-qa` or `cpt-frontend-qa` when a real harness exists.
- Server, cache, WP-CLI, deployment, or content actions. Route those back to `wordpress-server-ops` or the project's `PROJECT-CONTEXT.md` cache guidance.
