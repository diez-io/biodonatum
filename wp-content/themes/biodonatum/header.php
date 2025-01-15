<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= __('Biodonatum', 'static') ?></title>
    <? wp_head(); ?>
</head>

<body>
    <div class="modal-background" style="display:none;"></div>
    <aside class="pre-header"><?= __('top_line', 'static') ?></aside>
    <header class="header">
        <div class="container">
            <div class="header__wrapper">
                <div class="header__block">
                    <a href="/" class="logo">
                        <img src="<?= get_template_directory_uri(); ?>/assets/images/logo.png" alt="">
                    </a>
                </div>
                <div class="header__block">
                    <nav class="nav mob-hidden">
                        <a href="#" class="nav__link"><?= __('Home', 'static') ?></a>
                        <a href="#" class="nav__link"><?= __('About', 'static') ?></a>
                        <a href="#" class="nav__link"><?= __('Science', 'static') ?></a>
                        <a href="#" class="nav__link"><?= __('Scientists', 'static') ?></a>
                        <a href="#" class="nav__link"><?= __('Shop', 'static') ?></a>
                        <a href="#" class="nav__link"><?= __('FAQ', 'static') ?></a>
                        <a href="#" class="nav__link"><?= __('Contacts', 'static') ?></a>
                    </nav>
                </div>
                <div class="header__block header__block--no-gap">
                    <div class="socials mob-hidden">
                        <a class="socials__link" href="#">
                            <svg class="socials__icon">
                                <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-ig"></use>
                            </svg>
                        </a>
                        <a class="socials__link" href="#">
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
                    </div>
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
                    <a href="#" class="mobile-menu__link">
                        <?= __('Home', 'static') ?>
                    </a>
                </li>
                <li class="mobile-menu__item">
                    <a href="#" class="mobile-menu__link">
                        <?= __('About', 'static') ?>
                    </a>
                </li>
                <li class="mobile-menu__item">
                    <a href="#" class="mobile-menu__link">
                        <?= __('Science', 'static') ?>
                    </a>
                </li>
                <li class="mobile-menu__item">
                    <a href="#" class="mobile-menu__link">
                        <?= __('Scientists', 'static') ?>
                    </a>
                </li>
                <li class="mobile-menu__item">
                    <a href="#" class="mobile-menu__link">
                        <?= __('Shop', 'static') ?>
                    </a>
                </li>
                <li class="mobile-menu__item">
                    <a href="#" class="mobile-menu__link">
                        <?= __('FAQ', 'static') ?>
                    </a>
                </li>
                <li class="mobile-menu__item">
                    <a href="#" class="mobile-menu__link">
                        <?= __('Contacts', 'static') ?>
                    </a>
                </li>
            </ul>
            <div class="socials">
                <a class="socials__link" href="#">
                    <svg class="socials__icon">
                        <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-ig"></use>
                    </svg>
                </a>
                <a class="socials__link" href="#">
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
