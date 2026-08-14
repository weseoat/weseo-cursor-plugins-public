---
name: wp-media-import
description: Import local files into the WordPress Media Library over the WordPress REST API with safe filenames, metadata, verification, a bridge cache flush, and cleanup. Use when a Section, CPT, or content task needs assets that are not yet attachments, when the user asks to upload images or files to WordPress, or when a work record lists missing media. Replaces the legacy WP-CLI media import; there is no server shell in the SmartFlow workspace.
---

# WP Media Import

Use this Skill when files in the local workspace (or provided by the user) need to become WordPress Media Library attachments. Everything runs over the WordPress REST API with the project application password; there is no server shell, no WP-CLI, and no direct writes into `wp-content/uploads/`.

## Inputs

Read from `PROJECT-CONTEXT.md` or the user:

- Site URL and the credential environment variable names (default `WSO_BRIDGE_USER`, `WSO_BRIDGE_APP_PASSWORD`; the same application password used for the status bridge). Credentials enter commands exclusively through environment variables — never as literals in tracked files, chat, logs, or docs.
- The local source files to import: Figma exports, user-supplied files, or files staged under the project's gitignored scratch space (default `tmp/`). Upload material must not be placed inside the child theme deploy path and is not committed unless the project explicitly tracks assets.
- Attachment title, alt text, and caption policy; language and photographer credit conventions.
- The work record the import belongs to, when the media serves a Section or CPT.

## Step 1: Check For Existing Attachments

Before uploading, check whether the asset already exists so the Media Library does not collect duplicates:

```sh
curl -sS -u "$WSO_BRIDGE_USER:$WSO_BRIDGE_APP_PASSWORD" \
  "<site-url>/wp-json/wp/v2/media?search=<descriptive-term>&per_page=20&_fields=id,title,source_url,alt_text"
```

If a match fits the need, reuse its attachment ID and record it instead of uploading. The WST workflows already run this lightweight lookup during discovery; this Skill repeats it only when the result is not already in the work record.

## Step 2: Normalize Filenames

Rename the local files before uploading. WordPress derives the attachment slug and file URL from the uploaded filename.

| Rule | Example |
|---|---|
| Lowercase only | `Example Photo.jpg` -> `example-photo.jpg` |
| Hyphens instead of spaces | `team portrait.jpg` -> `team-portrait.jpg` |
| No punctuation that complicates shells or URLs | `photo(final).jpg` -> `photo-final.jpg` |
| Replace language-specific characters with ASCII equivalents | `büro.jpg` -> `buero.jpg` |
| No underscores | `team_photo.jpg` -> `team-photo.jpg` |
| Descriptive name | `IMG_1234.jpg` -> `mountain-panorama.jpg` |
| Include credit only when project policy asks for it | `mountain-panorama-sample-author.jpg` |

## Step 3: Upload Over The REST API

Upload each file with one authenticated request. Set the filename in the `Content-Disposition` header and the correct MIME type:

```sh
curl -sS -X POST -u "$WSO_BRIDGE_USER:$WSO_BRIDGE_APP_PASSWORD" \
  -H "Content-Disposition: attachment; filename=normalized-name.jpg" \
  -H "Content-Type: image/jpeg" \
  --data-binary "@<local-path>/normalized-name.jpg" \
  "<site-url>/wp-json/wp/v2/media"
```

The response is the created attachment object. Record `id` and `source_url` for metadata and verification. WordPress generates the thumbnail sizes during this request; large images can take a moment.

Failure handling:

- `401`/`403`: application password or capability problem (`upload_files` is required). Ask the user to re-issue the application password; never guess credentials.
- `413` or a server-size error: the file exceeds the upload limit. Hand the file to the user for admin upload instead of retrying; record the limit symptom.
- `5xx` during thumbnail generation: check with a `GET /wp-json/wp/v2/media?search=<name>` whether a usable attachment was created before retrying once. Never retry blindly.

## Step 4: Set Metadata

Update title, alt text, and caption in one request per attachment:

```sh
curl -sS -X POST -u "$WSO_BRIDGE_USER:$WSO_BRIDGE_APP_PASSWORD" \
  -H "Content-Type: application/json" \
  -d '{"title":"<attachment-title>","alt_text":"<accessible-alt-text>","caption":"<caption-or-credit>"}' \
  "<site-url>/wp-json/wp/v2/media/<attachment-id>"
```

| Field | Content |
|---|---|
| Filename | Descriptive, lowercase, hyphenated, URL-safe (set before upload). |
| Title | Short human-readable subject. |
| Alt text | Accessible description of what the image shows. |
| Caption | Credit or caption according to project policy. |

## Step 5: Flush Caches Through The Bridge

After imports that affect rendered pages, flush over the status bridge per the `status-bridge` Rule (never WP-CLI):

```sh
curl -sS -X POST -u "$WSO_BRIDGE_USER:$WSO_BRIDGE_APP_PASSWORD" "<site-url>/wp-json/wso/v1/flush-cache"
```

Verify the `flushed` list in the response. Skip the flush when the media is not yet referenced by any page.

## Step 6: Verify And Record

Confirm each attachment and its metadata:

```sh
curl -sS -u "$WSO_BRIDGE_USER:$WSO_BRIDGE_APP_PASSWORD" \
  "<site-url>/wp-json/wp/v2/media/<attachment-id>?_fields=id,title,alt_text,caption,source_url,media_details"
```

Check that `source_url` responds with the file and that the expected sizes exist in `media_details`. Record the attachment IDs, titles, and intended usage in the work record of the Section or CPT the media serves, so the WST workflow or content step can reference them; then delete staged upload copies from the scratch space.

## Cleanup

If a failed upload leaves an unusable attachment, remove only that verified bad attachment after confirming it is not referenced by content:

```sh
curl -sS -X DELETE -u "$WSO_BRIDGE_USER:$WSO_BRIDGE_APP_PASSWORD" \
  "<site-url>/wp-json/wp/v2/media/<orphan-attachment-id>?force=true"
```

Do not delete existing attachments or upload files unless the user explicitly asks and the target ID has been verified. Attachment deletion with `force=true` is irreversible; confirm the ID against the Step 6 verification output first.

## Checklist

- [ ] Confirm site URL, credential env var names, and project media policy.
- [ ] Check for existing attachments over `GET /wp-json/wp/v2/media?search=`.
- [ ] Normalize filenames locally before upload.
- [ ] Upload each file over `POST /wp-json/wp/v2/media`.
- [ ] Record attachment IDs and source URLs.
- [ ] Set title, alt text, and caption.
- [ ] Flush caches through the bridge when rendered pages are affected.
- [ ] Verify attachment records, metadata, and `source_url`.
- [ ] Record attachment IDs in the affected work record; clean staged copies from the scratch space.
- [ ] Clean up only verified unusable attachments from failed uploads.
