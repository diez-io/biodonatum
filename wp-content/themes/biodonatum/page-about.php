<?php
/* Template Name: About Page */
?>
<? get_header(); ?>

<main class="main">
    <section class="section pt-50">
        <div class="container">
            <div class="breadcrumbs mb-40">
                <a href="<?= esc_url(biodonatum_url_with_lang(home_url('/'))); ?>" class="breadcrumbs__link"><?= get_static_content('home') ?></a>
                <span class="breadcrumbs__link"><?= get_static_content('about') ?></span>
            </div>
            <div class="about">
                <div class="head">
                    <h2 class="title"><?= get_static_content('about') ?></h2>
                </div>
                <div class="editor">
                    <p>
                        <?= get_static_content('about_text_1') ?>
                        <br>
                        <?= get_static_content('about_text_2') ?>
                    </p>
                </div>
                <div class="about__stories">
                    <?
                    $stories = new WP_Query([
                        'post_type' => 'story',
                        'tax_query' => [
                            [
                                'taxonomy' => 'taxonomy_language',
                                'field'    => 'slug',
                                'terms'    => function_exists('get_current_language') ? get_current_language() : 'en',
                            ],
                        ],
                        'orderby' => 'date',
                        'order'   => 'ASC',
                    ]);
                    ?>
                    <?while ($stories->have_posts()): $stories->the_post();?>
                        <div class="about__stories__block">
                            <div class="about__stories__block__picture">
                                <?if (has_post_thumbnail()):?>
                                    <picture class="about__stories__block__picture--main">
                                        <img src="<?=get_the_post_thumbnail_url(get_the_ID(), 'full');?>" alt="">
                                    </picture>
                                <?endif;?>
                                <?if (get_field('sub_picture')):?>
                                    <picture class="about__stories__block__picture--sub">
                                        <img src="<?=get_field('sub_picture')['url']?>" alt="<?=esc_attr(get_field('sub_picture')['alt'])?>">
                                    </picture>
                                <?endif;?>
                            </div>
                            <div class="about__stories__block__text">

                                <?if (get_the_title()):?>
                                    <div class="about__stories__block__text--title">
                                        <?the_title()?>
                                    </div>
                                <?endif;?>

                                <?if (get_field('epigraph')):?>
                                    <div class="about__stories__block__text--epigraph">
                                        <?=get_field('epigraph')?>
                                    </div>
                                <?endif;?>

                                <?if (get_the_content()):?>
                                    <div class="about__stories__block__text--text">
                                        <?the_content()?>
                                    </div>
                                <?endif;?>

                                <?if (get_field('quote')):?>
                                    <div class="about__stories__block__text--quote">
                                        <?=get_field('quote')?>
                                    </div>
                                <?endif;?>
                            </div>
                        </div>
                    <?endwhile;?>
                    <?wp_reset_postdata();?>
                </div>
            </div>
        </div>
    </section>
    <section class="section section--bg about__wide" style="background-image: url('<?= get_template_directory_uri(); ?>/assets/images/about/about-wide.png')">
        <?/*<div class="about__wide__content">
            <div class="about__wide__content-big"><?= get_static_content('about_text_3') ?></div>
            <div class="about__wide__content-small"><?= get_static_content('about_text_4') ?></div>
        </div>*/?>
    </section>

    <? get_template_part('components/feedback'); ?>

</main>

<? get_footer(); ?>
