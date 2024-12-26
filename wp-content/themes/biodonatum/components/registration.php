<div class="registration island registration--registration">
    <div class="registration__title">Регистрация</div>
    <form class="form" data-form>
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