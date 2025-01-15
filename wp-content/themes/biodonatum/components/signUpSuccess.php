<div class="request-success" data-success-for="registration--registration">
    <div class="registration__title"><?= __("You've been signed up successfully!", 'static') ?></div>
    <div class="registration__input--lable"><?= __("Password create link has been sent to the provided email address.", 'static') ?></div>
    <div class="registration__title"><?= $args['email'] ?></div>
    <a href="<?= get_permalink( get_option('woocommerce_myaccount_page_id')) ?>" class="button button--wide"><?= __('Go to your profile page', 'static') ?></a>
</div>
