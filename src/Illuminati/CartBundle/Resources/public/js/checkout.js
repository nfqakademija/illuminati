
$( document ).ready(function() {
    // checkout JavaScripts

    $('#checkout').on('click', 'a[data-remove]', function (e) {

        e.preventDefault();

        var rowId = $(this).attr('data-remove');

        $.ajax({
            method: "POST",
            url: $(this).attr('href')
        })
        .done(function(html) {
            $('#checkout').html(html);
        })
        .fail(function() {
            alert( "error" );
        });

    });

    $('#checkout').on('change', '[data-quantity]', function (e) {

        var $row = $(this).closest('tr[data-row]');

        var productId = $row.find('input[data-product-id]').val();
        var orderId = $('#checkout table[data-order-id]').attr('data-order-id');
        var quantity = $(this).val();

        $.ajax({
            method: "POST",
            url: '/cart/add/product/' + productId + '/order/' + orderId,
            data: {"quantity": quantity}
        })
        .done(function(html) {
            $('#checkout').html(html);
        })
        .fail(function() {
            alert( "error" );
        });

    });


});