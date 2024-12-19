<section class="section section--bg section--hero" style="background-image: url('<?= get_template_directory_uri(); ?>/assets/images/main-hero-bg.jpg')">
    <div class="container">
        <div class="columns">
            <div class="columns__col-12 columns__col-mob-4">
                <div class="main-hero">
                    <h1 class="main-hero__title">
                        <?= get_static_content('main_hero_title') ?>
                    </h1>
                    <p class="text">
                        <?= get_static_content('main_hero_text') ?>
                    </p>
                    <a href="#" class="button"><?= get_static_content('buy_now') ?></a>
                    <div class="main-hero__circle container">
                    </div>
                    <div class="main-hero__circle__content">
                        <div>
                            <picture>
                                <img src="<?= get_template_directory_uri(); ?>/assets/images/index-slider-features-1.png" alt="">
                            </picture>
                            <p>
                                The biotechnological production cycle is 2 years
                            </p>
                        </div>
                        <div>
                            <picture>
                                <img src="<?= get_template_directory_uri(); ?>/assets/images/index-slider-features-2.png" alt="">
                            </picture>
                            <p>
                                Gluten free
                            </p>
                        </div>
                        <div>
                            <picture>
                                <img src="<?= get_template_directory_uri(); ?>/assets/images/index-slider-features-3.png" alt="">
                            </picture>
                            <p>
                                Lactose free
                            </p>
                        </div>
                        <div>
                            <picture>
                                <img src="<?= get_template_directory_uri(); ?>/assets/images/index-slider-features-4.png" alt="">
                            </picture>
                            <p>
                                Mushrooms and yeast free
                            </p>
                        </div>
                        <div>
                            <picture>
                                <img src="<?= get_template_directory_uri(); ?>/assets/images/index-slider-features-5.png" alt="">
                            </picture>
                            <p>
                                Suitable for diabetics
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>