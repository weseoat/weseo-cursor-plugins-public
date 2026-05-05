---
name: wp-media-import
description: Import files into the WordPress Media Library through WP-CLI with safe filenames, metadata, verification, cache flushing, and cleanup.
---

# WP Media Import

Use this Skill when media files already exist on the server and need to become WordPress Media Library attachments.

## Inputs

Read these values from project context or the user:

- WordPress root: `<wp-root>`
- WP-CLI command: `<wp-cli-command>`
- Cache flush command: `<cache-flush-command>`
- Upload directory: `wp-content/uploads/<year>/<month>/`
- Filenames to import.
- Attachment title, alt text, and caption policy.
- Language and photographer credit conventions.

## Step 1: Identify Unregistered Files

List files in the target upload directory and compare them with existing attachment records.

```sh
cd <wp-root>
ls -la wp-content/uploads/<year>/<month>/
<wp-cli-command> post list --post_type=attachment --fields=ID,post_title,guid --format=csv
```

Files present on disk but absent from attachment results need importing.

## Step 2: Normalize Filenames

Rename files before importing.

| Rule | Example |
|---|---|
| Lowercase only | `Example Photo.jpg` -> `example-photo.jpg` |
| Hyphens instead of spaces | `team portrait.jpg` -> `team-portrait.jpg` |
| No punctuation that complicates shells or URLs | `photo(final).jpg` -> `photo-final.jpg` |
| Replace language-specific characters with ASCII equivalents | `büro.jpg` -> `buero.jpg` |
| No underscores | `team_photo.jpg` -> `team-photo.jpg` |
| Descriptive name | `IMG_1234.jpg` -> `mountain-panorama.jpg` |
| Include credit only when project policy asks for it | `mountain-panorama-sample-author.jpg` |

Use shell globs only when needed for encoding or copy/paste issues.

```sh
cd <wp-root>/wp-content/uploads/<year>/<month>
mv "Original Name.jpg" normalized-name.jpg
```

## Step 3: Import Through WP-CLI

Import each file with preserved file time.

```sh
cd <wp-root>
<wp-cli-command> media import wp-content/uploads/<year>/<month>/normalized-name.jpg --preserve-filetime
```

Record the returned attachment ID for metadata and verification.

If thumbnail generation fails because the server is under memory pressure, retry once after confirming the first command did not leave a usable attachment.

## Step 4: Set Metadata

Set title, alt text, and caption for each attachment.

```sh
<wp-cli-command> post update <attachment-id> --post_title="<attachment-title>" --post_excerpt="<caption-or-credit>"
<wp-cli-command> post meta update <attachment-id> _wp_attachment_image_alt "<accessible-alt-text>"
```

Metadata guidance:

| Field | Content |
|---|---|
| Filename | Descriptive, lowercase, hyphenated, URL-safe. |
| Title | Short human-readable subject. |
| Alt text | Accessible description of what the image shows. |
| Caption | Credit or caption according to project policy. |

## Step 5: Flush Caches

Run the project-approved cache command.

```sh
cd <wp-root>
<cache-flush-command>
```

## Step 6: Verify

Confirm attachment records and metadata.

```sh
<wp-cli-command> post list --post_type=attachment --include=<attachment-ids> --fields=ID,post_title,post_excerpt,guid --format=table
<wp-cli-command> post meta get <attachment-id> _wp_attachment_image_alt
```

If the media is used by a Section, update the Section handoff with the attachment IDs, target URL, and any visual checks needed locally.

## Cleanup

If a failed import leaves an unusable attachment, remove only that known bad attachment after confirming it is not referenced by content.

```sh
<wp-cli-command> post delete <orphan-attachment-id> --force
```

Do not delete upload files or existing attachments unless the user explicitly asks and the target ID/path has been verified.

## Checklist

- [ ] Confirm upload directory and project media policy.
- [ ] Identify files present on disk but absent from the Media Library.
- [ ] Normalize filenames before import.
- [ ] Import each file with WP-CLI.
- [ ] Record attachment IDs.
- [ ] Set title, alt text, and caption.
- [ ] Flush caches.
- [ ] Verify attachment records and metadata.
- [ ] Update Section handoff when media affects a frontend Section.
- [ ] Clean up only verified unusable attachments from failed imports.
