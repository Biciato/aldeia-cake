<?php 
$horarios_visita = 
  [
    '08:30',
    '09:00',
    '09:30',
    '10:00',
    '10:30',
    '14:00',
    '14:30',
    '15:00',
    '15:30',
    '16:00'
  ];
?>
<!DOCTYPE html>
<html lang="pt" >
    <!-- begin::Head -->
    <head>
        <meta charset="utf-8"/>
        
        <title>Agende uma visita | Aldeia Montessori</title>
        <meta name="description" content="Adicionar novo prospect"> 
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no" />
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
        <?php echo $this->Html->css('/vendors/general/bootstrap-datepicker/dist/css/bootstrap-datepicker3.css'); ?>
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
    <div id="full-preloader" class="hide"></div>
       
      <!-- begin:: Page -->
  <div class="kt-grid kt-grid--ver kt-grid--root">
    <div class="kt-grid kt-grid--hor kt-grid--root kt-login kt-login--v2 kt-login--signin" id="kt_login">
  <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
    <div class="kt-grid__item kt-grid__item--fluid kt-login__wrapper">
      <div class="kt-login__container" style="top:0%">
        <div class="kt-login__logo">
          <a href="https://www.aldeiamontessori.com.br">
            <img src="/img/logo.png" style="max-width: 218px">    
          </a>
        </div>
        <div class="kt-login__signin">
          <div class="kt-prospect">
            <div class="kt-login__head">
              <h3 class="kt-login__title color-dark-gray">Agende uma visita</h3>
              <p style="margin-top:25px; font-weight:normal">
                Obrigada pelo seu seu interesse em nossa Instituição. <br/>
                Será um prazer receber você em uma de nossas Unidades. Desta forma poderá conhecer nossa estrutura e o projeto pedagógico que desenvolvemos fundamentado no Sistema Montessori. Nosso funcionamento é de 07h às 19h. Entretanto, nos horário entre 08:30h e 10:30h pela manhã e 14h e 16h na parte da tarde são os melhores para que você possa ver a escola em plena atividade. Esperamos por você! <br/>
              </p>
            </div>
            <?php echo $this->Form->create(null, ['id' => 'prospect-form', 'class' => 'kt-form']); ?>
              <div class="input-group">
                <input class="form-control white-login-input" type="text" placeholder="Seu nome" name="parentes[0][pessoa-parente][nome]" autocomplete="off">
              </div>
              <div class="input-group">
                <select class="form-control white-login-input" name="parentes[0][parentesco]">
                  <option value="">Selecione o seu parentesco com o aluno</option>
                  <?php foreach($config['parentescos'] as $id => $parentesco)
                    {
                     ?>
                       <option value="<?php echo $id; ?>"><?php echo $parentesco; ?></option>
                     <?php
                    } 
                  ?>
                </select>
              </div>
              <div class="input-group">
                <input class="form-control white-login-input" type="tel" placeholder="Seu telefone" name="parentes[0][pessoa-parente][telefones][]" autocomplete="off">
              </div>
              <div class="input-group">
                <input class="form-control white-login-input" type="email" placeholder="Seu email" name="parentes[0][pessoa-parente][email]" autocomplete="off">
              </div>
              <div class="input-group">
                <input class="form-control white-login-input" type="text" placeholder="Nome do aluno" name="pessoa-prospect[nome]" autocomplete="off">
              </div>
              <div class="input-group">
                <input class="form-control white-login-input" type="text" name="pessoa-prospect[data_nascimento]" placeholder="Data de nascimento do aluno" autocomplete="off">
              </div>
              <div class="input-group prospect-externo-datetime-wrapper prospect-externo-date-wrapper">
                <input class="form-control white-login-input" type="text" placeholder="Data da visita" name="interacao[data]" autocomplete="off">
              </div>
              <div class="input-group prospect-externo-datetime-wrapper prospect-externo-time-wrapper">
                 <select class="form-control white-login-input" name="interacao[hora]">
                  <option value="">Hora da visita</option>
                  <?php foreach($horarios_visita as $horario)
                    {
                     ?>
                       <option value="<?php echo $horario; ?>"><?php echo $horario; ?></option>
                     <?php
                    } 
                  ?>
                </select>
              </div>
              <div class="input-group">
                <select class="form-control white-login-input" name="unidade">
                  <option value="">Selecione a unidade</option>
                  <?php foreach($config['unidades'] as $id => $unidade)
                    {
                     ?>
                       <option value="<?php echo $id; ?>"><?php echo $unidade; ?></option>
                     <?php
                    } 
                  ?>
                </select>
              </div>
              <div class="input-group">
                <select class="form-control white-login-input" name="como_conheceu">
                  <option value="">Como conheceu a Aldeia?</option>
                  <?php foreach($config['meios_conhecimento'] as $id => $meio)
                    {
                     ?>
                       <option value="<?php echo $id; ?>"><?php echo $meio; ?></option>
                     <?php
                    } 
                  ?>
                </select>
              </div>
              <div class="input-group" style="margin-bottom:20px; margin-top:20px">
                <div class="g-recaptcha" data-sitekey="6Lflg7YUAAAAACO-UL7rodg-U2xm4NfQAMnO50lG"></div>
              </div>
              <div class="kt-login__actions" style="text-align: left">
                <a href="https://www.aldeiamontessori.com.br" class="btn btn-secondary transition-024" id="kt_back">Voltar</a> &nbsp;&nbsp;
                <button id="insert-prospect" class="btn btn-pill kt-login__btn-primary transition-024" style="float:right">Enviar</button>
              </div>
              <input type="hidden" name="origem" value="1">
            <?php echo $this->Form->end(); ?>
          </div>
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
        <?php 
          echo $this->Html->script('/vendors/general/jquery/dist/jquery.js');
          echo $this->Html->script('https://apis.google.com/js/platform.js');
          echo $this->Html->script('vanilla-masker');
          echo $this->Html->script('toastr.min'); 
          echo $this->Html->script('/vendors/general/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js'); 
          echo $this->Html->script('/vendors/custom/js/vendors/bootstrap-datepicker.init.js'); 
          echo $this->Html->script('datepicker-pt-br');
          echo $this->Html->script('datepicker-pt-br_full');
          echo $this->Html->script('https://www.google.com/recaptcha/api.js', ['async', 'defer']);
        ?>
        <script type="text/javascript">
            toastr.options = {
              "positionClass": "toast-top-center",
            };
        </script>
        <?php  
          echo $this->Html->script('prospect-externo');
        ?>
  </body>
    <!-- end::Body -->
</html>