<? get_header(); ?>

        <main class="main">
            <section class="section pt-50">
                <div class="container">
                    <div class="breadcrumbs mb-40">
                        <a href="<?= home_url(); ?>" class="breadcrumbs__link"><?= get_static_content('home') ?></a>
                        <a href="<?= get_permalink( get_option('woocommerce_shop_page_id')) ?>" class="breadcrumbs__link"><?= get_static_content('shop') ?></a>
                        <span class="breadcrumbs__link"><?= get_field('advanced_product_name') ?></span>
                    </div>
                </div>
            </section>
            <div class="product">
                <? get_template_part('components/productDetail'); ?>
                <? get_template_part('components/reviews'); ?>
            </div>
			<div class="product">
				<section class="section pt-50">
                    <div class="container">
                        <div class="head">
                            <h2 class="title"><?= get_static_content('faq') ?></h2>
                        </div>
                        <div class="faq" data-dropdown="">
                            <? $args = array(
                                'post_type' => 'static_content',
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

                                    <? $questionRows = get_field('static_faq_questions'); ?>
                                    <? foreach ($questionRows as $row) : ?>
                                        <div class="faq__item" data-dropdown-trigger="">
                                            <div class="faq__header">
                                                <h3 class="faq__title title title--extra-small">
                                                    <?= $row['static_faq_questions_question'] ?>
                                                </h3>
                                                <div class="faq__icon">
                                                    <svg>
                                                        <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-x"></use>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="faq__content">
                                                <?= $row['static_faq_questions_answer'] ?>
                                            </div>
                                        </div>
                                    <? endforeach; ?>



                                <?php endwhile;

                                wp_reset_postdata();
                            endif; ?>
                        </div>
                    </div>
                </section>
			</div>
			<? get_template_part('components/feedback'); ?>
        </main>

<? get_footer(); ?>
