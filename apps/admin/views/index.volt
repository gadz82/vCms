<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">

<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">

{{ get_title() }}

<meta name="description" content="CMS.IO Admin">
<meta name="author" content="Desegno">

<link rel="shortcut icon" href="/favicon.ico" />

{{ stylesheet_link('assets/admin/css/bootstrap/bootstrap.min.css') }} {{
stylesheet_link('https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css') }} {{
assets.outputCss('cssAdminTheme') }} {{ assets.outputCss() }} {% if
additiveCss is true %} {{ assets.outputCss('additiveCss') }} {% endif %}

{{ stylesheet_link('assets/admin/css/common.css') }}

<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
	<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
<![endif]-->

</head>

{% block bodyClass %}
<body class="skin-green fixed">
	{% endblock %} {% block wrapper %}
	<div class="wrapper">

		{% include "partials/header" with ['user': auth_user, 'header_alert': header_alert, 'current_app' : (currentApp is defined) ? currentApp : null] %}
		{% include "partials/sidebar" with ['user_menu': auth_menu] %}
		<div class="content-wrapper">
			{{ content() }}
			{{ partial("partials/fileManagerModal") }}
		</div>
		{% include "partials/footer.volt" %}

	</div>
	{% endblock %} {{ javascript_include('assets/admin/js/jquery/jquery.min.js') }} {{
	javascript_include('assets/admin/js/bootstrap/bootstrap.min.js') }} {{
	javascript_include('assets/admin/js/AdminLTE/app.min.js') }} {{
	assets.outputJs('jsAdminTheme') }} {{ javascript_include('assets/admin/js/common.js') }}

	{{ assets.outputJs() }} {% if additiveJs is true %} {{
	assets.outputJs('additiveJs') }} {% endif %}
</body>
</html>