<?php
function google_fonts() {?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100..900&display=swap" rel="stylesheet">
<?}

function theme_enqueue_assets() {
    // Enqueue CSS
    wp_enqueue_style('my-theme-style', get_template_directory_uri() . '/css/main.css');

    // Enqueue JS
    wp_enqueue_script('my-theme-script', get_template_directory_uri() . '/js/bundle.js', array(), null, true); // true loads it in the footer
}

add_action('wp_enqueue_scripts', 'theme_enqueue_assets');
add_action('wp_head', 'google_fonts');
