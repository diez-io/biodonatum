<?php
/* Template Name: FAQ Page */
?>
<? get_header(); ?>

            <div class="main">
                <section class="section pt-50">
                    <div class="container">
                        <div class="breadcrumbs mb-40">
                            <a href="<?= home_url(); ?>" class="breadcrumbs__link"><?= get_static_content('home') ?></a>
                            <span class="breadcrumbs__link"><?= get_static_content('faq') ?></span>
                        </div>
                        <div class="head">
                            <h2 class="title"><?= get_static_content('faq') ?></h2>
                        </div>
                        <div class="faq" data-dropdown="">
                            <div class="faq__item" data-dropdown-trigger="">
                                <div class="faq__header">
                                    <h3 class="faq__title title title--extra-small">
                                        <?= get_static_content('what_is_biodonatum') ?>
                                    </h3>
                                    <svg class="faq__icon">
                                        <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-chevrone"></use>
                                    </svg>
                                </div>
                                <div class="faq__content">
                                    <p class="text text--middle text--dark">
                                        <?= get_static_content('what_is_biodonatum_text') ?>
                                    </p>
                                </div>
                            </div>
                            <div class="faq__item" data-dropdown-trigger="">
                                <div class="faq__header">
                                    <h3 class="faq__title title title--extra-small">
                                        <?= get_static_content('how_to_buy') ?>
                                    </h3>
                                    <svg class="faq__icon">
                                        <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-chevrone"></use>
                                    </svg>
                                </div>
                                <div class="faq__content">
                                    <p class="text text--middle text--dark">
                                        <?= get_static_content('how_to_buy_text') ?>
                                    </p>
                                </div>
                            </div>
                            <div class="faq__item" data-dropdown-trigger="">
                                <div class="faq__header">
                                    <h3 class="faq__title title title--extra-small">
                                        <?= get_static_content('where_to_buy') ?>
                                    </h3>
                                    <svg class="faq__icon">
                                        <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-chevrone"></use>
                                    </svg>
                                </div>
                                <div class="faq__content">
                                    <p class="text text--middle text--dark">
                                        <?= get_static_content('where_to_buy_text') ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <? get_template_part('components/feedback'); ?>
            </div>

<? get_footer(); ?>
