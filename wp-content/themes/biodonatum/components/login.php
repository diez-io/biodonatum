<div class="registration island" style="display:none;">
    <div class="registration--login">
        <div class="registration__title">Войти</div>
        <div class="registration__new-user">
            Новый пользователь?
            <span class="registration__new-user__create">
                Создать учетную запись
            </span>
        </div>
        <form class="form custom-woocommerce-form" action="<?= esc_url(admin_url('admin-ajax.php')) ?>">
            <input type="hidden" name="rememberme" value="forever">
            <?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
            <input type="hidden" name="action" value="custom_login">

            <div class="registration__input">
                <div class="registration__input--lable">Адрес электронной почты:</div>
                <input class="input input--required" type="text" name="username" placeholder="Введите E-mail">
                <div class="wpcf7-not-valid-tip error_invalid_username" style="display:none;"></div>
            </div>
            <div class="registration__input">
                <div class="registration__input--lable">Пароль:</div>
                <input class="input input--required" type="password" name="password" placeholder="Введите пароль">
                <div class="wpcf7-not-valid-tip error_incorrect_password" style="display:none;"></div>
            </div>
            <div class="registration__btns">
                <button type="submit" class="button">Войти</button>
                <div class="registration__btns--forgot">Забыли пароль?</div>
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
                    By clicking the button you agree to the terms of the <span>Privacy Policy</span>
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
        <div class="registration__title">Восстановить пароль</div>
        <form class="form custom-woocommerce-form" action="<?= esc_url(admin_url('admin-ajax.php')) ?>">
            <?php wp_nonce_field( 'lost_password', 'woocommerce-lost-password-nonce' ); ?>
            <input type="hidden" name="action" value="custom_lost_password">

            <div class="registration__input">
                <div class="registration__input--lable">Адрес электронной почты:</div>
                <input class="input input--required" type="text" name="user_login" placeholder="Введите E-mail">
                <div class="wpcf7-not-valid-tip error_invalid_username error_key_generation_failed" style="display:none;"></div>
            </div>
            <button type="submit" class="button button--wide">Отправить ссылку для восстановления пароля</button>
            <input type="checkbox" style="display: none;"  data-agree>
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
    <div class="registration--registration" style="display:none;">
        <div class="registration__back">
            <svg>
                <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-vector"></use>
            </svg>
        </div>
        <div class="registration__title">Регистрация</div>
        <form class="form custom-woocommerce-form" action="<?= esc_url(admin_url('admin-ajax.php')) ?>">
            <? wp_nonce_field('woocommerce-register', 'woocommerce-register-nonce'); ?>
            <input type="hidden" name="action" value="custom_register">
            <div class="registration__input">
                <div class="registration__input--lable">Адрес электронной почты:</div>
                <input class="input input--required" type="text" name="email" placeholder="Введите E-mail">
                <div class="wpcf7-not-valid-tip error_registration-error-invalid-email error_registration-error-email-exists" style="display:none;"></div>
            </div>
            <button type="submit" class="button button--wide">Зарегистрироваться</button>
            <input type="checkbox" style="display: none;"  data-agree>
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
