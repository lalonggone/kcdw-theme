# KCDW Theme — Consistency Audit

_Audit date: 2026-06-08. Theme code only (CSS, PHP, asset architecture). DB/content
(menus, pages, options, logo selection) and accessibility are out of scope — see
the task brief. Each finding is tagged **[SAFE-FIX]** (applied this pass) or
**[DECISION]** (needs Laura's call; not applied)._

The theme was refactored from an FSE block theme to classic PHP templates. Many
findings are FSE artifacts that are dead by definition under classic templates.

---

## Summary

| # | Axis | Finding | Tier |
|---|------|---------|------|
| 1 | Dead enqueue | Entire `includes/enqueue/loader/` + `init.php` + `config.php` is never required | **SAFE-FIX** |
| 2 | Dead FSE CSS | `.site-header .wp-block-navigation*` / `.wp-block-button__link` styles markup no template emits | **SAFE-FIX** |
| 3 | Dead CSS | `.grid` / `.grid-container-*` scale has zero usages | **SAFE-FIX** |
| 4 | Dead FSE CSS | `.site-hero .wp-block-cover__inner-container` selector (hero is plain `<div>` now) | **SAFE-FIX** |
| 5 | i18n | Live `'kcdw-theme'` text domain in `press-url.php` (theme domain is `kcdw`) | **SAFE-FIX** |
| 6 | Markup / FSE | 3 custom templates hand-roll `<!DOCTYPE>` + `block_template_part()` → **empty header & no footer** | ✅ **RESOLVED** |
| 7 | Asset arch | Scaffold FSE patterns + `styles/example.json` + animate system are orphaned scaffold | ✅ **RESOLVED** |
| 8 | Type scale | Heroes hand-roll `clamp()`; section/card headings use fixed theme.json — and the clamps disagree | **DECISION** |
| 9 | Editor CSS | `editor.css` exists but is **never loaded** (`add_editor_style()` only lived in the dead loader) | **DEFERRED** (Laura) |
| 10 | Markup | `news-card` extracted to `parts/card-news.php`; section header + cards still inline | 🟡 **PARTIAL** |
| 11 | Tokens | Magic numbers: content widths (1080/720/**760**), prose measure (56/62/72ch), radius (2/3/4px) | **DECISION** |
| 12 | Breakpoints | Mixed but nearly consistent once dead grid is removed (36/48/64rem + 782px) | **DECISION** (doc only) |
| 13 | Color | Hover color drifts: nav→sienna, footer/cards→teal, press-link→sienna. No stated rule | **DECISION** |

---

## [SAFE-FIX] — applied this pass

### 1. Dead enqueue loader path
`functions.php` requires **only** `includes/enqueue/assets.php` (the live, no-build
enqueue of `assets/css/main.css` + `assets/js/main.js`) and `google-fonts.php`.
The scaffold's loader system is never wired in:

- `includes/enqueue/init.php` — never `require_once`'d anywhere (`grep enqueue/init` → 0 hits outside itself)
- `includes/enqueue/config.php` — only referenced by `init.php`
- `includes/enqueue/loader/process-configs.php`
- `includes/enqueue/loader/register-assets.php`
- `includes/enqueue/loader/enqueue-assets.php`

It also points at non-existent paths (`main/main.css`, `main/editor.css`). **Deleted.**
Consequence worth noting: this loader was the *only* caller of `add_editor_style()`
→ see Decision #9 (editor styles were already not loading).

### 2. Dead FSE navigation CSS — `components.css`
`header.php` renders `wp_nav_menu()` → `.nav__menu`, and the header CTA is a `.btn`.
Nothing emits `.wp-block-navigation*` or `.site-header .wp-block-button__link`
(`grep wp-block-navigation` in markup → 0). The `.site-header .wp-block-navigation a`
… `.wp-block-button__link` block (≈ lines 292–339) is dead. **Removed.**

### 3. Dead grid scale — `components.css`
`.grid`, `.grid > *`, `.grid-container-4/3/2` and their two breakpoints (≈ lines
414–446) have zero usages anywhere (the real grids are `.issues-grid`, `.news-grid`,
`.action-grid`, `.coalition-grid`, each defined independently). **Removed.**

### 4. Dead cover selector — `templates.css`
The front-page hero is a plain `<div class="site-hero__inner">`; it never emits a
`wp-block-cover`. The grouped selector `.site-hero__inner, .site-hero
.wp-block-cover__inner-container` (lines 52–59 and 96–99) carries a dead second
selector. **Dropped the `.wp-block-cover__inner-container` half**, kept `.site-hero__inner`.

### 5. Text-domain consistency — `press-url.php`
`style.css` declares `Text Domain: kcdw`; `functions.php` uses `'kcdw'`. The only
live mismatch was `__( 'Read the original article', 'kcdw-theme' )`. **Swapped to `'kcdw'`.**
(The other `'kcdw-theme'` strings were all inside the now-deleted `config.php`.)

---

## [DECISION] — Open decisions for Laura (not applied)

### 6. ✅ RESOLVED — Three custom templates rendered an empty header and no footer
`template-issue.php`, `template-lawsuit.php`, and `template-about.php` each hand-roll
the full HTML document and call the **FSE** function `block_template_part( 'header' )`
/ `block_template_part( 'footer' )`:

```php
<header class="site-header"><?php block_template_part( 'header' ); ?></header>
...
<?php block_template_part( 'footer' ); ?>
```

There is **no `parts/` directory and no registered `wp_template_part`** in this theme,
so these calls output nothing — the issue/lawsuit/about pages currently ship a
**bare `<header>` (no logo, no nav) and no footer at all**. Every other template
(`front-page`, `single`, `page`, `archive`, `404`, `index`) uses the classic
`get_header()` / `get_footer()` against `header.php` / `footer.php`.

**Applied:** all three converted to `get_header();` / `get_footer();` (against
`header.php` / `footer.php`), deleting the hand-rolled `<!DOCTYPE>` scaffolding and the
last `block_template_part()` calls in the theme. The custom `body_class('template-*')`
strings were unused in CSS; WP still emits `page-template-*` body classes automatically.
Also fixed the raw `href` links in `template-issue.php` (action strip + `$cta_url`) to use
`esc_url( home_url( … ) )`.

### 7. ✅ RESOLVED — Orphaned FSE scaffold (patterns, style variation, animate system)
**Applied:** deleted `patterns/` (hero/box/404 scaffold + empty `.gitkeep` dirs) and
`styles/example.json`; removed the animate system — `includes/head/animate-script.php`
(inline-style injector) and its `require` in `functions.php`, `animateOnView()` in
`main.js`, and the keyframes/`.animate*`/`--f-rem` block in `base.css`; removed the
now-orphaned `.page-hero`/`.page-title` and `.box`/`.boxes-wrapper` rules from
`components.css` (the latter referenced a non-existent `--primary` color). `main.js`
keeps `mobileNavToggle()`. Original finding below for the record.


All leftover from the `cassidydc-block-theme` scaffold; none is reachable from the
classic templates:

- `patterns/synced/heros/hero--default.php` — header says _"This is a backup file
  and is not meant to be used."_ Sole user of `.page-hero`, `.page-title`, `.animate`,
  `.wp-block-cover__inner-container`.
- `patterns/boxes/box-default.php` — demo; sole user of `.box` / `.boxes-wrapper`.
- `patterns/theming/sections/404.php` — demo (real 404 is the classic `404.php`).
- `patterns/synced/sections/.gitkeep`, `patterns/theming/footers/.gitkeep`,
  `patterns/theming/headers/.gitkeep` — empty dirs.
- `styles/example.json` — scaffold style variation.
- **Animate system:** `includes/head/animate-script.php` injects an inline `<style>`
  via JS (violates the "no inline styles" standard), `animateOnView()` in `main.js`,
  and the keyframes/`.animate*` block in `base.css`. Only the dead hero pattern uses
  `.animate`.

**Recommendation:** delete the scaffold patterns + `styles/example.json`, then remove
the now-orphaned CSS (`.page-hero`, `.page-title`, `.box`, `.boxes-wrapper`, keyframes
+ `.animate*`), drop `animate-script.php` and its `require` in `functions.php`, and
strip `animateOnView()` from `main.js` (keep `mobileNavToggle()`).
_Counter-consideration:_ if you want a block-pattern library for volunteer editors
later, keep `patterns/` and re-theme it intentionally instead. Left untouched this pass
because it's a multi-file system removal.

### 8. Type scale — fluid heroes vs fixed flow headings (priority lead)
Two parallel systems:

- **theme.json** defines fixed rem presets with `"fluid": false` (H1 3.25 / H2 2.375 /
  H3 1.75rem). These drive every in-flow heading via `styles.elements.h1…h6`.
- **CSS hand-rolls `clamp()`** on big display headings only — and inconsistently:
  - `.site-hero h1` → `clamp(2.5rem, 8vw, 5rem)`
  - `.issue-hero h1` / `.about-hero h1` → `clamp(2rem, 5vw, 3.25rem)`
  - `.lawsuit-header h1` → `clamp(1.75rem, 4vw, 2.75rem)`
  - `.single-content__title` → `clamp(1.75rem, 4vw, 3rem)`
  - `.issue-stat__value` → `clamp(2.5rem, 6vw, 4rem)`; `.issue-action-strip p` → `clamp(1.25rem, 3vw, 1.75rem)`; `.about-mission blockquote p` → `clamp(1.75rem, 4vw, 2.75rem)`

So full-bleed display type scales with the viewport; section/card headings don't.

**Recommendation (heroes-only, made intentional):** keep the fixed theme.json scale for
flow content, and treat **viewport-scaled `clamp()` as a deliberate "display heading"
treatment reserved for full-bleed hero/banner contexts.** Standardize on a single
documented clamp ramp instead of seven ad-hoc ones (see CONVENTIONS.md → Type scale).
Alternative if you'd rather everything breathe: set theme.json `"fluid": true` with
per-size min/max and delete the CSS clamps. Recommend the heroes-only path — it keeps
body/section rhythm predictable.

### 9. `editor.css` is never loaded — ⏸️ DEFERRED (Laura, 2026-06-10)
`editor.css` (post-title styling for the block editor) was only loadable via
`add_editor_style()`, which existed **solely in the dead loader (Decision/Fix #1)**.
So the block editor currently gets no theme editor styles.

**Status:** parked. Laura wants to first confirm where the block editor actually
surfaces in this build — most pages are classic PHP templates that don't render
content through the editor, so editor-side fidelity may not be worth wiring. Revisit
once that's clear. **Don't delete `editor.css` in the meantime** (no cost to keeping it).

**When revisited — the two options:**
- **Wire it:** one line in the live `includes/enqueue/assets.php`:
  `add_action('after_setup_theme', fn() => add_editor_style('assets/css/editor.css'));`
  (`editor.css` already `@import`s base + components, so the editor would match the front end).
- **Delete it:** if editor theming isn't wanted, remove `editor.css` and the CONVENTIONS.md row.

### 10. Repeated markup → partials — 🟡 PARTIAL (news-card done, 2026-06-10)
- ✅ **`news-card`** extracted to **`parts/card-news.php`**, called from `front-page.php`,
  `archive.php`, and `index.php`. The only variant — title heading level (`h3` on the
  front page, `h2` on archive/index) — is passed via the 3rd `get_template_part()` arg
  (`heading_level`, whitelisted to `h2`/`h3`). The front page previously used a bare
  `class="news-card"`; the partial unifies on `post_class( 'news-card' )` (CSS still
  matches `.news-card`; adds standard WP post classes). Introduces the `parts/`
  convention — classic `get_template_part`, not FSE. See CONVENTIONS.md → "Shared markup".
- ⏳ Still inline: the **section header** (`.section__eyebrow` + `<h2>`) repeats across
  every front-page section, and `issue-card` / `action-card` / `lawsuit-card` share
  structure. Extract the same way (`parts/section-header.php`, `parts/card-*.php`) when
  the duplication starts costing more than the indirection. Left for now — the front-page
  cards differ enough (issue = h3 + readmore, action = h4 + mixed CTA, lawsuit = status
  badge + h4) that a single card partial would need more args than it'd save.

### 11. Magic numbers vs tokens
Repeated literals with no single source:
- **Content max-width:** `1080px` (×10) for wide sections; `720px` for prose — but
  prose is **`720px` in some places and `760px` in others** (`lawsuit-summary`,
  `lawsuit-update`, `press-single`, `issue-action-strip`… use 760; `page-content`,
  `single`, `issue-content`, `lawsuit-content`, `about-content` use 720). Pick one.
- **Prose measure:** `56ch` / `62ch` / `72ch` / `40ch` — no scale.
- **border-radius:** `3px` (buttons/badges, matches theme.json button radius),
  `2px` (toggle bars, lawsuit badge), `4px` (editor), `50%` (avatars).
- **Durations:** `--transform-duration: 300ms` vs `--btn-duration: 180ms` — this one is
  **intentional** (snappier buttons, documented inline). Keeping; noted in CONVENTIONS.

**Recommendation:** add a small set of `theme.json` `settings.custom` tokens
(e.g. `--wp--custom--width--wide: 1080px`, `--width--prose: 720px`, `--radius--sm: 2px`,
`--radius--md: 3px`) and a documented `ch` measure scale, then reference them in CSS.
Standardize 760 → 720 for prose. New token system → your call; recommend doing it.

### 12. Breakpoints (documentation only)
After removing the dead grid (Fix #3), the set is already nearly clean:
`36rem` (576), `48rem` (768, primary mobile), `64rem` (1024, tablet), plus the
WordPress-fixed `782px` admin-bar breakpoint. No action needed beyond codifying the
set in CONVENTIONS.md so new media queries pick from it. (CSS custom properties can't
be used inside `@media`, so this stays a documented set, not a token.)

### 13. Color semantics — when sienna, when teal?
Current hover behavior is inconsistent:
- Classic nav hover → **sienna** (`components.css:33`)
- Footer links, `.card__readmore`, `.news-card__title`, `.lawsuit-update__doc-link` → **teal**
- `.press-single__link` hover → **sienna**

**Recommendation:** codify — **sienna = primary action / urgency / structural accent
borders** (CTAs, card top-borders, status badges); **teal = link & hover affordance**
(the "interactive cool" color). Under that rule, nav and press-link hovers should move
to teal for consistency. Documented as the proposed convention in CONVENTIONS.md;
the actual hover-color change is left for your approval (one-off vs rule).

---

## Not flagged (intentional or harmless)
- `--btn-duration` vs `--transform-duration` — intentional, documented inline.
- `.center`, `.twb`, `.visually-hidden` utilities — unused but cheap; `.visually-hidden`
  is a useful a11y helper. Kept as a small utility library.
- `home_url()` relative paths in templates — these are content links to real pages,
  consistently escaped with `esc_url()`. Correct as-is (DB content is out of scope).
- All dynamic output in templates is escaped (`esc_html` / `esc_url` / `esc_attr`).
  No escaping gaps found in live templates.
