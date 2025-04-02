<?php
/*
Plugin Name: Portfolio Player
Description: A WordPress plugin for artists to create and manage their music portfolio pages.
Version: 1.0
Author: Shawn Su
License: GPL2
*/

if (!defined('ABSPATH')) {
    exit;
}

// 載入插件檔案
require_once plugin_dir_path(__FILE__) . 'includes/settings.php';
require_once plugin_dir_path(__FILE__) . 'includes/utils.php';
require_once plugin_dir_path(__FILE__) . 'includes/portfolio.php';
require_once plugin_dir_path(__FILE__) . 'includes/scripts.php';

// 註冊短代碼
function portfolio_player_register_shortcodes() {
    add_shortcode('portfolio_player_settings', 'portfolio_player_settings_shortcode');
    add_shortcode('portfolio_player_portfolio', 'portfolio_player_portfolio_shortcode');
}
add_action('init', 'portfolio_player_register_shortcodes');

// 載入前端腳本和樣式
function portfolio_player_enqueue_frontend_scripts() {
    portfolio_player_enqueue_scripts();
}
add_action('wp_enqueue_scripts', 'portfolio_player_enqueue_frontend_scripts');

// 啟用插件時執行初始化
register_activation_hook(__FILE__, 'portfolio_player_create_pages');
