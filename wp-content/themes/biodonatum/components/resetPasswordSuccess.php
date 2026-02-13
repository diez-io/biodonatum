<div class="lost-password__container" data-success-for="lost-password__container">
    <div class="request-success">
        <div class="registration__success-svg">
            <svg>
                <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-success"></use>
            </svg>
        </div>
        <div class="registration__title"><?= get_static_content('password_has_been_reset') ?></div>
        <a href="<?= esc_url(biodonatum_url_with_lang(get_permalink(get_option('woocommerce_myaccount_page_id')))); ?>" class="button button--wide"><?= get_static_content('go_to_profile') ?></a>
    </div>
</div>
