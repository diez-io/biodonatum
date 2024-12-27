<div class="request-success" data-success-for="registration--registration">
    <div class="registration__title">Регистрация прошла успешно!</div>
    <div class="registration__input--lable">На указанный E-mail отправлена ссылка для создания пароля</div>
    <div class="registration__title"><?= $user->user_email ?></div>
    <a href="<?= get_permalink( get_option('woocommerce_myaccount_page_id')) ?>" class="button button--wide">Перейти в профиль</a>
</div>
