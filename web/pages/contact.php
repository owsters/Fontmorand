<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="For Sale: Stunning 19th century, 3-storey Maison de Maitre (manor house) in central France, lovingly restored and modernised. Includes (with planning permission) 2x16th century barns, cottage and 9 acres of land. 7 bedrooms, 3 bathrooms - large kitchen, salon and entrance hall.">
		<meta name="author" content="Owen Daniels">
		<link rel="shortcut icon" href="../img/favicon.png">
<meta name="keywords" content="Fontmorand,Prissac,Indre,Centre,Pontmain,resistance,France,Abloux,Vouhet,DunetMorand,Font,refugee,Brenne,cistude,créneau,maison,maître,maitre,argenton-sur-creuse,argenton,creuse,chateauroux,berry,for-sale,sale,house,buy,to-buy,to buy,manor,manor house,rightmove overseas,overseas,property,barn,lake,beautiful,magnificent,dream home,canoeing,hunting,fishing,sailing,oak,oak beams,historic"/>

<title>Fontmorand - beautiful Maison de Maitre for sale in Prissac, Indre (France)</title>

		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
			<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
		<![endif]-->

		<!-- Custom styles for this template -->
		<link href="../custom/css/custom-site.css" rel="stylesheet">
    <script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-51737752-1', 'fontmorand.fr');
	ga('require', 'displayfeatures');
  ga('send', 'pageview');

</script>
	</head>

<body>

<!-- Fixed navbar -->
<div class="navbar navbar-default navbar-fixed-top navbar-centered" role="navigation">
  <div>
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
        <span class="fa fa-bars fa-lg" style="color:#555"> Menu</span>
      </button>
      <!-- <a class="navbar-brand" href="index.html">Fontmorand</a> -->
    </div>
    <div class="navbar-collapse collapse"><!--.nav-collapse -->
      <ul class="nav navbar-nav">
        <li><a href="../index.html"><i class="fa fa-home fa-fw"></i> Home</a></li>
        <li><a href="details.html"><i class="fa fa-plus fa-fw"></i> Details</a></li>
        <li><a href="photos.html"><i class="fa fa-camera-retro fa-fw"></i> Photos</a>
        <li><a href="history.html"><i class="fa fa-book fa-fw"></i> History</a></li>
        <li><a href="area.html"><i class="fa fa-compass fa-fw"></i> Area</a></li>
        <li><a href="find-us.html"><i class="fa fa-map-marker fa-fw"></i> Find Us</a></li>
        <li class="active"><a href="contact.php"><i class="fa fa-envelope fa-fw"></i> Contact</a></li>
      </ul>
    </div><!--/.nav-collapse -->
  </div>
</div>

<div class="container container-header"> <!--jumbotron-->
  <div class="jumbotron">
    <h1>Fontmorand</h1>
    <p>For more information please contact us!</p>
    <!-- <p>
      <a class="btn btn-lg btn-primary" href="#" role="button">hey! &raquo;</a>
    </p> -->
  </div>
</div> <!-- /jumbotron -->

<div class="container"> <!--Main Body-->
  <!-- Begin Main Content -->
  <h1 align="center">Contact us</h1>
  <p align="center">Should you wish to contact us for further information, please complete our contact form by clicking the link below.</p>
  
  <!-- Button to trigger modal --> 
  <div align="center"><a href="#myModal" role="button" class="btn btn-default" data-toggle="modal"><i class="fa fa-envelope fa-fw"></i> Contact Us</a></div>
  <?php if (isset($_GET['s'])) echo "<div class=\"alert alert-success\">".$_GET['s']."</div>"; 
				elseif (isset($_GET['e'])) echo "<div class=\"alert alert-danger\">".$_GET['e']."</div>";  

  ?>
  <!-- Modal -->
  <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Please fill in all the fields below</h4>
          </div>
          <div class="modal-body">
          	<form id="contactform" method="post" action="mailer.php"  class="form-horizontal" role="form">
              <div class="form-group">
                <label for="name" class="col-sm-2 control-label">Name</label>
                <div class="col-sm-10">
                  <input required type="text" class="form-control" id="name" name="name" placeholder="Your Name">
                </div>
              </div>
              <div class="form-group">
                <label for="email" class="col-sm-2 control-label">Email</label>
                <div class="col-sm-10">
                  <input required type="email" class="form-control" id="email" name="email" placeholder="Your Email">
                </div>
              </div>
              <div class="form-group">
                <label for="subject" class="col-sm-2 control-label">Subject</label>
                <div class="col-sm-10">
                  <input required type="text" class="form-control" id="subject" name="subject" placeholder="Subject Message">
                </div>
              </div>
              <div class="form-group">
                <label for="message" class="col-sm-2 control-label">Message</label>
                <div class="col-sm-10">
                  <textarea required class="form-control" rows="10" id="message" name="message" placeholder="Your message..."></textarea>
                </div>
              </div>
              <div class="form-group">
                <div align="center">
                  <input type="hidden" name="save" value="contact">
                  <button type="submit" class="btn btn-default"><i class="fa fa-envelope fa-fw"></i> Send Message</button>
                </div>
              </div>
            </form>
          </div><!-- End of Modal body -->
        </div><!-- End of Modal content -->
      </div><!-- End of Modal dialog -->
  </div><!-- End of Modal -->

  <!-- end main content -->





</div> <!-- /Main Body -->

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
<script src="../dist/js/bootstrap.min.js"></script>
<!-- bootstrapvalidator -->
<script src="../custom/js/bootstrapValidator.min.js"></script>
<script src="../custom/js/form-validate.js"></script>
<script>
  $('.nav-collapse').click('li', function() {
    $('.nav-collapse').collapse('hide');
  });
</script>
</body>
</html>
