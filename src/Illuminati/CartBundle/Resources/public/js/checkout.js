
$( document ).ready(function() {

    // checkout JavaScripts
    $('#checkout').on('change', 'input.quantity', function (e) {

        $form = $(this).closest('form');

        $.ajax({
            method: "POST",
            url: $form.attr('action'),
            data: $form.serialize()
        })
        .done(function(html) {
            $('#checkout').html(html);
        })
        .fail(function() {
            alert( "error" );
        });

    });

});