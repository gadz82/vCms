 {% set avatar = 'img/' ~ user['avatar'] %}

<header class="main-header">

	{{ link_to('admin/index',application.appName, 'class':'logo' )}}

	<nav class="navbar navbar-static-top" role="navigation">
		<a href="#" class="sidebar-toggle" data-toggle="offcanvas"
			role="button"><span class="sr-only">Toggle navigation</span></a>

		<!-- Navbar Right Menu -->
		<div class="navbar-custom-menu">
			<ul class="nav navbar-nav">
				<li class="dropdown" style="margin-right:15px;">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id="currentAppDD">
						{% if currentApp is not defined or currentApp is null %}
							Scegli App
						{% else %}
							{{ currentApp['codice']~' - '~currentApp['titolo'] }}
						{% endif %}
					<span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu" id="appSwitcher">

						<li{% if currentApp is not defined or currentApp %} class="active"{% endif %}><a href="#" data-idapp="0">Tutte le App</a></li>

						<li class="divider"></li>
						{% for application in availableApps %}
							<li{% if application.id === currentApp['id'] %} class="active"{% endif %}><a href="#" data-idapp="{{ application.id }}"><b>{{ application.codice }}</b> - {{ application.descrizione }}</a></li>
						{% endfor %}

					</ul>
				</li>
				<li class="dropdown tasks-menu" style="margin-right:30px;">
					<a href="/" target="_blank">
						<i class="fa fa-globe fa-lg"></i>
						<span class="label label-info">VAI AL SITO</span>
					</a>
				</li>
				<li class="dropdown tasks-menu" style="margin-right:40px;">
					<a href="#" data-target="#files-manager-modal" id="file-manager-control" data-toggle="modal">
						<i class="fa fa-image fa-lg"></i>
						<span class="label label-primary">FILE MANAGER</span>
					</a >
				</li>
				{% for k in header_alert|keys %}
					{% include "partials/header_alert/"~k with ['h_data': header_alert[k]] %}
				{% endfor %}

				<li class="dropdown user user-menu">
					<a href="#"
						class="dropdown-toggle" data-toggle="dropdown"> {{ image(avatar, 'class':'user-image', 'alt':'User Image') }}
						<span class="hidden-xs">{{ user['nome']|e|capitalize ~ ' ' ~user['cognome']|e|capitalize }}</span>
					</a>
					<ul class="dropdown-menu">
						<li class="user-header">{{ image(avatar, 'class':'img-circle', 'alt':'User Image') }}
							<p>
								{{ user['nome']|e|capitalize ~ ' ' ~ user['cognome']|e|capitalize }}<small>{{ user['email'] }}</small>
							</p>
							<p>{{ user['ruolo']|e|capitalize }}</p>
						</li>

						<li class="user-footer">
							<div class="text-center">
								{{ link_to('admin/session/logout', '<i class="fa fa-power-off fa-fw"></i> Esci', 'class':'btn btn-danger bn-flat') }}
								{% if user['id_ruolo'] == 1 %}
									{{ link_to('admin/session/destroy', '<i class="fa fa-trash fa-fw"></i> Cache', 'class':'btn btn-info bn-flat') }}
									{{ link_to('admin/session/toggleDebug', '<iclass="fa fa-bug fa-fw"></i> Debug', 'class':'btn btn-success bn-flat') }}
								{% endif %}
							</div>
						</li>
					</ul>
				</li>


			</ul>
		</div>
	</nav>
</header>

