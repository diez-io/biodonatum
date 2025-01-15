<div class="lost-password__container" data-success-for="lost-password__container">
    <div class="request-success">
        <div class="registration__success-svg">
            <svg>
                <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-success"></use>
            </svg>
        </div>
        <div class="registration__title"><?= __('Password has been reset successfully!', 'static') ?></div>
        <a href="<?= get_permalink( get_option('woocommerce_myaccount_page_id')) ?>" class="button button--wide"><?= __('Go to your profile page', 'static') ?></a>
    </div>
</div>
