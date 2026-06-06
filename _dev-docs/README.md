# KCDW Theme — Dev Scripts

Scripts in this directory are for development and migration only.
They are not loaded by WordPress and have no effect on the front end.

---

## migrate-divi-to-blocks.php

Converts pages with Divi Visual Builder shortcode content (`[et_pb_*]`)
to valid Gutenberg block markup.

**What it does:**

1. Queries all published pages in the DB
2. Identifies pages containing Divi shortcodes
3. Strips structural wrapper shortcodes (`section`, `row`, `column`)
4. Converts content modules to blocks:
   - `et_pb_text` → `wp:paragraph` / `wp:heading` (parses inner HTML)
   - `et_pb_heading` → `wp:heading`
   - `et_pb_image` → `wp:image` (resolves attachment ID if possible)
   - `et_pb_button` → `wp:button`
   - `et_pb_cta` → heading + paragraph + button
   - `et_pb_blurb` → heading + paragraph
5. Strips all remaining shortcode tokens (third-party Divi addons, etc.)
6. Outputs a before/after preview for every page
7. With `--write`, updates all pages in the DB via `wp_update_post()`

**What it does NOT do:**

- Migrate visual styling (colors, spacing, animations) — those live in the
  new theme's CSS and theme.json
- Migrate Divi Library layouts (`et_pb_layout` CPT) — recreate as patterns
- Migrate widget areas — those are gone; rebuild in FSE template parts
- Run automatically — always requires explicit execution

---

### How to run

Always run from the WordPress root. The script path is relative to where
`wp eval-file` is called, so `cd` to the WP root first:

```bash
cd /path/to/wordpress
```

**Step 1 — Backup the database (mandatory)**

```bash
wp db export backup-pre-migration-$(date +%Y%m%d).sql
```

**Step 2 — Preview (read-only, safe to run multiple times)**

```bash
wp eval-file wp-content/themes/kcdw/_dev-docs/migrate-divi-to-blocks.php
```

Review every page's converted output in the terminal. Check:
- Headings converted at the correct level
- Paragraph text is clean (no leftover shortcode fragments)
- Images resolved to correct URLs
- Buttons have correct text and URLs
- No obviously missing content

**Step 3 — Write to DB (only after reviewing preview)**

```bash
wp eval-file wp-content/themes/kcdw/_dev-docs/migrate-divi-to-blocks.php -- --write
```

After writing, open each page in the block editor and verify it looks
correct before switching the live site to the new theme.

---

## Deployment Pipeline

**Always run scripts in this order. Never skip a stage.**

```
Local → Staging → Production
```

### 1. Local (Local by Flywheel)

```bash
# From WP root:
wp db export backup-pre-migration-$(date +%Y%m%d).sql
wp eval-file wp-content/themes/kcdw/_dev-docs/migrate-divi-to-blocks.php
# Review output, then:
wp eval-file wp-content/themes/kcdw/_dev-docs/migrate-divi-to-blocks.php -- --write
```

Open every converted page in the block editor. Fix any issues in the
script before moving to staging.

### 2. Staging

SSH into staging, then:

```bash
wp db export backup-pre-migration-$(date +%Y%m%d).sql
wp eval-file wp-content/themes/kcdw/_dev-docs/migrate-divi-to-blocks.php
# Review, then:
wp eval-file wp-content/themes/kcdw/_dev-docs/migrate-divi-to-blocks.php -- --write
```

Have a second person review the staging site before proceeding.

### 3. Production

Only after staging is clean and verified.

```bash
wp db export backup-pre-migration-$(date +%Y%m%d).sql
wp eval-file wp-content/themes/kcdw/_dev-docs/migrate-divi-to-blocks.php
# Final review, then:
wp eval-file wp-content/themes/kcdw/_dev-docs/migrate-divi-to-blocks.php -- --write
```

**Each environment has its own database.** Running on local does not affect
staging or production. Run independently on each.

**Never run on production without staging verification first.**

---

## DB Connection (Local by Flywheel)

If WP-CLI cannot connect to the DB, use the socket directly:

```bash
MYSQL="/Users/lauralong/Library/Application Support/Local/lightning-services/mysql-8.0.35+4/bin/darwin-arm64/bin/mysql"
SOCK="/Users/lauralong/Library/Application Support/Local/run/CL0Q3I0HJ/mysql/mysqld.sock"
"$MYSQL" -u root -proot -S "$SOCK" local -e "SELECT option_value FROM B2i_options WHERE option_name='siteurl';" 2>/dev/null
```

Table prefix: `B2i_`
