<section class="section pt-110 pb-110">
    <div class="container">
        <div class="slider slider--partners" data-slider="partners" data-slides="6">
            <div class="slider__header">
                <h2 class="title">
                    <?= __('Partners', 'static') ?>
                </h2>
            </div>
            <div class="swiper">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <a href="#">
                            <picture>
                                <img src="<?= get_template_directory_uri(); ?>/assets/images/wedding-logo.png">
                            </picture>
                        </a>
                    </div>
                    <div class="swiper-slide">
                        <a href="#">
                            <picture>
                                <img src="<?= get_template_directory_uri(); ?>/assets/images/alphega-logo.png">
                            </picture>
                        </a>
                    </div>
                    <div class="swiper-slide">
                        <a href="#">
                            <picture>
                                <img src="<?= get_template_directory_uri(); ?>/assets/images/vogue-logo.png">
                            </picture>
                        </a>
                    </div>
                    <div class="swiper-slide">
                        <a href="#">
                            <picture>
                                <img src="<?= get_template_directory_uri(); ?>/assets/images/sophia-logo.png">
                            </picture>
                        </a>
                    </div>
                    <div class="swiper-slide">
                        <a href="#">
                            <picture>
                                <img src="<?= get_template_directory_uri(); ?>/assets/images/BS-logo.png">
                            </picture>
                        </a>
                    </div>
                    <div class="swiper-slide">
                        <a href="#">
                            <picture>
                                <img src="<?= get_template_directory_uri(); ?>/assets/images/mens-health-logo.png">
                            </picture>
                        </a>
                    </div>
                </div>
                <div class="slider__control mob-hidden">
                    <div class="slider__btn slider__btn--blue slider__btn--prev">
                        <svg class="slider__icon">
                            <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-chevrone"></use>
                        </svg>
                    </div>
                    <div class="slider__btn slider__btn--blue slider__btn--next">
                        <svg class="slider__icon">
                            <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-chevrone"></use>
                        </svg>
                    </div>
                </div>
                <div class="swiper-pagination desktop-hidden"></div>
            </div>
        </div>

    </div>
</section>