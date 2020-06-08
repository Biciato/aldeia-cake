class FormularioTurma
{
    constructor(form_id, success_callback)
        {
        this.key                = form_id;
        this.form               = $("#turma-form-" + form_id);
        this.colaboradores_keys = (this.form.data('update')) ? this.form.data('colaboradores') : 0;
        this.insertSuccessCallback = success_callback;
        var classe = this;
        this.turma_form = 
            {
            init: function()
                {
                var self = this;
                $(document).on('click', '#adicionar-colaborador-' + classe.key, self.addColaborador);
                $(document).on('click', '.remover-colaborador-' + classe.key, self.removerColaborador);
                $(document).on('click', '#inserir-turma-' + classe.key, self.inserirTurma);
                if(classe.form.data('update'))
                  {
                    var selects_colaboradores = classe.form.find('select[name="colaboradores[]"]');
                    $.each(selects_colaboradores, function(i, item)
                      {
                        var $item = $(item);
                        $item.select2(
                          {
                            ajax: 
                                {
                                url: '/turmas/opcoes-colaboradores',
                                dataType: 'JSON',
                                delay: 320,
                                data: function (params) 
                                    {
                                    return {
                                        q: params.term
                                        };
                                    },
                                cache: true
                                },
                            placeholder: 'Colaborador',
                            minimumInputLength: 2,
                            templateResult: classe.turma_form.formatarColaborador,
                            templateSelection: classe.turma_form.formatarSelecaoColaborador
                          });
                          setTimeout(function()
                            {
                                $.ajax({
                                    type: 'GET',
                                    url: '/turmas/selecionar-colaborador/' + $item.data('colaborador')
                                }).then(function (colaborador) {
                                    var option = new Option(colaborador.pessoa.nome, colaborador.id, true, true);
                                    $item.append(option).trigger('change');
                                    $item.trigger({
                                        type: 'select2:select',
                                        params: {
                                            data: colaborador
                                        }
                                    });
                                });
                            }, 300);
                          
                      });
                  }
                self.initInputs();
                return this;
                },
            initInputs: function()
                {
                var horarios = classe.form.find('[name^="horario_"]');
                $.each(horarios, function(i, item)
                    {
                    var $item = $(item);
                    VMasker($(item)).maskPattern('99:99');
                    });
                },
            addColaborador: function()
                {
                classe.colaboradores_keys++;
                var colaboradores_keys = classe.colaboradores_keys;
                var markup = '<div data-parent-id="colaboradores-' + classe.key + '" class="form-group row colaborador-fields-' + colaboradores_keys + '-' + classe.key + '" >' +
                    '<div class="col-sm-11">' +
                        '<label for="colaborador">Colaborador</label>' +
                        '<select class="form-control" data-key="' + colaboradores_keys + '" data-selector=".colaborador-fields-' + colaboradores_keys + '-' + classe.key + '" id="colaborador-' + colaboradores_keys + '-' + classe.key + '" name="colaboradores[]">' +
                            '<option value="">Selecione...</option>' +
                        '</select>' + 
                        '<div class="form-text"></div>' +
                    '</div>' +
                    '<div class="col-sm-1">' +
                        '<label style="visibility:hidden">æææ</label>' +
                        '<a class="btn btn-danger remover-colaborador-' + classe.key + '" style="color:white" href="javascript:void(0)" data-key="' + colaboradores_keys + '">Remover</a>' +
                    '</div>' +
                '</div>';
                $(markup).insertAfter('#botao-colaborador-' + classe.key);
                classe.form.find('#colaborador-' + colaboradores_keys + '-' + classe.key).select2({
                    ajax: 
                        {
                        url: '/turmas/opcoes-colaboradores',
                        dataType: 'JSON',
                        delay: 320,
                        data: function (params) 
                            {
                            return {
                                q: params.term
                                };
                            },
                        cache: true
                        },
                    placeholder: 'Colaborador',
                    minimumInputLength: 2,
                    templateResult: classe.turma_form.formatarColaborador,
                    templateSelection: classe.turma_form.formatarSelecaoColaborador
                    });
                },
            formatarColaborador: function(colaborador)
                {
                if(colaborador.loading)
                    {
                    return colaborador.text;
                    }
                var $caixa = $(
                    "<div class='select2-result-colaborador clearfix'>" +
                        "<div>" + colaborador.pessoa.nome  + "</div>" +
                    "</div>"
                );
                return $caixa;
                },
            formatarSelecaoColaborador: function(colaborador)
                {
                if(typeof colaborador.pessoa !== 'undefined')
                  {
                    return colaborador.pessoa.nome;
                  }
                else if(typeof colaborador.text !== 'undefined')
                  {
                    return colaborador.text;
                  }
                return "Selecione...";
                },
            removerColaborador: function()
                {
                var key = $(this).data('key');
                var fields = $(".colaborador-fields-" + key + "-" + classe.key);
                fields.remove();
                },
            inserirTurma: function(e)
                {
                e.preventDefault();
                var data = classe.form.serialize();
                var inputs = classe.form.find('input, select');
                $.ajax(
                    {
                    url: '/turmas/inserir',
                    data: data,
                    method: 'POST',
                    dataType: 'JSON',
                    beforeSend: function()
                        {
                        inputs.removeClass('is-invalid');
                        inputs.siblings('.form-text').html("");
                        },
                    success: function(resposta)
                        {
                        if(resposta.success === true)
                            {
                            inputs.addClass("is-valid");
                            toastr.success('Turma inserida com sucesso!');
                            classe.insertSuccessCallback()
                            }
                        else
                            {
                            $.each(resposta.errors, function(i, item)
                                {
                                if(((i != 'colaboradores')&&(i != 'dias_semana')))
                                    {
                                    classe.showMessages("[name='" + i + "']", item);
                                    }
                                else
                                    {
                                    if((i == 'colaboradores')&&(typeof item._empty === 'undefined'))
                                        {
                                        var fields = classe.form.find('[name="colaboradores[]"]');
                                        $.each(fields, function(f, field)
                                            {
                                            var $field = $(field);
                                            if($field.val() == "")
                                                {
                                                classe.showMessages("#" + $field.attr('id'), item);
                                                }
                                            });
                                        }
                                    else
                                        {
                                        toastr.error(item._empty);
                                        }
                                    }
                                });
                            toastr.error("Corrija os campos inválidos");
                            if(classe.form.find('.is-invalid').length > 0)
                            {
                                var first = classe.form.find('.is-invalid')[0];
                                $('html, body').animate({
                                    scrollTop: ($(first).offset().top - 90)
                                }, 600);
                            }
                        }
                        }
                    }
                );
                }
            };
        }   
    showMessages(selector, item)
        {
        var ipt = this.form.find(selector);
        var msg = "";
        $.each(item, function(rule, _msg)
        {
            msg += _msg + "</br>";
        });
        msg.slice(0, -5);
        ipt.addClass('is-invalid');
        ipt.siblings('.form-text').html(msg);
        ipt.siblings('.form-text').addClass('text-danger');
        }
    
}