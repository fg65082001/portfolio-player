<?php
/*
 * Portfolio Player - Utility Functions
 * Filename: includes/utils.php
 * Description: Handles utility functions like page creation for the Portfolio Player plugin.
 */

if (!defined('ABSPATH')) {
    exit;
}

// 在插件啟用時創建必要的頁面
function portfolio_player_create_pages() {
    // 創建 artist-settings 頁面
    $settings_page = get_page_by_path('artist-settings');
    if (!$settings_page) {
        $settings_page_id = wp_insert_post([
            'post_title' => 'Artist Settings',
            'post_name' => 'artist-settings',
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_content' => '[portfolio_player_settings]'
        ]);
    }

    // 創建 music 父頁面
    $music_page = get_page_by_path('music');
    if (!$music_page) {
        $music_page_id = wp_insert_post([
            'post_title' => 'Music',
            'post_name' => 'music',
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_content' => '<!-- This is a parent page for artist portfolios -->'
        ]);
    }
}

register_activation_hook(dirname(__DIR__) . '/portfolio-player.php', 'portfolio_player_create_pages');