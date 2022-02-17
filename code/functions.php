<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/variaveis-aplicacao.php';
include_once 'classes-web.class.php';
include_once 'functions.php';
header('Access-Control-Allow-Origin: *');

function retorna_apenas_numeros($string)
{
    return preg_replace('/\D+/', '', $string); //
}

function validar_cpf_cnpj($numero, $tipo)
{
    if ($tipo === 'CNPJ') {
        $cnpj = preg_replace('/[^0-9]/', '', (string) $numero);
        if (strlen($cnpj) != 14)
            return false;
        if (preg_match('/(\d)\1{13}/', $cnpj))
            return false;
        for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        $resto = $soma % 11;
        if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto))
            return false;
        for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        $resto = $soma % 11;
        return $cnpj[13] == ($resto < 2 ? 0 : 11 - $resto);
    } else {
        $cpf = preg_replace('/[^0-9]/is', '', $numero);
        if (strlen($cpf) != 11) {
            return false;
        }
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        return true;
    }
}

function retorna_percentual_decimal_banco_dados($percentual)
{
    $percentual = str_replace('%', '', $percentual);
    $percentual = str_replace('.', '', $percentual);
    $percentual = str_replace(',', '.', $percentual);
    return $percentual;
}

function retorna_ddd_numero_telefone($numero)
{
    $numero = retorna_apenas_numeros($numero);
    return array(
        'DDD' => substr($numero, 0, 2),
        'NUMERO' => substr($numero, 2)
    );
}

function retorna_sim_nao_binario($valor)
{
    if ((int) $valor === 1) {
        return 'Sim';
    } else {
        return 'Não';
    }
}

function formata_data_banco_dados($data)
{
    return implode('-', array_reverse(explode('/', $data)));
}

function gerar_hash()
{
    return md5(sha1(sha1(uniqid() . uniqid() . date('YmdHisu') . uniqid())));
}

function gerar_cabecalho($titulo_pagina = 'Intranet')
{
    $modulo = 'FullDNA';
?>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">
    <link rel="icon" href="<?php echo WEBURL ?>assets/img/logos/icon-logo.png" type="image/x-icon">
    <link rel="shortcut icon" href="<?php echo WEBURL ?>assets/img/logos/icon-logo.png" type="image/x-icon">
    <title><?php echo $titulo_pagina . ' - ' . $modulo ?></title>
    <!-- Google font-->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900&amp;display=swap" rel="stylesheet">
    <!--     Fonts and icons     -->
    <!-- ico-font-->
    <link rel="stylesheet" type="text/css" href="../assets/css/vendors/icofont.css">
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700" />
    <!-- Nucleo Icons -->
    <link href="<?php echo WEBURL ?>assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="<?php echo WEBURL ?>assets/css/nucleo-svg.css" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <!-- CSS Files -->
    <link href="<?php echo WEBURL ?>assets/css/style.css" rel="stylesheet" />
    <link id="pagestyle" href="<?php echo WEBURL ?>assets/css/material-dashboard.css?v=3.0.0" rel="stylesheet" />

    <link rel="stylesheet" type="text/css" href="<?php echo WEBURL ?>assets/css/custom.css">
    <link rel="stylesheet" type="text/css" href="<?php echo WEBURL ?>assets/css/fontawesome.css">
<?php
}

function gerar_css($plugins = array())
{
    if (!empty($plugins)) {
        foreach ($plugins as $LOAD_PLUGINS) {
            switch ($LOAD_PLUGINS) {
                case 'chartlist':
                    echo '<link rel="stylesheet" type="text/css" href="' . WEBURL . 'assets/css/vendors/chartist.css">';
                    break;
                case 'apex-chart':
                    echo '<link rel="stylesheet" type="text/css" href="' . WEBURL . 'assets/css/vendors/apexcharts.css">';
                    break;
                case 'date-time-picker':
                    echo '<link rel="stylesheet" type="text/css" href="' . WEBURL . 'assets/css/vendors/date-time-picker.css">';
                    break;
                case 'date-range-picker':
                    echo '<link rel="stylesheet" type="text/css" href="' . WEBURL . 'assets/css/vendors/daterange-picker.css">';
                    break;
                case 'full-calendar':
                    echo '<link rel="stylesheet" type="text/css" href="' . WEBURL . 'assets/css/fullcalendar/main.min.css">';
                    break;
                case 'full-calendar-scheduler':
                    echo '<link rel="stylesheet" type="text/css" href="' . WEBURL . 'assets/css/fullcalendar/scheduler/main.min.css">';
                    break;
                case 'croppie':
                    echo '<link rel="stylesheet" type="text/css" href="' . WEBURL . 'assets/js/croppie/croppie.css">';
                    break;
                case 'calendar':
                    echo '<link rel="stylesheet" type="text/css" href="' . WEBURL . 'assets/css/vendors/calendar.css">';
                    break;
                case 'owlcarousel':
                    echo '<link rel="stylesheet" type="text/css" href="' . WEBURL . 'assets/css/vendors/owlcarousel.css">';
                    break;
                case 'prism':
                    echo '<link rel="stylesheet" type="text/css" href="' . WEBURL . 'assets/css/vendors/prism.css">';
                    break;
                case 'select2':
                    echo '<link rel="stylesheet" type="text/css" href="' . WEBURL . 'assets/css/vendors/select2.css">';
                    break;
                case 'select2':
                    echo '<link rel="stylesheet" type="text/css" href="' . WEBURL . 'assets/css/vendors/select2.css">';
                    break;
                case 'date-picker':
                    echo '<link rel="stylesheet" type="text/css" href="' . WEBURL . 'assets/css/vendors/date-picker.css">';
                    break;
                case 'datatable':
                    echo '<link rel="stylesheet" type="text/css" href="' . WEBURL . 'assets/css/vendors/datatables.css">';
                    break;
                case 'summernote':
                    echo '<link rel="stylesheet" type="text/css" href="' . WEBURL . 'assets/css/vendors/summernote.css">';
                    break;
                case 'toastr':
                    echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" integrity="sha512-3pIirOrwegjM6erE5gPSwkUzO+3cTjpnV9lexlNZqvupR64iZBnOOTiiLPb9M36zpMScbmUNIcHUqKD47M719g==" crossorigin="anonymous" referrerpolicy="no-referrer" />';
                    break;
                case 'photoswipe':
                    echo '<link rel="stylesheet" type="text/css" href="' . WEBURL . 'assets/css/vendors/photoswipe.css">';
                    break;
                case 'daterange-picker':
                    echo '<link rel="stylesheet" type="text/css" href="' . WEBURL . 'assets/css/vendors/daterange-picker.css">';
                    break;
                case 'fancybox':
                    echo '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css"/>';
                    break;
                default:
                    break;
            }
        }
    }
}

function gerar_rodape()
{
?>
    <!-- latest jquery-->
    <script src="<?php echo WEBURL ?>assets/js/jquery-3.5.1.min.js"></script>
    <!-- Bootstrap js-->
    <script src="<?php echo WEBURL ?>assets/js/bootstrap/bootstrap.bundle.min.js"></script>
    <!-- feather icon js-->
    <script src="<?php echo WEBURL ?>assets/js/icons/feather-icon/feather.min.js"></script>
    <script src="<?php echo WEBURL ?>assets/js/icons/feather-icon/feather-icon.js"></script>
    <!-- scrollbar js-->
    <script src="<?php echo WEBURL ?>assets/js/core/popper.min.js"></script>
    <script src="<?php echo WEBURL ?>assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="<?php echo WEBURL ?>assets/js/plugins/smooth-scrollbar.min.js"></script>
    <script src="<?php echo WEBURL ?>assets/js/plugins/chartjs.min.js"></script>
    <!-- Sidebar jquery-->
    <script src="<?php echo WEBURL ?>assets/js/config.js"></script>
    <!-- Plugins JS start-->
    <script src="<?php echo WEBURL ?>assets/js/tooltip-init.js"></script>
    <!-- Plugins JS Ends-->
    <!-- Theme js-->
    <script src="<?php echo WEBURL ?>assets/js/script.js"></script>
    <!-- login js-->
<?php
}

function gerar_js($plugins = array())
{
    if (!empty($plugins)) {
        foreach ($plugins as $LOAD_PLUGINS) {
            switch ($LOAD_PLUGINS) {
                case 'sweetalert':
                    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>';
                    break;
                case 'croppie':
                    echo '<script src="' . WEBURL . 'assets/js/croppie/croppie.js"></script>';
                    break;
                case 'to-do':
                    echo '<script src="' . WEBURL . 'assets/js/todo/todo.js"></script>';
                    break;
                case 'apex-chart':
                    echo '<script src="' . WEBURL . 'assets/js/chart/apex-chart/apexcharts.min.js"></script>';
                    break;
                case 'timelinev1':
                    echo '<script src="' . WEBURL . 'assets/js/timeline/timeline-v-1/main.js"></script>';
                    echo '<script src="' . WEBURL . 'assets/js/modernizr.js"></script>';

                    break;
                case 'date-range-picker':
                    echo '<script src="' . WEBURL . 'assets/js/datepicker/daterange-picker/daterangepicker.js"></script>';
                    break;
                case 'full-calendar':
                    echo '<script src="' . WEBURL . 'assets/js/fullcalendar/locales/pt-br.js"></script>';
                    echo '<script src="' . WEBURL . 'assets/js/fullcalendar/main.min.js"></script>';
                    echo '<script src="' . WEBURL . 'assets/js/fullcalendar/locales-all.min.js"></script>';
                    break;
                case 'toast-calendar':
                    echo '<script src="' . WEBURL . 'assets/js/calendar/tui-code-snippet.min.js"></script>';
                    echo '<script src="' . WEBURL . 'assets/js/calendar/tui-time-picker.min.js"></script>';
                    echo '<script src="' . WEBURL . 'assets/js/calendar/tui-date-picker.min.js"></script>';
                    echo '<script src="' . WEBURL . 'assets/js/calendar/tui-calendar.js"></script>';
                    echo '<script src="' . WEBURL . 'assets/js/calendar/chance.min.js"></script>';
                    break;
                case 'calendar':
                    echo ' <script src="https://uicdn.toast.com/tui.code-snippet/v1.5.2/tui-code-snippet.min.js"></script>';
                    echo '<script src="https://uicdn.toast.com/tui.time-picker/latest/tui-time-picker.min.js"></script>';
                    echo '<script src="https://uicdn.toast.com/tui.date-picker/latest/tui-date-picker.min.js"></script>';
                    echo '<script src="https://uicdn.toast.com/tui-calendar/latest/tui-calendar.js"></script>';
                    break;
                case 'popperjs':
                    echo ' <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>';
                    break;
                case 'apex-charts':
                    echo '<script src="' . WEBURL . 'assets/js/chart/apex-chart/apex-chart.js"></script>';
                    break;
                case 'form-submit':
                    echo '<script src="' . WEBURL . 'assets/js/form-validation-custom.js"></script>';
                    include_once $_SERVER['DOCUMENT_ROOT'] . '/php/includes/js/form-submit.php';
                    break;
                case 'prism':
                    echo '<link rel="stylesheet" type="text/css" href="' . WEBURL . 'assets/css/vendors/prism.css">';
                    break;
                case 'datatable':
                    echo '<script src="' . WEBURL . 'assets/js/datatable/datatables/jquery.dataTables.min.js"></script>';
                    echo '<script src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>';
                    echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>';
                    echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>';
                    echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>';
                    echo '<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js"></script>';
                    echo '<script src="https://cdn.datatables.net/plug-ins/1.10.21/filtering/type-based/accent-neutralise.js"></script>';
                    break;
                case 'select2':
                    echo '<script src="' . WEBURL . 'assets/js/select2/select2.full.min.js"></script>';
                    break;
                case 'date-picker':
                    echo '<script src="' . WEBURL . 'assets/js/datepicker/date-picker/datepicker.js"></script>';
                    echo '<script src="' . WEBURL . 'assets/js/datepicker/date-picker/datepicker.en.js"></script>';
                    break;
                case 'date-time-picker':
                    echo '<script src="' . WEBURL . 'assets/js/datepicker/date-time-picker/tempusdominus-bootstrap-4.min.js"></script>';
                    echo '<script src="' . WEBURL . 'assets/js/datepicker/date-time-picker/datetimepicker.custom.js"></script>';
                    break;
                case 'summernote':
                    echo '<script src="' . WEBURL . 'assets/js/editor/summernote/summernote.js?v=1.1"></script>';
                    break;
                case 'management':
                    echo '<script src="' . WEBURL . 'code/js/management.js?v=1.3"></script>';
                    break;
                case 'all':
                    echo '<script src="' . WEBURL . 'code/js/all.js"></script>';
                    break;
                case 'toastr':
                    echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>';
                    break;
                case 'mask-money':
                    echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-maskmoney/3.0.2/jquery.maskMoney.min.js" integrity="sha256-U0YLVHo5+B3q9VEC4BJqRngDIRFCjrhAIZooLdqVOcs=" crossorigin="anonymous"></script>';
                    break;
                case 'mask':
                    echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.js" integrity="sha512-0XDfGxFliYJPFrideYOoxdgNIvrwGTLnmK20xZbCAvPfLGQMzHUsaqZK8ZoH+luXGRxTrS46+Aq400nCnAT0/w==" crossorigin="anonymous"></script>';
                    break;
                case 'photoswipe':
                    echo '<script src="' . WEBURL . 'assets/js/photoswipe/photoswipe.min.js"></script>';
                    echo '<script src="' . WEBURL . 'assets/js/photoswipe/photoswipe-ui-default.min.js"></script>';
                    echo '<script src="' . WEBURL . 'assets/js/photoswipe/photoswipe.js"></script>';
                    break;
                case 'touchspin':
                    echo '<script src="' . WEBURL . 'assets/js/touchspin/touchspin.js"></script>';
                    echo '<script src="' . WEBURL . 'assets/js/touchspin/input-groups.min.js"></script>';
                    break;
                case 'moment':
                    echo '<script src="' . WEBURL . 'assets/js/datepicker/date-time-picker/moment.min.js"></script>';
                    break;
                case 'daterange-picker':
                    echo '<script src="' . WEBURL . 'assets/js/datepicker/daterange-picker/daterangepicker.js"></script>';
                    echo '<script src="' . WEBURL . 'assets/js/datepicker/daterange-picker/daterange-picker.custom.js"></script>';
                    break;
                case 'jspdf':
                    echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.4.1/jspdf.min.js"></script> ';
                    echo '<script src="https://cdn.jsdelivr.net/npm/canvas2image@1.0.5/canvas2image.min.js"></script>';
                    break;
                case 'morris-chart':
                    echo '<script src="' . WEBURL . 'assets/js/chart/morris-chart/raphael.js"></script>';
                    echo '<script src="' . WEBURL . 'assets/js/chart/morris-chart/morris.js"></script>';
                    echo '<script src="' . WEBURL . 'assets/js/chart/morris-chart/prettify.min.js"></script>';
                    break;
                case 'fancybox':
                    echo '<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js"></script>';
                    break;
                case 'exibition':
                    echo '<script src="' . WEBURL . 'php/modulos/usuarios/administrador/code/js/exibition.js"></script>';
                    break;
                case 'mask':
                    echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>';
                    break;
                default:
                    break;
            }
        }
    }
}

function remover_acentos($string)
{
    $array_acentos = array('à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ü', 'ú', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'O', 'Ù', 'Ü', 'Ú');
    $array_sem_acentos = array('a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'y', 'A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U');
    return str_replace($array_acentos, $array_sem_acentos, $string);
}

function gerar_nome_amigavel($nome_arquivo)
{
    while (strpos($nome_arquivo, ' ') !== false) {
        $nome_arquivo = str_replace(' ', '-', $nome_arquivo);
    }
    $nome_arquivo = mb_strtolower($nome_arquivo);
    setlocale(LC_CTYPE, 'pt_BR');
    $nome_arquivo = remover_acentos($nome_arquivo);
    return $nome_arquivo;
}

function formatar_porcentagem_sistema($valor)
{
    return number_format($valor, 2, ',', '.') . '%';
}

function redimensionamentoDeIcone($resourceType, $image_width, $image_height)
{
    $resizeWidth = 60;
    $resizeHeight = 60;
    $imageLayer = imagecreatetruecolor($resizeWidth, $resizeHeight);
    imagesavealpha($imageLayer, true); //canal alpha
    imagealphablending($imageLayer, false); //Desabilita a mesclagem
    $transparent = imagecolorallocatealpha($imageLayer, 255, 255, 255, 127);
    imagefilledrectangle($imageLayer, 0, 0, $resizeWidth, $resizeHeight, $transparent);
    imagecopyresampled($imageLayer, $resourceType, 0, 0, 0, 0, $resizeWidth, $resizeHeight, $image_width, $image_height);
    return $imageLayer;
}

function dateTimeFormatsystemStatus($datetime)
{
    $datetimeformat = explode(" ", $datetime);

    $dateformat = implode('/', array_reverse(explode('-', $datetimeformat[0])));

    $resultformat = $dateformat . " ás " . $datetimeformat[1];

    return $resultformat;
}

function formatDataeHoraDB($datetime)
{
    $datetimeformat = explode(" ", $datetime);

    $dateformat = implode('-', array_reverse(explode('/', $datetimeformat[0])));

    $resultformat = $dateformat . " " . $datetimeformat[1];

    return $resultformat;
}


function formataDataParaHTML($data)
{

    $dateformat = explode(" ", $data);

    $dateformatHTML = implode('/', explode('-', $dateformat[0]));

    $resultformat =  $dateformatHTML;

    return $resultformat;
}

function calcula_periodo_aquisitivo($data_inicial)
{
    $periodo_aquisitivo = implode('/', array_reverse(explode('-', date('d/m/Y', strtotime('+1 years', strtotime($data_inicial))))));
    return $periodo_aquisitivo;
}

function calcula_data_limite_ferias($data_inicial)
{
    $data_limite_ferias = implode('/', array_reverse(explode('-', date('d/m/Y', strtotime('+2 years', strtotime($data_inicial))))));
    return $data_limite_ferias;
}
