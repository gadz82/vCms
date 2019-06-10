<!DOCTYPE html>
<html lang="it">
<head>
	<meta charset="utf-8">
	<?= $this->tag->getTitle() ?>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta name="mobile-web-app-capable" content="yes">
	<link rel="apple-touch-icon" sizes="57x57" href="/assets/site/icons/apple-icon-57x57.png">
	<link rel="apple-touch-icon" sizes="60x60" href="/assets/site/icons/apple-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="72x72" href="/assets/site/icons/apple-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="76x76" href="/assets/site/icons/apple-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="114x114" href="/assets/site/icons/apple-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="120x120" href="/assets/site/icons/apple-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="144x144" href="/assets/site/icons/apple-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="152x152" href="/assets/site/icons/apple-icon-152x152.png">
	<link rel="apple-touch-icon" sizes="180x180" href="/assets/site/icons/apple-icon-180x180.png">
	<link rel="icon" type="image/png" sizes="192x192"  href="/assets/site/icons/android-icon-192x192.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/assets/site/icons/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="96x96" href="/assets/site/icons/favicon-96x96.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/assets/site/icons/favicon-16x16.png">
	<link rel="manifest" href="/assets/site/icons/manifest.json">
	<meta name="apple-itunes-app" content="app-id=1322393741">
	<meta name="msapplication-TileColor" content="#E20613">
	<meta name="msapplication-TileImage" content="/assets/site/icons//ms-icon-144x144.png">
	<!-- <meta name="theme-color" content="#ececec"> -->
	<?= $tags->getMetaDescription() ?>
	<?= $tags->getCanonicalUrl() ?>
	<?= $tags->getRobots() ?>
	<meta property="og:type" content="website" />
	<meta property="og:site_name" content="Marchi Auto - Concessionaria Fiat, Abarth, Alfa Romeo in Umbria" />
	<meta property="og:locale" content="it_IT" />
	<?= $tags->getOgTitle() ?>
	<?= $tags->getOgUrl() ?>
	<?= $tags->getOgImage() ?>
	<?= $tags->getOgDescription() ?>
	<?= $tags->getOgVideo() ?>

	<?= $this->tag->stylesheetLink('https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css') ?>
	<?= $tags->outputCssInline('inlineCssSiteTheme') ?>
	<?= $this->assets->outputCss('cssSiteTheme') ?>

	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
	<![endif]-->

	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-129115330-1"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());

		gtag('config', 'UA-129115330-1');
	</script>

	<style>
		@import url('https://fonts.googleapis.com/css?family=Montserrat:400,700');
		@import url('https://fonts.googleapis.com/css?family=Raleway:400,100,200,300,500,700,800,900,600');
		@import url('https://fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,600italic,700,700italic,800,800italic');
	</style>
	<!-- Global site tag (gtag.js) - Google Analytics -->

</head>

<body class="stretched <?php if ($isMobile) { ?>side-panel-left side-push-panel<?php } ?>">
	<?php if ($isMobile) { ?>
		<div class="body-overlay"></div>
		<div id="side-panel" class="dark">

    <div id="side-panel-trigger-close" class="side-panel-trigger"><a href="#"><i class="icon-line-cross"></i></a></div>

    <div class="side-panel-wrap">

        <div class="widget clearfix">

            <h4>Menu</h4>

            <nav class="nav-tree nobottommargin">
                <ul>
                    <?php $menu = json_decode($tags->renderBlock('menu')); ?>
                    <?php foreach ($menu as $item) { ?>
                    <li
                        <?php if ($item->submenu != null) { ?>
                        class="sub-menu"
                        <?php } ?>
                    >
                        <?php if (isset($item->blockTpl)) { ?>
                        <?= $tags->renderBlock($item->blockTpl) ?>
                        <?php } else { ?>
                        <a href="<?= $item->href ?>">
                            <i class="fa fa-chevron-right"></i>
                            <?= $item->title ?>
                        </a>
                        <?php if ($item->submenu != null) { ?>
                        <ul>
                            <?php foreach ($item->submenu as $subitem) { ?>
                            <li
                                <?php if ($subitem->class != null) { ?>
                                class="<?= $subitem->class ?>"
                                <?php } ?>
                            >
                                <a href="<?= $subitem->href ?>">
                                    <?= $subitem->title ?>
                                </a>
                            </li>
                            <?php } ?>
                        </ul>
                        <?php } ?>
                        <?php } ?>
                    </li>
                    <?php } ?>
                </ul>
            </nav>

        </div>

    </div>

</div>
	<?php } ?>


	<!-- Wrap all content -->
	<div id="preloader">
		<div class="spinner" id="preloader-status">
			<div class="rect2"></div>
		</div>
	</div>
	<div id="wrapper" class="clearfix">
	
		<div class="content-area">
			<!-- Header
		============================================= -->
<header id="header" data-sticky-class="not-dark" class="sticky-style-2">
    <div class="container clearfix">
		<?php if ($isMobile == false) { ?>
        	<div id="primary-menu-trigger"><i class="icon-reorder"></i></div>
		<?php } else { ?>
			<div id="side-panel-trigger" class="side-panel-trigger"><a href="#"><i class="icon-reorder"></i></a></div>
		<?php } ?>

        <!-- Logo
        ============================================= -->
        <div id="logo" class="m-b-lg-10">
            <a href="/" class="standard-logo">
                <?php if ($currentRoute == '/') { ?>
                <h1 class="hidden">
                    <span class="hidden">Gustour Conad - Gran Turismo del gusto e Agenda Conad</span>
                </h1>
                <?php } ?>
                <img src="/assets/site/images/logo.png" alt="Gustour Conad - Gran Turismo del Gusto">
                <a href="/" class="retina-logo"><img src="/assets/site/images/logo@2x.png" alt="Gustour Conad - Gran Turismo del Gusto"></a>

            </a>
        </div><!-- #logo end -->
		<?php if ($isMobile == false) { ?>
		<div class="top-banner">
			<?= $tags->renderBlock('banner-leaderboard') ?>
		</div>
		<?php } ?>

    </div>
	<div id="header-wrap">

			<!-- Primary Navigation
            ============================================= -->
			<nav id="primary-menu" class="style-2">
				<div class="container clearfix">
				<ul class="sf-js-enabled">
					<?php $menu = json_decode($tags->renderBlock('menu')); ?>
					<?php foreach ($menu as $item) { ?>
					<li
						class="
						<?php if ($item->class != null) { ?>
							<?= $item->class ?>
						<?php } ?>
						<?php if ($currentRoute == $item->href) { ?>
							current
						<?php } ?>"
					>
						<?php if (isset($item->blockTpl)) { ?>
							<?= $tags->renderBlock($item->blockTpl) ?>
						<?php } else { ?>
							<a href="<?= $item->href ?>">
								<div><?= $item->title ?></div>
							</a>
							<?php if ($item->submenu != null) { ?>
							<ul>
								<?php foreach ($item->submenu as $subitem) { ?>
								<li
									<?php if ($subitem->class != null) { ?>
									class="<?= $subitem->class ?>"
									<?php } ?>
								>
									<a href="<?= $subitem->href ?>">
										<div><?= $subitem->title ?></div>
									</a>
								</li>
								<?php } ?>
							</ul>
							<?php } ?>
						<?php } ?>
					</li>
					<?php } ?>
				</ul>

				</div>
				<!-- Top Search
                ============================================= -->
				<?php /*
				<div id="top-search">
					<a href="#" id="top-search-trigger"><i class="icon-search3"></i><i class="icon-line-cross"></i></a>
					<form action="search.html" method="get">
						<input type="text" name="q" class="form-control" value="" placeholder="Type &amp; Hit Enter..">
					</form>
				</div><!-- #top-search end -->
				*/ ?>
			</nav><!-- #primary-menu end -->
	</div>


</header><!-- #header end -->

			<?= $this->getContent() ?>

			<?php $_cache['site_footer'] = $this->di->get('viewCache'); $_cacheKey['site_footer'] = $_cache['site_footer']->start('site_footer', 14400); if ($_cacheKey['site_footer'] === null) { ?>
<!-- Footer
		============================================= -->
<footer id="footer">

	<div class="container">

		<?= $tags->renderBlock('footer') ?>
		<div class="footer-widgets-wrap clearfix">

			<div class="col_two_third">

				<div class="col_one_third">

					<div class="widget clearfix">

						<img src="/assets/site/images/logo.png" alt="Gustour Conad - Gran Turismo del gusto" class="footer-logo">
						<div>
							<abbr title="Phone Number"><strong>Tel:</strong></abbr> (91) 8547 632521<br>
							<abbr title="Fax"><strong>Fax:</strong></abbr> (91) 11 4752 1433<br>
							<abbr title="Email Address"><strong>Email:</strong></abbr> info@canvas.com
						</div>
						<div class="m-t-lg-10">
							<p class="f-w-600">Gran Turismo del Gusto</p>
						</div>

					</div>

				</div>

				<div class="col_one_third">

					<div class="widget widget_links clearfix">

						<h4>Blogroll</h4>

						<ul>
							<li><a href="http://codex.wordpress.org/">Documentation</a></li>
							<li><a href="http://wordpress.org/support/forum/requests-and-feedback">Feedback</a></li>
							<li><a href="http://wordpress.org/extend/plugins/">Plugins</a></li>
							<li><a href="http://wordpress.org/support/">Support Forums</a></li>
							<li><a href="http://wordpress.org/extend/themes/">Themes</a></li>
							<li><a href="http://wordpress.org/news/">WordPress Blog</a></li>
							<li><a href="http://planet.wordpress.org/">WordPress Planet</a></li>
						</ul>

					</div>

				</div>

				<div class="col_one_third col_last">

					<div class="widget clearfix">
						<h4>Recent Posts</h4>

						<div id="post-list-footer">
							<div class="spost clearfix">
								<div class="entry-c">
									<div class="entry-title">
										<h4><a href="#">Lorem ipsum dolor sit amet, consectetur</a></h4>
									</div>
									<ul class="entry-meta">
										<li>10th July 2014</li>
									</ul>
								</div>
							</div>

							<div class="spost clearfix">
								<div class="entry-c">
									<div class="entry-title">
										<h4><a href="#">Elit Assumenda vel amet dolorum quasi</a></h4>
									</div>
									<ul class="entry-meta">
										<li>10th July 2014</li>
									</ul>
								</div>
							</div>

							<div class="spost clearfix">
								<div class="entry-c">
									<div class="entry-title">
										<h4><a href="#">Debitis nihil placeat, illum est nisi</a></h4>
									</div>
									<ul class="entry-meta">
										<li>10th July 2014</li>
									</ul>
								</div>
							</div>
						</div>
					</div>

				</div>

			</div>

			<div class="col_one_third col_last">

				<div class="widget subscribe-widget clearfix">
					<h5><strong>Subscribe</strong> to Our Newsletter to get Important News, Amazing Offers &amp; Inside Scoops:</h5>
					<div class="widget-subscribe-form-result"></div>
					<form id="widget-subscribe-form" action="include/subscribe.php" role="form" method="post" class="nobottommargin" novalidate="novalidate">
						<div class="input-group divcenter">
							<span class="input-group-addon"><i class="icon-email2"></i></span>
							<input type="email" id="widget-subscribe-form-email" name="widget-subscribe-form-email" class="form-control required email" placeholder="Enter your Email" aria-required="true">
							<span class="input-group-btn">
										<button class="btn btn-success" type="submit">Subscribe</button>
									</span>
						</div>
					</form>
				</div>

				<div class="widget clearfix" style="margin-bottom: -20px;">

					<div class="row">

						<div class="col-md-6 clearfix bottommargin-sm">
							<a href="#" class="social-icon si-dark si-colored si-facebook nobottommargin" style="margin-right: 10px;">
								<i class="icon-facebook"></i>
								<i class="icon-facebook"></i>
							</a>
							<a href="#"><small style="display: block; margin-top: 3px;"><strong>Like us</strong><br>on Facebook</small></a>
						</div>
						<div class="col-md-6 clearfix">
							<a href="#" class="social-icon si-dark si-colored si-rss nobottommargin" style="margin-right: 10px;">
								<i class="icon-rss"></i>
								<i class="icon-rss"></i>
							</a>
							<a href="#"><small style="display: block; margin-top: 3px;"><strong>Subscribe</strong><br>to RSS Feeds</small></a>
						</div>

					</div>

				</div>

			</div>

		</div>

		<!-- Footer Widgets
        ============================================= -->
		<!-- .footer-widgets-wrap end -->

	</div>

	<!-- Copyrights
    ============================================= -->
	<div id="copyrights">

		<div class="container clearfix">

			<div class="col_half">
				&copy; <?= date('Y') ?> PAC 2000A Soc.coop.<br>
				<div class="copyright-links"><a href="/pagina/cookie-privacy-policy">Cookie & Privacy Policy</a></div>
			</div>

			<div class="col_half col_last tright">
				<div class="fright clearfix">
					<a href="https://www.facebook.com/gustour.conad" class="social-icon si-small si-borderless si-facebook">
						<i class="icon-facebook"></i>
						<i class="icon-facebook"></i>
					</a>

					<a href="#" class="social-icon si-small si-borderless si-twitter">
						<i class="icon-twitter"></i>
						<i class="icon-twitter"></i>
					</a>


					<a href="#" class="social-icon si-small si-borderless si-linkedin">
						<i class="icon-linkedin"></i>
						<i class="icon-linkedin"></i>
					</a>
				</div>

			</div>

		</div>

	</div><!-- #copyrights end -->

</footer><!-- #footer end -->
<!-- /FOOTER -->

<?php $_cache['site_footer']->save('site_footer', null, 14400); } else { echo $_cacheKey['site_footer']; } ?>
		</div>
	
	</div>

	<?php if ($additiveCss == true) { ?>
		<?= $this->assets->outputCss('additiveCss') ?>
	<?php } ?>
	<?= $this->assets->outputCss('cssSiteTheme') ?>
	<?= $tags->getInjectedCssByDi() ?>
	<?= $tags->renderBlock('custom-css') ?>

	<?= $this->tag->javascriptInclude('assets/site/js/jquery.js') ?>
	<?= $this->tag->javascriptInclude('assets/site/js/lib/jquery.ui.js') ?>

	<?= $this->assets->outputJs('jsSiteTheme') ?>
	<?php if ($additiveJs == true) { ?>
		<?= $this->assets->outputJs('additiveJs') ?>
	<?php } ?>

	<?= $tags->getInjectedJsByDi() ?>
	<?= $this->assets->outputJs() ?>

	<script type="text/javascript" id="cookiebanner"
		src="/assets/site/js/cookiebanner/cookiebanner.js"
		cookie="gustourconad-website"
		data-height="auto"
		data-effect="fade"
		data-link="#e70036"
		data-close-text="X"
		data-font-size="12px"
		data-moreinfo="/pagina/cookie-privacy-policy"
		data-linkmsg="Leggi"
		data-mask-opacity="1"
		data-accept-on-click="true"
		data-message="Questo sito utilizza solo cookie tecnici. Chiudendo questo banner o proseguendo la navigazione acconsenti all'uso di cookie.">
	</script>

</body>
</html>