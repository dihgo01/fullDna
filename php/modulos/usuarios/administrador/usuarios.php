<?php
if (!isset($_SESSION)) {
    session_start();
}
header('Access-Control-Allow-Origin: *');
require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

include_once 'code/classes-web.class.php';
include_once 'code/functions.php';
$classesWeb = new ClassesWeb();


$page_start = 'listagem';
if (isset($_POST['p2']) and trim($_POST['p2']) === 'register') {
    $page_start = 'register';
} elseif (isset($_POST['p2']) and trim($_POST['p2']) === 'edition') {
    $page_start = 'edition';
    $current_row = $classesWeb->get_query_unica('SELECT * FROM usuarios WHERE hash="' . $_POST['p3'] . '"AND status <> "Inativo"');

    $adapter = new League\Flysystem\Local\LocalFilesystemAdapter(
        $_SERVER['DOCUMENT_ROOT'] . '/uploads/',
    );

    $filesystem = new League\Flysystem\Filesystem($adapter);

    if (empty($current_row)) {
        header('Location: /user');
        exit;
    }
}



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    gerar_cabecalho('User');
    gerar_css(
        array(
            'toastr',
            'date-picker',
            'management',
            'datatable',
            'select2'
        )
    );
    ?>
</head>

<body class="g-sidenav-show  bg-gray-200">

    <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 fixed-start " id="sidenav-main">
        <div class="sidenav-header">
            <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
            <a class="navbar-brand m-0" href="<?php echo WEBURL ?>dashboard">
                <img src="<?php echo WEBURL ?>assets/img/logos/logo-black.png" class="navbar-brand-img h-100" alt="main_logo">
            </a>
        </div>
        <hr class="horizontal light mt-0 mb-2">
        <div class=" w-auto  max-height-vh-100" id="sidenav-collapse-main">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link text-black menu-dna" id="dashboard" href="<?php echo WEBURL ?>dashboard">
                        <div class="text-black text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">dashboard</i>
                        </div>
                        <span class="nav-link-text ms-1">Dashboard</span>
                    </a>
                </li>
                <?php if ($_SESSION['USUARIO_SESSION_ID']['ADMIN'] === 'Yes') { ?>
                    <li class="nav-item">
                        <a class="nav-link text-black <?php echo ($page_start === 'listagem') ? 'active bg-gradient-primary' : '' ?>" id="users" href="<?php echo WEBURL ?>user">
                            <div class="text-black text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="material-icons opacity-10">person</i>
                            </div>
                            <span class="nav-link-text ms-1">Users</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-black <?php echo ($page_start === 'register') ? 'active bg-gradient-primary' : '' ?>" id="register" href="<?php echo WEBURL ?>user/register">
                            <div class="text-black text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="material-icons opacity-10">person_add</i>
                            </div>
                            <span class="nav-link-text ms-1">User Registration</span>
                        </a>
                    </li>
                <?php } ?>
                <li class="nav-item">
                    <a class="nav-link text-black <?php echo ($page_start === 'edition') ? 'active bg-gradient-primary' : '' ?> " id="edit" href="<?php echo WEBURL ?>user/edition/<?php echo  $_SESSION['USUARIO_SESSION_ID']['USUARIO_ID'] ?>">
                        <div class="text-black text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">edit</i>
                        </div>
                        <span class="nav-link-text ms-1">Edit Profile</span>
                    </a>
                </li>
                <?php if ($_SESSION['USUARIO_SESSION_ID']['ADMIN'] === 'Yes') { ?>
                    <li class="nav-item">
                        <a class="nav-link text-black menu-dna" id="files" href="<?php echo WEBURL ?>files">
                            <div class="text-black text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="material-icons opacity-10">description</i>
                            </div>
                            <span class="nav-link-text ms-1">Files</span>
                        </a>
                    </li>
                <?php } ?>
                <li class="nav-item">
                    <a class="nav-link text-black " href="<?php echo WEBURL ?>">
                        <div class="text-black text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">input</i>
                        </div>
                        <span class="nav-link-text ms-1">Sign Up</span>
                    </a>
                </li>
            </ul>
        </div>
    </aside>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <!-- Navbar -->
        <?php include_once 'php/includes/page-header.php'; ?>
        <!-- End Navbar -->
        <?php
        if ($page_start === 'listagem' && ($_SESSION['USUARIO_SESSION_ID']['ADMIN'] === 'Yes')) {
        ?>
            <div class="container-fluid py-4">
                <div class="row">
                    <div class="col-12">
                        <div class="card my-4">
                            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                                <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                    <h6 class="text-white text-capitalize ps-3">Users table</h6>
                                </div>
                            </div>
                            <div class="card-body px-0 pb-5">
                                <div class="text-center mt-5 loader-datatable">
                                    <div class="loader-box">
                                        <div class="loader-3"></div>
                                    </div>
                                </div>
                                <div class="table-responsive p-0 start-datatable-element">
                                    <table class="table align-items-center mb-0 display datatables start-datatable">
                                        <thead>
                                            <tr>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Name</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">E-mail</th>
                                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Admin</th>
                                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Registration Date</th>
                                                <th class="text-secondary opacity-7"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $busca_usuarios = $classesWeb->busca_usuarios();
                                            if (!empty($busca_usuarios)) {
                                            ?>
                                                <?php
                                                foreach ($busca_usuarios as $USUARIOS) {
                                                ?>
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex px-2 py-1">
                                                                <div class="d-flex flex-column justify-content-center">
                                                                    <h6 class="mb-0 text-sm"><?php echo $USUARIOS->nome ?></h6>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <p class="text-xs font-weight-bold mb-0"><?php echo $USUARIOS->email ?></p>
                                                        </td>
                                                        <td class="align-middle text-center text-sm">
                                                            <?php if ($USUARIOS->admin === 'Yes') { ?>
                                                                <span class="badge badge-sm bg-gradient-success">Yes</span>
                                                            <?php } else { ?>
                                                                <span class="badge badge-sm bg-gradient-secondary">No</span>
                                                            <?php } ?>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <span class="text-secondary text-xs font-weight-bold"><?php echo formataDataParaHTML($USUARIOS->data_cadastro) ?></span>
                                                        </td>
                                                        <td class="align-middle">
                                                            <a href="<?php echo WEBURL . 'user/edition/' . $USUARIOS->hash ?>" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" data-original-title="Edit user">
                                                                <i class="material-icons ms-auto text-dark">edit</i>
                                                            </a>
                                                            <a href="#" class="text-secondary font-weight-bold text-xs" data-delete-item="<?php echo $USUARIOS->hash ?>" data-delete-table="usuarios" data-delete-parameter="hash" data-delete-message="By deleting this user the data cannot be recovered anymore. Are you sure you want to delete?" data-original-title="delete user">
                                                                <i class="material-icons ms-auto text-dark">delete</i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } else if ($page_start === 'register' &&  $_SESSION['USUARIO_SESSION_ID']['ADMIN'] === 'Yes' || $page_start === 'edition') { ?>
            <div class="container-fluid py-4">
                <div class="row">
                    <div class="col-12">
                        <form id="form-usuarios" class="theme-form needs-validation" novalidate="" action="<?php echo WEBURL; ?>php/modulos/usuarios/administrador/code/ajax-usuarios.php?action_type=gestao_usuarios&type=<?php echo ($page_start === 'register' ? 'new' : 'edit') ?>&key=<?php echo ($page_start === 'register' ? '' : $current_row->hash) ?>" method="POST">
                            <div class="card my-4">
                                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                        <h6 class="text-white text-capitalize ps-3">User Registration</h6>

                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="<?php echo ($_SESSION['USUARIO_SESSION_ID']['ADMIN'] === 'Yes') ? 'col-md-4' : 'col-md-6' ?> mb-3">
                                            <label for="nome">Name</label>
                                            <input type="text" class="form-control" name="nome" placeholder="Name" value="<?php echo (isset($current_row->nome) ? $current_row->nome : '') ?>" autocomplete="off">
                                        </div>
                                        <div class="<?php echo ($_SESSION['USUARIO_SESSION_ID']['ADMIN'] === 'Yes') ? 'col-md-4' : 'col-md-6' ?> mb-3">
                                            <label for="email">E-mail</label>
                                            <input type="email" class="form-control" name="email" placeholder="E-mail" value="<?php echo (isset($current_row->email) ? $current_row->email : '') ?>" autocomplete="off">
                                        </div>
                                        <?php if ($_SESSION['USUARIO_SESSION_ID']['ADMIN'] === 'Yes') { ?>
                                            <div class="col-md-4 mb-2">
                                                <label for="admin">Administrator</label>
                                                <select class="form-control" name="admin" required>
                                                    <option value="">Select</option>
                                                    <option value="Yes">Yes</option>
                                                    <option value="No">No</option>
                                                    <?php
                                                    $selected = '';
                                                    if (isset($current_row->admin) && TRIM($current_row->admin) !== '') {
                                                        $selected = 'selected';
                                                        echo '<option value="' . $current_row->admin . '" ' . $selected . '>' . $current_row->admin . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="login">Login </label>
                                                <input type="text" class="form-control" name="login" placeholder="Login" value="<?php echo (isset($current_row->username) ? $current_row->username : '') ?>" autocomplete="off" <?php echo (isset($current_row->name) ? '' : 'required=""') ?>>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <label for="senha">Password</label>
                                                <input type="text" class="form-control" name="senha" placeholder="Password" value="<?php echo (isset($current_row->senha) ? '' : '') ?>" autocomplete="off" <?php echo (isset($current_row->senha) ? '' : 'required=""') ?>>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="">
                                            <button class="btn bg-gradient-primary" type="submit"><?php echo ($page_start === 'register' ? 'Register' : 'Update') ?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- SECTION FILES -->
                    <?php if ($page_start === 'edition' && $_SESSION['USUARIO_SESSION_ID']['ADMIN'] === 'Yes') { ?>
                        <!-- VIEW FILES -->
                        <div class="col-12">
                            <div class="card my-4">
                                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                        <h6 class="text-white text-capitalize ps-3">Files</h6>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div id="lista_usuarios_vinculados">
                                        <input type="hidden" id="hash_usuario" value="<?php echo $current_row->hash ?>" />
                                        <div class="row mb-5">
                                            <div class="col-md-10 col-6 divBtn">
                                                <button class="btn bg-gradient-primary btn-voltar" data-retroceder="<?php echo isset($_SESSION['path_anterior']) ? $_SESSION['path_anterior'] : '' ?>"><i class="material-icons">arrow_back</i></button>
                                            </div>
                                            <div class="col-md-2 col-6">
                                                <button class="btn bg-gradient-primary add-folder">Add Folder</button>
                                            </div>
                                        </div>
                                        <ul class="list_folder">
                                            <?php

                                            $listing = $filesystem->listContents($current_row->hash . '/')->toArray(); ?>
                                            <?php
                                            foreach ($listing as $item) {

                                                if ($item instanceof \League\Flysystem\FileAttributes) {
                                                    $folder_path_array = explode("/", $item->path());
                                                    $folder_sem_ultimo_elem = array_pop($folder_path_array);

                                                    $folder_path_certo = implode("/", $folder_path_array);
                                                    echo '<li class ="list-files-user">
                                                            <div class="row">
                                                            <input type="hidden" id="folder" value="' . $folder_path_certo . '" />
                                                                <div class="col-md-10 col-9">
                                                                <a class="actionFile" data-path="' . $item->path() . '" href="#" ><i class="material-icons">description</i> ' . basename($item->path()) . ' </a>
                                                                </div>
                                                                <div class="col-md-2 col-3">
                                                                    <div class="row">
                                                                        <div class="col-4">
                                                                         <a href="' . WEBURL . 'uploads/' . $item->path() . '" download class="" data-path="' . $item->path() . '">
                                                                             <i class="material-icons opacity-10">download</i>
                                                                        </a>
                                                                        </div>
                                                                        <div class="col-4">
                                                                           <a href="#" class="link-delete-files" data-path="' . $item->path() . '">
                                                                            <i class="material-icons opacity-10">delete</i>
                                                                           </a>
                                                                        </div>
                                                                    <div>
                                                                
                                                                </div>
                                                            </div>       
                                                         </li>';
                                                } elseif ($item instanceof \League\Flysystem\DirectoryAttributes) {
                                                    $folder_path_array = explode("/", $item->path());
                                                    $folder_sem_ultimo_elem = array_pop($folder_path_array);

                                                    $folder_path_certo = implode("/", $folder_path_array);
                                                    echo '<li class ="list-files-user">
                                                        <div class="row">
                                                        <input type="hidden" id="folder" value="' . $folder_path_certo . '" />
                                                        <div class="col-md-10 col-9">
                                                        <i class="material-icons">folder</i>
                                                        <a class="actionFolder" data-path-back="' . $current_row->hash . '/' . '" data-path="' . $item->path() . '" href="#" >' . basename($item->path()) . '</a>
                                                        </div>
                                                        <div class="col-md-2 col-3">
                                                            <div class="row">
                                                                <div class="col-4">
                                                                <a href="#" class="link-rename" data-path="' . $item->path() . '">
                                                                    <i class="material-icons opacity-10">edit</i>
                                                                </a>   
                                                                </div>
                                                                <div class="col-4">
                                                                <a href="#" class="link-delete-folder" data-path="' . $item->path() . '">
                                                                    <i class="material-icons opacity-10">delete</i>
                                                                </a>
                                                                </div>
                                                            <div>
                                                            </div>
                                                        </div>       
                                                    </li>';
                                                }
                                            } ?>
                                        </ul>
                                        <div class="row">
                                            <div class="col-md-11 mt-3 mb-3">
                                                <button class="btn bg-gradient-primary add-file">Upload Files</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
        <?php include_once 'php/includes/footer.php'; ?>
    </main>

    <div class="modal fade" id="modalRename" tabindex="-1" role="dialog" aria-labelledby="modalIcone" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Folder Name</h5>

                </div>
                <div class="modal-body modal-img">
                    <input type="hidden" id="path_folder" value="" />
                    <input type="text" class="form-control inputFolder" name="nome_pasta" placeholder="Folder Name" value="" autocomplete="off">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="button" data-bs-dismiss="modal">Close</button>
                    <button class="btn bg-gradient-primary" id="btn_rename" type="button">Rename</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalAddFolder" tabindex="-1" role="dialog" aria-labelledby="modalIcone" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Create New Folder</h5>

                </div>
                <div class="modal-body modal-img">
                    <input type="hidden" id="path_folder_create" value="" />
                    <input type="text" class="form-control inputFolder" name="nome_nova_pasta" placeholder="Folder Name" value="" autocomplete="off">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="button" data-bs-dismiss="modal">Close</button>
                    <button class="btn bg-gradient-primary" id="btn_create" type="button">Create</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalAddFile" tabindex="-1" role="dialog" aria-labelledby="modalIcone" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Upload Files</h5>

                </div>
                <div class="modal-body modal-img">
                    <div class="row">
                        <input type="hidden" id="path_upload" value="" />
                        <div class="col-md-4 mb-3">
                            <label for="title">Title</label>
                            <input type="text" class="form-control" name="titulo" placeholder="Title" value="" autocomplete="off">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="data_emissao_rg">Date of Delete</label>
                            <input type="text" class="form-control field-date field-get-date" name="data_exclusao" placeholder="Date of delete" value="" autocomplete="off" required="">
                        </div>
                        <div class=" col-md-4 mb-3">
                            <label for="file">File</label>
                            <input type="file" class="form-control fileUploadInput" name="file" placeholder="E-mail" value="" autocomplete="off">
                        </div>
                        <div class="col-md-12 mb-2">
                            <label for="admin">Description</label>
                            <textarea type="text" rows="4" class="form-control textDescricao" name="descricao" placeholder="Description" autocomplete="off"><?php echo (isset($current_row->descricao) ? $current_row->descricao : '') ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="button" data-bs-dismiss="modal">Close</button>
                    <button class="btn bg-gradient-primary" id="btn_upload" type="button">Upload</button>
                </div>
            </div>
        </div>
    </div>


    <!--   Core JS Files   -->
    <?php
    gerar_rodape();
    gerar_js(array(
        'toastr',
        'date-picker',
        'datatable',
        'sweetalert',
        'form-submit',
        'management',
        'mask',
        'select2'
    ));
    ?>
    <script src="<?php echo WEBURL ?>/php/modulos/usuarios/administrador/code/usuarios.js"></script>
</body>

</html>