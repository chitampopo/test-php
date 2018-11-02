$(".pop").popover({trigger: "manual", html: true, animation: false})
    .on("mouseenter", function () {
        var _this = this;
        $(this).popover("show");
        $(".popover").on("mouseleave", function () {
            $(_this).popover('hide');
        });
    }).on("mouseleave", function () {
    var _this = this;
    setTimeout(function () {
        if (!$(".popover:hover").length) {
            $(_this).popover("hide");
        }
    }, 100);
});
$('.date-picker').datepicker({
    autoclose: true,
    todayHighlight: true,
    dateFormat: 'dd/mm/yy',
    lang: "vi-VN"
}).next().on(ace.click_event, function () {
    $(this).prev().focus();
});
if (!ace.vars['touch']) {
    $('.chosen-select').chosen({allow_single_deselect: true, search_contains: true});
    //resize the chosen on window resize
    $(window)
        .off('resize.chosen')
        .on('resize.chosen', function () {
            $('.chosen-select').each(function () {
                var $this = $(this);
                $this.next().css({'width': $this.parent().width()});
            })
        }).trigger('resize.chosen');
    //resize chosen on sidebar collapse/expand
    $(document).on('settings.ace.chosen', function (e, event_name, event_val) {
        if (event_name != 'sidebar_collapsed')
            return;
        $('.chosen-select').each(function () {
            var $this = $(this);
            $this.next().css({'width': $this.parent().width()});
        })
    });
    $('#chosen-multiple-style .btn').on('click', function (e) {
        var target = $(this).find('input[type=radio]');
        var which = parseInt(target.val());
        if (which == 2)
            $('#form-field-select-4').addClass('tag-input-style');
        else
            $('#form-field-select-4').removeClass('tag-input-style');
    });
}
$(".date-picker").inputmask("99/99/9999");
$('.number-float-2').number(true, 2, ',', '.');
$('.number-float-3').number(true, 3, ',', '.');
$('.number-int').number(true, 0, ',', '.');

function setUpLoading() {
    var t = $("body");
    $("#overlay").css({
        opacity: 0.5,
        top: t.offset().top,
        width: t.outerWidth(),
        height: t.outerHeight()
    });

    $("#img-load").css({
        top: (t.height() / 2),
        left: (t.width() / 2)
    });
}

function showLoading() {
    setUpLoading();
    $("#overlay").fadeIn();
}

function hideLoading() {
    $("#overlay").fadeOut();
}