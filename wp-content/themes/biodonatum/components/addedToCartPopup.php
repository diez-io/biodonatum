<div class="product-added-to-cart-popup">
    <div class="island">
        <div class="product-added-to-cart-popup__close-icon">
            <svg>
                <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-x"></use>
            </svg>
        </div>
        <h2>
            <?= get_static_content('product_added_to_cart') ?>
        </h2>
        <div class="product-added-to-cart-popup__btns">
            <a href="<?= esc_url(biodonatum_url_with_lang(wc_get_cart_url())); ?>" class="button button--wide">
                <?= get_static_content('go_to_cart') ?>
            </a>
        </div>
    </div>
</div>
