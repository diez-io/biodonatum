<? get_header(); ?>

<main class="main main--index" style="background-image: url('<?= get_template_directory_uri(); ?>/assets/images/index-bg.png')">
    <? get_template_part('components/mainHero'); ?>
    <? get_template_part('components/teasers'); ?>
    <? get_template_part('components/productDetail', null, ['woo_id' => 210]); ?>
    <? get_template_part('components/partners'); ?>
    <? get_template_part('components/feedback'); ?>
</main>

<? get_footer(); ?>
