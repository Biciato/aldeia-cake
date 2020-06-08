var prospect = 
  {
    init: function()
      {
        var self = this;
        $(document).on('click', "#insert-prospect", self.addProspect);
        $(document).on('blur', "#prospect-form input, #prospect-form select", self.frontEndValidation);
        self.initMasks();
        return this;
      },
    frontEndValidation: function(e)
      {
        var ipt = $(this);
        setTimeout(function()
          {
            var val = ipt.val();
            var name = ipt.attr('name');
            var error = false;
            var errorMap = function(val, name)
              {
                if(name == 'parentes[0][pessoa-parente][nome]')
                  {
                    if(val == "")
                      {
                        toastr.error("Insira o seu nome");
                        error = true;
                      } 
                  }
                else if(name == 'parentes[0][parentesco]')
                  {
                    if(val == "")
                      {
                        toastr.error("Selecione o seu parentesco com o aluno");
                        error = true;
                      }
                  }
                else if(name == 'parentes[0][pessoa-parente][telefones][]')
                  {
                    if(val == "")
                      {
                        toastr.error("Insira um telefone");
                        error = true;
                      }
                  }
                else if(name == 'parentes[0][pessoa-parente][telefones][]')
                  {
                    if(val == "")
                      {
                        toastr.error("Insira um telefone");
                        error = true;
                      }
                  }
                else if(name == 'parentes[0][pessoa-parente][email]')
                  {
                    if(val == '')
                      {
                        toastr.error("Insira o email");
                        error = true;
                      }
                  }
                else if(name == 'pessoa-prospect[nome]')
                  {
                    if(val == '')
                      {
                        toastr.error("Insira o nome do aluno");
                        error = true;
                      }
                  }
                else if(name == 'pessoa-prospect[data_nascimento]')
                  {
                    if(val != '')
                      {
                        var pieces = val.split('/');
                        if(pieces.length !== 3)
                          {
                            toastr.error('Insira uma data válida');
                            error = true;
                          }
                        else if(isNaN(Date.parse(pieces[1] + "/" + pieces[0] + "/" + pieces[2])))
                          {
                            toastr.error("Insira uma data válida");
                            error = true;
                          }
                        if(error)
                          {
                            ipt.val("");
                          }
                      }
                  }
                else if(name == 'interacao[data]')
                  {
                    if(val == '')
                      {
                        toastr.error("Insira uma data para a visita");
                        error = true;
                      }
                    else
                      {
                        var pieces = val.split('/');
                        if(pieces.length !== 3)
                          {
                            toastr.error('Insira uma data válida');
                            error = true;
                          }
                        else if(isNaN(Date.parse(pieces[1] + "/" + pieces[0] + "/" + pieces[2])))
                          {
                            toastr.error("Insira uma data válida");
                            error = true;
                          }
                        else
                          {
                            var date = new Date(pieces[1] + "/" + pieces[0] + "/" + pieces[2]);
                            var now = new Date();
                            var tomorrow = new Date();
                            tomorrow.setDate(now.getDate() + 1);
                            var diffDays = Math.ceil((date - now) / (1000 * 60 * 60 * 24), 10); 
                            console.log(diffDays);
                            if(diffDays < 1)
                              {
                                toastr.error('Insira uma data válida à partir de ' + tomorrow.getDate() + "/" + (tomorrow.getMonth() + 1) + "/" + tomorrow.getFullYear());
                                error = true;
                              }
                            else if([0,6].includes(date.getDay()))
                              {
                                toastr.error('Insira uma data de segunda à sexta');
                                error = true;
                              }

                          }
                        if(error)
                          {
                            ipt.val("");
                          }
                      }
                  }
                else if(name == 'interacao[hora]')
                  {
                    if(val == '')
                      {
                        error = true;
                        toastr.error('Insira a hora da visita');
                      }
                    else
                      {
                        pieces = val.split(":");
                        if(pieces.length !== 2)
                          {
                            error = true;
                            toastr.error("Insira uma hora válida");
                          }
                        else
                          {
                            if(
                              (parseInt(pieces[0]) < 8)
                              ||
                              (parseInt(pieces[0]) > 18)
                              ||
                              (
                                (parseInt(pieces[0]) == 18)
                                &&
                                (parseInt(pieces[1]) > 0)
                              )
                              ||
                              (
                                parseInt(pieces[1]) > 59
                              )
                              ||
                              (
                                parseInt(pieces[0]) > 23
                              )
                            )
                              {
                                toastr.error('Insira uma hora válida entre 08:00 e 18:00');
                                error = true;
                              }
                          }
                      }
                    if(error)
                      {
                        ipt.val("");
                      }
                  }
                else if(name == 'unidade')
                  {
                    if(val == "")
                      {
                        toastr.error("Selecione a unidade para a visita");
                        error = true;
                      }
                  }
              }
            errorMap(val, name);
            if(error)
              {
                ipt.addClass('is-invalid');
              }
            else
              {
                ipt.removeClass('is-invalid');
              }
          }, 200)
      },
    addProspect: function(e)
      {
        e.preventDefault();
        var gRecaptchaResponse = grecaptcha.getResponse();
        var form   = $("#prospect-form");
        var data   = form.serialize();
        var inputs = form.find(".form-control:not([type='hidden'])");
        $.ajax(
          {
            url: '/prospects/inserir-prospect-externo',
            data: data,
            dataType: 'JSON',
            method: 'POST',
            beforeSend: function()
              {
                inputs.removeClass('is-invalid');
              },
            success: function(resposta)
              {
                if(resposta.success === true)
                  {
                    inputs.addClass("is-valid");
                    toastr.success('Visita agendada com sucesso!');
                    setTimeout(function()
                      {
                        inputs.removeClass('is-valid').val("");
                        $('html, body').animate(
                          {scrollTop: 0}, 200);
                        window.location.href = 'https://www.aldeiamontessori.com.br';
                      }, 500);
                  }
                else if(typeof resposta.errors === 'object')
                  {
                   grecaptcha.reset();
                    var errorMap =
                      {
                        'pessoa-prospect' :
                          {
                            isMany : false,
                            hasChild: false
                          },
                        'parentes':
                          {
                            isMany : true,
                            hasChild: true,
                            child:
                              {
                                key: 'pessoa-parente'
                              }
                          },
                        'interacao': 
                          {
                            isMany: false,
                            hasChild: false
                          }
                      };
                    var mappedFields = Object.keys(errorMap);
                    $.each(resposta.errors, function(i, item)
                      {
                        if(mappedFields.indexOf(i) === -1)
                          {
                            $("[name='" + i + "']").addClass('is-invalid');
                            toastr.error(Object.values(item).join(","));
                          }
                        else
                          {
                            var rules = errorMap[i];
                                    
                            if(!rules.isMany)
                              {
                                var _break = false;
                                $.each(item, function(ii, iitem)
                                  {
                                    if(ii == "_required")
                                      {
                                        toastr.error(iitem);
                                        return false;
                                      }
                                    else
                                      {
                                        $("[name='" + i + "[" + ii + "]']").addClass('is-invalid');
                                        toastr.error(Object.values(iitem).join(","));
                                      }
                                  });
                              }
                            else
                              {
                                $.each(item, function(ii, iitem)
                                  {
                                    if(ii == "_required")
                                      {
                                        toastr.error(iitem);
                                        return false;
                                      }
                                    else
                                      {
                                        if(rules.hasChild)
                                          {
                                            $.each(iitem, function(iii, iiitem)
                                              {
                                                $("[name='" + i + "[" + ii + "][" + iii + "]']").addClass('is-invalid');
                                                if(typeof iiitem[Object.keys(iiitem)[0]] === 'string')
                                                  {
                                                    toastr.error(Object.values(iiitem).join(","));
                                                  }
                                              });
                                            $.each(iitem[rules.child.key], function(iiii, iiiitem)
                                              {
                                                $("[name='" + i + "[" + ii + "][" + rules.child.key + "][" + iiii + "]'], [name='" + i + "[" + ii + "][" + rules.child.key + "][" + iiii + "][]']").addClass('is-invalid');
                                                if(typeof iiiitem[Object.keys(iiiitem)[0]] === "string")
                                                  {
                                                    toastr.error(Object.values(iiiitem).join(","));
                                                  } 
                                              });
                                          }
                                      }
                                  });
                              }
                          }
                      });
                  }
                else if(resposta.errors === 'grecaptcha')
                  {
                    toastr.error("Complete o captcha");
                  }
              }
          });
      },
    initMasks: function()
      {
        function inputHandler(masks, max, event)
          {
            var c = event.target;
            var v = c.value.replace(/\D/g, '');
            var m = c.value.length > max ? 1 : 0;
            VMasker(c).unMask();
            VMasker(c).maskPattern(masks[m]);
            c.value = VMasker.toPattern(v, masks[m]);
          }

          var date = new Date();
          date.setDate(date.getDate()+2);


        var telMask = ['(99) 9999-99999', '(99) 99999-9999'];
        var tel = document.querySelector('[name="parentes[0][pessoa-parente][telefones][]"]');
        VMasker(tel).maskPattern(telMask[0]);
        VMasker(document.querySelector('[name="pessoa-prospect[data_nascimento]"]')).maskPattern('99/99/9999');
        tel.addEventListener('input', inputHandler.bind(undefined, telMask, 14), false);
        VMasker($("[name='interacao[data]']")).maskPattern("99/99/9999");
        $("[name='interacao[data]']").datepicker(
          {
            format: 'dd/mm/yyyy',
            autoclose: true,
            orientation:'auto bottom',
            locale: 'pt-BR',
            language: 'pt-BR',
            clearBtn: true,
            startDate: date,
            daysOfWeekDisabled: [0,6],
            daysOfWeekHighlighted: [1,2,3,4,5],
            datesDisabled: ['01/01/2020', '20/01/2020', '27/02/2020', '28/02/2020', '09/04/2020', '14/04/2020', '01/05/2020', '15/06/2020', '07/09/2020', '12/10/2020',
             '02/11/2020', '15/11/2020', '20/11/2020', '25/12/2020']
          });

          $("[name='pessoa-prospect[data_nascimento]']").datepicker(
          {
            format: 'dd/mm/yyyy',
            autoclose: true,
            orientation: 'auto bottom',
            locale: 'pt-br_full',
            language: 'pt-br_full',
            clearBtn: true,
          });
          

        VMasker($("[name='interacao[hora]']")).maskPattern("99:99");
      },
  };
$(document).ready(function()
  {
    prospect.init();
  });
  $(document).ajaxStart(function()
    {
      $("#full-preloader").css('display', 'block');
    });
  $(document).ajaxStop(function()
    {
      $("#full-preloader").css('display', 'none');
    });

