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

## 3. WordPress footer made identical to Astro (DE, FR, UK)

**Context:** WordPress serves cart/checkout/account on all three sites. Those pages rendered an Elementor theme-builder footer that had drifted from the August Astro redesign — English column headings on DE, missing links, a newsletter block the redesign removed. The pre-existing `hercules-footer-css.php` (DE and FR only, never UK) merely nudged the Elementor footer's bottom section with CSS and could never reach parity.

### Fix Applied

A **generated** mu-plugin, `hercules-custom-footer.php`, installed on all three sites. It hides the Elementor footer (`wp_head` @99) and renders the Astro footer verbatim (`wp_footer` @5).

Never hand-edit it. Regenerate with `wp-footer/gen-footer.mjs <de|fr|uk>`, which takes markup from the **deployed** Astro page, CSS from `Footer.astro`'s `<style>` block with `:global()` unwrapped, and derives the scoped overrides from that same CSS. Deploy Astro first — the generator reads the live site.

The two Astro CTA buttons are a React island that cannot run on WordPress. They become anchors opening the existing **Elementor contact popup id 5735** — the same id on all three sites. That popup sits in its own `elementor-location-popup` container, a sibling of the footer, so hiding the footer does not break it.

### Three theme collisions handled

1. Hello Elementor also owns `.site-footer`; its `theme.css` caps `.site-footer:not(.dynamic-footer)` at 500/600/800/1140px per breakpoint. That selector is specificity (0,2,0) and beat ours at (0,1,0) regardless of source order. Fixed with `footer.site-footer:not(.dynamic-footer)` = (0,2,1), no `!important`.
2. **DE only:** `accessibility-fixes.css` has `footer a{color:#0056b3!important}`, written for the Elementor footer but catching ours and turning every footer link blue. Countered with scoped `!important` rules. Contrast improves: #253461 on #E8F5FF is ~10.9:1 vs the blue's ~6.4:1, and that rule's own comment targets "4.5:1 on white" — this footer is not white.
3. Elementor's kit sets `.elementor-kit-8 button{color:#FFFFFF}` at (0,1,1), beating `.language-current` at (0,1,0) — the language pill rendered white-on-white. The first round of overrides covered anchors only, which is why this survived; they now cover **every** colour rule (21 per site). Verified no colour rule lives inside `@media`, so the unconditional `!important` freezes nothing.

### Verification

Visible footer text and the full href list must be identical between the Astro page and the WP page. Results: DE 907 chars / 30 links, FR 947 / 32, UK 859 / 29 — all byte-identical.

Deploy safety (mu-plugins auto-load, so a syntax error white-screens the site **including checkout**): upload to `~/claude-staging/`, run `php -l` on 8.3 **and** 8.4, then install with an auto-rollback guard that restores the previous file if the cart page stops returning 200. Rollback is `rm` of the file.

---

## 4. Contact-CTA spacing regression

**Context:** Hiding the social block (section 1) also removed `.follow-heading{margin-top:30px}` **and** `.social-icons{margin-bottom:25px}`, leaving the contact button flush against the phone numbers on all three sites.

### Root Cause of the first failed attempt

The initial fix used `.footer-phone + .footer-cta-popup{margin-top:25px}`. It worked on WordPress and **silently did nothing on Astro** — Astro wraps the button in `<astro-island>`, so the button is not `.footer-phone`'s adjacent sibling there. The WP port uses a bare anchor, which is.

**Lesson: "the rule is in the bundle" is not "the rule matches".** Verify selectors against the rendered DOM, not just the stylesheet.

### Fix Applied

```css
.footer-phone { margin-bottom: 25px; }
```

`.footer-column` is `display:block`, so if the social links are ever filled in this collapses with `.follow-heading`'s 30px rather than stacking to 55px. Shipped to Astro (`af059c1` DE, `9ae6d1f` FR, `cc6e1c1` UK) and regenerated into all three mu-plugins. Verified live on all six surfaces.

---

## 5. Infrastructure notes

- **SSH:** all three sites share one Plesk box, `136.144.235.35`, isolated per subscription — one account cannot reach another's vhost. Keys: DE `~/dev/hercules/id_rsa`, FR `~/dev/hercules/fr/id_rsa`, UK `~/dev/hercules/uk/id_rsa`. **`.env` and this repo's `CLAUDE.md` name the UK key `hercules_uk_merchandise`, which does not exist** — it is `hercules_id_rsa`, recovered from `~/dev/wsl-restore/home/kamindu/.ssh/`. That restored `.ssh/config` is the authoritative host to user to key mapping.
- **DE's git remote has no embedded credentials**, so `git push` blocks forever on an interactive prompt instead of failing. FR and UK carry a PAT in the remote URL.
- **`gh` is not installed**, but the GitHub REST API works with the PAT from `.env` — use it to check run status rather than guessing.
- **Check `*.pages.dev` directly** when a deploy looks stuck; it bypasses the edge router and rules out CDN cache in one request. **DE's run is by far the slowest** — two product-sync workers, then Pages, then a health-check dispatch.
- **Concurrent work:** Roy pushed SEO/CI commits to all three repos mid-session. None touched `Footer.astro`. **Nothing of his was overwritten** — verified after every push that his commit is still an ancestor of the live ref and that only `Footer.astro` differs. No `--force` was used; FR was rebased, DE and UK were parented on `origin/<branch>` via `commit-tree`.

---

## Still Open

1. **GitHub PAT in plaintext** in FR's and UK's git remote URLs — visible to anyone running `git remote -v`. Rotate and move to a credential helper. **Highest priority.**
2. **Cloudflare API token in plaintext** in this repo's `CLAUDE.md` ("Deploy Workers Manually") — rotate and move to secrets.
3. **`:Zone.Identifier` files still committed** — 4 in DE, 10 in UK. They corrupt the Windows git index and silently drop files from commits; this nearly deleted DE's `deploy.yml`. `git rm` them — this is the real fix.
4. **Stale key name** in `.env` and `CLAUDE.md` — `hercules_uk_merchandise` does not exist; it is `hercules_id_rsa`.
5. **Social profile URLs never supplied** — the "Follow us" block stays hidden until the hrefs are filled in.
6. **FR `/commande/` returns 404** — pre-existing, spotted during pre-flight, unrelated to this work.
7. **Staging divergence** — no site has the custom footer on its staging WordPress, and DE staging never had `hercules-footer-css.php` either.
