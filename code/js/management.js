$(document).ready(function () {
    $('body').on('click', '#iconNavbarSidenav' , function(){
        $('#sidenav-main').toggleClass('remove-transform')
    })

    if ($('.field-get-date').length > 0) {
        $('.field-get-date').datepicker({
            language: 'en'
        });
    }

    if ($('.field-cpf').length > 0) {
        $('.field-cpf').mask('999.999.999-99');
    }

    if ($('.field-cnpj').length > 0) {
        $('.field-cnpj').mask('99.999.999/9999-99');
    }

    if ($('.field-cep').length > 0) {
        $('.field-cep').mask('99999-999');
    }

    if ($('.field-percent').length > 0) {
        $(".field-percent").maskMoney({
            prefix: "",
            suffix: "%",
            thousands: ".",
            decimal: ",",
            precision: 2
        });
    }

    if ($(".field-phone").length > 0) {
        $(".field-phone").mask("(99) 99999-9999");
    }

    $(".field-phone").focusout(function () {
        var str_phone = $(this).val();
        if (parseInt(str_phone.replace("_", "").length) === 15) {
            $(this).unmask();
            $(this).mask("(99) 99999-9999");
        } else {
            $(this).unmask();
            $(this).mask("(99) 99999-9999");
        }
    });

    /*
     * DEIXAR O CÓDIGO ABAIXO COMENTADO, SERÁ ÚTIL NOS PRÓXIMOS MÓDULOS
     */

    /*if ($(".selector").length > 0) {
     $(".selector").select2({
     placeholder: "Selecione",
     maximumSelectionLength: 6,
     language: {
     noResults: function () {
     return "Nada encontrado.";
     },
     maximumSelected: function (e) {
     var t = "Você pode selecionar no máximo " + e.maximum + " nomes";
     return t;
     }
     }
     });
     }*/

    if ($(".field-select2-general").length > 0) {
        $(".field-select2-general").select2({
            placeholder: "Selecione",
            language: {
                noResults: function () {
                    return "Nada encontrado.";
                }
            },
        });
    }

    $('body').on('click', '.dropdown-toggle', function () {
        $(this).next().toggle();
    });

    if ($('.start-datatable').length > 0) {
        $('.start-datatable').each(function () {
            var element = $(this);
            element.DataTable({
                "bSort": false,
                "displayLength": 20,
                "lengthMenu": [[20, 50, 100], [20, 50, 100]],
               
                responsive: true,
                "initComplete": function (settings, json) {
                    setTimeout(function () {
                        $('.loader-datatable').hide();
                        element.show();
                        $('.dataTables_wrapper').show();
                    }, 1500);
                }
            });
        });
    }

    $('body').on('change', '[data-cb-show-hide-div]', function () {
        var target = $(this).attr('data-cb-show-hide-div');
        var value = $(this).find('option:selected').val();
        $('[' + target + ']').hide();
        $('[' + target + ']').find('input, select').val('');
        $('[' + target + '="' + value + '"]').show();
        $('[' + target + '="' + value + '"]').find('input, select').each(function () {
            if ($(this).hasClass('isRequired')) {
                $(this).prop('required', true);
            }
        });
    });

    /*$('body').on('click', '[btn-exclui-tarefa]', function () {
     var text = $(this).attr('btn-exclui-tarefa');
     var div = $(this).parent();
     if (text !== '') {
     Swal.fire({
     title: 'Confirma a exclusão?',
     text: 'Depois de excluida, esta tarefa não poderá ser recuperada posteriormente. Você confirma a exclusão?',
     icon: 'warning',
     showCancelButton: true,
     confirmButtonColor: '#d10000',
     cancelButtonColor: '#3085d6',
     confirmButtonText: 'Sim, quero excluir!',
     cancelButtonText: 'Voltar'
     }).then((result) => {
     if (result.value) {
     Swal.showLoading();
     $('.swal2-cancel ').remove();
     $.ajax({
     url: '/code/ajax.php',
     type: 'POST',
     dataType: 'JSON',
     data: {
     action_type: 'exclui_item_tarefa',
     key: text
     },
     success: function (data) {
     if (data.status === 'OK') {
     div.remove();
     } else {
     Swal.fire({
     title: "Ooops!",
     text: data.message,
     type: "error",
     cancelButtonText: "Entendi",
     });
     }
     }
     });
     }
     });
     } else {
     $(this).parent().remove();
     }
     });*/

    $('body').on('click', '[data-delete-item]', function () {
        var key = $(this).attr('data-delete-item');
        var table = $(this).attr('data-delete-table');
        var parameter = $(this).attr('data-delete-parameter');
        var message = $(this).attr('data-delete-message');
        Swal.fire({
            title: 'Confirmation',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d10000',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, I want to delete',
            cancelButtonText: 'Return'
        }).then((result) => {
            if (result.value) {
                Swal.showLoading();
                $('.swal2-cancel ').remove();
                $.ajax({
                    url: '/code/ajax.php',
                    type: 'POST',
                    crossDomain: true,
                    dataType: 'JSON',
                    data: {
                        action_type: 'excluir_item',
                        key: key,
                        table: table,
                        parameter: parameter
                    },
                    success: function (data) {
                        if (data.status === 'OK') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: data.message
                            }).then(function () {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: "Ooops!",
                                text: data.message,
                                icon: "error",
                                cancelButtonText: "Entendi",
                            });
                        }
                    }
                });
            }
        });
    });

   
    $('.add-responsavel-unidade').click(function () {
        $('#modalAddResponsavel input, #modalAddResponsavel select').val('');
        $('#modalAddResponsavel').modal('toggle');
    });

    $('body').on('click', '#btn_add_responsavel', function () {
        var validacao = true;
        var fields = $(this).parent().parent().find('.modal-body');
        var array_values = [];
        fields.find('input, select').each(function () {
            array_values.push($(this).val());
            if ($(this).attr('required').length > 0 && $(this).val() === '') {
                validacao = false;
            }
        });
        if (validacao) {
            $('#div-lista-responsaveis .div-itens').append('<div class="col-md-6 mb-3"><div class="info-block"><span class="pull-right removerResponsavelUnidade"><i class="icon-trash"></i></span><span class="pull-right editar-responsavel-unidade"><i class="icon-pencil"></i></span><h6>' + array_values[0] + '</h6> <p>' + array_values[1] + '</p> <div class="star-ratings"> <ul class="search-info"> <li>Aniversário: ' + array_values[2] + '/' + array_values[3] + '</li> <li>E-mail aniversário: ' + retorna_sim_nao_binario(array_values[4]) + '</li> <li>E-mail obrigações: ' + retorna_sim_nao_binario(array_values[5]) + '</li> </ul> </div> </div><input type="hidden" name="responsaveis[]" value="' + array_values + '"></div>');
            if ($('#div-sem-responsavel').is(':visible')) {
                $('#div-sem-responsavel').hide();
                $('#div-lista-responsaveis').slideDown();
            }
            $('#modalAddResponsavel').modal('toggle');
        } else {
            Swal.fire({
                title: "Ooops!",
                text: 'Todos os campos são obrigatórios',
                icon: "error",
                cancelButtonText: "Entendi"
            });
        }
    });

    $('body').on('click', '.removerResponsavelUnidade', function () {
        var div = $(this).parent().parent();
        Swal.fire({
            title: 'Confirma a remoção?',
            text: 'Depois de removido, este responsável não poderá ser recuperado posteriormente. Você confirma a exclusão?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#be0d0d',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sim, quero remover!',
            cancelButtonText: 'Voltar'
        }).then((result) => {
            if (result.value) {
                div.remove();
                if ($('.removerResponsavelUnidade').length < 1) {
                    $('#div-lista-responsaveis').hide();
                    $('#div-sem-responsavel').slideDown();
                }
            }
        });
    });

    $('body').on('click', '.editar-responsavel-unidade', function () {
        var div = $(this).parent().parent();
        var index = div.index();
        var valores = div.find('input').val().split(',');
        var aux = 0;
        $('#modalEditarResponsavel input, #modalEditarResponsavel select').val('');
        $('#modalEditarResponsavel').find('input, select').each(function () {
            $(this).val(valores[aux]);
            aux += 1;
        });
        $('#btn_editar_responsavel').attr('data-index', index);
        $('#modalEditarResponsavel').modal('toggle');
    });

    $('body').on('click', '#btn_editar_responsavel', function () {
        var index = $(this).attr('data-index');
        var validacao = true;
        var fields = $(this).parent().parent().find('.modal-body');
        var array_values = [];
        fields.find('input, select').each(function () {
            array_values.push($(this).val());
            if ($(this).attr('required').length > 0 && $(this).val() === '') {
                validacao = false;
            }
        });
        if (validacao) {
            $('#div-lista-responsaveis .div-itens .col-md-6').eq(index).html('<div class="info-block"><span class="pull-right removerResponsavelUnidade"><i class="icon-trash"></i></span><span class="pull-right editar-responsavel-unidade"><i class="icon-pencil"></i></span><h6>' + array_values[0] + '</h6> <p>' + array_values[1] + '</p> <div class="star-ratings"> <ul class="search-info"> <li>Aniversário: ' + array_values[2] + '/' + array_values[3] + '</li> <li>E-mail aniversário: ' + retorna_sim_nao_binario(array_values[4]) + '</li> <li>E-mail obrigações: ' + retorna_sim_nao_binario(array_values[5]) + '</li> </ul> </div> </div><input type="hidden" name="responsaveis[]" value="' + array_values + '">');
            $('#modalEditarResponsavel').modal('toggle');
        } else {
            Swal.fire({
                title: "Ooops!",
                text: 'Todos os campos são obrigatórios',
                icon: "error",
                cancelButtonText: "Entendi"
            });
        }
    });

    $('body').on('click', '.empresa-selecionada button', function () {
        $('#modalEscolherEmpresaGrupo').modal('toggle');
    });

   
});

function retorna_sim_nao_binario(valor) {
    if (parseInt(valor) === 1) {
        return 'Sim';
    } else {
        return 'Não';
    }
}


//Date and Time
if ($('.date-range-picker').length > 0) {
    $(function () {
        $('.date-range-picker').daterangepicker({
            timePicker: true,
            timePicker24Hour: true,
            timePickerIncrement: 1,
            locale: {
                format: 'DD/MM/YYYY HH:mm',
                cancelLabel: "Cancelar",
                applyLabel: "Aplicar",
                daysOfWeek: [
                    "Dom",
                    "Seg",
                    "Ter",
                    "Qua",
                    "Qui",
                    "Sex",
                    "Sab"
                ],
                monthNames: [
                    "Janeiro",
                    "Fevereiro",
                    "Março",
                    "Abril",
                    "Maio",
                    "Junho",
                    "Julho",
                    "Agosto",
                    "Setembro",
                    "Outubro",
                    "Novembro",
                    "Dezembro"
                ],
            }
        });
    });
};
