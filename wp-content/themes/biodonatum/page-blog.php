<?php
/* Template Name: Blog Page */
?>
<? get_header(); ?>

        <main class="main">
            <section class="section pt-50">
                <div class="container">
                    <div class="breadcrumbs mb-40">
                        <a href="<?= home_url(); ?>" class="breadcrumbs__link"><?= get_static_content('home') ?></a>
                        <span class="breadcrumbs__link"><?= get_static_content('blog') ?></span>
                    </div>
                    <div class="blog">
                        <div class="head">
                            <h2 class="title"><?= get_static_content('blog') ?></h2>
                        </div>
                        <div class="island">
                            <?= get_static_content('blog_text_1') ?>
                            <?= get_static_content('blog_text_2') ?>
                            <?= get_static_content('blog_text_3') ?>
                        </div>
                        <div class="blog__cards load-more-items">

                            <? $args = array(
                                'post_type' => 'blog',
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
                            $numberOfBlogs = $loop->found_posts;

                            if ($loop->have_posts()):
                                while ($loop->have_posts()):
                                    $loop->the_post(); ?>
                                    <div class="card">
                                        <picture>
                                            <? $card_image = get_field('blog_image_preview') ?: get_field('blog_image'); ?>
                                            <img src="<?= esc_url($card_image['url']); ?>" alt="<?= esc_attr($card_image['alt']); ?>">
                                        </picture>
                                        <div class="card__text">
                                            <div class="card__text-title">
                                                <?= esc_html(get_field('blog_title')); ?>
                                            </div>
                                            <a href="<?= the_permalink() ?>" class="card__text-link">
                                                <?= get_static_content('look_more') ?>
                                                <svg>
                                                    <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-vector"></use>
                                                </svg>
                                            </a>
                                            <div class="card__text-timestamp">
                                                <?= get_the_date('d.m.Y') ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile;
                                wp_reset_postdata();
                            endif;
                            ?>
                        </div>
                        <? if ($numberOfBlogs > 9) : ?>
                            <div class="button blog__load-more load-more-btn">
                                <?= get_static_content('load_more') ?>
                            </div>
                        <? endif; ?>
                    </div>
                </div>
            </section>
            <? get_template_part('components/feedback'); ?>
        </main>

<? get_footer(); ?>
