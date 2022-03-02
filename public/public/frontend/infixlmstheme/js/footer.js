function getList() {
    $('.shoping_cart ,.dark_overlay').toggleClass('active');

    let url = $('.item_list').val();
    $.ajax({
        type: 'GET',
        dataType: 'json',
        url: url,

        success: function (data) {
            $('#cartView').empty();
            let output = '';
            $.each(data, function (k, v) {
                output += '<div class="single_cart">'
                    + '<div data-id="' + v.id + '" class="remove_cart">\n' +
                    '                                                <svg id="icon3" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">\n' +
                    '                                                    <path data-name="Path 174" d="M0,0H16V16H0Z" fill="none"></path>\n' +
                    '                                                    <path data-name="Path 175" d="M14.95,6l-1-1L9.975,8.973,6,5,5,6,8.973,9.975,5,13.948l1,1,3.973-3.973,3.973,3.973,1-1L10.977,9.975Z" transform="translate(-1.975 -1.975)" fill="var(--system_primery_color)"></path>\n' +
                    '                                                </svg>\n' +
                    '                                            </div>' +
                    '<div class="thumb" style="background-image: url(' + v.image + ')">'
                    // + ' <img src="' + v.image + ' " alt="">'
                    + ' </div>'
                    + ' <div class="cart_content">   <h5>' + v.title + '</h5> <p><span class="prise">' + v.price + '</span></p>   </div> </div>';


            });
            console.log(data.length);
            if (data.length == 0) {
                output += '<div class="single_cart"> <h4>No Item into cart</h4> </div>';
            } else {
                $('.view_checkout_btn').show();
            }


            $('#cartView').html(output);
        }
    });
}

$(document).on('click', '.cart_store', function (e) {
    e.preventDefault();
    let btn = $(this);
    let id = $(this).data('id');
    let url = $('.enroll_cart').val();
    let csrf_token = $('.csrf_token').val();
    if ($.isNumeric(id)) {

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: url + '/' + id,
            data: {
                _token: csrf_token
            },
            success: function (data) {

                if (data['result'] === "failed") {
                    toastr.error(data['message']);
                    btn.show();
                } else {
                    toastr.success(data['message']);
                    btn.hide();
                }
                if (data.type === 'addToCart') {
                    $('.notify_count').html(data.total);
                    getList();
                } else {

                }

            }
        });

    } else {
        getList();
    }


});
$(".stripe-button-el").remove();
$(".razorpay-payment-button").hide();
