
$( document ).ready(function() {
    // common JavaScripts

    $('[data-toggle="tooltip"]').tooltip();

    // disabling send emails button on click
    $('#send-reminder-emails').click(function(){
        $(this).html('Sending payment reminder emails, please wait...');
        $(this).attr('disabled','disabled');
    });
});