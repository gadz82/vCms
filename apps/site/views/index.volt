<!DOCTYPE html>
<html lang="{{ applicationHrefLang }}">
<head>
	<meta charset="utf-8">
	{{ get_title() }}
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="language" content="{{ applicationHrefLang }}" />
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
	{{ tags.getMetaDescription() }}
	{{ tags.getCanonicalUrl() }}
	{{ tags.getPaginationLinks() }}
	{{ tags.getRobots() }}
	<meta property="og:type" content="website" />
	<meta property="og:site_name" content="Marchi Auto - Concessionaria Fiat, Abarth, Alfa Romeo in Umbria" />
	<meta property="og:locale" content="it_IT" />
	{{ tags.getOgTitle() }}
	{{ tags.getOgUrl() }}
	{{ tags.getOgImage() }}
	{{ tags.getOgDescription() }}
	{{ tags.getOgVideo() }}
	{{ tags.getAdditionalHeading() }}

	{{ stylesheet_link('https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css') }}
	{{ tags.outputCssInline('inlineCssSiteTheme') }}
	{{ assets.outputCss('cssSiteTheme') }}
	<script type="application/ld+json">
	{{ tags.getStructs() }}
	</script>

	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
	<![endif]-->

	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-xxxx-x"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());

		gtag('config', 'UA-xxxx-x');
	</script>

	<style>
		@import url('https://fonts.googleapis.com/css?family=Raleway:400,100,200,300,500,700,800,900,600');
		@import url('https://fonts.googleapis.com/css?family=Lato:400,300,300italic,400italic,600,600italic,700,700italic,800,800italic');
	</style>
	<!-- Global site tag (gtag.js) - Google Analytics -->

</head>
{% block bodyClass %}
<body class="stretched {% if isMobile %}side-panel-left side-push-panel{% endif %}">
	{% if isMobile %}
		<div class="body-overlay"></div>
		{% include "partials/menu-mobile.volt" %}
	{% endif %}

{% endblock %}
	<!-- Wrap all content -->
	<div id="preloader">
		<div class="spinner" id="preloader-status">
			<div class="rect2"></div>
		</div>
	</div>
	<div id="wrapper" class="clearfix">
	{% block wrapper %}
		<div class="content-area">
			{% include "partials/header.volt" %}

			{{ content() }}

			{% include "partials/footer.volt" %}
		</div>
	{% endblock %}
	</div>

	{% if additiveCss is true %}
		{{ assets.outputCss('additiveCss') }}
	{% endif %}
	{{ assets.outputCss('cssSiteTheme') }}
	{{ tags.getInjectedCssByDi() }}
	{{ tags.renderBlock('custom-css') }}

	{{ javascript_include('assets/site/js/jquery.js') }}
	{{ javascript_include('assets/site/js/lib/jquery.ui.js') }}

	{{ assets.outputJs('jsSiteTheme') }}
	{% if additiveJs is true %}
		{{ assets.outputJs('additiveJs') }}
	{% endif %}

	{{ tags.getInjectedJsByDi() }}
	{{ assets.outputJs() }}
	{{ assets.outputCss() }}
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