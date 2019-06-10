<!-- Page Title
		============================================= -->
<section id="page-title">

    <div class="container clearfix">
        <h1>Imposta nuova Password</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item"><a href="/users/">Area Utente</a></li>
            <li class="breadcrumb-item active">Nuova Password</li>
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
            <p>Imposta la nuova password con la quale accedere al tuo account.</p>
            <form id="password-reset-form" name="password-reset-form" class="nobottommargin" action="{{ url('/user/passwordLost') }}"  method="post">

                <div class="col_full">
                    <label for="register-form-password">Password:</label>
                    <input type="password" id="reset-form-password" name="password" value="" class="form-control" />
                </div>

                <div class="col_full">
                    <label for="register-form-repassword">Ripeti Password:</label>
                    <input type="password" id="reset-form-repassword" name="reset-form-repassword" value="" class="form-control" />
                </div>

                <input type="hidden" name="{{ csrfTokenKey }}" value="{{ csrfToken }}" />
                <input type="hidden" name="id_user" value="{{ id_user }}" />
                <div class="col_full nobottommargin">
                    <button class="button button-3d button-black nomargin" id="password-reset-submit" name="password-reset-form-submit" value="reset">Imposta Password</button>
                </div>
            </form>

        </div>

    </div>

</section><!-- #content end -->
