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
