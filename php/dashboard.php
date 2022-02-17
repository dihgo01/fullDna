<?php
if (!isset($_SESSION)) {
  session_start();
}
include_once 'code/classes-web.class.php';
include_once 'code/functions.php';
$classesWeb = new ClassesWeb();
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT');
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <?php
  gerar_cabecalho('Dashboard');
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

  <?php include_once 'php/includes/sidebar-menu.php'; ?>

  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
    <?php include_once 'php/includes/page-header.php'; ?>
    <!-- End Navbar -->
    <?php if ($_SESSION['USUARIO_SESSION_ID']['ADMIN'] === 'Yes') { ?>
      <div class="container-fluid py-4">
        <?php $busca_total_de_usuarios = $classesWeb->busca_todos_usuarios();
        $busca_total_arquivos = $classesWeb->busca_todos_arquivos_salvos();
        $total_de_download = $classesWeb->busca_total_de_downloads();
        ?>
        <div class="row">
          <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
              <div class="card-header p-3 pt-2">
                <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                  <i class="material-icons opacity-10">description</i>
                </div>
                <div class="text-end pt-1">
                  <p class="text-sm mb-0 text-capitalize">Total Files</p>
                  <h4 class="mb-0"><?php echo  $busca_total_arquivos->FILES ?></h4>
                </div>
              </div>
              <hr class="dark horizontal my-0">
              <div class="card-footer p-3">
              </div>
            </div>
          </div>
          <div class="col-xl-4 col-sm-6">
            <div class="card">
              <div class="card-header p-3 pt-2">
                <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                  <i class="material-icons opacity-10">person</i>
                </div>
                <div class="text-end pt-1">
                  <p class="text-sm mb-0 text-capitalize">Total Users</p>
                  <h4 class="mb-0"><?php echo $busca_total_de_usuarios->USER ?></h4>
                </div>
              </div>
              <hr class="dark horizontal my-0">
              <div class="card-footer p-3">
              </div>
            </div>
          </div>
          <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
              <div class="card-header p-3 pt-2">
                <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                  <i class="material-icons opacity-10">download</i>
                </div>
                <div class="text-end pt-1">
                  <p class="text-sm mb-0 text-capitalize">Total Download</p>
                  <h4 class="mb-0"><?php echo  $total_de_download->DOWNLOADS ?></h4>
                </div>
              </div>
              <hr class="dark horizontal my-0">
              <div class="card-footer p-3">
              </div>
            </div>
          </div>
        </div>
        <div class="row mb-4">
          <div class="col-lg-12 col-md-12 mb-md-0 mb-4">
            <div class="card">
              <div class="card-header pb-0">
                <div class="row">
                  <div class="col-lg-6 col-7">
                    <h6>Latest files added</h6>
                    <p class="text-sm mb-0">
                      <i class="fa fa-check text-info" aria-hidden="true"></i>
                      In the last<span class="font-weight-bold ms-1">30 days</span>
                    </p>
                  </div>
                  <div class="col-lg-6 col-5 my-auto text-end">
                  </div>
                </div>
              </div>
              <div class="card-body px-0 pb-2">
                <?php  ?>
                <div class="table-responsive">
                  <table class="table align-items-center mb-0">
                    <thead>
                      <tr>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Title</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">User</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Registration Date</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $busca_arquivos = $classesWeb->busca_todos_arquivos(7);
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
                              <div class="avatar-group mt-2">
                                <h6 class="mb-0 text-sm"><?php echo $ARQUIVOS->USUARIO ?></h6>
                              </div>
                            </td>
                            <td class="align-middle text-center text-sm">
                              <span class="text-xs font-weight-bold"><?php echo formataDataParaHTML($ARQUIVOS->data_cadastro) ?></span>
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
        <?php include_once 'php/includes/footer.php'; ?>
      </div>
    <?php } else { ?>
      <div class="container-fluid py-4">
        <?php
        $busca_total_arquivos_do_usuario = $classesWeb->busca_todos_arquivos_salvos_pelo_user($_SESSION['USUARIO_SESSION_ID']['USUARIO_ID']);
        $total_de_download_do_usuario = $classesWeb->busca_total_de_downloads_pelo_user($_SESSION['USUARIO_SESSION_ID']['USUARIO_ID']);
        ?>
        <div class="row">
          <div class="col-xl-6 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
              <div class="card-header p-3 pt-2">
                <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                  <i class="material-icons opacity-10">description</i>
                </div>
                <div class="text-end pt-1">
                  <p class="text-sm mb-0 text-capitalize">Total Files</p>
                  <h4 class="mb-0"><?php echo  $busca_total_arquivos_do_usuario->FILES ?></h4>
                </div>
              </div>
              <hr class="dark horizontal my-0">
              <div class="card-footer p-3">
              </div>
            </div>
          </div>
          <div class="col-xl-6 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
              <div class="card-header p-3 pt-2">
                <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                  <i class="material-icons opacity-10">download</i>
                </div>
                <div class="text-end pt-1">
                  <p class="text-sm mb-0 text-capitalize">Total Download</p>
                  <h4 class="mb-0"><?php echo (empty($total_de_download_do_usuario->DOWNLOADS)) ? '0' : $total_de_download_do_usuario->DOWNLOADS  ?></h4>
                </div>
              </div>
              <hr class="dark horizontal my-0">
              <div class="card-footer p-3">
              </div>
            </div>
          </div>
        </div>
        <div class="row mb-4">
          <div class="col-lg-12 col-md-12 mb-md-0 mb-4">
            <div class="card">
              <div class="card-header pb-0">
                <div class="row">
                  <div class="col-lg-6 col-7">
                    <h6>Latest files added</h6>
                    <p class="text-sm mb-0">
                      <i class="fa fa-check text-info" aria-hidden="true"></i>
                      In the last<span class="font-weight-bold ms-1">30 days</span>
                    </p>
                  </div>
                  <div class="col-lg-6 col-5 my-auto text-end">
                  </div>
                </div>
              </div>
              <?php
              $busca_arquivos_do_usuario = $classesWeb->busca_quarto_arquivos_do_usuario($_SESSION['USUARIO_SESSION_ID']['USUARIO_ID']);
              if (!empty($busca_arquivos_do_usuario)) {
              ?>
                <?php
                foreach ($busca_arquivos_do_usuario as $ARQUIVOS) {
                ?>
                 <div class="card-body px-0 card-user">
                    <div class="card-body card-user ">
                      <ul class="list-group">
                        <li class="list-group-item border-0 d-flex p-4 mb-2 bg-gray-100 border-radius-lg">
                          <div class="d-flex flex-column">
                            <h6 class="mb-3 text-sm"><?php echo $ARQUIVOS->titulo ?></h6>
                            <span class="mb-2 text-xs">Registration Data: <span class="text-dark font-weight-bold ms-sm-2"><?php echo formataDataParaHTML($ARQUIVOS->data_cadastro) ?></span></span>
                            <span class="mb-2 text-xs">Description: <span class="text-dark ms-sm-2 font-weight-bold"><?php echo $ARQUIVOS->descricao ?></span></span>
                          </div>
                          <div class="ms-auto text-end">
                            <a class="btn btn-link text-danger text-gradient px-3 mb-0" href="#" data-delete-item="<?php echo $ARQUIVOS->HASH_FILE ?>" data-delete-table="arquivos" data-delete-parameter="hash" data-delete-message="By deleting this file the data cannot be recovered anymore. Are you sure you want to delete?" data-original-title="delete file"><i class="material-icons text-sm me-2">delete</i>Delete</a>
                            <a class="btn btn-link text-dark px-3 mb-0" href="<?php echo WEBURL ?>php/modulos/arquivos/code/ajax-arquivos.php?action_type=dowload_de_arquivo&path_file=<?php echo $ARQUIVOS->path ?>&qtd_download=<?php echo $ARQUIVOS->qtd_download?>&hash_file=<?php echo $ARQUIVOS->HASH_FILE ?>"><i class="material-icons text-sm me-2">download</i>Download</a>
                          </div>
                        </li>
                      </ul>
                    </div>
                  </div>
                <?php } ?>
              <?php } else { ?>
                <div class="card-body px-0 pb-2">
                  <div class="card-body pt-4 p-3">
                      <h3 class="mb-2 text-center" >No Files</h3>
                  </div>

                </div>
              <?php } ?>
            </div>
          </div>
        </div>
        <?php include_once 'php/includes/footer.php'; ?>
      </div>
    <?php } ?>
  </main>
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

</body>

</html>