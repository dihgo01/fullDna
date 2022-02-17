<script>
    (function () {
        'use strict';
        window.addEventListener('load', function () {
            var forms = document.getElementsByClassName('needs-validation');
            var validation = Array.prototype.filter.call(forms, function (form) {
                form.addEventListener('submit', function (event) {
                    form.classList.remove('not-valid-form');
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Campos obrigatórios não preenchidos!'
                        });
                        form.classList.add('not-valid-form');
                    } else {
                        event.preventDefault();
                        event.stopPropagation();
                        var btn = $('#' + form.attributes[0].value).find('button[type="submit"]');
                        var btn_text = btn.text();
                        var loader = '<div class="loader-box"><div class="loader-15"></div></div>';
                        btn.html(loader);

                        toastr.options = {
                            debug: false,
                            positionClass: "toast-bottom-full-width",
                            onclick: null,
                            fadeIn: 300,
                            extendedTimeOut: 0,
                            timeOut: 0,
                            preventDuplicates: true,
                            closeButton: false,
                        };

                        console.log(form.attributes[0].value)

                        var formReady = new FormData($('#' + form.attributes[0].value)[0]);
                        $.ajax({
                            url: $('#' + form.attributes[0].value).attr('action'),
                            type: 'POST',
                            crossDomain: true,
                            dataType: 'json',
                            data: formReady,
                            processData: false,
                            contentType: false,
                            success: function (json) {
                                if (json.status === 'OK') {
                                    switch (json.type) {
                                        case 'redirect_dashboard':
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Sucesso!',
                                                text: json.message
                                            }).then(function () {
                                                window.location.href = '/dashboard';
                                            });
                                            break;
                                        case 'login_redirect':
                                            window.location.href = '/dashboard';
                                            break;
                                        case 'redirect_to_code_pass':
                                            $('#form-request-code-password input').val('');
                                            $('#form-request-code-password').hide();
                                            $('#form-send-code-password').slideDown(150);
                                            break;
                                        case 'redirect_to_new_pass':
                                            $('#form-send-code-password input').val('');
                                            $('#form-send-code-password').hide();
                                            $('#form-reset-password').slideDown(150);
                                            break;
                                        case 'redirect':
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Sucesso!',
                                                text: json.message
                                            }).then(function () {
                                                window.location.href = json.url;
                                            });
                                            break;
                                        default:
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Success!',
                                                text: json.message
                                            }).then(function () {
                                                window.location.reload();
                                            });
                                            break;
                                    }
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: json.message
                                    });
                                    btn.html(btn_text);
                                }
                                toastr.clear();
                            }
                        });
                        return false;
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();
</script>