<!-- Header
		============================================= -->
<header id="header" class="no-sticky
<?php /*
{% if currentRoute == '/' %}
transparent-header page-section dark
{% endif %}
*/ ?>
full-header
">

	<div id="header-wrap">
		<div class="container clearfix">
			<div id="primary-menu-trigger"><i class="icon-reorder"></i></div>

			<!-- Logo
			============================================= -->
			<div id="logo">
				{% if currentRoute == '/' %}
					<h1 class="hidden">
						<span class="hidden">{{ appConfig.appName }} - {{ appConfig.appDescription }}</span>
					</h1>
					<a href="/" class="standard-logo" data-dark-logo="/assets/site/images/logo-dark.png"><img src="/assets/site/images/logo.png" alt="{{ appConfig.appName }} - {{ appConfig.appDescription }}"></a>
					<a href="/" class="retina-logo" data-dark-logo="/assets/site/images/logo-dark@2x.png"><img src="/assets/site/images/logo@2x.png" alt="{{ appConfig.appName }} - {{ appConfig.appDescription }}"></a>
				{% else %}
					<a href="/" class="standard-logo" data-dark-logo="/assets/site/images/logo-dark.png"><img src="/assets/site/images/logo.png"alt="{{ appConfig.appName }} - {{ appConfig.appDescription }}"></a>
					<a href="/" class="retina-logo" data-dark-logo="/assets/site/images/logo-dark@2x.png"><img src="/assets/site/images/logo@2x.png" alt="{{ appConfig.appName }} - {{ appConfig.appDescription }}"></a>
				{% endif %}
			</div><!-- #logo end -->

			<!-- Primary Navigation
			============================================= -->
			<!-- Primary Navigation
			============================================= -->
			<nav id="primary-menu" class="not-dark">

				<ul class="sf-js-enabled">
					{% set menu = tags.renderBlock('menu', false)|json_decode %}
					{% for item in menu %}
					<li
						class="
						{% if item.class is not null %}
							{{item.class}}
						{%endif%}
						{% if currentRoute == item.href %}
							current
						{%endif%}"
					>
						{% if item.blockTpl is defined %}
							{{tags.renderBlock(item.blockTpl)}}
						{% else %}
							<a href="{{item.href}}">
								<div>{{item.title}}</div>
							</a>
							{% if item.submenu is not null %}
							<ul>
								{% for subitem in item.submenu %}
								<li
									{% if subitem.class is not null %}
									class="{{subitem.class}}"
									{% endif %}
								>
									<a href="{{subitem.href}}">
										<div>{{subitem.title}}</div>
									</a>
								</li>
								{% endfor %}
							</ul>
							{% endif %}
						{% endif %}
					</li>
					{% endfor %}
				</ul>

			</nav><!-- #primary-menu end -->
		</div>
	</div>

</header><!-- #header end -->