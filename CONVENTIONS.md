# KCDW Theme — Conventions

The single source of truth for "what's the right value / name / pattern here."
When in doubt, follow this file; if this file is wrong, fix it here first, then the code.

This is a **classic PHP theme** (refactored away from FSE). Design tokens live in
`theme.json`; CSS is plain files in `assets/css/` with **no build step** — edit them
directly. No inline styles. BEM-ish class names.

---

## CSS file organization

`assets/css/main.css` is the only enqueued stylesheet; it `@import`s the rest, in order:

| File | Holds |
|------|-------|
| `base.css` | `:root` variables, resets/defaults, keyframes, animation hooks, utilities (`.center`, `.visually-hidden`) |
| `components.css` | Reusable cross-page pieces: header, footer, nav, buttons, alert bar, cards, stat blocks, section utilities |
| `templates.css` | Page-specific layout: front page, single, archive, 404, and the issue/lawsuit/about templates |
| `editor.css` | Block-editor-only overrides (imports base + components) |

**Where does a new rule go?** Reusable across pages → `components.css`. Specific to one
template/page type → `templates.css`. A token, reset, keyframe, or utility → `base.css`.

`editor.css` is loaded via `add_editor_style()` — keep it importing `base.css` +
`components.css` so the editor matches the front end. _(Note: wiring this up is an open
item — see THEME-CONSISTENCY-AUDIT.md #9.)_

---

## Design tokens (theme.json is the source)

**Never** hardcode a color or font family in CSS. Reference the generated custom props:
`var(--wp--preset--color--<slug>)`, `var(--wp--preset--font-family--<slug>)`.

### Color palette (`theme.json` → `settings.color.palette`)
| Slug | Hex | Role |
|------|-----|------|
| `kcdw-midnight` | `#0B272C` | Darkest bg (hero, CTA sections, single header) |
| `kcdw-forest` | `#225656` | Secondary dark bg (footer, lawsuits, mission) |
| `kcdw-teal` | `#6BC0AE` | **Link / hover affordance**; eyebrows on dark bg |
| `kcdw-steel` | `#5D8294` | Muted meta text (dates) |
| `kcdw-sienna` | `#855523` | **Primary action / urgency / structural accent** |
| `kcdw-mist` | `#CCD2D1` | Light text on dark bg, hairline borders |
| `kcdw-body` | `#3D3D3D` | Default body text |
| `kcdw-white` | `#ffffff` | Page bg, text on dark bg |

### Color semantics (proposed — see AUDIT #13)
- **Sienna** = primary CTAs, card/section accent borders (`border-inline-start`,
  `border-block-start`), active status badges, urgency eyebrows on light bg.
- **Teal** = links and hover/focus states, eyebrows on dark bg.
- Hover convention: **interactive text → teal on hover/focus.** _(Nav and
  `.press-single__link` currently hover sienna — pending alignment, AUDIT #13.)_

### Typography
Families: `barlow-condensed` (headings/display, weight 900, uppercase),
`barlow` (body + buttons), `ibm-plex-mono` (eyebrows, meta, dates, badges).

Type scale (`theme.json` → `fontSizes`, `"fluid": false`) drives **all in-flow
headings** via `styles.elements.h1…h6`:

| Slug | Size | Element |
|------|------|---------|
| `small` | 0.875rem | small text |
| `normal` | 1rem | body |
| `large` | 1.25rem | lead |
| `h3` | 1.75rem | H3 |
| `h2` | 2.375rem | H2 |
| `h1` | 3.25rem | H1 |

**Fluid `clamp()` is reserved for full-bleed display headings only** (hero titles,
stat values, action-strip headlines) — a deliberate "this heading scales with the
viewport" treatment, not the default. In-flow section/card headings use the fixed
scale above. When you need a fluid display heading, use the standard ramp rather than
inventing a new `clamp()`:

```css
/* display ramp — min, viewport-relative, max */
.x__display { font-size: clamp(2rem, 5vw, 3.25rem); }   /* secondary hero (issue/about) */
.site-hero h1 { font-size: clamp(2.5rem, 8vw, 5rem); }  /* primary home hero only */
```

_(The codebase currently has ~7 ad-hoc clamps; consolidating onto a documented ramp is
open — AUDIT #8.)_

### Durations (`base.css`)
- `--transform-duration: 300ms` — default for links, nav, generic transitions.
- `--btn-duration: 180ms` — buttons only (intentionally snappier). Use for `.btn*`.

---

## Spacing, widths & radius (current literals — see AUDIT #11 for tokenizing)

Until tokens land, use these **canonical values** so new code matches existing code:

- **Wide content max-width:** `1080px` (`margin-inline: auto; max-inline-size: 1080px`).
- **Prose / reading column:** `720px`. _(Do not use 760px — standardize to 720.)_
- **Prose measure (`max-inline-size` in `ch`):** body `62ch`, intro/wide `72ch`,
  tight `40ch`. Prefer these three rather than new values.
- **Section padding:** `padding-block: 5rem` (major sections), `4rem` (content bands),
  `3rem` on mobile (`width < 48rem`). `padding-inline: 1.5rem`, `1rem` on mobile.
- **border-radius:** `3px` for buttons/badges/CTAs (matches `theme.json` button radius);
  `2px` for tiny chrome (toggle bars). Avatars `50%`.

---

## Breakpoints

CSS custom properties can't be used inside `@media`, so this is a **documented set**,
not a token. Use these and only these (mobile-first `max` via `width <`):

| Token | Value | Use |
|-------|-------|-----|
| sm | `36rem` (576px) | 2-up → 1-up for news/action grids |
| **md** | `48rem` (768px) | **primary mobile breakpoint**: nav collapses, hero/section padding shrinks |
| lg | `64rem` (1024px) | 4-up/3-up → 2-up grids, footer columns collapse |
| (WP) | `782px` | WordPress-fixed admin-bar height switch only — don't reuse for layout |

---

## Markup & PHP patterns

- **Namespace:** all theme PHP is `namespace KCDW\Theme;` with `declare(strict_types=1);`.
- **Text domain:** `kcdw` everywhere (matches `style.css`). Never `kcdw-theme`.
- **Escape on output, always:** `esc_html()` for text, `esc_url()` for URLs,
  `esc_attr()` for attributes. No exceptions for dynamic data.
- **Internal links:** `esc_url( home_url( '/path/' ) )`. Don't hardcode raw `href="/path/"`.
- **Document scaffolding:** every template uses `get_header();` / `get_footer();`
  (→ `header.php` / `footer.php`). **Do not** hand-roll `<!DOCTYPE>` or call the FSE
  function `block_template_part()` — there are no FSE parts in this theme.
  _(Three templates still violate this — AUDIT #6.)_
- **Slug-keyed templates:** `template-issue.php` / `template-lawsuit.php` resolve content
  from a PHP array keyed by `get_post_field('post_name')`. Add a case to the array to add
  a page; never key by post ID (IDs differ per environment).
- **Functionality belongs in plugins**, not `functions.php`. `functions.php` only wires
  up `includes/` (theme setup, enqueue, fields, CPT, shortcodes, head, cleanup).
- **DB/content changes** (menus, options, pages, logo) are **not theme code** — they go in
  `_dev-docs/*.php` as idempotent slug-based scripts. See `_dev-docs/README.md`.

### Enqueueing assets
The live enqueue is `includes/enqueue/assets.php` (`kcdw-main` style + script, no build).
Google Fonts in `includes/enqueue/google-fonts.php`. _The scaffold's `enqueue/loader/`
system was dead and has been removed — don't reintroduce a config/loader indirection._

---

## BEM-ish naming

`block__element--modifier`, lowercase, hyphen-separated. Block = the component
(`.news-card`, `.lawsuit-meta`, `.site-header`); element = a child (`__title`, `__date`);
modifier = a variant (`--center`, `--active`, `--has-image`). Page sections use
`.section` + `.section--<name>` (e.g. `.section--issues`). Shared bits live on utility
classes (`.section__eyebrow`, `.card__readmore`).

---

## Shared markup → `parts/` partials

Markup used by more than one template lives once in `parts/` and is pulled in with
classic `get_template_part()` (not FSE `block_template_part()` — there are no FSE parts
here). Naming: `get_template_part( 'parts/card', 'news' )` loads `parts/card-news.php`.

**Established partials:**

| Partial | File | Notes |
|---------|------|-------|
| News card | `parts/card-news.php` | The post card in the news loops. Call **inside a post loop** (`the_post()` must have run). Pass the title heading level via the 3rd arg: `get_template_part( 'parts/card', 'news', [ 'heading_level' => 'h3' ] )` — `h3` inside the front-page "Latest Updates" section (the section owns the `<h2>`), default `h2` on archive/index. Read in the partial as `$args['heading_level']`; whitelisted to `h2`/`h3`. Uses `post_class( 'news-card' )` so `.news-card` CSS still matches. |

Used by `front-page.php`, `archive.php`, `index.php`.

**Still inline (candidate partials — see AUDIT #10):** the front-page section header
(`.section__eyebrow` + `<h2>`) and the `issue-card` / `action-card` / `lawsuit-card`
shells. Extract them the same way when their duplication starts to bite.
