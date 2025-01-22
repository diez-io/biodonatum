<footer class="footer">
    <div class="container">
        <div class="footer__wrapper">
            <div class="footer__block">
                <a href="#" class="footer__logo">
                    <img src="<?= get_template_directory_uri(); ?>/assets/images/logo.png" alt="">
                </a>
            </div>
            <div class="footer__block">
                <nav class="footer__nav">
                    <? $language_slug = defined('CURRENT_LANGUAGE') ? CURRENT_LANGUAGE : ''; ?>

                    <a href="<?= home_url("/$language_slug" . parse_url(get_permalink(get_page_by_path('about')), PHP_URL_PATH)); ?>" class="footer__link"><?= get_static_content('about') ?></a>
                    <a href="<?= home_url("/$language_slug" . parse_url(get_permalink(get_page_by_path('scientists')), PHP_URL_PATH)); ?>" class="footer__link"><?= get_static_content('scientists') ?></a>
                    <a style="text-decoration: line-through red;" href="#" class="footer__link"><?= get_static_content('terms_of_sales') ?></a>
                    <a style="text-decoration: line-through red;" href="#" class="footer__link"><?= get_static_content('return_policy') ?></a>
                    <a href="<?= home_url("/$language_slug" . parse_url(get_permalink(get_page_by_path('delivery')), PHP_URL_PATH)); ?>" class="footer__link"><?= get_static_content('delivery_terms') ?></a>
                    <a style="text-decoration: line-through red;" href="#" class="footer__link"><?= get_static_content('loyalty_program') ?></a>
                    <a style="text-decoration: line-through red;" href="#" class="footer__link"><?= get_static_content('privacy_policy') ?></a>
                    <a href="<?= home_url("/$language_slug" . parse_url(get_permalink(get_page_by_path('vacancy')), PHP_URL_PATH)); ?>" class="footer__link"><?= get_static_content('vacancy') ?></a>
                </nav>
            </div>
            <div class="footer__block">
                <div class="socials">
                    <a class="socials__link" href="#">
                        <svg class="socials__icon">
                            <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-ig"></use>
                        </svg>
                    </a>
                    <a class="socials__link socials__link--fb" href="#">
                        <svg class="socials__icon socials__icon--fb">
                            <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-fb"></use>
                        </svg>
                    </a>
                    <a class="socials__link" href="#">
                        <svg class="socials__icon">
                            <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-yt"></use>
                        </svg>
                    </a>
                    <a class="socials__link" href="#">
                        <svg class="socials__icon socials__icon--wa">
                            <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-wa"></use>
                        </svg>
                    </a>
                    <a class="socials__link" href="#">
                        <svg class="socials__icon">
                            <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-tt"></use>
                        </svg>
                    </a>
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