<?php
/* Template Name: Reviews Page */
?>
<? get_header(); ?>

        <div class="main">
            <section class="section pt-50">
                <div class="container">
                    <div class="breadcrumbs mb-40">
                        <a href="<?= home_url(); ?>" class="breadcrumbs__link">to index</a>
                        <span class="breadcrumbs__link">Reviews</span>
                    </div>
                </div>
            </section>

            <? get_template_part('components/reviews'); ?>
            <? get_template_part('components/feedback'); ?>
        </div>

<? get_footer(); ?>
