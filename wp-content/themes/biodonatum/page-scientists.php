<?php
/* Template Name: Scientists Page */
?>
<? get_header(); ?>

        <main class="main">
            <section class="section pt-50">
                <div class="container">
                    <div class="breadcrumbs mb-40">
                        <a href="<?= esc_url(biodonatum_url_with_lang(home_url('/'))); ?>" class="breadcrumbs__link"><?= get_static_content('home') ?></a>
                        <span class="breadcrumbs__link"><?= get_static_content('scientists') ?></span>
                    </div>
                    <div class="scientists">
                        <div class="head">
                            <h2 class="title"><?= get_static_content('scientists_title') ?></h2>
                        </div>
                        <div class="island board">
                            <?= get_static_content('scientists_text_1') ?>
                            <?= get_static_content('scientists_text_2') ?>
                        </div>
                        <div class="slider" data-slider="scientists">
                            <div class="swiper">
                                <div class="swiper-wrapper load-more-items">
                                    <? $args = array(
                                        'post_type' => 'scientist',
                                        'tax_query' => [
                                            [
                                                'taxonomy' => 'taxonomy_language',
                                                'field'    => 'slug',
                                                'terms'    => function_exists('get_current_language') ? get_current_language() : 'en',
                                            ],
                                        ],
                                        'posts_per_page' => 100,
                                    );

                                    $loop = new WP_Query($args);
                                    $numberOfScientists = $loop->found_posts;

                                    if ($loop->have_posts()):
                                        while ($loop->have_posts()):
                                            $loop->the_post(); ?>

                                            <div class="swiper-slide card">
                                                <picture>
                                                    <? $card_image = get_field('scientist_photo'); ?>
                                                    <img src="<?= esc_url($card_image['url']) ?>" alt="<?= esc_attr($card_image['alt']) ?>">
                                                </picture>
                                                <div class="card__text">
                                                    <div class="card__text-title">
                                                        <?= esc_html(get_field('scientist_name')); ?>
                                                    </div>
                                                    <div class="card__text-description">
                                                        <?= get_field('scientist_description') ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endwhile;

                                        wp_reset_postdata();
                                    endif;
                                    ?>
                                </div>
                                <div class="slider__control mob-hidden">
                                    <div class="slider__btn slider__btn--prev">
                                        <svg class="slider__icon">
                                            <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-chevrone"></use>
                                        </svg>
                                    </div>
                                    <div class="slider__btn slider__btn--next">
                                        <svg class="slider__icon">
                                            <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-chevrone"></use>
                                        </svg>
                                    </div>
                                </div>
                                <div class="swiper-pagination"></div>
                                <? if ($numberOfScientists > 4) : ?>
                                    <div class="button button--wide scientists__load-more load-more-btn">
                                        <?= get_static_content('load_more') ?>
                                    </div>
                                <? endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <? get_template_part('components/feedback'); ?>
        </main>

<? get_footer(); ?>
