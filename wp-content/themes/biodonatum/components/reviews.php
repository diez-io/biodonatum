<?

$order_raw = isset($_GET['order']) ? sanitize_text_field($_GET['order']) : 'featured';
$allowed_orders = ['featured','newest','highest','lowest'];
$order = in_array($order_raw, $allowed_orders, true) ? $order_raw : 'featured';

$query_args = [
    'post_type'      => 'review',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'tax_query' => [
        [
            'taxonomy' => 'taxonomy_language',
            'field'    => 'slug',
            'terms'    => $_SESSION['lang'],
        ],
    ],
];

if (get_query_var('product_link')) {
    $query_args['meta_query'] = [[
        'key'     => 'product_link',
        'value'   => (int)get_query_var('product_link'),
        'compare' => '=',
    ]];
}

switch ($order) {
    case 'highest':
        $query_args['meta_key'] = 'rating';
        $query_args['orderby'] = ['meta_value_num' => 'DESC', 'date' => 'DESC'];
        break;

    case 'lowest':
        $query_args['meta_key'] = 'rating';
        $query_args['orderby'] = ['meta_value_num' => 'ASC', 'date' => 'DESC'];
        break;

    default:
        $query_args['orderby'] = ['date' => 'DESC'];
        break;
}

$reviews_query = new WP_Query($query_args);

$columns = array_fill(0, 4, []);
foreach ($reviews_query->posts ?? [] as $i => $review)
    $columns[$i % 4][] = $review;

$total_reviews = (int)$reviews_query->found_posts;

$sort_items = [
    'featured' => 'sort_by_featured',
    'newest'   => 'sort_by_newest',
    'highest'  => 'sort_by_highest_rating',
    'lowest'   => 'sort_by_lowest_rating',
];
    
?>
<section class="section pt-50">
    <div class="container">
        <div class="reviews">
            <div class="head">
                <h2 class="title"><?= get_static_content('reviews') ?></h2>
            </div>
            <div class="reviews__filter">
                <div class="reviews__filter__something">
                    <?=$total_reviews?> <?= get_static_content('reviews') ?>
                    <?/*<svg>
                        <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-chevrone"></use>
                    </svg>*/?>
                </div>
                <div class="reviews__filter__popup popup">
                    <div class="reviews__filter__popup-btn popup-btn">
                        <svg>
                            <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-filter"></use>
                        </svg>
                    </div>
                    <div class="island reviews__filter__popup-body popup-body">
                        <div class="reviews__filter__title">
                            <?= get_static_content('sort_by') ?>:
                        </div>
                        <?foreach ($sort_items as $key => $static_key):?>
                            <a 
                                href="<?=esc_url(add_query_arg(['order' => $key]))?>" 
                                class="reviews__filter__item <?=($order == $key) ? ' is-active' : ''?>" 
                                data-order="<?php echo esc_attr($key); ?>"
                                role="link"
                            >
                                <?=get_static_content($static_key); ?>
                            </a>
                        <?endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="reviews__tiles">
                <?foreach ($columns as $column):?>
                    <div class="reviews__tiles__col load-more-items">
                        <?foreach ($column as $review):?>
                            <?
                            global $post;
                            $post = $review;

                            $review_id = get_the_ID();
                            $rating = intval(get_field('rating', $review_id));

                            ?>
                            <article class="article-reviews">
                                <div class="article-reviews__wrapper">
                                    <picture class="article-reviews__picture">
                                        <?the_post_thumbnail('medium');?>
                                    </picture>
                                    <div class="article-reviews__content">
                                        <div class="article-reviews__title">
                                            <div class="article-reviews__author"><?=the_title()?></div>
                                            <div class="article-reviews__date"><?get_the_date();?></div>
                                        </div>
                                        <div class="article-reviews__rating">
                                            <?for ($i = 0; $i < 5; $i++):?>
                                                <?if ($rating > $i):?>
                                                    <svg class="star">
                                                        <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-star"></use>
                                                    </svg>
                                                <?else:?>
                                                    <svg class="star">
                                                        <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-star-not-filled"></use>
                                                    </svg>
                                                <?endif;?>
                                            <?endfor;?>
                                        </div>
                                        <div class="article-reviews__text"><?=$review->post_content?></div>
                                    </div>
                                </div>
                            </article>
                        <?endforeach;?>
                    </div>
                <?endforeach;?>
            </div>
            <div class="button load-more-btn">
                <?= get_static_content('load_more') ?>
            </div>
        </div>
    </div>
</section>

<?wp_reset_postdata();?>