// -------   Mail Send ajax

$(document).ready(function () {
    var form = $('#myForm'); // contact form
    var submit = $('.submit-btn'); // submit button
    var alert = $('.alert-msg'); // alert div for show alert message

    // form submit event
    form.on('submit', function (e) {
        e.preventDefault(); // prevent default form submit

        var name = $('input[name="name"]').val();
        var email = $('input[name="email"]').val();
        var subject = $('input[name="subject"]').val();
        var message = $('[name="message"]').val();
        var hasCaptcha = $('input[name="hasCaptcha"]').val();

        var captcha = $('[name="g-recaptcha-response"]').html();
        console.log(captcha);
        var error = false;


        if (hasCaptcha) {
            if (!grecaptcha.getResponse()) {
                error = true;
                toastr.error('Please verify that you are not a robot');
            }
        }

        if (message == "") {
            error = true;
            toastr.error('Message Field is Required');
        }

        if (subject == "") {
            error = true;
            toastr.error('Subject Field is Required');
        }
        if (email == "") {
            error = true;
            toastr.error('Email Field is Required');
        }
        if (name == "") {
            error = true;
            toastr.error('Name Field is Required');
        }

        if (error) {
            return false;
        }

        $.ajax({
            url: 'contact-submit', // form action url
            type: 'POST', // form submit method get/post
            dataType: 'html', // request type html/json/xml
            data: form.serialize(), // serialize form data
            beforeSend: function () {
                alert.fadeOut();
                submit.html('Sending....'); // change submit button text
            },
            success: function (data) {
                alert.html(data).fadeIn(); // fade in response data
                form.trigger('reset'); // reset form
                submit.attr("style", "display: none !important");
                ; // reset submit button text
            },
            error: function (e) {
                console.log(e)
            }
        });
    });
});
