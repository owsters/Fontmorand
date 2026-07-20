# Fontmorand Visual Revision Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the text-only homepage hero and small-thumbnail-grid galleries (Photos, Area) with a full-bleed lead-photo + editorial-supporting-shots pattern, and fix the Area page's text/photo overlap bug by removing the side-by-side layout that caused it.

**Architecture:** Pure CSS/markup change on top of the existing foundation from the modernisation plan (`web/includes/*`, `web/css/site.css`, `web/js/{site,lightbox}.js` - all already built, unchanged by this plan). No new JS: the existing `lightbox.js` already treats any `.gallery-item` inside a `.gallery` container generically, and `site.js`'s tab logic already treats any `.tab-panel` inside a `.tab-group` generically - both keep working as long as the new markup keeps those same class/structure conventions.

**Tech Stack:** Hand-written CSS (no build step), PHP includes already in place. No new dependencies.

## Global Constraints

- No build tools, no jQuery, no Bootstrap (unchanged from the original plan).
- British English spelling; no em dashes (use " - " instead).
- Lead photos use the existing optimised full-size image (already capped at 1600px long edge) directly, not a thumbnail. Supporting shots continue to use the existing `_tn.jpg` thumbnails - no new image files, no changes to `scripts/optimize-images.sh` or any image binary.
- Every lead photo and every supporting shot must remain a `.gallery-item` inside the same `.gallery` container it was in before, so the lightbox's next/prev still cycles through every photo in that tab (lead photo included) exactly as before - photo count per tab must not change.
- Details and History pages are explicitly out of scope - do not touch `web/pages/details.php` or `web/pages/history.php`.
- Page structure/navigation is unchanged - do not touch `web/includes/nav.php`.
- This project has no automated test framework - verification is `php -l` + `php -S`/curl/grep, not a test suite.

---

## File Structure

```
web/
├── css/site.css       (modify: add .full-bleed, .hero-photo, .lead-photo, .prose-block, .supporting-row/.supporting-item/.supporting-caption + responsive rules)
├── index.php           (modify: replace the text-only .hero block with a full-bleed hero-photo)
└── pages/
    ├── photos.php       (modify: each of the 4 tab-panels gets a lead photo + supporting-row instead of a card-grid)
    └── area.php          (modify: each of the 2 tab-panels gets a lead photo + prose-block + supporting-row instead of the side-by-side .entry)
```

---

### Task 1: New CSS components - full-bleed, hero photo, lead photo, prose block, supporting row

**Files:**
- Modify: `web/css/site.css:271-280`

**Interfaces:**
- Produces: CSS classes `.full-bleed`, `.hero-photo`, `.hero-photo-caption`, `.lead-photo` (modifier on `.hero-photo`), `.prose-block`, `.supporting-row`, `.supporting-item`, `.supporting-caption` - all consumed by Tasks 2-4.
- Consumes: existing `:root` variables (`--color-*`, `--font-*`, `--space-unit`, `--max-width`) already defined at the top of `site.css` - do not redefine them.

- [ ] **Step 1: Insert the new component rules immediately before the existing `@media` block**

In `web/css/site.css`, find this exact block (currently lines 269-273):

```css
.modal-overlay.is-open { display: flex; }

.modal-box { background: var(--color-surface); padding: calc(var(--space-unit) * 4); max-width: 480px; width: 90%; }

@media (max-width: 768px) {
```

Replace it with:

```css
.modal-overlay.is-open { display: flex; }

.modal-box { background: var(--color-surface); padding: calc(var(--space-unit) * 4); max-width: 480px; width: 90%; }

.full-bleed {
  width: 100vw;
  margin-left: calc(50% - 50vw);
  margin-right: calc(50% - 50vw);
}

.hero-photo {
  position: relative;
  overflow: hidden;
  display: block;
}

.hero-photo img {
  width: 100%;
  height: 60vh;
  max-height: 560px;
  object-fit: cover;
  display: block;
}

.hero-photo::after {
  content: "";
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, rgba(20, 18, 15, 0) 45%, rgba(20, 18, 15, 0.68) 100%);
  pointer-events: none;
}

.hero-photo-caption {
  position: absolute;
  left: 0;
  right: 0;
  bottom: 0;
  padding: calc(var(--space-unit) * 4);
  color: #f4f1ec;
  z-index: 1;
}

.hero-photo-caption h1 {
  font-size: 40px;
  color: #f4f1ec;
  margin-bottom: calc(var(--space-unit));
}

.hero-photo-caption p {
  font-size: 15px;
  max-width: 640px;
  margin: 0;
}

.lead-photo img { height: 40vh; max-height: 420px; }

.lead-photo .hero-photo-caption {
  padding: calc(var(--space-unit) * 2) calc(var(--space-unit) * 3);
  font-style: italic;
  font-size: 13px;
}

.prose-block {
  max-width: 640px;
  margin: 0 auto;
  padding: calc(var(--space-unit) * 3) calc(var(--space-unit) * 3) 0;
}

.supporting-row {
  display: flex;
  flex-wrap: wrap;
  gap: calc(var(--space-unit) * 2);
  max-width: var(--max-width);
  margin: 0 auto;
  padding: calc(var(--space-unit) * 3) calc(var(--space-unit) * 3);
}

.supporting-item {
  display: block;
  flex: 1 1 200px;
  color: var(--color-text);
}

.supporting-item img {
  width: 100%;
  height: 160px;
  object-fit: cover;
  display: block;
  transition: transform 0.15s ease;
}

.supporting-item:hover img { transform: translateY(-3px); }

.supporting-caption {
  display: block;
  font-size: 12px;
  color: var(--color-text-muted);
  margin-top: calc(var(--space-unit));
}

@media (max-width: 768px) {
```

- [ ] **Step 2: Add responsive overrides inside the existing media query**

Find this exact block (the current contents of the `@media (max-width: 768px)` rule):

```css
  .nav-toggle { display: inline-block; }
  .nav-list { display: none; width: 100%; }
  .nav-list.is-open { display: block; }
  .nav-list ul { flex-direction: column; gap: calc(var(--space-unit) * 2); padding: calc(var(--space-unit) * 2) 0; }
  .entry { flex-direction: column; }
  .entry img { width: 100%; height: auto; max-height: 240px; }
}
```

Replace it with:

```css
  .nav-toggle { display: inline-block; }
  .nav-list { display: none; width: 100%; }
  .nav-list.is-open { display: block; }
  .nav-list ul { flex-direction: column; gap: calc(var(--space-unit) * 2); padding: calc(var(--space-unit) * 2) 0; }
  .entry { flex-direction: column; }
  .entry img { width: 100%; height: auto; max-height: 240px; }
  .hero-photo img { height: 42vh; }
  .lead-photo img { height: 32vh; }
  .hero-photo-caption h1 { font-size: 28px; }
  .supporting-row { padding: calc(var(--space-unit) * 2); }
}
```

- [ ] **Step 3: Verify the CSS is syntactically sane**

```bash
python3 -c "
content = open('web/css/site.css').read()
assert content.count('{') == content.count('}'), 'brace mismatch'
for cls in ['.full-bleed', '.hero-photo', '.hero-photo-caption', '.lead-photo', '.prose-block', '.supporting-row', '.supporting-item', '.supporting-caption']:
    assert cls in content, f'{cls} missing'
print('OK')
"
```
Expected: `OK`

- [ ] **Step 4: Commit**

```bash
git add web/css/site.css
git commit -m "Add full-bleed hero, lead-photo, and supporting-row CSS components"
```

---

### Task 2: Homepage - full-bleed photo hero

**Files:**
- Modify: `web/index.php:9-12`

**Interfaces:**
- Consumes: `.full-bleed`, `.hero-photo`, `.hero-photo-caption` from Task 1.

- [ ] **Step 1: Replace the text-only hero block**

In `web/index.php`, find:

```php
<div class="hero">
  <h1>Fontmorand</h1>
  <p>A beautiful, secluded Maison de Maître, set in 3.5 hectares of private, picturesque countryside in central France.</p>
</div>
```

Replace with:

```php
<div class="full-bleed hero-photo">
  <img src="pages/img/ext/01.jpg" alt="Fontmorand, viewed from across the lake">
  <div class="hero-photo-caption">
    <h1>Fontmorand</h1>
    <p>A beautiful, secluded Maison de Maître, set in 3.5 hectares of private, picturesque countryside in central France.</p>
  </div>
</div>
```

- [ ] **Step 2: Verify**

```bash
php -l web/index.php
php -S localhost:8000 -t web >/tmp/fontmorand-smoke.log 2>&1 &
SERVER_PID=$!
sleep 1
curl -s http://localhost:8000/index.php | grep -q 'class="full-bleed hero-photo"' && echo PASS_HERO
curl -s http://localhost:8000/index.php | grep -q 'pages/img/ext/01.jpg' && echo PASS_IMG
kill $SERVER_PID
```
Expected: `PASS_HERO` and `PASS_IMG` both printed.

- [ ] **Step 3: Commit**

```bash
git add web/index.php
git commit -m "Replace text-only homepage hero with a full-bleed photo hero"
```

---

### Task 3: Photos page - lead photo + supporting row per tab

**Files:**
- Modify: `web/pages/photos.php:21-73`

**Interfaces:**
- Consumes: `.full-bleed`, `.hero-photo`/`.lead-photo`, `.supporting-row`/`.supporting-item`/`.supporting-caption` from Task 1. `.gallery`/`.gallery-item` (from the original plan's Task 1 foundation, already in `web/css/site.css` conventions used by `js/lightbox.js`) must still wrap every lead + supporting photo in each tab.
- Produces: no change to tab ids (`ext`, `int`, `ob`, `grd`) or `data-tab-target` values - `site.js`'s tab-switching logic is untouched by this task.

- [ ] **Step 1: Replace all four tab-panels**

In `web/pages/photos.php`, find the block starting at `<div id="ext" class="tab-panel is-active">` and ending at the closing `</div>` right before the line `</div>` that closes `.tab-group` (i.e. replace everything from the `ext` panel through the end of the `grd` panel - lines 21-73 of the current file):

```php
    <div id="ext" class="tab-panel is-active">
      <div class="card-grid gallery">
        <a class="card gallery-item" href="img/ext/01.jpg" data-caption="Main view of the house."><img src="img/ext/01_tn.jpg" alt="Fontmorand"><h6>Fontmorand</h6><p>Main view of the house.</p></a>
        <a class="card gallery-item" href="img/ext/02.jpg" data-caption="Front view, reflected on the lake."><img src="img/ext/02_tn.jpg" alt="Reflection"><h6>Reflection</h6><p>Front view, reflected on the lake.</p></a>
        <a class="card gallery-item" href="img/ext/03.jpg" data-caption="Beautiful view of the house in bloom."><img src="img/ext/03_tn.jpg" alt="In Bloom"><h6>In Bloom</h6><p>Beautiful view of the house in bloom.</p></a>
        <a class="card gallery-item" href="img/ext/04.jpg" data-caption="View of all buildings from across the lake."><img src="img/ext/04_tn.jpg" alt="Whole Property"><h6>Whole Property</h6><p>View of all buildings from across the lake.</p></a>
        <a class="card gallery-item" href="img/ext/05.jpg" data-caption="Side view from the edge of the games field."><img src="img/ext/05_tn.jpg" alt="Side View"><h6>Side View</h6><p>Side view from the edge of the games field.</p></a>
        <a class="card gallery-item" href="img/ext/06.jpg" data-caption="Close-up side view of the main house."><img src="img/ext/06_tn.jpg" alt="Side View"><h6>Side View</h6><p>Close-up side view of the main house.</p></a>
        <a class="card gallery-item" href="img/ext/07.jpg" data-caption="Rear view of the house from the track."><img src="img/ext/07_tn.jpg" alt="Rear View"><h6>Rear View</h6><p>Rear view of the house from the track.</p></a>
        <a class="card gallery-item" href="img/ext/08.jpg" data-caption="Aerial view of Fontmorand."><img src="img/ext/08_tn.jpg" alt="Aerial View"><h6>Aerial View</h6><p>Aerial view of Fontmorand.</p></a>
      </div>
    </div>

    <div id="int" class="tab-panel">
      <div class="card-grid gallery">
        <a class="card gallery-item" href="img/int/01.jpg" data-caption="Kitchen from hall doorway."><img src="img/int/01_tn.jpg" alt="Kitchen"><h6>Kitchen</h6><p>Kitchen from hall doorway.</p></a>
        <a class="card gallery-item" href="img/int/02.jpg" data-caption="Kitchen from side door."><img src="img/int/02_tn.jpg" alt="Kitchen"><h6>Kitchen</h6><p>Kitchen from side door.</p></a>
        <a class="card gallery-item" href="img/int/03.jpg" data-caption="Kitchen cupboards and appliances."><img src="img/int/03_tn.jpg" alt="Kitchen"><h6>Kitchen</h6><p>Kitchen cupboards and appliances.</p></a>
        <a class="card gallery-item" href="img/int/04.jpg" data-caption="Cosy fireplace in the kitchen."><img src="img/int/04_tn.jpg" alt="Kitchen"><h6>Kitchen</h6><p>Cosy fireplace in the kitchen.</p></a>
        <a class="card gallery-item" href="img/int/05.jpg" data-caption="Entrance hall, viewed from the staircase."><img src="img/int/05_tn.jpg" alt="Entrance Hall"><h6>Entrance Hall</h6><p>Entrance hall, viewed from the staircase.</p></a>
        <a class="card gallery-item" href="img/int/06.jpg" data-caption="View of the staircase from the front door."><img src="img/int/06_tn.jpg" alt="Entrance Hall"><h6>Entrance Hall</h6><p>View of the staircase from the front door.</p></a>
        <a class="card gallery-item" href="img/int/07.jpg" data-caption="Main dining area in the front room."><img src="img/int/07_tn.jpg" alt="Front Room"><h6>Front Room</h6><p>Main dining area in the front room.</p></a>
        <a class="card gallery-item" href="img/int/08.jpg" data-caption="Main sitting area in the front room."><img src="img/int/08_tn.jpg" alt="Front Room"><h6>Front Room</h6><p>Main sitting area in the front room.</p></a>
        <a class="card gallery-item" href="img/int/09.jpg" data-caption="Cosy fireplace in the front room."><img src="img/int/09_tn.jpg" alt="Front Room"><h6>Front Room</h6><p>Cosy fireplace in the front room.</p></a>
        <a class="card gallery-item" href="img/int/10.jpg" data-caption="Master bedroom."><img src="img/int/10_tn.jpg" alt="Master Bedroom"><h6>Master Bedroom</h6><p>Master bedroom.</p></a>
        <a class="card gallery-item" href="img/int/11.jpg" data-caption="Guest room 1."><img src="img/int/11_tn.jpg" alt="Guest Room 1"><h6>Guest Room 1</h6><p></p></a>
        <a class="card gallery-item" href="img/int/12.jpg" data-caption="Guest room 2."><img src="img/int/12_tn.jpg" alt="Guest Room 2"><h6>Guest Room 2</h6><p></p></a>
      </div>
    </div>

    <div id="ob" class="tab-panel">
      <div class="card-grid gallery">
        <a class="card gallery-item" href="img/out/01.jpg" data-caption="View of out-buildings from the lake."><img src="img/out/01_tn.jpg" alt="Out-Buildings"><h6>Out-Buildings</h6><p>View of out-buildings from the lake.</p></a>
        <a class="card gallery-item" href="img/out/02.jpg" data-caption="View of out-buildings from the entrance."><img src="img/out/02_tn.jpg" alt="Entrance Gate"><h6>Entrance Gate</h6><p>View of out-buildings from the entrance.</p></a>
        <a class="card gallery-item" href="img/out/04.jpg" data-caption="Front view of the gatekeeper's cottage."><img src="img/out/04_tn.jpg" alt="Cottage"><h6>Cottage</h6><p>Front view of the gatekeeper's cottage.</p></a>
        <a class="card gallery-item" href="img/out/05.jpg" data-caption="View of the open barn from the paddock."><img src="img/out/05_tn.jpg" alt="Open Barn"><h6>Open Barn</h6><p>View of the open barn from the paddock.</p></a>
        <a class="card gallery-item" href="img/out/06.jpg" data-caption="16th-century roof lattice-work."><img src="img/out/06_tn.jpg" alt="Open Barn"><h6>Open Barn</h6><p>16th-century roof lattice-work.</p></a>
        <a class="card gallery-item" href="img/out/07.jpg" data-caption="A view of the open barn."><img src="img/out/07_tn.jpg" alt="Open Barn"><h6>Open Barn</h6><p>A view of the open barn.</p></a>
        <a class="card gallery-item" href="img/out/08.jpg" data-caption="Lean-to on the end of the open barn."><img src="img/out/08_tn.jpg" alt="Lean-to"><h6>Lean-to</h6><p>Lean-to on the end of the open barn.</p></a>
        <a class="card gallery-item" href="img/out/09.jpg" data-caption="Closed barn, from the main garden."><img src="img/out/09_tn.jpg" alt="Closed Barn"><h6>Closed Barn</h6><p>Closed barn, from the main garden.</p></a>
        <a class="card gallery-item" href="img/out/10.jpg" data-caption="The porcherie, a closed barn."><img src="img/out/10_tn.jpg" alt="Porcherie"><h6>Porcherie</h6><p>The porcherie, a closed barn.</p></a>
      </div>
    </div>

    <div id="grd" class="tab-panel">
      <div class="card-grid gallery">
        <a class="card gallery-item" href="img/grd/01.jpg" data-caption="View of the big lake from the house."><img src="img/grd/01_tn.jpg" alt="Big Lake"><h6>Big Lake</h6><p>View of the big lake from the house.</p></a>
        <a class="card gallery-item" href="img/grd/02.jpg" data-caption="View of the big lake from the closed barn."><img src="img/grd/02_tn.jpg" alt="Big Lake"><h6>Big Lake</h6><p>View of the big lake from the closed barn.</p></a>
        <a class="card gallery-item" href="img/grd/03.jpg" data-caption="View of the small lake."><img src="img/grd/03_tn.jpg" alt="Small Lake"><h6>Small Lake</h6><p>View of the small lake.</p></a>
        <a class="card gallery-item" href="img/grd/04.jpg" data-caption="The font of Fontmorand."><img src="img/grd/04_tn.jpg" alt="Font"><h6>Font</h6><p>The font of Fontmorand.</p></a>
        <a class="card gallery-item" href="img/grd/05.jpg" data-caption="View of the house over the paddock."><img src="img/grd/05_tn.jpg" alt="Paddock"><h6>Paddock</h6><p>View of the house over the paddock.</p></a>
      </div>
    </div>
```

Replace with:

```php
    <div id="ext" class="tab-panel is-active">
      <div class="gallery">
        <a class="gallery-item hero-photo lead-photo full-bleed" href="img/ext/01.jpg" data-caption="Main view of the house.">
          <img src="img/ext/01.jpg" alt="Fontmorand">
          <span class="hero-photo-caption">Main view of the house</span>
        </a>
        <div class="supporting-row">
          <a class="gallery-item supporting-item" href="img/ext/02.jpg" data-caption="Front view, reflected on the lake."><img src="img/ext/02_tn.jpg" alt="Reflection"><span class="supporting-caption">Reflection</span></a>
          <a class="gallery-item supporting-item" href="img/ext/03.jpg" data-caption="Beautiful view of the house in bloom."><img src="img/ext/03_tn.jpg" alt="In Bloom"><span class="supporting-caption">In Bloom</span></a>
          <a class="gallery-item supporting-item" href="img/ext/04.jpg" data-caption="View of all buildings from across the lake."><img src="img/ext/04_tn.jpg" alt="Whole Property"><span class="supporting-caption">Whole Property</span></a>
          <a class="gallery-item supporting-item" href="img/ext/05.jpg" data-caption="Side view from the edge of the games field."><img src="img/ext/05_tn.jpg" alt="Side View"><span class="supporting-caption">Side View</span></a>
          <a class="gallery-item supporting-item" href="img/ext/06.jpg" data-caption="Close-up side view of the main house."><img src="img/ext/06_tn.jpg" alt="Side View"><span class="supporting-caption">Side View</span></a>
          <a class="gallery-item supporting-item" href="img/ext/07.jpg" data-caption="Rear view of the house from the track."><img src="img/ext/07_tn.jpg" alt="Rear View"><span class="supporting-caption">Rear View</span></a>
          <a class="gallery-item supporting-item" href="img/ext/08.jpg" data-caption="Aerial view of Fontmorand."><img src="img/ext/08_tn.jpg" alt="Aerial View"><span class="supporting-caption">Aerial View</span></a>
        </div>
      </div>
    </div>

    <div id="int" class="tab-panel">
      <div class="gallery">
        <a class="gallery-item hero-photo lead-photo full-bleed" href="img/int/05.jpg" data-caption="Entrance hall, viewed from the staircase.">
          <img src="img/int/05.jpg" alt="Entrance Hall">
          <span class="hero-photo-caption">Entrance hall, viewed from the staircase</span>
        </a>
        <div class="supporting-row">
          <a class="gallery-item supporting-item" href="img/int/01.jpg" data-caption="Kitchen from hall doorway."><img src="img/int/01_tn.jpg" alt="Kitchen"><span class="supporting-caption">Kitchen</span></a>
          <a class="gallery-item supporting-item" href="img/int/02.jpg" data-caption="Kitchen from side door."><img src="img/int/02_tn.jpg" alt="Kitchen"><span class="supporting-caption">Kitchen</span></a>
          <a class="gallery-item supporting-item" href="img/int/03.jpg" data-caption="Kitchen cupboards and appliances."><img src="img/int/03_tn.jpg" alt="Kitchen"><span class="supporting-caption">Kitchen</span></a>
          <a class="gallery-item supporting-item" href="img/int/04.jpg" data-caption="Cosy fireplace in the kitchen."><img src="img/int/04_tn.jpg" alt="Kitchen"><span class="supporting-caption">Kitchen</span></a>
          <a class="gallery-item supporting-item" href="img/int/06.jpg" data-caption="View of the staircase from the front door."><img src="img/int/06_tn.jpg" alt="Entrance Hall"><span class="supporting-caption">Entrance Hall</span></a>
          <a class="gallery-item supporting-item" href="img/int/07.jpg" data-caption="Main dining area in the front room."><img src="img/int/07_tn.jpg" alt="Front Room"><span class="supporting-caption">Front Room</span></a>
          <a class="gallery-item supporting-item" href="img/int/08.jpg" data-caption="Main sitting area in the front room."><img src="img/int/08_tn.jpg" alt="Front Room"><span class="supporting-caption">Front Room</span></a>
          <a class="gallery-item supporting-item" href="img/int/09.jpg" data-caption="Cosy fireplace in the front room."><img src="img/int/09_tn.jpg" alt="Front Room"><span class="supporting-caption">Front Room</span></a>
          <a class="gallery-item supporting-item" href="img/int/10.jpg" data-caption="Master bedroom."><img src="img/int/10_tn.jpg" alt="Master Bedroom"><span class="supporting-caption">Master Bedroom</span></a>
          <a class="gallery-item supporting-item" href="img/int/11.jpg" data-caption="Guest room 1."><img src="img/int/11_tn.jpg" alt="Guest Room 1"><span class="supporting-caption">Guest Room 1</span></a>
          <a class="gallery-item supporting-item" href="img/int/12.jpg" data-caption="Guest room 2."><img src="img/int/12_tn.jpg" alt="Guest Room 2"><span class="supporting-caption">Guest Room 2</span></a>
        </div>
      </div>
    </div>

    <div id="ob" class="tab-panel">
      <div class="gallery">
        <a class="gallery-item hero-photo lead-photo full-bleed" href="img/out/01.jpg" data-caption="View of out-buildings from the lake.">
          <img src="img/out/01.jpg" alt="Out-Buildings">
          <span class="hero-photo-caption">View of out-buildings from the lake</span>
        </a>
        <div class="supporting-row">
          <a class="gallery-item supporting-item" href="img/out/02.jpg" data-caption="View of out-buildings from the entrance."><img src="img/out/02_tn.jpg" alt="Entrance Gate"><span class="supporting-caption">Entrance Gate</span></a>
          <a class="gallery-item supporting-item" href="img/out/04.jpg" data-caption="Front view of the gatekeeper's cottage."><img src="img/out/04_tn.jpg" alt="Cottage"><span class="supporting-caption">Cottage</span></a>
          <a class="gallery-item supporting-item" href="img/out/05.jpg" data-caption="View of the open barn from the paddock."><img src="img/out/05_tn.jpg" alt="Open Barn"><span class="supporting-caption">Open Barn</span></a>
          <a class="gallery-item supporting-item" href="img/out/06.jpg" data-caption="16th-century roof lattice-work."><img src="img/out/06_tn.jpg" alt="Open Barn"><span class="supporting-caption">Open Barn</span></a>
          <a class="gallery-item supporting-item" href="img/out/07.jpg" data-caption="A view of the open barn."><img src="img/out/07_tn.jpg" alt="Open Barn"><span class="supporting-caption">Open Barn</span></a>
          <a class="gallery-item supporting-item" href="img/out/08.jpg" data-caption="Lean-to on the end of the open barn."><img src="img/out/08_tn.jpg" alt="Lean-to"><span class="supporting-caption">Lean-to</span></a>
          <a class="gallery-item supporting-item" href="img/out/09.jpg" data-caption="Closed barn, from the main garden."><img src="img/out/09_tn.jpg" alt="Closed Barn"><span class="supporting-caption">Closed Barn</span></a>
          <a class="gallery-item supporting-item" href="img/out/10.jpg" data-caption="The porcherie, a closed barn."><img src="img/out/10_tn.jpg" alt="Porcherie"><span class="supporting-caption">Porcherie</span></a>
        </div>
      </div>
    </div>

    <div id="grd" class="tab-panel">
      <div class="gallery">
        <a class="gallery-item hero-photo lead-photo full-bleed" href="img/grd/01.jpg" data-caption="View of the big lake from the house.">
          <img src="img/grd/01.jpg" alt="Big Lake">
          <span class="hero-photo-caption">View of the big lake from the house</span>
        </a>
        <div class="supporting-row">
          <a class="gallery-item supporting-item" href="img/grd/02.jpg" data-caption="View of the big lake from the closed barn."><img src="img/grd/02_tn.jpg" alt="Big Lake"><span class="supporting-caption">Big Lake</span></a>
          <a class="gallery-item supporting-item" href="img/grd/03.jpg" data-caption="View of the small lake."><img src="img/grd/03_tn.jpg" alt="Small Lake"><span class="supporting-caption">Small Lake</span></a>
          <a class="gallery-item supporting-item" href="img/grd/04.jpg" data-caption="The font of Fontmorand."><img src="img/grd/04_tn.jpg" alt="Font"><span class="supporting-caption">Font</span></a>
          <a class="gallery-item supporting-item" href="img/grd/05.jpg" data-caption="View of the house over the paddock."><img src="img/grd/05_tn.jpg" alt="Paddock"><span class="supporting-caption">Paddock</span></a>
        </div>
      </div>
    </div>
```

- [ ] **Step 2: Verify**

```bash
php -l web/pages/photos.php
php -S localhost:8000 -t web >/tmp/fontmorand-smoke.log 2>&1 &
SERVER_PID=$!
sleep 1
curl -s http://localhost:8000/pages/photos.php | grep -c 'gallery-item' # expect 34 (8+12+9+5) - same total as before
curl -s http://localhost:8000/pages/photos.php | grep -c 'lead-photo' # expect 4 (one per tab)
kill $SERVER_PID
```
Expected: 34 gallery-items total, 4 lead-photos (one per tab).

- [ ] **Step 3: Commit**

```bash
git add web/pages/photos.php
git commit -m "Restructure Photos page tabs onto lead-photo + supporting-row layout"
```

---

### Task 4: Area page - lead photo + prose block + supporting row per tab (fixes the overlap bug)

**Files:**
- Modify: `web/pages/area.php:19-51`

**Interfaces:**
- Consumes: `.full-bleed`, `.hero-photo`/`.lead-photo`, `.prose-block`, `.supporting-row`/`.supporting-item`/`.supporting-caption` from Task 1.
- Produces: no change to tab ids (`creuse`, `brenne`) or `data-tab-target` values.

- [ ] **Step 1: Replace both tab-panels**

In `web/pages/area.php`, find the block from `<div id="creuse" class="tab-panel is-active">` through the closing `</div>` at the end of the `brenne` panel (lines 19-51 of the current file):

```php
    <div id="creuse" class="tab-panel is-active">
      <div class="entry">
        <div class="card-grid gallery" style="grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); max-width: 220px;">
          <a class="card gallery-item" href="img/region/creuse.jpg" data-caption="The river Creuse"><img src="img/region/creuse_tn.jpg" alt="Creuse Valley"></a>
          <a class="card gallery-item" href="img/region/barrage.jpg" data-caption="Barrage at Eguzon"><img src="img/region/barrage_tn.jpg" alt="Barrage"></a>
          <a class="card gallery-item" href="img/region/gargilesse.jpg" data-caption="Cathedral at Gargilesse"><img src="img/region/gargilesse_tn.jpg" alt="Gargilesse"></a>
          <a class="card gallery-item" href="img/region/eguzon.jpg" data-caption="Lac de Chambon at Eguzon"><img src="img/region/eguzon_tn.jpg" alt="Eguzon"></a>
          <a class="card gallery-item" href="img/region/canoe.jpg" data-caption="Canoeing on the river Creuse"><img src="img/region/canoe_tn.jpg" alt="Canoe"></a>
        </div>
        <div class="entry-body">
          <h3>The Creuse Valley</h3>
          <p>The area has museums, châteaux, tennis courts, swimming and fishing lakes, riding, and quality restaurants specialising in local produce. The Creuse Valley is often called "secret" France, as many visitors drive through it without stopping, on their way to the more crowded south.</p>
          <p>It is beautiful, and greener than the south. About 30 minutes' drive away is the huge Lac de Chambon, a man-made lake formed by damming the river Creuse near the picturesque town of Eguzon, which also provides the area with hydro-electric power.</p>
          <p>The lake offers a wide range of water sports, including swimming with offshore pontoons for diving, water-skiing, sailing and pedalos - plenty for children of all ages.</p>
          <p>Nearby towns include the ancient St Benoit-du-Sault, about ten kilometres away and rated one of the prettiest villages in France - a medieval village perched on a craggy granite outcrop, full of tiny winding streets and monuments. Argenton-sur-Creuse is 20 minutes away, is part of the national rail network, and has good restaurants, plenty of shops and a Roman excavation site with a museum. More recent history can be found in the village of Oradour-sur-Glane, preserved as a memorial to the Second World War. The city of Poitiers is about 90 minutes away, home to the Futuroscope theme park. Limoges is within an hour by car, famous for its porcelain and cathedral, and has a wide range of shops including Galeries Lafayette.</p>
          <p>The elegant château town of Le Blanc is about 25 minutes away, with a specialised fish market, cafes and restaurants. Canoes can also be hired there, to row up or down the river.</p>
        </div>
      </div>
    </div>

    <div id="brenne" class="tab-panel">
      <div class="entry">
        <div class="card-grid gallery" style="grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); max-width: 220px;">
          <a class="card gallery-item" href="img/region/brenne1.jpg" data-caption="Brenne countryside"><img src="img/region/brenne1_tn.jpg" alt="Brenne Countryside"></a>
          <a class="card gallery-item" href="img/region/brenne2.jpg" data-caption="Brenne wildlife"><img src="img/region/brenne2_tn.jpg" alt="Brenne Wildlife"></a>
        </div>
        <div class="entry-body">
          <h3>The Brenne National Park</h3>
          <p>Also known as the land of a thousand lakes, the house is surrounded by the trees, grassland and lakes of the Brenne, one of France's best-loved national parks. Wildlife here includes deer, wildcats, turtles and otters; eagles can often be seen, and buzzards are abundant enough to surprise you with how close they come. The Brenne is dotted with hundreds of lakes and is well worth a visit for its unusual bird life. Other pleasures of the area include fishing, walking, canoeing, riding and cycling.</p>
          <p>Fishing is a rural passion in France, and visitors to the region can fish the rivers, which hold a mixture of fish including trout. There is a public fishing lake directly opposite Fontmorand, containing some giant carp. For serious anglers, carp and other coarse fishing can be found throughout the Berry region, particularly in the Brenne. There are also several golf courses within an hour's drive, including the Val de l'Indre Golf Club at Villedieu-sur-Indre, La Porcelaine Golf Club near Limoges, and Limoges-St Lazare Golf Club, among others.</p>
        </div>
      </div>
    </div>
```

Replace with:

```php
    <div id="creuse" class="tab-panel is-active">
      <div class="gallery">
        <a class="gallery-item hero-photo lead-photo full-bleed" href="img/region/creuse.jpg" data-caption="The river Creuse">
          <img src="img/region/creuse.jpg" alt="Creuse Valley">
          <span class="hero-photo-caption">The river Creuse</span>
        </a>
        <div class="prose-block">
          <h3>The Creuse Valley</h3>
          <p>The area has museums, châteaux, tennis courts, swimming and fishing lakes, riding, and quality restaurants specialising in local produce. The Creuse Valley is often called "secret" France, as many visitors drive through it without stopping, on their way to the more crowded south.</p>
          <p>It is beautiful, and greener than the south. About 30 minutes' drive away is the huge Lac de Chambon, a man-made lake formed by damming the river Creuse near the picturesque town of Eguzon, which also provides the area with hydro-electric power.</p>
          <p>The lake offers a wide range of water sports, including swimming with offshore pontoons for diving, water-skiing, sailing and pedalos - plenty for children of all ages.</p>
          <p>Nearby towns include the ancient St Benoit-du-Sault, about ten kilometres away and rated one of the prettiest villages in France - a medieval village perched on a craggy granite outcrop, full of tiny winding streets and monuments. Argenton-sur-Creuse is 20 minutes away, is part of the national rail network, and has good restaurants, plenty of shops and a Roman excavation site with a museum. More recent history can be found in the village of Oradour-sur-Glane, preserved as a memorial to the Second World War. The city of Poitiers is about 90 minutes away, home to the Futuroscope theme park. Limoges is within an hour by car, famous for its porcelain and cathedral, and has a wide range of shops including Galeries Lafayette.</p>
          <p>The elegant château town of Le Blanc is about 25 minutes away, with a specialised fish market, cafes and restaurants. Canoes can also be hired there, to row up or down the river.</p>
        </div>
        <div class="supporting-row">
          <a class="gallery-item supporting-item" href="img/region/barrage.jpg" data-caption="Barrage at Eguzon"><img src="img/region/barrage_tn.jpg" alt="Barrage"><span class="supporting-caption">Barrage at Eguzon</span></a>
          <a class="gallery-item supporting-item" href="img/region/gargilesse.jpg" data-caption="Cathedral at Gargilesse"><img src="img/region/gargilesse_tn.jpg" alt="Gargilesse"><span class="supporting-caption">Gargilesse</span></a>
          <a class="gallery-item supporting-item" href="img/region/eguzon.jpg" data-caption="Lac de Chambon at Eguzon"><img src="img/region/eguzon_tn.jpg" alt="Eguzon"><span class="supporting-caption">Lac de Chambon</span></a>
          <a class="gallery-item supporting-item" href="img/region/canoe.jpg" data-caption="Canoeing on the river Creuse"><img src="img/region/canoe_tn.jpg" alt="Canoe"><span class="supporting-caption">Canoeing</span></a>
        </div>
      </div>
    </div>

    <div id="brenne" class="tab-panel">
      <div class="gallery">
        <a class="gallery-item hero-photo lead-photo full-bleed" href="img/region/brenne1.jpg" data-caption="Brenne countryside">
          <img src="img/region/brenne1.jpg" alt="Brenne Countryside">
          <span class="hero-photo-caption">Brenne countryside</span>
        </a>
        <div class="prose-block">
          <h3>The Brenne National Park</h3>
          <p>Also known as the land of a thousand lakes, the house is surrounded by the trees, grassland and lakes of the Brenne, one of France's best-loved national parks. Wildlife here includes deer, wildcats, turtles and otters; eagles can often be seen, and buzzards are abundant enough to surprise you with how close they come. The Brenne is dotted with hundreds of lakes and is well worth a visit for its unusual bird life. Other pleasures of the area include fishing, walking, canoeing, riding and cycling.</p>
          <p>Fishing is a rural passion in France, and visitors to the region can fish the rivers, which hold a mixture of fish including trout. There is a public fishing lake directly opposite Fontmorand, containing some giant carp. For serious anglers, carp and other coarse fishing can be found throughout the Berry region, particularly in the Brenne. There are also several golf courses within an hour's drive, including the Val de l'Indre Golf Club at Villedieu-sur-Indre, La Porcelaine Golf Club near Limoges, and Limoges-St Lazare Golf Club, among others.</p>
        </div>
        <div class="supporting-row">
          <a class="gallery-item supporting-item" href="img/region/brenne2.jpg" data-caption="Brenne wildlife"><img src="img/region/brenne2_tn.jpg" alt="Brenne Wildlife"><span class="supporting-caption">Brenne wildlife</span></a>
        </div>
      </div>
    </div>
```

- [ ] **Step 2: Verify**

```bash
php -l web/pages/area.php
php -S localhost:8000 -t web >/tmp/fontmorand-smoke.log 2>&1 &
SERVER_PID=$!
sleep 1
curl -s http://localhost:8000/pages/area.php | grep -c 'gallery-item' # expect 7 (5+2) - same total as before
curl -s http://localhost:8000/pages/area.php | grep -c 'class="entry"' # expect 0 - old overlapping layout is gone
curl -s http://localhost:8000/pages/area.php | grep -q 'prose-block' && echo PASS_PROSE
kill $SERVER_PID
```
Expected: 7 gallery-items, 0 `class="entry"` matches, `PASS_PROSE` printed.

- [ ] **Step 3: Commit**

```bash
git add web/pages/area.php
git commit -m "Restructure Area page tabs onto lead-photo + prose-block + supporting-row, fixing the text/photo overlap"
```

---

## Final manual checklist (not agent-executable)

- [ ] Open the live preview and visually confirm: homepage hero photo is full-bleed with legible overlaid text at both mobile and desktop widths; Photos and Area lead photos are full-bleed with no overlap; Area's prose text no longer collides with any photo
- [ ] Click through the lightbox on Photos and Area to confirm next/prev still cycles through every photo in a tab, including the lead photo
- [ ] Push the branch update so PR #1 reflects this revision
