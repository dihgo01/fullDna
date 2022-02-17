$(document).ready(function () {
    if ($('input.field-cpf').length > 0) {
        $('input.field-cpf').mask('999.999.999-99');
    }
    
    if ($('input.field-phone').length > 0) {
        $('input.field-phone').mask('(99) 99999-9999');
    }

    $('.change-state').change(function () {
        var div = $(this).closest('form');
        $.ajax({
            url: '/code/ajax.php',
            type: 'POST',
            data: {
                action_type: 'get_cities_by_state',
                key: $(this).find('option:selected').val()
            },
            success: function (data) {
                div.find('.cities-by-state').html('');
                div.find('.cities-by-state').html(data);
            }
        });
    });
});