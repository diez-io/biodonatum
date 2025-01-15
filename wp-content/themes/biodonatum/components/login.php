<div class="registration island" style="display:none;">
    <div class="registration--login">
        <div class="registration__title"><?= __('Sign In', 'static') ?></div>
        <div class="registration__new-user">
            <?= __('New user?', 'static') ?>
            <span class="registration__new-user__create">
                <?= __('Sign Up', 'static') ?>
            </span>
        </div>
        <form class="form custom-woocommerce-form" action="<?= esc_url(admin_url('admin-ajax.php')) ?>">
            <input type="hidden" name="rememberme" value="forever">
            <?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
            <input type="hidden" name="action" value="custom_login">

            <div class="registration__input">
                <div class="registration__input--lable"><?= __('E-mail', 'static') ?>:</div>
                <input class="input input--required" type="text" name="username" placeholder="<?= __('Enter your e-mail', 'static') ?>">
                <div class="wpcf7-not-valid-tip error_invalid_username" style="display:none;"></div>
            </div>
            <div class="registration__input">
                <div class="registration__input--lable"><?= __('Password', 'static') ?>:</div>
                <input class="input input--required" type="password" name="password" placeholder="<?= __('Enter your password', 'static') ?>">
                <div class="wpcf7-not-valid-tip error_incorrect_password" style="display:none;"></div>
            </div>
            <div class="registration__btns">
                <button type="submit" class="button"><?= __('Sign In', 'static') ?></button>
                <div class="registration__btns--forgot"><?= __('Forgot your password?', 'static') ?></div>
            </div>
            <input type="checkbox" style="display: none;"  data-agree>
            <div data-agree-custom class="noselect">
                <svg>
                    <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-checkbox"></use>
                </svg>
                <svg style="display: none;">
                    <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-checkbox-checked"></use>
                </svg>
                <div>
                    <?= __('By clicking the button you agree to the terms of the <span>Privacy Policy</span>', 'static') ?>
                </div>
            </div>
            <span class="wpcf7-spinner"></span>
            <div class="wpcf7-response-output" aria-hidden="true" style="display:none;"></div>
        </form>
    </div>
    <div class="registration--reset_password" style="display:none;">
        <div class="registration__back">
            <svg>
                <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-vector"></use>
            </svg>
        </div>
        <div class="registration__title"><?= __('Reset Password', 'static') ?></div>
        <form class="form custom-woocommerce-form" action="<?= esc_url(admin_url('admin-ajax.php')) ?>">
            <?php wp_nonce_field( 'lost_password', 'woocommerce-lost-password-nonce' ); ?>
            <input type="hidden" name="action" value="custom_lost_password">

            <div class="registration__input">
                <div class="registration__input--lable"><?= __('E-mail', 'static') ?>:</div>
                <input class="input input--required" type="text" name="user_login" placeholder="<?= __('Enter your e-mail', 'static') ?>">
                <div class="wpcf7-not-valid-tip error_invalid_username error_key_generation_failed" style="display:none;"></div>
            </div>
            <button type="submit" class="button button--wide"><?= __('Send password reset link', 'static') ?></button>
            <input type="checkbox" style="display: none;"  data-agree>
            <div data-agree-custom class="noselect">
                <svg>
                    <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-checkbox"></use>
                </svg>
                <svg style="display: none;">
                    <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-checkbox-checked"></use>
                </svg>
                <div>
                    <?= __('By clicking the button you agree to the terms of the <span>Privacy Policy</span>', 'static') ?>
                </div>
            </div>
            <span class="wpcf7-spinner"></span>
            <div class="wpcf7-response-output" aria-hidden="true" style="display:none;"></div>
        </form>
    </div>
    <div class="registration--registration" style="display:none;">
        <div class="registration__back">
            <svg>
                <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-vector"></use>
            </svg>
        </div>
        <div class="registration__title"><?= __('Sign Up', 'static') ?></div>
        <form class="form custom-woocommerce-form" action="<?= esc_url(admin_url('admin-ajax.php')) ?>">
            <? wp_nonce_field('woocommerce-register', 'woocommerce-register-nonce'); ?>
            <input type="hidden" name="action" value="custom_register">
            <div class="registration__input">
                <div class="registration__input--lable"><?= __('E-mail', 'static') ?>:</div>
                <input class="input input--required" type="text" name="email" placeholder="<?= __('Enter your e-mail', 'static') ?>">
                <div class="wpcf7-not-valid-tip error_registration-error-invalid-email error_registration-error-email-exists" style="display:none;"></div>
            </div>
            <button type="submit" class="button button--wide"><?= __('Sign Up', 'static') ?></button>
            <input type="checkbox" style="display: none;"  data-agree>
            <div data-agree-custom class="noselect">
                <svg>
                    <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-checkbox"></use>
                </svg>
                <svg style="display: none;">
                    <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-checkbox-checked"></use>
                </svg>
                <div>
                    <?= __('By clicking the button you agree to the terms of the <span>Privacy Policy</span>', 'static') ?>
                </div>
            </div>
            <span class="wpcf7-spinner"></span>
            <div class="wpcf7-response-output" aria-hidden="true" style="display:none;"></div>
        </form>
    </div>
</div>
