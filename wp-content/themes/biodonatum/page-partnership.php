<?php
/* Template Name: Partnership Page */
?>
<? get_header(); ?>

<main class="main">
    <section class="section pt-50">
        <div class="container">
            <div class="breadcrumbs mb-40">
                <a href="<?= home_url(); ?>" class="breadcrumbs__link"><?= get_static_content('home') ?></a>
                <span class="breadcrumbs__link"><?= get_static_content('partnership') ?></span>
            </div>
            <div class="partnership">
                <div class="head">
                    <h2 class="title"><?= get_static_content('partnership') ?></h2>
                </div>
                <div class="island partnership__global-reach">
                    <h3>
                        <?= get_static_content('global_reach_logistics') ?>
                    </h3>
                    <?= get_static_content('global_reach_logistics_text') ?>
                </div>
                <div class="island partnership__global-technology">
                    <h3>
                        <?= get_static_content('global_technology_support') ?>
                    </h3>
                    <?= get_static_content('global_technology_support_text') ?>
                </div>
                <div class="island partnership__synergistic">
                    <h3>
                        <?= get_static_content('global_synergistic_marketing_and_logistic_support') ?>
                    </h3>
                    <?= get_static_content('global_synergistic_marketing_and_logistic_support_text') ?>
                </div>
            </div>
        </div>
    </section>

    <? get_template_part('components/feedback'); ?>
</main>

<? get_footer(); ?>
