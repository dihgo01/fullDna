<?php
include_once 'code/functions.php';
include_once 'code/classes-web.class.php';
$classesWeb = new ClassesWeb();

?>
<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 fixed-start " id="sidenav-main">
<div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand m-0" href="<?php echo WEBURL ?>dashboard">
            <img src="<?php echo WEBURL ?>assets/img/logos/logo-black.png" class="navbar-brand-img h-100" alt="main_logo">
        </a>
    </div>
    <hr class="horizontal light mt-0 mb-2">
    <div class="w-auto  max-height-vh-100" id="sidenav-collapse-main">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link text-black active bg-gradient-primary menu-dna" id="dashboard" href="<?php echo WEBURL ?>dashboard">
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
