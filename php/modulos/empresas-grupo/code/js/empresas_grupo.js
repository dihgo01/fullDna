$(document).ready(function () {
    /*
     * CÓDIGO NOVO
     */
    if ($(".select2-usuarios-modal").length > 0) {
        $(".select2-usuarios-modal").select2({
            placeholder: "Selecione",
            dropdownParent: $("#modalAddUsuario"),
            language: {
                noResults: function () {
                    return "Nada encontrado.";
                }
            }
        });
    }

    /*
     * CÓDIGO NOVO
     */
    $('body').on('click', '#btn_abrir_modal_vinculacao_usuario', function () {
        $('#modalAddUsuario select').val('').trigger('change');
        $('#modalAddUsuario').modal('toggle');
    });

    /*
     * CÓDIGO NOVO
     */
    $('body').on('click', '#btn_vincular_usuario_empresa', function (e) {
        let usuario = $('#modalAddUsuario select[name="usuario"]').val();
        if (usuario !== '') {
            $('input[name="usuarios_vinculados[]"]').each(function (e) {
                if ($(this).val() === usuario) {
                    Swal.fire({
                        title: "Ooops!",
                        text: 'Este usuário já está vinculado na empresa.',
                        icon: "error",
                        cancelButtonText: "OK"
                    });
                    e.preventDefault();
                }
            });
            $.ajax({
                url: '/php/modulos/empresas-grupo/code/ajax-empresas-grupo.php',
                type: 'POST',
                data: {
                    action_type: 'busca_dados_usuario',
                    usuario: usuario
                },
                success: function (result) {
                    $("#lista_usuarios_vinculados").append(result);
                    $(".texto_sem_contatos").hide();
                    $("#modalAddUsuario").modal("toggle");
                }
            });
        } else {
            Swal.fire({
                title: "Ooops!",
                text: 'Campos obrigatórios não preenchidos!',
                icon: "error",
                cancelButtonText: "OK"
            });
        }
    });

    /*
     * CÓDIGO NOVO
     */
    $('body').on('click', '.btnExcluirContatoEmpresa', function () {
        var btn = $(this);
        var elemento = $(this).parent().parent().parent();
        Swal.fire({
            title: 'Confirmação',
            text: "Você tem certeza que deseja excluir este contato?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d10000',
            cancelButtonColor: '#adadad',
            confirmButtonText: 'Sim, quero excluir!',
            cancelButtonText: 'Voltar'
        }).then((result) => {
            if (result.isConfirmed) {
                if (typeof btn.attr('data-id-usuario') !== 'undefined') {
                    $.ajax({
                        url: '/php/modulos/empresas-grupo/code/ajax-empresas-grupo.php',
                        type: 'POST',
                        dataType: 'JSON',
                        data: {
                            action_type: 'excluir_usuario_edicao',
                            usuario: btn.attr('data-id-usuario')
                        },
                        success: function (data) {
                            if (data.status === 'OK') {
                                elemento.remove();
                                if (parseInt($("#lista_usuarios_vinculados .info-block").length) <= 0) {
                                    $(".texto_sem_contatos").show();
                                }
                            }
                        }
                    });
                } else {
                    elemento.remove();
                    if (parseInt($("#lista_usuarios_vinculados .info-block").length) <= 0) {
                        $(".texto_sem_contatos").show();
                    }
                }
            }
        })
    });

    /*
     * CÓDIGO NOVO
     */
    $('body').on('click', '#btn_abrir_modal_vinculacao_impostos', function () {
        $('#modalAddImpostos select, #modalAddImpostos input').val('').trigger('change');
        $('#modalAddImpostos').modal('toggle');
    });

    /*
     * CÓDIGO NOVO
     */
    $('body').on('change', '#modalAddImpostos select[name="grupo_imposto_modal"]', function (e) {
        let imposto = $(this).find('option:selected').val();
        $.ajax({
            url: '/php/modulos/empresas-grupo/code/ajax-empresas-grupo.php',
            type: 'POST',
            dataType: 'JSON',
            data: {
                action_type: 'buscar_impostos_do_grupo',
                imposto: imposto

            }, success(data) {
                $('#modalAddImpostos select[name="imposto_modal"]').html('<option value="" data-aliquota="" data-reajuste="">Selecione</option>');
                if (data.length > 0) {
                    for (var j = 0; j < data.length; j++) {
                        $('#modalAddImpostos select[name="imposto_modal"]').append('<option value="' + data[j]['HASH'] + '" data-aliquota="' + data[j]['ALIQUOTA'] + '" data-reajuste="' + data[j]['REAJUSTE'] + '">' + data[j]['IMPOSTO'] + '</option>');
                    }
                }
                $('#modalAddImpostos select[name="imposto_modal"]').trigger('change');
            }
        });
    });

    /*
     * CÓDIGO NOVO
     */
    $('body').on('change', '#modalAddImpostos select[name="imposto_modal"]', function () {
        let opcao_selecionada = $(this).find('option:selected');
        $('#modalAddImpostos input[name="aliquota_modal"]').val(opcao_selecionada.attr('data-aliquota'));
        $('#modalAddImpostos input[name="reajuste_modal"]').val(opcao_selecionada.attr('data-reajuste'));
    });

    /*
     * CÓDIGO NOVO
     */
    $("body").on("click", "#btn_vincular_imposto_empresa", function (e) {
        let validacao = true;
        $("#modalAddImpostos .required").each(function () {
            if ($(this).val().trim() === "") {
                validacao = false;
            }
        });

        if (validacao) {
            $('input[name="impostos_vinculados[]"]').each(function (e) {
                if ($(this).val() === $('#modalAddImpostos select[name="imposto_modal"]').val()) {
                    Swal.fire({
                        title: "Ooops!",
                        text: 'Este imposto já está vinculado na empresa.',
                        icon: "error",
                        cancelButtonText: "OK"
                    });
                    e.preventDefault();
                }
            });
            $("#lista_impostos_vinculados").append('<div class="info-block"> <div class="row"> <div class="col-md-11 col-10"> <h6><i class="icon-info-alt"></i> ' + $('#modalAddImpostos select[name="imposto_modal"] option:selected').text() + '</h6> </div> <div class="col-md-1 col-2 text-right"> <button type="button" class="icone-excluir-lista btnExcluirImpostoEmpresa"><i class="icon-close"></i></button> </div> </div> <input type="hidden" name="impostos_vinculados[]" value="' + $('#modalAddImpostos select[name="imposto_modal"]').val() + '"> <input type="hidden" name="aliquotas_vinculados[]" value="' + $('#modalAddImpostos input[name="aliquota_modal"]').val() + '"><input type="hidden" name="reajustes_vinculados[]" value="' + $('#modalAddImpostos input[name="reajuste_modal"]').val() + '"> <div class="star-ratings"> <ul class="search-info"> <li>GRUPO: ' + $('#modalAddImpostos select[name="grupo_imposto_modal"] option:selected').text() + '</li> <li>ALÍQUOTA: ' + $('#modalAddImpostos input[name="aliquota_modal"]').val() + '</li> <li>REAJUSTE: ' + $('#modalAddImpostos input[name="reajuste_modal"]').val() + '</li></ul> </div> </div>');
            $(".texto_imposto_sem_resultado").hide();
            $("#modalAddImpostos").modal("toggle");
        } else {
            Swal.fire({
                title: "Ooops!",
                text: 'Campos obrigatórios não preenchidos!',
                icon: "error",
                cancelButtonText: "OK"
            });
        }

    });

    /*
     * CÓDIGO NOVO
     */
    $('body').on('click', '.btnExcluirImpostoEmpresa', function () {
        var btn = $(this);
        var elemento = $(this).parent().parent().parent();
        Swal.fire({
            title: 'Confirmação',
            text: "Você tem certeza que deseja excluir este imposto?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d10000',
            cancelButtonColor: '#adadad',
            confirmButtonText: 'Sim, quero excluir!',
            cancelButtonText: 'Voltar'
        }).then((result) => {
            if (result.isConfirmed) {
                if (typeof btn.attr('data-id-imposto') !== 'undefined') {
                    $.ajax({
                        url: '/php/modulos/empresas-grupo/code/ajax-empresas-grupo.php',
                        type: 'POST',
                        dataType: 'JSON',
                        data: {
                            action_type: 'excluir_imposto_edicao',
                            imposto: btn.attr('data-id-imposto')
                        },
                        success: function (data) {
                            if (data.status === 'OK') {
                                elemento.remove();
                                if (parseInt($("#lista_impostos_vinculados .info-block").length) <= 0) {
                                    $(".texto_imposto_sem_resultado").show();
                                }
                            }
                        }
                    });
                } else {
                    elemento.remove();
                    if (parseInt($("#lista_impostos_vinculados .info-block").length) <= 0) {
                        $(".texto_imposto_sem_resultado").show();
                    }
                }
            }
        })
    });

    /*
     * CÓDIGO NOVO
     */
    $('body').on('click', '.logo_view', function () {
        $('#modalLogo').modal('toggle');
    });

    $('body').on('click', '.icone_view', function () {
        $('#modalIcone').modal('toggle');
    });


});