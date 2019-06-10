<!-- Page Title
		============================================= -->
<section id="page-title">

    <div class="container clearfix">
        <h1>Il mio Account</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item"><a href="/users/">Area Utente</a></li>
            <li class="breadcrumb-item active">Profilo</li>
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

            <div class="row clearfix">

                <div class="col-sm-9">
                    <div class="heading-block noborder">
                        <h3>{{user.nome}} {{user.cognome}}</h3>
                        <span>Gestisci Account</span>
                    </div>

                    <div class="clear"></div>
                    <div class="row clearfix">
                        <div class="col-md-12">
                            <div class="clearfix" >
                                <p>Ciao {{user.nome}}, benvenuto nel tuo account utente nel sito. Completa o modifica le informazioni sottostanti e premi il pulsante Salva.</p>
                                <form id="user-form" name="user-form" class="nobottommargin" action="{{ url('/user/editProfile') }}"  method="post">
                                    <div class="row m-b-lg-15">
                                        <div class="col-sm-6">
                                            <label for="register-form-name">Nome:</label>
                                            <input type="text" id="register-form-name" name="nome" value="{{user.nome}}" class="form-control" required/>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="register-form-name">Cognome:</label>
                                            <input type="text" id="register-form-cognome" name="cognome" value="{{user.cognome}}" class="form-control" required/>
                                        </div>
                                    </div>

                                    <div class="col_full">
                                        <label for="register-form-email">Email:</label>
                                        <input type="email" id="register-form-email" name="email" value="{{user.email}}" class="form-control" required />
                                    </div>
                                    <div class="col_full">
                                        <label for="username">Username:</label>
                                        <input type="text" id="username" name="username" value="{{user.username}}" class="form-control" required />
                                        <small>Cambiando l'username potrai accedere utilizzando un nickname diverso dalla email di registrazione</small>
                                    </div>
                                    <hr>
                                        <div>
                                            <h5>Informazioni Aggiuntive</h5>
                                            <div class="col_full">
                                                <label for="telefono">Telefono:</label>
                                                <input type="text" id="telefono" name="telefono" value="{{user.telefono}}" class="form-control" />
                                            </div>

                                            <div class="col_full">
                                                <label for="indirizzo">Indirizzo:</label>
                                                <input type="text" id="indirizzo" name="indirizzo" value="{{user.indirizzo}}" class="form-control" />
                                            </div>
                                            <div class="row m-b-lg-15">
                                                <div class="col-sm-6">
                                                    <label for="localita">Località:</label>
                                                    <input type="text" id="localita" name="localita" value="{{user.localita}}" class="form-control" />
                                                </div>
                                                <div class="col-sm-6">
                                                    <label for="localita">Cap:</label>
                                                    <input type="text" id="cap" name="cap" value="{{user.cap}}" class="form-control" />
                                                </div>
                                            </div>
                                            <div class="col_full input-daterange">
                                                <label for="data_di_nascita">Data di Nascita:</label>
                                                <input type="text" id="data_di_nascita" value="{{user.data_di_nascita}}" name="data_di_nascita" class="form-control">
                                            </div>
                                        </div>
                                    <hr>
                                        <div>
                                            <h5>Modifica Password</h5>
                                            <div class="col_full">
                                                <label for="old-password">Vecchia Password:</label>
                                                <input type="password" id="old-password" name="old-password" value="" class="form-control" />
                                            </div>

                                            <div class="col_full">
                                                <label for="new-password">Nuova Password:</label>
                                                <input type="password" id="new-password" name="new-password" value="" class="form-control" />
                                            </div>
                                            <div class="col_full">
                                                <label for="new-repassword">Ripeti Nuova Password:</label>
                                                <input type="password" id="new-repassword" name="new-repassword" value="" class="form-control" />
                                            </div>
                                        </div>
                                    <hr>
                                    <input type="hidden" name="{{ csrfTokenKey }}" value="{{ csrfToken }}" />
                                    <input type="hidden" name="id_user" value="{{ user.id }}" />
                                    <div class="col_full nobottommargin">
                                        <button class="button button-3d button-black nomargin" id="user-form-submit" name="user-form-submit" value="register">Salva</button>
                                    </div>
                                </form>
                            </div>

                        </div>

                    </div>

                </div>

                <div class="line visible-xs-block"></div>

                <div class="col-sm-3 clearfix">

                    <div class="list-group">

                        <a href="/user/logout" class="list-group-item clearfix">Logout <i class="icon-line2-logout pull-right"></i></a>
                        <a id="user-delete" href="/user/delete" class="list-group-item clearfix list-group-item-danger">Elimina Profilo<i class="fa fa-close pull-right"></i></a>
                    </div>

                    <div class="fancy-title topmargin title-border">
                        <h4>Profilo Utente</h4>
                    </div>

                    <p>Completa il profilo utente con i tuoi dati. Aggiorna le tue informazioni, ti ricordiamo che gli utenti registrati hanno diretto accesso a contenuti e file riservati.</p>


                </div>

            </div>

        </div>

    </div>

</section><!-- #content end -->

