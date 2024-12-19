<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Biodonatum</title>
    <? wp_head(); ?>
</head>

<body>
    <aside class="pre-header"><?= get_static_content('top_line') ?></aside>
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
                        <a href="#" class="nav__link"><?= get_static_content('home') ?></a>
                        <a href="#" class="nav__link"><?= get_static_content('about') ?></a>
                        <a href="#" class="nav__link"><?= get_static_content('science') ?></a>
                        <a href="#" class="nav__link"><?= get_static_content('scientists') ?></a>
                        <a href="#" class="nav__link"><?= get_static_content('shop') ?></a>
                        <a href="#" class="nav__link"><?= get_static_content('faq') ?></a>
                        <a href="#" class="nav__link"><?= get_static_content('contacts') ?></a>
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
                        <div class="header__user">
                            <div class="header__element">
                                <svg class="icon">
                                    <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-person"></use>
                                </svg>
                            </div>
                        </div>
                        <div class="header__cart">
                            <div class="header__element">
                                <svg class="icon">
                                    <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-cart"></use>
                                </svg>
                            </div>
                        </div>
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
                        <?= get_static_content('home') ?>
                    </a>
                </li>
                <li class="mobile-menu__item">
                    <a href="#" class="mobile-menu__link">
                        <?= get_static_content('about') ?>
                    </a>
                </li>
                <li class="mobile-menu__item">
                    <a href="#" class="mobile-menu__link">
                        <?= get_static_content('science') ?>
                    </a>
                </li>
                <li class="mobile-menu__item">
                    <a href="#" class="mobile-menu__link">
                        <?= get_static_content('scientists') ?>
                    </a>
                </li>
                <li class="mobile-menu__item">
                    <a href="#" class="mobile-menu__link">
                        <?= get_static_content('shop') ?>
                    </a>
                </li>
                <li class="mobile-menu__item">
                    <a href="#" class="mobile-menu__link">
                        <?= get_static_content('faq') ?>
                    </a>
                </li>
                <li class="mobile-menu__item">
                    <a href="#" class="mobile-menu__link">
                        <?= get_static_content('contacts') ?>
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
    </header>