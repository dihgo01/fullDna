<?php
if (!isset($_SESSION)) {
    session_start();
}
header('Access-Control-Allow-Origin: *');

include_once 'code/classes-web.class.php';
include_once 'code/functions.php';
$classesWeb = new ClassesWeb();

$page_start = 'listagem';
if (isset($_POST['p2']) and trim($_POST['p2']) === 'register') {
    $page_start = 'register';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    gerar_cabecalho('Files');
    gerar_css(
        array(
            'toastr',
            'management',
            'datatable',
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
                        <a class="nav-link text-black menu-dna" id="users" href="<?php echo WEBURL ?>user">
                            <div class="text-black text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="material-icons opacity-10">person</i>
                            </div>
                            <span class="nav-link-text ms-1">Users</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-black menu-dna" id="register" href="<?php echo WEBURL ?>user/register">
                            <div class="text-black text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="material-icons opacity-10">person_add</i>
                            </div>
                            <span class="nav-link-text ms-1">User Registration</span>
                        </a>
                    </li>
                <?php } ?>
                <li class="nav-item">
                    <a class="nav-link text-black menu-dna " id="edit" href="<?php echo WEBURL ?>user/edition/<?php echo  $_SESSION['USUARIO_SESSION_ID']['USUARIO_ID'] ?>">
                        <div class="text-black text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">edit</i>
                        </div>
                        <span class="nav-link-text ms-1">Edit Profile</span>
                    </a>
                </li>
                <?php if ($_SESSION['USUARIO_SESSION_ID']['ADMIN'] === 'Yes') { ?>
                    <li class="nav-item">
                        <a class="nav-link text-black menu-dna active bg-gradient-primary" id="files" href="<?php echo WEBURL ?>files">
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
        if ($page_start === 'listagem') {
        ?>
            <div class="container-fluid py-4">
                <div class="row">
                    <div class="col-12">
                        <div class="card my-4">
                            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                                <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                    <h6 class="text-white text-capitalize ps-3">Files table</h6>
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
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Title</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">User</th>
                                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Registration Date</th>
                                                <th class="text-secondary opacity-7"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $busca_arquivos = $classesWeb->busca_todos_arquivos();
                                            if (!empty($busca_arquivos)) {
                                            ?>
                                                <?php
                                                foreach ($busca_arquivos as $ARQUIVOS) {
                                                ?>
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex px-2 py-1">
                                                                <div class="d-flex flex-column justify-content-center">
                                                                    <h6 class="mb-0 text-sm"><?php echo $ARQUIVOS->titulo ?></h6>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <p class="text-xs font-weight-bold mb-0"><?php echo $ARQUIVOS->USUARIO ?></p>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <span class="text-secondary text-xs font-weight-bold"><?php echo formataDataParaHTML($ARQUIVOS->data_cadastro) ?></span>
                                                        </td>
                                                        <td class="align-middle">
                                                            <a href="<?php echo WEBURL ?>php/modulos/arquivos/code/ajax-arquivos.php?action_type=dowload_de_arquivo&path_file=<?php echo $ARQUIVOS->path ?>&qtd_download=<?php echo $ARQUIVOS->qtd_download ?>&hash_file=<?php echo $ARQUIVOS->HASH_FILE ?>" class="text-secondary font-weight-bold text-xs download_file" data-original-title="Download File">
                                                                <i class="material-icons ms-auto text-dark">download</i>
                                                            </a>
                                                            <a href="#" class="text-secondary font-weight-bold text-xs" data-delete-item="<?php echo $ARQUIVOS->HASH_FILE ?>" data-delete-table="arquivos" data-delete-parameter="hash" data-delete-message="By deleting this file the data cannot be recovered anymore. Are you sure you want to delete?" data-original-title="delete file">
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
        <?php } ?>
        <?php include_once 'php/includes/footer.php'; ?>
    </main>
    <!--   Core JS Files   -->
    <?php
    gerar_rodape();
    gerar_js(array(
        'toastr',
        'datatable',
        'sweetalert',
        'form-submit',
        'management',
    ));
    ?>

    <script src="<?php echo WEBURL ?>php/modulos/arquivos/code/arquivos.js"></script>

</body>

</html>