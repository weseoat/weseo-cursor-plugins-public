#!/usr/bin/env bash
# ACF Local JSON pull-before-commit (SmartFlow, setup-acf-local-json Step 8)
#
# Mirrors the server-side acf-json/ folder into the repository before every
# commit, so a deploy tool that removes repo-foreign theme files (WP Pusher,
# weseo-git-installer) never deletes a JSON that only exists on the server.
# Afterwards it refuses to commit any acf-json file whose "modified" lies in
# the future (a future value makes the ACF sync hint permanent).
#
# Install:      copy to .githooks/pre-commit at the repository root, fill the
#               three placeholders below from PROJECT-CONTEXT.md, add
#               ".githooks/* text eol=lf" to .gitattributes, then run
#               git config core.hooksPath .githooks
# Manual run:   .githooks/pre-commit --dry-run
# Skip once:    ACF_JSON_PULL_SKIP=1 git commit ...   (or git commit --no-verify)
#
# Credentials come only from the repo-root .env (WSO_FTP_USER / WSO_FTP_PASSWORD)
# or from the environment; they are passed to curl over a temporary netrc file
# and are never printed.
set -u

TAG="[acf-json-pull]"
DRY_RUN=0
[ "${1:-}" = "--dry-run" ] && DRY_RUN=1

if [ "${ACF_JSON_PULL_SKIP:-0}" = "1" ]; then
  echo "$TAG skipped (ACF_JSON_PULL_SKIP=1)"
  exit 0
fi

ROOT="$(git rev-parse --show-toplevel 2>/dev/null)" || { echo "$TAG ERROR: not inside a git repository" >&2; exit 1; }
cd "$ROOT" || exit 1

# --- project values (from PROJECT-CONTEXT.md; the repo root is the wp-content level)
LOCAL_DIR="themes/<child-theme>/acf-json"      # theme path
REMOTE_HOST="<ftp-host>"                        # FTP host of the read-only user
REMOTE_DIR="<remote-dir>"                       # ftp_acf_json_path, e.g. themes/<child-theme>/acf-json

case "$LOCAL_DIR$REMOTE_HOST$REMOTE_DIR" in
  *"<"*">"*)
    echo "$TAG ERROR: placeholders not filled in .githooks/pre-commit (LOCAL_DIR / REMOTE_HOST / REMOTE_DIR). Commit aborted." >&2
    exit 1 ;;
esac

# --- credentials (repo-root .env, never echoed) --------------------------------
FTP_USER=""
FTP_PASS=""
if [ -f .env ]; then
  while IFS= read -r line || [ -n "$line" ]; do
    line="${line%$'\r'}"
    case "$line" in
      WSO_FTP_USER=*)     FTP_USER="${line#WSO_FTP_USER=}" ;;
      WSO_FTP_PASSWORD=*) FTP_PASS="${line#WSO_FTP_PASSWORD=}" ;;
    esac
  done < .env
fi
FTP_USER="${WSO_FTP_USER:-$FTP_USER}"
FTP_PASS="${WSO_FTP_PASSWORD:-$FTP_PASS}"

if [ -z "$FTP_USER" ] || [ -z "$FTP_PASS" ]; then
  echo "$TAG ERROR: WSO_FTP_USER / WSO_FTP_PASSWORD not set (.env). Commit aborted." >&2
  echo "$TAG        Skip once with ACF_JSON_PULL_SKIP=1 or --no-verify." >&2
  exit 1
fi

TMP="$(mktemp -d)" || exit 1
trap 'rm -rf "$TMP"' EXIT
mkdir -p "$TMP/remote"

NETRC="$TMP/netrc"
umask 077
printf 'machine %s login %s password %s\n' "$REMOTE_HOST" "$FTP_USER" "$FTP_PASS" > "$NETRC"
umask 022

curl_ftp() {
  curl -sS --ssl-reqd --netrc-file "$NETRC" --connect-timeout 15 --max-time 120 "$@"
}

# --- remote listing -----------------------------------------------------------
if ! curl_ftp --list-only "ftp://$REMOTE_HOST/$REMOTE_DIR/" > "$TMP/list.txt" 2> "$TMP/curl.err"; then
  echo "$TAG ERROR: cannot list ftp://$REMOTE_HOST/$REMOTE_DIR/ - $(head -n1 "$TMP/curl.err")" >&2
  echo "$TAG        Commit aborted so the deploy cannot wipe server-side ACF JSON." >&2
  echo "$TAG        Skip once with ACF_JSON_PULL_SKIP=1 or --no-verify." >&2
  exit 1
fi

REMOTE_FILES=()
while IFS= read -r name || [ -n "$name" ]; do
  name="${name%$'\r'}"
  case "$name" in
    *.json) REMOTE_FILES+=("$name") ;;
  esac
done < "$TMP/list.txt"

# --- helpers (pure bash: no per-file subprocesses, MSYS spawning is slow) ------
json_meta() {
  META_CONTENT="$(<"$1")"
  META_KEY=""
  META_MOD=0
  [[ $META_CONTENT =~ \"key\":[[:space:]]*\"([^\"]+)\" ]] && META_KEY="${BASH_REMATCH[1]}"
  [[ $META_CONTENT =~ \"modified\":[[:space:]]*([0-9]+) ]] && META_MOD="${BASH_REMATCH[1]}"
}

# --- future-modified guard (runs after the pull; defined here, called at the end)
modified_guard() {
  local now hits=0 lf
  now="$(date -u +%s)"
  for lf in "$LOCAL_DIR"/*.json; do
    [ -e "$lf" ] || continue
    json_meta "$lf"
    if [ "$META_MOD" -gt "$now" ]; then
      echo "$TAG ERROR: ${lf##*/} has \"modified\": $META_MOD in the future (now $now)." >&2
      hits=$((hits + 1))
    fi
  done
  if [ "$hits" -gt 0 ]; then
    echo "$TAG        A future modified makes the ACF sync hint permanent. Set it to the real current" >&2
    echo "$TAG        UTC epoch (python -c \"import time; print(int(time.time()))\") and commit again." >&2
    return 1
  fi
  return 0
}

if [ "${#REMOTE_FILES[@]}" -eq 0 ]; then
  echo "$TAG WARNING: server folder returned no *.json files - nothing pulled." >&2
  modified_guard || exit 1
  exit 0
fi

# --- download all files (parallel transfers, one curl process) ----------------
ARGS=()
for f in "${REMOTE_FILES[@]}"; do
  ARGS+=(-o "$TMP/remote/$f" "ftp://$REMOTE_HOST/$REMOTE_DIR/$f")
done
if ! curl_ftp --parallel --parallel-max 8 "${ARGS[@]}" 2> "$TMP/curl.err"; then
  echo "$TAG ERROR: download failed - $(head -n1 "$TMP/curl.err")" >&2
  exit 1
fi

declare -A LOCAL_FILE_BY_KEY=()
declare -A LOCAL_MOD_BY_KEY=()
declare -A LOCAL_CONTENT_BY_KEY=()
declare -A LOCAL_SEEN=()
mkdir -p "$LOCAL_DIR"
for lf in "$LOCAL_DIR"/*.json; do
  [ -e "$lf" ] || continue
  json_meta "$lf"
  [ -n "$META_KEY" ] || continue
  LOCAL_FILE_BY_KEY["$META_KEY"]="$lf"
  LOCAL_MOD_BY_KEY["$META_KEY"]="$META_MOD"
  LOCAL_CONTENT_BY_KEY["$META_KEY"]="${META_CONTENT//$'\r'/}"
done

added=0; updated=0; kept_newer=0; conflicts=0; unchanged=0
STAGE=()

for f in "${REMOTE_FILES[@]}"; do
  rf="$TMP/remote/$f"
  [ -s "$rf" ] || { echo "$TAG WARNING: empty download for $f, skipped"; continue; }
  json_meta "$rf"
  rkey="$META_KEY"
  rmod="$META_MOD"
  rcontent="${META_CONTENT//$'\r'/}"
  if [ -z "$rkey" ]; then
    echo "$TAG WARNING: $f has no \"key\", skipped"
    continue
  fi

  lf="${LOCAL_FILE_BY_KEY[$rkey]:-}"
  if [ -z "$lf" ]; then
    target="$LOCAL_DIR/$f"
    echo "$TAG + $f  ($rkey) only on server -> added"
    if [ "$DRY_RUN" -eq 0 ]; then
      cp "$rf" "$target"
      STAGE+=("$target")
    fi
    added=$((added + 1))
    continue
  fi

  LOCAL_SEEN["$rkey"]=1
  lmod="${LOCAL_MOD_BY_KEY[$rkey]}"
  lname="${lf##*/}"

  if [ "$rmod" -gt "$lmod" ]; then
    echo "$TAG ~ $lname  ($rkey) server newer ($rmod > $lmod) -> updated"
    if [ "$DRY_RUN" -eq 0 ]; then
      cp "$rf" "$lf"
      STAGE+=("$lf")
    fi
    updated=$((updated + 1))
  elif [ "$rmod" -lt "$lmod" ]; then
    echo "$TAG = $lname  ($rkey) local newer ($lmod > $rmod) -> kept"
    kept_newer=$((kept_newer + 1))
  elif [ "$rcontent" == "${LOCAL_CONTENT_BY_KEY[$rkey]}" ]; then
    unchanged=$((unchanged + 1))
  else
    echo "$TAG ! $lname  ($rkey) same modified ($rmod) but content differs -> local kept, CHECK"
    conflicts=$((conflicts + 1))
  fi
done

local_only=0
for k in "${!LOCAL_FILE_BY_KEY[@]}"; do
  if [ -z "${LOCAL_SEEN[$k]:-}" ]; then
    lname="${LOCAL_FILE_BY_KEY[$k]##*/}"
    echo "$TAG ? $lname  ($k) only local -> kept (new group, or deleted on server)"
    local_only=$((local_only + 1))
  fi
done

if [ "$DRY_RUN" -eq 0 ] && [ "${#STAGE[@]}" -gt 0 ]; then
  git add -- "${STAGE[@]}"
fi

mode="pulled"
[ "$DRY_RUN" -eq 1 ] && mode="dry-run"
echo "$TAG $mode: ${#REMOTE_FILES[@]} server files | added $added, updated $updated, unchanged $unchanged, local newer $kept_newer, conflicts $conflicts, local only $local_only"

# --- future-modified guard over the local folder (after the pull) --------------
modified_guard || exit 1

exit 0
