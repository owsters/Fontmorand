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
  </div>
</div>

<?php
$extraScripts = ['js/lightbox.js'];
require __DIR__ . '/../includes/footer.php';
?>
