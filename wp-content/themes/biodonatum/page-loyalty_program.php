<?php
/* Template Name: Loyalty Program Page */
?>
<? get_header(); ?>

            <main class="main">
                <section class="section pt-50">
                    <div class="container">
                        <div class="breadcrumbs mb-40">
                            <a href="<?= esc_url(biodonatum_url_with_lang(home_url('/'))); ?>" class="breadcrumbs__link"><?= get_static_content('home') ?></a>
                            <span class="breadcrumbs__link"><?= get_static_content('loyalty_program') ?></span>
                        </div>
                        <div class="head">
                            <h2 class="title"><?= get_static_content('loyalty_program') ?></h2>
                        </div>
                        <div class="editor">
                            <p>
                                <?= get_static_content('loyalty_program_text') ?>
                            </p>
                        </div>
                    </div>
                </section>

                <? get_template_part('components/feedback'); ?>
            </main>

<? get_footer(); ?>
