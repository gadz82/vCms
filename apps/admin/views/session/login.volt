{% extends "index.volt" %} {% block bodyClass %}
<body class="login-page skin-green">
	{% endblock%} {% block wrapper %}
	<div class="login-box">
		<div class="login-logo">
			<p>
				<strong>{{ application.appName }}</strong><br>Admin Panel
			</p>
		</div>

		<div class="login-box-body">
			<p class="login-box-msg">Effettua l&apos;accesso</p>
				
				<?php $this->flashSession->output(); ?>
				
				<form action="" method="post">

				<div class="form-group has-feedback">
					{{ form.render('username') }} <span
						class="glyphicon glyphicon-envelope form-control-feedback"></span>
				</div>

				<div class="form-group has-feedback">
					{{ form.render('password') }} <span
						class="glyphicon glyphicon-lock form-control-feedback"></span>
				</div>

				{{ form.render('csrf', ['value': security.getToken()]) }}

				<div class="row">
					<div class="col-xs-8">
						<div class="checkbox icheck">
							<label><input id="remember" name="remember" type="checkbox" /><span>Ricordami</span></label>
						</div>
					</div>
					<div class="col-xs-4">
						<button type="submit" class="btn btn-primary btn-block btn-flat">
							<span>Accedi</span>
						</button>
					</div>
				</div>
			</form>

		</div>
		<div class="text-center" style="margin-top:15px;">
			<a href="/" target="_blank">Vai al sito</a>
		</div>
	</div>
	{% endblock%}