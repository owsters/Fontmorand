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
