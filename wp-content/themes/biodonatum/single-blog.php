<? get_header(); ?>

        <main class="main">
            <section class="section pt-50">
                <div class="container">
                    <div class="breadcrumbs mb-40">
                        <a href="<?= esc_url(biodonatum_url_with_lang(home_url('/'))); ?>" class="breadcrumbs__link"><?= get_static_content('home') ?></a>
                        <? $language_slug = defined('CURRENT_LANGUAGE') ? CURRENT_LANGUAGE : ''; ?>
                        <a href="<?= home_url("/$language_slug" . parse_url(get_permalink(get_page_by_path('blog')), PHP_URL_PATH)); ?>" class="breadcrumbs__link"><?= get_static_content('blog') ?></a>
                        <span class="breadcrumbs__link"><?= esc_html(get_field('blog_title')); ?></span>
                    </div>
                </div>
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
            <?$blog_text_4 = get_static_content('blog_text_4');?>
            <?if ($blog_text_4 != ''):?>
                <section class="section mt-100">
                    <div class="container">
                        <div class="island board">
                            <?=$blog_text_4?>
                        </div>
                    </div>
                </section>
                <?endif;?>
            <section class="section section--bg section--hero blog-detail__hero mt-200" style="background-image: url('<?= esc_url(get_field('blog_image')['url']); ?>')"></section>
        
            <? get_template_part('components/feedback'); ?>

        </main>

<? get_footer(); ?>
