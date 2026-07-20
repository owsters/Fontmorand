# Fontmorand Reimagining - Design

**Date:** 2026-07-20
**Status:** Approved direction (visual mock v2 signed off in brainstorm session)
**Supersedes:** the visual layer of `2026-07-20-fontmorand-modernisation-design.md`. The code-quality work from that spec (semantic HTML, no Bootstrap/jQuery, image optimisation discipline) carries forward; the page structure, visual design and PHP runtime do not.

## Why this exists

The first modernisation pass improved the code but read as a reskin of the original site. Full-bleed heroes exposed the weakness of the legacy photography (homepage hero was 980x462 stretched across the viewport; the Creuse lead photo was 480x360). This design reimagines the site from the brief up.

## The brief

Fontmorand is a maison de maitre in Prissac, Indre, France. The family owned it for years and sold it (around 2018 - exact dates to be confirmed). The site is a **family archive made public**: a first-person memoir of a house, its photographs and its stories, kept online for a wider audience.

- **Character:** intimate, story-first. A small, beautifully typeset book about a house.
- **Voice:** first-person family ("we"), past tense, British English.
- **Photos are artefacts**, not decoration: mounted, captioned, dated, never enlarged beyond their native resolution.

## Site structure

Five chapters replace the seven estate-agent pages. Navigation reads as a table of contents.

| Page | URL | Content | Absorbs |
|---|---|---|---|
| Home | `/` | Title, lead plate, first-person introduction, drone film plate, chapter index, closing note with contact email | Home, Contact |
| The House (Chapter I) | `/house/` | The house as prose - rooms, walled garden, grounds, renovation - practical details woven into narrative, photos inline | Details |
| The Story (Chapter II) | `/story/` | Full history: the medieval font in its grove, past owners, 10 July 1944, Odette Androt, the family's own chapter | History |
| The Photographs (Chapter III) | `/photographs/` | The album: all gallery photos mounted, captioned, dated; grouped house / gardens / grounds / interior | Photos |
| The Valley (Chapter IV) | `/valley/` | Secret France: the Creuse, Lac de Chambon, Gargilesse, the Brenne; newly sourced imagery; soft regional map | Area, Find Us |

- **Contact:** no page, no form, no Formspree. An email link in the footer of every page and a short closing note on Home.
- **Find Us / interactive map:** dropped. A hand-styled static SVG regional map lives in The Valley - France's outline, the Indre highlighted, Prissac as a small oxblood point, "about 3 hours south of Paris". No street-level detail (respects current owners' privacy), no Google dependency.
- **Redirects:** every old URL maps explicitly: `/index.php` → `/`, `/pages/details.php` → `/house/`, `/pages/history.php` → `/story/`, `/pages/photos.php` → `/photographs/`, `/pages/area.php` → `/valley/`, `/pages/find-us.php` → `/valley/`, `/pages/contact.php` → `/`.

## Visual system

Approved as mock v2 (near-white monograph palette, album plate treatment). Preserved mocks: `.superpowers/brainstorm/1079-1784564977/content/blended-home-v2.html`.

### Palette (CSS variables)

| Variable | Value | Use |
|---|---|---|
| `--paper` | `#faf8f3` | Page background |
| `--ink` | `#26221c` | Body text, headlines |
| `--ink-soft` | `#4a443a` | Secondary body text |
| `--muted` | `#8a8272` | Captions, nav, footer |
| `--hairline` | `#e6e1d5` | Borders, rules |
| `--accent` | `#8c2f24` | Oxblood: chapter labels, drop caps, links, map point |
| `--mount` | `#ffffff` | Photo mounts |
| `--band` | `#f3f0e9` | Full-width section bands (chapter index etc.) |

### Typography

- Georgia serif throughout (system font, no webfont loading).
- Italic wordmark "Fontmorand" in the masthead.
- Headlines large and unhurried: ~3.2rem desktop for page titles, tight line-height.
- Small-caps letterspaced labels (`.7rem`, `.25-.35em` tracking, uppercase) in accent colour for chapter numbers and kickers.
- Body at a narrow reading measure (~620px), `line-height: 1.8`.
- Drop cap opens each chapter's first paragraph.

### Photographs as plates

- Every image sits in a white mount (`--mount`, ~16px padding) with a soft shadow, italic caption left, date right.
- **Hard rule: no image is ever rendered wider than its native pixel width.** Enforced per-image via max-width. This is the permanent fix for the blur problem.
- Small artefact images (the font 136x200, the carved head, etc.) become small mounted artefacts - intentional, not apologetic.
- Lightbox retained for full-size viewing. Tabs retired; chapter structure replaces them.

### The film

- Drone video: https://youtu.be/m1-x4JLgI70 ("Fontmorand" by Kris Daniels).
- Presented as a mounted plate with poster frame (thumbnail) and play button; clicking swaps in a `youtube-nocookie.com` iframe. Nothing loads from YouTube until clicked.
- Future enhancement (not a blocker): obtain the original file from Kris; extract high-res stills (aerial plate for The House) and possibly a short self-hosted loop.

### Interaction and motion

Restrained: plates lift a few pixels on hover, links underline, lightbox for zoom. No parallax, no scroll-triggered fades. Mobile: single column, plates near-full-width, chapter index stacks.

## Content plan

### Copy

- All copy drafted fresh in first-person family voice, past tense, British English - rewritten from existing pages plus the old French source material (`Dad/FR/Histoires.htm`), not reflowed.
- Facts only the family knows are marked `[CONFIRM: ...]` in drafts and must not ship unresolved: purchase year, sale year (~2018), drone film details, any anecdotes.
- The two WWII stories (10 July 1944; Odette Androt) keep their existing family-fact-check flag - adapted from OCR-damaged source, verify before public.

### Family photographs

- Homepage lead plate: `Dad/02 - from distance.jpg` (3733x1548, currently unused). `Dad/03 - full property.jpg` (3500x1536) also available.
- All web images re-derived from the largest available originals; never-upscale rule from the existing `scripts/optimize-images.sh` applies.
- Real dates on captions where known (most gallery photos are 2013 per file metadata).

### Sourced imagery (The Valley)

- Hunt high-res, properly licensed photos (Wikimedia Commons and other open-licence sources) for: the Creuse valley, Lac de Chambon, the Eguzon dam, Gargilesse-Dampierre, the Brenne etangs.
- Each candidate shown to Owen for approval before use.
- Licence checked per image; attributions collected in a small colophon at the foot of The Valley page.
- Retired: `region/creuse.jpg` (480x360), `region/eguzon.jpg` (700x438).

## Technical approach

**Fully static.** Plain HTML, CSS, vanilla JS. No PHP runtime, no build tools, no API keys, no forms.

```
web/
├── index.html
├── house/index.html
├── story/index.html
├── photographs/index.html
├── valley/index.html
├── css/site.css        # rebuilt on the palette variables above
├── js/lightbox.js      # kept
├── img/                # re-derived images
├── _redirects          # Cloudflare Pages redirect rules
├── .htaccess           # same redirects for the Heroku/Apache interim
├── robots.txt          # new
└── sitemap.xml         # updated to new URLs
```

- Shared nav/footer duplicated across the five pages as clearly commented blocks (five pages; duplication beats templating machinery).
- Clean URLs via directory indexes. Per-page meta descriptions. GoatCounter snippet stays (static-friendly; `YOURCODE` placeholder until signup).

### Migration path (staged, each step independently safe)

1. Build the static site on the `modernisation` branch, replacing the PHP version. PR #1 becomes the complete story. Nothing deploys until merge.
2. Heroku's `heroku-php-apache2` serves static files fine - merging works on the current host unchanged. `.htaccess` handles redirects there.
3. When ready: Cloudflare Pages project pointed at the GitHub repo (`web/` as output), DNS for `fontmorand.com` / `fontmorand.fr` moves to Cloudflare, verify, delete the Heroku app. `_redirects` takes over.

### Housekeeping this design closes

- Formspree: never needed - cancelled from the checklist.
- Google Maps: regenerate the leaked key (kills it), then delete it. Site has no Maps usage.
- robots.txt: added. Dead `.card` CSS: gone with the CSS rebuild. Hero alt-text mismatch: moot (new hero, new copy).

### Remaining go-live checklist

1. GoatCounter signup, replace `YOURCODE`.
2. Family fact-check of the WWII stories.
3. Owen resolves all `[CONFIRM]` placeholders.
4. Owen approves sourced Valley imagery.
5. Cross-browser / responsive check.
6. Merge PR; later, Cloudflare cutover (step 3 above).

## Out of scope

- French translation (deferred, as before).
- Self-hosted video loop / drone stills (pending original file from Kris).
- Any changes to the dormant `Dad/COM` and `Dad/FR` archives.

## Testing

- Local preview: any static server (`php -S 0.0.0.0:8000 -t web` still works on BigRon; no PHP semantics needed).
- Per-page checks: no image rendered above native width (scriptable: compare rendered vs natural dimensions), all internal links resolve, redirects map completely (old URL inventory above), lightbox works, YouTube facade loads nothing until clicked, HTML validates.
- Responsive check at phone / tablet / desktop widths.
