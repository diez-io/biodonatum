<?php
/* Template Name: Vacancy Page */
?>
<? get_header(); ?>

        <main class="main">
            <section class="section pt-50">
                <div class="container">
                    <div class="breadcrumbs mb-40">
                        <a href="<?= esc_url(biodonatum_url_with_lang(home_url('/'))); ?>" class="breadcrumbs__link"><?= get_static_content('home') ?></a>
                        <span class="breadcrumbs__link"><?= get_static_content('vacancy') ?></span>
                    </div>
                    <div class="vacancy">
                        <div class="head">
                            <h2 class="title"><?= get_static_content('vacancy') ?></h2>
                        </div>
                        <div class="editor">
                            <?= get_static_content('vacancy_text') ?>
                        </div>
                        <div class="vacancy__cards load-more-items">
                            <?
                            $args = array(
                                'post_type' => 'vacancy',
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
                            $numberOfVacancies = $loop->found_posts;

                            if ($loop->have_posts()):
                                while ($loop->have_posts()):
                                    $loop->the_post(); ?>

                                    <div class="card">
                                        <div class="card__text">
                                            <div class="card__text-title">
                                                <?= esc_html(get_field('vacancy_name')) ?>
                                            </div>
                                            <div class="vacancy__links">
                                                <div class="card__text-link">
                                                    <svg>
                                                        <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-coins"></use>
                                                    </svg>
                                                    <?= esc_html(get_field('vacancy_salary')) ?>
                                                </div>
                                                <div class="card__text-link">
                                                    <svg>
                                                        <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-location"></use>
                                                    </svg>
                                                    <?= esc_html(get_field('vacancy_city')) ?>
                                                </div>
                                                <div class="card__text-link">
                                                    <svg>
                                                        <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-document"></use>
                                                    </svg>
                                                    <?= get_static_content('experience') ?>
                                                    <?= esc_html(get_field('vacancy_experience')) ?>
                                                </div>
                                            </div>
                                            <div class="card__text-description">
                                                <h3><?= get_static_content('description') ?></h3>
                                                <?= esc_html(get_field('vacancy_description')) ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile;

                                wp_reset_postdata();
                            endif; ?>
                        </div>
                        <? if ($numberOfVacancies > 4) : ?>
                            <div class="button load-more-btn">
                                <?= get_static_content('load_more') ?>
                            </div>
                        <? endif; ?>
                    </div>
                </div>
            </section>
            <div class="vacancy__section-wrapper">
                <section class="section section--bg section--hero" style="background-image: url('/wp-content/uploads/2025/08/photo_2025-08-18-12.16.29.jpeg')">
                </section>
                <section class="section pt-50 vacancy__section-wrapper__form">
                    <div class="container">
                        <div class="island">
                            <h2 class="form__title">
                                <?= get_static_content('join_our_team') ?>
                            </h2>
                            <?= get_cf7_form_by_title('join_team', 'join-our-team-form') ?>
                        </div>
                    </div>
                </section>
            </div>

            <? get_template_part('components/feedback'); ?>
        </main>

<? get_footer(); ?>
