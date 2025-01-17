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
                    <a href="#" class="footer__link"><?= get_static_content('about') ?></a>
                    <a href="#" class="footer__link"><?= get_static_content('science') ?></a>
                    <a href="#" class="footer__link"><?= get_static_content('scientists') ?></a>
                    <a href="#" class="footer__link"><?= get_static_content('terms_of_sales') ?></a>
                    <a href="#" class="footer__link"><?= get_static_content('return_policy') ?></a>
                    <a href="#" class="footer__link"><?= get_static_content('delivery_terms') ?></a>
                    <a href="#" class="footer__link"><?= get_static_content('loyalty_program') ?></a>
                    <a href="#" class="footer__link"><?= get_static_content('privacy_policy') ?></a>
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
<div class="temp">
    <div class="container">
        <div class="temp__wrapper">
            <? $language_slug = defined('CURRENT_LANGUAGE') ? CURRENT_LANGUAGE : ''; ?>

            <a href="<?= home_url("/$language_slug"); ?>" class="temp__link">Главная</a>
            <a href="<?= home_url("/$language_slug" . parse_url(get_permalink(get_page_by_path('faq')), PHP_URL_PATH)); ?>" class="temp__link">FAQ</a>
            <a href="<?= home_url("/$language_slug" . parse_url(get_permalink(get_page_by_path('reviews')), PHP_URL_PATH)); ?>" class="temp__link">Отзывы</a>
            <a href="<?= home_url("/$language_slug" . parse_url(get_permalink(get_page_by_path('delivery')), PHP_URL_PATH)); ?>" class="temp__link">Доставка</a>
            <a href="<?= home_url("/$language_slug" . parse_url(get_permalink(get_page_by_path('scientists')), PHP_URL_PATH)); ?>" class="temp__link">Ученые</a>
            <a href="<?= home_url("/$language_slug" . parse_url(get_permalink(get_page_by_path('blog')), PHP_URL_PATH)); ?>" class="temp__link">Блог</a>
            <a href="<?= home_url("/$language_slug" . parse_url(get_permalink(get_page_by_path('contacts')), PHP_URL_PATH)); ?>" class="temp__link">Контакты</a>
            <a href="<?= home_url("/$language_slug" . parse_url(get_permalink(get_page_by_path('vacancy')), PHP_URL_PATH)); ?>" class="temp__link">Вакансии</a>
            <a href="<?= home_url("/$language_slug" . parse_url(get_permalink(get_page_by_path('about')), PHP_URL_PATH)); ?>" class="temp__link">О нас</a>
        </div>
    </div>
</div>
<? wp_footer(); ?>
</body>

</html>