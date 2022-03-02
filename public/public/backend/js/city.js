$( document ).ready(function() {
    $('#add_city_modal').hide();
});
function open_add_city_modal(el){
    $('#add_city_modal').modal('show');
    $('#city_add').modal('show');
}
function edit_city_modal(el){
    let url = $('.city_edit').val();
    let token = $('.csrf_token').val();
    $.post(url, {_token:token, id:el}, function(data){
        $('#edit_form').html(data);
        $('#Item_Edit').modal('show');
    });
}
