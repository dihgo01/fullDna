//LISTA PASTAS
$("body").on("click", ".actionFolder", function (e) {
    let path_folder = $(this).attr('data-path');
    let path_anterior = $(this).attr('data-path-back');
    e.preventDefault();
    $.ajax({
        url: "/php/modulos/usuarios/administrador/code/ajax-usuarios.php",
        type: "POST",
        crossDomain: true,
        dataType: "JSON",
        data: {
            action_type: 'entrada_em_pasta',
            path_folder: path_folder,
            path_anterior: path_anterior,
        }, success(data) {
            $('.list_folder').html(data.html_list);

        }
    })
})


$("body").on("click", ".btn-voltar", function (e) {
    let path_folder = $(this).attr('data-retroceder');
    //let path_anterior = $(this).attr('data-path-back');
    e.preventDefault();
    $.ajax({
        url: "/php/modulos/usuarios/administrador/code/ajax-usuarios.php",
        type: "POST",
        crossDomain: true,
        dataType: "JSON",
        data: {
            action_type: 'voltando_em_pasta',
            path_folder: path_folder,

        }, success(data) {
            console.log(data.html_list);
            $('.list_folder').html(data.html_list);

        }
    })
})

// ABRE MODAL
$("body").on("click", ".link-rename", function (e) {
    let path = $(this).attr('data-path');
    $('#path_folder').val(path)
    $('#modalRename input[name="nome_pasta"]').val('');
    $('#modalRename').modal('toggle');

})

// TROCA NOME
$("body").on("click", "#btn_rename", function (e) {

    let path = $('#path_folder').val()
    let pasta_nome = $('#modalRename input[name="nome_pasta"]').val();

    $.ajax({
        url: "/php/modulos/usuarios/administrador/code/ajax-usuarios.php",
        type: "POST",
        crossDomain: true,
        dataType: "JSON",
        data: {
            action_type: 'rename_files',
            path: path,
            pasta_nome: pasta_nome,

        }, success(data) {
            location.reload()

            $('#modalRename').modal('toggle');
        }
    })


})

// DELETE ARQUIVOS 
$("body").on("click", ".link-delete-files", function (e) {
    let path = $(this).attr('data-path');
    e.preventDefault();
    $.ajax({
        url: "/php/modulos/usuarios/administrador/code/ajax-usuarios.php",
        type: "POST",
        crossDomain: true,
        dataType: "JSON",
        data: {
            action_type: 'delete_files',
            path: path,

        }, success(data) {
            location.reload()

        }
    })

})

// DELETE PASTAS

$("body").on("click", ".link-delete-folder", function (e) {
    let path = $(this).attr('data-path');
    e.preventDefault();
    $.ajax({
        url: "/php/modulos/usuarios/administrador/code/ajax-usuarios.php",
        type: "POST",
        crossDomain: true,
        dataType: "JSON",
        data: {
            action_type: 'delete_folder',
            path: path,

        }, success(data) {
            location.reload()

        }
    })

})


$("body").on("click", ".add-folder", function (e) {
    let path = $('#folder').val();
    $('#path_folder_create').val(path)
    $('#modalAddFolder input[name="nome_nova_pasta"]').val('');
    $('#modalAddFolder').modal('toggle');
})


$("body").on("click", "#btn_create", function (e) {

    let path = $('#path_folder_create').val()
    let pasta_nome = $('#modalAddFolder input[name="nome_nova_pasta"]').val();

    $.ajax({
        url: "/php/modulos/usuarios/administrador/code/ajax-usuarios.php",
        type: "POST",
        crossDomain: true,
        dataType: "JSON",
        data: {
            action_type: 'create_folder',
            path: path,
            pasta_nome: pasta_nome,

        }, success(data) {
            location.reload()

            $('#modalAddFolder').modal('toggle');
        }
    })


})


$("body").on("click", ".add-file", function (e) {
    let path = $('#folder').val();
    $('#path_upload').val(path)
    $('#modalAddFile input[name="nome_nova_pasta"]').val('');
    $('#modalAddFile').modal('toggle');
})


$("body").on("click", "#btn_upload", function (e) {
    let hash_usuario = $('#hash_usuario').val();
    let path = $('#path_upload').val()
    let titulo = $('#modalAddFile input[name="titulo"]').val();
    let data = $('#modalAddFile input[name="data_exclusao"]').val();
    let file = $('.fileUploadInput').val();
    let descricao = $('.textDescricao').val();

    alert(file )
    $.ajax({
        url: "/php/modulos/usuarios/administrador/code/ajax-usuarios.php",
        type: "POST",
        crossDomain: true,
        dataType: "JSON",
        data: {
            action_type: 'upload_files',
            path: path,
            hash_usuario: hash_usuario,
            titulo: titulo,
            data: data,
            file: file,
            descricao: descricao,
        }, success(data) {
            if (data.status === 'OK') {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message
                })

                $('#modalAddFolder').modal('toggle');
            }
        }
    })


})

/*   e.preventDefault();
  
})



$("body").on("click", "#btn_clonar_campo", function () {
   $("#modalAddDependentesUsuario .modal-body").append(
           '<div class="row elementosDinamicosDependentes">' +
           '<div class="col-md-5 mb-3">' +
           '<div class="form-group">' + 
           '<label for="funcao">Nome do Dependente:</label>' +
           '<input class="form-control inputDependentes mb-3 inputElementos" type="text" ' +
           'placeholder="Nome do dependente" required="">' +
           '</div>' +
           '</div>' +
           '<div class="col-md-5 mb-3">' +
           '<div class="form-group">' +
           '<label for="funcao">Grau de Parentesco:</label>' +
           '<select class="form-control mb-3 inputGrauParentesco inputElementos" ' +
           'id="grau_parentesco" name="grau_parentesco" required="">' +
           '<option value="" selected>Selecione</option>' +
           '<option>Filho/Filha</option>' +
           '<option>Esposo/Esposa</option>' +
           '<option>Pai/Mãe</option>' +
           '<option>Outro</option>' +
           '</select>' +
           '</div>' +
           '</div>' +
           '<div class="col-md-2 mb-3">' +
           '<label>&nbsp;</label>' +
           '<button class="form-control btn btn-danger btnRemoveDependente" type="button">' +
           '<i class="icofont icofont-minus"></i>' +
           '</button>' +
           '</div>' +
           '</div>'
           );
})


$("body").on("click", ".btnRemoveDependente", function () {
   $(this).parent().parent().remove();
})


$("body").on("click", "#btn_inserir_dependentes", function () {
   var validacao = true;
   $(".inputElementos").each(function () {
       if ($(this).val().trim() === '') {
           validacao = false;
       }
   });
   if (validacao) {
       $(".inputDependentes").each(function () {
           $(".divComDependentes").append(
                   '<div class="info-block"> <h6 class="">Nome do Dependente: ' +
                   $(this).val().trim() + '<br> Grau de Parentesco: ' + $('.inputGrauParentesco').val().trim() +
                   '<input hidden name="nome_dependente[]" value="' +
                   $(this).val().trim() +
                   '">' +
                   '<input hidden name="grau_parentesco[]" value="' +
                   $('.inputGrauParentesco').val().trim() +
                   '">' +
                   ' <a href="#" class="f-right iconeLixeiraLista"><i class="icon-trash"></i></a></h6></div>'
                   );
           $(".divSemDependentes").hide();
           $(".divComDependentes").show();
           $("#modalAddDependentesUsuario").modal("toggle");
       })
   } else {
       Swal.fire({
           title: "Ooops!",
           text: "Não pode ter campos em branco.",
           icon: "error",
           cancelButtonText: "OK",
       });
   }
})


$("body").on("click", ".iconeLixeiraLista", function () {
   var elemento = $(this).parent().parent();
   Swal.fire({
       tittle: "Confirmação",
       text: "Tem certeza que deseja excluir este dependente?",
       icon: "warning",
       showCancelButton: true,
       confirmButtonColor: "#298c43",
       cancelButtonColor: "#3085d6",
       confirmButtonText: "Sim, quero excluir!",
       cancelButtonText: "Voltar",
   }).then((result) => {
       if (result.value) {
           elemento.remove();
           if ($(".divComDependentes .search-page").length <= 0) {
               $(".divComDependentes").hide();
               $(".divSemDependentes").show();
           }
       }
   })
})

$(document).ready(function () {
   var bloco_modulos_altura = 0;
   $('.search-page .blocoModuloUsuarios').each(function () {
       if ($(this).outerHeight() > bloco_modulos_altura) {
           bloco_modulos_altura = $(this).find('.info-block').outerHeight();
           $('.search-page .blocoModuloUsuarios .info-block').css('min-height', bloco_modulos_altura + 'px');
       }
   });

   $("body").on("click", ".btn_abrir_modal_usuarios_funcoes", function () {
       var modulo_hash = $(this).attr("data-id-modulo");
       $("#modalAddFuncoesModulo input").val("");
       $("#modalAddFuncoesModulo .elementosDinamicosFuncoes").remove();
       $("#modalAddFuncoesModulo input[name='modulo_hash']").val(modulo_hash);
       $("#modalAddFuncoesModulo").modal("toggle");
   });

   $("body").on("click", "#btn_clonar_campo", function () {
       $("#modalAddFuncoesModulo .modal-body").append(
               '<div class="row elementosDinamicosFuncoes"> <div class="col-md-9 mb-3"> <div class="form-group"> <label for="funcao">Função</label> <input class="form-control inputFuncoesModulo" type="text" placeholder="Nome da Função"> </div> </div> <div class="col-md-3 mb-3"> <label>&nbsp;</label> <button class="form-control btn btn-danger btnRemoverFuncaoModulo" type="button"><i class="icofont icofont-minus"></i></button> </div> </div>'
               );
   });

   $("body").on("click", ".btnRemoverFuncaoModulo", function () {
       $(this).parent().parent().remove();
   });

   $("body").on("click", "#btn_inserir_funcoes", function () {
       let funcoes = [];
       var validacao = true;
       $('#modalAddFuncoesModulo .inputFuncoesModulo').each(function () {
           if ($(this).val().trim() === '') {
               validacao = false;
           } else {
               funcoes.push($(this).val().trim());
           }
       });
       if (validacao) {
           $.ajax({
               url: '/php/modulos/usuarios/administrador/code/ajax-usuarios.php',
               type: 'POST',
               dataType: 'JSON',
               data: {
                   action_type: 'incluir_funcoes_modulos',
                   modulo_hash: $('#modalAddFuncoesModulo input[name="modulo_hash"]').val(),
                   funcoes: funcoes
               }, success(data) {
                   if (data.status === 'OK') {
                       Swal.fire({
                           icon: 'success',
                           title: 'Sucesso!',
                           text: data.message
                       }).then(function () {
                           $('#acessos-permissoes .card_' + $('#modalAddFuncoesModulo input[name="modulo_hash"]').val()).html(data.html);
                           $("#modalAddFuncoesModulo").modal("toggle");
                       });
                   } else {
                       Swal.fire({
                           title: "Ooops!",
                           text: data.message,
                           icon: "error",
                           cancelButtonText: "Entendi"
                       });
                   }
               }
           });
       } else {
           Swal.fire({
               title: "Ooops!",
               text: 'Todos os campos são de preenchimento obrigatório.',
               icon: "error",
               cancelButtonText: "OK"
           });
       }
   });
});

if ($(".filtro-inteligente-sistema-bancos").length > 0) {
   $(".filtro-inteligente-sistema-bancos").select2({
       placeholder: "Selecione",
       language: {
           noResults: function () {
               return "Nada encontrado.";
           },
           searching: function () {
               return "Buscando...";
           },
           errorLoading: function () {
               return "Digite o nome ou o código do banco para pesquisar.";
           }
       },
       ajax: {
           url: "/select2/sistema_bancos.php",
           dataType: "JSON",
           delay: 250,
           data: function (data) {
               return {
                   searchTerm: data.term
               };
           },
           processResults: function (response) {
               return {
                   results: response
               };
           },
           cache: true
       }
   });
}



/*$("body").on("change", ".get-hierarchy", function (){
   var hierarquia = $(this).find("option:selected").attr("value");
   console.log(hierarquia);
   $.ajax({
       url: "/php/modulos/usuarios/administrador/code/ajax-usuarios.php",
       type: "POST",
       dataType: "JSON",
       data: {
           action_type: 'filtrar_hierarquia_selecionada',
           hierarquia: hierarquia,
       }, success(data) {
           if(data.status === 'OK') {
               var permissoes = data.permissoes.split(", ");
               console.log(permissoes);
               //console.log(data.permissoes);
               $("input[name='permissoes[]']").prop("checked", false); 
               $("input[name='permissoes[]']").each(function(){
                   if(permissoes.includes($(this).attr("data-permissao"))) {
                       $(this).prop("checked", true);
                       console.log($(this).attr("data-permissao")) 
                   }
               })            
           }
       }
   })
})

$("body").on("change", ".get-hierarchy", function (){
   var hierarquia = $(this).find("option:selected").attr("value");
   console.log(hierarquia);
   $.ajax({
       url: "/php/modulos/usuarios/administrador/code/ajax-usuarios.php",
       type: "POST",
       dataType: "JSON",
       data: {
           action_type: 'filtrar_hierarquia_selecionada',
           hierarquia: hierarquia,
       }, success(data) {
           if(data.status === 'OK') {
               var permissoes = data.permissoes.split(", ");
               console.log(permissoes);
               //console.log(data.permissoes);
               $("input[name='permissoes[]']").prop("checked", false); 
               $("input[name='permissoes[]']").each(function(){
                   console.log($(this).attr("data-permissao"))
                   if(permissoes.includes($(this).attr("data-permissao"))) {
                       
                       $(this).prop("checked", true);
                       console.log($(this).attr("data-permissao")) 
                   }
               })            
           }
       }
   })
}) */