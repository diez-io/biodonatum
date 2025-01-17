<div class="request-success" data-success-for="registration--reset_password">
    <div class="registration__back">
        <svg>
            <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-vector"></use>
        </svg>
    </div>
    <div class="registration__title"><?= get_static_content('success') ?></div>
    <div class="registration__input--lable"><?= get_static_content('password_reset_link_has_been_sent') ?></div>
    <div class="registration__title"><?= $args['email'] ?></div>
</div>
