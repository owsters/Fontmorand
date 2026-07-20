# Fontmorand Site Modernisation Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Modernise the Fontmorand site (a live, hand-written PHP/HTML site deployed to Heroku) in place - shared PHP includes instead of duplicated markup, a hand-written "Stone Manor" CSS/JS design system replacing Bootstrap 3.1.1/jQuery, real content fixes, and a few overdue technical/security fixes - while keeping the existing Heroku deployment untouched.

**Architecture:** `web/` stays the Heroku document root. Three PHP includes (`head.php`, `nav.php`, `footer.php`) replace the duplicated `<head>`/nav markup on every page. Each of the 7 pages is rewritten in place as `.php`, using the shared includes, a single `css/site.css` stylesheet, and two small vanilla-JS files (`site.js` for nav/tabs/modal, `lightbox.js` for the photo galleries). No build step, no npm, no jQuery, no Bootstrap.

**Tech Stack:** PHP (native, Heroku's `heroku-php-apache2` buildpack - already in place), hand-written CSS with custom properties, vanilla JavaScript (ES5-compatible, no transpilation), `sips` (macOS built-in) for one-off image optimisation.

## Global Constraints

- Keep Heroku hosting; `Procfile` and the `web/` document root are unchanged - no hosting migration.
- No build tools, no npm, no bundler - hand-written PHP/CSS/JS only, served as-is.
- Bootstrap 3.1.1 and jQuery 1.10.2 are removed entirely, with no replacement framework.
- English only this round - no French translation or language switcher.
- British English spelling in all new/edited copy (e.g. "colour", "organiser", "recognised", "centre").
- No em dashes in any copy - use a hyphen-minus (` - `) instead.
- All colours and key spacing values are defined as CSS custom properties (`:root` variables), not hardcoded throughout the stylesheet.
- Every interactive element (links, buttons, cards, thumbnails) gets a visible hover state.
- Mobile responsive by default - single-column layout below 768px.

---

## File Structure

```
web/
├── includes/
│   ├── head.php        (new)
│   ├── nav.php          (new)
│   └── footer.php       (new)
├── css/
│   └── site.css         (new)
├── js/
│   ├── site.js           (new - nav toggle, tabs, modal)
│   └── lightbox.js       (new - photo gallery lightbox)
├── index.php            (new, replaces index.html)
├── pages/
│   ├── details.php       (new, replaces details.html)
│   ├── photos.php        (new, replaces photos.html)
│   ├── history.php       (new, replaces history.html)
│   ├── area.php           (new, replaces area.html)
│   ├── find-us.php        (new, replaces find-us.html)
│   ├── contact.php        (rewritten in place)
│   └── mailer.php         (deleted)
├── sitemap.xml           (new)
└── (old/, dist/, custom/, and stray Icon/_notes files - deleted)
```

---

### Task 1: Foundation - shared includes, stylesheet, and vanilla JS

**Files:**
- Create: `web/includes/head.php`
- Create: `web/includes/nav.php`
- Create: `web/includes/footer.php`
- Create: `web/css/site.css`
- Create: `web/js/site.js`
- Create: `web/js/lightbox.js`

**Interfaces:**
- Produces: `head.php` expects `$pageTitle`, `$pageDescription`, `$baseUrl` set before `require`. `nav.php` expects `$activeNav` (one of `home|details|photos|history|area|find-us|contact`) and `$baseUrl`. `footer.php` expects `$baseUrl` and optional `$extraScripts` (array of paths relative to `$baseUrl`). CSS classes later tasks rely on: `.content`, `.hero`, `.page-title`, `.btn`, `.card-grid`/`.card`, `.tab-group`/`.tab-list`/`.tab-link`/`.tab-panel`, `.entry`/`.entry-body`, `.gallery`/`.gallery-item`, `.lightbox-*`, `.form-field`/`.form-label`/`.form-input`/`.form-textarea`, `.alert`/`.alert-success`/`.alert-error`, `.modal-overlay`/`.modal-box`/`.modal-close`. JS hooks: `data-tab-target` (on `.tab-link`, value = target panel id), `data-modal-target` (on any trigger, value = target modal id), `.nav-toggle`/`.nav-list` (mobile menu).

- [ ] **Step 1: Create `web/includes/head.php`**

```php
<?php
/**
 * Expects before include:
 *   $pageTitle       string
 *   $pageDescription string
 *   $baseUrl         string  '' at site root, '../' from web/pages/*
 */
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="<?php echo htmlspecialchars($pageDescription); ?>">
<meta name="author" content="Owen Daniels">
<link rel="shortcut icon" href="<?php echo $baseUrl; ?>img/favicon.png">
<title><?php echo htmlspecialchars($pageTitle); ?></title>
<link href="<?php echo $baseUrl; ?>css/site.css" rel="stylesheet">
</head>
<body>
```

- [ ] **Step 2: Create `web/includes/nav.php`**

```php
<?php
/**
 * Expects before include:
 *   $activeNav  string  one of: home, details, photos, history, area, find-us, contact
 *   $baseUrl    string
 */
$navItems = [
  'home'    => ['label' => 'Home',    'href' => $baseUrl . 'index.php'],
  'details' => ['label' => 'Details', 'href' => $baseUrl . 'pages/details.php'],
  'photos'  => ['label' => 'Photos',  'href' => $baseUrl . 'pages/photos.php'],
  'history' => ['label' => 'History', 'href' => $baseUrl . 'pages/history.php'],
  'area'    => ['label' => 'Area',    'href' => $baseUrl . 'pages/area.php'],
  'find-us' => ['label' => 'Find Us', 'href' => $baseUrl . 'pages/find-us.php'],
  'contact' => ['label' => 'Contact', 'href' => $baseUrl . 'pages/contact.php'],
];
?>
<header class="site-header">
  <div class="site-header-inner">
    <a class="site-brand" href="<?php echo $baseUrl; ?>index.php">Fontmorand</a>
    <button type="button" class="nav-toggle" aria-label="Toggle navigation">Menu</button>
    <nav class="nav-list">
      <ul>
        <?php foreach ($navItems as $key => $item): ?>
        <li><a class="nav-link<?php echo $key === $activeNav ? ' is-active' : ''; ?>" href="<?php echo $item['href']; ?>"><?php echo $item['label']; ?></a></li>
        <?php endforeach; ?>
      </ul>
    </nav>
  </div>
</header>
```

- [ ] **Step 3: Create `web/includes/footer.php`**

```php
<?php
/**
 * Expects before include:
 *   $baseUrl       string
 *   $extraScripts  array of script paths relative to $baseUrl (optional)
 *
 * Manual step required once, before this goes live: sign up for a free
 * account at https://www.goatcounter.com, create a site, and replace
 * 'YOURCODE' below with the subdomain code GoatCounter gives you.
 */
$extraScripts = isset($extraScripts) ? $extraScripts : [];
$goatcounterCode = 'YOURCODE';
?>
<footer class="site-footer">
  <p>&copy; <?php echo date('Y'); ?> Fontmorand, Prissac, France.</p>
</footer>
<script src="<?php echo $baseUrl; ?>js/site.js"></script>
<?php foreach ($extraScripts as $script): ?>
<script src="<?php echo $baseUrl . $script; ?>"></script>
<?php endforeach; ?>
<script data-goatcounter="https://<?php echo $goatcounterCode; ?>.goatcounter.com/count" async src="//gc.zgo.at/count.js"></script>
</body>
</html>
```

- [ ] **Step 4: Create `web/css/site.css`**

```css
:root {
  --color-bg: #f4f1ec;
  --color-text: #2e2a24;
  --color-text-muted: #5b564c;
  --color-accent: #8a8377;
  --color-border: #ded8cb;
  --color-surface: #ffffff;
  --font-heading: Georgia, 'Times New Roman', serif;
  --font-body: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
  --space-unit: 8px;
  --max-width: 1080px;
}

* { box-sizing: border-box; }

body {
  margin: 0;
  background: var(--color-bg);
  color: var(--color-text);
  font-family: var(--font-body);
  line-height: 1.6;
}

h1, h2, h3, h4 {
  font-family: var(--font-heading);
  line-height: 1.2;
  margin: 0 0 calc(var(--space-unit) * 2);
}

p { margin: 0 0 calc(var(--space-unit) * 2); }

a { color: var(--color-text); text-decoration: none; }
a:hover { text-decoration: underline; }

.content {
  max-width: var(--max-width);
  margin: 0 auto;
  padding: calc(var(--space-unit) * 4) calc(var(--space-unit) * 3);
}

.site-header {
  background: var(--color-surface);
  border-bottom: 1px solid var(--color-border);
  position: sticky;
  top: 0;
  z-index: 10;
}

.site-header-inner {
  max-width: var(--max-width);
  margin: 0 auto;
  padding: calc(var(--space-unit) * 2) calc(var(--space-unit) * 3);
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
}

.site-brand {
  font-family: var(--font-heading);
  font-size: 20px;
  font-weight: bold;
}

.nav-toggle {
  display: none;
  background: none;
  border: 1px solid var(--color-border);
  padding: calc(var(--space-unit)) calc(var(--space-unit) * 2);
  font-family: var(--font-body);
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  cursor: pointer;
}

.nav-list ul {
  display: flex;
  gap: calc(var(--space-unit) * 3);
  margin: 0;
  padding: 0;
  list-style: none;
}

.nav-link {
  font-size: 12px;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--color-text-muted);
  padding-bottom: 4px;
  border-bottom: 2px solid transparent;
}

.nav-link.is-active,
.nav-link:hover {
  color: var(--color-text);
  border-bottom-color: var(--color-accent);
  text-decoration: none;
}

.hero {
  background: var(--color-surface);
  border-bottom: 1px solid var(--color-border);
  padding: calc(var(--space-unit) * 8) calc(var(--space-unit) * 3);
  text-align: center;
}

.hero h1 { font-size: 40px; margin-bottom: calc(var(--space-unit) * 2); }

.hero p {
  font-size: 18px;
  color: var(--color-text-muted);
  max-width: 640px;
  margin: 0 auto;
}

.page-title { text-align: center; margin-bottom: calc(var(--space-unit) * 4); }

.btn {
  display: inline-block;
  padding: calc(var(--space-unit)) calc(var(--space-unit) * 3);
  border: 1px solid var(--color-text);
  font-size: 12px;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  transition: transform 0.15s ease, background 0.15s ease;
  cursor: pointer;
  background: var(--color-surface);
}

.btn:hover {
  background: var(--color-text);
  color: var(--color-bg);
  text-decoration: none;
  transform: translateY(-1px);
}

.card-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
  gap: calc(var(--space-unit) * 3);
  margin: calc(var(--space-unit) * 3) 0;
}

.card {
  display: block;
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  padding: calc(var(--space-unit) * 2);
  text-align: center;
  color: var(--color-text);
  transition: transform 0.15s ease, box-shadow 0.15s ease;
}

.card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 20px rgba(46, 42, 36, 0.08);
  text-decoration: none;
}

.card img {
  width: 100%;
  height: 140px;
  object-fit: cover;
  display: block;
  margin-bottom: calc(var(--space-unit) * 1.5);
}

.card h6 { margin: 0 0 4px; font-family: var(--font-body); font-weight: bold; }
.card p { font-size: 13px; color: var(--color-text-muted); margin: 0; }

.tab-list {
  display: flex;
  flex-wrap: wrap;
  gap: calc(var(--space-unit) * 2);
  border-bottom: 1px solid var(--color-border);
  margin-bottom: calc(var(--space-unit) * 3);
  padding: 0;
  list-style: none;
}

.tab-link {
  display: inline-block;
  padding: calc(var(--space-unit)) calc(var(--space-unit) * 2);
  font-size: 13px;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  color: var(--color-text-muted);
  border-bottom: 2px solid transparent;
}

.tab-link.is-active,
.tab-link:hover {
  color: var(--color-text);
  border-bottom-color: var(--color-accent);
  text-decoration: none;
}

.tab-panel { display: none; }
.tab-panel.is-active { display: block; }

.entry {
  display: flex;
  gap: calc(var(--space-unit) * 3);
  margin-bottom: calc(var(--space-unit) * 4);
  align-items: flex-start;
}

.entry img { width: 160px; height: 160px; object-fit: cover; flex-shrink: 0; }
.entry-body h3 { margin-top: 0; }

.lightbox-overlay {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(20, 18, 15, 0.92);
  z-index: 100;
  align-items: center;
  justify-content: center;
  flex-direction: column;
}

.lightbox-overlay.is-open { display: flex; }

.lightbox-image { max-width: 90vw; max-height: 80vh; object-fit: contain; }
.lightbox-caption { color: #f4f1ec; margin-top: calc(var(--space-unit) * 2); }

.lightbox-close, .lightbox-prev, .lightbox-next {
  position: absolute;
  background: none;
  border: none;
  color: #f4f1ec;
  font-size: 32px;
  cursor: pointer;
}

.lightbox-close { top: calc(var(--space-unit) * 2); right: calc(var(--space-unit) * 3); }
.lightbox-prev { left: calc(var(--space-unit) * 3); top: 50%; transform: translateY(-50%); }
.lightbox-next { right: calc(var(--space-unit) * 3); top: 50%; transform: translateY(-50%); }

.form-field { margin-bottom: calc(var(--space-unit) * 2); }
.form-label { display: block; font-size: 13px; margin-bottom: 4px; }

.form-input, .form-textarea {
  width: 100%;
  padding: calc(var(--space-unit) * 1.5);
  border: 1px solid var(--color-border);
  background: var(--color-surface);
  font-family: var(--font-body);
  font-size: 14px;
}

.alert { padding: calc(var(--space-unit) * 2); margin-bottom: calc(var(--space-unit) * 3); border: 1px solid var(--color-border); }
.alert-success { border-color: #6b8f5c; color: #375028; background: #eef4ea; }
.alert-error { border-color: #a85b4e; color: #6e2f26; background: #f8ece9; }

.site-footer { text-align: center; padding: calc(var(--space-unit) * 4); color: var(--color-text-muted); font-size: 13px; }

.modal-overlay {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(20, 18, 15, 0.6);
  align-items: center;
  justify-content: center;
  z-index: 100;
}

.modal-overlay.is-open { display: flex; }

.modal-box { background: var(--color-surface); padding: calc(var(--space-unit) * 4); max-width: 480px; width: 90%; }

@media (max-width: 768px) {
  .nav-toggle { display: inline-block; }
  .nav-list { display: none; width: 100%; }
  .nav-list.is-open { display: block; }
  .nav-list ul { flex-direction: column; gap: calc(var(--space-unit) * 2); padding: calc(var(--space-unit) * 2) 0; }
  .entry { flex-direction: column; }
  .entry img { width: 100%; height: auto; max-height: 240px; }
}
```

- [ ] **Step 5: Create `web/js/site.js`**

```js
document.addEventListener('DOMContentLoaded', function () {
  var toggle = document.querySelector('.nav-toggle');
  var nav = document.querySelector('.nav-list');
  if (toggle && nav) {
    toggle.addEventListener('click', function () {
      nav.classList.toggle('is-open');
    });
  }

  document.querySelectorAll('.tab-group').forEach(function (group) {
    var links = group.querySelectorAll('.tab-link');
    links.forEach(function (link) {
      link.addEventListener('click', function (e) {
        e.preventDefault();
        var targetId = link.getAttribute('data-tab-target');
        var panel = document.getElementById(targetId);
        if (!panel) return;
        links.forEach(function (l) { l.classList.remove('is-active'); });
        group.querySelectorAll('.tab-panel').forEach(function (p) { p.classList.remove('is-active'); });
        link.classList.add('is-active');
        panel.classList.add('is-active');
      });
    });
  });

  document.querySelectorAll('[data-modal-target]').forEach(function (trigger) {
    trigger.addEventListener('click', function (e) {
      e.preventDefault();
      var modal = document.getElementById(trigger.getAttribute('data-modal-target'));
      if (modal) modal.classList.add('is-open');
    });
  });

  document.querySelectorAll('.modal-overlay').forEach(function (modal) {
    modal.addEventListener('click', function (e) {
      if (e.target === modal || e.target.closest('.modal-close')) {
        modal.classList.remove('is-open');
      }
    });
  });
});
```

- [ ] **Step 6: Create `web/js/lightbox.js`**

```js
document.addEventListener('DOMContentLoaded', function () {
  var overlay = document.createElement('div');
  overlay.className = 'lightbox-overlay';
  overlay.innerHTML =
    '<button class="lightbox-close" aria-label="Close">&times;</button>' +
    '<button class="lightbox-prev" aria-label="Previous">&lsaquo;</button>' +
    '<img class="lightbox-image" src="" alt="">' +
    '<p class="lightbox-caption"></p>' +
    '<button class="lightbox-next" aria-label="Next">&rsaquo;</button>';
  document.body.appendChild(overlay);

  var img = overlay.querySelector('.lightbox-image');
  var caption = overlay.querySelector('.lightbox-caption');
  var items = [];
  var index = 0;

  function show(i) {
    index = (i + items.length) % items.length;
    var item = items[index];
    img.src = item.getAttribute('href');
    caption.textContent = item.getAttribute('data-caption') || '';
    overlay.classList.add('is-open');
  }

  function close() {
    overlay.classList.remove('is-open');
  }

  document.querySelectorAll('.gallery').forEach(function (gallery) {
    var links = Array.prototype.slice.call(gallery.querySelectorAll('.gallery-item'));
    links.forEach(function (link, i) {
      link.addEventListener('click', function (e) {
        e.preventDefault();
        items = links;
        show(i);
      });
    });
  });

  overlay.querySelector('.lightbox-close').addEventListener('click', close);
  overlay.querySelector('.lightbox-prev').addEventListener('click', function () { show(index - 1); });
  overlay.querySelector('.lightbox-next').addEventListener('click', function () { show(index + 1); });
  overlay.addEventListener('click', function (e) {
    if (e.target === overlay) close();
  });
  document.addEventListener('keydown', function (e) {
    if (!overlay.classList.contains('is-open')) return;
    if (e.key === 'Escape') close();
    if (e.key === 'ArrowLeft') show(index - 1);
    if (e.key === 'ArrowRight') show(index + 1);
  });
});
```

- [ ] **Step 7: Smoke-test the foundation before any page depends on it**

Create a temporary `web/_smoke.php`:

```php
<?php
$pageTitle = 'Smoke Test';
$pageDescription = 'Foundation smoke test';
$activeNav = 'home';
$baseUrl = '';
require __DIR__ . '/includes/head.php';
require __DIR__ . '/includes/nav.php';
?>
<div class="content"><p>Smoke test content</p></div>
<?php
$extraScripts = [];
require __DIR__ . '/includes/footer.php';
?>
```

Run:
```bash
php -l web/includes/head.php && php -l web/includes/nav.php && php -l web/includes/footer.php && php -l web/_smoke.php
php -S localhost:8000 -t web >/tmp/fontmorand-smoke.log 2>&1 &
SERVER_PID=$!
sleep 1
curl -s http://localhost:8000/_smoke.php | grep -q "Smoke test content" && echo PASS_CONTENT
curl -s http://localhost:8000/_smoke.php | grep -q 'class="nav-link is-active"' && echo PASS_NAV
kill $SERVER_PID
rm web/_smoke.php
```
Expected: `PASS_CONTENT` and `PASS_NAV` both printed, no PHP syntax errors.

- [ ] **Step 8: Commit**

```bash
git add web/includes web/css web/js
git commit -m "Add shared PHP includes, Stone Manor stylesheet, and vanilla JS foundation"
```

---

### Task 2: One-time image optimisation pass

**Files:**
- Create: `scripts/optimize-images.sh`
- Modify (in place, via script): all files under `web/pages/img/{ext,int,out,grd,region}/*.jpg`

**Interfaces:**
- Consumes: nothing from Task 1.
- Produces: resized full-size images and regenerated thumbnails at the same filenames later page tasks (3, 5, 6, 7) reference directly - no filename changes, so no interface change for those tasks.

- [ ] **Step 1: Create `scripts/optimize-images.sh`**

```bash
#!/bin/bash
set -euo pipefail

IMG_DIR="web/pages/img"
MAX_DIM=1600
THUMB_WIDTH=220
QUALITY=75

for dir in ext int out grd region; do
  for full in "$IMG_DIR/$dir"/*.jpg; do
    base=$(basename "$full")
    case "$base" in *_tn.jpg) continue ;; esac
    echo "Resizing $full"
    sips -Z "$MAX_DIM" --setProperty formatOptions "$QUALITY" "$full" >/dev/null
    thumb="${full%.jpg}_tn.jpg"
    sips -Z "$THUMB_WIDTH" --setProperty formatOptions "$QUALITY" "$full" --out "$thumb" >/dev/null
  done
done

echo "Done. New total size:"
du -sh "$IMG_DIR"
```

- [ ] **Step 2: Record the before size, then run the script**

```bash
du -sh web/pages/img
chmod +x scripts/optimize-images.sh
./scripts/optimize-images.sh
```
Expected: script prints a "New total size" that is meaningfully smaller than the before size (currently ~18MB across these five folders).

- [ ] **Step 3: Verify images are still valid**

```bash
file web/pages/img/ext/01.jpg web/pages/img/ext/01_tn.jpg
sips -g pixelWidth -g pixelHeight web/pages/img/ext/01.jpg
```
Expected: both report `JPEG image data`; the full image's longest dimension is at or under 1600px.

- [ ] **Step 4: Commit**

```bash
git add scripts/optimize-images.sh web/pages/img
git commit -m "Add one-time image optimisation script and run it against all site photos"
```

---

### Task 3: Migrate the homepage

**Files:**
- Create: `web/index.php`
- Delete: `web/index.html`

**Interfaces:**
- Consumes: `web/includes/{head,nav,footer}.php`, `web/css/site.css` (Task 1).

- [ ] **Step 1: Create `web/index.php`**

```php
<?php
$pageTitle = 'Fontmorand - a historic manor house in Prissac, France';
$pageDescription = "Fontmorand is a 19th-century Maison de Maître in Prissac, in the Indre department of central France - its history, grounds and surrounding countryside.";
$activeNav = 'home';
$baseUrl = '';
require __DIR__ . '/includes/head.php';
require __DIR__ . '/includes/nav.php';
?>
<div class="hero">
  <h1>Fontmorand</h1>
  <p>A beautiful, secluded Maison de Maître, set in 3.5 hectares of private, picturesque countryside in central France.</p>
</div>

<div class="content">
  <h2 class="page-title">Welcome to Fontmorand</h2>
  <p>This site was set up by the previous owners of this magnificent, historic and sympathetically renovated Maison de Maître in central France, to act as a central point of reference for everything related to the house - including its rich and colourful history, its grounds and the surrounding area.</p>
  <p>Take a tour of the site, check out the photos, and read the stories behind the house - from a medieval font hidden in its own grove, to a night in July 1944 that decided whether Prissac would be spared. For more information, feel free to <a href="pages/contact.php">contact us</a> directly.</p>
</div>

<?php
$extraScripts = [];
require __DIR__ . '/includes/footer.php';
?>
```

- [ ] **Step 2: Verify**

```bash
php -l web/index.php
php -S localhost:8000 -t web >/tmp/fontmorand-smoke.log 2>&1 &
SERVER_PID=$!
sleep 1
curl -s http://localhost:8000/index.php | grep -q "Welcome to Fontmorand" && echo PASS_HOME
curl -s http://localhost:8000/index.php | grep -q "pages/contact.php" && echo PASS_LINK
kill $SERVER_PID
```
Expected: `PASS_HOME` and `PASS_LINK` both printed.

- [ ] **Step 3: Remove the old page and commit**

```bash
git rm web/index.html
git add web/index.php
git commit -m "Migrate homepage to PHP includes and Stone Manor design"
```

---

### Task 4: Migrate the Details page

**Files:**
- Create: `web/pages/details.php`
- Delete: `web/pages/details.html`

**Interfaces:**
- Consumes: `web/includes/{head,nav,footer}.php` (Task 1).

- [ ] **Step 1: Create `web/pages/details.php`**

```php
<?php
$pageTitle = 'Details - Fontmorand, a manor house in Prissac';
$pageDescription = "A 290m2, three-storey Maison de Maître near Châteauroux, with two 16th-century barns, a gatekeeper's cottage and 3.5 hectares of parkland.";
$activeNav = 'details';
$baseUrl = '../';
require __DIR__ . '/../includes/head.php';
require __DIR__ . '/../includes/nav.php';
?>
<div class="content">
  <h1 class="page-title">Details</h1>
  <p>Fontmorand is a small hamlet at the foot of the limestone cliff of Prissac. The remains of Château Fontmorand, an 11th-century small castle with a moat and drawbridge, lie here. Château Fontmorand was sold to the French Nation in 1792 by the Marquis de Villemort. By 1850 all that remained were a few stone walls and the north-western tower. North of these lake-surrounded ruins, the present-day house - built from the château's remains - commands a breathtaking view of the Prairie de Fontmorand at the heart of the Val de l'Abloux.</p>

  <h3>The House</h3>
  <p>A 19th-century Maison de Maître in central France, 30 minutes south of Châteauroux and one hour north of Limoges airport.</p>
  <p>A three-storey, sympathetically renovated house of 290m², comprising a large entrance hall leading to a family country kitchen and spacious living and dining rooms, each with a period fireplace.</p>
  <p>An oak spiral staircase leads to the first floor, with four double bedrooms - one en suite with a dressing room - and a family bathroom. The second floor has two completed bedrooms (one currently used as a library) and a third nearing completion, plus a family bathroom.</p>
  <ul>
    <li>9 acres (3.5 hectares) of parkland, including two lakes, an acre of woodland and 2 acres of paddock</li>
    <li>Two 16th-century barns (156m² and 120m²), both re-roofed in traditional style, suitable for a guest house/gite conversion, studio, or stables for the nearby paddock</li>
    <li>A gatekeeper's cottage (96m²), also suitable for conversion</li>
  </ul>

  <h3>Interesting features</h3>
  <ul>
    <li>Beautiful, uninterrupted views across the peaceful Val de l'Abloux</li>
    <li>Original 16th-century oak beams taken from the old château</li>
    <li>Double glazed and fully insulated</li>
    <li>Two efficient wood-burning stoves, keeping heating costs low</li>
    <li>Close to the village of Prissac, with shops, a restaurant and a café/bar</li>
    <li>A delightful 300m² entertainment terrace overlooking the lake</li>
  </ul>

  <h3>Places of interest nearby</h3>
  <ul>
    <li>Located within the Brenne National Park</li>
    <li>Six miles from the medieval town of St Benoit-du-Sault, one of France's officially designated "most beautiful villages"</li>
    <li>Seven miles from the "belle ville" of Bélabre, with its beautiful river for walks and canoeing</li>
    <li>Numerous châteaux and places of historical interest within an hour's drive</li>
  </ul>

  <h3>Essential amenities</h3>
  <ul>
    <li>A well-equipped hospital with full A&amp;E facilities at Le Blanc, within 30 minutes</li>
    <li>Banking, supermarkets and a hotel at St Benoit-du-Sault (6 miles)</li>
    <li>The nearest mainline rail station is at Argenton-sur-Creuse (20 minutes) or Châteauroux (40 minutes)</li>
  </ul>
</div>

<?php
$extraScripts = [];
require __DIR__ . '/../includes/footer.php';
?>
```

- [ ] **Step 2: Verify**

```bash
php -l web/pages/details.php
php -S localhost:8000 -t web >/tmp/fontmorand-smoke.log 2>&1 &
SERVER_PID=$!
sleep 1
curl -s http://localhost:8000/pages/details.php | grep -q "gatekeeper's cottage" && echo PASS
kill $SERVER_PID
```
Expected: `PASS` printed.

- [ ] **Step 3: Remove the old page and commit**

```bash
git rm web/pages/details.html
git add web/pages/details.php
git commit -m "Migrate Details page to PHP includes and Stone Manor design"
```

---

### Task 5: Migrate the Photos page

**Files:**
- Create: `web/pages/photos.php`
- Delete: `web/pages/photos.html`

**Interfaces:**
- Consumes: `web/includes/{head,nav,footer}.php`, `web/js/lightbox.js` (Task 1); optimised images under `web/pages/img/{ext,int,out,grd}/` (Task 2).

- [ ] **Step 1: Create `web/pages/photos.php`**

```php
<?php
$pageTitle = 'Photos - Fontmorand, a manor house in Prissac';
$pageDescription = 'Photographs of Fontmorand: the house, its out-buildings and its grounds in Prissac, central France.';
$activeNav = 'photos';
$baseUrl = '../';
require __DIR__ . '/../includes/head.php';
require __DIR__ . '/../includes/nav.php';
?>
<div class="content">
  <h1 class="page-title">Photos</h1>
  <p style="text-align:center;">A photographic tour of the house, its out-buildings and its grounds - click any photo to view it full-size.</p>

  <div class="tab-group">
    <ul class="tab-list">
      <li><a class="tab-link is-active" href="#ext" data-tab-target="ext">Exterior</a></li>
      <li><a class="tab-link" href="#int" data-tab-target="int">Interior</a></li>
      <li><a class="tab-link" href="#ob" data-tab-target="ob">Out-Buildings</a></li>
      <li><a class="tab-link" href="#grd" data-tab-target="grd">Grounds</a></li>
    </ul>

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
  </div>

  <p style="text-align:center;">Please don't hesitate to <a class="btn" href="contact.php">contact us</a> if you have any questions!</p>
</div>

<?php
$extraScripts = ['js/lightbox.js'];
require __DIR__ . '/../includes/footer.php';
?>
```

- [ ] **Step 2: Verify**

```bash
php -l web/pages/photos.php
php -S localhost:8000 -t web >/tmp/fontmorand-smoke.log 2>&1 &
SERVER_PID=$!
sleep 1
curl -s http://localhost:8000/pages/photos.php | grep -c 'gallery-item' # expect 34 (8+12+9+5)
curl -s http://localhost:8000/pages/photos.php | grep -q 'js/lightbox.js' && echo PASS_SCRIPT
kill $SERVER_PID
```
Expected: the count matches 34 gallery items, and `PASS_SCRIPT` is printed.

- [ ] **Step 3: Manually confirm the lightbox behaviour in a browser**

Open `http://localhost:8000/pages/photos.php`, click a thumbnail in each of the four tabs, and confirm: the overlay opens showing the full-size image and caption, next/prev cycle within that tab's photos only, and Esc/click-outside closes it.

- [ ] **Step 4: Remove the old page and commit**

```bash
git rm web/pages/photos.html
git add web/pages/photos.php
git commit -m "Migrate Photos page to PHP includes, flat tabs, and the vanilla lightbox"
```

---

### Task 6: Migrate the History page (with two new WWII entries)

**Files:**
- Create: `web/pages/history.php`
- Delete: `web/pages/history.html`

**Interfaces:**
- Consumes: `web/includes/{head,nav,footer}.php` (Task 1); existing images `web/pages/img/font.jpg`, `web/pages/img/monk.jpg`, `web/pages/img/crenau.jpg` (unchanged filenames from Task 2's pass).

**Manual step before this content goes live:** the "10 July 1944" and "Odette Androt" entries below are adapted from `FR/Histoires.htm`, a scanned/OCR'd French page in the old hosting archive with some encoding damage. Have the family check names, dates and details against their own records before publishing, given the subject matter.

- [ ] **Step 1: Create `web/pages/history.php`**

```php
<?php
$pageTitle = 'History - Fontmorand, Prissac';
$pageDescription = 'The history of Fontmorand: the origin of its name, and its role in the French Resistance during the Second World War.';
$activeNav = 'history';
$baseUrl = '../';
require __DIR__ . '/../includes/head.php';
require __DIR__ . '/../includes/nav.php';
?>
<div class="content">
  <h1 class="page-title">History</h1>
  <p>Since 2004, alongside restoring the house, we have spent much time looking into its rich history.</p>

  <div class="tab-group">
    <ul class="tab-list">
      <li><a class="tab-link is-active" href="#name" data-tab-target="name">Origin of the Name</a></li>
      <li><a class="tab-link" href="#c-sb" data-tab-target="c-sb">Créneau - South Barn</a></li>
      <li><a class="tab-link" href="#raid" data-tab-target="raid">10 July 1944</a></li>
      <li><a class="tab-link" href="#androt" data-tab-target="androt">Odette Androt</a></li>
    </ul>

    <div id="name" class="tab-panel is-active">
      <div class="entry">
        <img src="img/font.jpg" alt="The Font">
        <div class="entry-body">
          <h3>Font</h3>
          <p>The Font is situated at the north-eastern corner of the property, hidden in its own shady grove. Over the centuries the spring has been used as a convenient washing area for the household's linen - you can still clearly see the circular, bowl-shaped structure.</p>
        </div>
      </div>
      <div class="entry">
        <img src="img/monk.jpg" alt="Morand">
        <div class="entry-body">
          <h3>Morand</h3>
          <p>Benedictine patron saint of wine growers. Feast day: 3 June.</p>
          <p>Morand was born into a rich, aristocratic family near Worms, Germany, and was educated at Worms Cathedral School. Following his ordination there, he made a pilgrimage to Compostela, Spain, and entered the Benedictine monastery at Cluny.</p>
          <p>Morand was sent to Alsace to become counsellor to Count Frederick Pferz, based at <a href="http://www.alsace-passion.com/altkirch_3.htm" target="_blank" rel="noopener">St Christopher Church at Altkirch</a>. He performed many miracles there, and became patron saint of the wine-making community after celebrating each Lent sustained by nothing but a bunch of grapes.</p>
        </div>
      </div>
    </div>

    <div id="c-sb" class="tab-panel">
      <div class="entry">
        <img src="img/crenau.jpg" alt="Créneau South Barn">
        <div class="entry-body">
          <h3>Créneau - South Barn</h3>
          <p>In the north wall of the South Barn there is a niche containing a small statue of the Virgin Mary. It is not the original statue, but one which represents it - the Lady of Pontmain - until the original is found.</p>
          <p>The niche was built after the Second World War, to commemorate the secret activity of the French Resistance here at Prissac, when refugees were hidden in the South Barn en route to Spain. This is confirmed by the local family who lived at Fontmorand at the time.</p>
          <p>German forces arrived in Prissac on 10 July 1944 from the direction of Luzeret, turned right towards Bélabre and Oulches, and fortunately missed both Fontmorand and le Moulin Ribaud - headquarters of the local Resistance leader (the "Marquis") in Prissac. This act, and the bravery of Henri Mégray, forged a lasting connection between Prissac, the Lady of Pontmain, and the 1871 armistice of 17 January.</p>
          <p>The story also tells of a romance between the organiser of the refugee route - who had arrived in Prissac from Alsace - and one of the household's daughters. They married, and a daughter of that marriage still lives and works in Châteauroux today.</p>
          <p>More on the Lady of Pontmain can be found <a href="http://www.marypages.com/PontmainEng1.htm" target="_blank" rel="noopener">here</a>.</p>
        </div>
      </div>
    </div>

    <div id="raid" class="tab-panel">
      <div class="entry-body">
        <h3>10 July 1944</h3>
        <p>In 1972, the newspaper <em>La Nouvelle République</em> marked the 28th anniversary of a dark day for this corner of the Berry. On 10 July 1944, the Waffen-SS "Das Reich" division carried out a sweep against the Resistance across the surrounding communes - Bélabre, Chalais, Oulches, Ciron, Prissac and Lignac - an act of terror without precedent for the area. Villages and hamlets including Bélabre, Terrier, Porcher, les Descends, Paillet, la Claircie, Château-Guillaume and Chillouet suffered violence and burning; the wooded hills of la Bicherie, north of Prissac, were searched.</p>
        <p>Prissac itself was spared that day for two reasons: the caution of the local Resistance leadership, and above all the heroic silence of a young man named Henri Mégray, known as "Riri". Riri, then about fifteen years old, had been picked up and forced into the lead vehicle of the German column as a guide. Stripped and beaten in front of the cemetery gate, he never revealed where the Marquis of Prissac and his men were hiding - even though he spent his days alongside the men of the Resistance.</p>
        <p>Fontmorand and the nearby Moulin Ribaud, the Marquis's headquarters in Prissac, were spared as the German column turned away towards Bélabre and Oulches, narrowly missing both. The people of Prissac owed Henri Mégray a considerable debt for his silence that day.</p>
        <p><em>Adapted from a 1972 "La Nouvelle République" article preserved in the family archive.</em></p>
      </div>
    </div>

    <div id="androt" class="tab-panel">
      <div class="entry-body">
        <h3>Odette Androt - Righteous Among the Nations</h3>
        <p>Odette Androt was the town-hall secretary of Prissac. During the Occupation, around twenty Jewish families took refuge in the area, including three members of the Gozland family - born in Constantine and adopted Marseillais - and four members of the Siac family, who had fled Romania and then occupied Paris. Odette provided all of them with identity and ration cards that did not carry the "Juif" stamp otherwise required by law.</p>
        <p>In 1943, Prissac's mayor - a known Vichy loyalist - ordered her to draw up a list of the town's Jewish residents. Once he had signed it, Odette secretly destroyed the list rather than pass it on to the Ministry of the Interior.</p>
        <p>In the summer of 1944, as German forces retreating from the south of France were rumoured to be hunting Jews on their way to reinforce the Normandy front, Monsieur and Madame Siac, their twelve-year-old daughter Thérèse and six-year-old son Lucien tried and failed to find shelter with a villager. They turned to Odette, who sheltered them in her own home for several days, until German forces had left the area.</p>
        <p>On 31 December 1998, Yad Vashem recognised Odette Androt as Righteous Among the Nations.</p>
      </div>
    </div>
  </div>
</div>

<?php
$extraScripts = [];
require __DIR__ . '/../includes/footer.php';
?>
```

- [ ] **Step 2: Verify**

```bash
php -l web/pages/history.php
php -S localhost:8000 -t web >/tmp/fontmorand-smoke.log 2>&1 &
SERVER_PID=$!
sleep 1
curl -s http://localhost:8000/pages/history.php | grep -q "Odette Androt" && echo PASS_ANDROT
curl -s http://localhost:8000/pages/history.php | grep -q "Das Reich" && echo PASS_RAID
curl -s http://localhost:8000/pages/history.php | grep -q 'src="img/font.jpg"' && echo PASS_IMG
kill $SERVER_PID
```
Expected: all three `PASS_*` lines printed.

- [ ] **Step 3: Remove the old page and commit**

```bash
git rm web/pages/history.html
git add web/pages/history.php
git commit -m "Migrate History page; add the 10 July 1944 and Odette Androt entries"
```

---

### Task 7: Migrate the Area page

**Files:**
- Create: `web/pages/area.php`
- Delete: `web/pages/area.html`

**Interfaces:**
- Consumes: `web/includes/{head,nav,footer}.php`, `web/js/lightbox.js` (Task 1); optimised images under `web/pages/img/region/` (Task 2).

- [ ] **Step 1: Create `web/pages/area.php`**

```php
<?php
$pageTitle = 'Area - Fontmorand, Prissac and the Brenne National Park';
$pageDescription = 'Fontmorand sits in the Creuse Valley within the Brenne National Park, in the Berry region of central France.';
$activeNav = 'area';
$baseUrl = '../';
require __DIR__ . '/../includes/head.php';
require __DIR__ . '/../includes/nav.php';
?>
<div class="content">
  <h1 class="page-title">Area</h1>
  <p>Fontmorand is set in the heart of the Berry region of France, an area of outstanding natural beauty. Here is just a sample of the local delights within easy reach of the property.</p>

  <div class="tab-group">
    <ul class="tab-list">
      <li><a class="tab-link is-active" href="#creuse" data-tab-target="creuse">The Creuse Valley</a></li>
      <li><a class="tab-link" href="#brenne" data-tab-target="brenne">Brenne National Park</a></li>
    </ul>

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
  </div>
</div>

<?php
$extraScripts = ['js/lightbox.js'];
require __DIR__ . '/../includes/footer.php';
?>
```

- [ ] **Step 2: Verify**

```bash
php -l web/pages/area.php
php -S localhost:8000 -t web >/tmp/fontmorand-smoke.log 2>&1 &
SERVER_PID=$!
sleep 1
curl -s http://localhost:8000/pages/area.php | grep -q "Brenne National Park" && echo PASS_BRENNE
curl -s http://localhost:8000/pages/area.php | grep -q 'js/lightbox.js' && echo PASS_SCRIPT
kill $SERVER_PID
```
Expected: both `PASS_*` lines printed.

- [ ] **Step 3: Remove the old page and commit**

```bash
git rm web/pages/area.html
git add web/pages/area.php
git commit -m "Migrate Area page to PHP includes and the vanilla lightbox"
```

---

### Task 8: Migrate the Find Us page (with rotated Maps key)

**Files:**
- Create: `web/pages/find-us.php`
- Delete: `web/pages/find-us.html`

**Interfaces:**
- Consumes: `web/includes/{head,nav,footer}.php` (Task 1).

**Manual step before this goes live:** rotate the Google Maps API key. The old key (`AIzaSy...SsrI`, redacted here) is exposed in the current live page and git history - create a new key in Google Cloud Console, restrict it to the `fontmorand.com`/`fontmorand.fr` HTTP referrers, and replace `YOUR_RESTRICTED_MAPS_KEY` below with it.

- [ ] **Step 1: Create `web/pages/find-us.php`**

```php
<?php
$pageTitle = 'Find Us - directions to Fontmorand, Prissac';
$pageDescription = "How to find Fontmorand in Prissac, Indre, central France - by car or by train from Paris, Limoges and Argenton-sur-Creuse.";
$activeNav = 'find-us';
$baseUrl = '../';
require __DIR__ . '/../includes/head.php';
require __DIR__ . '/../includes/nav.php';
?>
<div class="content">
  <h1 class="page-title">Find us!</h1>
  <p>The map below shows our exact location. Use the tabs to find the best route from your starting point.</p>

  <div class="tab-group">
    <ul class="tab-list">
      <li><a class="tab-link is-active" href="#gmap" data-tab-target="gmap">Map</a></li>
      <li><a class="tab-link" href="#paris" data-tab-target="paris">From Paris</a></li>
      <li><a class="tab-link" href="#elsewhere" data-tab-target="elsewhere">From Limoges &amp; Argenton</a></li>
    </ul>

    <div id="gmap" class="tab-panel is-active">
      <div id="map" style="height:450px;width:100%;"></div>
      <script>
        function initFontmorandMap() {
          var location = { lat: 46.506208, lng: 1.305091 };
          var map = new google.maps.Map(document.getElementById('map'), { zoom: 13, center: location });
          new google.maps.Marker({ position: location, map: map });
        }
      </script>
      <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=YOUR_RESTRICTED_MAPS_KEY&callback=initFontmorandMap">
      </script>
    </div>

    <div id="paris" class="tab-panel">
      <h4>Directions from Paris</h4>
      <h5>Fly/Drive</h5>
      <p>There are two large international airports near Paris - Roissy Charles de Gaulle to the north, and Paris Orly to the south. Orly is the closer of the two to Prissac, but Charles de Gaulle often has more frequent flights.</p>
      <p>If driving, the route from either airport is essentially the same: head south on the motorway network - A6, then A10 towards Orléans, A71 towards Vierzon, then A20 towards Limoges - and leave at junction 18 for Argenton-sur-Creuse and Prissac.</p>
      <p>Fontmorand is on the outskirts of Prissac, only 10 minutes from junction 18 on the A20, and well signposted.</p>
      <p>At the T-junction on entering Prissac, turn left and head down the hill. When you see the Étang de Prissac (lake) on your left, turn right immediately opposite - between the bollards - and follow the track to the end, through the gate.</p>
      <p>The drive is approximately 350km, taking between 2h45 and 4h depending on traffic out of Paris. There are tolls on the motorways, so allow for stops.</p>
      <h5>Train</h5>
      <p>There is a good Intercity line between Paris Gare d'Austerlitz and Argenton-sur-Creuse via Châteauroux, taking around 2h20 through some beautiful countryside.</p>
      <p>Gare d'Austerlitz connects to Gare du Nord (Eurostar, Thalys, and RER B from Charles de Gaulle) via Métro line 5, about 15 minutes door to door. It also connects directly to Orly via the RER C, about 45 minutes.</p>
      <p>The train from Paris to Argenton costs around €45 each way. Taxis are available in Argenton, or a car can be hired at Châteauroux, 15 minutes before Argenton on the same line.</p>
    </div>

    <div id="elsewhere" class="tab-panel">
      <h4>From Limoges &amp; Argenton-sur-Creuse</h4>
      <p>Limoges has its own airport and is roughly an hour's drive from Fontmorand via the A20.</p>
      <p>Argenton-sur-Creuse, on the same Paris-Limoges rail line mentioned above, is about 20 minutes from Prissac by road.</p>
      <p>From either, we'd recommend using GPS or a mapping app for the final leg into Prissac - the route in from the A20 changes with roadworks and diversions more often than a page like this can keep up with. Once you're within a few kilometres, follow the signs for Prissac and get in touch if you'd like a hand.</p>
    </div>
  </div>
</div>

<?php
$extraScripts = [];
require __DIR__ . '/../includes/footer.php';
?>
```

- [ ] **Step 2: Verify**

```bash
php -l web/pages/find-us.php
php -S localhost:8000 -t web >/tmp/fontmorand-smoke.log 2>&1 &
SERVER_PID=$!
sleep 1
curl -s http://localhost:8000/pages/find-us.php | grep -q "initFontmorandMap" && echo PASS_MAP
curl -s http://localhost:8000/pages/find-us.php | grep -qc "AIzaSy" ; echo "old key present: $?"
kill $SERVER_PID
```
Expected: `PASS_MAP` printed; the old key grep should report a non-zero exit (i.e. not found) once `YOUR_RESTRICTED_MAPS_KEY` is swapped for the real rotated key.

- [ ] **Step 3: Remove the old page and commit**

```bash
git rm web/pages/find-us.html
git add web/pages/find-us.php
git commit -m "Migrate Find Us page; drop empty direction tabs for honest general guidance"
```

---

### Task 9: Migrate the Contact page and remove the broken mail handler

**Files:**
- Create (rewrite in place): `web/pages/contact.php`
- Delete: `web/pages/mailer.php`

**Interfaces:**
- Consumes: `web/includes/{head,nav,footer}.php` (Task 1) - `.modal-overlay`/`.modal-box`/`.modal-close`/`data-modal-target` from Task 1's CSS/JS.

**Manual step before this goes live:** sign up at https://formspree.io, verify `contact@fontmorand.fr` as the recipient, create a form, and replace `YOUR_FORMSPREE_ID` below with the real form ID from your Formspree dashboard.

- [ ] **Step 1: Rewrite `web/pages/contact.php`**

```php
<?php
$pageTitle = 'Contact - Fontmorand, Prissac, France';
$pageDescription = 'Get in touch about Fontmorand, a historic manor house in Prissac, central France.';
$activeNav = 'contact';
$baseUrl = '../';
require __DIR__ . '/../includes/head.php';
require __DIR__ . '/../includes/nav.php';
?>
<div class="hero">
  <h1>Fontmorand</h1>
  <p>For more information, please get in touch.</p>
</div>

<div class="content">
  <h1 class="page-title">Contact us</h1>
  <p style="text-align:center;">Should you wish to contact us for further information, please complete the form below.</p>

  <div style="text-align:center;">
    <a class="btn" href="#contact-modal" data-modal-target="contact-modal">Contact Us</a>
  </div>

  <?php if (isset($_GET['s'])): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($_GET['s']); ?></div>
  <?php elseif (isset($_GET['e'])): ?>
    <div class="alert alert-error"><?php echo htmlspecialchars($_GET['e']); ?></div>
  <?php endif; ?>

  <div id="contact-modal" class="modal-overlay">
    <div class="modal-box">
      <button type="button" class="modal-close btn" style="float:right;">&times;</button>
      <h4>Get in touch</h4>
      <form method="post" action="https://formspree.io/f/YOUR_FORMSPREE_ID">
        <input type="hidden" name="_next" value="https://fontmorand.com/pages/contact.php?s=Thank+you.+Your+message+has+been+sent.">
        <input type="hidden" name="_subject" value="New enquiry from fontmorand.com">
        <div class="form-field">
          <label class="form-label" for="name">Name</label>
          <input required type="text" class="form-input" id="name" name="name" placeholder="Your name">
        </div>
        <div class="form-field">
          <label class="form-label" for="email">Email</label>
          <input required type="email" class="form-input" id="email" name="_replyto" placeholder="Your email">
        </div>
        <div class="form-field">
          <label class="form-label" for="subject">Subject</label>
          <input required type="text" class="form-input" id="subject" name="subject" placeholder="Subject">
        </div>
        <div class="form-field">
          <label class="form-label" for="message">Message</label>
          <textarea required class="form-textarea" rows="6" id="message" name="message" placeholder="Your message..."></textarea>
        </div>
        <div class="form-field" style="text-align:center;">
          <button type="submit" class="btn">Send Message</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php
$extraScripts = [];
require __DIR__ . '/../includes/footer.php';
?>
```

- [ ] **Step 2: Verify**

```bash
php -l web/pages/contact.php
php -S localhost:8000 -t web >/tmp/fontmorand-smoke.log 2>&1 &
SERVER_PID=$!
sleep 1
curl -s "http://localhost:8000/pages/contact.php?s=Sent!" | grep -q 'alert-success' && echo PASS_SUCCESS
curl -s "http://localhost:8000/pages/contact.php?e=Missing%20name" | grep -q 'alert-error' && echo PASS_ERROR
curl -s http://localhost:8000/pages/contact.php | grep -q 'formspree.io/f/YOUR_FORMSPREE_ID' && echo PASS_FORM
kill $SERVER_PID
```
Expected: all three `PASS_*` lines printed (the form action check should stop passing once the real Formspree ID is filled in - that's expected and correct).

- [ ] **Step 3: Delete the broken mail handler and commit**

```bash
git rm web/pages/mailer.php
git add web/pages/contact.php
git commit -m "Rewrite Contact page onto Formspree; delete unreliable mailer.php"
```

---

### Task 10: Cleanup - remove dead frameworks and stray files

**Files:**
- Delete: `web/old/`, `web/dist/`, `web/custom/`
- Delete: all `_notes/` directories and stray `Icon` files under `web/pages/img/` and `web/img/`

**Interfaces:**
- Consumes: nothing (safe once Tasks 3-9 have removed every reference to `dist/js/bootstrap.min.js`, `custom/css/custom-site.css`, `custom/js/*`, and jQuery).

- [ ] **Step 1: Confirm nothing still references the folders being deleted**

```bash
grep -rl "dist/js\|custom/css\|custom/js\|jquery" web/*.php web/pages/*.php || echo "CLEAN"
```
Expected: `CLEAN` (no matches - every page migrated in Tasks 3-9 already dropped these references).

- [ ] **Step 2: Delete the old framework/vendor folders**

```bash
rm -rf web/old web/dist web/custom
```

- [ ] **Step 3: Remove stray Dreamweaver/Finder artefacts**

```bash
find web/pages/img web/img -type d -name "_notes" -exec rm -rf {} +
find web/pages/img web/img -type f -name "Icon*" -delete
```

- [ ] **Step 4: Verify the site still serves correctly**

```bash
php -S localhost:8000 -t web >/tmp/fontmorand-smoke.log 2>&1 &
SERVER_PID=$!
sleep 1
for path in index.php pages/details.php pages/photos.php pages/history.php pages/area.php pages/find-us.php pages/contact.php; do
  code=$(curl -s -o /dev/null -w "%{http_code}" "http://localhost:8000/$path")
  echo "$path -> $code"
done
kill $SERVER_PID
```
Expected: every path reports `200`.

- [ ] **Step 5: Commit**

```bash
git add -A web
git commit -m "Remove dead Bootstrap/jQuery vendor folders and stray editor artefacts"
```

---

### Task 11: SEO pass - sitemap

**Files:**
- Create: `web/sitemap.xml`

**Interfaces:**
- Consumes: the final `.php` URLs produced by Tasks 3-9 (title/meta description work is already done per-page in those tasks, so this task only adds the sitemap).

- [ ] **Step 1: Create `web/sitemap.xml`**

```xml
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url><loc>https://fontmorand.com/index.php</loc></url>
  <url><loc>https://fontmorand.com/pages/details.php</loc></url>
  <url><loc>https://fontmorand.com/pages/photos.php</loc></url>
  <url><loc>https://fontmorand.com/pages/history.php</loc></url>
  <url><loc>https://fontmorand.com/pages/area.php</loc></url>
  <url><loc>https://fontmorand.com/pages/find-us.php</loc></url>
  <url><loc>https://fontmorand.com/pages/contact.php</loc></url>
</urlset>
```

- [ ] **Step 2: Validate the XML**

```bash
php -r "var_dump(simplexml_load_file('web/sitemap.xml') !== false);"
```
Expected: `bool(true)`.

- [ ] **Step 3: Confirm every page has a unique, non-stale title and description**

```bash
for f in web/index.php web/pages/*.php; do
  echo "=== $f ==="
  grep -E "pageTitle|pageDescription" "$f" | head -2
done
```
Expected: every page shows a distinct `$pageTitle`/`$pageDescription`, none containing "for sale".

- [ ] **Step 4: Commit**

```bash
git add web/sitemap.xml
git commit -m "Add sitemap.xml for SEO"
```

---

## Final manual checklist (not agent-executable)

- [ ] Sign up for GoatCounter (or equivalent) and replace `YOURCODE` in `includes/footer.php`
- [ ] Rotate and restrict the Google Maps API key; replace `YOUR_RESTRICTED_MAPS_KEY` in `pages/find-us.php`, then load the Map tab in a browser and confirm the embed renders with the marker in the right place
- [ ] Sign up for Formspree, verify `contact@fontmorand.fr`, and replace `YOUR_FORMSPREE_ID` in `pages/contact.php`; send a real test enquiry end-to-end and confirm it arrives
- [ ] Cross-browser/responsive check once Task 1's foundation is in place and at least one page is migrated: view at mobile (~375px) and desktop widths, confirm the nav toggle, tabs, and lightbox all work correctly at both
- [ ] Have the family review the new "10 July 1944" and "Odette Androt" History entries against their own records before this is live for the public
- [ ] Push to `owsters/Fontmorand` and confirm Heroku's autodeploy picks it up; verify `fontmorand.com` and `fontmorand.fr` both render correctly
