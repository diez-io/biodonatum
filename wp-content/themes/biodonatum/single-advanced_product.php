<? get_header(); ?>

        <main class="main">
            <section class="section pt-50">
                <div class="container">
                    <div class="breadcrumbs mb-40">
                        <a href="<?= home_url(); ?>" class="breadcrumbs__link"><?= __('Home', 'static') ?></a>
                        <a href="<?= get_permalink( get_option('woocommerce_shop_page_id')) ?>" class="breadcrumbs__link"><?= __('Shop', 'static') ?></a>
                        <span class="breadcrumbs__link"><?= get_field('advanced_product_name') ?></span>
                    </div>
                </div>
            </section>
            <div class="product">
                <? get_template_part('components/productDetail'); ?>
                <? get_template_part('components/reviews'); ?>
            </div>

            <? get_template_part('components/feedback'); ?>
        </main>

<? get_footer(); ?>
