<?php
include_once 'code/functions.php';
header('Access-Control-Allow-Origin: *');

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <?php
    gerar_cabecalho('Login');
    gerar_css(
        array(
            'toastr',
            'management'
        )
    );
    ?>
</head>

<body>
    <!-- login page start-->
    <main class="main-content  mt-0">
        <section>
            <div class="page-header min-vh-100">
                <div class="container">
                    <div class="row">
                        <div class="col-6 d-lg-flex d-none h-100 my-auto pe-0 position-absolute top-0 start-0 text-center justify-content-center flex-column">
                            <div class="position-relative bg-gradient-primary h-100 m-3 px-7 border-radius-lg d-flex flex-column justify-content-center" style="background-image: url('../assets/img/illustrations/illustration-signup.jpg'); background-size: cover;">
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-5 col-md-7 d-flex flex-column ms-auto me-auto ms-lg-auto me-lg-5">
                            <div class="logo"><img class="img-fluid for-light text-center" src="<?php echo WEBURL ?>assets/img/logos/logo-black.png" alt="loginpage"></div>
                            <div class="card card-plain">
                                <div class="card-header">
                                    <h4 class="font-weight-bolder text-center">Sign In</h4>
                                    <p class="mb-0 text-center">Enter your username and password to register</p>
                                </div>
                                <div class="card-body">
                                    <form id="form-login" class="theme-form needs-validation" novalidate="" action="<?php echo WEBURL ?>code/ajax.php?action_type=login" method="POST">
                                        <div class="input-group input-group-outline mb-3">
                                            <label class="form-label">Username</label>
                                            <input type="text" name='login' class="form-control">
                                        </div>
                                        <div class="input-group input-group-outline mb-3">
                                            <label class="form-label">Password</label>
                                            <input type="password" name='password' class="form-control">
                                        </div>
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-lg bg-gradient-primary btn-lg w-100 mt-4 mb-0">Sign Up</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </section>
    </main>
    <?php
            gerar_rodape();
            gerar_js(array(
                'toastr',
                'sweetalert',
                'form-submit',
                'management',
            ));
            ?>
    <script src="<?php echo WEBURL ?>assets/js/material-dashboard.min.js?v=3.0.0"></script>

</body>

</html>