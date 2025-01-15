<? get_header(); ?>

        <main class="main">
            <section class="section pt-50">
                <div class="container">
                    <div class="breadcrumbs mb-40">
                        <a href="<?= home_url(); ?>" class="breadcrumbs__link"><?= __('Home', 'static') ?></a>
                        <? $language_slug = defined('CURRENT_LANGUAGE') ? CURRENT_LANGUAGE : ''; ?>
                        <a href="<?= home_url("/$language_slug" . parse_url(get_permalink(get_page_by_path('blog')), PHP_URL_PATH)); ?>" class="breadcrumbs__link"><?= __('Blog', 'static') ?></a>
                        <span class="breadcrumbs__link"><?= esc_html(get_field('blog_title')); ?></span>
                    </div>
                </div>
            </section>
            <section class="section section--bg section--hero blog-detail__hero" style="background-image: url('<?= esc_url(get_field('blog_image')['url']); ?>')">
            </section>
            <section class="section pt-50">
                <div class="container">
                    <div class="blog-detail">
                        <div class="head">
                            <h2 class="title"><?= esc_html(get_field('blog_title')); ?></h2>
                        </div>
                        <div class="editor">
                            <?= get_field('blog_text') ?>
                        </div>

                        <? if ($epilogue = get_field('blog_epilogue')) : ?>
                            <div class="island">
                                <?= $epilogue ?>
                            </div>
                        <? endif; ?>
                    </div>
                </div>
            </section>

            <? get_template_part('components/feedback'); ?>

        </main>

<? get_footer(); ?>
