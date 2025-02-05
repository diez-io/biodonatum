<?php
/*
Plugin Name: Custom Admin Filters
Description: Adds a taxonomy filter dropdown to the CPT admin list.
Version: 1.0
Author: Your Name
*/


function your_plugin_enqueue_admin_styles() {
    // Only enqueue on admin pages where posts are displayed
    if (is_admin()) {
        wp_enqueue_style('your-plugin-admin-styles', plugin_dir_url(__FILE__) . 'assets/admin-style.css');
    }
}

add_action('admin_enqueue_scripts', 'your_plugin_enqueue_admin_styles');

add_action('manage_posts_extra_tablenav', function ($which) {
    if ($which !== 'top') {
        return; // Only add the tabs above the posts table
    }

    global $typenow;

    // Check if the current post type has the 'taxonomy_language' taxonomy
    if (taxonomy_exists('taxonomy_language') && is_object_in_taxonomy($typenow, 'taxonomy_language')) {
        // Get terms for the 'taxonomy_language' taxonomy
        $terms = get_terms([
            'taxonomy'   => 'taxonomy_language',
            'hide_empty' => false,
        ]);

        if (!empty($terms)) {
            // Output the tabs instead of a select dropdown
            $selected = isset($_GET['taxonomy_language']) ? $_GET['taxonomy_language'] : '';
            echo '<div class="nav-tab-wrapper taxonomy-language-tabs">';
            echo '<a href="' . remove_query_arg('taxonomy_language') . '" class="nav-tab ' . ($selected !== '' ?: 'nav-tab-active') . '">' . esc_html__('All Languages', 'textdomain') . '</a>';

            foreach ($terms as $term) {
                echo '<a href="' . add_query_arg('taxonomy_language', $term->slug) . '" class="nav-tab ' . ($selected === $term->slug ? 'nav-tab-active' : '') . '">' . esc_html($term->name) . '</a>';
            }
            echo '</div>';
        }
    }
});

//add_action('pre_get_posts', function ($query) {
//    global $pagenow, $typenow;
//
//    // Apply the filter only on the admin post list page
//    if ($pagenow === 'edit.php' && is_admin() && isset($_GET['taxonomy_language'])) {
//        $query->query_vars['taxonomy_language'] = $_GET['taxonomy_language'];
//    }
//});
