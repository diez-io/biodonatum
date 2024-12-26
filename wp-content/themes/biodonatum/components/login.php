<div class="registration island" style="display:none;">
    <div class="registration--login">
        <div class="registration__title">Войти</div>
        <div class="registration__new-user">
            Новый пользователь?
            <span class="registration__new-user__create">
                Создать учетную запись
            </span>
        </div>
        <form class="form" data-form>
            <div class="registration__input">
                <div class="registration__input--lable">Адрес электронной почты:</div>
                <input class="input" type="text" name="email" placeholder="Введите E-mail">
            </div>
            <div class="registration__input">
                <div class="registration__input--lable">Пароль:</div>
                <input class="input" type="password" name="password" placeholder="Введите пароль">
            </div>
            <div class="registration__btns">
                <button type="submit" class="button">Войти</button>
                <div class="registration__btns--forgot">Забыли пароль?</div>
            </div>
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
        </form>
    </div>
    <div class="registration--reset_password" style="display:none;">
        <div class="registration__title">Восстановить пароль</div>
        <form class="form" data-form>
            <div class="registration__input">
                <div class="registration__input--lable">Адрес электронной почты:</div>
                <input class="input" type="text" name="email" placeholder="Введите E-mail">
            </div>
            <button type="submit" class="button button--wide">Отправить ссылку для восстановления пароля</button>
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
        </form>
    </div>
    <div class="registration--registration" style="display:none;">
        <div class="registration__title">Регистрация</div>
        <form class="form" data-form action="<?= esc_url(admin_url('admin-ajax.php')) ?>">
            <? wp_nonce_field('woocommerce-register', 'woocommerce-register-nonce'); ?>
            <input type="hidden" name="action" value="custom_register">
            <div class="registration__input">
                <div class="registration__input--lable">Адрес электронной почты:</div>
                <input class="input" type="text" name="email" placeholder="Введите E-mail">
            </div>
            <button type="submit" class="button button--wide">Зарегистрироваться</button>
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
        </form>
    </div>
</div>
