# Today's Progress - 2026-08-27

---

## 1. Footer social icons hidden until real profile URLs exist (DE, UK, FR)

**Context:** The 2026-08 footer redesign shipped a "Follow us" block on all three sites, but the social profile URLs were never supplied. All four entries carried `href: ''` and the markup fell back to `href={social.href || '#'}`, so every page rendered four icons linking to a dead `#` anchor.

### Root Cause

The redesign added the social block ahead of the content. Empty hrefs were left as placeholders with a `|| '#'` fallback rather than a visibility guard, so the placeholders shipped to production on all three sites.

| Site | Where socialLinks live | Heading |
|---|---|---|
| DE | hardcoded in `Footer.astro` | `Folgen Sie uns` |
| UK | CMS singleton `src/content/keystatic/footer.json` | `followHeading` → "Follow us" |
| FR | hardcoded in `Footer.astro` | `Suivez-nous` |

### Fixes Applied

Filter to entries that actually have an href, and drop the heading **and** the icon row entirely when none remain. Reversible with no markup change — fill in the hrefs and the block returns.

```astro
const activeSocialLinks = socialLinks.filter((social) => social.href);
{activeSocialLinks.length > 0 && (<Fragment> …heading + icons… </Fragment>)}
```

| Site | Repo | Branch | Commit | Size |
|---|---|---|---|---|
| DE | `hercules-live-de` | `main` | `c908e36` | 1 file, +10 / −2 |
| UK | `hercules-live-uk` | `production` | `a1fd9f0` | 1 file, +11 / −2 |
| FR | `royboy31-hercules-live-fr` | `production` | `49abb00` | 1 file, +10 / −2 |

Every commit touches exactly one file: `src/components/Footer.astro`. The DE change already existed uncommitted from the prior session; the UK and FR changes were written today as ports of it.

### Verification

- Syntax-checked all three with `@astrojs/compiler` — all OK. **Not** a full build; `node_modules` was absent and a real build needs `npm ci` plus live API calls.
- Each commit diffed against its parent before pushing: exactly `M src/components/Footer.astro`, identical tree file counts (DE 461, UK 578, FR 426), all workflow files byte-identical by OID.
- Deploy runs **not yet confirmed green** — `gh` is not installed on the Windows machine, so status must be checked in each repo's Actions tab.

---

## 2. Windows git index corruption in the DE and UK repos

**Context:** A commit meant to touch only `Footer.astro` in DE also deleted `.github/workflows/deploy.yml`, `.gitignore`, `README.md` and `SYNC-CONFIGURATION.md` — it would have removed the auto-deploy pipeline on the very push meant to use it. Caught before pushing and reset. Reflog shows the identical thing happened unnoticed the night before (`450c42d`, 01:56, reset out 45 seconds later).

### Root Cause

Both repos have files committed with `:` in their names — `.claude-config/keys/id_ed25519_de.pub:Zone.Identifier` and siblings, **4 in DE, 10 in UK**. These are Windows alternate-data-stream artifacts from downloaded files. `:` is illegal on NTFS, so every index refresh on Windows aborts at the first such path:

```
error: invalid path '.claude-config/keys/id_ed25519_de.pub:Zone.Identifier'
fatal: make_cache_entry failed for path ...
```

leaving a **partially built index**. Symptoms: phantom `D ` staged deletions for files that exist on disk and match HEAD byte-for-byte, and a `git reset` that cannot clear them. A commit built from that index silently drops the missing files.

**`git commit -- <path>` does NOT protect against this** — the pathspec form still commits from the broken index.

### Workaround Applied

Build a clean index out-of-tree, verify, then commit via plumbing. The repo's own index is never touched:

```bash
export GIT_INDEX_FILE=/tmp/build.idx
git -c core.protectNTFS=false read-tree HEAD          # DE=461 entries, UK=578
git -c core.protectNTFS=false add <the one file>
git -c core.protectNTFS=false diff --cached --stat    # must show ONLY that file
TREE=$(git -c core.protectNTFS=false write-tree)
C=$(git -c user.name=royboy31 \
        -c user.email=113940559+royboy31@users.noreply.github.com \
        commit-tree $TREE -p HEAD -m "msg")
git diff --name-status HEAD $C                        # must list ONLY that file
git update-ref refs/heads/<branch> $C
```

FR needed none of this — 0 illegal paths, healthy index (426 = 426), ordinary `git add` / `git commit`.

### Notes

- **WSL is not installed** on the Windows machine (`wsl.exe -l` → "not installed"). It would sidestep the problem (`:` is legal on Linux) but is not an available route.
- **`scripts/deploy.sh` must not be used.** It hardcodes `cd /home/kamindu/...` (a WSL path) and runs `git add -A` — that blanket add produced the bad commits above.
- Neither UK nor FR has a local git identity configured; without `-c user.name/user.email`, commits are authored as `Kamindu@DESKTOP-QSE7QRT`.

---

## Still Open

1. **Real social profile URLs were never supplied** — the actual blocker. The block stays hidden until the hrefs are filled in (DE/FR in `Footer.astro`, UK in `src/content/keystatic/footer.json`).
2. **The `:Zone.Identifier` files are still committed** — 4 in DE, 10 in UK. Until they are `git rm`'d, both repos keep the broken index and every future commit from Windows needs the plumbing workaround. **This is the real fix.**
3. **Deploy runs not yet confirmed** — check each repo's Actions tab.
4. **`CLAUDE.md` contains a live Cloudflare API token in plaintext** (in the "Deploy Workers Manually" section) — worth rotating and moving to secrets.
