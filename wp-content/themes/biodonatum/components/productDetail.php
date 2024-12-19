<section class="section">
    <div class="container">
        <?
        $product_id = 210;
        $post_type = 'advanced_product';
        $post_type_prefix = $post_type . '_';

        $args = [
            'post_type'  => $post_type,
            'meta_query' => [
                [
                    'key'     => $post_type_prefix . 'woo_id',
                    'value'   => $product_id,
                    'compare' => '=',
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

        $query = new WP_Query($args);

        if ($query->have_posts()) {
            $advanced_product_id = $query->posts[0]->ID;
            $woo_product = wc_get_product($product_id);?>

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
                                            <img src="<?= esc_url($image['url']); ?>" alt="<?= esc_attr($image['alt']); ?>">
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
                                            The biotechnological<br> production cycle is 2<br> years
                                        </p>
                                    </article>
                                </div>
                                <div class="swiper-slide">
                                    <article class="article-feature">
                                        <picture>
                                            <img src="<?= get_template_directory_uri(); ?>/assets/images/index-slider-features-2.png" alt="">
                                        </picture>
                                        <p>
                                            Gluten free
                                        </p>
                                    </article>
                                </div>
                                <div class="swiper-slide">
                                    <article class="article-feature">
                                        <picture>
                                            <img src="<?= get_template_directory_uri(); ?>/assets/images/index-slider-features-3.png" alt="">
                                        </picture>
                                        <p>
                                            Lactose free
                                        </p>
                                    </article>
                                </div>
                                <div class="swiper-slide">
                                    <article class="article-feature">
                                        <picture>
                                            <img src="<?= get_template_directory_uri(); ?>/assets/images/index-slider-features-4.png" alt="">
                                        </picture>
                                        <p>
                                            Mushrooms<br> and yeast free
                                        </p>
                                    </article>
                                </div>
                                <div class="swiper-slide">
                                    <article class="article-feature">
                                        <picture>
                                            <img src="<?= get_template_directory_uri(); ?>/assets/images/index-slider-features-5.png" alt="">
                                        </picture>
                                        <p>
                                            Suitable<br>for diabetics
                                        </p>
                                    </article>
                                </div>
                            </div>
                            <div class="teasers-pagination-wrapper">
                                <div>Листайте влево/вправо</div>
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
                        </article>
                        <div class="buttons">
                            <button class="button" type="submit">Buy 1 pack</button>
                            <button class="button button--green">Buy a subscription</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tabs" data-tabs="">
                <div class="tabs__header">
                    <button class="tabs__head active" data-tabs-head="1"><?= get_field($post_type_prefix . 'detailed_description_name', $advanced_product_id) ?></button>
                    <button class="tabs__head" data-tabs-head="2"><?= get_field($post_type_prefix . 'detailed_functions_name', $advanced_product_id) ?></button>
                    <button class="tabs__head" data-tabs-head="3"><?= get_field($post_type_prefix . 'detailed_instructions_name', $advanced_product_id) ?></button>
                    <button class="tabs__head" data-tabs-head="4"><?= get_field($post_type_prefix . 'detailed_composition_name', $advanced_product_id) ?></button>
                    <button class="tabs__head" data-tabs-head="5"><?= get_field($post_type_prefix . 'detailed_calorie_name', $advanced_product_id) ?></button>
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
        <?
        }
        ?>
    </div>
</section>