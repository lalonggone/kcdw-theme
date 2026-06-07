# KCDW Site Audit Report

---

## 1. Plugin Inventory

| Plugin | Display Name | Keep? | Notes |
|---|---|---|---|
| `wordpress-seo` | Yoast SEO 27.7 | **Keep** | SEO is essential for an advocacy site |
| `akismet` | Akismet 5.7 | **Keep** | Spam filtering for contact/petition forms |
| `jetpack` | Jetpack 15.8 | **Maybe** | Kitchen sink — use only for stats/CDN, consider dropping for performance |
| `jetpack-protect` | Jetpack Protect 5.0 | **Maybe** | Subset of Jetpack; redundant if Jetpack is kept |
| `icon-block` | The Icon Block 2.0 | **Keep** | Lightweight, useful for FSE block editing |
| `mailpoet` | MailPoet 5.28 | **Pick one** | 3 email marketing plugins installed simultaneously — pick one, remove the other two |
| `emailoctopus` | EmailOctopus 3.1 | **Pick one** | ↑ |
| `creative-mail-by-constant-contact` | Creative Mail 1.6.9 | **Drop** | Constant Contact/Newfold branding suggests Bluehost auto-install; lowest value of the three |
| `optinmonster` | OptinMonster 2.16 | **Audit** | Popup builder — evaluate if needed; adds significant JS weight |
| `google-analytics-for-wordpress` | MonsterInsights 10.2 | **Maybe** | GA can be added via Yoast or directly; MonsterInsights adds its own overhead |
| `instagram-feed` | Smash Balloon 6.11 | **Audit** | Keep only if an Instagram feed is actually on the site |
| `addons-for-divi` | Divi Torque Lite 4.3 | **Drop** | Divi-only; goes with Divi |
| `supreme-modules-for-divi` | Supreme Modules Lite 2.5 | **Drop** | Divi-only; redundant with Pro version |
| `supreme-modules-pro-for-divi` | Divi Supreme Pro 4.9 | **Drop** | Divi-only |
| `superb-blocks` | Superb Addons 4.0 | **Drop** | Generic block patterns/sliders — building a custom theme |
| `bluehost-wordpress-plugin` | Bluehost Plugin 4.17 | **Drop** | Bluehost hosting integration — not needed with new hosting stack |

**Bottom line:** Drop 6 immediately (3× Divi addons + Bluehost + Superb), consolidate to 1 email
plugin, audit OptinMonster and MonsterInsights.

---

## 2. Template Mapping

The Divi theme has **zero site-specific PHP templates**. Every template file (`page.php`,
`single.php`, `single-project.php`, `404.php`, `header.php`, `footer.php`) is unmodified Divi
framework boilerplate.

What this means for the FSE migration:

- No custom template logic needs to be ported
- All page layouts were built in the Divi Visual Builder (stored as post content in the DB, not as
  PHP files)
- The FSE equivalents to build are: `front-page.html`, `page.html`, `single.html`, `archive.html`,
  `404.html`, `search.html`, and a blank template — all already scaffolded in `kcdw`
- The Divi Library CPT (`et_pb_layout`) will be irrelevant in FSE; those layouts should be
  recreated as block patterns

---

## 3. Content & IA

### Navigation — Main Nav (5 items, in order)

1. About Us
2. News
3. Information
4. Get Involved
5. Donate

### Pages

| ID | Title | Status | Notes |
|---|---|---|---|
| 6 | Home | published | Front page |
| 68 | About Us | published | In main nav |
| 67 | Get Involved | published | In main nav |
| 135 | Donate | published | In main nav |
| 250 | Information | published | In main nav — likely issue/context content |
| 448 | News | published | In main nav |
| 810 | Latest Information | published | Not in nav — possibly redundant with News |
| 839 | SB258 | published | Not in nav — specific to Senate Bill 258 litigation |
| 643 | Testimonials | draft | Not published |
| 785 | Home | draft | Duplicate draft of front page |

### Posts (published)

- "Front Page Article of the Salt Lake Tribune" — press coverage
- "Breaking News! SB 258" — legislative update
- "Newsletter and Protest" — action/event update
- 1 untitled published post
- 2 untitled drafts

### Custom Post Types (from plugins — not theme-registered)

| CPT | Count | Source | Keep? |
|---|---|---|---|
| `emailoctopus_form` | 2 | EmailOctopus plugin | Only if keeping EmailOctopus |
| `feedback` | 3 | Jetpack (contact form responses) | Keep as records, not needed in FSE |
| `mailpoet_page` | 2 | MailPoet | Only if keeping MailPoet |
| `wpforms` | 1 (Simple Contact Form) | WPForms | Keep — site needs a contact form |

### Key observations

- The site is small: ~8 published pages, ~4 published posts. Content migration is manageable.
- `SB258` and `Latest Information` are outside the nav — need to decide if they get folded into
  News/Information or get their own nav slots in the new IA.
- WPForms is active with a contact form — keep it or replace with a simpler alternative.
- No custom post types are theme-registered; all CPTs come from plugins.

---

## 4. Design System

### Actual site color palette (from `et_global_colors` in DB)

These are the colors actively used in the Divi Visual Builder — the real brand palette:

| Role | Hex | Preview |
|---|---|---|
| Primary action / accent | `#fe4d00` | Bright orange-red |
| Burnt orange variant | `#e06100` | Deeper orange |
| Gold / amber | `#e0b535` | Warm yellow-gold |
| Gold transparent overlay | `rgba(224,186,68,0.24)` | Light gold tint |
| Forest green | `#3a6351` | Mid-range green |
| Green transparent overlay | `rgba(58,99,81,0.12)` | Light green tint |
| Dark brown | `#5a4724` | Rich earth brown |
| Dark reddish-brown | `#5c2813` | Deep canyon red |
| Warm off-white / cream | `#f2f0eb` | Light background |
| Body text | `#666666` | Medium gray |
| White | `#ffffff` | |
| Dark overlay | `rgba(0,0,0,0.12)` | Transparent dark |

**The palette is warm earth tones: canyon oranges, desert gold, sage green, and rich browns.**
This makes complete sense for a Colorado River canyon advocacy site. Use these as the basis for
`theme.json` color tokens.

### Layout & spacing (from Divi customizer)

| Setting | Value |
|---|---|
| Content width | `1080px` |
| Gutter width | 3 (Divi scale) |
| Section padding | 4% |
| Body font size | `14px` |
| Body line-height | `1.7` |
| H1 base size | `30px` |
| Button font size | `20px` |

### Typography

- **Body font:** None set (defaults to Open Sans → system sans-serif)
- **Heading font:** None set (inherits body)
- **Body color:** `#666666`
- **Heading color:** inherits body color

No custom web fonts are loaded by the theme itself. Any Open Sans usage is loaded by Divi's
Google Fonts integration. The new theme should define explicit font families in `theme.json`.

### Header & nav (from Divi customizer)

- Header style: left-aligned logo
- Sticky/fixed nav: **on**
- Nav link color: `rgba(0,0,0,0.6)`
- Active nav link: `#2ea3f2` (Divi default blue — likely should be `#fe4d00` in new theme)
- Primary nav background: `#ffffff`
- Secondary nav bar: `#2ea3f2` (Divi default — top bar with phone/email)
- Mobile nav background: `#ffffff`

### Footer (from Divi customizer)

- Background: `#222222` (dark)
- Widget text: `#ffffff`
- Widget headers: `#2ea3f2` (Divi default — update to brand color)
- Layout: 4 columns
- Social icons: Facebook + Instagram (Twitter disabled)
- Credits: "The fiscal sponsor for Kane Creek Development Watch (KCDW) is Canyonlands Watershed
  Council (CWC). CWC EIN: 87-0637713. © 2024, Kane Creek Development Watch. All rights reserved."

### Buttons (from Divi customizer)

- Style: **ghost/outline** — transparent background, white border
- Border width: `2px`, border radius: `3px`
- Text color: `#ffffff`, font size: `20px`
- Hover: `rgba(255,255,255,0.2)` background

### Logo

Stored at: `https://kanecreekwatch.org/wp-content/uploads/2024/02/kane_creek_development_watch.png`

---

## 5. Custom Functionality

**None.** Divi's `functions.php` is 9,065 lines of pure framework code — every function is
namespaced `et_divi_*` / `et_pb_*`. There are:

- No custom shortcodes
- No custom post types or taxonomies
- No site-specific hooks or filters
- No custom enqueues

The child theme (`bluehost-savemoab`) has no `functions.php` at all.

**Implication:** Nothing in the theme needs to be ported to a plugin. The new `kcdw` `functions.php`
stays lean as intended.

---

## 6. Performance Issues

### Critical

- **`style-static.min.css` = 805KB** — if Divi's Dynamic CSS is turned off, this loads on every
  page and will tank Core Web Vitals
- **`scripts.min.js` = 268KB** — Divi's front-end JS bundle, loaded on every page

### Significant

- 3 email marketing plugins registering scripts simultaneously on the front end
- Supreme Modules Lite + Pro (both active) each add JS/CSS bundles
- OptinMonster adds popup JS on every page load
- MonsterInsights adds GA inline script overhead

### FSE baseline

The new `kcdw` theme currently outputs zero front-end JS and a very small CSS payload. Every plugin
dropped is a direct performance win.

---

## 7. Accessibility Issues

Known Divi a11y problems to audit on the live site:

- Non-semantic heading hierarchy (Divi lets editors drop H1s anywhere)
- Missing `alt` text on Divi image modules (common with non-technical editors)
- Color contrast failures — Divi's default `#666666` on white fails AA for body text at small sizes
- No skip-to-content link in the default Divi header
- Divi's mobile hamburger menu often lacks proper ARIA roles and focus management
- Supreme Modules Pro sliders/carousels frequently have carousel a11y issues

**FSE plan:** Include a skip-link in `header.html` from day one, enforce heading hierarchy in
templates, and set minimum contrast in `theme.json` (the `#666666` default must go).

---

## 8. Technical Debt

| Item | Severity | Notes |
|---|---|---|
| 3 simultaneous email plugins | High | Will cause duplicate opt-in forms and double-fires on form submissions |
| Divi Supreme Lite + Pro both active | Medium | Pro supersedes Lite; Lite should have been deactivated |
| `bluehost-savemoab` child theme present but inactive | Low | Leftover Bluehost provisioning; can be deleted after reviewing its `theme.json` |
| Divi Visual Builder layouts in DB | Medium | All page content is locked in Divi shortcode format in `wp_posts.post_content`; cannot be reused in FSE — content must be manually re-entered or migrated |
| `CALUDE.md` filename typo | Low | Rename to `CLAUDE.md` |

---

## Summary: What to Do Before Writing Theme Code

- [x] Audit completed — see sections above
- [x] `CALUDE.md` renamed to `CLAUDE.md`, DB connection info added
- [ ] **Deactivate and delete:** `addons-for-divi`, `supreme-modules-for-divi`,
  `supreme-modules-pro-for-divi`, `superb-blocks`, `bluehost-wordpress-plugin`,
  `creative-mail-by-constant-contact`
- [ ] **Decide:** MailPoet or EmailOctopus (pick one, drop the other)
- [ ] **IA decision:** Determine final nav structure — does SB258 get its own nav slot? Does
  "Latest Information" merge into "News"?
- [ ] **Begin `theme.json`** — color palette, typography, spacing scale are now documented above
