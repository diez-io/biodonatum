<?php
/* Template Name: Partners Page */
?>
<? get_header(); ?>

<main class="main">
    <section class="section pt-50">
        <div class="container">
            <div class="breadcrumbs mb-40">
                <a href="<?= home_url(); ?>" class="breadcrumbs__link"><?= get_static_content('home') ?></a>
                <span class="breadcrumbs__link"><?= get_static_content('partners') ?></span>
            </div>
            <div class="partners-page">
                <? get_template_part('components/partners'); ?>

                <div class="partners-page__download">
                    <a target="_blank" href="<?= wp_get_attachment_url(get_static_content('company_profile_file')) ?>">
                        <div class="partners-page__download__icon">
                            <svg>
                                <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-pdf"></use>
                            </svg>
                        </div>
                        <div class="partners-page__download__text">
                            <?= get_static_content('download_company_profile') ?>
                        </div>
                    </a>
                </div>

                <div class="island">
                    <h3>
                        <span class="partners-page__emogi">🚀</span> <?= get_static_content('joining_forces_shaping_the_future') ?>
                    </h3>
                    <p>
                        <?= get_static_content('joining_forces_shaping_the_future_text') ?>
                    </p>
                    <h3>
                        <span class="partners-page__emogi">💡</span> <?= get_static_content('driving_innovation_multiplying_success') ?>
                    </h3>
                    <p>
                        <?= get_static_content('driving_innovation_multiplying_success_text') ?>
                    </p>
                    <h3>
                        <span class="partners-page__emogi">🤝</span> <?= get_static_content('together_towards_new_opportunities') ?>
                    </h3>
                    <p>
                        <?= get_static_content('together_towards_new_opportunities_text') ?>
                    </p>

                    <? $language_slug = defined('CURRENT_LANGUAGE') ? CURRENT_LANGUAGE : ''; ?>
                    <a href="<?= home_url("/$language_slug" . parse_url(get_permalink(get_page_by_path('contacts')), PHP_URL_PATH)); ?>" class="partners-page__contact-us">
                        <span class="partners-page__emogi">📩</span> <?= get_static_content('contact_us_lets_grow_together') ?>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <? get_template_part('components/feedback'); ?>
</main>

<? get_footer(); ?>
