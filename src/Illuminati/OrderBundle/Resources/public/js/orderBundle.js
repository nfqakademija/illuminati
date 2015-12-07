$(function(){
   $('.submit-mark-as-paid').on('click', function(e){
       e.preventDefault();

       if (confirm("Are you sure you want mark this order as paid?")) {

           var form         = $(this).closest('form');
           var formAction   = form.attr('action');
           var formData     = form.serialize();

           $.post(formAction,formData,function(response){
               if (response.status == 0) {
                   form.next('.marked-as-paid').removeClass('hidden');
                   form.remove();
               }
           });
       }

   });

    // datetime picker

    jQuery('.datetime').datetimepicker({
        format: 'Y-m-d H:00'
    });
});