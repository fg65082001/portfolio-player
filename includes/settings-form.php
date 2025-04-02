<?php
/*
 * Portfolio Player - Settings Form Handler
 * Filename: includes/settings-form.php
 * Description: Handles the settings form submission and updates user meta for the Portfolio Player plugin.
 */

if (!defined('ABSPATH')) {
    exit;
}

function portfolio_player_handle_settings_form($user_id) {
    $portfolio_url = '';
    $show_url_section = false;
    $error_message = '';

    if (isset($_POST['portfolio_player_save_settings'])) {
        $songs = [];
        if (isset($_POST['songs'])) {
            foreach ($_POST['songs'] as $song) {
                $songs[] = [
                    'genre' => sanitize_text_field($song['genre'] ?? ''),
                    'title' => sanitize_text_field($song['title']),
                    'artist' => sanitize_text_field($song['artist']),
                    'url' => esc_url_raw($song['url'])
                ];
            }
        }
        update_user_meta($user_id, 'portfolio_player_songs', $songs);

        $colors = [
            'background' => sanitize_hex_color($_POST['player_background_color'] ?? '#282828'),
            'highlight' => sanitize_hex_color($_POST['highlight_color'] ?? '#1db954'),
            'artist' => sanitize_hex_color($_POST['artist_color'] ?? '#b3b3b3'),
            'title' => sanitize_hex_color($_POST['title_color'] ?? '#ffffff')
        ];
        update_user_meta($user_id, 'portfolio_player_colors', $colors);

        $styles = [
            'padding' => 20,
            'artist_font_size' => 13,
            'title_font_size' => 16
        ];
        update_user_meta($user_id, 'portfolio_player_styles', $styles);

        $artist_photo = esc_url_raw($_POST['artist_photo'] ?? '');
        $artist_video = esc_url_raw($_POST['artist_video'] ?? '');
        $extra_image_1 = esc_url_raw($_POST['extra_image_1'] ?? '');
        $extra_image_2 = esc_url_raw($_POST['extra_image_2'] ?? '');
        $extra_image_3 = esc_url_raw($_POST['extra_image_3'] ?? '');
        $artist_bio = wp_kses_post($_POST['artist_bio'] ?? '');
        $display_name = sanitize_text_field($_POST['display_name'] ?? '');
        $tagline = sanitize_text_field($_POST['tagline'] ?? '');
        $email = sanitize_email($_POST['email'] ?? '');
        $youtube_url = esc_url_raw($_POST['youtube_url'] ?? '');
        $instagram_url = esc_url_raw($_POST['youtube_url'] ?? '');
        $threads_url = esc_url_raw($_POST['threads_url'] ?? '');
        $website_url = esc_url_raw($_POST['website_url'] ?? '');
        $custom_url_suffix = sanitize_text_field($_POST['custom_url_suffix'] ?? '');
        $social_icon_mode = sanitize_text_field($_POST['social_icon_mode'] ?? 'dark');

        if (!empty($artist_photo) && !empty($artist_video)) {
            $error_message = '<div class="error"><p>只能二選一唷！請選擇照片或影片之一。</p></div>';
        } else {
            update_user_meta($user_id, 'portfolio_player_artist_photo', $artist_photo);
            update_user_meta($user_id, 'portfolio_player_artist_video', $artist_video);
            update_user_meta($user_id, 'portfolio_player_extra_image_1', $extra_image_1);
            update_user_meta($user_id, 'portfolio_player_extra_image_2', $extra_image_2);
            update_user_meta($user_id, 'portfolio_player_extra_image_3', $extra_image_3);
            update_user_meta($user_id, 'portfolio_player_artist_bio', $artist_bio);
            update_user_meta($user_id, 'portfolio_player_display_name', $display_name);
            update_user_meta($user_id, 'portfolio_player_tagline', $tagline);
            update_user_meta($user_id, 'portfolio_player_email', $email);
            update_user_meta($user_id, 'portfolio_player_youtube_url', $youtube_url);
            update_user_meta($user_id, 'portfolio_player_instagram_url', $instagram_url);
            update_user_meta($user_id, 'portfolio_player_threads_url', $threads_url);
            update_user_meta($user_id, 'portfolio_player_website_url', $website_url);
            update_user_meta($user_id, 'portfolio_player_social_icon_mode', $social_icon_mode);

            if (!empty($custom_url_suffix)) {
                $parent_page = get_page_by_path('music');
                $parent_page_id = $parent_page ? $parent_page->ID : 0;

                $existing_portfolio_page = get_page_by_path('music/' . $custom_url_suffix, OBJECT, 'page');
                $existing_portfolio_page_id = get_user_meta($user_id, 'portfolio_player_page_id', true);

                if ($existing_portfolio_page && $existing_portfolio_page->ID != $existing_portfolio_page_id) {
                    $error_message = '<div class="error"><p>此 URL 已被使用，請選擇另一個。</p></div>';
                } else {
                    update_user_meta($user_id, 'portfolio_player_custom_url_suffix', $custom_url_suffix);

                    $post_title = !empty($display_name) && !empty($tagline) ? $display_name . ' - ' . $tagline : 'Music Portfolio - ' . $custom_url_suffix;

                    if ($existing_portfolio_page_id) {
                        wp_update_post([
                            'ID' => $existing_portfolio_page_id,
                            'post_name' => $custom_url_suffix,
                            'post_parent' => $parent_page_id,
                            'post_title' => $post_title,
                            'post_content' => '[portfolio_player_portfolio user_id="' . $user_id . '"]'
                        ]);
                    } else {
                        $new_portfolio_page_id = wp_insert_post([
                            'post_title' => $post_title,
                            'post_name' => $custom_url_suffix,
                            'post_status' => 'publish',
                            'post_type' => 'page',
                            'post_parent' => $parent_page_id,
                            'post_content' => '[portfolio_player_portfolio user_id="' . $user_id . '"]'
                        ]);
                        update_user_meta($user_id, 'portfolio_player_page_id', $new_portfolio_page_id);
                    }
                    $portfolio_url = home_url('/music/' . $custom_url_suffix);
                    $show_url_section = true;
                }
            }
        }
    }

    if (!empty($custom_url_suffix)) {
        $portfolio_url = home_url('/music/' . $custom_url_suffix);
        $show_url_section = true;
    }

    // 獲取綁定 Email 和金鑰
    $bound_email = '';
    $masked_key = '';
    $key = get_user_meta($user_id, 'portfolio_player_key_portfolio-player', true);
    if ($key) {
        $api_url = 'https://ourdaysrecords.com/boss/wp-json/odr-key/v1/check';
        $response = wp_remote_get($api_url . '?key=' . urlencode($key), array(
            'timeout' => 15,
            'sslverify' => false,
        ));
        if (!is_wp_error($response)) {
            $body = json_decode(wp_remote_retrieve_body($response), true);
            if (isset($body['email'])) {
                $bound_email = $body['email'];
            }
        }

        // 遮罩金鑰（保留前4個和後2個字元）
        $key_length = strlen($key);
        if ($key_length > 6) {
            $masked_key = substr($key, 0, 4) . str_repeat('*', $key_length - 6) . substr($key, -2);
        } else {
            $masked_key = $key; // 若太短則不遮罩
        }
    }

    $response = [
        'portfolio_url' => $portfolio_url,
        'show_url_section' => $show_url_section,
        'songs' => get_user_meta($user_id, 'portfolio_player_songs', true) ?: [],
        'colors' => get_user_meta($user_id, 'portfolio_player_colors', true) ?: [
            'background' => '#282828',
            'highlight' => '#1db954',
            'artist' => '#b3b3b3',
            'title' => '#ffffff'
        ],
        'styles' => get_user_meta($user_id, 'portfolio_player_styles', true) ?: [
            'padding' => 20,
            'artist_font_size' => 13,
            'title_font_size' => 16
        ],
        'artist_photo' => get_user_meta($user_id, 'portfolio_player_artist_photo', true) ?: '',
        'artist_video' => get_user_meta($user_id, 'portfolio_player_artist_video', true) ?: '',
        'extra_image_1' => get_user_meta($user_id, 'portfolio_player_extra_image_1', true) ?: '',
        'extra_image_2' => get_user_meta($user_id, 'portfolio_player_extra_image_2', true) ?: '',
        'extra_image_3' => get_user_meta($user_id, 'portfolio_player_extra_image_3', true) ?: '',
        'artist_bio' => get_user_meta($user_id, 'portfolio_player_artist_bio', true) ?: '',
        'display_name' => get_user_meta($user_id, 'portfolio_player_display_name', true) ?: '',
        'tagline' => get_user_meta($user_id, 'portfolio_player_tagline', true) ?: '',
        'email' => get_user_meta($user_id, 'portfolio_player_email', true) ?: '',
        'youtube_url' => get_user_meta($user_id, 'portfolio_player_youtube_url', true) ?: '',
        'instagram_url' => get_user_meta($user_id, 'portfolio_player_instagram_url', true) ?: '',
        'threads_url' => get_user_meta($user_id, 'portfolio_player_threads_url', true) ?: '',
        'website_url' => get_user_meta($user_id, 'portfolio_player_website_url', true) ?: '',
        'custom_url_suffix' => $custom_url_suffix,
        'social_icon_mode' => get_user_meta($user_id, 'portfolio_player_social_icon_mode', true) ?: 'dark',
        'bound_email' => $bound_email, // 新增綁定 Email
        'masked_key' => $masked_key    // 新增遮罩金鑰
    ];

    if (!empty($error_message)) {
        $response['error_message'] = $error_message;
    }

    return $response;
}