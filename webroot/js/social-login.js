    var startGoogleApi = function()
      {
        gapi.load('auth2', function(){
              window['auth2'] = gapi.auth2.init({
                client_id: '309408464657-t6r6lebujdgfifcfp67lik5pnt2d7i4q.apps.googleusercontent.com',
                cookiepolicy: 'single_host_origin',
              });
              bindLogin(document.getElementById('googleLogin'));
            });
      }
    var bindLogin = function(element)
      {
        window['auth2'].attachClickHandler(element, {},
                googleLogin, function(error) {
                  alert(JSON.stringify(error, undefined, 2));
                });
      }
    var googleLogout = function() 
      {
        var auth2 = gapi.auth2.getAuthInstance();
        auth2.signOut();
      }
    var googleLogin = function(usr)
      {
        var profile = usr.getBasicProfile();
        var email   = profile.getEmail();
        var token   = usr.getAuthResponse().id_token;
        var _csrfToken = $("[name='_csrfToken']").val();
        $.ajax(
          {
            url: '/login/google-login',
            data: {email: email, token: token, _csrfToken: _csrfToken},
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
                    toastr.warning(resposta.reason);
                    googleLogout();
                  }
              }
          });
      }
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '704093496714310',
      status     : true, 
      cookie     : true,
      xfbml      : true,
      oauth      : true,
      version    : 'v3.3'
    });
  };
var facebookLogin = function(response)
  {
      if (response.authResponse)
        {
          FB.api('/me', {fields:"email"}, function(response) 
            {
                var email = response.email; 
                var _csrfToken = $("[name='_csrfToken']").val();  
                $.ajax(
                  {
                    url: '/login/facebook-login',
                    data: {email: email, _csrfToken: _csrfToken},
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
                            toastr.warning(resposta.reason);
                          }
                      }
                  });
            });
        }
      else
        {
          console.log('Autenticação cancelada');
        }
  }
$(document).ajaxStart(function()
  {
    $("#full-preloader").css('display', 'block');
  });
$(document).ajaxStop(function()
  {
    $("#full-preloader").css('display', 'none');
  });