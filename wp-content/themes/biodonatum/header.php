<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?>"<?= $_SESSION['lang'] === 'ar' ? ' dir="rtl"' : '' ?>>

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= get_static_content('title') ?></title>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-20S32199Y4"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-20S32199Y4');
    </script>

    <? wp_head(); ?>
</head>

<body>
    <div class="modal-background" style="display:none;"></div>
    <aside class="pre-header"><?= get_static_content('top_line') ?></aside>
    <header class="header">
        <div class="container">
            <div class="header__wrapper">
                <div class="header__block">
                    <a href="/" class="logo">
                        <img src="<?= wp_get_attachment_url(get_static_content('logo')) ?>" alt="">
                    </a>
                </div>
                <div class="header__block">
                    <nav class="nav mob-hidden">
                        <? $language_slug = defined('CURRENT_LANGUAGE') ? CURRENT_LANGUAGE : ''; ?>

                        <a href="<?= home_url("/$language_slug"); ?>" class="nav__link"><?= get_static_content('home') ?></a>
                        <a href="<?= home_url("/$language_slug" . parse_url(get_permalink(get_page_by_path('about')), PHP_URL_PATH)); ?>" class="nav__link"><?= get_static_content('about') ?></a>
                        <a href="<?= home_url("/$language_slug" . parse_url(get_permalink(get_page_by_path('blog')), PHP_URL_PATH)); ?>" class="nav__link"><?= get_static_content('blog') ?></a>
                        <a href="<?= home_url("/$language_slug" . parse_url(get_permalink(get_page_by_path('scientists')), PHP_URL_PATH)); ?>" class="nav__link"><?= get_static_content('scientists') ?></a>
                        <a href="<?= esc_url(wc_get_page_permalink( 'shop' )) ?>" class="nav__link"><?= get_static_content('shop') ?></a>
                        <a href="<?= home_url("/$language_slug" . parse_url(get_permalink(get_page_by_path('reviews')), PHP_URL_PATH)); ?>" class="nav__link"><?= get_static_content('reviews') ?></a>
                        <a href="<?= home_url("/$language_slug" . parse_url(get_permalink(get_page_by_path('faq')), PHP_URL_PATH)); ?>" class="nav__link"><?= get_static_content('faq') ?></a>
                        <a href="<?= home_url("/$language_slug" . parse_url(get_permalink(get_page_by_path('contacts')), PHP_URL_PATH)); ?>" class="nav__link"><?= get_static_content('contacts') ?></a>
                    </nav>
                </div>
                <div class="header__block header__block__socials-and-personal">
                    <div class="socials mob-hidden">
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
                    </div>
                    <div class="header__block__divider mob-hidden"></div>
                    <div class="header__personal">
                        <div class="header__language">
                            <div class="header__element">
                                <span><?= $_SESSION['lang'] ?></span>
                            </div>
                            <div class="menu">
                                <? global $supported_languages; ?>
                                <? foreach ($supported_languages as $key => $name) : ?>
                                    <form method="POST" action="">
                                        <input type="hidden" name="lang" value="<?= $key ?>">
                                        <button type="submit" class="menu__item"><?= $name ?></button>
                                    </form>
                                <? endforeach; ?>
                            </div>
                        </div>
                        <div data-url="<?= get_permalink( get_option('woocommerce_myaccount_page_id')) ?>" <?= is_user_logged_in() ? 'logged-in' : '' ?> class="header__user">
                            <div class="header__element">
                                <svg class="icon">
                                    <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-person"></use>
                                </svg>
                            </div>
                        </div>
                        <a href="<?= esc_url(wc_get_cart_url()); ?>" class="header__cart">
                            <div class="header__element">
                                <svg class="icon">
                                    <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-cart"></use>
                                </svg>
                            </div>
                            <? $cart_count = get_cart_count(); ?>
                            <div class="cart_count" <?= $cart_count ? '' : 'style="display:none;"' ?>>
                                <?= $cart_count ?>
                            </div>
                        </a>
                    </div>
                    <div class="burger" data-burger>
                        <div class="burger__item"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mobile-menu" data-menu>
            <ul class="mobile-menu__list">
                <li class="mobile-menu__item">
                    <a href="<?= home_url("/$language_slug"); ?>" class="mobile-menu__link">
                        <?= get_static_content('home') ?>
                    </a>
                </li>
                <li class="mobile-menu__item">
                    <a href="<?= home_url("/$language_slug" . parse_url(get_permalink(get_page_by_path('about')), PHP_URL_PATH)); ?>" class="mobile-menu__link">
                        <?= get_static_content('about') ?>
                    </a>
                </li>
                <li class="mobile-menu__item">
                    <a href="<?= home_url("/$language_slug" . parse_url(get_permalink(get_page_by_path('blog')), PHP_URL_PATH)); ?>" class="mobile-menu__link">
                        <?= get_static_content('blog') ?>
                    </a>
                </li>
                <li class="mobile-menu__item">
                    <a href="<?= home_url("/$language_slug" . parse_url(get_permalink(get_page_by_path('scientists')), PHP_URL_PATH)); ?>" class="mobile-menu__link">
                        <?= get_static_content('scientists') ?>
                    </a>
                </li>
                <li class="mobile-menu__item">
                    <a href="<?= esc_url(wc_get_page_permalink( 'shop' )) ?>" class="mobile-menu__link">
                        <?= get_static_content('shop') ?>
                    </a>
                </li>
                <li class="mobile-menu__item">
                    <a href="<?= home_url("/$language_slug" . parse_url(get_permalink(get_page_by_path('reviews')), PHP_URL_PATH)); ?>" class="mobile-menu__link">
                        <?= get_static_content('reviews') ?>
                    </a>
                </li>
                <li class="mobile-menu__item">
                    <a href="<?= home_url("/$language_slug" . parse_url(get_permalink(get_page_by_path('faq')), PHP_URL_PATH)); ?>" class="mobile-menu__link">
                        <?= get_static_content('faq') ?>
                    </a>
                </li>
                <li class="mobile-menu__item">
                    <a href="<?= home_url("/$language_slug" . parse_url(get_permalink(get_page_by_path('contacts')), PHP_URL_PATH)); ?>" class="mobile-menu__link">
                        <?= get_static_content('contacts') ?>
                    </a>
                </li>
            </ul>
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
            </div>

        </div>
        <!--        <button class="button button&#45;&#45;icon button&#45;&#45;mob-wide">-->
        <!--            <svg class="button__icon">-->
        <!--                <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-person"></use>-->
        <!--            </svg>-->
        <!--            Личный кабинет-->
        <!--        </button>-->
        </div>
        <? get_template_part('components/login'); ?>
    </header>
