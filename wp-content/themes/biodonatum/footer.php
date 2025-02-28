<div class="scroll-up-btn">
    <svg class="socials__icon socials__icon--wa">
        <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-angle-rounded"></use>
    </svg>
</div>

<footer class="footer">
    <div class="container">
        <div class="footer__wrapper">
            <div class="footer__block">
                <? $language_slug = defined('CURRENT_LANGUAGE') ? CURRENT_LANGUAGE : ''; ?>
                <a href="<?= home_url("/$language_slug"); ?>" class="footer__logo">
                    <img src="<?= get_template_directory_uri(); ?>/assets/images/logo.png" alt="">
                </a>
            </div>
            <div class="footer__block">
                <nav class="footer__nav">
                    <a href="<?= home_url("/$language_slug" . parse_url(get_permalink(get_page_by_path('about')), PHP_URL_PATH)); ?>" class="footer__link"><?= get_static_content('about') ?></a>
                    <a href="<?= home_url("/$language_slug" . parse_url(get_permalink(get_page_by_path('scientists')), PHP_URL_PATH)); ?>" class="footer__link"><?= get_static_content('scientists') ?></a>
                    <a href="<?= home_url("/$language_slug" . parse_url(get_permalink(get_page_by_path('terms-of-sales')), PHP_URL_PATH)); ?>" class="footer__link"><?= get_static_content('terms_of_sales') ?></a>
                    <a href="<?= home_url("/$language_slug" . parse_url(get_permalink(get_page_by_path('refund_returns')), PHP_URL_PATH)); ?>" class="footer__link"><?= get_static_content('return_policy') ?></a>
                    <a href="<?= home_url("/$language_slug" . parse_url(get_permalink(get_page_by_path('delivery')), PHP_URL_PATH)); ?>" class="footer__link"><?= get_static_content('delivery_terms') ?></a>
                    <a href="<?= home_url("/$language_slug" . parse_url(get_permalink(get_page_by_path('loyalty-program')), PHP_URL_PATH)); ?>" class="footer__link"><?= get_static_content('loyalty_program') ?></a>
                    <a href="<?= home_url("/$language_slug" . parse_url(get_permalink(get_page_by_path('privacy-policy')), PHP_URL_PATH)); ?>" class="footer__link"><?= get_static_content('privacy_policy') ?></a>
                    <a href="<?= home_url("/$language_slug" . parse_url(get_permalink(get_page_by_path('vacancy')), PHP_URL_PATH)); ?>" class="footer__link"><?= get_static_content('vacancy') ?></a>
                    <a href="<?= home_url("/$language_slug" . parse_url(get_permalink(get_page_by_path('partners')), PHP_URL_PATH)); ?>" class="footer__link"><?= get_static_content('partners') ?></a>
                    <a href="<?= home_url("/$language_slug" . parse_url(get_permalink(get_page_by_path('partnership')), PHP_URL_PATH)); ?>" class="footer__link">Partnership</a>
                </nav>
            </div>
            <div class="footer__block">
                <div class="socials">
                    <a class="socials__link" href="https://www.instagram.com/biodonatum/" target="_blank">
                        <svg class="socials__icon">
                            <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-ig"></use>
                        </svg>
                    </a>
                    <a class="socials__link" href="https://www.facebook.com/profile.php?id=61556171075786" target="_blank">
                        <svg class="socials__icon socials__icon--fb">
                            <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-fb"></use>
                        </svg>
                    </a>
                    <a class="socials__link" href="https://www.youtube.com/channel/UCZXE6CdK9G1eWEG6TTGXWcQ" target="_blank">
                        <svg class="socials__icon">
                            <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-yt"></use>
                        </svg>
                    </a>
                    <a class="socials__link" href="https://api.whatsapp.com/send/?phone=33667007969&text=Hey%2C+%2ABiodonatum%2A%21+I+need+info+about+Biodonatum+https%3A%2F%2Fbiodonatum.com&type=phone_number&app_absent=0" target="_blank">
                        <svg class="socials__icon socials__icon--wa">
                            <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-wa"></use>
                        </svg>
                    </a>
                    <!--a class="socials__link" href="#">
                        <svg class="socials__icon">
                            <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-tt"></use>
                        </svg>
                    </a-->
                </div>
            </div>
        </div>
    </div>
</footer>
<aside class="footer__copyright"><?= get_static_content('bottom_line') ?></aside>

<style>
    .temp {
        padding: 20px 0;
        background-color: #FFFFFF;
    }

    .temp__wrapper {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 30px 40px;
    }

    .temp__link {
        transition: .2s linear;
    }

    .temp__link:hover {
        opacity: .7;
    }
</style>
<? wp_footer(); ?>
</body>

</html>