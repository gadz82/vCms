<!-- Page Title
		============================================= -->
<section id="page-title">

    <div class="container clearfix">
        <h1>Attivazione Account</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item"><a href="/users/">Area Utente</a></li>
            <li class="breadcrumb-item active">Attivazione Account</li>
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
            <p>Inserisci l'indirizzo email che hai utilizzato per effettuare la registrazione. Riceverai una mail con il link dia ttiva</p>
            <form id="send-activation-form" name="send-activation-form" class="nobottommargin" action="{{ url('/user/sendActivation') }}"  method="post">

                <div class="col_full">
                    <label for="register-form-name">Email usata per la registrazione:</label>
                    <input type="email" id="register-form-email" name="email" value="" class="form-control" required />
                </div>

                <input type="hidden" name="{{ csrfTokenKey }}" value="{{ csrfToken }}" />
                <div class="col_full nobottommargin">
                    <button class="button button-3d button-black nomargin" id="activation-form-submit" name="activation-form-form-submit" value="register">Invia</button>
                </div>
            </form>

        </div>

    </div>

</section><!-- #content end -->
