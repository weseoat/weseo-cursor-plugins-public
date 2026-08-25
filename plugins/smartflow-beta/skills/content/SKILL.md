---
name: content
description: Edit WordPress page, post, and ACF options content over the REST API with a mandatory pre-write backup, full Flexible Content array round-trip, UTF-8 JSON, and a status-bridge cache flush. Use when changing Section copy, filling or updating Flexible Content rows, editing a page or CPT post's ACF values, or patching options-page fields — not for new layouts, templates, or CSS.
---

# WordPress Content Over REST

Use this Skill to change **content values** in WordPress over the REST API. There is no server shell and no WP-CLI. Writes hit the database immediately; they are not tracked theme source and do not go through the deploy path.

Apply the `wordpress-content-editing` Rule on every write. Title and subtitle fields follow the bundled `headline-filling` Skill. New media goes through `wp-media-import` first, then this Skill stores the attachment ID.

## Inputs

Read from `PROJECT-CONTEXT.md` or the user:

- Staging / dev URL (working target). Stop if the URL is the live URL unless the user explicitly confirmed that target.
- Credential environment variable names (default `WSO_BRIDGE_USER`, `WSO_BRIDGE_APP_PASSWORD`). Secrets enter commands only through environment variables (`secrets` Rule).
- Flexible Content field name and layout list under ACF References / Flexible Content Layouts.
- Target page, post, or options slug: ID, REST base, and language.



## Out Of Scope


| Request                                              | Route instead                                                                       |
| ---------------------------------------------------- | ----------------------------------------------------------------------------------- |
| New Section layout, ACF field group, or WST template | `wst-section-workflow` / `wst-new-post-type`                                        |
| CSS / visual QA                                      | `frontend-section-qa` / `cpt-frontend-qa`                                           |
| Upload a file into the Media Library                 | `wp-media-import`                                                                   |
| Compose a whole page from Figma                      | later page-builder workflow; this Skill only patches values on an existing document |




## Step 1: Prepare

1. Create a scratch folder outside the deploy path: `tmp/<task-slug>/`.
2. Load `.env` and confirm the credential variables are set (existence only, never print values).
3. Probe the connection:

```sh
curl -sS -u "$WSO_BRIDGE_USER:$WSO_BRIDGE_APP_PASSWORD" \
  "<site-url>/wp-json/wp/v2/users/me?context=edit"
```

The user must have `manage_options` (same application password as the status bridge). On `401`/`403`, ask the user to re-issue the password; never guess credentials.

## Step 2: Backup Before Every Write

Read the full current document, including ACF, and save the response before changing anything:

```sh
curl -sS -u "$WSO_BRIDGE_USER:$WSO_BRIDGE_APP_PASSWORD" \
  "<site-url>/wp-json/wp/v2/<rest-base>/<id>?context=edit"
```

Options pages use the project endpoints:

```sh
curl -sS -u "$WSO_BRIDGE_USER:$WSO_BRIDGE_APP_PASSWORD" \
  "<site-url>/wp-json/wso/v1/options/<slug>"
```

Save as `tmp/<task-slug>/backup-<type>-<id>-<timestamp>.json`. These files stay in `tmp/` (gitignored) and are never committed. Repeat the backup immediately before every subsequent write, not only the first one.

## Step 3: Plan The Edit

1. Identify the target row by layout name (`acf_fc_layout`), index, or visible copy. Resolve the Flexible Content field name from `PROJECT-CONTEXT.md`; do not hardcode `flexible_content`.
2. Clone the full array from the backup. Change only the requested fields. Preserve layout metadata, media IDs, button targets, and nested arrays per the `wordpress-content-editing` Rule.
3. Normalize types before write (`acfe_flexible_toggle` booleans, enums, attachment IDs not URLs).
4. For title/subtitle Format and Style fields, follow `headline-filling`.
5. Show the user a short preview of the planned field diffs unless they already specified the exact new values.



## Step 4: Write (UTF-8)

Send the complete Flexible Content array (or the complete options document), not a partial row:

```sh
curl -sS -X POST -u "$WSO_BRIDGE_USER:$WSO_BRIDGE_APP_PASSWORD" \
  -H "Content-Type: application/json; charset=utf-8" \
  --data-binary "@tmp/<task-slug>/payload.json" \
  "<site-url>/wp-json/wp/v2/<rest-base>/<id>"
```

Write the payload file as UTF-8 bytes. Plain-text fields use real characters (`ä`, `ö`, `ü`, `ß`, `–`, `„`, `“`), not HTML entities. WYSIWYG fields may contain HTML; umlauts stay real characters there too. Do not round-trip the body through a mis-encoded PowerShell string.

On `4xx`/`5xx`, stop, keep the backup, and report the response body. Do not retry a write blindly.

## Step 5: Flush And Verify

Flush caches through the status bridge (`status-bridge` Rule), never WP-CLI:

```sh
curl -sS -X POST -u "$WSO_BRIDGE_USER:$WSO_BRIDGE_APP_PASSWORD" \
  "<site-url>/wp-json/wso/v1/flush-cache"
```

Re-read the document with `context=edit` and confirm the intended fields. Then open the public URL and check that the new copy is visible. A content write is not a frontend QA pass.

## Step 6: Record

If a matching Section or CPT work record exists under `docs/`, add the page URL, changed fields, preserved layout/style fields, and any follow-up that still needs CSS. Leave Visual QA Targets alone unless this task is explicitly a content fix for a listed row.

Leave the backup JSON in `tmp/` until the user confirms the result; delete it on request.

> ## Checklist

- [ ] Working URL is staging/dev, or the user confirmed live.
- [ ] REST probe `users/me` succeeded; credential env vars are set.
- [ ] Full document backed up under `tmp/<task-slug>/` before each write.
- [ ] Flexible Content field name taken from `PROJECT-CONTEXT.md`.
- [ ] Complete array round-trip; only requested fields changed.
- [ ] UTF-8 JSON with real characters; payload written as bytes.
- [ ] Title/subtitle fields follow `headline-filling` when touched.
- [ ] New media imported through `wp-media-import` (IDs, not URLs).
- [ ] Cache flushed through the status bridge.
- [ ] Re-read plus public URL confirm the change.
- [ ] Work record updated when one exists; backups not committed.