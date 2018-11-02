$(document).ready(function () {
    function readURL_front(input) {
        $('#img-card-id-front').show();
        if (input.files && input.files[0]) {
            console.log(input.files);
            var reader_image_front = new FileReader();
            reader_image_front.onload = function (e) {
                $('#img-card-id-front').attr('src', e.target.result);
            };
            reader_image_front.readAsDataURL(input.files[0]);
        }
    }function readURL_back(input) {
        $('#img-card-id-back').show();
        if (input.files && input.files[0]) {
            var reader_image_back = new FileReader();
            reader_image_back.onload = function (e) {
                $('#img-card-id-back').attr('src', e.target.result);
            };
            reader_image_back.readAsDataURL(input.files[0]);
        }
    }

    $("#usermanagerupload-image_card_id_front").change(function () {
        readURL_front(this);
    });
    $("#usermanagerupload-image_card_id_back").change(function () {
        readURL_back(this);
    });

});