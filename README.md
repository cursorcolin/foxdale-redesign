<!-- Copyright © 2026 Foxdale Village. All rights reserved. -->

# Foxdale Village — 2026 Website Refresh

A complete redesign of [foxdalevillage.org](https://www.foxdalevillage.org/), built to match the
**Marketing Packet Redesign 2026** ("Rooted in Values. Designed for Living.") and the
**Floor Plan Booklet 2026**. All photography, floor plans, copy, and brand colors come directly
from those two documents, so the site and the printed materials read as one campaign.

## Preview it

```bash
cd site
python3 -m http.server 8741
# open http://localhost:8741
```

Or just double-click `index.html` — there is no build step and no dependencies. Google Fonts
(Playfair Display + Lato) is the only external resource.

## Pages

| Page | File | Source material |
|---|---|---|
| Home | `index.html` | Packet cover, welcome spread, mission/vision/values |
| Life at Foxdale | `life.html` | "Wellness, nature, and community" spread + dining/OLLI from old Lifestyles page |
| Residences & Floor Plans | `residences.html` | Floor Plan Booklet (all 11 plans, filterable, tap to zoom) |
| Healthcare | `healthcare.html` | "Giving you peace of mind" spread + "What's Your Next Move" advice from old site |
| Our Campus | `campus.html` | 23-acre campus map spread with 8-point legend |
| Plan Your Move | `planning.html` | Contracts, amenities checklist, Priority List steps |
| Schedule a Visit | `visit.html` | Contact info + tour request form (includes brochure request option) |
| About | `about.html` | Old About/Leadership/Board/Documents pages, condensed |
| Careers | `careers.html` | Old Careers page; links to Paylocity job board |
| Giving & Volunteering | `giving.html` | Old Giving + Volunteer pages, merged |

## Migration decisions (vs. the old 30-page WordPress site)

All 30 sitemap pages of the old site were crawled. Highlights recovered in the second pass:
per-plan **YouTube virtual tours** (now on every floor plan card), the **department-specific
contact directory** (now on Schedule a Visit), richer careers benefits (10% 403(b), shift
differentials), history facts (built 1987–1990; 148 cottages, 57 apartments), two-level
memory care, direct healthcare admission for non-residents, and Priority List specifics
($1,000 deposit + $200 fee, age 62+).

**Carried over, restructured:**
- Careers, Giving, Volunteer, About/Leadership/Board, Documents — kept but consolidated
  from 8 pages into 3, reachable from the top utility bar and footer (not the main nav,
  which stays focused on prospective residents).
- Full board bios were condensed to a name/affiliation roster; full bios can live on
  click-through if desired.
- "What's Your Next Move?" article became a section on Healthcare instead of a buried page.
- Lifestyles content (dining venues, OLLI partnership, resident governance) merged into
  Life at Foxdale.
- Brochure request folded into the visit form as an interest option.

**Deliberately not ported:**
- `/residents/` portal (daily menus, resident calendars) — internal tool; linked in the
  footer, should stay on its own subsite rather than in the marketing nav.
- Events calendar — the plugin's last public event listing was stale; recommend a simple
  "Upcoming events" section only if marketing commits to maintaining it.
- Coronavirus information, `/to-come/`, `/test-lightbox/` — outdated or test pages.
- Accessibility/privacy-policy boilerplate — should be recreated as simple footer pages
  at launch.
- Additional Resources (4 external links) — low value; can live as a footer list if wanted.

## Design system (sampled from the packet)

- **Deep green** `#1e4f3a` — headings, buttons, footer
- **Sage** `#87a18b` — accent bands, checkmarks, quotes
- **Cream** `#f7f6f0` / warm paper `#fffdf8` — backgrounds
- **Playfair Display** (serif, italics for emphasis) echoes the packet's display face;
  **Lato** for body copy
- Large type, high contrast, generous spacing — deliberately readable for the audience

## Notes for the real build

- The tour form is a front-end demo. Wire it to the existing form handler
  (or a WordPress plugin like Gravity Forms / WPForms) before launch.
- If staying on WordPress: this maps cleanly onto a block theme — each section here is
  effectively one block pattern (hero, split, card grid, checklist, quote band, CTA band).
  The CSS variables at the top of `assets/css/style.css` are the whole brand kit.
- If leaving WordPress: the site is already static and could be hosted for free
  (Netlify, Cloudflare Pages, GitHub Pages) with a form service bolted on.
- Photos were extracted from the print PDFs (CMYK, converted to sRGB). For launch,
  request the original RGB photography from the packet designer for maximum quality.
- Floor plan cards are cropped straight from the Floor Plan Booklet PDF, so they update
  automatically whenever the booklet is re-exported.
