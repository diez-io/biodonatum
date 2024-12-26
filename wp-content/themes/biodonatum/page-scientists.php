<?php
/* Template Name: Scientists Page */
?>
<? get_header(); ?>

        <main class="main">
            <section class="section pt-50">
                <div class="container">
                    <div class="breadcrumbs mb-40">
                        <a href="<?= home_url(); ?>" class="breadcrumbs__link">to index</a>
                        <span class="breadcrumbs__link">Our Scientists</span>
                    </div>
                    <div class="scientists">
                        <div class="head">
                            <h2 class="title">Our Scientists</h2>
                        </div>
                        <div class="island">
                            Our Scientific Board is represented by doctors, scientists and researchers in such fields as microbiology, immunology, genetics, metabolomics, gastroenterology, pediatrics, gynecology and cosmetology. They head clinics and laboratories, teach at world-renowned institutes and are authors of scientific journals and publications.
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
                                                'terms'    => $_SESSION['lang'],
                                            ],
                                        ],
                                        //'posts_per_page' => 10,
                                    );

                                    $loop = new WP_Query($args);

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
                                <div class="button button--wide scientists__load-more load-more-btn">
                                    LOAD MORE
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <? get_template_part('components/feedback'); ?>
        </main>

<? get_footer(); ?>
