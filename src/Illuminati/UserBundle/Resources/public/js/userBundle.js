$(function(){
    // Toggles password input field visibility.
    $('.toggle-pass').click(function() {
        var passwordField = $('.togglable-pass');
        var inputType = passwordField.attr('type');
        if(inputType == 'password') {
            passwordField.attr('type','text');
        } else if ( inputType == 'text' ) {
            passwordField.attr('type','password');
        }
    });
});