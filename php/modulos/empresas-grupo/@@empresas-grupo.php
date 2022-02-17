<?php
if (!isset($_SESSION)) {
    session_start();
}
include_once 'code/classes-web.class.php';
include_once 'code/functions.php';
$classesWeb = new ClassesWeb();

$page_start = 'listagem';
if (isset($_POST['p3']) && TRIM($_POST['p3']) === 'cadastro') {
    $page_start = 'cadastro';
} else if (isset($_POST['p3']) && TRIM($_POST['p3']) === 'edicao') {
    $page_start = 'edicao';
    $current_row = $classesWeb->get_query_unica('SELECT * FROM empresas_grupo WHERE hash="' . $_POST['p4'] . '" AND status <> "Inativo"');

    if (empty($current_row)) {
        header('Location: /empresas/grupo-empresas');
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <?php
    gerar_cabecalho('Empresas do Grupo');
    gerar_css(
        array(
            'toastr',
            'croppie',
            'management',
            'datatable',
            'select2'
        )
    );
    ?>
</head>

<body>
    <div class="tap-top"><i data-feather="chevrons-up"></i></div>
    <div class="page-wrapper compact-wrapper" id="pageWrapper">
        <?php include_once 'php/includes/page-header.php'; ?>
        <div class="page-body-wrapper">
            <?php include_once 'php/includes/sidebar-menu.php'; ?>
            <div class="page-body">
                <?php
                if ($page_start === 'listagem') {
                    $busca_empresas = $classesWeb->busca_empresas_grupo_e_telefone();
                ?>
                    <div class="container-fluid">
                        <div class="page-title">
                            <div class="row">
                                <div class="col-6">
                                    <h3>Empresas do Grupo</h3>
                                </div>
                                <div class="col-6">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="<?php echo WEBURL ?>dashboard"><i data-feather="dashboard"></i></a></li>
                                        <li class="breadcrumb-item">Empresas</li>
                                        <li class="breadcrumb-item">Listagem</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card-header card-no-border">
                                        <h5 class="d-flex">Empresas do Grupo <a href="<?php echo WEBURL; ?><?php echo $_POST["p1"]; ?>/<?php echo $_POST["p2"]; ?>/cadastro" class="btn_add_registro btn-primary"><i class="icofont icofont-plus"></i></a></h5>
                                        <span>Listagem de empresas do grupo cadastradas.</span>
                                    </div>
                                    <div class="card-body pt-0">
                                        <div class="text-center mt-5 loader-datatable">
                                            <div class="loader-box">
                                                <div class="loader-3"></div>
                                            </div>
                                        </div>
                                        <div class="start-datatable-element">
                                            <table class="display datatables start-datatable">
                                                <thead class="text-center">
                                                    <tr>
                                                        <th>Nome Fantasia</th>
                                                        <th>Razão Social</th>
                                                        <th>CNPJ</th>
                                                        <th>Status</th>
                                                        <th>#</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="text-center">
                                                    <?php
                                                    if (!empty($busca_empresas)) {
                                                    ?>
                                                        <?php
                                                        foreach ($busca_empresas as $EMPRESAS) {
                                                        ?>
                                                            <tr>
                                                                <td><?php echo $EMPRESAS->nome_fantasia ?></td>
                                                                <td><?php echo $EMPRESAS->razao_social ?></td>
                                                                <td><?php echo $EMPRESAS->cnpj ?></td>
                                                                <td><?php echo "<label class='badge badge-success'>$EMPRESAS->status</label>"; ?></td>
                                                                <td>
                                                                    <div class="dropdown dropdown-item-table">
                                                                        <button type="button" class="btn btn-primary dropbtn dropdown-toggle dropdown-datatable p-2" data-toggle="dropdown"><i class="icofont icofont-arrow-down"></i></button>
                                                                        <div class="dropdown-menu dropdown-list">
                                                                            <a class="dropdown-item text-center" href="<?php echo WEBURL . 'empresas/empresas-grupo/edicao/' . $EMPRESAS->hash ?>">Ver/Editar</a>
                                                                            <a class="dropdown-item text-center" href="#" data-delete-item="<?php echo $EMPRESAS->hash ?>" data-delete-table="empresas_grupo" data-delete-parameter="hash" data-delete-message="Excluindo esta empresa do grupo, os dados não poderão ser recuperados mais. Tem certeza que deseja excluir?">Excluir</a>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    <?php } ?>

                                                </tbody>
                                                <tfoot class="text-center">
                                                    <tr>
                                                        <th>Nome Fantasia</th>
                                                        <th>Razão Social</th>
                                                        <th>CNPJ</th>
                                                        <th>Status</th>
                                                        <th>#</th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } else if ($page_start === 'cadastro' || $page_start === 'edicao') { ?>
                    <div class="container-fluid">
                        <div class="page-title">
                            <div class="row">
                                <div class="col-6">
                                    <h3><?php echo ($page_start === 'cadastro' ? 'Cadastro' : 'Edição') ?> de Empresas do Grupo</h3>
                                </div>
                                <div class="col-6">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="<?php echo WEBURL ?>dashboard"><i data-feather="home"></i></a></li>
                                        <li class="breadcrumb-item">Empresas</li>
                                        <li class="breadcrumb-item "><?php echo ($page_start === 'cadastro' ? 'Cadastro' : 'Edição') ?></li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="container-fluid ">
                        <form id="form-produtos" class="theme-form needs-validation" novalidate="" action="<?php echo WEBURL ?>php/modulos/empresas-grupo/code/ajax-empresas-grupo.php?action_type=cadastro_de_empresas&type=<?php echo ($page_start === 'cadastro' ? 'new' : 'edit') ?>&key=<?php echo ($page_start === 'cadastro' ? '' : $current_row->hash) ?>" method="POST">
                            <div class="col-md-12" id="empresas-grupos">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Dados Cadastrais</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="col-form-label" for="razao_social">Razão Social</label>
                                                <input type="text" class="form-control" name="razao_social" placeholder="Razão Social" value="<?php echo (isset($current_row->razao_social) ? $current_row->razao_social : '') ?>" autocomplete="off" required="">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="col-form-label" for="nome_fantasia">Nome Fantasia</label>
                                                <input type="text" class="form-control" name="nome_fantasia" placeholder="Nome Fantasia" value="<?php echo (isset($current_row->nome_fantasia) ? $current_row->nome_fantasia : '') ?>" autocomplete="off" required="">
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label class="col-form-label " for="cnpj">CNPJ</label>
                                                <input type="text" class="form-control field-cnpj " name="cnpj" placeholder="CNPJ" value="<?php echo (isset($current_row->cnpj) ? $current_row->cnpj : '') ?>" autocomplete="off" required="">
                                            </div>
                                            <div class="col-md-2 mb-3">
                                                <label class="col-form-label ">CEP</label>
                                                <input class="form-control search-address field-cep" type="text" name="cep" placeholder="CEP" value="<?php echo (isset($current_row->cep) ? $current_row->cep : '') ?>" autocomplete="off" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="col-form-label" for="logradouro">Logradouro</label>
                                                <input type="text" class="form-control" name="logradouro" placeholder="Endereço" value="<?php echo (isset($current_row->logradouro) ? $current_row->logradouro : '') ?>" autocomplete="off" required="">
                                            </div>
                                            <div class="col-md-1 mb-3">
                                                <label class="col-form-label" for="numero">Número</label>
                                                <input type="text" class="form-control" name="numero" placeholder="N°" value="<?php echo (isset($current_row->numero) ? $current_row->numero : '') ?>" autocomplete="off" required="">
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label class="col-form-label" for="complemento">Complemento</label>
                                                <input class="form-control" type="text" name="complemento" placeholder="Bloco e nº do apartamento, etc." value="<?php echo (isset($current_row->complemento) ? $current_row->complemento : '') ?>" autocomplete="off">
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label class="col-form-label" for="bairro">Bairro</label>
                                                <input class="form-control" type="text" name="bairro" placeholder="Bairro" value="<?php echo (isset($current_row->bairro) ? $current_row->bairro : '') ?>" autocomplete="off" required>
                                            </div>

                                            <!-- CÓDIGO NOVO  -->
                                            <div class="col-md-3 mb-3">
                                                <label class="col-form-label" for="estado">Estado</label>
                                                <select class="form-control btnFiltraCidadesPorEstado" name="estado" required>
                                                    <option data-estado-id="" data-estado="" value="">Selecione</option>
                                                    <?php
                                                    $busca_estados = $classesWeb->busca_estado();
                                                    if (!empty($busca_estados)) {
                                                        foreach ($busca_estados as $ESTADOS) {
                                                            $selected = '';
                                                            if (isset($current_row->estado) && TRIM($current_row->estado) !== '' && TRIM($current_row->estado) === TRIM($ESTADOS->hash)) {
                                                                $selected = 'selected';
                                                            }
                                                            echo '<option data-estado-id="' . $ESTADOS->id . '" data-estado="' . $ESTADOS->uf . '" value="' . $ESTADOS->hash . '" ' . $selected . '>' . $ESTADOS->nome . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                            <!-- CÓDIGO NOVO -->
                                            <div class="col-md-3 mb-3">
                                                <label class="col-form-label" for="cidade">Cidade</label>
                                                <select class="form-control field-select2-general selectCidadesFiltradasPorEstado" name="cidade" required>
                                                    <option value="">Selecione</option>
                                                    <?php
                                                    if (isset($current_row->cidade) && TRIM($current_row->cidade) !== '') {
                                                        $busca_cidade_estado = $classesWeb->busca_cidade_por_estado_pelo_hash($current_row->estado);
                                                        if (!empty($busca_cidade_estado)) {
                                                            foreach ($busca_cidade_estado as $CIDADES) {
                                                                $selected = '';
                                                                if (TRIM($current_row->cidade) !== '' && TRIM($current_row->cidade) === TRIM($CIDADES->hash)) {
                                                                    $selected = 'selected';
                                                                }
                                                                echo '<option data-cidade="' . $CIDADES->nome . '" value="' . $CIDADES->hash . '" ' . $selected . '>' . $CIDADES->nome . '</option>';
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label class="col-form-label" for="logo">Logo da Empresa</label>
                                                <input class="form-control mb-3" type="file" name="logo" <?php
                                                                                                            if ($page_start === 'cadastro') {
                                                                                                                echo 'required';
                                                                                                            }
                                                                                                            ?>>
                                                <p class="text-muted fs-12">Faça o upload de uma imagem no formato 130x45 pixels ou proporcional, de no máximo 1MB</p>
                                                <?php if ($page_start === 'edicao') { ?>
                                                    <a class="logo_view" href="#"><i class="icon-image"></i> Clique aqui para ver a logo atual</a>
                                                <?php } ?>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label class="col-form-label" for="logo">Ícone da Empresa</label>
                                                <input id="icone-input" class="form-control mb-3" type="file" name="icone_empresa" <?php
                                                                                                                                    if ($page_start === 'cadastro') {
                                                                                                                                        echo 'required';
                                                                                                                                    }
                                                                                                                                    ?>>
                                                <p class="text-muted fs-12">Faça o upload de uma imagem no formato 60x60 pixels ou proporcional, de no máximo 1MB</p>
                                                <?php if ($page_start === 'edicao') { ?>
                                                    <a class="icone_view" href="#"><i class="icon-image"></i> Clique aqui para ver a ícone atual</a>
                                                <?php } ?>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label class="col-form-label" for="cor_principal">Cor Príncipal do Tema</label>
                                                <input class=" form-cor" type="color" name="cor_principal" value="<?php echo (isset($current_row->cor_principal) ? $current_row->cor_principal : '') ?>" required autocomplete="off">
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label class="col-form-label" for="cor_secundaria">Cor Secundaria do Tema</label>
                                                <input class=" form-cor" type="color" name="cor_secundaria" value="<?php echo (isset($current_row->cor_secundaria) ? $current_row->cor_secundaria : '') ?>" required autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- CÓDIGO NOVO -->
                            <?php
                            if ($page_start === 'edicao') {
                                $busca_usuarios_empresa = $classesWeb->busca_usuarios_empresa_grupo($current_row->hash);
                            }
                            ?>
                            <div class="col-md-12">
                                <div id="card-impostos" class="card">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-md-6 col-6">
                                                <h5>Contatos da Empresa</h5>
                                            </div>
                                            <div class="col-md-6 col-6 text-right">
                                                <button type="button" class="btn btn-primary btn_abrir_modal_vinculacoes" id="btn_abrir_modal_vinculacao_usuario"><i class="icofont icofont-plus"></i> Vincular Contatos</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <p class="texto_sem_contatos text-center <?php
                                                                                            if ($page_start !== 'cadastro' && isset($busca_usuarios_empresa) && !empty($busca_usuarios_empresa)) {
                                                                                                echo 'esconderElemento';
                                                                                            }
                                                                                            ?>">Não há contatos vinculados ainda para esta empresa.</p>
                                            </div>
                                            <div class="col-md-12 search-page">
                                                <div id="lista_usuarios_vinculados">
                                                    <?php
                                                    if (!empty($busca_usuarios_empresa)) {
                                                        foreach ($busca_usuarios_empresa as $USUARIOS_EMPRESA) {
                                                            echo '<div class="info-block">
                                                                            <div class="row">
                                                                                <div class="col-md-11 col-10">
                                                                                    <h6><i class="icon-user"></i> ' . mb_strtoupper($USUARIOS_EMPRESA->nome, 'UTF-8') . '</h6>
                                                                                </div>
                                                                                <div class="col-md-1 col-2 text-right">
                                                                                    <button type="button" class="icone-excluir-lista btnExcluirContatoEmpresa" data-id-usuario="' . $USUARIOS_EMPRESA->HASH_USUARIO_EMPRESA . '"><i class="icon-close"></i></button>
                                                                                </div>
                                                                            </div>
                                                                            <input type="hidden" name="usuarios_vinculados[]" value="' . $USUARIOS_EMPRESA->hash . '">
                                                                            <div class="star-ratings">
                                                                                <ul class="search-info">
                                                                                    <li><i class="icofont icofont-ui-email"></i> ' . $USUARIOS_EMPRESA->email . '</li>
                                                                                    <li><i class="icofont icofont-ipod-touch"></i> ' . $USUARIOS_EMPRESA->USUARIO_CELULAR . '</li>
                                                                                    <li><i class="icofont icofont-brand-whatsapp"></i> ' . $USUARIOS_EMPRESA->USUARIO_WHATSAPP . '</li>
                                                                                    <li><i class="icofont icofont-ui-call"></i> ' . $USUARIOS_EMPRESA->USUARIO_TELEFONE . '</li>
                                                                                </ul>
                                                                            </div>
                                                                        </div>';
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- CÓDIGO NOVO -->
                            <?php
                            if ($page_start === 'edicao') {
                                $busca_impostos = $classesWeb->busca_grupo_empresa_impostos($current_row->hash);
                            }
                            ?>
                            <div class="col-md-12">
                                <div id="card-impostos" class="card">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-md-6 col-6">
                                                <h5>Impostos da Empresa</h5>
                                            </div>
                                            <div class="col-md-6 col-6 text-right">
                                                <button type="button" class="btn btn-primary btn_abrir_modal_vinculacoes" id="btn_abrir_modal_vinculacao_impostos"><i class="icofont icofont-plus"></i> Vincular Impostos</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <p class="texto_imposto_sem_resultado text-center <?php
                                                                                                    if ($page_start !== 'cadastro' && isset($busca_impostos) && !empty($busca_impostos)) {
                                                                                                        echo 'esconderElemento';
                                                                                                    }
                                                                                                    ?>">Não há impostos vinculados ainda para esta empresa.</p>

                                            </div>
                                            <div class="col-md-12 search-page">
                                                <div id="lista_impostos_vinculados">
                                                    <?php
                                                    if (!empty($busca_impostos)) {
                                                        foreach ($busca_impostos as $IMPOSTOS_EMPRESA) {
                                                            echo '<div class="info-block">
                                                                            <div class="row">
                                                                                <div class="col-md-11 col-10">
                                                                                    <h6><i class="icon-info-alt"></i> ' . $IMPOSTOS_EMPRESA->IMPOSTO_NOME . '</h6>
                                                                                </div>
                                                                                <div class="col-md-1 col-2 text-right">
                                                                                    <button type="button" class="icone-excluir-lista btnExcluirImpostoEmpresa" data-id-imposto="' . $IMPOSTOS_EMPRESA->hash . '"><i class="icon-close"></i></button>
                                                                                </div>
                                                                            </div>
                                                                            <input type="hidden" name="impostos_vinculados[]" value="' . $IMPOSTOS_EMPRESA->imposto_hash . '">
                                                                            <input type="hidden" name="aliquotas_vinculados[]" value="' . formatar_porcentagem_sistema($IMPOSTOS_EMPRESA->aliquota) . '">
                                                                            <input type="hidden" name="reajustes_vinculados[]" value="' . formatar_porcentagem_sistema($IMPOSTOS_EMPRESA->reajuste) . '">
                                                                            <div class="star-ratings">
                                                                                <ul class="search-info">
                                                                                    <li>GRUPO: ' . $IMPOSTOS_EMPRESA->GRUPO_IMPOSTO_NOME . '</li>
                                                                                    <li>ALÍQUOTA: ' . formatar_porcentagem_sistema($IMPOSTOS_EMPRESA->aliquota) . '</li>
                                                                                    <li>REAJUSTE: ' . formatar_porcentagem_sistema($IMPOSTOS_EMPRESA->reajuste) . '</li>
                                                                                </ul>
                                                                            </div>
                                                                        </div>';
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 mt-5">
                                    <div class="card">
                                        <div class="card-body">
                                            <button class="btn btn-primary" type="submit"><?php echo ($page_start === 'cadastro' ? 'Cadastrar' : 'Atualizar') ?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
            </div>
        <?php } ?>
        </div>
        <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/php/includes/footer.php'; ?>
    </div>


    <!-- CÓDIGO NOVO -->
    <div class="modal fade" id="modalAddUsuario" tabindex="-1" role="dialog" aria-labelledby="modalAddUsuario" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Vincular Usuário à Empresa</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Fechar"><i data-feather="x-circle"></i></button>
                </div>
                <div class="modal-body theme-form">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="usuario">Selecione o usuário que deseja adicionar</label>
                            <select class="form-control select2-usuarios-modal" name="usuario" required>
                                <option value="">Selecione</option>
                                <?php
                                $busca_usuarios = $classesWeb->busca_todos_usuarios();
                                if (!empty($busca_usuarios)) {
                                    foreach ($busca_usuarios as $USUARIOS) {
                                        echo '<option value="' . $USUARIOS->hash . '">' . $USUARIOS->nome . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="button" data-bs-dismiss="modal">Fechar</button>
                    <button class="btn btn-primary" id="btn_vincular_usuario_empresa" type="button">Inserir</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalLogo" tabindex="-1" role="dialog" aria-labelledby="modalLogo" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Logo atual da empresa</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Fechar"><i data-feather="x-circle"></i></button>
                </div>
                <div class="modal-body modal-img">
                    <img class="logo_path" src=" <?php echo WEBURL . (isset($current_row->path_logo) ? $current_row->path_logo : '') ?>" alt="Logo Empresa" />
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="button" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalIcone" tabindex="-1" role="dialog" aria-labelledby="modalIcone" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ícone atual da empresa</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Fechar"><i data-feather="x-circle"></i></button>
                </div>
                <div class="modal-body modal-img">
                    <img class="logo_path" src=" <?php echo WEBURL . (isset($current_row->path_icone) ? $current_row->path_icone : '') ?>" alt="Ícone Empresa" />
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="button" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- CÓDIGO NOVO -->
    <div class="modal fade" id="modalAddImpostos" tabindex="-1" role="dialog" aria-labelledby="modalAddImpostos" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Vincular Imposto à Empresa</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Fechar"><i data-feather="x-circle"></i></button>
                </div>
                <div class="modal-body theme-form">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="grupo_imposto_modal">Selecione o grupo do imposto</label>
                            <select class="form-control required" name="grupo_imposto_modal">
                                <?php
                                $busca_grupo_impostos = $classesWeb->busca_impostos_grupo();
                                if (!empty($busca_grupo_impostos)) {
                                    echo '<option value="">Selecione</option>';
                                    foreach ($busca_grupo_impostos as $GRUPO_IMPOSTO) {
                                        echo '<option value="' . $GRUPO_IMPOSTO->hash . '">' . $GRUPO_IMPOSTO->grupo . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="imposto_modal">Selecione o imposto</label>
                            <select class="form-control required" name="imposto_modal">
                                <option value="">Selecione</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="aliquota_modal">Alíquota (%)</label>
                            <input type="text" class="form-control field-percent required" name="aliquota_modal" placeholder="Alíquota (%)" autocomplete="off" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="reajuste_modal">Reajuste (%)</label>
                            <input type="text" class="form-control field-percent required" name="reajuste_modal" placeholder="Reajuste (%)" autocomplete="off" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="button" data-bs-dismiss="modal">Fechar</button>
                    <button class="btn btn-primary" id="btn_vincular_imposto_empresa" type="button">Inserir</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalIcone" tabindex="-1" role="dialog" aria-labelledby="modalLogo" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ícone atual da empresa</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Fechar"><i data-feather="x-circle"></i></button>
                </div>
                <div class="modal-body modal-img">
                    <div id="img-demo" width="95" height="60"></div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="button" data-bs-dismiss="modal">Fechar</button>
                    <button class="btn btn-primary" id="btn_cropie_do_icone" type="button">Inserir</button>
                </div>
            </div>
        </div>
    </div>

    <?php
    gerar_rodape();
    gerar_js(
        array(
            'toastr',
            'datatable',
            'sweetalert',
            'form-submit',
            'management',
            'mask',
            'croppie',
            'mask-money',
            'select2'
        )
    );
    ?>
    <script src="<?php echo WEBURL ?>php/modulos/empresas-grupo/code/js/empresas_grupo.js"></script>
</body>

</html>