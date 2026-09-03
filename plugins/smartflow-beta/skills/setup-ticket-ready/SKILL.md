---
name: setup-ticket-ready
description: Bring a WESEO WordPress/WST project to a ticket-ready state in minutes - the lite setup track of setup-local-project with only the six mandatory gates without which no support ticket works (repo clone with hostname folder, .env plus REST test, minimal PROJECT-CONTEXT.md, working branch resolved including the legacy "master - user commits" state, Atlassian MCP, Playwright MCP). On-first-need gates (status bridge, Figma MCP, Confluence anchor) are set up the moment a ticket needs them; everything else is recorded as pending with the Voll-Setup pointer instead of skipped silently. Runs as step 0 gate check of jira-ticket-workflow and standalone when a colleague wants to prepare a project for support tickets ("Projekt ticket-ready machen"). The full first setup remains setup-local-project.
---

# Setup Ticket Ready

Support colleagues jump between many projects, and each project would need
the full `setup-local-project` wizard once — 13 gates, bridge install plus
deploy, FTP user, Confluence anchor — before the first small fix. For a
two-line ticket that setup costs more than the fix. This Skill is the
deliberate lite track: only the gates without which a single Jira ticket
cannot be processed, in the same `PROJECT-CONTEXT.md`, with the same
schema, and under the same Rules (`deploy-and-branches`, `secrets`,
`file-edit-boundary`, `status-bridge` apply automatically).

A project prepared by this Skill can be raised to the full setup later
without a break: every full-setup gate this track does not run is recorded
as `pending: Voll-Setup` in `PROJECT-CONTEXT.md`, so `setup-local-project`
resumes exactly there.

Like `setup-local-project`, this Skill is resumable: re-read
`PROJECT-CONTEXT.md` on every invocation, find the first mandatory gate
whose status is missing or `pending`, and resume there. It runs standalone
("Projekt ticket-ready machen") or as step 0 of the bundled
`jira-ticket-workflow` Skill, which calls it whenever mandatory gates are
open — the colleague only has to remember one thing: paste the ticket link.

The target user is a support or frontend colleague. Communicate in German
for all user-facing steps; keep commands, file names, and UI labels in
their original language. For each gate, lead with **Was passiert**,
**Warum**, and **Du musst** (the exact user action, or `Nichts tun`), and
end with `Erledigt: <result>` or `Offen: <open point> - nächster Schritt:
<action>`.

Do not invent repository names, hostnames, URLs, branches, theme names, or
Jira keys. If a value is unknown, stop and ask.

## The Six Mandatory Gates

### Gate 1: Repository Cloned, Folder = Hostname

Clone the project repository (wp-content level) if not present. The local
folder name must be exactly the server hostname, character for character —
Chrome DevTools Local Overrides use the hostname as the mapping folder
name (`setup-local-project` Steps 1–2 in condensed form).

```sh
git clone <repo-url> <server-hostname>
```

Verify `wp-content/themes/<child-theme>/` exists and the deny-all
`.gitignore` is intact.

### Gate 2: `.env` Plus REST Test

Guide the user to create a WordPress application password (admin ->
`Benutzer` -> `Profil` -> `Application Passwords`) and write it into the
repo-root `.env` themselves (`WSO_SITE_URL`, `WSO_BRIDGE_USER`,
`WSO_BRIDGE_APP_PASSWORD`), per the `secrets` Rule. Then verify:

```sh
curl -sS -u "$WSO_BRIDGE_USER:$WSO_BRIDGE_APP_PASSWORD" "<site-url>/wp-json/wp/v2/users/me?context=edit"
```

Expected: a user object whose `capabilities` include `manage_options`.
On 401/403 have the user re-issue the password; never guess credentials.
Record `rest_access: verified` (or `pending: <reason>`).

### Gate 3: Minimal `PROJECT-CONTEXT.md`

Create or complete `PROJECT-CONTEXT.md` with the minimum a ticket run
needs — otherwise the agent asks per ticket:

- Project name, live URL, and dev/staging URL.
- Server hostname (equals the folder name) and child theme path.
- Deploy path: how this project reaches the server (for example WP Pusher
  on `master`, or `weseo-git-installer` with deploy branch) — as a fact,
  not as something to configure now.
- Jira site URL and project key.
- Working branch (Gate 4).
- Credential variable names from Gate 2 — names only, never values.

Use the same schema as `setup-local-project`. Record every full-setup gate
this track does not run as `pending: Voll-Setup` (see the lists below) so
nothing is silently missing.

### Gate 4: Working Branch Resolved

Ask which branch the agent works on and how commits happen, and record it:

- **SmartFlow model:** a non-protected working branch is recorded; the
  agent commits there after the explicit confirmation the commit gate
  requires (`deploy-and-branches` Rule), the user pushes.
- **Legacy state:** the project deploys `master` (or `main`/`trunk`)
  directly, typically over WP Pusher, and there is no separate working
  branch. Record verbatim: `Working branch: master — user commits`. In
  this state the agent **never commits**: it prepares the commit (staged
  file list, summary, proposed message with the SmartFlow trailer) and
  hands over; the colleague commits and pushes themselves. This is a
  recognized state per the `deploy-and-branches` Rule, not a
  contradiction to resolve per ticket.

Do not create ticket branches, permanent agent branches, or re-wire the
project's deploy configuration — those alternatives are deliberately
rejected for support work.

### Gate 5: Atlassian MCP Running

Probe the MCP catalog for a server whose name contains `atlassian`; treat
`needsAuth`, `error`, and `loading` as not ready. The team standard and
install route (community `mcp-atlassian`, version 0.22.0 or newer, local
stdio, token in the `env` block) is in `setup-local-project` Step 5 —
guide a colleague without a server through exactly that route.

For ticket work **Jira is the blocking surface**: verify with a cheap live
read against the project's Jira (one-result issue search or reading a
known issue key). Ticket fetch, screenshot viewing
(`jira_get_issue_images`), and the solution comment all depend on it.
A working Confluence search is welcome but not required here — the
Confluence anchor is an on-first-need gate. Record
`atlassian_mcp: ready` (or `pending: <reason>`).

### Gate 6: Playwright MCP Running

Verify the Playwright MCP server as in `setup-local-project` Step 11:
Node.js present, server entry in the untracked `.cursor/mcp.json`, browser
tools listed after restart, and a short loop against the dev URL
(navigate, snapshot, screenshot). Injection-proof verification — the proof
mode of every ticket fix — depends on it. Record `playwright_mcp: ready`
(or `pending: <reason>`).

## On-First-Need Gates

Not part of the mandatory pass, but set up **immediately** when the
current ticket needs them — never worked around. Record each as
`pending: on first need` until then:

| Gate | Needed when | Route |
|---|---|---|
| Status bridge | WPGB read/write, cache/permalink flush, bridge-verified deploy checks | bundled `install-status-bridge` Skill (needs a deployable commit path — in the legacy state the colleague commits/pushes the bridge install) |
| Figma MCP | the ticket references a design | `setup-local-project` Step 12 |
| Confluence anchor | project-context questions the ticket text cannot answer | `setup-local-project` Step 6 |

## Deliberately Out (Stay `pending: Voll-Setup`)

These belong to the full `setup-local-project` run and are consciously not
part of ticket readiness: `weseo-git-installer` configuration, the
read-only FTP user with `.ftpaccess`, ACF options REST exposure, the
`.wso-deployed-commit` contract, and the `css_setup` marker pass. Record
them as `pending: Voll-Setup` in `PROJECT-CONTEXT.md` and move on.

## Deploy Proof Without A Bridge

Projects prepared by this track typically have no status bridge yet, so
`deployed_commit` verification is impossible. The served check then runs
over a **marker** per `jira-ticket-workflow` step 5: remember one
unambiguous marker the fix introduces (a new CSS variable, class, or
changed text), and after the colleague has pushed/deployed, check the
served file or markup for it. A missing marker is almost always page
cache — ask the colleague to flush the cache in the admin, then re-check.
Until the marker is served, the status stays `implementation pass,
deployed verification pending`.

## Hand-Over

End with a short German summary: which gates are `ready`/`verified`, which
are `pending` with reason and next action, and that the project is now
ready for `jira-ticket-workflow`. Point out once that the full setup
remains `setup-local-project` and resumes from the recorded
`pending: Voll-Setup` markers.

## Stop Conditions

Stop and ask before storing or displaying a credential, before continuing
past a failed REST test, and when the recorded working branch is a
protected branch **without** the explicit `— user commits` qualifier
(resolve per the `deploy-and-branches` Rule instead of committing).

## Scope Boundaries

This Skill prepares ticket processing; it does not process tickets
(`jira-ticket-workflow`), does not run the full first setup
(`setup-local-project`), does not migrate legacy Remote-SSH projects
(`migrate-ssh-to-local`), and does not reconcile CSS values
(`project-css-setup`). The batch case (`jira-batch-workflow`) uses the
same gates but stays a deliberate open point of the source decision log.
