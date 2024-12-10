<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Biodonatum</title>
    <? wp_head(); ?>
</head>

<body>
    <aside class="pre-header">ÉCONOMISEZ 10 % SUR VOTRE PREMIÈRE COMMANDE. Entrez le code HALLO10 à la caisse pour
        10 % de
        réduction sur votre première commande.</aside>
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
                        <a href="#" class="nav__link">Home</a>
                        <a href="#" class="nav__link">About</a>
                        <a href="#" class="nav__link">Science</a>
                        <a href="#" class="nav__link">Scientists</a>
                        <a href="#" class="nav__link">Shop</a>
                        <a href="#" class="nav__link">FAQ</a>
                        <a href="#" class="nav__link">Contacts</a>
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
                                <span>EN</span>
                            </div>
                            <div class="menu">
                                <a href="#" class="menu__item">English</a>
                                <a href="#" class="menu__item">French</a>
                                <a href="#" class="menu__item">Russian</a>
                                <a href="#" class="menu__item">German</a>
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
                        Home
                    </a>
                </li>
                <li class="mobile-menu__item">
                    <a href="#" class="mobile-menu__link">
                        About
                    </a>
                </li>
                <li class="mobile-menu__item">
                    <a href="#" class="mobile-menu__link">
                        Science
                    </a>
                </li>
                <li class="mobile-menu__item">
                    <a href="#" class="mobile-menu__link">
                        Scientists
                    </a>
                </li>
                <li class="mobile-menu__item">
                    <a href="#" class="mobile-menu__link">
                        Shop
                    </a>
                </li>
                <li class="mobile-menu__item">
                    <a href="#" class="mobile-menu__link">
                        FAQ
                    </a>
                </li>
                <li class="mobile-menu__item">
                    <a href="#" class="mobile-menu__link">
                        Contacts
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