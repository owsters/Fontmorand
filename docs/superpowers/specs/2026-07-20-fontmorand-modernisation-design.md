# Fontmorand site modernisation - design

## Context

Fontmorand is a hand-written PHP/HTML site for a historic manor house in Prissac, France. It lives in this repo (`Dad/Fontmorand`) and auto-deploys to Heroku via `Procfile` (`vendor/bin/heroku-php-apache2 web/`). Confirmed live: both `fontmorand.com` and `fontmorand.fr` currently resolve to this app's content.

The repo has been dormant since 2019 (last commit `0c38ea7`, 2019-07-21). It was built on Bootstrap 3.1.1 + jQuery 1.10.2 with no shared templating - every page hand-duplicates the full `<head>` and nav markup. There are two unrelated legacy archive folders in the parent `Dad/` directory (`COM/`, `FR/`) from an older shared-hosting account (~2013, pre-Heroku) - these are **not live** and are out of scope, though `FR/Histoires.htm` may be a useful source of extra history anecdotes in a future round.

## Purpose & audience

- **Purpose:** family/heritage showcase - informational only, no commercial/for-sale framing. (Note: the live `<title>` tag still reads "...for sale in Prissac" - stale, to be fixed on every page.)
- **Audience:** wider public / search visitors, not just people who already know the house - SEO and discoverability matter.
- **Content scope:** refresh and expand existing content, not just re-skin it.
- **Language:** English only this round. French translation explicitly out of scope for now.
- **Hosting:** keep Heroku as-is. No hosting migration.

## 1. Architecture & file structure

Work in-place in `web/` (Heroku's document root, unchanged). Introduce PHP includes for shared page chrome:

```
web/
├── includes/
│   ├── head.php        (meta, title param, CSS links, favicon)
│   ├── nav.php          (nav bar, active-page highlighting)
│   └── footer.php       (footer, analytics snippet, closing scripts)
├── index.php            (was index.html)
├── pages/
│   ├── details.php
│   ├── photos.php
│   ├── history.php
│   ├── area.php
│   ├── find-us.php
│   └── contact.php       (kept, action points at new form-service endpoint)
├── css/
│   └── site.css          (new Stone Manor stylesheet)
├── js/
│   └── (vanilla lightbox, tabs, nav-toggle scripts)
├── img/ ...               (existing ext/int/grd/out/region structure retained)
└── (old/, dist/, custom/, mailer.php - deleted)
```

`.html` pages become `.php` so they can `include` the shared partials - no routing/Procfile changes needed, Heroku's PHP buildpack handles `.php` natively.

**Full framework removal:** Bootstrap 3.1.1 and jQuery 1.10.2 are removed entirely, with no replacement framework. Hand-written CSS/JS is sufficient for a 7-page site and removes any framework version to maintain. End state: the only third-party JS on the site is the Google Maps embed script and the analytics tag - everything else is hand-written vanilla JS/CSS, no build step.

## 2. Content & information architecture

Keep the existing 7-page structure. Fixes to existing gaps:

- **Title tags & meta descriptions** - rewritten on every page for the heritage-showcase framing (removing stale "for sale" wording) and for SEO.
- **`find-us.php`** - fill in the empty "From Limoges" / "From Argenton-sur-Creuse" direction tabs (markup exists today with no content).
- **`history.php`** - complete the placeholder "next history item" section rather than leaving a dead HTML comment; consider `FR/Histoires.htm` as a source for additional anecdotes, adapted into English.
- **`index.php`** - remove the dead commented-out "For Sale" real-estate block (agency logos, Rightmove Overseas links) entirely rather than leaving it commented out.
- Add `sitemap.xml` and per-page meta descriptions - currently absent.

## 3. Visual design system - "Stone Manor"

Warm heritage direction, chosen over a minimal tech aesthetic and a darker "Golden Hour" alternative, via the visual brainstorming companion.

```css
:root {
  --color-bg: #f4f1ec;        /* warm putty/stone */
  --color-text: #2e2a24;      /* near-black warm brown */
  --color-text-muted: #5b564c;
  --color-accent: #8a8377;    /* muted stone-grey accent */
  --color-border: #ded8cb;
  --color-surface: #ffffff;
  --font-heading: Georgia, 'Times New Roman', serif; /* placeholder - pick a quieter, less "trendy" serif during build */
  --font-body: -apple-system, Helvetica, Arial, sans-serif; /* placeholder - consider a more distinctive body face during build */
  --space-unit: 8px;
}
```

- **Headings:** classic serif, restrained (avoid overused AI-era faces like Fraunces/Inter/Geist - pick something quieter that fits a historic building).
- **Body/nav:** clean sans-serif, small-caps/tracked uppercase nav labels.
- **Layout:** photography-led, large hero images per page using the existing `ext/int/grd/out/region` photo categorisation; sharp square-cornered cards (no rounded "app" look) for an architectural, restrained feel.
- **Hover states:** subtle lift + border-darken on cards/thumbnails, underline on text links.
- **Mobile:** single-column stack below 768px; nav collapses to a simple vanilla-JS toggle (replacing the old jQuery offcanvas plugin).

## 4. Technical fixes & quality

- **Images:** one-time optimisation pass on all ~23MB of photos - resize to a sensible max dimension (e.g. 1600px long edge), compress JPEGs, regenerate thumbnails consistently, add `width`/`height` attributes and `loading="lazy"` on below-the-fold images. Done as a local dev-time script (e.g. ImageMagick/`sips`), not a deploy-time build step.
- **Gallery/lightbox:** replace blueimp-gallery (loaded over plain `http://`, mixed-content risk) with a small hand-rolled, dependency-free vanilla-JS lightbox - thumbnail click → full-screen overlay, prev/next, keyboard/swipe nav, Esc/click-outside to close.
- **Tabs:** history's pill-tabs and photos' dropdown category tabs currently use Bootstrap's JS tab component - replace with a small vanilla-JS toggle.
- **Contact form:** `contact.php` stays, but posts to a hosted form service (Formspree or Web3Forms) instead of `mailer.php`. `mailer.php` is deleted. Rationale: PHP's `mail()` has no MTA configured on Heroku and no mail add-on is declared, so the current form is very likely silently non-functional in production.
- **Analytics:** remove the dead Universal Analytics snippet (UA sunset mid-2024) from every page's `<head>`; add one lightweight modern analytics script (Plausible or GoatCounter) once, in `includes/footer.php`.
- **Security/cleanup:**
  - Rotate and restrict the Google Maps API key currently hardcoded in plaintext in `find-us.html` (HTTP referrer restriction in Google Cloud Console) before it goes into the new `find-us.php`.
  - Delete `web/old/` (unused Bootstrap docs-template scaffold, not site content), `web/dist/`, `web/custom/`.
  - Remove stray `Icon` / `dwsync.xml` (Dreamweaver sync metadata) files scattered through image folders.

## 5. Migration & execution plan

In-place, page by page - the site stays deployable at every step, never a half-migrated state live:

1. **Foundation** - `includes/head.php`, `nav.php`, `footer.php`; `css/site.css` (Stone Manor variables/base styles); vanilla-JS lightbox/tabs/nav-toggle scripts.
2. **Image pass** - resize/compress all photos once, regenerate thumbnails, replace in place under existing `ext/int/grd/out/region` folders.
3. **Migrate pages one at a time** onto the new includes + styling + content fixes, in this order: `index` → `details` → `photos` (gallery rewire) → `history` (tabs rewire + missing entry) → `area` → `find-us` (maps key rotation + empty direction tabs filled) → `contact` (Formspree/Web3Forms swap, `mailer.php` deleted).
4. **Cleanup** - delete `web/old/`, `web/dist/`, `web/custom/`, stray Dreamweaver/Finder artifact files.
5. **SEO pass** - title tags, meta descriptions, `sitemap.xml` across all pages.
6. Commit incrementally per step/page; push to `owsters/Fontmorand`; Heroku autodeploy verifies each stage live.

## 6. Testing & verification

No automated test suite (static-ish PHP site) - manual but structured:

- After each page migration: serve locally (`php -S localhost:8000 -t web/`) and compare against the previous version for content parity.
- Cross-browser/responsive check (mobile + desktop widths) once foundation CSS is in place.
- Contact form: submit a real test enquiry through the new form-service integration and confirm delivery.
- Google Maps: confirm the rotated/restricted API key still renders the embed correctly.
- Final pass: no remaining `web/old` or Bootstrap/jQuery references; all internal links resolve; `sitemap.xml` validates.

## Out of scope (this round)

- French translation / language switcher
- Hosting migration off Heroku
- Any booking/payment/CMS functionality (site remains static informational content)
- Deep content additions beyond filling existing gaps (empty direction tabs, missing history entry) - a full rewrite of all copy is not required

## Revision 1: visual layout (post-implementation feedback)

After the first implementation pass shipped, real feedback was: the rebuild kept the original site's structure and small-thumbnail-grid galleries almost unchanged, the homepage lost its only photo (it never had one, but a text-only hero read as a regression), and the Area page had a genuine CSS bug (prose text overlapping its photo column, because `.entry-body` had no defined width and refused to shrink next to the fixed-width image column). Section 3 above already called for "large hero images per page" - the first implementation didn't deliver on that. This revision does.

Approved via the visual brainstorming companion (mockup comparisons, not just text):

- **Homepage:** full-bleed photo hero (edge-to-edge, breaking out of the `.content` max-width column) with the title and tagline overlaid on a dark gradient at the bottom of the image, instead of the text-only `.hero` band.
- **Photos page (all 4 tabs) and Area page (both tabs):** each tab gets one full-bleed **lead photo** (the strongest/most representative shot in that category) with a small italic caption overlay, followed by the remaining photos as smaller **supporting shots** in a contained row below. This replaces the small 5-6-per-row thumbnail grid.
- **Area page specifically:** the prose text moves from beside the photos (the old `.entry` side-by-side layout that overlapped) to below the lead photo and above the supporting-shot row - a vertical stack, not a side-by-side split. This is a structural fix, not just a style tweak - there is no longer a fixed-width column for prose to collide with.
- **Details and History pages: unchanged.** Details has no imagery in the original content; History's small portrait-style story images weren't flagged as a problem. Confirmed explicitly rather than assumed.
- **Page structure/navigation: unchanged.** Still the same 7 pages - no specific problem was identified with the information architecture itself, only with how photos were presented within pages.
- **Technical approach:** a `.full-bleed` CSS utility using the standard `width: 100vw` / negative-margin (via `calc(50% - 50vw)`) break-out technique - no layout framework needed, consistent with the "no build tools" constraint.
- Lead photos display at their optimised full size (already capped at 1600px long edge by the Task 2 image pass) rather than a thumbnail, since they're now shown large; supporting shots continue to use the existing `_tn.jpg` thumbnails. Both lead and supporting shots stay inside the page's `.gallery` container so the existing lightbox's next/prev still cycles through every photo in that tab, lead photo included.
