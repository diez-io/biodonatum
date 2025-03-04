<?php
/* Template Name: Contacts Page */
?>
<? get_header(); ?>
<?
function printContacts($type) {
    $args = array(
        'post_type' => 'contact',
        'meta_query' => [
            [
                'key'     => 'contact_address_type',
                'value'   => $type,
                'compare' => '='
            ],
        ],
        'tax_query' => [
            [
                'taxonomy' => 'taxonomy_language',
                'field'    => 'slug',
                'terms'    => $_SESSION['lang'],
            ],
        ],
        //'posts_per_page' => 10,
    );

    $loop = new WP_Query($args);

    if ($loop->have_posts()): ?>
        <? if ($type === 'partner') : ?>
            <div class="head">
                <h2 class="title"><?= get_static_content('our_distributor_partners') ?></h2>
            </div>
        <? endif; ?>

        <div class="contacts__cards">
        <?
        while ($loop->have_posts()):
            $loop->the_post(); ?>

            <div class="card">
                <div class="card__text">
                    <div class="card__text-title">
                        <?= esc_html(get_field('contact_name')) ?>
                    </div>
                    <div class="card__text-description">
                        <?= esc_html(get_field('contact_company')) ?>
                        <br>
                        <?= esc_html(get_field('contact_address')) ?>
                    </div>
                    <div class="contacts__contacts">
                        <?
                        $websites = get_field('contact_websites');

                        if ($websites !== null && $websites !== false) :
                            if (count($websites) === 1) :
                                $url = esc_url($websites[0]['contact_websites_item']); ?>

                                <a href="<?= $url ?>" class="card__text-link">
                                    <svg>
                                        <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-globe"></use>
                                    </svg>
                                    <?= parse_url($url, PHP_URL_HOST) ?>
                                </a>
                            <? else : ?>
                                <div class="card__text-link card__text-link_multiple">

                                <? foreach ($websites as $websites_row) :
                                    $url = esc_url($websites_row['contact_websites_item']); ?>

                                    <a href="<?= $url ?>" class="card__text-link">
                                        <svg>
                                            <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-globe"></use>
                                        </svg>
                                        <?= parse_url($url, PHP_URL_HOST) ?>
                                    </a>
                                <? endforeach; ?>

                                </div>
                            <? endif; ?>
                        <? endif; ?>

                        <?
                        $phoneNumbers = get_field('contact_phone_numbers');

                        if (count($phoneNumbers) === 1) :
                            $phone = $phoneNumbers[0]['contact_phone_numbers_item']; ?>

                            <a href="tel:<?= esc_attr($phone) ?>" class="card__text-link">
                                <svg>
                                    <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-phone"></use>
                                </svg>
                                <span class="phone-number">
                                    <?= esc_html($phone) ?>
                                </span>
                            </a>
                        <? else : ?>
                            <div class="card__text-link card__text-link_multiple">

                            <? foreach ($phoneNumbers as $phoneNumbers_row) :
                                $phone = $phoneNumbers_row['contact_phone_numbers_item']; ?>

                                <a href="tel:<?= esc_attr($phone) ?>" class="card__text-link">
                                    <svg>
                                        <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-phone"></use>
                                    </svg>
                                    <span class="phone-number">
                                        <?= esc_html($phone) ?>
                                    </span>
                                </a>
                            <? endforeach; ?>

                            </div>
                        <? endif; ?>

                        <? $email = get_field('contact_email'); ?>
                        <a href="mailto:<?= esc_attr($email) ?>" class="card__text-link">
                            <svg>
                                <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-email"></use>
                            </svg>
                            <?= esc_html($email) ?>
                        </a>
                    </div>
                </div>
            </div>

        <?php endwhile; ?>

        </div>

        <? wp_reset_postdata();
    endif;
}
?>

<main class="main">
    <section class="section pt-50">
        <div class="container">
            <div class="breadcrumbs mb-40">
                <a href="<?= home_url(); ?>" class="breadcrumbs__link"><?= get_static_content('home') ?></a>
                <span class="breadcrumbs__link"><?= get_static_content('contacts') ?></span>
            </div>
            <div class="contacts">
                <div class="head">
                    <h2 class="title"><?= get_static_content('contacts') ?></h2>
                </div>
                <? printContacts('main'); ?>
                <? printContacts('regular'); ?>
                <? printContacts('partner'); ?>
                <div class="editor">
                    <p>
                        <?= get_static_content('contacts_text_1') ?>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <? get_template_part('components/feedback'); ?>
</main>

<? get_footer(); ?>
