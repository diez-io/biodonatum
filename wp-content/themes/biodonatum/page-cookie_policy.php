<?php
/* Template Name: Cookie Policy Page */
?>
<? get_header(); ?>

            <main class="main">
                <section class="section pt-50">
                    <div class="container">
                        <div class="breadcrumbs mb-40">
                            <a href="<?= esc_url(biodonatum_url_with_lang(home_url('/'))); ?>" class="breadcrumbs__link"><?= get_static_content('home') ?></a>
                            <span class="breadcrumbs__link"><?= get_static_content('cookie_policy') ?></span>
                        </div>
                        <div class="head">
                            <h2 class="title"><?= get_static_content('cookie_policy') ?></h2>
                        </div>

                        <? if (get_static_content('cookie_policy_file')) : ?>
                            <a target="_blank" class="download-terms-link" href="<?= wp_get_attachment_url(get_static_content('cookie_policy_file')) ?>">
                                <div class="download-terms-link__icon">
                                    <svg>
                                        <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-pdf"></use>
                                    </svg>
                                </div>
                                <div class="download-terms-link__text">
                                    <?= get_static_content('download') . ' ' . get_static_content('cookie_policy') ?>
                                </div>
                            </a>
                        <? endif; ?>
                    </div>
                </section>

                <? get_template_part('components/feedback'); ?>
            </main>

<? get_footer(); ?>
