<!-- Copyright © 2026 Foxdale Village. All rights reserved. -->

# Foxdale Village — WordPress Port

The static 2026 site (`../site/`) ported to a WordPress **block theme**, so
non-technical folks can edit every page in the visual editor. Everything the
static site has — all ten pages, the design system, the filterable floor
plans with lightbox, the scroll reveals — carries over.

## What's here

```
wordpress/
├── themes/foxdale-2026/     The block theme (install this on WordPress)
│   ├── theme.json           Brand kit: palette, fonts, 1180px content width
│   ├── functions.php        Asset loading, pattern categories
│   ├── inc/floor-plans.php  Floor Plan post type + [foxdale_floor_plans] grid
│   ├── parts/               Header & footer template parts
│   ├── templates/           Page templates (sections render full-bleed)
│   ├── patterns/            Reusable section patterns (hero, split, cards…)
│   └── assets/              Ported CSS/JS + all site imagery
├── seed/
│   ├── pages/*.html         All 10 pages converted to block markup
│   └── seed.php             One-shot importer (pages, floor plans, front page)
├── tools/html-to-blocks.py  The converter that generated seed/pages/
└── docker-compose.yml       Local dev environment
```

## Run it locally (Docker)

```bash
cd wordpress
docker compose up -d
docker compose run --rm cli core install --url=http://localhost:8080 \
  --title="Foxdale Village" --admin_user=admin --admin_password=admin \
  --admin_email=admin@example.com --skip-email
docker compose run --rm cli theme activate foxdale-2026
docker compose run --rm cli rewrite structure '/%postname%/'
docker compose run --rm cli eval-file /opt/foxdale/seed/seed.php
```

Then open <http://localhost:8080> (admin at `/wp-admin`, admin/admin).

The seeder creates the ten pages from `seed/pages/`, imports all eleven floor
plans (drawing, cottage/apartment type, specs line, virtual-tour link), and
sets Home as the front page. It's idempotent — re-running updates in place.

## Deploying to real hosting

1. Upload `themes/foxdale-2026/` to `wp-content/themes/` and activate it.
2. Set permalinks to "Post name" (Settings → Permalinks).
3. Copy `seed/` to the server and run `wp eval-file seed/seed.php`
   (or recreate the pages by hand from the patterns — every section is in
   the inserter under "Foxdale Village").

## How editors work with it

- **Pages** are stacks of sections; every heading, paragraph, image, and
  button is an editable block. New sections come from the pattern inserter
  ("Foxdale Village" category).
- **Floor plans** live under *Floor Plans* in the dashboard. Each plan is a
  post: title = plan name, featured image = plan drawing, sidebar box =
  specs line + tour URL, Plan Type = Cottage/Apartment. The Residences page
  grid, filters, and lightbox update automatically.
- **Menus** (top nav, utility bar, footer) are in the header/footer template
  parts (Appearance → Editor → Patterns → Template parts).

## Before launch

- **Tour form:** the Visit page contains a placeholder. Install WPForms or
  Gravity Forms, rebuild the form (Name, Email, Phone, "I'm interested in…"
  dropdown incl. brochure-by-mail, Message), and swap in the form block.
- **Redirects:** the old 30-page site consolidates to 10. Add 301s (e.g. the
  Redirection plugin): `/lifestyles/` → `/life/`, leadership/board/documents
  pages → `/about/`, "What's Your Next Move" article → `/healthcare/#next-move`,
  brochure request → `/visit/`, volunteer page → `/giving/#volunteer`.
- **Imagery:** page images are served from the theme's `assets/img/` so the
  seed works out of the box. Replacing an image in the editor uses the Media
  Library, which is fine — request original RGB photography from the packet
  designer before launch for best quality (current files were extracted from
  the print PDFs).

## Regenerating the seeds

If the static site in `../site/` changes:

```bash
pip install beautifulsoup4
python3 tools/html-to-blocks.py   # rewrites seed/pages/*.html
```
