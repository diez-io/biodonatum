<?php
/* Template Name: Delivery Page */
?>
<? get_header(); ?>

            <main class="main">
                <section class="section pt-50">
                    <div class="container">
                        <div class="breadcrumbs mb-40">
                            <a href="<?= home_url(); ?>" class="breadcrumbs__link"><?= get_static_content('home') ?></a>
                            <span class="breadcrumbs__link"><?= get_static_content('delivery_terms') ?></span>
                        </div>
                        <div class="head">
                            <h2 class="title"><?= get_static_content('delivery_terms') ?></h2>
                        </div>
                        <div class="editor">
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
                        </div>
                    </div>
                </section>

                <? get_template_part('components/feedback'); ?>
            </main>

<? get_footer(); ?>
