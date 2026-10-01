#!/usr/bin/env python3
"""Install the SmartFlow plugin for Claude Code, Codex, and the Cursor CLI.

Cursor loads the plugin from its marketplace on its own. Claude Code and Codex
can load the skills, agents, and MCP servers from their marketplaces too, but
neither can carry Rules in a plugin, and Codex plugins carry no agents either.
This script puts those parts into the per-user configuration of each agent,
which is exactly what T3 Code, the plain CLIs, and the IDEs read:

Claude Code
  ~/.claude/rules/<plugin>/*.md        always-on and path-scoped Rules
  marketplace + plugin install         skills, rule-* skills, agents, MCP servers

Codex
  ~/.codex/AGENTS.md                   managed block with the always-on Rules
  ~/.codex/agents/<agent>.toml         the six package agents
  ~/.codex/config.toml                 managed block: MCP servers, plugin enable,
                                       project_doc_max_bytes (Rules exceed the
                                       32 KiB default)
  marketplace + plugin install         skills incl. rule-* skills

Cursor CLI (T3 Code starts it in ACP mode, where only user-level MCP servers
are honoured)
  ~/.cursor/mcp.json                   playwright-a/b/c and mcp-atlassian when missing

Secrets never enter any file this script writes. mcp-atlassian reads
JIRA_URL, JIRA_USERNAME, JIRA_API_TOKEN, CONFLUENCE_URL, CONFLUENCE_USERNAME,
CONFLUENCE_API_TOKEN from the user environment; `migrate-secrets` moves an
existing inline configuration from ~/.cursor/mcp.json into user environment
variables (Windows `setx`; other systems get printed instructions).

Usage (from any checkout or plugin cache that contains this file):

    python install-agent-targets.py install [--targets claude,codex,cursor] [--dry-run]
    python install-agent-targets.py doctor
    python install-agent-targets.py uninstall [--targets ...] [--dry-run]
    python install-agent-targets.py migrate-secrets [--dry-run]

Options:

    --marketplace <path-or-git-url>   source for the Claude/Codex marketplaces
                                      (default: the repository checkout that
                                      contains this plugin, else the git URL
                                      from targets.json)
    --home <dir>                      alternative home directory (tests)

Every write is idempotent and marked, so rerunning after a plugin update
refreshes the managed parts and leaves everything else alone. No third-party
dependencies; Python 3.11+.
"""

from __future__ import annotations

import argparse
import datetime as dt
import json
import os
import re
import shutil
import subprocess
import sys
from pathlib import Path
from typing import Any

PLUGIN_ROOT = Path(__file__).resolve().parents[1]
TARGETS_ALL = ("claude", "codex", "cursor")
ATLASSIAN_ENV_VARS = (
    "JIRA_URL",
    "JIRA_USERNAME",
    "JIRA_API_TOKEN",
    "CONFLUENCE_URL",
    "CONFLUENCE_USERNAME",
    "CONFLUENCE_API_TOKEN",
)
CURSOR_USER_SERVERS: dict[str, dict[str, Any]] = {
    "mcp-atlassian": {"command": "uvx", "args": ["mcp-atlassian"]},
    "playwright-a": {"command": "npx", "args": ["-y", "@playwright/mcp@latest", "--isolated"]},
    "playwright-b": {"command": "npx", "args": ["-y", "@playwright/mcp@latest", "--isolated"]},
    "playwright-c": {"command": "npx", "args": ["-y", "@playwright/mcp@latest", "--isolated"]},
}
CODEX_PROJECT_DOC_MAX_BYTES = 262144


# --------------------------------------------------------------------------- #
# Context
# --------------------------------------------------------------------------- #


class Ctx:
    def __init__(self, args: argparse.Namespace) -> None:
        self.dry_run: bool = bool(args.dry_run)
        self.home = Path(args.home).expanduser() if args.home else Path.home()
        self.plugin_root = PLUGIN_ROOT
        self.plugin_name, self.plugin_version = read_identity(self.plugin_root)
        self.targets_cfg = read_json(self.plugin_root / "targets.json", default={})
        marketplace = self.targets_cfg.get("marketplace", {})
        self.marketplace_name: str = marketplace.get("name", "weseo-cursor-plugins")
        self.repo_root = find_repo_root(self.plugin_root)
        # The generated trees carry claude/rules and codex/; the Cursor plugin
        # folder does not, so from a repository checkout use the Claude tree.
        self.assets_root = self.plugin_root
        if not (self.plugin_root / "claude" / "rules").is_dir() and self.repo_root:
            self.assets_root = self.repo_root / "claude" / "plugins" / self.plugin_name
        self.marketplace_source: str = args.marketplace or (
            str(self.repo_root) if self.repo_root else marketplace.get("git", "")
        )
        self.marker = f"smartflow:{self.plugin_name}"
        self.messages: list[str] = []

    def log(self, message: str) -> None:
        prefix = "[dry-run] " if self.dry_run else ""
        print(prefix + message)

    def write_text(self, path: Path, content: str) -> None:
        self.log(f"write {path}")
        if self.dry_run:
            return
        path.parent.mkdir(parents=True, exist_ok=True)
        path.write_text(content, encoding="utf-8", newline="\n")

    def copy_file(self, src: Path, dst: Path) -> None:
        self.log(f"copy  {src.name} -> {dst}")
        if self.dry_run:
            return
        dst.parent.mkdir(parents=True, exist_ok=True)
        shutil.copyfile(src, dst)

    def remove(self, path: Path) -> None:
        if not path.exists():
            return
        self.log(f"remove {path}")
        if self.dry_run:
            return
        if path.is_dir():
            shutil.rmtree(path)
        else:
            path.unlink()

    def backup(self, path: Path) -> None:
        if not path.is_file() or self.dry_run:
            return
        stamp = dt.datetime.now().strftime("%Y%m%d-%H%M%S")
        shutil.copyfile(path, path.with_name(f"{path.name}.bak-{stamp}"))

    def run(self, argv: list[str], *, secret_args: int = 0) -> subprocess.CompletedProcess[str] | None:
        """Run a command; the last ``secret_args`` arguments are never logged."""
        shown = argv[: len(argv) - secret_args] + ["<redacted>"] * secret_args
        self.log("run   " + " ".join(quote(a) for a in shown))
        if self.dry_run:
            return None
        return subprocess.run(argv, capture_output=True, text=True, encoding="utf-8", errors="replace")


def quote(arg: str) -> str:
    return f'"{arg}"' if " " in arg else arg


def read_json(path: Path, default: Any = None) -> Any:
    if not path.is_file():
        return default
    # utf-8-sig: Cursor/PowerShell write mcp.json with a BOM.
    return json.loads(path.read_text(encoding="utf-8-sig"))


def read_identity(root: Path) -> tuple[str, str]:
    # manifest.json (copied into every generated tree) carries name and version;
    # the per-agent manifests are fallbacks.
    for candidate in (
        root / "manifest.json",
        root / ".cursor-plugin" / "plugin.json",
        root / ".claude-plugin" / "plugin.json",
        root / "plugin.json",
    ):
        data = read_json(candidate)
        if isinstance(data, dict) and data.get("name"):
            return str(data["name"]), str(data.get("version", ""))
    raise SystemExit(f"error: no plugin manifest found under {root}")


def find_repo_root(plugin_root: Path) -> Path | None:
    """The maintainer checkout, when this file runs from plugins/<name>/, claude/plugins/<name>/, or codex/plugins/<name>/."""
    for ancestor in (plugin_root.parent.parent, plugin_root.parent.parent.parent):
        if (ancestor / ".claude-plugin" / "marketplace.json").is_file() and (ancestor / ".agents" / "plugins" / "marketplace.json").is_file():
            return ancestor
    return None


def user_env_var_defined(name: str) -> bool:
    """Windows: a variable set with `setx` exists in the registry before any new process sees it."""
    if os.name != "nt":
        return False
    result = subprocess.run(
        ["reg", "query", r"HKCU\Environment", "/v", name], capture_output=True, text=True, errors="replace"
    )
    return result.returncode == 0


def which(name: str) -> str | None:
    found = shutil.which(name)
    if found:
        return found
    if name == "codex" and os.name == "nt":
        base = Path(os.environ.get("LOCALAPPDATA", "")) / "OpenAI" / "Codex" / "bin"
        for exe in sorted(base.glob("*/codex.exe"), key=lambda p: p.stat().st_mtime, reverse=True):
            return str(exe)
    return None


# --------------------------------------------------------------------------- #
# Managed text blocks
# --------------------------------------------------------------------------- #


def block_markers(marker: str, comment: str) -> tuple[str, str]:
    if comment == "html":
        return f"<!-- {marker} begin -->", f"<!-- {marker} end -->"
    return f"# >>> {marker} >>>", f"# <<< {marker} <<<"


def upsert_block(text: str, marker: str, body: str, comment: str) -> str:
    begin, end = block_markers(marker, comment)
    block = f"{begin}\n{body.rstrip()}\n{end}\n"
    pattern = re.compile(re.escape(begin) + r".*?" + re.escape(end) + r"\n?", re.DOTALL)
    if pattern.search(text):
        return pattern.sub(lambda _: block, text, count=1)
    if text and not text.endswith("\n"):
        text += "\n"
    if text:
        text += "\n"
    return text + block


def remove_block(text: str, marker: str, comment: str) -> str:
    begin, end = block_markers(marker, comment)
    pattern = re.compile(r"\n?" + re.escape(begin) + r".*?" + re.escape(end) + r"\n?", re.DOTALL)
    return pattern.sub("", text, count=1)


def has_block(text: str, marker: str, comment: str) -> bool:
    begin, _ = block_markers(marker, comment)
    return begin in text


# --------------------------------------------------------------------------- #
# Claude Code
# --------------------------------------------------------------------------- #


def claude_rules_dir(ctx: Ctx) -> Path:
    return ctx.home / ".claude" / "rules" / ctx.plugin_name


def install_claude(ctx: Ctx) -> None:
    src = ctx.assets_root / "claude" / "rules"
    if not src.is_dir():
        ctx.messages.append("claude: no claude/rules in this plugin build; run scripts/build-agent-targets.py first")
        return
    dst = claude_rules_dir(ctx)
    ctx.remove(dst)
    for rule in sorted(src.glob("*.md")):
        ctx.copy_file(rule, dst / rule.name)

    claude = which("claude")
    if not claude:
        ctx.messages.append("claude: CLI not found on PATH; rules installed, plugin not registered")
        return
    add = ctx.run([claude, "plugin", "marketplace", "add", ctx.marketplace_source, "--scope", "user"])
    if add is not None and add.returncode != 0 and "already" not in (add.stdout + add.stderr).lower():
        ctx.messages.append(f"claude: marketplace add failed: {(add.stderr or add.stdout).strip()[:300]}")
    else:
        ctx.run([claude, "plugin", "marketplace", "update", ctx.marketplace_name])
    plugin_id = f"{ctx.plugin_name}@{ctx.marketplace_name}"
    install = ctx.run([claude, "plugin", "install", plugin_id, "--scope", "user"])
    if install is not None and install.returncode != 0 and "already" not in (install.stdout + install.stderr).lower():
        ctx.messages.append(f"claude: plugin install failed: {(install.stderr or install.stdout).strip()[:300]}")
    # An installed plugin stays on its cached copy until updated; refresh every time.
    ctx.run([claude, "plugin", "update", plugin_id])


def uninstall_claude(ctx: Ctx) -> None:
    ctx.remove(claude_rules_dir(ctx))
    claude = which("claude")
    if claude:
        ctx.run([claude, "plugin", "uninstall", f"{ctx.plugin_name}@{ctx.marketplace_name}", "--scope", "user"])


# --------------------------------------------------------------------------- #
# Codex
# --------------------------------------------------------------------------- #


def codex_home(ctx: Ctx) -> Path:
    return Path(os.environ.get("CODEX_HOME") or (ctx.home / ".codex"))


def toml_has_table(text: str, table: str) -> bool:
    pattern = re.compile(r"^\s*\[\s*" + re.escape(table) + r"\s*\]", re.MULTILINE)
    quoted = re.compile(r'^\s*\[\s*mcp_servers\.\s*"' + re.escape(table.split(".", 1)[-1]) + r'"\s*\]', re.MULTILINE)
    return bool(pattern.search(text) or quoted.search(text))


def set_toml_root_int(text: str, key: str, value: int) -> str:
    """Set an integer at the TOML root (before the first table header)."""
    first_table = re.search(r"^\s*\[", text, re.MULTILINE)
    root_part = text[: first_table.start()] if first_table else text
    rest = text[first_table.start() :] if first_table else ""
    pattern = re.compile(r"^\s*" + re.escape(key) + r"\s*=\s*(\d+)\s*$", re.MULTILINE)
    match = pattern.search(root_part)
    if match:
        if int(match.group(1)) >= value:
            return text
        root_part = root_part[: match.start()] + f"{key} = {value}" + root_part[match.end() :]
    else:
        if root_part and not root_part.endswith("\n"):
            root_part += "\n"
        root_part += f"{key} = {value}\n"
        if rest:
            root_part += "\n"
    return root_part + rest


def install_codex(ctx: Ctx) -> None:
    assets = ctx.assets_root / "codex"
    if not (assets / "AGENTS.md").is_file():
        ctx.messages.append("codex: no codex/ assets in this plugin build; run scripts/build-agent-targets.py first")
        return
    home = codex_home(ctx)

    # AGENTS.md managed block with the always-on rules.
    agents_md = home / "AGENTS.md"
    current = agents_md.read_text(encoding="utf-8") if agents_md.is_file() else ""
    ctx.backup(agents_md)
    ctx.write_text(agents_md, upsert_block(current, ctx.marker, (assets / "AGENTS.md").read_text(encoding="utf-8"), "html"))

    # Custom agents.
    for toml in sorted((assets / "agents").glob("*.toml")):
        ctx.copy_file(toml, home / "agents" / toml.name)

    # config.toml: root key + managed table block.
    config = home / "config.toml"
    text = config.read_text(encoding="utf-8") if config.is_file() else ""
    ctx.backup(config)
    text = set_toml_root_int(text, "project_doc_max_bytes", CODEX_PROJECT_DOC_MAX_BYTES)
    without_block = remove_block(text, ctx.marker, "toml")
    body_lines: list[str] = [f'[plugins."{ctx.plugin_name}@{ctx.marketplace_name}"]', "enabled = true"]
    for table in split_toml_tables((assets / "mcp-servers.toml").read_text(encoding="utf-8")):
        header = table.splitlines()[0]
        name = re.sub(r"^\[\s*mcp_servers\.|\s*\]$", "", header).strip().strip('"')
        if toml_has_table(without_block, f"mcp_servers.{name}") or toml_has_table(without_block, f'mcp_servers."{name}"'):
            ctx.messages.append(f"codex: kept your existing [mcp_servers.{name}] (not managed)")
            continue
        body_lines.append("")
        body_lines.append(table.strip())
    text = upsert_block(without_block, ctx.marker, "\n".join(body_lines), "toml")
    ctx.write_text(config, text)

    codex = which("codex")
    if not codex:
        ctx.messages.append("codex: CLI not found; config written, marketplace not registered")
        return
    add = ctx.run([codex, "plugin", "marketplace", "add", ctx.marketplace_source])
    if add is not None and add.returncode != 0 and "already" not in (add.stdout + add.stderr).lower():
        ctx.messages.append(f"codex: marketplace add failed: {(add.stderr or add.stdout).strip()[:300]}")
    install = ctx.run([codex, "plugin", "add", f"{ctx.plugin_name}@{ctx.marketplace_name}"])
    if install is not None and install.returncode != 0:
        ctx.messages.append(f"codex: plugin add failed: {(install.stderr or install.stdout).strip()[:300]}")


def split_toml_tables(text: str) -> list[str]:
    tables: list[str] = []
    current: list[str] = []
    for line in text.splitlines():
        if line.startswith("["):
            if current:
                tables.append("\n".join(current))
            current = [line]
        elif current and not line.startswith("#"):
            current.append(line)
    if current:
        tables.append("\n".join(current))
    return tables


def uninstall_codex(ctx: Ctx) -> None:
    home = codex_home(ctx)
    agents_md = home / "AGENTS.md"
    if agents_md.is_file():
        ctx.backup(agents_md)
        ctx.write_text(agents_md, remove_block(agents_md.read_text(encoding="utf-8"), ctx.marker, "html"))
    for toml in sorted((ctx.assets_root / "codex" / "agents").glob("*.toml")):
        ctx.remove(home / "agents" / toml.name)
    config = home / "config.toml"
    if config.is_file():
        ctx.backup(config)
        ctx.write_text(config, remove_block(config.read_text(encoding="utf-8"), ctx.marker, "toml"))
    codex = which("codex")
    if codex:
        ctx.run([codex, "plugin", "remove", f"{ctx.plugin_name}@{ctx.marketplace_name}"])


# --------------------------------------------------------------------------- #
# Cursor CLI
# --------------------------------------------------------------------------- #


def cursor_mcp_path(ctx: Ctx) -> Path:
    return ctx.home / ".cursor" / "mcp.json"


def cursor_rules_dir(ctx: Ctx) -> Path:
    return ctx.home / ".cursor" / "rules" / ctx.plugin_name


def claude_plugin_installed(ctx: Ctx) -> bool:
    """Cursor also loads the MCP servers of installed Claude Code plugins."""
    data = read_json(ctx.home / ".claude" / "plugins" / "installed_plugins.json", default={}) or {}
    plugins = data.get("plugins", data) if isinstance(data, dict) else {}
    return any(str(key).startswith(ctx.plugin_name + "@") for key in plugins)


def install_cursor(ctx: Ctx) -> None:
    # Rules: the Cursor CLI (which T3 Code runs) applies ~/.cursor/rules/*.mdc,
    # while rules from the plugin cache are unreliable there (known Cursor bugs).
    src = ctx.plugin_root / "rules"
    dst = cursor_rules_dir(ctx)
    if src.is_dir():
        ctx.remove(dst)
        for rule in sorted(src.glob("*.mdc")):
            ctx.copy_file(rule, dst / rule.name)
    else:
        ctx.messages.append("cursor: no rules/ folder in this plugin build; rules not installed")

    # MCP: T3 Code starts cursor-agent in ACP mode, where only user-level
    # servers count. Skip when the Claude Code plugin already provides them.
    if claude_plugin_installed(ctx):
        ctx.log("cursor: MCP servers come from the installed Claude Code plugin; nothing added to mcp.json")
        return
    path = cursor_mcp_path(ctx)
    data = read_json(path, default={}) or {}
    servers = data.setdefault("mcpServers", {})
    added: list[str] = []
    for name, cfg in CURSOR_USER_SERVERS.items():
        if name in servers:
            continue
        servers[name] = dict(cfg)
        added.append(name)
    if not added:
        ctx.log(f"cursor: {path} already has all SmartFlow servers")
        return
    ctx.backup(path)
    ctx.write_text(path, json.dumps(data, indent=2, ensure_ascii=False) + "\n")
    ctx.messages.append("cursor: added " + ", ".join(added) + f" to {path}; restart Cursor / the agent session")


def uninstall_cursor(ctx: Ctx) -> None:
    ctx.remove(cursor_rules_dir(ctx))
    path = cursor_mcp_path(ctx)
    data = read_json(path, default=None)
    if not isinstance(data, dict):
        return
    servers = data.get("mcpServers", {})
    removed = [n for n in CURSOR_USER_SERVERS if n in servers and servers[n] == CURSOR_USER_SERVERS[n]]
    for name in removed:
        del servers[name]
    if removed:
        ctx.backup(path)
        ctx.write_text(path, json.dumps(data, indent=2, ensure_ascii=False) + "\n")
        ctx.messages.append("cursor: removed " + ", ".join(removed))


# --------------------------------------------------------------------------- #
# Secrets
# --------------------------------------------------------------------------- #


def migrate_secrets(ctx: Ctx) -> None:
    path = cursor_mcp_path(ctx)
    data = read_json(path, default=None)
    entry = (data or {}).get("mcpServers", {}).get("mcp-atlassian") if isinstance(data, dict) else None
    env = (entry or {}).get("env", {}) if isinstance(entry, dict) else {}
    literal = {k: v for k, v in env.items() if k in ATLASSIAN_ENV_VARS and isinstance(v, str) and not v.startswith("${")}
    if not literal:
        ctx.log("no inline Atlassian values in ~/.cursor/mcp.json; nothing to migrate")
        return
    ctx.log("moving " + ", ".join(sorted(literal)) + " into user environment variables (values are not printed)")
    if os.name == "nt":
        for key, value in literal.items():
            result = ctx.run(["setx", key, value], secret_args=1)
            if result is not None and result.returncode != 0:
                ctx.messages.append(f"setx {key} failed; ~/.cursor/mcp.json left unchanged")
                return
    else:
        ctx.messages.append(
            "add these to your shell profile, then remove the env block from ~/.cursor/mcp.json: "
            + " ".join(f"export {k}=..." for k in sorted(literal))
        )
        return
    for key in literal:
        env.pop(key, None)
    if not env:
        entry.pop("env", None)
    ctx.backup(path)
    ctx.write_text(path, json.dumps(data, indent=2, ensure_ascii=False) + "\n")
    ctx.messages.append("secrets migrated; new processes (Cursor, T3 Code, terminals) see the variables after a restart")


# --------------------------------------------------------------------------- #
# Doctor
# --------------------------------------------------------------------------- #


def doctor(ctx: Ctx) -> int:
    ok = True

    def row(status: bool | None, label: str, detail: str = "") -> None:
        nonlocal ok
        mark = "ok  " if status else ("--  " if status is None else "FAIL")
        if status is False:
            ok = False
        print(f"  {mark} {label}" + (f"  ({detail})" if detail else ""))

    print(f"SmartFlow agent targets doctor: {ctx.plugin_name} {ctx.plugin_version}")
    print(f"  plugin root: {ctx.plugin_root}")
    if ctx.assets_root != ctx.plugin_root:
        print(f"  assets:      {ctx.assets_root}")
    print(f"  marketplace: {ctx.marketplace_name} <- {ctx.marketplace_source or '(unknown)'}")

    print("Environment")
    for var in ATLASSIAN_ENV_VARS:
        if os.environ.get(var):
            row(True, var, "set")
        elif user_env_var_defined(var):
            row(True, var, "set for new processes (restart this shell / T3 Code to use it here)")
        else:
            row(False, var, "missing")
    for tool in ("claude", "codex", "cursor-agent", "npx", "uvx"):
        found = which(tool)
        row(bool(found), tool, found or "not found")

    print("Claude Code")
    rules = claude_rules_dir(ctx)
    row(rules.is_dir(), "rules", f"{len(list(rules.glob('*.md')))} files in {rules}" if rules.is_dir() else str(rules))
    claude = which("claude")
    if claude:
        listed = subprocess.run([claude, "plugin", "list"], capture_output=True, text=True, encoding="utf-8", errors="replace")
        row(ctx.plugin_name in listed.stdout, "plugin installed", f"{ctx.plugin_name}@{ctx.marketplace_name}")

    print("Codex")
    home = codex_home(ctx)
    agents_md = home / "AGENTS.md"
    row(agents_md.is_file() and has_block(agents_md.read_text(encoding="utf-8"), ctx.marker, "html"), "AGENTS.md block", str(agents_md))
    agent_files = list((home / "agents").glob("*.toml")) if (home / "agents").is_dir() else []
    expected = [p.name for p in (ctx.assets_root / "codex" / "agents").glob("*.toml")]
    present = [p.name for p in agent_files if p.name in expected]
    row(len(present) == len(expected) and bool(expected), "agents", f"{len(present)}/{len(expected)} in {home / 'agents'}")
    config = home / "config.toml"
    cfg_text = config.read_text(encoding="utf-8") if config.is_file() else ""
    row(has_block(cfg_text, ctx.marker, "toml"), "config.toml block", str(config))
    row(bool(re.search(r"^\s*project_doc_max_bytes\s*=\s*\d+", cfg_text, re.MULTILINE)), "project_doc_max_bytes")
    codex = which("codex")
    if codex:
        listed = subprocess.run([codex, "plugin", "list"], capture_output=True, text=True, encoding="utf-8", errors="replace")
        row(ctx.plugin_name in (listed.stdout + listed.stderr), "plugin listed", ctx.marketplace_name)

    print("Cursor")
    crules = cursor_rules_dir(ctx)
    row(crules.is_dir(), "rules", f"{len(list(crules.glob('*.mdc')))} files in {crules}" if crules.is_dir() else str(crules))
    mcp = read_json(cursor_mcp_path(ctx), default={}) or {}
    servers = mcp.get("mcpServers", {})
    via_claude = claude_plugin_installed(ctx)
    for name in CURSOR_USER_SERVERS:
        if via_claude and name != "mcp-atlassian":
            row(None, f"user MCP {name}", "provided by the Claude Code plugin")
        else:
            row(name in servers, f"user MCP {name}")
    atl = servers.get("mcp-atlassian", {})
    inline = [k for k, v in atl.get("env", {}).items() if k in ATLASSIAN_ENV_VARS and isinstance(v, str) and not v.startswith("${")]
    row(not inline, "no inline Atlassian secrets", ("inline: " + ", ".join(inline)) if inline else "clean")

    print("Result:", "all checks passed" if ok else "some checks failed")
    return 0 if ok else 1


# --------------------------------------------------------------------------- #
# Main
# --------------------------------------------------------------------------- #


def parse_targets(value: str) -> list[str]:
    targets = [t.strip() for t in value.split(",") if t.strip()]
    unknown = [t for t in targets if t not in TARGETS_ALL]
    if unknown:
        raise SystemExit(f"error: unknown target(s): {', '.join(unknown)}; use {', '.join(TARGETS_ALL)}")
    return targets or list(TARGETS_ALL)


def main() -> int:
    parser = argparse.ArgumentParser(description="Install the SmartFlow plugin for Claude Code, Codex, and the Cursor CLI.")
    parser.add_argument("command", choices=("install", "uninstall", "doctor", "migrate-secrets"))
    parser.add_argument("--targets", default=",".join(TARGETS_ALL), help="comma-separated: claude,codex,cursor")
    parser.add_argument("--marketplace", help="path or git URL of the plugin marketplace")
    parser.add_argument("--home", help="alternative home directory (tests)")
    parser.add_argument("--dry-run", action="store_true")
    args = parser.parse_args()
    ctx = Ctx(args)
    targets = parse_targets(args.targets)

    if args.command == "doctor":
        return doctor(ctx)
    if args.command == "migrate-secrets":
        migrate_secrets(ctx)
    else:
        actions = {
            "install": {"claude": install_claude, "codex": install_codex, "cursor": install_cursor},
            "uninstall": {"claude": uninstall_claude, "codex": uninstall_codex, "cursor": uninstall_cursor},
        }[args.command]
        for target in targets:
            print(f"== {target} ==")
            actions[target](ctx)
    if ctx.messages:
        print("Notes:")
        for message in ctx.messages:
            print(f"- {message}")
    if args.command == "install":
        print("Done. Restart agent sessions (T3 Code: 'Restart agent session') so the new config loads.")
    return 0


if __name__ == "__main__":
    sys.exit(main())
