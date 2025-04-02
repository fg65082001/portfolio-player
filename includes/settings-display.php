<?php
/*
 * Portfolio Player - Settings Display
 * Filename: includes/settings-display.php
 * Description: Renders the settings form UI for the Portfolio Player plugin.
 */

if (!defined('ABSPATH')) {
    exit;
}

function portfolio_player_display_settings($data) {
    ob_start();
    ?>
    <div class="portfolio-player-settings">
        <h1>Portfolio Player 設定</h1>
        <?php if (!empty($data['error_message'])): ?>
            <?php echo $data['error_message']; ?>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data">
            <div class="tabs">
                <ul class="tab-list">
                    <li class="tab active" data-tab="essential-info">重要資訊</li>
                    <li class="tab" data-tab="bio">個人介紹</li>
                    <li class="tab" data-tab="social-accounts">社群帳號</li>
                    <li class="tab" data-tab="player-colors">顏色設定</li>
                    <li class="tab" data-tab="songs-list">歌曲列表</li>
                    <li class="tab" data-tab="account-info">帳號資訊</li> <!-- 新增 Tab -->
                </ul>

                <div class="tab-content active" id="essential-info">
                    <h2>重要資訊</h2>
                    <table class="form-table">
                        <tr>
                            <th><label for="custom_url_suffix">自訂 URL</label></th>
                            <td class="align-middle">
                                <input type="text" name="custom_url_suffix" id="custom_url_suffix" value="<?php echo esc_attr($data['custom_url_suffix']); ?>" class="regular-text" placeholder="例如：john" required />
                                <p class="description" id="url-preview">最終作品集網址: https://ourdaysrecords.com/music/<?php echo esc_attr($data['custom_url_suffix'] ?: '[你輸入的]'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="email">電子郵件</label></th>
                            <td class="align-middle">
                                <input type="email" name="email" id="email" value="<?php echo esc_attr($data['email']); ?>" class="regular-text" placeholder="例如：artist@example.com" />
                                <p class="description">這將用於接收其他人的合作邀請訊息。</p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="display_name">顯示名稱</label></th>
                            <td class="align-middle">
                                <input type="text" name="display_name" id="display_name" value="<?php echo esc_attr($data['display_name']); ?>" class="regular-text" placeholder="例如：John Doe" />
                                <p class="description">這將顯示在您的音樂作品集頁面中，位於藝人圖片下方、個人介紹上方。</p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="tagline">你的標語</label></th>
                            <td class="align-middle">
                                <input type="text" name="tagline" id="tagline" value="<?php echo esc_attr($data['tagline']); ?>" class="regular-text" placeholder="例如：一位混音與母帶工程師" />
                                <p class="description">你可以說自己的職位、或者擅長的事情。</p>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="tab-content" id="bio">
                    <h2>個人介紹</h2>
                    <table class="form-table">
                        <tr>
                            <th><label>主要照片/影片</label></th>
                            <td>
                                <label for="artist_photo">照片 URL</label><br>
                                <input type="url" name="artist_photo" id="artist_photo" value="<?php echo esc_url($data['artist_photo']); ?>" class="regular-text" placeholder="請輸入照片 URL" /><br><br>
                                <label for="artist_video">影片 URL</label><br>
                                <input type="url" name="artist_video" id="artist_video" value="<?php echo esc_url($data['artist_video']); ?>" class="regular-text" placeholder="請輸入影片 URL" />
                                <p class="description">照片和影片二選一，影片可以使用 YouTube 或 Vimeo 連結。</p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="extra_image_1">額外照片 1</label></th>
                            <td>
                                <input type="url" name="extra_image_1" id="extra_image_1" value="<?php echo esc_url($data['extra_image_1']); ?>" class="regular-text" placeholder="請輸入照片 URL" />
                            </td>
                        </tr>
                        <tr>
                            <th><label for="extra_image_2">額外照片 2</label></th>
                            <td>
                                <input type="url" name="extra_image_2" id="extra_image_2" value="<?php echo esc_url($data['extra_image_2']); ?>" class="regular-text" placeholder="請輸入照片 URL" />
                            </td>
                        </tr>
                        <tr>
                            <th><label for="extra_image_3">額外照片 3</label></th>
                            <td>
                                <input type="url" name="extra_image_3" id="extra_image_3" value="<?php echo esc_url($data['extra_image_3']); ?>" class="regular-text" placeholder="請輸入照片 URL" />
                            </td>
                        </tr>
                        <tr>
                            <th><label for="artist_bio">個人介紹</label></th>
                            <td>
                                <?php
                                $settings = [
                                    'textarea_name' => 'artist_bio',
                                    'media_buttons' => false,
                                    'textarea_rows' => 10,
                                    'teeny' => true,
                                    'tinymce' => [
                                        'toolbar1' => 'bold,italic,link,unlink',
                                        'toolbar2' => ''
                                    ]
                                ];
                                wp_editor($data['artist_bio'], 'artist_bio', $settings);
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="tab-content" id="social-accounts">
                    <h2>社群帳號</h2>
                    <table class="form-table">
                        <tr>
                            <th><label for="youtube_url">YouTube 連結</label></th>
                            <td class="align-middle">
                                <input type="url" name="youtube_url" id="youtube_url" value="<?php echo esc_url($data['youtube_url']); ?>" class="regular-text" placeholder="例如：https://youtube.com/@yourchannel" />
                                <p class="description">輸入您的 YouTube 頻道連結。</p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="instagram_url">Instagram 連結</label></th>
                            <td class="align-middle">
                                <input type="url" name="instagram_url" id="instagram_url" value="<?php echo esc_url($data['instagram_url']); ?>" class="regular-text" placeholder="例如：https://instagram.com/yourprofile" />
                                <p class="description">輸入您的 Instagram 個人檔案連結。</p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="threads_url">Threads 連結</label></th>
                            <td class="align-middle">
                                <input type="url" name="threads_url" id="threads_url" value="<?php echo esc_url($data['threads_url']); ?>" class="regular-text" placeholder="例如：https://threads.net/@yourprofile" />
                                <p class="description">輸入您的 Threads 個人檔案連結。</p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="website_url">個人網站連結</label></th>
                            <td class="align-middle">
                                <input type="url" name="website_url" id="website_url" value="<?php echo esc_url($data['website_url']); ?>" class="regular-text" placeholder="例如：https://yourwebsite.com" />
                                <p class="description">輸入您的個人網站連結。</p>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="tab-content" id="player-colors">
                    <h2>顏色設定</h2>
                    <table class="form-table">
                        <tr>
                            <th><label for="player_background_color">背景顏色</label></th>
                            <td>
                                <input type="color" name="player_background_color" id="player_background_color" value="<?php echo esc_attr($data['colors']['background']); ?>" />
                                <input type="text" id="player_background_hex" value="<?php echo esc_attr($data['colors']['background']); ?>" class="hex-input" placeholder="HEX 色碼" />
                            </td>
                        </tr>
                        <tr>
                            <th><label for="highlight_color">強調顏色</label></th>
                            <td>
                                <input type="color" name="highlight_color" id="highlight_color" value="<?php echo esc_attr($data['colors']['highlight']); ?>" />
                                <input type="text" id="highlight_hex" value="<?php echo esc_attr($data['colors']['highlight']); ?>" class="hex-input" placeholder="HEX 色碼" />
                            </td>
                        </tr>
                        <tr>
                            <th><label for="title_color">主要字體顏色</label></th>
                            <td>
                                <input type="color" name="title_color" id="title_color" value="<?php echo esc_attr($data['colors']['title']); ?>" />
                                <input type="text" id="title_hex" value="<?php echo esc_attr($data['colors']['title']); ?>" class="hex-input" placeholder="HEX 色碼" />
                            </td>
                        </tr>
                        <tr>
                            <th><label for="artist_color">次要字體顏色</label></th>
                            <td>
                                <input type="color" name="artist_color" id="artist_color" value="<?php echo esc_attr($data['colors']['artist']); ?>" />
                                <input type="text" id="artist_hex" value="<?php echo esc_attr($data['colors']['artist']); ?>" class="hex-input" placeholder="HEX 色碼" />
                            </td>
                        </tr>
                        <tr>
                            <th><label for="social_icon_mode">社群圖標顏色</label></th>
                            <td>
                                <select name="social_icon_mode" id="social_icon_mode">
                                    <option value="dark" <?php selected($data['social_icon_mode'], 'dark'); ?>>黑圖標（Dark Mode）</option>
                                    <option value="light" <?php selected($data['social_icon_mode'], 'light'); ?>>白圖標（Light Mode）</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="tab-content" id="songs-list">
                    <h2>歌曲列表</h2>
                    <table id="songs-table" class="widefat">
                        <thead>
                            <tr>
                                <th>曲風</th>
                                <th>歌名</th>
                                <th>演出者</th>
                                <th>歌曲連結</th>
                                <th>刪除</th>
                                <th>順序</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['songs'] as $index => $song): ?>
                                <tr>
                                    <td><input type="text" name="songs[<?php echo $index; ?>][genre]" value="<?php echo esc_attr($song['genre'] ?? ''); ?>" /></td>
                                    <td><input type="text" name="songs[<?php echo $index; ?>][title]" value="<?php echo esc_attr($song['title']); ?>" /></td>
                                    <td><input type="text" name="songs[<?php echo $index; ?>][artist]" value="<?php echo esc_attr($song['artist']); ?>" /></td>
                                    <td><input type="url" name="songs[<?php echo $index; ?>][url]" value="<?php echo esc_url($song['url']); ?>" /></td>
                                    <td>
                                        <button type="button" class="remove-song">
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="10" cy="10" r="9" stroke="#ff0000" stroke-width="2"/>
                                                <line x1="6" y1="6" x2="14" y2="14" stroke="#ff0000" stroke-width="2"/>
                                                <line x1="14" y1="6" x2="6" y2="14" stroke="#ff0000" stroke-width="2"/>
                                            </svg>
                                        </button>
                                    </td>
                                    <td><span class="dashicons dashicons-move"></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <button type="button" id="add-song" class="button">新增歌曲</button>
                </div>

                <!-- 新增帳號資訊 Tab -->
                <div class="tab-content" id="account-info">
                    <h2>帳號資訊</h2>
                    <table class="form-table">
                        <tr>
                            <th><label>綁定 Email</label></th>
                            <td><?php echo esc_html($data['bound_email'] ?: '未綁定'); ?></td>
                        </tr>
                        <tr>
                            <th><label>金鑰</label></th>
                            <td><?php echo esc_html($data['masked_key'] ?: '未綁定'); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <p class="submit-button"><input type="submit" name="portfolio_player_save_settings" class="button-primary" value="保存設定" /></p>
        </form>

        <?php if ($data['show_url_section']): ?>
            <div class="portfolio-url-section">
                <h3>作品集網址</h3>
                <div class="url-display">
                    <span id="portfolio-url"><?php echo esc_url($data['portfolio_url']); ?></span>
                    <button type="button" id="copy-url" class="button button-secondary">複製</button>
                </div>
                <button type="button" id="go-to-url" class="button button-primary">前往</button>
            </div>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}