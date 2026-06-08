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
wp eval-file wp-content/themes/kcdw/_dev-docs/migrate-divi-to-blocks.php write
```

After writing, open each page in the block editor and verify it looks
correct before switching the live site to the new theme.

---

---

## seed-content.php

Populates the local database with real KCDW content — SCF option fields,
front-page hero fields, lawsuit data, issue page content, take action pages,
and coalition member info. Also creates any pages in the nav structure that
do not yet exist (Our Lawsuits, The Fight, Take Action, and their children).

**What it sets:**

- Global site options: alert bar, petition count, social URLs, next hearing
- Home page (ID 6): hero headline, body, CTAs
- About page (ID 68): coalition intro, mission statement, coalition_members repeater
- Donate page (ID 135): headline, body, fiscal sponsor note
- SB 258 page (ID 839): lawsuit fields (status, plaintiffs, court, summary, update)
- Creates + seeds: Water Rights Lawsuit, all 6 issue pages, all 4 Take Action sub-pages, In the News sub-pages

**Dry-run by default.** Logs every field and page that would be created.
Nothing is written until you pass the `write` argument.

### How to run

**Step 1 — Backup**

```bash
wp db export backup-pre-seed-$(date +%Y%m%d).sql
```

**Step 2 — Preview**

```bash
wp eval-file wp-content/themes/kcdw/_dev-docs/seed-content.php
```

Review the output. Confirm page titles, slugs, field content look correct.

**Step 3 — Write**

```bash
wp eval-file wp-content/themes/kcdw/_dev-docs/seed-content.php write
```

**This script is safe to re-run.** It checks for existing pages by slug
before creating them, and `update_field()` overwrites rather than duplicates.

---

## build-primary-menu.php

Rebuilds the menu assigned to the `primary` theme location to the canonical
KCDW nav: four section parents with dropdown children, plus About and Donate.

**Key behaviours:**

- Pages are resolved **by path/slug** (`get_page_by_path`), never by post ID,
  so the same script produces the correct menu on every environment even
  though IDs differ. Pages that can't be resolved are reported and skipped.
- **Idempotent** — clears the target menu's items and rebuilds from the spec,
  so re-running always converges to the same 20-item tree.
- If no menu is assigned to `primary`, it creates one and assigns it.

**The tree it builds:**

```
The Fight ▾      → Water Rights, Floodplain & Flood Risk, The Fake Town,
                   Affordable Housing, Cultural Resources, Meet the Developer
Our Lawsuits ▾   → Water Rights Lawsuit, SB258
Take Action ▾    → Sign the Petition, Contact Officials, Show Up, Spread the Word
In the News ▾    → Press Coverage, Newsletter Archive
About
Donate
```

### How to run

```bash
wp db export backup-pre-menu-$(date +%Y%m%d).sql                          # backup
wp eval-file wp-content/themes/kcdw/_dev-docs/build-primary-menu.php       # dry run
wp eval-file wp-content/themes/kcdw/_dev-docs/build-primary-menu.php write # apply
```

> **Note:** `_dev-docs/` is excluded from the staging rsync deploy, so this
> file does **not** arrive on the server via CI. Get it onto staging another
> way (git pull on the server, or upload), then run it there.

---

## DB Script Application Status

DB/content scripts apply per environment (the deploy ships code only). Track
where each has been run. **Staging is the only deployed environment for now.**

| Script                  | Local | Staging | Production |
| ----------------------- | ----- | ------- | ---------- |
| seed-content.php        | ✅    | ❓ verify | —          |
| build-primary-menu.php  | ✅    | ⬜ pending | —          |

Legend: ✅ applied · ⬜ not yet applied · ❓ unknown, needs verifying · — n/a

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
wp eval-file wp-content/themes/kcdw/_dev-docs/migrate-divi-to-blocks.php write
```

Open every converted page in the block editor. Fix any issues in the
script before moving to staging.

### 2. Staging

SSH into staging, then:

```bash
wp db export backup-pre-migration-$(date +%Y%m%d).sql
wp eval-file wp-content/themes/kcdw/_dev-docs/migrate-divi-to-blocks.php
# Review, then:
wp eval-file wp-content/themes/kcdw/_dev-docs/migrate-divi-to-blocks.php write
```

Have a second person review the staging site before proceeding.

### 3. Production

Only after staging is clean and verified.

```bash
wp db export backup-pre-migration-$(date +%Y%m%d).sql
wp eval-file wp-content/themes/kcdw/_dev-docs/migrate-divi-to-blocks.php
# Final review, then:
wp eval-file wp-content/themes/kcdw/_dev-docs/migrate-divi-to-blocks.php write
```

**Each environment has its own database.** Running on local does not affect
staging or production. Run independently on each.

**Never run on production without staging verification first.**

---

## Automated Deploy — GitHub Actions → Bluehost Staging

The theme deploys to Bluehost staging automatically on every push to `main`
via `.github/workflows/deploy-staging.yml`.

**How it works:**

- SSHes into Bluehost with a private key stored as a GitHub secret.
- `rsync`s the repo (which is the theme folder) into the remote theme dir:
  `/home3/zzfojqmy/public_html/staging/8258/wp-content/themes/kcdw/`
- **Additive only** — no `--delete`. Files are added/updated, never removed
  from the server.
- **Theme only** — plugins, uploads, the database, and every other server
  directory are untouched.
- Excluded from the sync: `.git/`, `.github/`, `_dev-docs/`, `AUDIT.md`,
  `CLAUDE.md`, `CHANGELOG.md`, `README.md`, `node_modules/`.

### Required GitHub secrets

Set these in the repo: **Settings → Secrets and variables → Actions → New
repository secret**.

| Secret            | Value                                              |
| ----------------- | -------------------------------------------------- |
| `SSH_PRIVATE_KEY` | Private key authorized for Bluehost SSH (full PEM, including the `-----BEGIN/END-----` lines) |
| `SSH_HOST`        | `162.241.253.174`                                  |
| `SSH_USER`        | `zzfojqmy`                                         |

**Generating the key pair (one time):**

```bash
ssh-keygen -t ed25519 -C "github-actions-kcdw-deploy" -f kcdw_deploy_key
```

- Paste the **private** key (`kcdw_deploy_key`) into the `SSH_PRIVATE_KEY` secret.
- Append the **public** key (`kcdw_deploy_key.pub`) to
  `~/.ssh/authorized_keys` on Bluehost for the `zzfojqmy` user (via cPanel
  → SSH Access, or `ssh-copy-id`).
- Delete the local copies of the key once both are placed.

Pushes to `main` then deploy automatically. Watch runs under the repo's
**Actions** tab.

---

## DB Connection (Local by Flywheel)

If WP-CLI cannot connect to the DB, use the socket directly:

```bash
MYSQL="/Users/lauralong/Library/Application Support/Local/lightning-services/mysql-8.0.35+4/bin/darwin-arm64/bin/mysql"
SOCK="/Users/lauralong/Library/Application Support/Local/run/CL0Q3I0HJ/mysql/mysqld.sock"
"$MYSQL" -u root -proot -S "$SOCK" local -e "SELECT option_value FROM B2i_options WHERE option_name='siteurl';" 2>/dev/null
```

Table prefix: `B2i_`
