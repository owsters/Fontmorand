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
