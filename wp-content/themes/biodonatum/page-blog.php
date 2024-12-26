<?php
/* Template Name: Blog Page */
?>
<? get_header(); ?>

        <main class="main">
            <section class="section pt-50">
                <div class="container">
                    <div class="breadcrumbs mb-40">
                        <a href="<?= home_url(); ?>" class="breadcrumbs__link">to index</a>
                        <span class="breadcrumbs__link">Blog</span>
                    </div>
                    <div class="blog">
                        <div class="head">
                            <h2 class="title">Blog</h2>
                        </div>
                        <div class="island">
                            Microbiome science requires precision. In collaboration with scientists from all over the world, for 110 years we have been working and creating bio microbiotics for you and your health, for the benefit of future generations. We use probiotic strains with clinical data obtained in Japan.
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
                                                Look More
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
                        <div class="button blog__load-more load-more-btn">
                            LOAD MORE
                        </div>
                    </div>
                </div>
            </section>
            <? get_template_part('components/feedback'); ?>
        </main>

<? get_footer(); ?>
