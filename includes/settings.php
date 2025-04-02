<?php
/*
 * Portfolio Player - Settings Shortcode
 * Filename: includes/settings.php
 * Description: Handles the settings shortcode for the Portfolio Player plugin.
 */

if (!defined('ABSPATH')) {
    exit;
}

function portfolio_player_settings_shortcode() {
    if (!is_user_logged_in()) {
        $login_url = wp_login_url(home_url('/verify-key'));
        return '<p>請<a href="' . esc_url($login_url) . '">登入</a>或註冊以設定 Portfolio Player。</p>';
    }

    $user_id = get_current_user_id();
    $auth_check = apply_filters('portfolio_player_auth_check', true, $user_id);

    if (!$auth_check) {
        wp_redirect(home_url('/verify-key'));
        exit;
    }

    require_once dirname(__FILE__) . '/settings-form.php';
    $data = portfolio_player_handle_settings_form($user_id);

    require_once dirname(__FILE__) . '/settings-display.php';
    $output = portfolio_player_display_settings($data);

    wp_enqueue_script('portfolio-player-settings-scripts', plugin_dir_url(__FILE__) . 'settings-scripts.js', array('jquery'), null, true);
    wp_enqueue_style('portfolio-player-settings-styles', plugin_dir_url(__FILE__) . 'settings-styles.css');

    return $output;
}

add_shortcode('portfolio_player_settings', 'portfolio_player_settings_shortcode');