<section class="section">
    <div class="container">
        <?
        $post_type = 'advanced_product';
        $post_type_prefix = $post_type . '_';
        $advanced_product_id = null;
        $isDetailedProductPage = !isset($args['woo_id']);

        if (!$isDetailedProductPage) {
            $product_id = $args['woo_id'];

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
                $woo_product = wc_get_product($product_id);
            }



            $queryArgs = [
                'post_type'  => $post_type,
                'meta_query' => [
                    [
                        'key'     => $post_type_prefix . 'woo_id',
                        'value'   => $args['subscription_woo_id'],
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
                $subscription_product_id = $query->posts[0]->ID;
            }
        }
        else {
            $advanced_product_id = get_the_ID();
            $product_id = get_field($post_type_prefix . 'woo_id');
            $woo_product = wc_get_product($product_id);

            $isVariable = $woo_product->get_type() === 'variable';
        }

        if ($advanced_product_id) : ?>
            <div class="product-detail">
                <div class="product-detail__left">
                    <div class="slider" data-slider="detail">
                        <div class="swiper">
                            <div class="swiper-wrapper">
                                <?
                                $images = get_field($post_type_prefix . 'images', $advanced_product_id);

                                foreach ($images as $image_row) : ?>
                                    <div class="swiper-slide">
                                        <picture>
                                            <? $image = $image_row[$post_type_prefix . 'images_item']; ?>
                                            <img class="product-swiper-img" src="<?= esc_url($image['url']); ?>" alt="<?= esc_attr($image['alt']); ?>">
                                        </picture>
                                    </div>
                                <? endforeach; ?>
                            </div>
                            <div class="slider__control">
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
                        </div>
                    </div>
                    <div class="slider" data-slider="detail-teasers" data-slides="5">
                        <div class="swiper">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide">
                                    <article class="article-feature">
                                        <picture>
                                            <img src="<?= get_template_directory_uri(); ?>/assets/images/index-slider-features-1.png" alt="">
                                        </picture>
                                        <p>
                                            <?= get_static_content('teaser_1') ?>
                                        </p>
                                    </article>
                                </div>
                                <div class="swiper-slide">
                                    <article class="article-feature">
                                        <picture>
                                            <img src="<?= get_template_directory_uri(); ?>/assets/images/index-slider-features-2.png" alt="">
                                        </picture>
                                        <p>
                                            <?= get_static_content('teaser_2') ?>
                                        </p>
                                    </article>
                                </div>
                                <div class="swiper-slide">
                                    <article class="article-feature">
                                        <picture>
                                            <img src="<?= get_template_directory_uri(); ?>/assets/images/index-slider-features-3.png" alt="">
                                        </picture>
                                        <p>
                                            <?= get_static_content('teaser_3') ?>
                                        </p>
                                    </article>
                                </div>
                                <div class="swiper-slide">
                                    <article class="article-feature">
                                        <picture>
                                            <img src="<?= get_template_directory_uri(); ?>/assets/images/index-slider-features-4.png" alt="">
                                        </picture>
                                        <p>
                                            <?= get_static_content('teaser_4') ?>
                                        </p>
                                    </article>
                                </div>
                                <div class="swiper-slide">
                                    <article class="article-feature">
                                        <picture>
                                            <img src="<?= get_template_directory_uri(); ?>/assets/images/index-slider-features-5.png" alt="">
                                        </picture>
                                        <p>
                                            <?= get_static_content('teaser_5') ?>
                                        </p>
                                    </article>
                                </div>
                            </div>
                            <div class="teasers-pagination-wrapper">
                                <div><?= get_static_content('swipe_left_right') ?></div>
                                <div class="swiper-pagination desktop-hidden"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="product-detail__right">
                    <div class="product-detail__header">
                        <h2 class="title"><?= get_field($post_type_prefix . 'name', $advanced_product_id) ?></h2>
                        <p><?= get_field($post_type_prefix . 'short_description', $advanced_product_id) ?></p>
                    </div>

                    <div class="product-detail__card">
                        <article class="article-product">
                            <h3 class="title--extra-small title--blue"><?= get_field($post_type_prefix . 'feature_title', $advanced_product_id) ?></h3>
                            <ul>
                                <?
                                $features = get_field($post_type_prefix . 'features_features', $advanced_product_id);

                                foreach ($features as $feature_row) :
                                    $feature = $feature_row[$post_type_prefix . 'features_features_item']; ?>
                                    <li><?= $feature ?></li>
                                <? endforeach; ?>
                            </ul>
                            <p class="text--semi-bold"><?= get_field($post_type_prefix . 'weight', $advanced_product_id) ?></p>
                            <p class="text--semi-bold"><?= get_field($post_type_prefix . 'donation', $advanced_product_id) ?></p>
                            <!--p class="title--extra-small">Buy <span class="text--blue">1 pack now</span></p-->
                            <p class="title--extra-small"><?= get_field($post_type_prefix . 'call_to_action', $advanced_product_id) ?></span></p>
                            <?
                                if ($isVariable) {
                                    $variations = $woo_product->get_available_variations();

                                    foreach ($variations as $variation) {
                                        error_log('<div>next variation: ' . '</div><br><br>');
                                        error_log(print_r($variation, true));

                                        echo '<div>next variation: ' . $variation['attributes']['attribute_duration'] . '</div>';
                                        echo $variation['display_regular_price'] . ' ' . $variation['display_price'];
                                    }
                                }
                            ?>
                        </article>
                        <div class="buttons">
                            <? if ($isDetailedProductPage) : ?>
                                <div class="quantity_panel">
                                    <div class="quantity_panel--minus">-</div>
                                    <input type="text" value="1">
                                    <div class="quantity_panel--plus">+</div>
                                </div>
                                <button class="button add-to-cart-button" type="submit" data-product-id="<?= $product_id ?>"><?= get_static_content('add_to_cart') ?></button>
                                <!--button class="button button--green add-to-cart-button" data-product-id="<?= $product_id ?>"><?= get_static_content('buy_subscription') ?></button-->
                            <? else : ?>
                                <button class="button" type="submit" onclick="location.href='<?= the_permalink($advanced_product_id) ?>'"><?= get_static_content('buy_one_pack') ?></button>
                                <button class="button button--green" onclick="location.href='<?= the_permalink($subscription_product_id) ?>'"><?= get_static_content('buy_subscription') ?></button>
                            <? endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tabs" data-tabs="" data-slider="tabs">
                <div class="swiper tabs__header">
                    <div class="swiper-wrapper">
                        <button class="tabs__head active" data-tabs-head="1"><?= get_field($post_type_prefix . 'detailed_description_name', $advanced_product_id) ?></button>
                        <button class="tabs__head" data-tabs-head="2"><?= get_field($post_type_prefix . 'detailed_functions_name', $advanced_product_id) ?></button>
                        <button class="tabs__head" data-tabs-head="3"><?= get_field($post_type_prefix . 'detailed_instructions_name', $advanced_product_id) ?></button>
                        <button class="tabs__head" data-tabs-head="4"><?= get_field($post_type_prefix . 'detailed_composition_name', $advanced_product_id) ?></button>
                        <button class="tabs__head" data-tabs-head="5"><?= get_field($post_type_prefix . 'detailed_calorie_name', $advanced_product_id) ?></button>
                    </div>
                </div>
                <div class="tabs__body">
                    <div class="tabs__item active" data-tabs-content="1">
                        <?= get_field($post_type_prefix . 'detailed_description_content', $advanced_product_id) ?>
                    </div>
                    <div class="tabs__item" data-tabs-content="2">
                        <?= get_field($post_type_prefix . 'detailed_functions_content', $advanced_product_id) ?>
                    </div>
                    <div class="tabs__item" data-tabs-content="3">
                        <?= get_field($post_type_prefix . 'detailed_instructions_content', $advanced_product_id) ?>
                    </div>
                    <div class="tabs__item" data-tabs-content="4">
                        <?= get_field($post_type_prefix . 'detailed_composition_content', $advanced_product_id) ?>
                    </div>
                    <div class="tabs__item" data-tabs-content="5">
                        <?= get_field($post_type_prefix . 'detailed_calorie_content', $advanced_product_id) ?>
                    </div>
                </div>
            </div>
        <? endif; ?>
    </div>
</section>