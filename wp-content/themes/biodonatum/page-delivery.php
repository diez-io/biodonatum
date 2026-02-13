<?php
/* Template Name: Delivery Page */
?>
<? get_header(); ?>

            <main class="main">
                <section class="section pt-50">
                    <div class="container">
                        <div class="breadcrumbs mb-40">
                            <a href="<?= esc_url(biodonatum_url_with_lang(home_url('/'))); ?>" class="breadcrumbs__link"><?= get_static_content('home') ?></a>
                            <span class="breadcrumbs__link"><?= get_static_content('delivery_terms') ?></span>
                        </div>
                        <div class="head">
                            <h2 class="title"><?= get_static_content('delivery_terms') ?></h2>
                        </div>
                        <?/*<div class="editor">
                            <p>
                                <?= get_static_content('delivery_text_1') ?>
                            </p>
                            <p>
                                <?= get_static_content('delivery_text_2') ?>
                                <?= get_static_content('delivery_text_3') ?>
                                <?= get_static_content('delivery_text_4') ?>
                            </p>
                            <p>
                                <?= get_static_content('delivery_text_5') ?>
                                <?= get_static_content('delivery_text_6') ?>
                                <?= get_static_content('delivery_text_7') ?>
                            </p>
                            <p>
                                <?= get_static_content('delivery_text_8') ?>
                            </p>
                        </div>*/?>
                        <? if (get_static_content('delivery_terms_file')) : ?>
                            <a target="_blank" class="download-terms-link" href="<?= wp_get_attachment_url(get_static_content('delivery_terms_file')) ?>">
                                <div class="download-terms-link__icon">
                                    <svg>
                                        <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-pdf"></use>
                                    </svg>
                                </div>
                                <div class="download-terms-link__text">
                                    <?= get_static_content('download') . ' ' . get_static_content('delivery_terms') ?>
                                </div>
                            </a>
                        <? endif; ?>
                    </div>
                </section>

                <? get_template_part('components/feedback'); ?>
            </main>

<? get_footer(); ?>
