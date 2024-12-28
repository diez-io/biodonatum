jQuery(function ($) {
    $('.header__user').on('click', function() {
        if ($(this).is('[logged-in]')) {
            location.href = $(this).data('url');
        }
        else {
            $('.modal-background').toggle();
            $('.registration:not(.lost-password)').toggle();
        }
    });

    $('.modal-background').on('click', function() {
        $('.registration').hide();
        $(this).hide();
    });

    $('.registration__btns--forgot').on('click', function() {
        $('.registration--login').hide();
        $('.registration--reset_password').show();
    });

    $('.registration__new-user__create').on('click', function() {
        $('.registration--login').hide();
        $('.registration--registration').show();
    });

    $('.registration__back').on('click', function() {
        $(this).parent().hide();
        $('.registration--login').show();
    });
});
