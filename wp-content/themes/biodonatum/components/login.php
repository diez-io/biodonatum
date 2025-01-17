<div class="registration island" style="display:none;">
    <div class="registration--login">
        <div class="registration__title"><?= get_static_content('sign_in') ?></div>
        <div class="registration__new-user">
            <?= get_static_content('new_user') ?>
            <span class="registration__new-user__create">
                <?= get_static_content('sign_up') ?>
            </span>
        </div>
        <form class="form custom-woocommerce-form" action="<?= esc_url(admin_url('admin-ajax.php')) ?>">
            <input type="hidden" name="rememberme" value="forever">
            <?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
            <input type="hidden" name="action" value="custom_login">

            <div class="registration__input">
                <div class="registration__input--lable"><?= get_static_content('email') ?>:</div>
                <input class="input input--required" type="text" name="username" placeholder="<?= get_static_content('enter_email') ?>">
                <div class="wpcf7-not-valid-tip error_invalid_username" style="display:none;"></div>
            </div>
            <div class="registration__input">
                <div class="registration__input--lable"><?= get_static_content('password') ?>:</div>
                <input class="input input--required" type="password" name="password" placeholder="<?= get_static_content('enter_password') ?>">
                <div class="wpcf7-not-valid-tip error_incorrect_password" style="display:none;"></div>
            </div>
            <div class="registration__btns">
                <button type="submit" class="button"><?= get_static_content('sign_in') ?></button>
                <div class="registration__btns--forgot"><?= get_static_content('forgot_password') ?></div>
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
                    <?= get_static_content('by_clicking_the_button') ?>
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
        <div class="registration__title"><?= get_static_content('reset_password') ?></div>
        <form class="form custom-woocommerce-form" action="<?= esc_url(admin_url('admin-ajax.php')) ?>">
            <?php wp_nonce_field( 'lost_password', 'woocommerce-lost-password-nonce' ); ?>
            <input type="hidden" name="action" value="custom_lost_password">

            <div class="registration__input">
                <div class="registration__input--lable"><?= get_static_content('email') ?>:</div>
                <input class="input input--required" type="text" name="user_login" placeholder="<?= get_static_content('enter_email') ?>">
                <div class="wpcf7-not-valid-tip error_invalid_username error_key_generation_failed" style="display:none;"></div>
            </div>
            <button type="submit" class="button button--wide"><?= get_static_content('send_password_reset_link') ?></button>
            <input type="checkbox" style="display: none;"  data-agree>
            <div data-agree-custom class="noselect">
                <svg>
                    <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-checkbox"></use>
                </svg>
                <svg style="display: none;">
                    <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-checkbox-checked"></use>
                </svg>
                <div>
                    <?= get_static_content('by_clicking_the_button') ?>
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
        <div class="registration__title"><?= get_static_content('sign_up') ?></div>
        <form class="form custom-woocommerce-form" action="<?= esc_url(admin_url('admin-ajax.php')) ?>">
            <? wp_nonce_field('woocommerce-register', 'woocommerce-register-nonce'); ?>
            <input type="hidden" name="action" value="custom_register">
            <div class="registration__input">
                <div class="registration__input--lable"><?= get_static_content('email') ?>:</div>
                <input class="input input--required" type="text" name="email" placeholder="<?= get_static_content('enter_email') ?>">
                <div class="wpcf7-not-valid-tip error_registration-error-invalid-email error_registration-error-email-exists" style="display:none;"></div>
            </div>
            <button type="submit" class="button button--wide"><?= get_static_content('sign_up') ?></button>
            <input type="checkbox" style="display: none;"  data-agree>
            <div data-agree-custom class="noselect">
                <svg>
                    <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-checkbox"></use>
                </svg>
                <svg style="display: none;">
                    <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-checkbox-checked"></use>
                </svg>
                <div>
                    <?= get_static_content('by_clicking_the_button') ?>
                </div>
            </div>
            <span class="wpcf7-spinner"></span>
            <div class="wpcf7-response-output" aria-hidden="true" style="display:none;"></div>
        </form>
    </div>
</div>
