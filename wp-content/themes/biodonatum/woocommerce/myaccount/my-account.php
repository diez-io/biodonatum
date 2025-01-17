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
                <span class="breadcrumbs__link"><?= __('Personal account', 'static') ?></span>
            </div>
            <div class="account">
                <div class="head">
                    <h2 class="title"><?= __('Personal account', 'static') ?></h2>
                </div>
				<div class="account__edit">
					<div class="account__edit__col1">
						<div class="island">
							<picture class="">
								<img src="<?= get_template_directory_uri(); ?>/assets/images/person.jpg" alt="">
							</picture>
						</div>
						<div class="island">
							<a class="account__logout" href="<?= esc_url( wc_logout_url(home_url()) ) ?>"><?= __('Log out', 'static') ?></a>
						</div>
					</div>
					<div class="account__edit__col2">
						<div class="island">
							<h3><?= __('Account details', 'static') ?></h3>
							<?
								do_action('woocommerce_account_edit-account_endpoint');
							?>
						</div>
						<div class="island">
							<h3><?= __('Payment methods', 'static') ?></h3>
							<?
								do_action('woocommerce_account_payment-methods_endpoint');
							?>
						</div>
						<div class="island">
							<h3><?= __('Addresses', 'static') ?></h3>
							<?
								do_action('woocommerce_account_edit-address_endpoint');
							?>
						</div>
					</div>
				</div>
                <div class="head">
                    <h2 class="title"><?= __('History', 'static') ?></h2>
                </div>
                <div class="island">
					<?
						do_action('woocommerce_account_orders_endpoint');
					?>
				</div>
            </div>
		</div>
	</section>

    <? get_template_part('components/feedback'); ?>

</main>
<? endif; ?>
