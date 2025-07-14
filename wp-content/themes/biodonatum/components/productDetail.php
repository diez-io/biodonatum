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
                    <div class="product-detail__header" id="product-detail__header">
                        <h2 class="title"><?= get_field($post_type_prefix . 'name', $advanced_product_id) ?></h2>
                        <? if ($isDetailedProductPage) : ?>
                            <div class="product-detail__product-amount">
                                <?= $woo_product->get_price_html() ?>
                            </div>
                        <? endif; ?>
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
                            <p class="text--semi-bold"><?= get_static_content('product_donation') ?></p>
                            <!--p class="title--extra-small">Buy <span class="text--blue">1 pack now</span></p-->
                            <? if(!$isDetailedProductPage) : ?>
                                <div class="type-animation">
                                    <div class="type-animation__strings">
                                        <p class="title--extra-small"><?= get_static_content('call_to_action_1') ?></p>
                                        <p class="title--extra-small"><span class="text--blue"><?= get_static_content('call_to_action_2') ?></span></p>
                                        <p class="title--extra-small"><span class="text--red"><?= get_static_content('call_to_action_3') ?></span></p>
                                    </div>
                                    <p class="title--extra-small"><?= get_static_content('call_to_action') ?> <span class="type-animation__typed"></span></p>
                                </div>
                            <? endif; ?>
                            <?
                                if ($isDetailedProductPage && $isVariable) :
                                    $variations = $woo_product->get_available_variations();
                                    $grouped_variations = [];
                                    $has_types = isset($variations[0]['attributes']['attribute_type']); ?>

                                    <? if (!$has_types) : ?>
                                        <div class="select-subscription-duration__label">
                                            <?= get_static_content('how_long_subscription') ?>
                                        </div>

                                        <? $grouped_variations['subscription'] = $variations; ?>
                                    <? else:
                                        foreach ($variations as $variation) {
                                            $type = $variation['attributes']['attribute_type'];
                                            $grouped_variations[$type][] = $variation;
                                        }
                                    endif;?>

                                    <?php
                                    // Find the first subscription variation for use in price and add-to-cart logic
                                    $first_subscription_variation = null;
                                    if (isset($grouped_variations['subscription']) && count($grouped_variations['subscription']) > 0) {
                                        $first_subscription_variation = $grouped_variations['subscription'][0];
                                    }
                                    ?>

                                    <? foreach ($grouped_variations as $type => $variations) : ?>
                                        <? if ($type === 'subscription') : ?>
                                            <div class="variation-type variation-type__single<?= $type === 'regular' ? ' variation-type__single--selected' : '' ?>" data-variation-type="<?= htmlspecialchars($type) ?>">
                                                <? if ($has_types) : ?>
                                                    <div><?= get_static_content("type_$type") ?></div>
                                                <? endif; ?>
                                                <div class="select-subscription-duration">
                                                    <div class="select-subscription-duration__selected">
                                                        <div class="select-subscription-duration__selected__title">
                                                            - <?= get_static_content('select_duration') ?> -
                                                        </div>
                                                        <div class="select-subscription-duration__selected__icon">
                                                            <svg>
                                                                <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-angle-rounded"></use>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <div class="select-subscription-duration__list" style="display:none;">
                                                        <? foreach ($variations as $variation) : ?>
                                                            <div
                                                                class="select-subscription-duration__option"
                                                                data-variation-id="<?= $variation['variation_id'] ?>"
                                                                data-price="<?= htmlspecialchars($variation['price_html'], ENT_QUOTES, 'UTF-8') ?>"
                                                            >
                                                                <?= get_static_content('months_' . $variation['attributes']['attribute_duration']) ?>
                                                            </div>
                                                        <? endforeach; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <? else : $variation = $variations[0]; ?>
                                            <div class="variation-type variation-type__single<?= $type === 'regular' ? ' variation-type__single--selected' : '' ?>" data-variation-type="<?= htmlspecialchars($type) ?>">
                                                <div class="variation-type__single-option"
                                                    data-variation-id="<?= $variation['variation_id'] ?>"
                                                    data-price="<?= htmlspecialchars($variation['price_html'], ENT_QUOTES, 'UTF-8') ?>"
                                                    tabindex="0">
                                                    <?= get_static_content("type_$type") ?>
                                                </div>
                                            </div>
                                        <? endif; ?>
                                    <? endforeach; ?>
                                <? endif; ?>
                        </article>
                        <? if ($isDetailedProductPage && $isVariable) : ?>
                            <div class="product-detail__product-price" style="display:none;" data-subscription-from-string="<?= htmlspecialchars(sprintf(get_static_content('from_s'), $first_subscription_variation['price_html']), ENT_QUOTES, 'UTF-8') ?>">
                                <?php if ($first_subscription_variation): ?>
                                    <span class="product-detail__product-price-from" data-subscription-from-price="<?= htmlspecialchars($first_subscription_variation['price_html'], ENT_QUOTES, 'UTF-8') ?>">
                                        <?= sprintf(get_static_content('from_s'), $first_subscription_variation['price_html']) ?>
                                    </span>
                                <?php else: ?>
                                    <?= $variations[0]['price_html'] ?>
                                <?php endif; ?>
                            </div>
                        <? endif; ?>
                        <div class="buttons<?= $isDetailedProductPage ? ' buttons__add-to-cart' : '' ?>">
                            <? if ($isDetailedProductPage) : ?>
                                <div class="quantity_panel">
                                    <div class="quantity_panel--minus noselect">-</div>
                                    <input type="text" value="1">
                                    <div class="quantity_panel--plus noselect">+</div>
                                </div>
                                <button class="button add-to-cart-button<?= $isVariable ? ' subscription-add-to-cart-button' : '' ?>" type="submit" data-product-id="<?= $product_id ?>"><?= get_static_content('add_to_cart') ?></button>
                                <!--button class="button button--green add-to-cart-button" data-product-id="<?= $product_id ?>"><?= get_static_content('buy_subscription') ?></button-->
                            <? else : ?>
                                <button class="button" type="submit" onclick="location.href='<?= the_permalink($advanced_product_id) ?>'"><?= get_static_content('buy_one_pack') ?></button>
                                <button class="button button--green" onclick="location.href='<?= the_permalink($subscription_product_id) ?>'"><?= get_static_content('buy_subscription') ?></button>
                            <? endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tabs product-detail__tabs" data-slider="tabs">
                <div thumbsSlider="" class="swiper tabs__header">
                    <div class="swiper-wrapper">
                        <button class="swiper-slide tabs__head"><?= get_static_content('product_detailed_description') ?></button>
                        <button class="swiper-slide tabs__head"><?= get_static_content('product_detailed_functions') ?></button>
                        <button class="swiper-slide tabs__head"><?= get_static_content('product_detailed_instructions') ?></button>
                        <button class="swiper-slide tabs__head"><?= get_static_content('product_detailed_composition') ?></button>
                        <button class="swiper-slide tabs__head"><?= get_static_content('product_detailed_calorie') ?></button>
                    </div>
                    <div class="teasers-pagination-wrapper">
                        <div><?= get_static_content('swipe_left_right') ?></div>
                        <div class="swiper-pagination desktop-hidden"></div>
                    </div>
                </div>
                <div class="tabs__body swiper">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide tabs__item">
                            <?= get_field($post_type_prefix . 'detailed_description_content', $advanced_product_id) ?>
                        </div>
                        <div class="swiper-slide tabs__item">
                            <?= get_field($post_type_prefix . 'detailed_functions_content', $advanced_product_id) ?>
                        </div>
                        <div class="swiper-slide tabs__item">
                            <?= get_field($post_type_prefix . 'detailed_instructions_content', $advanced_product_id) ?>
                        </div>
                        <div class="swiper-slide tabs__item">
                            <?= get_field($post_type_prefix . 'detailed_composition_content', $advanced_product_id) ?>
                        </div>
                        <div class="swiper-slide tabs__item product-detail__calory-info">
                            <table class="product-detail__calory-table">
                                <tbody>
                                    <td colspan="2"><?= get_field($post_type_prefix . 'detailed_calorie_table_title', $advanced_product_id) ?></td>
                                    <? $caloryTableRows = get_field($post_type_prefix . 'detailed_calorie_table', $advanced_product_id); ?>
                                    <? foreach ($caloryTableRows as $row) : ?>
                                        <tr>
                                            <th>
                                                <?= $row[$post_type_prefix . 'detailed_calorie_table_property'] ?>
                                            </th>
                                            <td>
                                                <?= $row[$post_type_prefix . 'detailed_calorie_table_value'] ?>
                                            </td>
                                        </tr>
                                    <? endforeach; ?>
                                </tbody>
                            </table>
                            <p><?= get_field($post_type_prefix . 'detailed_calorie_table_under', $advanced_product_id) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        <? endif; ?>
    </div>
</section>

<?
    if ($isDetailedProductPage) {
        get_template_part('components/addedToCartPopup');
    }
