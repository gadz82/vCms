<!-- Page Title
		============================================= -->
<section id="page-title">

    <div class="container clearfix">
        <h1>Area Utente</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Login</li>
        </ol>
    </div>

</section><!-- #page-title end -->
{{ get_content() }}
{{ flashSession.output() }}
{{ flash.output() }}
<!-- Content
============================================= -->
<section id="content">

    <div class="content-wrap">

        <div class="container clearfix">

            <div class="tabs divcenter nobottommargin clearfix" id="tab-login-register" style="max-width: 500px;">

                <ul class="tab-nav tab-nav2 center clearfix">
                    <li class="inline-block"><a href="#tab-login">Accedi</a></li>
                    <li class="inline-block"><a href="#tab-register">Registrati</a></li>
                </ul>

                <div class="tab-container">

                    <div class="row m-b-lg-15">
                        <div class="col-md-12">
                            <a class="btn btn-primary btn-block btn-lg" href="{{facebookLoginUrl}}"><i class="fa fa-facebook fa-fw"></i> Accedi con Facebook</a>
                        </div>

                    </div>

                    <div class="tab-content clearfix" id="tab-login">
                        <div class="panel panel-default nobottommargin">
                            <div class="panel-body" style="padding: 40px;">
                                <form id="login-form" name="login-form" class="nobottommargin" action="{{ url('/user/login') }}" method="post">

                                    <h3>Accedi</h3>

                                    <div class="col_full">
                                        <label for="login-form-username">Username:</label>
                                        <input type="text" id="login-form-username" name="username" value="" required class="form-control" />
                                    </div>

                                    <div class="col_full">
                                        <label for="login-form-password">Password:</label>
                                        <input type="password" id="login-form-password" name="password" required value="" class="form-control" />
                                    </div>
                                    <input type="hidden" name="{{ csrfTokenKey }}" value="{{ csrfToken }}" />

                                    <div class="col_full nobottommargin">
                                        <button class="button button-3d button-black nomargin" id="login-form-submit" name="login-form-submit" value="login">Login</button>
                                        <a href="/user/passwordLost" class="fright">Password dimenticata?</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="tab-content clearfix" id="tab-register">
                        <div class="panel panel-default nobottommargin">
                            <div class="panel-body" style="padding: 40px;">
                                <h3>Registrati</h3>

                                <form id="register-form" name="register-form" class="nobottommargin" action="{{ url('/user/registration') }}"  method="post">

                                    <div class="col_full">
                                        <label for="register-form-name">Nome:</label>
                                        <input type="text" id="register-form-name" name="nome" value="" class="form-control" required/>
                                    </div>
                                    <div class="col_full">
                                        <label for="register-form-name">Cognome:</label>
                                        <input type="text" id="register-form-cognome" name="cognome" value="" class="form-control" required/>
                                    </div>

                                    <div class="col_full">
                                        <label for="register-form-email">Email:</label>
                                        <input type="email" id="register-form-email" name="email" value="" class="form-control" required />
                                    </div>

                                    <div class="col_full">
                                        <label for="register-form-password">Password:</label>
                                        <input type="password" id="register-form-password" name="password" value="" class="form-control" />
                                    </div>

                                    <div class="col_full">
                                        <label for="register-form-repassword">Ripeti Password:</label>
                                        <input type="password" id="register-form-repassword" name="register-form-repassword" value="" class="form-control" />
                                    </div>
                                    <input type="hidden" name="{{ csrfTokenKey }}" value="{{ csrfToken }}" />
                                    <div class="col_full">
                                        <p>
                                            Accetto la privacy policy
                                            <input type="checkbox" name="privacy" required>
                                        </p>
                                    </div>
                                    <div class="col_full nobottommargin">
                                        <button class="button button-3d button-black nomargin" id="register-form-submit" name="register-form-submit" value="register">Registrati</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>

</section><!-- #content end -->
