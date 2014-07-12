<div class="navbar" role="navigation">
	<div class="navbar-header">
		<button type="button" class="navbar-toggle" data-toggle="collapse"
			data-target="#rebound-navbar-collapse">
			<span class="fa fa-bars"></span> Menu
		</button>
		<a href="<?php echo WEB_PATH; ?>" class="navbar-brand" style="font-size: 53px;">Play boy viet</a>
		<p class="brand-text">Play boy- App Mobile.</p>
	</div>
	<!-- end navbar-header -->
	<div class="collapse navbar-collapse" id="rebound-navbar-collapse">
		<ul class="nav navbar-nav">

			<li class="title">Menu</li>
			<li class="active"><a href="<?php echo WEB_PATH; ?>">Home</a></li>
			<li><a href="<?php echo WEB_PATH. '/template/hotgirl';?>">Hot girl</a></li>
			<li><a href="<?php echo WEB_PATH. '/template/hotgirlnew';?>">Hot Girl New</a></li>
			<li><a href="<?php echo WEB_PATH;?>/user/login">login</a></li>

		</ul>
              <?php require_once 'search.php';?>
              <?php require_once 'sortedbytopics.php';?>
              
            </div>
	<!-- end navbar-collapse -->
</div>
<!-- end navbar -->