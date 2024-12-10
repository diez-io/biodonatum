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
                    <a href="#" class="footer__link">About us</a>
                    <a href="#" class="footer__link">Science</a>
                    <a href="#" class="footer__link">Scientists</a>
                    <a href="#" class="footer__link">Terms of sales</a>
                    <a href="#" class="footer__link">Return Policy</a>
                    <a href="#" class="footer__link">Delivery terms</a>
                    <a href="#" class="footer__link">Loyalty program</a>
                    <a href="#" class="footer__link">Privacy Policy</a>
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
<aside class="footer__copyright">1982 Microbiotic Biodonatum</aside>

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
            <a href="<?= home_url(); ?>" class="temp__link">Главная</a>
            <a href="<?= get_permalink(get_page_by_path('faq')); ?>" class="temp__link">FAQ</a>
            <a href="<?= get_permalink(get_page_by_path('reviews')); ?>" class="temp__link">Отзывы</a>
            <a href="<?= get_permalink(get_page_by_path('delivery')); ?>" class="temp__link">Доставка</a>
            <a href="<?= get_permalink(get_page_by_path('scientists')); ?>" class="temp__link">Ученые</a>
            <a href="<?= get_permalink(get_page_by_path('cart')); ?>" class="temp__link"
                style="text-decoration-line: line-through;">Оформление</a>
            <a href="<?= get_permalink(get_page_by_path('blog')); ?>" class="temp__link">Блог</a>
            <a href="<?= get_permalink(get_page_by_path('contacts')); ?>" class="temp__link">Контакты</a>
            <a href="<?= get_permalink(get_page_by_path('vacancy')); ?>" class="temp__link">Вакансии</a>
            <a href="<?= get_permalink(get_page_by_path('product')); ?>" class="temp__link">Товар</a>
            <a href="<?= get_permalink(get_page_by_path('about')); ?>" class="temp__link">О нас</a>
        </div>
    </div>
</div>
<? wp_footer(); ?>
</body>

</html>