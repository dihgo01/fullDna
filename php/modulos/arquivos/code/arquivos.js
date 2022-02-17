

/*$('body').on('click' , '.download_file', function(e){
    e.preventDefault()
    let hash_file = $(this).attr('data-hash-file');
    let qtd_download = $(this).attr('data-qtd-download');
    let path_file = $(this).attr('data-path');

    $.ajax({
        url: "/php/modulos/arquivos/code/ajax-arquivos.php",
        type: "POST",
        dataType: "JSON",
        data: {
            action_type: 'dowload_de_arquivo',
            hash_file: hash_file,
            qtd_download: qtd_download,
            path_file: path_file
            
        }, success(data) {
            if(data.status === 'OK') {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: data.message
                })       
            }
        }
    })
}) */