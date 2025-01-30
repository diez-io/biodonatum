<modal class="remove-profile-img-modal">
    <div class="island">
        <h2>
            <?= get_static_content('remove_profile_picture_sure') ?>
        </h2>
        <div class="remove-profile-img-modal__btns">
            <form action="" method="post">
                <? wp_nonce_field('profile_picture_remove_action', 'profile_picture_remove_nonce'); ?>
                <button class="button button--wide" type="submit" name="remove_profile_picture">
                    <?= get_static_content('remove') ?>
                </button>
            </form>
            <button class="button button--wide">
                <?= get_static_content('cancel') ?>
            </button>
        </div>
    </div>
</modal>
