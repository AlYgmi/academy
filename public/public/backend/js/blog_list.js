
$(document).on('click', '.editBlog', function () {

    let blog = $(this).data('item');
    console.log(blog)
    $('#BlogId').val(blog.id);
    $('.editTitle').val(blog.title);
    $('.editSlug').val(blog.slug);
    // $('.editPublishDate').val(blog.authored_date);
    $('#image').val(blog.image);
    $('#description').summernote("code", blog.description);

    $('.editPublishDate').datepicker('setDate', blog.authored_date);
    $("#editBlog").modal('show');

});


$(document).on('click', '.deleteBlog', function () {
    let id = $(this).data('id');
    $('#blogDeleteId').val(id);
    $("#deleteBlog").modal('show');
})

$(".editTitle").on('input', function(){
    let title = $(".editTitle").val();
  $(".editSlug").val(convertToSlug(title));
});

$(".addTitle").on('input', function(){
    let title = $(".addTitle").val();
  $(".addSlug").val(convertToSlug(title));
});

function convertToSlug(Text)
{
    return Text
        .toLowerCase()
        .replace(/ /g,'-')
        .replace(/[^\w-]+/g,'')
        ;
}
