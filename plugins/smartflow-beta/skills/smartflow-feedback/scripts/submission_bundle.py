#!/usr/bin/env python3
"""Pack and verify reviewed Skill/Rule submissions. No network or code execution."""

from __future__ import annotations

import argparse
import hashlib
import io
import json
import re
import stat
import sys
import unicodedata
import zipfile
from datetime import datetime, timezone
from pathlib import Path, PurePosixPath

MAX_FILES = 200
MAX_FILE_BYTES = 5 * 1024 * 1024
MAX_TOTAL_BYTES = 20 * 1024 * 1024
MAX_MANIFEST_BYTES = 256 * 1024
RESERVED = {"CON", "PRN", "AUX", "NUL", *(f"COM{i}" for i in range(1, 10)),
            *(f"LPT{i}" for i in range(1, 10))}


class BundleError(ValueError):
    pass


def require(condition: bool, message: str) -> None:
    if not condition:
        raise BundleError(message)


def digest(data: bytes) -> str:
    return hashlib.sha256(data).hexdigest()


def json_object(pairs: list) -> dict:
    result = {}
    for key, value in pairs:
        require(key not in result, "Duplicate JSON key")
        result[key] = value
    return result


def parse_json(data: bytes) -> dict:
    require(len(data) <= MAX_MANIFEST_BYTES, "Manifest/spec exceeds 256 KiB")
    value = json.loads(data.decode("utf-8-sig"), object_pairs_hook=json_object)
    require(isinstance(value, dict), "Manifest/spec must be an object")
    return value


def file_path(value: str) -> str:
    require(isinstance(value, str) and bool(value), "Missing file path")
    require("\\" not in value and not value.startswith("/"), "Use relative POSIX paths")
    parts = value.split("/")
    require(parts[0] in {"skills", "rules", "references", "scripts", "assets"},
            "Only contribution source paths are allowed")
    require(len(parts) >= 2, "File must be inside a source directory")
    for part in parts:
        require(part not in {"", ".", ".."} and not part.startswith("."),
                "Dot paths, hidden files and traversal are forbidden")
        require(not any(ord(c) < 32 or c in '<>:"|?*' for c in part)
                and not part.endswith((".", " ")), "Non-portable file path")
        require(part.split(".")[0].upper() not in RESERVED, "Reserved file name")
        lower = part.lower()
        require(lower not in {"node_modules", "credentials", "credentials.json",
                              "id_rsa", "id_ed25519", "__pycache__"}
                and not lower.endswith((".pem", ".key", ".p12", ".pfx", ".env")),
                "Credential/cache paths are forbidden")
    return value


def unique_paths(paths: list[str]) -> None:
    normalized = [unicodedata.normalize("NFC", p).casefold() for p in paths]
    require(len(set(normalized)) == len(normalized), "Duplicate/colliding file paths")
    for name in normalized:
        require(not any(parent in normalized for parent in
                        (str(p) for p in PurePosixPath(name).parents if str(p) != ".")),
                "File/directory path collision")


def metadata(value: dict, paths: list[str]) -> dict:
    title = value.get("title")
    require(isinstance(title, str) and 0 < len(title) <= 200, "Title must be 1-200 characters")
    plugin = value.get("source_plugin")
    require(isinstance(plugin, dict) and set(plugin) == {"name", "version"},
            "source_plugin needs name and version")
    require(all(isinstance(v, str) and 0 < len(v) <= 100 for v in plugin.values()),
            "Invalid source_plugin metadata; use 'unknown' if unavailable")
    artifacts = value.get("artifacts")
    require(isinstance(artifacts, list) and 0 < len(artifacts) <= MAX_FILES,
            "At least one Skill or Rule entrypoint is required")
    entrypoints = []
    for artifact in artifacts:
        require(isinstance(artifact, dict) and set(artifact) == {"kind", "entrypoint"},
                "Artifact needs kind and entrypoint")
        kind, entry = artifact["kind"], file_path(artifact["entrypoint"])
        pattern = r"skills/[a-z0-9]+(?:-[a-z0-9]+)*/SKILL\.md" if kind == "skill" else (
            r"rules/[a-z0-9]+(?:-[a-z0-9]+)*\.mdc" if kind == "rule" else None)
        require(pattern is not None and re.fullmatch(pattern, entry) is not None,
                "Invalid Skill/Rule entrypoint")
        require(entry in paths, "Entrypoint missing from file list")
        entrypoints.append(entry)
    unique_paths(entrypoints)
    return {"title": title, "source_plugin": plugin, "artifacts": artifacts}


def regular_source(root: Path, relative: str) -> Path:
    target = root
    for part in PurePosixPath(relative).parts:
        target = target / part
        info = target.lstat()
        require(not stat.S_ISLNK(info.st_mode)
                and not getattr(info, "st_file_attributes", 0) & 0x400,
                "Symlinks and reparse points are forbidden")
    require(target.is_file() and target.resolve().is_relative_to(root), "Not a regular source file")
    require(target.stat().st_size <= MAX_FILE_BYTES, "Source file exceeds 5 MiB")
    return target


def pack(root: Path, spec_path: Path, output: Path) -> dict:
    spec = parse_json(spec_path.read_bytes())
    require(set(spec) == {"title", "source_plugin", "artifacts", "files"}, "Unexpected spec fields")
    require(isinstance(spec["files"], list) and 0 < len(spec["files"]) <= MAX_FILES,
            "Select 1-200 files explicitly")
    paths = sorted(file_path(p) for p in spec["files"])
    unique_paths(paths)
    manifest = {"schema_version": 1, **metadata(spec, paths),
                "created_utc": datetime.now(timezone.utc).isoformat(timespec="seconds"), "files": []}
    root = root.resolve(strict=True)
    payload = {}
    total = 0
    for relative in paths:
        data = regular_source(root, relative).read_bytes()
        total += len(data)
        require(len(data) <= MAX_FILE_BYTES and total <= MAX_TOTAL_BYTES, "Bundle size limit exceeded")
        payload[relative] = data
        manifest["files"].append({"path": relative, "size": len(data), "sha256": digest(data)})
    buffer = io.BytesIO()
    with zipfile.ZipFile(buffer, "w", compression=zipfile.ZIP_DEFLATED) as archive:
        archive.writestr("submission.json", json.dumps(manifest, ensure_ascii=False, indent=2) + "\n")
        for relative, data in payload.items():
            archive.writestr("files/" + relative, data)
    raw = buffer.getvalue()
    verify_bytes(raw)  # Validate before writing any output.
    output.parent.mkdir(parents=True, exist_ok=True)
    with output.open("xb") as handle:
        handle.write(raw)
    return summary(manifest, raw)


def verify_bytes(raw: bytes, expected_sha256: str | None = None) -> tuple[dict, dict]:
    require(len(raw) <= MAX_TOTAL_BYTES + MAX_MANIFEST_BYTES + 1024 * 1024, "Archive too large")
    if expected_sha256 is not None:
        require(re.fullmatch(r"[a-fA-F0-9]{64}", expected_sha256) is not None,
                "Expected ZIP SHA-256 must contain 64 hex characters")
        require(digest(raw) == expected_sha256.lower(), "ZIP SHA-256 mismatch")
    payload = {}
    with zipfile.ZipFile(io.BytesIO(raw)) as archive:
        infos = archive.infolist()
        names = [info.filename for info in infos]
        require(1 < len(infos) <= MAX_FILES + 1, "Invalid archive file count")
        require(len(names) == len(set(names)), "Duplicate ZIP member")
        require(names.count("submission.json") == 1, "Missing submission.json")
        for info in infos:
            require(info.orig_filename == info.filename, "NUL in ZIP member name")
            require(not info.is_dir(), "Explicit directory members are forbidden")
            mode = stat.S_IFMT(info.external_attr >> 16)
            require(mode in {0, stat.S_IFREG}, "Non-regular ZIP member")
            require(not info.flag_bits & 1 and info.compress_type in
                    {zipfile.ZIP_STORED, zipfile.ZIP_DEFLATED}, "Unsupported ZIP encoding")
            limit = MAX_MANIFEST_BYTES if info.filename == "submission.json" else MAX_FILE_BYTES
            require(0 <= info.file_size <= limit, "ZIP member exceeds size limit")
            if info.filename != "submission.json":
                require(info.filename.startswith("files/"), "Unexpected ZIP member")
                file_path(info.filename[len("files/"):])
        require(sum(i.file_size for i in infos) <= MAX_TOTAL_BYTES + MAX_MANIFEST_BYTES,
                "Expanded ZIP exceeds size limit")
        manifest = parse_json(archive.read("submission.json"))
        require(set(manifest) == {"schema_version", "title", "source_plugin", "artifacts",
                                  "created_utc", "files"}, "Unexpected manifest fields")
        require(type(manifest["schema_version"]) is int and manifest["schema_version"] == 1,
                "Unsupported schema version")
        require(isinstance(manifest["created_utc"], str), "Missing creation time")
        created = datetime.fromisoformat(manifest["created_utc"])
        require(created.utcoffset() is not None and created.utcoffset().total_seconds() == 0,
                "Creation time must be UTC")
        records = manifest["files"]
        require(isinstance(records, list) and 0 < len(records) <= MAX_FILES, "Invalid file list")
        paths = []
        for record in records:
            require(isinstance(record, dict) and set(record) == {"path", "size", "sha256"},
                    "Invalid file record")
            paths.append(file_path(record["path"]))
            require(type(record["size"]) is int and 0 <= record["size"] <= MAX_FILE_BYTES,
                    "Invalid file size")
            require(isinstance(record["sha256"], str) and
                    re.fullmatch(r"[a-f0-9]{64}", record["sha256"]) is not None, "Invalid file hash")
        unique_paths(paths)
        metadata(manifest, paths)
        require(set(names) == {"submission.json", *("files/" + p for p in paths)},
                "ZIP members do not match the manifest")
        for record in records:
            data = archive.read("files/" + record["path"])
            require(len(data) == record["size"] and digest(data) == record["sha256"],
                    "File content does not match its manifest")
            payload[record["path"]] = data
        require(sum(map(len, payload.values())) <= MAX_TOTAL_BYTES, "Payload exceeds size limit")
    return manifest, payload


def summary(manifest: dict, raw: bytes) -> dict:
    return {"schema_version": 1, "zip_sha256": digest(raw), "zip_bytes": len(raw),
            "file_count": len(manifest["files"]), "manifest": manifest}


def inspect(archive: Path, expected_sha256: str | None = None) -> dict:
    require(archive.stat().st_size <= MAX_TOTAL_BYTES + MAX_MANIFEST_BYTES + 1024 * 1024,
            "Archive too large")
    raw = archive.read_bytes()
    manifest, _ = verify_bytes(raw, expected_sha256)
    return summary(manifest, raw)


def extract(archive: Path, destination: Path, expected_sha256: str) -> dict:
    require(archive.stat().st_size <= MAX_TOTAL_BYTES + MAX_MANIFEST_BYTES + 1024 * 1024,
            "Archive too large")
    raw = archive.read_bytes()
    manifest, payload = verify_bytes(raw, expected_sha256)
    # Parent must already exist; the new directory is private scratch, never the plugin tree.
    parent = destination.parent.resolve(strict=True)
    target = parent / destination.name
    target.mkdir(exist_ok=False)
    for relative, data in payload.items():
        output = target / "files" / relative
        output.parent.mkdir(parents=True, exist_ok=True)
        with output.open("xb") as handle:
            handle.write(data)
    with (target / "submission.json").open("x", encoding="utf-8") as handle:
        json.dump(manifest, handle, ensure_ascii=False, indent=2)
        handle.write("\n")
    return summary(manifest, raw)


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    commands = parser.add_subparsers(dest="command", required=True)
    create = commands.add_parser("pack")
    create.add_argument("--root", type=Path, required=True, help="Reviewed staging directory")
    create.add_argument("--spec", type=Path, required=True)
    create.add_argument("--output", type=Path, required=True)
    for action in ("inspect", "extract"):
        sub = commands.add_parser(action)
        sub.add_argument("--archive", type=Path, required=True)
        sub.add_argument("--sha256", required=action == "extract")
        if action == "extract":
            sub.add_argument("--destination", type=Path, required=True)
    args = parser.parse_args()
    try:
        if args.command == "pack":
            result = pack(args.root, args.spec, args.output)
        elif args.command == "inspect":
            result = inspect(args.archive, args.sha256)
        else:
            result = extract(args.archive, args.destination, args.sha256)
        print(json.dumps(result, ensure_ascii=True, indent=2))  # Portable Windows console receipt.
        return 0
    except (BundleError, OSError, ValueError, TypeError, KeyError, zipfile.BadZipFile,
            RuntimeError, NotImplementedError):
        # Do not echo untrusted paths/content or potentially sensitive JSON in diagnostics.
        print("Bundle operation failed. Check schema, paths, hashes, limits and destination; no upload occurred.",
              file=sys.stderr)
        return 1


if __name__ == "__main__":
    raise SystemExit(main())
