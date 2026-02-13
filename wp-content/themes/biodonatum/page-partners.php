<?php
/* Template Name: Partners Page */
?>
<? get_header(); ?>

<main class="main">
    <section class="section pt-50">
        <div class="container">
            <div class="breadcrumbs mb-40">
                <a href="<?= esc_url(biodonatum_url_with_lang(home_url('/'))); ?>" class="breadcrumbs__link"><?= get_static_content('home') ?></a>
                <span class="breadcrumbs__link"><?= get_static_content('partners') ?></span>
            </div>
            <div class="partners-page">
                <? get_template_part('components/partners'); ?>
            </div>
        </div>
    </section>

    <? get_template_part('components/feedback'); ?>
</main>

<? get_footer(); ?>
