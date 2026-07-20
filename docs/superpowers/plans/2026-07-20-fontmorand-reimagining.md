# Fontmorand Reimagining Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the PHP site in `web/` with a fully static five-chapter family memoir per `docs/superpowers/specs/2026-07-20-fontmorand-reimagining-design.md`.

**Architecture:** Five static HTML pages sharing duplicated, comment-marked masthead/footer blocks; one stylesheet built on CSS variables (monograph palette); vanilla JS for mobile nav, lightbox and a click-to-play YouTube facade. Photos render as mounted "plates" that are never wider than their native pixels. No server-side code, no build tools, no API keys.

**Tech Stack:** HTML5, CSS (variables, grid/flex), vanilla JS, `sips` for image derivation, `php -S` purely as a local static file server.

## Global Constraints

- British English; no em dashes anywhere in copy or code comments - use hyphen-minus.
- Voice: first-person family ("we"), past tense. The house was sold; never imply current ownership.
- Palette (exact values, defined once as CSS variables): `--paper #faf8f3`, `--ink #26221c`, `--ink-soft #4a443a`, `--muted #8a8272`, `--hairline #e6e1d5`, `--accent #8c2f24`, `--mount #ffffff`, `--band #f3f0e9`.
- Typography: Georgia serif only. No webfonts.
- **Never-upscale rule:** every `<img>` carries explicit `width`/`height` attributes equal to its file's native pixels, CSS clamps with `max-width:100%`, and no plate's layout width may exceed the image's native width. `scripts/check-images.sh` enforces this and must pass in every task that touches HTML.
- Facts only the family can confirm are marked with an adjacent `<!-- CONFIRM: ... -->` HTML comment. Never invent new facts; copy facts come only from the existing pages, the spec, or the session's brainstorm.
- Known facts: bought 2004, sold 2018 (fourteen years). Drone film: https://youtu.be/m1-x4JLgI70 by Kris Daniels ("Kris's drone film").
- Contact email `contact@fontmorand.fr` with a CONFIRM comment (Owen must verify the mailbox exists).
- Work directly on the `modernisation` branch. Commit after every task; never push unless a task says to.
- Local test server: `php -S 127.0.0.1:8100 -t web` (static files only; no PHP semantics used). Always kill it at the end of a step.
- Repo root: `/Users/owen/Library/Mobile Documents/com~apple~CloudDocs/Web/Owen/Dad/Fontmorand`. All paths below are relative to it. Quote all paths (spaces in the absolute prefix).

---

### Task 1: Design system - stylesheet, nav JS, film facade JS, image-rule checker

**Files:**
- Create (replace): `web/css/site.css`
- Create (replace): `web/js/site.js`
- Create: `web/js/film.js`
- Create: `scripts/check-images.sh`
- Keep untouched: `web/js/lightbox.js`

**Interfaces:**
- Produces CSS classes used by every page task: `.masthead`, `.masthead-nav`, `.nav-toggle`, `.kicker`, `.page-opening`, `.chapter-title`, `.prose`, `.dropcap`, `.plate`, `.plate-caption`, `.plate-narrow`, `.artefact`, `.artefact-row`, `.album-grid`, `.film-plate`, `.film-poster`, `.film-play`, `.band`, `.chapter-index`, `.chapter-card`, `.colophon`, `.map-figure`, `.site-footer`, plus lightbox classes matching `web/js/lightbox.js` (`.lightbox-overlay`, `.lightbox-image`, `.lightbox-caption`, `.lightbox-close`, `.lightbox-prev`, `.lightbox-next`, `.is-open`, `.gallery`, `.gallery-item`).
- Produces `js/film.js`: on `DOMContentLoaded`, every `.film-plate` gets a click handler on its `.film-play` that replaces the `.film-poster` element with a `youtube-nocookie.com` iframe (video id from `data-video` attribute on the `.film-plate`).
- Produces `scripts/check-images.sh <html-file...>`: exits non-zero listing any `<img>` whose `width` attribute exceeds the native pixel width of the file referenced by `src` (resolved relative to the HTML file), or which lacks `width`/`height` attributes.

- [ ] **Step 1: Write `web/css/site.css`** (complete replacement):

```css
/* Fontmorand - memoir stylesheet. Palette and type per the 2026-07-20 reimagining spec. */
:root {
  --paper: #faf8f3;
  --ink: #26221c;
  --ink-soft: #4a443a;
  --muted: #8a8272;
  --hairline: #e6e1d5;
  --accent: #8c2f24;
  --mount: #ffffff;
  --band: #f3f0e9;
  --measure: 620px;
  --plate-max: 820px;
}

* { box-sizing: border-box; }
html { -webkit-text-size-adjust: 100%; }
body {
  margin: 0;
  background: var(--paper);
  color: var(--ink);
  font-family: Georgia, 'Times New Roman', serif;
  font-size: 17px;
  line-height: 1.8;
}
img { max-width: 100%; height: auto; display: block; }
a { color: var(--accent); text-decoration-thickness: 1px; text-underline-offset: 3px; }
a:hover { text-decoration: underline; }

/* ---------- Masthead ---------- */
.masthead {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  gap: 1rem;
  padding: 1.4rem 2.5rem;
  border-bottom: 1px solid var(--hairline);
}
.wordmark {
  font-style: italic;
  font-size: 1.25rem;
  color: var(--ink);
  text-decoration: none;
}
.masthead-nav { display: flex; gap: 1.6rem; flex-wrap: wrap; }
.masthead-nav a {
  font-size: .72rem;
  letter-spacing: .22em;
  text-transform: uppercase;
  color: var(--muted);
  text-decoration: none;
}
.masthead-nav a:hover, .masthead-nav a.is-active { color: var(--accent); }
.nav-toggle {
  display: none;
  background: none;
  border: 1px solid var(--hairline);
  color: var(--ink);
  font: inherit;
  font-size: .72rem;
  letter-spacing: .22em;
  text-transform: uppercase;
  padding: .4rem .8rem;
  cursor: pointer;
}

/* ---------- Openings and prose ---------- */
.kicker {
  font-size: .72rem;
  letter-spacing: .35em;
  text-transform: uppercase;
  color: var(--accent);
}
.page-opening {
  max-width: 660px;
  margin: 0 auto;
  padding: 4rem 2rem 3rem;
  text-align: center;
}
.page-opening .kicker { margin-bottom: 1.2rem; }
.chapter-title {
  font-size: 3.2rem;
  line-height: 1.1;
  font-weight: normal;
  margin: 0 0 1.2rem;
}
.page-opening p { font-size: 1.08rem; color: var(--ink-soft); margin: 0; }
.prose { max-width: var(--measure); margin: 0 auto; padding: 0 2rem; }
.prose + .prose { margin-top: 1.2rem; }
.prose h2 {
  font-size: 1.6rem;
  font-weight: normal;
  margin: 3rem 0 .8rem;
}
.prose .kicker { display: block; margin: 3rem 0 .4rem; }
.prose .kicker + h2 { margin-top: 0; }
.dropcap::first-letter {
  float: left;
  font-size: 3.3rem;
  line-height: .85;
  padding: 5px 10px 0 0;
  color: var(--accent);
}

/* ---------- Plates ---------- */
.plate {
  background: var(--mount);
  padding: 16px 16px 14px;
  box-shadow: 0 8px 24px rgba(38, 34, 28, .12);
  max-width: var(--plate-max);
  margin: 2.5rem auto;
  transition: transform .2s ease, box-shadow .2s ease;
}
.plate:hover { transform: translateY(-3px); box-shadow: 0 12px 30px rgba(38, 34, 28, .16); }
.plate-wrap { padding: 0 2rem; }
.plate-narrow { max-width: 480px; }
.plate img { width: 100%; }
.plate-caption {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
  font-size: .85rem;
  color: var(--muted);
  padding-top: 10px;
}
.plate-caption em, .plate-caption .cap { font-style: italic; }
a.plate-link { text-decoration: none; color: inherit; display: block; }

/* Small historical artefacts (tiny source images shown at natural size) */
.artefact-row {
  display: flex;
  gap: 2rem;
  justify-content: center;
  flex-wrap: wrap;
  margin: 2.5rem auto;
  padding: 0 2rem;
}
.artefact { margin: 0; }
.artefact .plate { margin: 0; padding: 12px 12px 10px; }
.artefact img { width: auto; }

/* ---------- Album grid (The Photographs) ---------- */
.album-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 2rem;
  max-width: 1080px;
  margin: 2.5rem auto;
  padding: 0 2rem;
}
.album-grid .plate { margin: 0; max-width: none; }

/* ---------- Film plate ---------- */
.film-plate .film-poster { position: relative; }
.film-play {
  position: absolute;
  inset: 0;
  width: 100%;
  border: 0;
  background: transparent;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}
.film-play span {
  width: 72px;
  height: 72px;
  border-radius: 50%;
  background: rgba(38, 34, 28, .72);
  display: flex;
  align-items: center;
  justify-content: center;
  transition: transform .2s ease;
}
.film-play:hover span { transform: scale(1.06); }
.film-play span::after {
  content: '';
  border-left: 22px solid var(--paper);
  border-top: 13px solid transparent;
  border-bottom: 13px solid transparent;
  margin-left: 6px;
}
.film-plate iframe { width: 100%; aspect-ratio: 16 / 9; border: 0; display: block; }

/* ---------- Bands and chapter index ---------- */
.band {
  border-top: 1px solid var(--hairline);
  background: var(--band);
  padding: 3rem 2.5rem;
  margin-top: 3.5rem;
}
.chapter-index {
  max-width: 820px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.6rem 2.5rem;
}
.chapter-card { text-decoration: none; color: inherit; display: block; }
.chapter-card .kicker { letter-spacing: .25em; }
.chapter-card h3 { font-size: 1.35rem; font-weight: normal; margin: .3rem 0; }
.chapter-card p { font-size: .92rem; color: var(--ink-soft); line-height: 1.6; margin: 0; }
.chapter-card:hover h3 { color: var(--accent); }

/* ---------- Map and colophon ---------- */
.map-figure { max-width: 480px; margin: 2.5rem auto; padding: 0 2rem; }
.map-figure svg { width: 100%; height: auto; display: block; }
.map-figure figcaption {
  text-align: center;
  font-size: .85rem;
  font-style: italic;
  color: var(--muted);
  padding-top: 10px;
}
.colophon {
  max-width: var(--measure);
  margin: 3rem auto 0;
  padding: 1.5rem 2rem 0;
  border-top: 1px solid var(--hairline);
  font-size: .82rem;
  color: var(--muted);
  line-height: 1.7;
}

/* ---------- Footer ---------- */
.site-footer {
  text-align: center;
  padding: 2rem;
  font-size: .8rem;
  color: var(--muted);
  border-top: 1px solid var(--hairline);
  margin-top: 3.5rem;
}
.band + .site-footer { margin-top: 0; }
.site-footer a { color: var(--muted); }

/* ---------- Lightbox (markup created by js/lightbox.js) ---------- */
.lightbox-overlay {
  position: fixed;
  inset: 0;
  background: rgba(38, 34, 28, .92);
  display: none;
  align-items: center;
  justify-content: center;
  z-index: 100;
}
.lightbox-overlay.is-open { display: flex; }
.lightbox-image { max-width: 88vw; max-height: 82vh; width: auto; height: auto; }
.lightbox-caption {
  position: absolute;
  bottom: 1.2rem;
  left: 0;
  right: 0;
  text-align: center;
  color: var(--paper);
  font-style: italic;
  font-size: .95rem;
  margin: 0;
}
.lightbox-close, .lightbox-prev, .lightbox-next {
  position: absolute;
  background: none;
  border: 0;
  color: var(--paper);
  font-size: 2.4rem;
  font-family: Georgia, serif;
  cursor: pointer;
  padding: 1rem;
}
.lightbox-close { top: .5rem; right: 1rem; }
.lightbox-prev { left: .5rem; top: 50%; transform: translateY(-50%); }
.lightbox-next { right: .5rem; top: 50%; transform: translateY(-50%); }

/* ---------- Responsive ---------- */
@media (max-width: 760px) {
  .masthead { padding: 1rem 1.2rem; flex-wrap: wrap; }
  .nav-toggle { display: block; }
  .masthead-nav {
    display: none;
    width: 100%;
    flex-direction: column;
    gap: .9rem;
    padding-top: 1rem;
  }
  .masthead-nav.is-open { display: flex; }
  .chapter-title { font-size: 2.2rem; }
  .page-opening { padding: 2.5rem 1.2rem 2rem; }
  .prose, .plate-wrap, .artefact-row, .map-figure { padding-left: 1.2rem; padding-right: 1.2rem; }
  .chapter-index { grid-template-columns: 1fr; }
  .album-grid { grid-template-columns: 1fr; padding: 0 1.2rem; }
}
```

- [ ] **Step 2: Write `web/js/site.js`** (complete replacement - mobile nav only; tab code retired):

```js
document.addEventListener('DOMContentLoaded', function () {
  var toggle = document.querySelector('.nav-toggle');
  var nav = document.querySelector('.masthead-nav');
  if (!toggle || !nav) return;
  toggle.addEventListener('click', function () {
    var open = nav.classList.toggle('is-open');
    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
  });
});
```

- [ ] **Step 3: Write `web/js/film.js`**:

```js
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.film-plate').forEach(function (plate) {
    var play = plate.querySelector('.film-play');
    var poster = plate.querySelector('.film-poster');
    var id = plate.getAttribute('data-video');
    if (!play || !poster || !id) return;
    play.addEventListener('click', function () {
      var iframe = document.createElement('iframe');
      iframe.src = 'https://www.youtube-nocookie.com/embed/' + id + '?autoplay=1';
      iframe.title = 'Fontmorand drone film';
      iframe.allow = 'autoplay; encrypted-media; fullscreen';
      iframe.setAttribute('allowfullscreen', '');
      poster.replaceWith(iframe);
    });
  });
});
```

- [ ] **Step 4: Write `scripts/check-images.sh`**:

```bash
#!/bin/bash
# Enforce the never-upscale rule: every <img> in the given HTML files must have
# width/height attributes, and width must not exceed the source file's native pixels.
# Usage: scripts/check-images.sh web/index.html web/house/index.html ...
set -u
fail=0
for html in "$@"; do
  dir=$(dirname "$html")
  while IFS= read -r tag; do
    src=$(echo "$tag" | sed -n 's/.*src="\([^"]*\)".*/\1/p')
    w=$(echo "$tag" | sed -n 's/.*width="\([0-9]*\)".*/\1/p')
    h=$(echo "$tag" | sed -n 's/.*height="\([0-9]*\)".*/\1/p')
    case "$src" in http*|//*) continue ;; esac
    if [ -z "$w" ] || [ -z "$h" ]; then
      echo "FAIL $html: missing width/height on <img src=\"$src\">"; fail=1; continue
    fi
    if [ "${src#/}" != "$src" ]; then path="web${src}"; else path="$dir/$src"; fi
    if [ ! -f "$path" ]; then
      echo "FAIL $html: missing file $path"; fail=1; continue
    fi
    native=$(sips -g pixelWidth "$path" 2>/dev/null | awk '/pixelWidth/{print $2}')
    if [ -n "$native" ] && [ "$w" -gt "$native" ]; then
      echo "FAIL $html: $src width=$w exceeds native $native"; fail=1
    fi
  done < <(grep -o '<img [^>]*>' "$html")
done
[ $fail -eq 0 ] && echo "PASS: image rule holds"
exit $fail
```

- [ ] **Step 5: Verify the shell script parses and JS/CSS have no syntax errors**

Run: `bash -n scripts/check-images.sh && node --check web/js/site.js && node --check web/js/film.js && echo OK`
Expected: `OK`

- [ ] **Step 6: Commit**

```bash
chmod +x scripts/check-images.sh
git add web/css/site.css web/js/site.js web/js/film.js scripts/check-images.sh
git commit -m "feat: memoir design system - stylesheet, nav/film JS, image-rule checker"
```

---

### Task 2: Image reorganisation and derivation

**Files:**
- Move: `web/pages/img/*` → `web/img/` (subfolders `ext/ int/ out/ grd/ region/` plus `font.jpg`, `monk.jpg`, `crenau.jpg`)
- Create: `web/img/lead/distance.jpg` (from `../02 - from distance.jpg`), `web/img/lead/property.jpg` (from `../03 - full property.jpg`)
- Create: `web/img/<group>/NN_m.jpg` 800px-wide album derivatives for every gallery image
- Delete: all `*_tn.jpg` thumbnails (220px - too small for the plate treatment)

**Interfaces:**
- Produces the image tree every page task references: full images at `img/<group>/NN.jpg`, album derivatives at `img/<group>/NN_m.jpg`, leads at `img/lead/distance.jpg` (1600w) and `img/lead/property.jpg` (1600w), artefacts at `img/font.jpg` (136x200), `img/monk.jpg` (136x170), `img/crenau.jpg` (100x142).
- Produces `docs/superpowers/plans/image-manifest.txt`: one line per image, `path WxH`, used by page tasks to fill width/height attributes accurately.

- [ ] **Step 1: Move the tree and remove thumbnails**

```bash
git mv web/pages/img web/img
find web/img -name "*_tn.jpg" -print -delete
git add -A web/img
```

- [ ] **Step 2: Derive lead images (downscale only - sources are 3733w and 3500w)**

```bash
mkdir -p web/img/lead
sips -Z 1600 -s format jpeg -s formatOptions 78 "../02 - from distance.jpg" --out web/img/lead/distance.jpg
sips -Z 1600 -s format jpeg -s formatOptions 78 "../03 - full property.jpg" --out web/img/lead/property.jpg
```

- [ ] **Step 3: Generate 800w album derivatives for every gallery image** (every `NN.jpg` in ext/int/out/grd/region; all sources are wider than 800px except `region/creuse.jpg` and `region/eguzon.jpg`, which are retired in this step)

```bash
git rm web/img/region/creuse.jpg web/img/region/eguzon.jpg
for f in web/img/{ext,int,out,grd,region}/*.jpg; do
  case "$f" in *_m.jpg) continue ;; esac
  out="${f%.jpg}_m.jpg"
  sips -Z 800 -s format jpeg -s formatOptions 75 "$f" --out "$out"
done
```

- [ ] **Step 4: Build the dimension manifest**

```bash
> docs/superpowers/plans/image-manifest.txt
find web/img -name "*.jpg" -o -name "*.png" | sort | while read -r f; do
  w=$(sips -g pixelWidth "$f" | awk '/pixelWidth/{print $2}')
  h=$(sips -g pixelHeight "$f" | awk '/pixelHeight/{print $2}')
  echo "$f ${w}x${h}" >> docs/superpowers/plans/image-manifest.txt
done
cat docs/superpowers/plans/image-manifest.txt
```

Expected: every listed file has both dimensions; `lead/distance.jpg` reports `1600x663` (or within a pixel of proportional), no `_tn` files remain, no `creuse.jpg`/`eguzon.jpg` under region.

- [ ] **Step 5: Verify no upscaling occurred** (derivative never larger than source)

```bash
bad=0
for f in web/img/{ext,int,out,grd,region}/*_m.jpg; do
  src="${f%_m.jpg}.jpg"
  sw=$(sips -g pixelWidth "$src" | awk '/pixelWidth/{print $2}')
  dw=$(sips -g pixelWidth "$f" | awk '/pixelWidth/{print $2}')
  [ "$dw" -gt "$sw" ] && { echo "UPSCALED: $f"; bad=1; }
done
[ $bad -eq 0 ] && echo "PASS: no upscales"
```

Expected: `PASS: no upscales`

- [ ] **Step 6: Commit**

```bash
git add -A web/img docs/superpowers/plans/image-manifest.txt
git commit -m "feat: reorganise images under web/img, derive leads and album sizes, retire sub-web thumbnails"
```

---

### Task 3: Home page

**Files:**
- Create: `web/index.html`
- Reference: `docs/superpowers/plans/image-manifest.txt` for exact width/height values (the values shown below are correct if the manifest agrees; trust the manifest).

**Interfaces:**
- Consumes: all Task 1 CSS classes and `js/site.js`, `js/film.js`; Task 2 image tree.
- Produces: the masthead and footer blocks that Tasks 4-6 and 8 duplicate verbatim (adjusting relative paths and the `is-active` link).

- [ ] **Step 1: Write `web/index.html`** (complete file; `width`/`height` for `img/lead/distance.jpg` must match the manifest - expected 1600x663):

```html
<!DOCTYPE html>
<html lang="en-GB">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Fontmorand - a house we loved, and the stories it kept</title>
  <meta name="description" content="Fontmorand was our family's maison de maitre in Prissac, central France. Its photographs and stories - a medieval font, a wartime night in 1944, and fourteen years of our own - gathered in one place.">
  <link rel="icon" href="img/favicon.png">
  <link rel="stylesheet" href="css/site.css">
</head>
<body>

<!-- MASTHEAD - duplicated on every page; keep in sync (paths and is-active differ per page) -->
<header class="masthead">
  <a class="wordmark" href="./">Fontmorand</a>
  <button class="nav-toggle" aria-expanded="false" aria-controls="site-nav">Menu</button>
  <nav class="masthead-nav" id="site-nav">
    <a href="house/">The House</a>
    <a href="story/">The Story</a>
    <a href="photographs/">The Photographs</a>
    <a href="valley/">The Valley</a>
  </nav>
</header>

<main>
  <div class="page-opening">
    <p class="kicker">Prissac &middot; Indre &middot; France</p>
    <h1 class="chapter-title">A house we loved,<br>and the stories it kept</h1>
    <p>Fontmorand was our family's maison de ma&icirc;tre in the quiet heart of France. We have gathered its photographs and its stories here - the medieval font hidden in its own grove, the night in 1944 that spared the village, and fourteen years of our own.</p>
  </div>

  <div class="plate-wrap">
    <figure class="plate" style="margin-top:0;">
      <img src="img/lead/distance.jpg" alt="Fontmorand seen from across the fields, its pale facade against winter trees" width="1600" height="663">
      <figcaption class="plate-caption"><span class="cap">Fontmorand from across the fields</span><span>winter 2013</span></figcaption>
      <!-- CONFIRM: photo date - file dated December 2013; was this taken that winter? -->
    </figure>
  </div>

  <div class="prose" style="padding-top:1.5rem;padding-bottom:.5rem;">
    <p class="dropcap">We bought Fontmorand in 2004 and sold it, fourteen years later, with real reluctance. In between, it taught us about lime mortar and long lunches, about the Brenne's thousand lakes, and about how a house can hold a century of other people's memories alongside your own.</p>
    <!-- CONFIRM: sale year 2018 - Owen to verify -->
  </div>

  <div class="plate-wrap">
    <p class="kicker" style="display:block;text-align:center;margin:2rem 0 -1.5rem;">The house from the air</p>
    <figure class="plate film-plate" data-video="m1-x4JLgI70">
      <div class="film-poster">
        <img src="https://i.ytimg.com/vi/m1-x4JLgI70/hqdefault.jpg" alt="Still from the drone film: Fontmorand and its grounds from above" width="480" height="360">
        <button class="film-play" aria-label="Play the drone film"><span></span></button>
      </div>
      <figcaption class="plate-caption"><span class="cap">Kris's drone film - the house and grounds from above</span><span>watch</span></figcaption>
    </figure>
  </div>

  <section class="band">
    <div class="chapter-index">
      <a class="chapter-card" href="house/">
        <span class="kicker">Chapter I</span>
        <h3>The House</h3>
        <p>Three storeys built from the remains of a ch&acirc;teau, two 16th-century barns, and 3.5 hectares of the Val de l'Abloux.</p>
      </a>
      <a class="chapter-card" href="story/">
        <span class="kicker">Chapter II</span>
        <h3>The Story</h3>
        <p>A medieval font, a saint from Worms, a wartime night, and the people who came before us.</p>
      </a>
      <a class="chapter-card" href="photographs/">
        <span class="kicker">Chapter III</span>
        <h3>The Photographs</h3>
        <p>The album - the house, its rooms, its out-buildings and its grounds, captioned and dated.</p>
      </a>
      <a class="chapter-card" href="valley/">
        <span class="kicker">Chapter IV</span>
        <h3>The Valley</h3>
        <p>Secret France - the Creuse, the Brenne's lakes, and why we never wanted the south.</p>
      </a>
    </div>
  </section>

  <div class="prose" style="padding-top:2.5rem;text-align:center;">
    <p>Fontmorand has new owners now, and we wish them every happiness there. If you have a question about the house, its history, or the photographs here, do write to us.</p>
    <!-- CONFIRM: contact@fontmorand.fr mailbox exists and is monitored -->
    <p><a href="mailto:contact@fontmorand.fr">contact@fontmorand.fr</a></p>
  </div>
</main>

<!-- FOOTER - duplicated on every page; keep in sync -->
<footer class="site-footer">
  <p>Fontmorand &middot; Prissac, Indre &middot; kept online by the family who loved it &middot; <a href="mailto:contact@fontmorand.fr">write to us</a></p>
</footer>

<script src="js/site.js"></script>
<script src="js/film.js"></script>
<!-- GoatCounter analytics - replace YOURCODE after signup -->
<script data-goatcounter="https://YOURCODE.goatcounter.com/count" async src="//gc.zgo.at/count.js"></script>
</body>
</html>
```

Note: the YouTube poster `<img>` is remote (`http*` src), so `check-images.sh` skips it by design.

- [ ] **Step 2: Verify against the manifest, image rule, and server**

```bash
grep "lead/distance.jpg" docs/superpowers/plans/image-manifest.txt
bash scripts/check-images.sh web/index.html
php -S 127.0.0.1:8100 -t web >/dev/null 2>&1 & SP=$!; sleep 1
curl -s http://127.0.0.1:8100/ | grep -c "chapter-card"
curl -s http://127.0.0.1:8100/ | grep -q "youtube" && echo "FAIL: youtube iframe present before click" || echo "PASS: facade only"
kill $SP
```

Expected: manifest line matches the width/height used; `PASS: image rule holds`; `4` chapter cards; `PASS: facade only` (the only YouTube reference is the poster thumbnail and data-video attribute - the grep targets `youtube-nocookie`, adjust to `grep -q "youtube-nocookie"`).

- [ ] **Step 3: Commit**

```bash
git add web/index.html
git commit -m "feat: home page - memoir opening, lead plate, film plate, chapter index"
```

---

### Task 4: Chapter I - The House

**Files:**
- Create: `web/house/index.html`
- Reference: `docs/superpowers/plans/image-manifest.txt` for every width/height below (expected: `lead/property.jpg` 1600x702, `int/05.jpg` 2048x1536, `int/04.jpg` 2048x1536, `out/06.jpg` 2048x1536, `out/04.jpg` 2841x1980, `grd/01.jpg` 2048x1362 - trust the manifest if it differs).

**Interfaces:**
- Consumes: Task 1 classes, Task 2 images, Task 3's masthead/footer pattern (repeated below with `../` paths).

- [ ] **Step 1: Write `web/house/index.html`** (complete file):

```html
<!DOCTYPE html>
<html lang="en-GB">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>The House - Fontmorand</title>
  <meta name="description" content="Chapter I: the house itself - three storeys built from the remains of an 11th-century chateau, two 16th-century barns, a gatekeeper's cottage and 3.5 hectares of the Val de l'Abloux.">
  <link rel="icon" href="../img/favicon.png">
  <link rel="stylesheet" href="../css/site.css">
</head>
<body>

<!-- MASTHEAD - duplicated on every page; keep in sync (paths and is-active differ per page) -->
<header class="masthead">
  <a class="wordmark" href="../">Fontmorand</a>
  <button class="nav-toggle" aria-expanded="false" aria-controls="site-nav">Menu</button>
  <nav class="masthead-nav" id="site-nav">
    <a href="./" class="is-active">The House</a>
    <a href="../story/">The Story</a>
    <a href="../photographs/">The Photographs</a>
    <a href="../valley/">The Valley</a>
  </nav>
</header>

<main>
  <div class="page-opening">
    <p class="kicker">Chapter I</p>
    <h1 class="chapter-title">The House</h1>
    <p>A maison de ma&icirc;tre at the foot of the limestone cliff of Prissac, built from the stones of a far older castle.</p>
  </div>

  <div class="prose">
    <p class="dropcap">Fontmorand is a small hamlet in the Val de l'Abloux, half an hour south of Ch&acirc;teauroux and an hour north of Limoges. The house we came to own stands where Ch&acirc;teau Fontmorand once stood - an 11th-century castle with a moat and drawbridge, sold to the French Nation in 1792 by the Marquis de Villemort. By 1850 little of it remained but a few walls and the north-western tower, and the present house was raised from its stones. From the front rooms you looked straight down the Prairie de Fontmorand, and the view had not much changed in two hundred years.</p>
  </div>

  <div class="plate-wrap">
    <figure class="plate">
      <img src="../img/lead/property.jpg" alt="The full property seen from a distance: the house, barns and cottage among trees" width="1600" height="702">
      <figcaption class="plate-caption"><span class="cap">The whole property, seen across the prairie</span><span>2013</span></figcaption>
    </figure>
  </div>

  <div class="prose">
    <span class="kicker">Inside</span>
    <h2>Three storeys, slowly won back</h2>
    <p>The house gave us 290 square metres over three floors. A large entrance hall led through to a country kitchen and to the living and dining rooms, each with its period fireplace; two wood-burning stoves kept the winters affordable. An oak spiral staircase wound up to four double bedrooms on the first floor - one with its own dressing room - and on the second floor were two more bedrooms, one of which we lined with books and called the library, with a third nearly finished when we left. The beams overhead were 16th-century oak, salvaged - like so much of the house - from the old ch&acirc;teau.</p>
  </div>

  <div class="plate-wrap">
    <figure class="plate">
      <img src="../img/int/05.jpg" alt="The entrance hall seen from the oak spiral staircase" width="2048" height="1536">
      <figcaption class="plate-caption"><span class="cap">The entrance hall, from the staircase</span><span>2013</span></figcaption>
    </figure>
    <figure class="plate">
      <img src="../img/int/04.jpg" alt="The fireplace in the kitchen, laid with a wood-burning stove" width="2048" height="1536">
      <figcaption class="plate-caption"><span class="cap">The kitchen fireplace</span><span>2013</span></figcaption>
    </figure>
  </div>

  <div class="prose">
    <span class="kicker">Outside</span>
    <h2>Barns, cottage, lakes and paddock</h2>
    <p>Around the house lay 3.5 hectares - nine acres - of parkland: two lakes, an acre of woodland, two acres of paddock, and a 300-square-metre terrace above the water where most of our summer entertaining happened. Two 16th-century barns, of 156 and 120 square metres, had been re-roofed in the traditional style, their lattice-work a small marvel when you stood beneath it. By the gate stood the gatekeeper's cottage, 96 square metres of future project that we always meant to get to.</p>
  </div>

  <div class="plate-wrap">
    <figure class="plate">
      <img src="../img/out/06.jpg" alt="16th-century lattice-work roof timbers inside the open barn" width="2048" height="1536">
      <figcaption class="plate-caption"><span class="cap">The open barn's 16th-century roof</span><span>2013</span></figcaption>
    </figure>
    <figure class="plate">
      <img src="../img/out/04.jpg" alt="The gatekeeper's cottage by the entrance" width="2841" height="1980">
      <figcaption class="plate-caption"><span class="cap">The gatekeeper's cottage</span><span>2013</span></figcaption>
    </figure>
    <figure class="plate">
      <img src="../img/grd/01.jpg" alt="The big lake seen from the house" width="2048" height="1362">
      <figcaption class="plate-caption"><span class="cap">The big lake, from the house</span><span>2013</span></figcaption>
    </figure>
  </div>

  <div class="prose">
    <p>Prissac village was close enough for bread - shops, a restaurant, a caf&eacute;-bar - and the wider practicalities were nearer than visitors expected: St Beno&icirc;t-du-Sault six miles away for banking and markets, the hospital at Le Blanc within half an hour, and mainline trains at Argenton-sur-Creuse. But those are details for <a href="../valley/">Chapter IV</a>. The point of Fontmorand was never convenience; it was the stillness.</p>
  </div>
</main>

<!-- FOOTER - duplicated on every page; keep in sync -->
<footer class="site-footer">
  <p>Fontmorand &middot; Prissac, Indre &middot; kept online by the family who loved it &middot; <a href="mailto:contact@fontmorand.fr">write to us</a></p>
</footer>

<script src="../js/site.js"></script>
<!-- GoatCounter analytics - replace YOURCODE after signup -->
<script data-goatcounter="https://YOURCODE.goatcounter.com/count" async src="//gc.zgo.at/count.js"></script>
</body>
</html>
```

- [ ] **Step 2: Verify**

```bash
bash scripts/check-images.sh web/house/index.html
php -S 127.0.0.1:8100 -t web >/dev/null 2>&1 & SP=$!; sleep 1
curl -s http://127.0.0.1:8100/house/ | grep -c "<figure class=\"plate\""
kill $SP
```

Expected: `PASS: image rule holds`; `6` plates.

- [ ] **Step 3: Commit**

```bash
git add web/house/index.html
git commit -m "feat: Chapter I - The House, details rewoven as first-person prose with plates"
```

---

### Task 5: Chapter II - The Story

**Files:**
- Create: `web/story/index.html`
- Reference: manifest for artefact dimensions (expected `img/font.jpg` 136x200, `img/monk.jpg` 136x170, `img/crenau.jpg` 100x142, `img/grd/04.jpg` 2048x1363).

**Interfaces:**
- Consumes: Task 1 classes (notably `.artefact-row`, `.plate-narrow`), Task 2 images, masthead/footer pattern with `../` paths.

- [ ] **Step 1: Write `web/story/index.html`** (complete file):

```html
<!DOCTYPE html>
<html lang="en-GB">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>The Story - Fontmorand</title>
  <meta name="description" content="Chapter II: the history of Fontmorand - the medieval font that named it, the saint called Morand, the Resistance in the South Barn, the raid of 10 July 1944, and Odette Androt, Righteous Among the Nations.">
  <link rel="icon" href="../img/favicon.png">
  <link rel="stylesheet" href="../css/site.css">
</head>
<body>

<!-- MASTHEAD - duplicated on every page; keep in sync (paths and is-active differ per page) -->
<header class="masthead">
  <a class="wordmark" href="../">Fontmorand</a>
  <button class="nav-toggle" aria-expanded="false" aria-controls="site-nav">Menu</button>
  <nav class="masthead-nav" id="site-nav">
    <a href="../house/">The House</a>
    <a href="./" class="is-active">The Story</a>
    <a href="../photographs/">The Photographs</a>
    <a href="../valley/">The Valley</a>
  </nav>
</header>

<main>
  <div class="page-opening">
    <p class="kicker">Chapter II</p>
    <h1 class="chapter-title">The Story</h1>
    <p>From 2004 we restored the house; in the evenings we dug into its past. It kept more history than we had any right to expect.</p>
  </div>

  <div class="prose">
    <span class="kicker">The name</span>
    <h2>The font, and Morand</h2>
    <p class="dropcap">At the north-eastern corner of the property, hidden in its own shady grove, there is a spring - the font that gave Fontmorand half its name. For centuries it served as the household's washing place, and the circular, bowl-shaped stone structure is still clearly there under the leaves. We took visitors to see it more often than they probably wished.</p>
  </div>

  <div class="plate-wrap">
    <figure class="plate">
      <img src="../img/grd/04.jpg" alt="The font of Fontmorand - a circular stone spring structure in its grove" width="2048" height="1363">
      <figcaption class="plate-caption"><span class="cap">The font, in its grove</span><span>2013</span></figcaption>
    </figure>
  </div>

  <div class="artefact-row">
    <figure class="artefact">
      <div class="plate plate-narrow">
        <img src="../img/font.jpg" alt="The font - engraving from the family archive" width="136" height="200">
        <figcaption class="plate-caption"><span class="cap">The font</span></figcaption>
      </div>
    </figure>
    <figure class="artefact">
      <div class="plate plate-narrow">
        <img src="../img/monk.jpg" alt="Saint Morand, depicted with a bunch of grapes" width="136" height="170">
        <figcaption class="plate-caption"><span class="cap">Saint Morand</span></figcaption>
      </div>
    </figure>
  </div>

  <div class="prose">
    <p>The other half of the name, we believe, is Morand - a Benedictine saint, patron of wine growers, feast day the third of June. Morand was born to an aristocratic family near Worms and educated at its cathedral school, made a pilgrimage to Compostela, and entered the great monastery at Cluny. Sent into Alsace as counsellor to Count Frederick Pferz at Altkirch, he became famous for celebrating an entire Lent sustained by nothing but a bunch of grapes - which, in France, is exactly the sort of miracle that gets remembered.</p>

    <span class="kicker">The war</span>
    <h2>The niche in the South Barn</h2>
    <p>In the north wall of the South Barn there is a niche holding a small statue of the Virgin Mary - the Lady of Pontmain. It is not the original statue but a stand-in, placed there until the original is found. The niche was built after the Second World War to commemorate what happened quietly at Fontmorand during it: refugees were hidden in the South Barn on their way south to Spain, part of a secret route run by the Resistance at Prissac. The family who lived at Fontmorand in those years confirmed the story to us themselves.</p>
    <p>The story has a coda we particularly loved: the organiser of the refugee route, who had come to Prissac from Alsace, fell for one of the household's daughters. They married, and a daughter of that marriage was still living and working in Ch&acirc;teauroux when we knew the house.</p>
  </div>

  <div class="artefact-row">
    <figure class="artefact">
      <div class="plate plate-narrow">
        <img src="../img/crenau.jpg" alt="The niche in the South Barn's north wall, holding the statue of the Lady of Pontmain" width="100" height="142">
        <figcaption class="plate-caption"><span class="cap">The cr&eacute;neau</span></figcaption>
      </div>
    </figure>
  </div>

  <div class="prose">
    <span class="kicker">10 July 1944</span>
    <h2>The day the column turned away</h2>
    <!-- FAMILY FACT-CHECK REQUIRED: this entry and the next are adapted from an OCR-damaged 1972 newspaper article in the family archive. Verify names, places and details before go-live. -->
    <p>On 10 July 1944, the Waffen-SS "Das Reich" division swept these communes for the Resistance - B&eacute;labre, Chalais, Oulches, Ciron, Prissac, Lignac. It was an act of terror without precedent for this quiet corner of the Berry: villages and hamlets burned, from B&eacute;labre and Terrier to Ch&acirc;teau-Guillaume and Chillouet, and the wooded hills of la Bicherie, just north of Prissac, were searched.</p>
    <p>Prissac itself was spared, for two reasons. One was the caution of its Resistance leadership. The other was the heroic silence of a boy of about fifteen named Henri M&eacute;gray - "Riri" - who was seized and forced into the lead vehicle of the German column as a guide. Stripped and beaten at the cemetery gate, he said nothing, though he spent his days alongside the men of the Resistance and knew exactly where they were. The column turned away towards B&eacute;labre and Oulches - narrowly missing both Fontmorand and the Moulin Ribaud, headquarters of the local Resistance leader. Every family in Prissac, including ours decades later, owed that boy more than could be repaid.</p>
    <p><em>Adapted from a 1972 "La Nouvelle R&eacute;publique" article preserved in the family archive.</em></p>

    <span class="kicker">Odette Androt</span>
    <h2>Righteous Among the Nations</h2>
    <p>Odette Androt was the town-hall secretary of Prissac. During the Occupation, around twenty Jewish families took refuge in the area - among them three members of the Gozland family, born in Constantine and Marseillais by adoption, and four of the Siac family, who had fled Romania and then occupied Paris. Odette provided them all with identity and ration cards that did not carry the "Juif" stamp the law required.</p>
    <p>In 1943 Prissac's mayor, a known Vichy loyalist, ordered her to draw up a list of the town's Jewish residents. Once he had signed it, she secretly destroyed it rather than pass it to the Ministry of the Interior. In the summer of 1944, with retreating German forces rumoured to be hunting Jews on their way north to Normandy, the Siacs - husband, wife, twelve-year-old Th&eacute;r&egrave;se and six-year-old Lucien - could find no villager to take them in. Odette sheltered them in her own home until the danger had passed.</p>
    <p>On 31 December 1998, Yad Vashem recognised Odette Androt as Righteous Among the Nations.</p>

    <span class="kicker">Our chapter</span>
    <h2>2004 to 2018</h2>
    <p>We were only the latest family to pass through Fontmorand's long story, and we knew it. We spent our fourteen years restoring the house - roofs, beams, bathrooms, the endless pointing - and recording as much of its history as we could find, including everything on this page. When we sold it, we kept the photographs and the stories. This site is where they live now.</p>
    <!-- CONFIRM: sale year 2018 - Owen to verify -->
  </div>
</main>

<!-- FOOTER - duplicated on every page; keep in sync -->
<footer class="site-footer">
  <p>Fontmorand &middot; Prissac, Indre &middot; kept online by the family who loved it &middot; <a href="mailto:contact@fontmorand.fr">write to us</a></p>
</footer>

<script src="../js/site.js"></script>
<!-- GoatCounter analytics - replace YOURCODE after signup -->
<script data-goatcounter="https://YOURCODE.goatcounter.com/count" async src="//gc.zgo.at/count.js"></script>
</body>
</html>
```

- [ ] **Step 2: Verify**

```bash
bash scripts/check-images.sh web/story/index.html
php -S 127.0.0.1:8100 -t web >/dev/null 2>&1 & SP=$!; sleep 1
curl -s http://127.0.0.1:8100/story/ | grep -c "kicker"
curl -s http://127.0.0.1:8100/story/ | grep -q "FAMILY FACT-CHECK" && echo "flag present"
kill $SP
```

Expected: `PASS: image rule holds`; kicker count `6` (chapter kicker + five section kickers); `flag present`.

- [ ] **Step 3: Commit**

```bash
git add web/story/index.html
git commit -m "feat: Chapter II - The Story, full history as narrative with artefact plates"
```

---

### Task 6: Chapter III - The Photographs

**Files:**
- Create: `web/photographs/index.html`
- Reference: manifest for all `_m` derivative dimensions (expected 800 wide, proportional heights - e.g. `ext/01_m.jpg` 800x?, read each from the manifest; heights below marked `H?` MUST be replaced with manifest values).

**Interfaces:**
- Consumes: Task 1 classes (`.album-grid`, `.gallery`, `.gallery-item`), Task 2 `_m` derivatives and full images, `js/lightbox.js` (unchanged contract: click on `.gallery-item` anchors inside a `.gallery` opens overlay with `href` image and `data-caption`).

- [ ] **Step 1: Write `web/photographs/index.html`.** Full file below. It contains four album sections. **For every `<img>`, replace `H?` with the height listed for that exact `_m` file in `docs/superpowers/plans/image-manifest.txt`** (all `_m` files are 800 wide; heights vary per image). The caption text is carried over from the old photos page; every photo is dated 2013.

```html
<!DOCTYPE html>
<html lang="en-GB">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>The Photographs - Fontmorand</title>
  <meta name="description" content="Chapter III: the Fontmorand album - the house, its rooms, its out-buildings and its grounds, photographed in 2013, captioned and dated.">
  <link rel="icon" href="../img/favicon.png">
  <link rel="stylesheet" href="../css/site.css">
</head>
<body>

<!-- MASTHEAD - duplicated on every page; keep in sync (paths and is-active differ per page) -->
<header class="masthead">
  <a class="wordmark" href="../">Fontmorand</a>
  <button class="nav-toggle" aria-expanded="false" aria-controls="site-nav">Menu</button>
  <nav class="masthead-nav" id="site-nav">
    <a href="../house/">The House</a>
    <a href="../story/">The Story</a>
    <a href="./" class="is-active">The Photographs</a>
    <a href="../valley/">The Valley</a>
  </nav>
</header>

<main>
  <div class="page-opening">
    <p class="kicker">Chapter III</p>
    <h1 class="chapter-title">The Photographs</h1>
    <p>The album, as we kept it - the house, its rooms, its out-buildings and its grounds. Click any plate to see it full size.</p>
  </div>

  <section class="gallery">
    <div class="prose"><span class="kicker">The exterior</span></div>
    <div class="album-grid">
      <a class="gallery-item plate-link" href="../img/ext/01.jpg" data-caption="Main view of the house">
        <figure class="plate"><img src="../img/ext/01_m.jpg" alt="Main view of the house" width="800" height="H?"><figcaption class="plate-caption"><span class="cap">Main view of the house</span><span>2013</span></figcaption></figure>
      </a>
      <a class="gallery-item plate-link" href="../img/ext/02.jpg" data-caption="Front view, reflected on the lake">
        <figure class="plate"><img src="../img/ext/02_m.jpg" alt="The house reflected on the lake" width="800" height="H?"><figcaption class="plate-caption"><span class="cap">Reflected on the lake</span><span>2013</span></figcaption></figure>
      </a>
      <a class="gallery-item plate-link" href="../img/ext/03.jpg" data-caption="The house in bloom">
        <figure class="plate"><img src="../img/ext/03_m.jpg" alt="The house with the garden in bloom" width="800" height="H?"><figcaption class="plate-caption"><span class="cap">In bloom</span><span>2013</span></figcaption></figure>
      </a>
      <a class="gallery-item plate-link" href="../img/ext/04.jpg" data-caption="All the buildings, from across the lake">
        <figure class="plate"><img src="../img/ext/04_m.jpg" alt="All the buildings seen from across the lake" width="800" height="H?"><figcaption class="plate-caption"><span class="cap">The whole property</span><span>2013</span></figcaption></figure>
      </a>
      <a class="gallery-item plate-link" href="../img/ext/05.jpg" data-caption="Side view from the edge of the games field">
        <figure class="plate"><img src="../img/ext/05_m.jpg" alt="Side view from the edge of the games field" width="800" height="H?"><figcaption class="plate-caption"><span class="cap">From the games field</span><span>2013</span></figcaption></figure>
      </a>
      <a class="gallery-item plate-link" href="../img/ext/06.jpg" data-caption="Close-up side view of the main house">
        <figure class="plate"><img src="../img/ext/06_m.jpg" alt="Close-up side view of the main house" width="800" height="H?"><figcaption class="plate-caption"><span class="cap">Side view, close</span><span>2013</span></figcaption></figure>
      </a>
      <a class="gallery-item plate-link" href="../img/ext/07.jpg" data-caption="Rear view of the house from the track">
        <figure class="plate"><img src="../img/ext/07_m.jpg" alt="Rear view of the house from the track" width="800" height="H?"><figcaption class="plate-caption"><span class="cap">From the track</span><span>2013</span></figcaption></figure>
      </a>
      <a class="gallery-item plate-link" href="../img/ext/08.jpg" data-caption="Aerial view of Fontmorand">
        <figure class="plate"><img src="../img/ext/08_m.jpg" alt="Aerial view of Fontmorand" width="800" height="H?"><figcaption class="plate-caption"><span class="cap">From the air</span><span>2013</span></figcaption></figure>
      </a>
    </div>

    <div class="prose"><span class="kicker">The interior</span></div>
    <div class="album-grid">
      <a class="gallery-item plate-link" href="../img/int/05.jpg" data-caption="Entrance hall, viewed from the staircase">
        <figure class="plate"><img src="../img/int/05_m.jpg" alt="Entrance hall viewed from the staircase" width="800" height="H?"><figcaption class="plate-caption"><span class="cap">The entrance hall</span><span>2013</span></figcaption></figure>
      </a>
      <a class="gallery-item plate-link" href="../img/int/06.jpg" data-caption="The staircase, from the front door">
        <figure class="plate"><img src="../img/int/06_m.jpg" alt="The oak staircase seen from the front door" width="800" height="H?"><figcaption class="plate-caption"><span class="cap">The staircase</span><span>2013</span></figcaption></figure>
      </a>
      <a class="gallery-item plate-link" href="../img/int/01.jpg" data-caption="Kitchen, from the hall doorway">
        <figure class="plate"><img src="../img/int/01_m.jpg" alt="The kitchen from the hall doorway" width="800" height="H?"><figcaption class="plate-caption"><span class="cap">The kitchen</span><span>2013</span></figcaption></figure>
      </a>
      <a class="gallery-item plate-link" href="../img/int/02.jpg" data-caption="Kitchen, from the side door">
        <figure class="plate"><img src="../img/int/02_m.jpg" alt="The kitchen from the side door" width="800" height="H?"><figcaption class="plate-caption"><span class="cap">The kitchen, side door</span><span>2013</span></figcaption></figure>
      </a>
      <a class="gallery-item plate-link" href="../img/int/03.jpg" data-caption="Kitchen cupboards and appliances">
        <figure class="plate"><img src="../img/int/03_m.jpg" alt="Kitchen cupboards and appliances" width="800" height="H?"><figcaption class="plate-caption"><span class="cap">The kitchen cupboards</span><span>2013</span></figcaption></figure>
      </a>
      <a class="gallery-item plate-link" href="../img/int/04.jpg" data-caption="The fireplace in the kitchen">
        <figure class="plate"><img src="../img/int/04_m.jpg" alt="The fireplace in the kitchen" width="800" height="H?"><figcaption class="plate-caption"><span class="cap">The kitchen fireplace</span><span>2013</span></figcaption></figure>
      </a>
      <a class="gallery-item plate-link" href="../img/int/07.jpg" data-caption="The dining area in the front room">
        <figure class="plate"><img src="../img/int/07_m.jpg" alt="The dining area in the front room" width="800" height="H?"><figcaption class="plate-caption"><span class="cap">The dining room</span><span>2013</span></figcaption></figure>
      </a>
      <a class="gallery-item plate-link" href="../img/int/08.jpg" data-caption="The sitting area in the front room">
        <figure class="plate"><img src="../img/int/08_m.jpg" alt="The sitting area in the front room" width="800" height="H?"><figcaption class="plate-caption"><span class="cap">The sitting room</span><span>2013</span></figcaption></figure>
      </a>
      <a class="gallery-item plate-link" href="../img/int/09.jpg" data-caption="The fireplace in the front room">
        <figure class="plate"><img src="../img/int/09_m.jpg" alt="The fireplace in the front room" width="800" height="H?"><figcaption class="plate-caption"><span class="cap">The front-room fireplace</span><span>2013</span></figcaption></figure>
      </a>
      <a class="gallery-item plate-link" href="../img/int/10.jpg" data-caption="The master bedroom">
        <figure class="plate"><img src="../img/int/10_m.jpg" alt="The master bedroom" width="800" height="H?"><figcaption class="plate-caption"><span class="cap">The master bedroom</span><span>2013</span></figcaption></figure>
      </a>
      <a class="gallery-item plate-link" href="../img/int/11.jpg" data-caption="Guest room one">
        <figure class="plate"><img src="../img/int/11_m.jpg" alt="The first guest room" width="800" height="H?"><figcaption class="plate-caption"><span class="cap">Guest room one</span><span>2013</span></figcaption></figure>
      </a>
      <a class="gallery-item plate-link" href="../img/int/12.jpg" data-caption="Guest room two">
        <figure class="plate"><img src="../img/int/12_m.jpg" alt="The second guest room" width="800" height="H?"><figcaption class="plate-caption"><span class="cap">Guest room two</span><span>2013</span></figcaption></figure>
      </a>
    </div>

    <div class="prose"><span class="kicker">The out-buildings</span></div>
    <div class="album-grid">
      <a class="gallery-item plate-link" href="../img/out/01.jpg" data-caption="The out-buildings, from the lake">
        <figure class="plate"><img src="../img/out/01_m.jpg" alt="The out-buildings seen from the lake" width="800" height="H?"><figcaption class="plate-caption"><span class="cap">From the lake</span><span>2013</span></figcaption></figure>
      </a>
      <a class="gallery-item plate-link" href="../img/out/02.jpg" data-caption="The out-buildings, from the entrance">
        <figure class="plate"><img src="../img/out/02_m.jpg" alt="The out-buildings seen from the entrance gate" width="800" height="H?"><figcaption class="plate-caption"><span class="cap">From the entrance</span><span>2013</span></figcaption></figure>
      </a>
      <a class="gallery-item plate-link" href="../img/out/04.jpg" data-caption="The gatekeeper's cottage">
        <figure class="plate"><img src="../img/out/04_m.jpg" alt="The gatekeeper's cottage" width="800" height="H?"><figcaption class="plate-caption"><span class="cap">The cottage</span><span>2013</span></figcaption></figure>
      </a>
      <a class="gallery-item plate-link" href="../img/out/05.jpg" data-caption="The open barn, from the paddock">
        <figure class="plate"><img src="../img/out/05_m.jpg" alt="The open barn seen from the paddock" width="800" height="H?"><figcaption class="plate-caption"><span class="cap">The open barn</span><span>2013</span></figcaption></figure>
      </a>
      <a class="gallery-item plate-link" href="../img/out/06.jpg" data-caption="16th-century roof lattice-work">
        <figure class="plate"><img src="../img/out/06_m.jpg" alt="16th-century lattice-work in the barn roof" width="800" height="H?"><figcaption class="plate-caption"><span class="cap">The barn roof</span><span>2013</span></figcaption></figure>
      </a>
      <a class="gallery-item plate-link" href="../img/out/07.jpg" data-caption="The open barn">
        <figure class="plate"><img src="../img/out/07_m.jpg" alt="The open barn" width="800" height="H?"><figcaption class="plate-caption"><span class="cap">The open barn</span><span>2013</span></figcaption></figure>
      </a>
      <a class="gallery-item plate-link" href="../img/out/08.jpg" data-caption="The lean-to on the end of the open barn">
        <figure class="plate"><img src="../img/out/08_m.jpg" alt="The lean-to on the end of the open barn" width="800" height="H?"><figcaption class="plate-caption"><span class="cap">The lean-to</span><span>2013</span></figcaption></figure>
      </a>
      <a class="gallery-item plate-link" href="../img/out/09.jpg" data-caption="The closed barn, from the main garden">
        <figure class="plate"><img src="../img/out/09_m.jpg" alt="The closed barn seen from the main garden" width="800" height="H?"><figcaption class="plate-caption"><span class="cap">The closed barn</span><span>2013</span></figcaption></figure>
      </a>
      <a class="gallery-item plate-link" href="../img/out/10.jpg" data-caption="The porcherie">
        <figure class="plate"><img src="../img/out/10_m.jpg" alt="The porcherie, a closed barn" width="800" height="H?"><figcaption class="plate-caption"><span class="cap">The porcherie</span><span>2013</span></figcaption></figure>
      </a>
    </div>

    <div class="prose"><span class="kicker">The grounds</span></div>
    <div class="album-grid">
      <a class="gallery-item plate-link" href="../img/grd/01.jpg" data-caption="The big lake, from the house">
        <figure class="plate"><img src="../img/grd/01_m.jpg" alt="The big lake seen from the house" width="800" height="H?"><figcaption class="plate-caption"><span class="cap">The big lake</span><span>2013</span></figcaption></figure>
      </a>
      <a class="gallery-item plate-link" href="../img/grd/02.jpg" data-caption="The big lake, from the closed barn">
        <figure class="plate"><img src="../img/grd/02_m.jpg" alt="The big lake seen from the closed barn" width="800" height="H?"><figcaption class="plate-caption"><span class="cap">The big lake, from the barn</span><span>2013</span></figcaption></figure>
      </a>
      <a class="gallery-item plate-link" href="../img/grd/03.jpg" data-caption="The small lake">
        <figure class="plate"><img src="../img/grd/03_m.jpg" alt="The small lake" width="800" height="H?"><figcaption class="plate-caption"><span class="cap">The small lake</span><span>2013</span></figcaption></figure>
      </a>
      <a class="gallery-item plate-link" href="../img/grd/04.jpg" data-caption="The font of Fontmorand">
        <figure class="plate"><img src="../img/grd/04_m.jpg" alt="The font of Fontmorand in its grove" width="800" height="H?"><figcaption class="plate-caption"><span class="cap">The font</span><span>2013</span></figcaption></figure>
      </a>
      <a class="gallery-item plate-link" href="../img/grd/05.jpg" data-caption="The house, over the paddock">
        <figure class="plate"><img src="../img/grd/05_m.jpg" alt="The house seen over the paddock" width="800" height="H?"><figcaption class="plate-caption"><span class="cap">Over the paddock</span><span>2013</span></figcaption></figure>
      </a>
    </div>
  </section>
</main>

<!-- FOOTER - duplicated on every page; keep in sync -->
<footer class="site-footer">
  <p>Fontmorand &middot; Prissac, Indre &middot; kept online by the family who loved it &middot; <a href="mailto:contact@fontmorand.fr">write to us</a></p>
</footer>

<script src="../js/site.js"></script>
<script src="../js/lightbox.js"></script>
<!-- GoatCounter analytics - replace YOURCODE after signup -->
<script data-goatcounter="https://YOURCODE.goatcounter.com/count" async src="//gc.zgo.at/count.js"></script>
</body>
</html>
```

Note: the old page's Exterior section had an `img/ext/` folder; if the manifest shows `ext/` does not exist (the old listing was partial), check `web/img/` for the actual exterior folder name and adjust paths accordingly - captions and ordering come from the old `photos.php` regardless. `out/03.jpg` was absent from the old page and stays absent.

- [ ] **Step 2: Verify - no `H?` left, image rule passes, all lightbox targets exist**

```bash
grep -c 'H?' web/photographs/index.html
bash scripts/check-images.sh web/photographs/index.html
php -S 127.0.0.1:8100 -t web >/dev/null 2>&1 & SP=$!; sleep 1
for u in $(grep -o 'href="\.\./img/[^"]*"' web/photographs/index.html | sed 's/href="\.\.\///;s/"//'); do
  code=$(curl -s -o /dev/null -w "%{http_code}" "http://127.0.0.1:8100/$u")
  [ "$code" != "200" ] && echo "MISSING $u ($code)"
done; echo "link check done"
kill $SP
```

Expected: `0` occurrences of `H?`; `PASS: image rule holds`; `link check done` with no MISSING lines.

- [ ] **Step 3: Commit**

```bash
git add web/photographs/index.html
git commit -m "feat: Chapter III - The Photographs, album grid of mounted plates with lightbox"
```

---

### Task 7: Valley imagery sourcing (MAIN SESSION - interactive, do not dispatch to a subagent)

**Files:**
- Create: `web/img/region/` additions - approved, licensed photographs
- Create: `docs/superpowers/plans/valley-image-credits.md` - per-image source URL, author, licence, attribution line

**Interfaces:**
- Produces: 4-6 approved images in `web/img/region/` (each ≤1600px wide, derived downscale-only), an `_m` 800w derivative for each, manifest updated, and the credits file Task 8 turns into the colophon.

- [ ] **Step 1:** Search Wikimedia Commons (API: `https://commons.wikimedia.org/w/api.php?action=query&generator=search&gsrsearch=...&prop=imageinfo&iiprop=url|extmetadata&format=json`) for high-resolution, freely licensed (CC BY / CC BY-SA / public domain) photographs of: the Creuse valley near Crozant/Gargilesse, Lac de Chambon (Indre) and the Eguzon dam, Gargilesse-Dampierre village, the Brenne etangs, and St Benoit-du-Sault. Shortlist 2-3 candidates per subject; record author + licence for each.
- [ ] **Step 2:** Download shortlisted candidates to the brainstorm content dir, build a contact-sheet screen (companion server pattern from this session), and ask Owen to approve/reject per subject. Wait for his selections.
- [ ] **Step 3:** For approved images: download the original, derive a ≤1600w web version into `web/img/region/` with a descriptive filename (`creuse-valley.jpg`, `lac-de-chambon.jpg`, `gargilesse.jpg`, `brenne-etang.jpg`, `st-benoit.jpg`), generate the 800w `_m` derivative, append all to the manifest, and write `docs/superpowers/plans/valley-image-credits.md` with, per image: filename, source page URL, author, licence, and a ready-to-use attribution line ("Photograph of X by AUTHOR, LICENCE, via Wikimedia Commons").
- [ ] **Step 4: Commit**

```bash
git add web/img/region docs/superpowers/plans/valley-image-credits.md docs/superpowers/plans/image-manifest.txt
git commit -m "feat: licensed regional photography for The Valley, with credits"
```

---

### Task 8: Chapter IV - The Valley (regional map + colophon)

**Files:**
- Create: `web/valley/index.html`
- Reference: `docs/superpowers/plans/valley-image-credits.md` (colophon text), manifest (dimensions of the new region images - the four plate `<img>`s below use FILENAME/W/H placeholders that MUST be filled from Task 7's actual approved files; if a subject was rejected with no replacement, drop its plate).

**Interfaces:**
- Consumes: Task 1 classes (`.map-figure`, `.colophon`), Task 7 images and credits, masthead/footer pattern with `../` paths.

- [ ] **Step 1: Write `web/valley/index.html`** (complete file; substitute the four region image FILENAME/W/H sets and the colophon lines from Task 7's credits file):

```html
<!DOCTYPE html>
<html lang="en-GB">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>The Valley - Fontmorand</title>
  <meta name="description" content="Chapter IV: secret France - the Creuse valley, Lac de Chambon, Gargilesse, the Brenne's thousand lakes, and where Fontmorand sits among them.">
  <link rel="icon" href="../img/favicon.png">
  <link rel="stylesheet" href="../css/site.css">
</head>
<body>

<!-- MASTHEAD - duplicated on every page; keep in sync (paths and is-active differ per page) -->
<header class="masthead">
  <a class="wordmark" href="../">Fontmorand</a>
  <button class="nav-toggle" aria-expanded="false" aria-controls="site-nav">Menu</button>
  <nav class="masthead-nav" id="site-nav">
    <a href="../house/">The House</a>
    <a href="../story/">The Story</a>
    <a href="../photographs/">The Photographs</a>
    <a href="./" class="is-active">The Valley</a>
  </nav>
</header>

<main>
  <div class="page-opening">
    <p class="kicker">Chapter IV</p>
    <h1 class="chapter-title">The Valley</h1>
    <p>They call it secret France - the green country most visitors drive through on their way to the crowded south. We never understood the hurry.</p>
  </div>

  <figure class="map-figure">
    <!-- Stylised regional map - deliberately soft; no street-level detail -->
    <svg viewBox="0 0 300 290" role="img" aria-label="Stylised map of France with Prissac marked in the Indre, roughly three hours south of Paris">
      <path d="M147,18 L174,30 L196,52 L194,90 L211,126 L196,165 L162,174 L129,168 L111,186 L63,183 L72,147 L48,120 L15,111 L33,87 L69,81 L90,63 L120,36 Z"
            fill="none" stroke="#8a8272" stroke-width="1.5" stroke-linejoin="round"/>
      <circle cx="134" cy="54" r="3" fill="#8a8272"/>
      <text x="142" y="58" font-family="Georgia, serif" font-size="11" fill="#8a8272">Paris</text>
      <circle cx="105" cy="120" r="4.5" fill="#8c2f24"/>
      <text x="114" y="124" font-family="Georgia, serif" font-size="12" font-style="italic" fill="#26221c">Prissac</text>
      <path d="M132,58 Q118,86 108,114" fill="none" stroke="#8c2f24" stroke-width="1" stroke-dasharray="3 4"/>
      <text x="138" y="92" font-family="Georgia, serif" font-size="10" font-style="italic" fill="#8a8272">about 3 hours</text>
    </svg>
    <figcaption>Prissac, in the Indre - about three hours south of Paris</figcaption>
  </figure>

  <div class="prose">
    <span class="kicker">The Creuse</span>
    <h2>The valley itself</h2>
    <p class="dropcap">The Creuse valley had museums, ch&acirc;teaux, swimming and fishing lakes, riding, and restaurants that took local produce seriously - and half the time we seemed to have it to ourselves. It is greener than the south, and quieter. Half an hour from the house lay the huge Lac de Chambon, made by damming the Creuse near the pretty town of Eguzon; it gave the area its hydro-electric power and gave us pontoons to swim to, sailing, water-skiing and pedalos - children of all ages, catered for.</p>
  </div>

  <div class="plate-wrap">
    <figure class="plate">
      <img src="../img/region/FILENAME-1.jpg" alt="The Creuse valley" width="W" height="H">
      <figcaption class="plate-caption"><span class="cap">The Creuse valley</span><span>photograph: see colophon</span></figcaption>
    </figure>
    <figure class="plate">
      <img src="../img/region/FILENAME-2.jpg" alt="Lac de Chambon near Eguzon" width="W" height="H">
      <figcaption class="plate-caption"><span class="cap">Lac de Chambon, near Eguzon</span><span>photograph: see colophon</span></figcaption>
    </figure>
  </div>

  <div class="prose">
    <span class="kicker">The towns</span>
    <h2>Places we kept going back to</h2>
    <p>St Beno&icirc;t-du-Sault, ten kilometres away, is officially one of the prettiest villages in France - medieval, perched on a craggy granite outcrop, all tiny winding streets. Argenton-sur-Creuse, twenty minutes off, had the trains, good restaurants and a Roman excavation with its museum. Gargilesse-Dampierre drew the painters, as it drew George Sand before them. Le Blanc, the elegant ch&acirc;teau town twenty-five minutes away, had a specialised fish market and canoes for hire on the river. And for the bigger days out: Limoges within the hour for porcelain and its cathedral, Poitiers at ninety minutes for Futuroscope, and Oradour-sur-Glane, preserved in silence as a memorial to the Second World War - sobering, and worth it.</p>
  </div>

  <div class="plate-wrap">
    <figure class="plate">
      <img src="../img/region/FILENAME-3.jpg" alt="Gargilesse-Dampierre village" width="W" height="H">
      <figcaption class="plate-caption"><span class="cap">Gargilesse-Dampierre</span><span>photograph: see colophon</span></figcaption>
    </figure>
  </div>

  <div class="prose">
    <span class="kicker">The Brenne</span>
    <h2>A thousand lakes</h2>
    <p>Fontmorand sat inside the Brenne National Park - the land of a thousand lakes. Deer, wildcats, turtles and otters lived around us; eagles were not rare, and the buzzards came close enough to startle you. There was a public fishing lake directly opposite the house with some giant carp in it, which we mention because every angler who ever visited asked. Fishing, walking, canoeing, riding, cycling; and for the golfers, Val de l'Indre, La Porcelaine and Limoges-St Lazare were all within the hour.</p>
  </div>

  <div class="plate-wrap">
    <figure class="plate">
      <img src="../img/region/FILENAME-4.jpg" alt="An etang in the Brenne" width="W" height="H">
      <figcaption class="plate-caption"><span class="cap">An &eacute;tang in the Brenne</span><span>photograph: see colophon</span></figcaption>
    </figure>
  </div>

  <div class="colophon">
    <p>Regional photographs: <!-- one attribution line per image from docs/superpowers/plans/valley-image-credits.md, e.g. "Creuse valley by AUTHOR, CC BY-SA 4.0, via Wikimedia Commons" - joined with semicolons -->. All other photographs are the family's own.</p>
  </div>
</main>

<!-- FOOTER - duplicated on every page; keep in sync -->
<footer class="site-footer">
  <p>Fontmorand &middot; Prissac, Indre &middot; kept online by the family who loved it &middot; <a href="mailto:contact@fontmorand.fr">write to us</a></p>
</footer>

<script src="../js/site.js"></script>
<!-- GoatCounter analytics - replace YOURCODE after signup -->
<script data-goatcounter="https://YOURCODE.goatcounter.com/count" async src="//gc.zgo.at/count.js"></script>
</body>
</html>
```

- [ ] **Step 2: Verify**

```bash
grep -c 'FILENAME\|width="W"' web/valley/index.html
bash scripts/check-images.sh web/valley/index.html
php -S 127.0.0.1:8100 -t web >/dev/null 2>&1 & SP=$!; sleep 1
curl -s http://127.0.0.1:8100/valley/ | grep -q "colophon" && echo "colophon present"
kill $SP
```

Expected: `0` placeholder remnants; `PASS: image rule holds`; `colophon present`.

- [ ] **Step 3: Commit**

```bash
git add web/valley/index.html
git commit -m "feat: Chapter IV - The Valley, regional prose, licensed plates, stylised map, colophon"
```

---

### Task 9: Retire the PHP site; redirects, robots, sitemap

**Files:**
- Delete: `web/index.php`, `web/pages/*.php`, `web/includes/`, `web/img/house.jpg`, `web/img/house_new.jpg`, `web/img/rmOverseasLogo.jpg`, `web/img/FPS-Sticker-small.png`, `web/img/old_wall.png`, `web/img/old_wall_transparent.png`
- Create: `web/_redirects`, `web/.htaccess`, `web/robots.txt`
- Replace: `web/sitemap.xml`
- Keep: `Procfile`, `app.json`, `composer.json` (Heroku interim serving), `web/img/favicon.png`

**Interfaces:**
- Consumes: all five pages existing (Tasks 3-8).

- [ ] **Step 1: Delete the PHP layer and orphaned legacy images**

```bash
git rm web/index.php
git rm -r web/pages web/includes
git rm web/img/house.jpg web/img/house_new.jpg web/img/rmOverseasLogo.jpg web/img/FPS-Sticker-small.png web/img/old_wall.png web/img/old_wall_transparent.png
```

(`web/pages` at this point contains only the `.php` files - the `img` tree moved out in Task 2. If anything else remains in it, stop and list it before deleting.)

- [ ] **Step 2: Write `web/_redirects`** (Cloudflare Pages):

```
/index.php            /              301
/pages/details.php    /house/        301
/pages/history.php    /story/        301
/pages/photos.php     /photographs/  301
/pages/area.php       /valley/       301
/pages/find-us.php    /valley/       301
/pages/contact.php    /              301
```

- [ ] **Step 3: Write `web/.htaccess`** (Heroku Apache interim - same mapping):

```apache
RedirectPermanent /index.php /
RedirectPermanent /pages/details.php /house/
RedirectPermanent /pages/history.php /story/
RedirectPermanent /pages/photos.php /photographs/
RedirectPermanent /pages/area.php /valley/
RedirectPermanent /pages/find-us.php /valley/
RedirectPermanent /pages/contact.php /
DirectoryIndex index.html
```

- [ ] **Step 4: Write `web/robots.txt`**:

```
User-agent: *
Allow: /

Sitemap: https://fontmorand.com/sitemap.xml
```

- [ ] **Step 5: Replace `web/sitemap.xml`**:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url><loc>https://fontmorand.com/</loc></url>
  <url><loc>https://fontmorand.com/house/</loc></url>
  <url><loc>https://fontmorand.com/story/</loc></url>
  <url><loc>https://fontmorand.com/photographs/</loc></url>
  <url><loc>https://fontmorand.com/valley/</loc></url>
</urlset>
```

- [ ] **Step 6: Verify - no PHP remains, no references to retired paths, all pages still serve**

```bash
find web -name "*.php" | wc -l
grep -rn "pages/\|\.php\|house_new\|Formspree\|formspree" web --include="*.html" --include="*.css" --include="*.js" | grep -v "_redirects\|htaccess" | wc -l
php -S 127.0.0.1:8100 -t web >/dev/null 2>&1 & SP=$!; sleep 1
for p in "" house/ story/ photographs/ valley/; do
  code=$(curl -s -o /dev/null -w "%{http_code}" "http://127.0.0.1:8100/$p")
  echo "$p -> $code"
done
kill $SP
```

Expected: `0` PHP files; `0` stale references; all five paths `200`.

- [ ] **Step 7: Commit**

```bash
git add -A web
git commit -m "feat: retire PHP layer; add redirects, robots.txt and new sitemap - site is fully static"
```

---

### Task 10: Full-site verification and PR update

**Files:**
- Modify: PR #1 body (via `gh`)

- [ ] **Step 1: Image rule across every page**

Run: `bash scripts/check-images.sh web/index.html web/house/index.html web/story/index.html web/photographs/index.html web/valley/index.html`
Expected: `PASS: image rule holds`

- [ ] **Step 2: Internal link sweep** (every local href/src on every page resolves):

```bash
php -S 127.0.0.1:8100 -t web >/dev/null 2>&1 & SP=$!; sleep 1
fail=0
for page in "" "house/" "story/" "photographs/" "valley/"; do
  base="http://127.0.0.1:8100/$page"
  for u in $(curl -s "$base" | grep -o '\(href\|src\)="[^"]*"' | sed 's/.*="//;s/"//' | grep -v '^http\|^mailto\|^//\|^#'); do
    code=$(curl -s -o /dev/null -w "%{http_code}" "$base$u")
    [ "$code" != "200" ] && { echo "BROKEN on /$page: $u ($code)"; fail=1; }
  done
done
[ $fail -eq 0 ] && echo "ALL LINKS OK"
kill $SP
```

Expected: `ALL LINKS OK`

- [ ] **Step 3: Copy rules sweep** - no em dashes, no "for sale" framing, no present-tense ownership slips:

```bash
grep -rn $'—' web --include="*.html" | wc -l
grep -rni "for sale\|viewing\|asking price" web --include="*.html" | wc -l
```

Expected: `0` and `0`.

- [ ] **Step 4: Confirm CONFIRM inventory** - list every open item for Owen:

Run: `grep -rn "CONFIRM\|FAMILY FACT-CHECK\|YOURCODE" web --include="*.html"`
Expected: exactly the planned set - photo date (home), sale year (home + story), contact mailbox (home), fact-check flag (story), GoatCounter x5. Anything else is a leak; anything missing was lost.

- [ ] **Step 5: Update the PR**

```bash
git push owsters modernisation
gh pr edit 1 --title "Reimagine Fontmorand as a static five-chapter family memoir" --body "$(cat <<'EOF'
Replaces the earlier PHP reskin with the full reimagining per docs/superpowers/specs/2026-07-20-fontmorand-reimagining-design.md.

- Five chapters (Home, The House, The Story, The Photographs, The Valley); first-person memoir voice; monograph palette; every photo a mounted plate, never rendered above native resolution
- Fully static - no PHP runtime, no forms, no API keys; Heroku serves it as-is, Cloudflare Pages migration ready (_redirects included)
- Licensed regional photography with colophon; stylised SVG map (no Google Maps)
- Drone film as click-to-play youtube-nocookie plate

Before merge:
- [ ] Family fact-check: 10 July 1944 and Odette Androt entries (story page)
- [ ] Confirm sale year (2018?), lead photo date, contact@fontmorand.fr mailbox
- [ ] GoatCounter signup - replace YOURCODE in all five pages
- [ ] Regenerate (then delete) the leaked Google Maps API key in Google Cloud Console
- [ ] Cross-browser / responsive review at bigron.local:8000

🤖 Generated with [Claude Code](https://claude.com/claude-code)
EOF
)"
```

- [ ] **Step 6: Final commit of any stragglers**

```bash
git status --short
git add -A && git diff --cached --quiet || git commit -m "chore: final verification tidy-up"
```

---

## Self-review notes

- Spec coverage: structure (T3-T8), visual system (T1), plates/never-upscale (T1 checker, enforced T3-T8, T10), film facade (T1 JS, T3), regional imagery + colophon (T7, T8), SVG map (T8), redirects/robots/sitemap (T9), static conversion + Heroku interim (T9), PR checklist (T10). Cloudflare cutover itself is a manual post-merge step per spec, not a task.
- The `ext/` folder caveat is carried in Task 6 because the pre-plan listing was truncated; the manifest from Task 2 is the source of truth.
- Task 7 must run in the main session (Wikimedia search + Owen's approval via the companion); all other tasks are subagent-safe.
