var login =
  {
  	init: function()
  	  {
  	  	var self = this;
  	  	$(document).on('click', "#kt_login_signin_submit", {showSigninForm: self.showSigninForm}, self.login);
        $(document).on('click', "#kt_login_forgot_submit", {showSigninForm: self.showSigninForm}, self.recover);
        $(document).on('click', "#kt_login_forgot", self.showForgotForm);
        $(document).on('click', "#kt_login_forgot_cancel", {showSigninForm: self.showSigninForm}, self.cancelForgot);
  	  	return this;
  	  },
  	login: function(e)
  	  {
  	  	e.preventDefault();
  	  	var data = $("#login-form").serialize();
  	  	$.ajax(
  	  	  {
  	  	  	url: '/login/login',
  	  	  	data: data,
  	  	  	method: 'POST',
  	  	  	dataType: 'JSON',
  	  	  	success: function(resposta)
  	  	  	  {
  	  	  	  	if(resposta.success === true)
  	  	  	  	  {
                    var redir = (typeof resposta.landing_page !== "undefined") ? resposta.landing_page : ""; 
  	  	  	  	  	window.location.href = "/" + redir;
  	  	  	  	  }	
  	  	  	  	else
  	  	  	  	  {
  	  	  	  	  	toastr.warning('Credenciais incorretas');
  	  	  	  	  }
  	  	  	  }
  	  	  });
  	  },
    recover: function(e)
      {
        e.preventDefault();
        var showSigninForm = e.data.showSigninForm;
        var data = $("#recover-form").serialize();
        $.ajax(
          {
            url: '/login/recover',
            data: data,
            method: 'POST',
            dataType: 'JSON',
            success: function(resposta)
              {
                if(resposta.success === true)
                  {
                    toastr.success("Email de recuperação enviado com sucesso!");
                    showSigninForm();
                  } 
                else
                  {
                    toastr.warning(resposta.reason);
                  }
              }
          });
      },
    showForgotForm: function() 
      {
        var login = $('#kt_login');
        login.removeClass('kt-login--signin');
        login.removeClass('kt-login--signup');
        login.addClass('kt-login--forgot');
      },
    cancelForgot: function(e)
      {
        e.preventDefault();
        e.data.showSigninForm();
      },
    showSigninForm: function()
      {
        var login = $('#kt_login');
        login.removeClass('kt-login--forgot');
        login.removeClass('kt-login--signup');
        login.addClass('kt-login--signin');
      }
  }
$(document).ready(function()
  {
  	login.init();
  })