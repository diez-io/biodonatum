<?php
/**
 * Lost password reset form.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-reset-password.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.2.0
 */

defined( 'ABSPATH' ) || exit;

//do_action( 'woocommerce_before_reset_password_form' );
?>

<div class="lost-password registration">
    <div class="lost-password__container">
        <div class="registration__title">Создание нового пароля</div>
        <form class="form custom-woocommerce-form" action="<?= esc_url(admin_url('admin-ajax.php')) ?>">

            <? wp_nonce_field( 'reset_password', 'woocommerce-reset-password-nonce' ); ?>
            <input type="hidden" name="reset_key" value="<?= esc_attr($args['key']) ?>" />
            <input type="hidden" name="reset_login" value="<?= esc_attr($args['login']) ?>" />
            <input type="hidden" name="action" value="custom_reset_password">

            <div class="registration__input">
                <div class="registration__input--lable">Новый пароль:</div>
                <input class="input input--required" type="password" name="password_1" placeholder="Введите пароль">
                <div class="wpcf7-not-valid-tip error_incorrect_password" style="display:none;"></div>
            </div>
            <div class="registration__input">
                <div class="registration__input--lable">Повторить пароль:</div>
                <input class="input input--required" type="password" name="password_2" placeholder="Введите пароль">
                <div class="wpcf7-not-valid-tip error_password_mismatch" style="display:none;"></div>
            </div>
            <button type="submit" class="button button--wide">Сменить пароль</button>
            <input type="checkbox" style="display: none;"  data-agree required>
            <div data-agree-custom class="noselect">
                <svg>
                    <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-checkbox"></use>
                </svg>
                <svg style="display: none;">
                    <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-checkbox-checked"></use>
                </svg>
                <div>
                    By clicking the button you agree to the terms of the <span>Privacy Policy</span>
                </div>
            </div>
            <span class="wpcf7-spinner"></span>
            <div class="wpcf7-response-output" aria-hidden="true" style="display:none;"></div>
        </form>
    </div>
</div>

<?php
//do_action( 'woocommerce_after_reset_password_form' );

