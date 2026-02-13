<section class="section pt-80 ptm-60 pb-80 pbm-60">
    <div class="container">
        <div class="feedback">
            <div class="feedback__contacts">
                <?
                $args = array(
                    'post_type' => 'contact',
                    'meta_query' => [
                        [
                            'key'     => 'contact_address_type',
                            'value'   => 'main',
                            'compare' => '='
                        ],
                    ],
                    'tax_query' => [
                        [
                            'taxonomy' => 'taxonomy_language',
                            'field'    => 'slug',
                            'terms'    => function_exists('get_current_language') ? get_current_language() : 'en',
                        ],
                    ],
                    //'posts_per_page' => 10,
                );

                $loop = new WP_Query($args);

                if ($loop->have_posts()):
                    $loop->the_post(); ?>

                    <div class="feedback__header">
                        <img class="feedback__logo" src="<?= get_template_directory_uri(); ?>/assets/images/feedback-logo.png" alt="">
                        <div class="feedback__titles">
                            <h2 class="feedback__title"><?= get_static_content('contacts') ?></h2>
                            <h3 class="feedback__title"><?= esc_html(get_field('contact_name')) ?></h3>
                        </div>
                    </div>
                    <div class="feedback__info">
                        <p class="feedback__text text">
                            <?= esc_html(get_field('contact_company')) ?>
                            <br>
                            <?= esc_html(get_field('contact_address')) ?>
                        </p>
                        <div class="feedback__phones">
                            <div class="feedback__phones--icon">
                                <svg>
                                    <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-phone"></use>
                                </svg>
                            </div>
                            <div class="feedback__links">
                                <? $phoneNumbers = get_field('contact_phone_numbers');

                                foreach ($phoneNumbers as $phoneNumbers_row) :
                                    $phone = $phoneNumbers_row['contact_phone_numbers_item']; ?>

                                    <a class="feedback__link" href="tel:<?= esc_attr($phone) ?>">
                                        <span class="phone-number">
                                            <?= esc_html($phone) ?>
                                        </span>
                                    </a>
                                <? endforeach; ?>
                            </div>
                        </div>
                        <div class="feedback__email">
                            <svg>
                                <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-email"></use>
                            </svg>
                            <? $email = get_field('contact_email'); ?>
                            <a class="feedback__link" href="mailto:<?= esc_attr($email) ?>">
                                <?= esc_html($email) ?>
                            </a>
                        </div>
                    </div>

                    <? wp_reset_postdata();
                endif; ?>
            </div>
            <div class="feedback__form">
                <h3 class="feedback__title"><?= get_static_content('write_to_us') ?></h3>
                <?= get_cf7_form_by_title('message') ?>
            </div>
        </div>
    </div>
</section>