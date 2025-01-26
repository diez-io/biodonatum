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
                    <a href="#product-detail__header" class="button"><?= get_static_content('buy_now') ?></a>
                    <div class="main-hero__circle container">
                    </div>
                    <div class="main-hero__circle__content">
                        <div>
                            <picture>
                                <img src="<?= get_template_directory_uri(); ?>/assets/images/index-slider-features-1.png" alt="">
                            </picture>
                            <p>
                                <?= get_static_content('teaser_1') ?>
                            </p>
                        </div>
                        <div>
                            <picture>
                                <img src="<?= get_template_directory_uri(); ?>/assets/images/index-slider-features-2.png" alt="">
                            </picture>
                            <p>
                                <?= get_static_content('teaser_2') ?>
                            </p>
                        </div>
                        <div>
                            <picture>
                                <img src="<?= get_template_directory_uri(); ?>/assets/images/index-slider-features-3.png" alt="">
                            </picture>
                            <p>
                                <?= get_static_content('teaser_3') ?>
                            </p>
                        </div>
                        <div>
                            <picture>
                                <img src="<?= get_template_directory_uri(); ?>/assets/images/index-slider-features-4.png" alt="">
                            </picture>
                            <p>
                                <?= get_static_content('teaser_4') ?>
                            </p>
                        </div>
                        <div>
                            <picture>
                                <img src="<?= get_template_directory_uri(); ?>/assets/images/index-slider-features-5.png" alt="">
                            </picture>
                            <p>
                                <?= get_static_content('teaser_5') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>