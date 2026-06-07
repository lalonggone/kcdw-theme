# KCDW Theme — Claude Instructions

## Project
Custom WordPress FSE block theme for Kane Creek Development Watch (KCDW),
a grassroots advocacy organization fighting to stop the Echo Canyon luxury
resort development on the Colorado River corridor near Moab, Utah.

**Live site:** https://kanecreekwatch.org
**Local dev:** http://kanecreekwatch.local
**Repo:** https://github.com/lalonggone/kcdw-theme
**Scaffold base:** jacobcassidy/wp-starter-fse-block-theme

## Developer
Laura Long — experienced developer, comfortable with PHP, Git, and custom
WP plugins. Learning FSE/block theme architecture. No page builders. Ever.

## Stack
- WordPress FSE block theme (PHP 8.3+)
- theme.json for all design tokens
- Block templates in /templates, block parts in /parts
- Custom functionality lives in separate plugins (not this theme)
- No jQuery, no page builders, no Divi, no bloat

## Mission Context
The site serves a time-sensitive advocacy campaign with active lawsuits,
public hearings, and media attention. Content editors are non-technical
volunteers. The theme must be:
- Fast and lightweight
- Easy for non-developers to edit content via blocks
- Visually urgent and compelling — this is a fight, not a brochure
- Accessible (WCAG 2.1 AA minimum)
- Mobile-first

## Key Site Sections Needed
- Home with urgent action hero
- Issue pages (water rights, floodplain, housing, cultural sites)
- Latest updates / news
- Legal tracker (active lawsuits)
- Press / media room
- About KCDW
- Donate (integrated CTAs throughout, not just one page)
- Petition signature count display
- Take action page

## Coding Standards
- Semantic HTML
- BEM-ish CSS naming
- No inline styles
- All colors, spacing, and typography via theme.json
- Comment your block templates — other devs may touch this
- Keep functions.php lean — custom functionality goes in plugins

## Current Status
Freshly scaffolded from jacobcassidy/wp-starter-fse-block-theme. The Divi
theme is being replaced with this custom FSE theme. Content, DB, and select
plugins are being retained. Only Divi gets killed.

## Reference — Old Divi Theme
The outgoing theme lives at `../Divi/` (sibling directory to this theme).
Audit it to understand what needs to be replicated in the new theme.
Also reference the imported DB at http://kanecreekwatch.local for content
structure, pages, and navigation.

Do NOT copy Divi code directly. Extract intent, structure, and content only.

## Local Database Access

**DB prefix:** `B2i_`  
**Database:** `local`  
**User/pass:** `root` / `root`  
**Socket:** `/Users/lauralong/Library/Application Support/Local/run/CL0Q3I0HJ/mysql/mysqld.sock`  
**MySQL binary:** `/Users/lauralong/Library/Application Support/Local/lightning-services/mysql-8.0.35+4/bin/darwin-arm64/bin/mysql`

Example query (site must be running in Local by Flywheel):

```bash
MYSQL="/Users/lauralong/Library/Application Support/Local/lightning-services/mysql-8.0.35+4/bin/darwin-arm64/bin/mysql"
SOCK="/Users/lauralong/Library/Application Support/Local/run/CL0Q3I0HJ/mysql/mysqld.sock"
"$MYSQL" -u root -proot -S "$SOCK" local -e "SELECT * FROM B2i_options WHERE option_name='siteurl';" 2>/dev/null
```

## Audit Instructions (run this first)
Before writing any theme code, audit the old Divi theme and report on:

1. **Plugin inventory** — list all plugins in `../../plugins/`, what each
   does, what is necessary, what is bloated or redundant
2. **Template mapping** — what Divi templates/layouts exist, what pages
   use them, what needs to be recreated as FSE block templates
3. **Content & IA** — map all pages, their hierarchy, custom post types,
   and navigation structure from the DB
4. **Design system** — extract colors, fonts, and spacing from the Divi
   theme for recreation in theme.json
5. **Custom functionality** — anything in functions.php, custom shortcodes,
   hooks, or theme-level code that needs to move to plugins
6. **Performance issues** — heavy scripts, plugin bloat, anything obviously
   hurting load time
7. **Accessibility issues** — obvious problems in the old theme/templates
8. **Technical debt** — anything else that is a mess

Output a structured audit report before touching any theme files.
See AUDIT.md for the completed audit report.
