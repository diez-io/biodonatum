<?php
/* Template Name: FAQ Page */
?>
<? get_template_part('components/header'); ?>

            <div class="main">
                <section class="section pt-50">
                    <div class="container">
                        <div class="breadcrumbs mb-40">
                            <a href="<?= home_url(); ?>" class="breadcrumbs__link">to index</a>
                            <span class="breadcrumbs__link">FAQ</span>
                        </div>
                        <div class="head">
                            <h2 class="title">FAQ</h2>
                        </div>
                        <div class="faq" data-dropdown="">
                            <div class="faq__item" data-dropdown-trigger="">
                                <div class="faq__header">
                                    <h3 class="faq__title title title--extra-small">
                                        What is Biodonatum?
                                    </h3>
                                    <svg class="faq__icon">
                                        <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-chevrone"></use>
                                    </svg>
                                </div>
                                <div class="faq__content">
                                    <p class="text text--middle text--dark">
                                        Biodonatum is a health supplement or product (additional details could be
                                        provided about its specific nature).
                                    </p>
                                </div>
                            </div>
                            <div class="faq__item" data-dropdown-trigger="">
                                <div class="faq__header">
                                    <h3 class="faq__title title title--extra-small">
                                        How to buy?
                                    </h3>
                                    <svg class="faq__icon">
                                        <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-chevrone"></use>
                                    </svg>
                                </div>
                                <div class="faq__content">
                                    <p class="text text--middle text--dark">
                                        Biodonatum is a health supplement or product (additional details could be
                                        provided about its specific nature).
                                    </p>
                                </div>
                            </div>
                            <div class="faq__item" data-dropdown-trigger="">
                                <div class="faq__header">
                                    <h3 class="faq__title title title--extra-small">
                                        Where to buy?
                                    </h3>
                                    <svg class="faq__icon">
                                        <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-chevrone"></use>
                                    </svg>
                                </div>
                                <div class="faq__content">
                                    <p class="text text--middle text--dark">
                                        Biodonatum is a health supplement or product (additional details could be
                                        provided about its specific nature).
                                    </p>
                                </div>
                            </div>
                            <div class="faq__item" data-dropdown-trigger="">
                                <div class="faq__header">
                                    <h3 class="faq__title title title--extra-small">
                                        What is Biodonatum?
                                    </h3>
                                    <svg class="faq__icon">
                                        <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-chevrone"></use>
                                    </svg>
                                </div>
                                <div class="faq__content">
                                    <p class="text text--middle text--dark">
                                        Biodonatum is a health supplement or product (additional details could be
                                        provided about its specific nature).
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <? get_template_part('components/feedback'); ?>
            </div>

<? get_template_part('components/footer'); ?>
