<?php
/* Template Name: About Page */
?>
<? get_header(); ?>

<main class="main">
    <section class="section pt-50">
        <div class="container">
            <div class="breadcrumbs mb-40">
                <a href="<?= home_url(); ?>" class="breadcrumbs__link"><?= get_static_content('home') ?></a>
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
                    <div class="about__stories__block">
                        <div class="about__stories__block__picture">
                            <picture class="about__stories__block__picture--main">
                                <img src="<?= get_template_directory_uri(); ?>/assets/images/about/about-1.jpg" alt="">
                            </picture>
                            <picture class="about__stories__block__picture--sub">
                                <img src="<?= get_template_directory_uri(); ?>/assets/images/about/about-1-1.jpg" alt="">
                            </picture>
                        </div>
                        <div class="about__stories__block__text">
                            <div class="about__stories__block__text--title">
                                <?= get_static_content('story_1_title') ?>
                            </div>
                            <div class="about__stories__block__text--epigraph">
                                <?= get_static_content('story_1_epigraph') ?>
                            </div>
                        </div>
                    </div>
                    <div class="about__stories__block">
                        <div class="about__stories__block__picture">
                            <picture class="about__stories__block__picture--main">
                                <img src="<?= get_template_directory_uri(); ?>/assets/images/about/about-2.jpg" alt="">
                            </picture>
                        </div>
                        <div class="about__stories__block__text">
                            <div class="about__stories__block__text--title">
                                <?= get_static_content('story_2_title') ?>
                            </div>
                            <div class="about__stories__block__text--epigraph">
                                <?= get_static_content('story_2_epigraph') ?>
                            </div>
                            <div class="about__stories__block__text--text">
                                <?= get_static_content('story_2_text') ?>
                            </div>
                        </div>
                    </div>
                    <div class="about__stories__block">
                        <div class="about__stories__block__picture">
                            <picture class="about__stories__block__picture--main">
                                <img src="<?= get_template_directory_uri(); ?>/assets/images/about/about-3.jpg" alt="">
                            </picture>
                            <picture class="about__stories__block__picture--sub">
                                <img src="<?= get_template_directory_uri(); ?>/assets/images/about/about-3-3.jpg" alt="">
                            </picture>
                        </div>
                        <div class="about__stories__block__text">
                            <div class="about__stories__block__text--title">
                                <?= get_static_content('story_3_title') ?>
                            </div>
                            <div class="about__stories__block__text--epigraph">
                                <?= get_static_content('story_3_epigraph') ?>
                            </div>
                            <div class="about__stories__block__text--text">
                                <?= get_static_content('story_3_text') ?>
                            </div>
                        </div>
                    </div>
                    <div class="about__stories__block">
                        <div class="about__stories__block__picture">
                            <picture class="about__stories__block__picture--main">
                                <img src="<?= get_template_directory_uri(); ?>/assets/images/about/about-4.jpg" alt="">
                            </picture>
                        </div>
                        <div class="about__stories__block__text">
                            <div class="about__stories__block__text--title">
                                <?= get_static_content('story_4_title') ?>
                            </div>
                            <div class="about__stories__block__text--epigraph">
                                <?= get_static_content('story_4_epigraph') ?>
                            </div>
                            <div class="about__stories__block__text--text">
                                <?= get_static_content('story_4_text') ?>
                            </div>
                            <div class="about__stories__block__text--quote">
                                <?= get_static_content('story_4_quote') ?>
                            </div>
                        </div>
                    </div>
                    <div class="about__stories__block">
                        <div class="about__stories__block__picture">
                            <picture class="about__stories__block__picture--main">
                                <img src="<?= get_template_directory_uri(); ?>/assets/images/about/about-5.jpg" alt="">
                            </picture>
                            <picture class="about__stories__block__picture--sub">
                                <img src="<?= get_template_directory_uri(); ?>/assets/images/about/about-5-5.jpg" alt="">
                            </picture>
                        </div>
                        <div class="about__stories__block__text">
                            <div class="about__stories__block__text--title">
                                <?= get_static_content('story_5_title') ?>
                            </div>
                            <div class="about__stories__block__text--epigraph">
                                <?= get_static_content('story_5_epigraph') ?>
                            </div>
                        </div>
                    </div>
                    <div class="about__stories__block">
                        <div class="about__stories__block__picture">
                            <picture class="about__stories__block__picture--main">
                                <img src="<?= get_template_directory_uri(); ?>/assets/images/about/about-6.jpg" alt="">
                            </picture>
                        </div>
                        <div class="about__stories__block__text">
                            <div class="about__stories__block__text--title">
                                <?= get_static_content('story_6_title') ?>
                            </div>
                            <div class="about__stories__block__text--epigraph">
                                <?= get_static_content('story_6_epigraph') ?>
                            </div>
                        </div>
                    </div>
                    <div class="about__stories__block">
                        <div class="about__stories__block__picture">
                            <picture class="about__stories__block__picture--main">
                                <img src="<?= get_template_directory_uri(); ?>/assets/images/about/about-7.jpeg" alt="">
                            </picture>
                        </div>
                        <div class="about__stories__block__text">
                            <div class="about__stories__block__text--title">
                                <?= get_static_content('story_7_title') ?>
                            </div>
                            <div class="about__stories__block__text--epigraph">
                                <?= get_static_content('story_7_epigraph') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="section section--bg about__wide" style="background-image: url('<?= get_template_directory_uri(); ?>/assets/images/about/about-wide.png')">
        <div class="about__wide__content">
            <div class="about__wide__content-big"><?= get_static_content('about_text_3') ?></div>
            <div class="about__wide__content-small"><?= get_static_content('about_text_4') ?></div>
        </div>
    </section>

    <? get_template_part('components/feedback'); ?>

</main>

<? get_footer(); ?>
