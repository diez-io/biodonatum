<section class="section pt-80 ptm-60 pb-80 pbm-60">
    <div class="container">
        <div class="feedback">
            <div class="feedback__contacts">
                <div class="feedback__header">
                    <img class="feedback__logo" src="<?= get_template_directory_uri(); ?>/assets/images/feedback-logo.png" alt="">
                    <div class="feedback__titles">
                        <h2 class="feedback__title"><?= __('Contacts', 'static') ?></h2>
                        <h3 class="feedback__title">Distributor in Uzbekistan</h3>
                    </div>
                </div>
                <div class="feedback__info">
                    <p class="feedback__text text">
                        Company LLC “Aprel Nutrition”<br>
                        Republic of Uzbekistan, Tashkent, Mirzo-Ulugbek<br> District, Temur Malik Street, Building 3a
                    </p>
                    <div class="feedback__phones">
                        <div class="feedback__phones--icon">
                            <svg>
                                <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-phone"></use>
                            </svg>
                        </div>
                        <div class="feedback__links">
                            <a class="feedback__link" href="tel:+998 99-971-09-90">+998 99-971-09-90</a>
                            <a class="feedback__link" href="tel:+998 99-972-09-90">+998 99-972-09-90</a>
                        </div>
                    </div>
                    <div class="feedback__email">
                        <svg>
                            <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-email"></use>
                        </svg>
                        <a class="feedback__link" href="mailto:info@biodonatum.com">info@biodonatum.com</a>
                    </div>
                </div>
            </div>
            <div class="feedback__form">
                <h3 class="feedback__title"><?= __('Write to us', 'static') ?></h3>
                <?= get_cf7_form_by_title('message') ?>
            </div>
        </div>
    </div>
</section>