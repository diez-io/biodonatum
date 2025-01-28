<?php /* Template Name: Shop Page */ ?>

<? get_header(); ?>

<main class="main">
    <section class="section pt-50">
        <div class="container">
            <div class="breadcrumbs mb-40">
                <a href="<?= home_url(); ?>" class="breadcrumbs__link"><?= get_static_content('home') ?></a>
                <span class="breadcrumbs__link"><?= get_static_content('shop') ?></span>
            </div>
            <div class="shop">
                <div class="head">
                    <h2 class="title"><?= get_static_content('shop') ?></h2>
                </div>
                <div class="shop__cards load-more-items">
                    <?
                    $args = [
                        'limit' => -1, // -1 for all products
                        'orderby' => 'date',
                        'order' => 'DESC',
                        'return' => 'ids', // Return product IDs only for better performance
                    ];

                    $query = new WC_Product_Query($args);
                    $products = $query->get_products();

                    if (!empty($products)) {
                        $numberOfProducts = 0;

                        foreach ($products as $product_id) {
                            $product = wc_get_product($product_id);
                            $post_type = 'advanced_product';
                            $post_type_prefix = $post_type . '_';

                            $queryArgs = [
                                'post_type'  => $post_type,
                                'meta_query' => [
                                    [
                                        'key'     => $post_type_prefix . 'woo_id',
                                        'value'   => $product_id,
                                        'compare' => '='
                                    ],
                                ],
                                'tax_query' => [
                                    [
                                        'taxonomy' => 'taxonomy_language',
                                        'field'    => 'slug',
                                        'terms'    => $_SESSION['lang'],
                                    ],
                                ],
                            ];

                            $query = new WP_Query($queryArgs);

                            if ($query->have_posts()) {
                                $advanced_product_id = $query->posts[0]->ID;
                                $numberOfProducts++;

                                ?>
                                <div class="card">
                                    <div class="slider" data-slider="detail">
                                        <div class="swiper">
                                            <div class="swiper-wrapper">
                                                <?
                                                $images = get_field($post_type_prefix . 'images', $advanced_product_id);

                                                foreach ($images as $image_row) : ?>
                                                    <div class="swiper-slide">
                                                        <picture>
                                                            <? $image = $image_row[$post_type_prefix . 'images_item']; ?>
                                                            <img src="<?= esc_url($image['url']); ?>" alt="<?= esc_attr($image['alt']); ?>">
                                                        </picture>
                                                    </div>
                                                <? endforeach; ?>
                                            </div>
                                            <div class="slider__control">
                                                <div class="slider__btn slider__btn--prev">
                                                    <svg class="slider__icon">
                                                        <use xlink:href="./assets/sprite.svg#icon-chevrone"></use>
                                                    </svg>
                                                </div>
                                                <div class="slider__btn slider__btn--next">
                                                    <svg class="slider__icon">
                                                        <use xlink:href="./assets/sprite.svg#icon-chevrone"></use>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="swiper-pagination"></div>
                                        </div>
                                    </div>
                                    <div class="card__text">
                                        <div class="card__text-title">
                                            <?= get_field($post_type_prefix . 'name', $advanced_product_id) ?>
                                        </div>
                                        <div class="card__text-description">
                                            <?= get_field($post_type_prefix . 'short_description', $advanced_product_id) ?>
                                        </div>
                                        <div class="card__price">
                                            <div class="card__price__title"><?= get_static_content('price') ?>:</div>
                                            <div class="card__price__price"><?= $product->get_price_html() ?></div>
                                        </div>
                                        <a href="<?= the_permalink($advanced_product_id) ?>" class="card__buy-btn">
                                            <?= get_static_content('buy') ?>
                                        </a>
                                    </div>
                                </div>
                                <?
                            }
                        }
                    }
                    ?>
                </div>
                <? if ($numberOfProducts > 4) : ?>
                    <div class="button shop__load-more load-more-btn">
                        <?= get_static_content('load_more') ?>
                    </div>
                <? endif; ?>
            </div>
        </div>
    </section>
    <? get_template_part('components/feedback'); ?>
</main>

<? get_footer(); ?>
