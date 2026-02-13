<?php
/* Template Name: FAQ Page */
?>
<? get_header(); ?>

            <div class="main">
                <section class="section pt-50">
                    <div class="container">
                        <div class="breadcrumbs mb-40">
                            <a href="<?= esc_url(biodonatum_url_with_lang(home_url('/'))); ?>" class="breadcrumbs__link"><?= get_static_content('home') ?></a>
                            <span class="breadcrumbs__link"><?= get_static_content('faq') ?></span>
                        </div>
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
                                        'terms'    => function_exists('get_current_language') ? get_current_language() : 'en',
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

                <? get_template_part('components/feedback'); ?>
            </div>

<? get_footer(); ?>
