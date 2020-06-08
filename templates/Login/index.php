
<!DOCTYPE html>
<html lang="pt" >
    <!-- begin::Head -->
    <head>
        <meta charset="utf-8"/>
        
        <title>Login | Aldeia Montessori</title>
        <meta name="description" content="Login page example"> 
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="google-signin-client_id" content="309408464657-t6r6lebujdgfifcfp67lik5pnt2d7i4q.apps.googleusercontent.com">

        <!--begin::Fonts -->
        <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
        <script>
            WebFont.load({
                google: {"families":["Poppins:300,400,500,600,700","Roboto:300,400,500,600,700"]},
                active: function() {
                    sessionStorage.fonts = true;
                }
            });
        </script>
        <!--end::Fonts -->
                    
        <!--begin::Page Custom Styles(used by this page) --> 
          	<?php echo $this->Html->css('demo1/login-2'); ?>
        <!--end::Page Custom Styles -->
        
        <!--begin::Global Theme Styles(used by all pages) -->
            <?php echo $this->Html->css('vendors.bundle'); ?>
            <?php echo $this->Html->css('style.bundle'); ?>
        <!--end::Global Theme Styles -->

        <!--begin::Layout Skins(used by all pages) -->
        <?php echo $this->Html->css('skins/header/base/light'); ?>
        <?php echo $this->Html->css('skins/header/menu/light'); ?>
        <?php echo $this->Html->css('skins/brand/dark'); ?>
        <?php echo $this->Html->css('skins/aside/dark'); ?>
        <?php echo $this->Html->css('toastr'); ?>
        <?php echo $this->Html->css('aldeia-custom-css'); ?>

        <link rel="shortcut icon" href="/img/favicon.png" />
    </head>
    <!-- end::Head -->

    <!-- begin::Body -->
    <body  class="kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--fixed kt-subheader--enabled kt-subheader--solid kt-aside--enabled kt-aside--fixed kt-page--loading"  >
    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v3.3&appId=704093496714310&autoLogAppEvents=1"></script>
    <div id="full-preloader" class="hide"></div>
       
    	<!-- begin:: Page -->
	<div class="kt-grid kt-grid--ver kt-grid--root">
		<div class="kt-grid kt-grid--hor kt-grid--root kt-login kt-login--v2 kt-login--signin" id="kt_login">
	<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
		<div class="kt-grid__item kt-grid__item--fluid kt-login__wrapper">
			<div class="kt-login__container">
				<div class="kt-login__logo">
					<a href="#">
						<img src="/img/logo.png" style="max-width: 218px">  	
					</a>
				</div>
				<div class="kt-login__signin">
                    <div class="col-sm-6 pull-left">
                        <div class="col-sm-12 pull-left margin-bottom-30">
                          <h3 class="color-dark-gray margin-bottom-15">
                            Bem vindo!
                          </h3>
                          <p class="color-dark-gray margin-bottom-15">
                             Use o Facebook, conta do Google ou email para acesso
                          </p>  
                        </div>
                        <div class="col-sm-12 pull-left margin-bottom-15">
                            <div class="btn btn-fb btn-block" onclick="FB.login(function(response){facebookLogin(response)})">
                                <b>
                                    Fazer login com o Facebook
                                </b>
                            </div>
                        </div>
                        <div class="col-sm-12 pull-left">
                            <div class="btn btn-google btn-block" id="googleLogin" data-onsuccess="googleLogin">
                                <b>
                                    Fazer login com o Google
                                </b>
                            </div>
                        </div>
                        <div style="display:none">
                            <div class="fb-login-button" data-width="100%" data-size="large" data-button-type="login_with" data-auto-logout-link="false" login_text="Faça login com o Facebook" data-scope="email" data-use-continue-as="false" onlogin="facebookLogin"></div>
                        </div>
                    </div>
                    <div class="col-sm-6 pull-left login-form-wrapper">
                        <?php echo $this->Form->create(null, ['id' => 'login-form', 'class' => 'kt-form']); ?>
                            <div class="input-group">
                                <input class="form-control white-login-input" type="text" placeholder="Informe o Email ou CPF" name="email" autocomplete="off">
                            </div>
                            <div class="input-group">
                                <input class="form-control white-login-input" type="password" placeholder="Senha" name="senha">
                            </div>
                            <div class="row kt-login__extra">
                                <div class="col">
                                </div>
                                <div class="col kt-align-right color-dark-gray padding-0">
                                    <a href="javascript:;" id="kt_login_forgot" class="kt-link kt-login__link color-dark-gray">Esqueceu a senha?</a>
                                </div>
                            </div>
                            <div class="kt-login__actions color-dark-gray text-right">
                                <button id="kt_login_signin_submit" class="btn kt-login__btn-primary transition-024">Entrar</button>
                            </div>
                        <?php echo $this->Form->end(); ?>
                    </div>
				</div>
				<div class="kt-login__forgot">
					<div class="kt-login__head">
						<h3 class="kt-login__title color-dark-gray">Esqueceu sua senha?</h3>
						<div class="kt-login__desc color-dark-gray">Insira seu email para recuperar o acesso à sua conta</div>
					</div>
					<?php echo $this->Form->create(null, ['id' => 'recover-form', 'class' => 'kt-form']); ?>
						<div class="input-group">
							<input class="form-control white-login-input" type="text" placeholder="Email" name="email" id="kt_email" autocomplete="off">
						</div>
						<div class="kt-login__actions">
							<button id="kt_login_forgot_submit" class="btn btn-pill kt-login__btn-primary transition-024">Enviar</button>&nbsp;&nbsp;
							<button id="kt_login_forgot_cancel" class="btn btn-pill kt-login__btn-secondary transition-024">Voltar</button>
						</div>
					<?php echo $this->Form->end(); ?>
				</div>
			</div>	
		</div>
	</div>
</div>	
</div>
<!-- end:: Page -->

        <!-- begin::Global Config(global config for global JS sciprts) -->
        <script>
            var KTAppOptions = {"colors":{"state":{"brand":"#5d78ff","dark":"#282a3c","light":"#ffffff","primary":"#5867dd","success":"#34bfa3","info":"#36a3f7","warning":"#ffb822","danger":"#fd3995"},"base":{"label":["#c5cbe3","#a1a8c3","#3d4465","#3e4466"],"shape":["#f0f3ff","#d9dffa","#afb4d4","#646c9a"]}}};
        </script>
        <!-- end::Global Config -->

    	<!--begin::Global Theme Bundle(used by all pages) 
    	    	   <script src="./assets/vendors/global/vendors.bundle.js" type="text/javascript"></script>
		    	   <script src="./assets/js/demo1/scripts.bundle.js" type="text/javascript"></script>
				<!--end::Global Theme Bundle -->
        <?php echo $this->Html->script('/vendors/general/jquery/dist/jquery.js'); ?>
        <?php echo $this->Html->script('https://apis.google.com/js/platform.js'); ?>
        
        <?php echo $this->Html->script('toastr.min'); ?>
        <?php echo $this->Html->script('login'); ?>
        <?php echo $this->Html->script('social-login'); ?>
            <!--begin::Page Scripts(used by this page) -->
                            <script src="./assets/js/demo1/pages/login/login-general.js" type="text/javascript"></script>
                        <!--end::Page Scripts -->
        <script>startGoogleApi();</script>
        <?php if((@$password_changed === true))
          {
            ?>
            <script>
                toastr.success("Sua senha foi alterada com sucesso!");
            </script>
            <?php
          }
          else if((@$password_changed === false))
          {
            ?>
            <script>
                toastr.error("Link inválido ou expirado!");
            </script>
            <?php
          }  ?>
            </body>
    <!-- end::Body -->
</html>