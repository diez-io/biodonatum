<?php
/**
 * My Account page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

defined( 'ABSPATH' ) || exit;

global $wp;
$isViewOrderPage = false;

if ( ! empty( $wp->query_vars ) ) {
	foreach ( $wp->query_vars as $key => $value ) {
		if ( 'view-order' === $key ) {
			do_action( 'woocommerce_account_content' );
			$isViewOrderPage = true;
			break;
		}
	}
}

if (!$isViewOrderPage) : ?>

<main class="main">
    <section class="section pt-50">
        <div class="container">
            <div class="breadcrumbs mb-40">
                <a href="<?= home_url(); ?>" class="breadcrumbs__link"><?= get_static_content('home') ?></a>
                <span class="breadcrumbs__link"><?= get_static_content('personal_account') ?></span>
            </div>
            <div class="account">
                <div class="head">
                    <h2 class="title"><?= get_static_content('personal_account') ?></h2>
                </div>
				<div class="account__edit">
					<div class="account__edit__col1">
						<div class="island">
							<div class="account__profile-picture">
								<picture>
									<?
										$user_id = get_current_user_id();
										$profile_image_url = get_user_meta($user_id, 'profile_image_url', true);
									?>
									<img class="account__profile-picture__img" src="<?= $profile_image_url ?: get_template_directory_uri() . '/assets/images/person.jpg' ?>" alt="">
									<img class="account__profile-picture__preview" style="display:none;" src="">
								</picture>
								<? if ($profile_image_url) : ?>
									<div class="remove-profile-picture noselect">
										x
									</div>
								<? endif; ?>
								<div class="edit-profile-picture noselect">
									<svg>
										<use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-upload"></use>
									</svg>
									<?= get_static_content('change_photo') ?>
								</div>
							</div>
							<form class="account__profile-picture-edit-form" action="" method="post" enctype="multipart/form-data">
								<? wp_nonce_field('profile_picture_upload_action', 'profile_picture_upload_nonce'); ?>
								<input type="file" name="image" accept="image/*" style="display:none" required>
								<button style="display:none" class="button button--wide mt-20" type="submit" name="upload_profile_picture">
									<?= get_static_content('save') ?>
								</button>
							</form>
							<button style="display:none" class="account__profile-picture__cancel button button--wide mt-20">
								<?= get_static_content('cancel') ?>
							</button>
						</div>
						<div class="island">
							<a class="account__logout" href="<?= esc_url( wc_logout_url(home_url()) ) ?>"><?= get_static_content('log_out') ?></a>
						</div>
					</div>
					<div class="account__edit__col2">
						<div class="island">
							<h3><?= get_static_content('account_details') ?></h3>
							<?
								do_action('woocommerce_account_edit-account_endpoint');
							?>
						</div>
						<div class="island">
							<h3><?= get_static_content('payment_methods') ?></h3>
							<?
								do_action('woocommerce_account_payment-methods_endpoint');
							?>
						</div>
						<div class="island">
							<h3><?= get_static_content('addresses') ?></h3>
							<?
								do_action('woocommerce_account_edit-address_endpoint');
							?>
						</div>
					</div>
				</div>
                <div class="head">
                    <h2 class="title"><?= get_static_content('history') ?></h2>
                </div>
                <div class="island">
					<?
						$urlSlugs = explode("/", parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
						$current_page = 1;

						if (count($urlSlugs) === 5 && $urlSlugs[2] === 'orders' && is_numeric($urlSlugs[3])) {
							$current_page = intval($urlSlugs[3]);
						}

						do_action('woocommerce_account_orders_endpoint', $current_page);
					?>
				</div>
            </div>
		</div>
	</section>

    <? get_template_part('components/feedback'); ?>

</main>
<? get_template_part('components/removeProfileImageForm'); ?>

<? endif; ?>
