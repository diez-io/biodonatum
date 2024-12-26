
jQuery(function ($) {
    const currentQuantity = new Map();

    if ($('.product-detail').length) {
        currentQuantity.set(0, parseInt($('.quantity_panel input').val()));
        var isCart = false;
    }
    else {
        $('[data-cart_item_key]').each(function() {
            currentQuantity.set($(this).data('cart_item_key'), parseInt($(this).find('.quantity_panel input').val()));
        });

        var isCart = true;
    }

    function updateCartCount(count = NaN) {
        const $cartCount = $('.cart_count');

        if (isNaN) {
            $.get(wc_cart.ajax_url + '?action=get_cart_count', function (response) {
                $cartCount.toggle(response.cart_count > 0);
                $cartCount.text(response.cart_count);
            });
        }
        else {
            $cartCount.toggle(count > 0);
            $cartCount.text(count);
        }
    }

    function updateCartTotals() {
        $.get(wc_cart.ajax_url + '?action=get_cart_totals', function (response) {
            $('.cart__total--subtotal').html(response.cart_subtotal);
            $('.cart__total--discount').html(response.cart_discount);
            $('.cart__total--total').html(response.cart_total);
        });
    }

    function updateCartItem(cartItemKey) {
        const $item = $(`[data-cart_item_key="${cartItemKey}"]`);

        $.ajax({
            url: wc_cart.ajax_url,
            type: 'POST',
            data: {
                action: 'update_cart_item',
                cart_item_key: cartItemKey,
                quantity: currentQuantity.get(cartItemKey),
            },
            success: function (response) {
                if (response.success) {
                    updateCartCount(response.data.cart_count);
                    $item.find('.card-item__subtotal').html(response.data.item_subtotal);
                    $item.find('.card-item__discount').html(response.data.item_discount);
                    $('.cart__total--subtotal').html(response.data.cart_subtotal);
                    $('.cart__total--discount').html(response.data.cart_discount);
                    $('.cart__total--total').html(response.data.cart_total);
                }
                else {
                    alert('Failed to update the cart item.');
                }
            }
        });
    }

    $('.quantity_panel--minus, .quantity_panel--plus').on('click', function(e) {
        if (isCart) {
            var key = $(this).closest('tr').data('cart_item_key');
        }
        else {
            var key = 0;
        }

        const $input = $(this).siblings('input');
        const previousQuantity = currentQuantity.get(key);

        if ($(this).hasClass('quantity_panel--minus')) {
            currentQuantity.set(key, previousQuantity - (previousQuantity === 1 ? 0 : 1));
        }
        else {
            currentQuantity.set(key, previousQuantity + 1);
        }

        isCart && updateCartItem(key);

        $input.val(currentQuantity.get(key));
    });

    $('.quantity_panel input').on('input', function(e) {
        if (isCart) {
            var key = $(this).closest('tr').data('cart_item_key');
        }
        else {
            var key = 0;
        }

        if ($(this).val() === '') {
            $(this).val(1);
            currentQuantity.set(key, 1);
            isCart && updateCartItem(key);

            return;
        }

        const newValue = parseInt($(this).val());

        if (isNaN(newValue)) {
            $(this).val(currentQuantity.get(key));
        }
        else if (newValue === 0) {
            $(this).val(1);
            currentQuantity.set(key, 1);
            isCart && updateCartItem(key);
        }
        else {
            $(this).val(newValue);
            currentQuantity.set(key, newValue);
            isCart && updateCartItem(key);
        }

    });

    // add to cart
    $('.add-to-cart-button').on('click', function (e) {
        e.preventDefault();
        const productId = jQuery(this).data('product-id');

        $.ajax({
            url: wc_add_to_cart_params.ajax_url,
            type: 'POST',
            data: {
                action: 'woocommerce_add_to_cart',
                product_id: productId,
                quantity: currentQuantity,
            },
            success: function (response) {
                if (response.error) {
                    jQuery('#cart-response').html('Error: ' + response.message);
                } else {
                    updateCartCount();
                    jQuery('#cart-response').html('Product added to cart!');
                }
            }
        });
    });

    // remove item from cart
    $('.cart__table--del').on('click', function (e) {
        const $button = $(this);
        const $cartItem = $button.closest('tr');
        const cartItemKey = $cartItem.data('cart_item_key');

        if (!cartItemKey) {
            console.error('Cart item key or remove URL missing.');
            return;
        }

        $.ajax({
            url: wc_add_to_cart_params.ajax_url,
            type: 'POST',
            data: {
                action: 'woocommerce_remove_from_cart',
                cart_item_key: cartItemKey,
            },
            success: function (response) {
                if (response.error) {
                    consol.error(response.message);
                } else {
                    updateCartCount();
                    updateCartTotals();

                    $cartItem.remove();
                    currentQuantity.delete(cartItemKey);
                }
            }
        });
    });

    function applyCoupons(data) {
        $('.cart__total--subtotal').html(data.cart_subtotal);
        $('.cart__total--total').html(data.cart_total);

        $coupons = $('.cart__total--coupons');
        $discount = $('.cart__total--discount');
        $coupons.html('');

        if (data.coupon_applied.length > 0) {

            $coupons.show();
            $discount.show();
            $coupons.prev().show();
            $discount.prev().show();

            data.coupon_applied.forEach(coupon => {
                $('.cart__total--coupons').append(`
                    <div class="cart__total--coupon">
                        <div class="cart__total--coupon-name">
                            ${coupon}
                        </div>
                        <div class="cart__total--coupon-del">
                            <svg>
                                <use xlink:href="/wp-content/themes/biodonatum/assets/sprite.svg#icon-x"></use>
                            </svg>
                        </div>
                    </div>
                `);
            });

            $('.cart__total--discount').html(data.cart_discount);
        }
        else {
            $coupons.hide();
            $discount.hide();
            $coupons.prev().hide();
            $discount.prev().hide();
        }

        data.cart_items.forEach(item => {
            const $item = $(`[data-cart_item_key="${item.cart_item_key}"]`);
            $item.find('.card-item__discount').html(item.discount);
        });
    }

    // apply promocode
    $('.cart__promocode form').on('submit', function (e) {
        e.preventDefault();

        const couponCode = $(this).find('[name="coupon_code"').val();

        $.ajax({
            url: wc_cart.ajax_url, // WooCommerce AJAX URL
            type: 'POST',
            data: {
                action: 'apply_custom_coupon', // WooCommerce default action for applying coupons
                coupon_code: couponCode,
            },
            success: function (response) {
                if (response.success) {
                    //$('#promocode-message').html('<span style="color: green;">Coupon applied successfully!</span>');
                    // Optionally reload cart totals or update the UI
                    //location.reload(); // Reload page to reflect changes
                    console.log('success');

                    applyCoupons(response.data);
                }
                else {
                    //$('#promocode-message').html('<span style="color: red;">' + response.data.message + '</span>');
                    console.error('error');
                }
            },
            error: function () {
                console.error('error 2');
                //$('#promocode-message').html('<span style="color: red;">Error applying coupon.</span>');
            },
        });
    });

    // remove promocode
    $('.cart__total--coupon-del').on('click', function (e) {
        const couponCode = $(this).siblings('.cart__total--coupon-name').text();

        $.ajax({
            url: wc_cart.ajax_url, // WooCommerce AJAX URL
            type: 'POST',
            data: {
                action: 'remove_custom_coupon', // WooCommerce default action for applying coupons
                coupon_code: couponCode,
            },
            success: function (response) {
                if (response.success) {
                    //$('#promocode-message').html('<span style="color: green;">Coupon applied successfully!</span>');
                    // Optionally reload cart totals or update the UI
                    //location.reload(); // Reload page to reflect changes
                    console.log('success');

                    applyCoupons(response.data);
                } else {
                    //$('#promocode-message').html('<span style="color: red;">' + response.data.message + '</span>');
                    console.error('error');
                }
            },
            error: function () {
                console.error('error 2');
                //$('#promocode-message').html('<span style="color: red;">Error applying coupon.</span>');
            },
        });
    });
});
