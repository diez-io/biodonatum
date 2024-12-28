<div class="request-success" data-success-for="registration--reset_password">
    <div class="registration__back">
        <svg>
            <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-vector"></use>
        </svg>
    </div>
    <div class="registration__title">Успешно!</div>
    <div class="registration__input--lable">На указанный E-mail отправлена ссылка для восстановления пароля</div>
    <div class="registration__title"><?= $args['email'] ?></div>
</div>
