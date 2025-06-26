<?php
/* Template Name: Terms of sales Page */
?>
<? get_header(); ?>

            <main class="main">
                <section class="section pt-50">
                    <div class="container">
                        <div class="breadcrumbs mb-40">
                            <a href="<?= home_url(); ?>" class="breadcrumbs__link"><?= get_static_content('home') ?></a>
                            <span class="breadcrumbs__link"><?= get_static_content('terms_of_sales') ?></span>
                        </div>
                        <div class="head">
                            <h2 class="title"><?= get_static_content('terms_of_sales') ?></h2>
                        </div>
                        <div class="editor">
                            <p>
                                <?= get_static_content('terms_of_sales_text') ?>
                            </p>
                        </div>

                        <? if (get_static_content('terms_of_sales_file')) : ?>
                            <a target="_blank" class="download-terms-link" href="<?= wp_get_attachment_url(get_static_content('terms_of_sales_file')) ?>">
                                <div class="download-terms-link__icon">
                                    <svg>
                                        <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-pdf"></use>
                                    </svg>
                                </div>
                                <div class="download-terms-link__text">
                                    <?= get_static_content('download') . ' ' . get_static_content('terms_of_sales') ?>
                                </div>
                            </a>
                        <? endif; ?>
                        <? if (get_static_content('cancellation_policy_file')) : ?>
                            <a target="_blank" class="download-terms-link" href="<?= wp_get_attachment_url(get_static_content('cancellation_policy_file')) ?>">
                                <div class="download-terms-link__icon">
                                    <svg>
                                        <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-pdf"></use>
                                    </svg>
                                </div>
                                <div class="download-terms-link__text">
                                    <?= get_static_content('download') . ' ' . get_static_content('cancellation_policy') ?>
                                </div>
                            </a>
                        <? endif; ?>
                    </div>
                </section>

                <? get_template_part('components/feedback'); ?>
            </main>

<? get_footer(); ?>
