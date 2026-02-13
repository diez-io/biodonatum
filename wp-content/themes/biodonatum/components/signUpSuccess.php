<div class="request-success" data-success-for="registration--registration">
    <div class="registration__title"><?= get_static_content('signed_up_successfully') ?></div>
    <div class="registration__input--lable"><?= get_static_content('password_create_link_has_been_sent') ?></div>
    <div class="registration__title"><?= $args['email'] ?></div>
    <a href="<?= esc_url(biodonatum_url_with_lang(get_permalink(get_option('woocommerce_myaccount_page_id')))); ?>" class="button button--wide"><?= get_static_content('go_to_profile') ?></a>
</div>
