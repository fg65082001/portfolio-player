<?php
/*
 * Portfolio Player - Portfolio Display
 * Filename: includes/portfolio.php
 * Description: Handles the portfolio shortcode for displaying the player's portfolio page.
 */

if (!defined('ABSPATH')) {
    exit;
}

function portfolio_player_portfolio_shortcode($atts) {
    $atts = shortcode_atts(['user_id' => ''], $atts);
    $user_id = !empty($atts['user_id']) ? absint($atts['user_id']) : 0;

    if (!$user_id) {
        return '<p>無效的用戶 ID。</p>';
    }

    // 處理聯絡表單提交
    if (isset($_POST['portfolio_player_send_message'])) {
        $visitor_name = sanitize_text_field($_POST['visitor_name'] ?? '');
        $client_email = sanitize_email($_POST['client_email'] ?? '');
        $message = sanitize_textarea_field($_POST['message'] ?? '');
        $captcha_answer = intval($_POST['captcha_answer'] ?? 0);
        $captcha_sum = intval($_POST['captcha_num1']) + intval($_POST['captcha_num2']);
        $artist_email = get_user_meta($user_id, 'portfolio_player_email', true);

        if ($captcha_answer !== $captcha_sum) {
            echo '<div class="message-error">驗證答案錯誤，請再試一次。</div>';
        } elseif (!empty($visitor_name) && !empty($message) && !empty($artist_email)) {
            $subject = "[$visitor_name]想要找你一起工作!";
            $body = $message;
            $headers = [
                'Content-Type: text/plain; charset=UTF-8',
                'Reply-To: ' . $visitor_name . ' <' . $client_email . '>'
            ];
            $sent = wp_mail($artist_email, $subject, $body, $headers);
            if ($sent) {
                echo '<div class="message-sent">訊息已成功發送！</div>';
            } else {
                echo '<div class="message-error">訊息發送失敗，請稍後再試。</div>';
            }
        } else {
            echo '<div class="message-error">請填寫所有欄位。</div>';
        }
    }

    // 獲取用戶設定資料
    $songs = get_user_meta($user_id, 'portfolio_player_songs', true) ?: [];
    $artist_photo = get_user_meta($user_id, 'portfolio_player_artist_photo', true) ?: '';
    $artist_video = get_user_meta($user_id, 'portfolio_player_artist_video', true) ?: '';
    $extra_image_1 = get_user_meta($user_id, 'portfolio_player_extra_image_1', true) ?: '';
    $extra_image_2 = get_user_meta($user_id, 'portfolio_player_extra_image_2', true) ?: '';
    $extra_image_3 = get_user_meta($user_id, 'portfolio_player_extra_image_3', true) ?: '';
    $artist_bio = get_user_meta($user_id, 'portfolio_player_artist_bio', true) ?: '';
    $display_name = get_user_meta($user_id, 'portfolio_player_display_name', true) ?: '';
    $tagline = get_user_meta($user_id, 'portfolio_player_tagline', true) ?: '';
    $colors = get_user_meta($user_id, 'portfolio_player_colors', true) ?: [
        'background' => '#282828',
        'highlight' => '#1db954',
        'artist' => '#b3b3b3',
        'title' => '#ffffff'
    ];
    $youtube_url = get_user_meta($user_id, 'portfolio_player_youtube_url', true) ?: '';
    $instagram_url = get_user_meta($user_id, 'portfolio_player_instagram_url', true) ?: '';
    $threads_url = get_user_meta($user_id, 'portfolio_player_threads_url', true) ?: '';
    $website_url = get_user_meta($user_id, 'portfolio_player_website_url', true) ?: '';
    $social_icon_mode = get_user_meta($user_id, 'portfolio_player_social_icon_mode', true) ?: 'dark';

    if (empty($songs)) return '<p>暫無歌曲。</p>';

    $captcha_num1 = rand(1, 10);
    $captcha_num2 = rand(1, 10);

    $icons = [
        'dark' => [
            'instagram' => 'https://ourdaysrecords.b-cdn.net/social%20icons/black/instagram.png',
            'threads' => 'https://ourdaysrecords.b-cdn.net/social%20icons/black/threads.png',
            'youtube' => 'https://ourdaysrecords.b-cdn.net/social%20icons/black/youtube.png',
            'website' => 'https://ourdaysrecords.b-cdn.net/social%20icons/black/world-wide-web.png'
        ],
        'light' => [
            'instagram' => 'https://ourdaysrecords.b-cdn.net/social%20icons/white/instagram-w.png',
            'threads' => 'https://ourdaysrecords.b-cdn.net/social%20icons/white/threads-w.png',
            'youtube' => 'https://ourdaysrecords.b-cdn.net/social%20icons/white/youtube-w.png',
            'website' => 'https://ourdaysrecords.b-cdn.net/social%20icons/white/world-wide-web%20(1).png'
        ]
    ];

    ob_start();
    ?>
    <div class="portfolio-player-template">
        <!-- 桌面版主要照片/影片 -->
        <?php if (!empty($artist_video) || !empty($artist_photo)): ?>
            <div class="artist-image desktop-only">
                <?php if (!empty($artist_video)): ?>
                    <div class="video-wrapper">
                        <?php echo wp_oembed_get($artist_video, ['width' => 800]); ?>
                    </div>
                <?php elseif (!empty($artist_photo)): ?>
                    <img src="<?php echo esc_url($artist_photo); ?>" alt="Artist Photo" />
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- 手機版幻燈片 -->
        <div class="artist-slider mobile-only">
            <div class="slider-container">
                <?php if (!empty($artist_video) || !empty($artist_photo)): ?>
                    <div class="slide">
                        <?php if (!empty($artist_video)): ?>
                            <div class="video-wrapper">
                                <?php echo wp_oembed_get($artist_video, ['width' => 800]); ?>
                            </div>
                        <?php elseif (!empty($artist_photo)): ?>
                            <img src="<?php echo esc_url($artist_photo); ?>" alt="Artist Photo" />
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($extra_image_1)): ?>
                    <div class="slide">
                        <img src="<?php echo esc_url($extra_image_1); ?>" alt="Extra Image 1" />
                    </div>
                <?php endif; ?>
                <?php if (!empty($extra_image_2)): ?>
                    <div class="slide">
                        <img src="<?php echo esc_url($extra_image_2); ?>" alt="Extra Image 2" />
                    </div>
                <?php endif; ?>
                <?php if (!empty($extra_image_3)): ?>
                    <div class="slide">
                        <img src="<?php echo esc_url($extra_image_3); ?>" alt="Extra Image 3" />
                    </div>
                <?php endif; ?>
            </div>
            <div class="slider-dots">
                <?php if (!empty($artist_video) || !empty($artist_photo)): ?>
                    <span class="dot active" data-slide="0"></span>
                <?php endif; ?>
                <?php if (!empty($extra_image_1)): ?>
                    <span class="dot" data-slide="1"></span>
                <?php endif; ?>
                <?php if (!empty($extra_image_2)): ?>
                    <span class="dot" data-slide="2"></span>
                <?php endif; ?>
                <?php if (!empty($extra_image_3)): ?>
                    <span class="dot" data-slide="3"></span>
                <?php endif; ?>
            </div>
        </div>

        <!-- 桌面版額外照片 -->
        <?php if (!empty($extra_image_1) || !empty($extra_image_2) || !empty($extra_image_3)): ?>
            <div class="extra-images desktop-only">
                <?php if (!empty($extra_image_1)): ?>
                    <div class="extra-image">
                        <img src="<?php echo esc_url($extra_image_1); ?>" alt="Extra Image 1" />
                    </div>
                <?php endif; ?>
                <?php if (!empty($extra_image_2)): ?>
                    <div class="extra-image">
                        <img src="<?php echo esc_url($extra_image_2); ?>" alt="Extra Image 2" />
                    </div>
                <?php endif; ?>
                <?php if (!empty($extra_image_3)): ?>
                    <div class="extra-image">
                        <img src="<?php echo esc_url($extra_image_3); ?>" alt="Extra Image 3" />
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($display_name)): ?>
            <div class="display-name" style="color: <?php echo esc_attr($colors['title']); ?>;">
                <?php echo esc_html($display_name); ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($tagline)): ?>
            <div class="artist-tagline" style="color: <?php echo esc_attr($colors['highlight']); ?>;">
                #<?php echo esc_html($tagline); ?>
            </div>
        <?php endif; ?>
        <div class="social-links">
            <?php if (!empty($instagram_url)): ?>
                <a href="<?php echo esc_url($instagram_url); ?>" target="_blank" class="social-icon instagram">
                    <span class="icon-circle" style="background-color: <?php echo esc_attr($colors['artist']); ?>;">
                        <img src="<?php echo esc_url($icons[$social_icon_mode]['instagram']); ?>" alt="Instagram" class="social-icon-img" />
                    </span>
                </a>
            <?php endif; ?>
            <?php if (!empty($threads_url)): ?>
                <a href="<?php echo esc_url($threads_url); ?>" target="_blank" class="social-icon threads">
                    <span class="icon-circle" style="background-color: <?php echo esc_attr($colors['artist']); ?>;">
                        <img src="<?php echo esc_url($icons[$social_icon_mode]['threads']); ?>" alt="Threads" class="social-icon-img" />
                    </span>
                </a>
            <?php endif; ?>
            <?php if (!empty($youtube_url)): ?>
                <a href="<?php echo esc_url($youtube_url); ?>" target="_blank" class="social-icon youtube">
                    <span class="icon-circle" style="background-color: <?php echo esc_attr($colors['artist']); ?>;">
                        <img src="<?php echo esc_url($icons[$social_icon_mode]['youtube']); ?>" alt="YouTube" class="social-icon-img" />
                    </span>
                </a>
            <?php endif; ?>
            <?php if (!empty($website_url)): ?>
                <a href="<?php echo esc_url($website_url); ?>" target="_blank" class="social-icon website">
                    <span class="icon-circle" style="background-color: <?php echo esc_attr($colors['artist']); ?>;">
                        <img src="<?php echo esc_url($icons[$social_icon_mode]['website']); ?>" alt="Website" class="social-icon-img" />
                    </span>
                </a>
            <?php endif; ?>
        </div>
        <?php if (!empty($artist_bio)): ?>
            <div class="artist-bio" style="color: <?php echo esc_attr($colors['title']); ?>;">
                <?php echo wp_kses_post($artist_bio); ?>
            </div>
        <?php endif; ?>
        <div id="player-container">
            <audio id="audio" controls></audio>
            <ul id="playlist">
                <?php foreach ($songs as $song): ?>
                    <li data-src="<?php echo esc_url($song['url']); ?>">
                        <div class="song-info">
                            <div class="song-genre" style="color: <?php echo esc_attr($colors['highlight']); ?>;">
                                <?php echo !empty($song['genre']) ? '#' . esc_html($song['genre']) : ''; ?>
                            </div>
                            <div class="song-title"><?php echo esc_html($song['title']); ?></div>
                            <div class="song-artist"><?php echo esc_html($song['artist']); ?></div>
                        </div>
                        <div class="song-duration"></div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="contact-form">
            <h2 style="color: <?php echo esc_attr($colors['title']); ?>;">聯絡我</h2>
            <form method="post">
                <div class="form-group">
                    <label for="visitor_name" style="color: <?php echo esc_attr($colors['title']); ?>;">怎麼稱呼你</label>
                    <input type="text" name="visitor_name" id="visitor_name" class="regular-text" placeholder="請輸入你的名稱" required />
                </div>
                <div class="form-group">
                    <label for="client_email" style="color: <?php echo esc_attr($colors['title']); ?>;">你的電子郵件</label>
                    <input type="email" name="client_email" id="client_email" class="regular-text" placeholder="請輸入你的電子郵件" required />
                </div>
                <div class="form-group">
                    <label for="message" style="color: <?php echo esc_attr($colors['title']); ?>;">說說我們可以一起完成的事</label>
                    <textarea name="message" id="message" rows="5" class="regular-text" placeholder="請輸入你的訊息" required></textarea>
                </div>
                <div class="form-group">
                    <label for="captcha_answer" style="color: <?php echo esc_attr($colors['title']); ?>;">驗證：<?php echo $captcha_num1 . ' + ' . $captcha_num2; ?> = ?</label>
                    <input type="number" name="captcha_answer" id="captcha_answer" class="regular-text" required />
                    <input type="hidden" name="captcha_num1" value="<?php echo $captcha_num1; ?>" />
                    <input type="hidden" name="captcha_num2" value="<?php echo $captcha_num2; ?>" />
                </div>
                <p><input type="submit" name="portfolio_player_send_message" class="button-primary send-message-button" value="發送訊息" /></p>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const path = window.location.pathname;
            if (path.includes("music")) {
                document.body.classList.add("portfolio-page");
            }

            const slider = document.querySelector('.slider-container');
            const dots = document.querySelectorAll('.slider-dots .dot');
            if (slider && dots.length > 0) {
                dots.forEach(dot => {
                    dot.addEventListener('click', function() {
                        const slideIndex = parseInt(this.getAttribute('data-slide'));
                        slider.scrollTo({
                            left: slideIndex * slider.offsetWidth,
                            behavior: 'smooth'
                        });
                        dots.forEach(d => d.classList.remove('active'));
                        this.classList.add('active');
                    });
                });

                slider.addEventListener('scroll', function() {
                    const slideWidth = slider.offsetWidth;
                    const scrollLeft = slider.scrollLeft;
                    const activeIndex = Math.round(scrollLeft / slideWidth);
                    dots.forEach(d => d.classList.remove('active'));
                    if (dots[activeIndex]) dots[activeIndex].classList.add('active');
                });
            }
        });
    </script>
    <style>
        body.portfolio-page { 
            background-color: <?php echo esc_attr($colors['background']); ?> !important; 
            margin: 0; 
        }
        body.portfolio-page header, 
        body.portfolio-page footer, 
        body.portfolio-page #masthead, 
        body.portfolio-page #colophon, 
        body.portfolio-page .site-header, 
        body.portfolio-page .site-footer,
        body.portfolio-page .page-title,
        body.portfolio-page .site-branding,
        body.portfolio-page .entry-header { 
            display: none !important; 
        }
        .portfolio-player-template { 
            max-width: 800px; 
            margin: 0 auto; 
            padding: 20px; 
        }
        #player-container { 
            max-width: 800px; 
            margin: 0 auto; 
            padding: 20px 0; 
        }
        .artist-image { 
            margin-bottom: 20px; 
            text-align: center; 
        }
        .artist-image img { 
            border-radius: 15px; 
            max-width: 100%; 
            height: auto; 
        }
        .video-wrapper { 
            position: relative; 
            padding-bottom: 56.25%; 
            height: 0; 
            overflow: hidden; 
        }
        .video-wrapper iframe { 
            position: absolute; 
            top: 0; 
            left: 0; 
            width: 100%; 
            height: 100%; 
            border-radius: 15px; 
        }
        .artist-slider { 
            margin-bottom: 20px; 
            display: none; 
        }
        .slider-container { 
            display: flex; 
            overflow-x: auto; 
            scroll-snap-type: x mandatory; 
            -webkit-overflow-scrolling: touch; 
        }
        .slide { 
            flex: 0 0 100%; 
            scroll-snap-align: start; 
            text-align: center; 
        }
        .slide img { 
            border-radius: 15px; 
            max-width: 100%; 
            height: auto; 
        }
        .slider-dots { 
            text-align: center; 
            margin-top: 10px; 
        }
        .dot { 
            display: inline-block; 
            width: 10px; 
            height: 10px; 
            background-color: #bbb; 
            border-radius: 50%; 
            margin: 0 5px; 
            cursor: pointer; 
        }
        .dot.active { 
            background-color: <?php echo esc_attr($colors['highlight']); ?>; 
        }
        .extra-images.desktop-only { 
            display: flex; 
            flex-wrap: wrap; 
            gap: 10px; 
            margin-bottom: 20px; 
            justify-content: center; 
        }
        .extra-image { 
            flex: 1 1 30%; 
            max-width: 250px; 
        }
        .extra-image img { 
            border-radius: 10px; 
            max-width: 100%; 
            height: auto; 
        }
        .desktop-only { 
            display: block; 
        }
        .mobile-only { 
            display: none; 
        }
        .display-name { 
            font-size: 50px; 
            font-weight: 900; 
            margin-bottom: 20px; 
            text-align: center; 
        }
        .artist-tagline { 
            margin-top: 5px; 
            margin-bottom: 20px; 
            text-align: center; 
            font-size: 18px; 
            font-weight: 800; 
        }
        .social-links {
            text-align: center;
            margin: 20px 0 40px 0;
        }
        .social-icon {
            margin: 0 8px;
            display: inline-block;
            position: relative;
            width: 32px;
            height: 32px;
        }
        .social-icon .icon-circle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            transition: opacity 0.3s;
        }
        .social-icon:hover .icon-circle {
            opacity: 0.8;
        }
        .social-icon .social-icon-img {
            width: 20px;
            height: 20px;
        }
        .artist-bio { 
            margin-bottom: 40px; 
        }
        .artist-bio p { 
            color: inherit; 
        }
        .contact-form { 
            margin-top: 40px; 
        }
        .form-group { 
            margin-bottom: 20px; 
        }
        .form-group label { 
            display: block; 
            margin-bottom: 5px; 
        }
        .form-group input, .form-group textarea { 
            width: 100%; 
            padding: 10px; 
            box-sizing: border-box; 
            color: <?php echo esc_attr($colors['artist']); ?> !important; 
            border: 1px solid #ddd;
        }
        .form-group input::placeholder, .form-group textarea::placeholder { 
            color: <?php echo esc_attr($colors['artist']); ?> !important; 
            opacity: 0.7; 
        }
        .form-group input:focus, .form-group textarea:focus {
            border-color: <?php echo esc_attr($colors['highlight']); ?> !important;
            outline: none;
        }
        .button-primary {
            background-color: <?php echo esc_attr($colors['highlight']); ?> !important;
            border-color: <?php echo esc_attr($colors['highlight']); ?> !important;
            color: <?php echo esc_attr($colors['background']); ?> !important;
            padding: 10px 20px;
            width: 100%;
            max-width: 200px;
            margin: 0 auto;
            display: block;
            text-align: center;
            border-radius: 4px;
        }
        .button-primary:hover {
            background-color: <?php echo esc_attr($colors['highlight']); ?> !important;
            opacity: 0.9;
        }
        .send-message-button {
            max-width: none !important;
        }
        .message-sent { 
            color: <?php echo esc_attr($colors['highlight']); ?> !important; 
            margin-bottom: 20px; 
        }
        .message-error { 
            color: #ff0000 !important; 
            margin-bottom: 20px; 
        }
        @media (max-width: 768px) {
            .portfolio-player-template { padding: 10px; }
            #player-container { padding: 10px 0; }
            #playlist li { flex-direction: column; align-items: flex-start; }
            .song-duration { text-align: left; margin-top: 5px; }
            .artist-slider.mobile-only { display: block; }
            .desktop-only { display: none !important; }
            .slider-container { 
                scroll-behavior: smooth; 
                -ms-overflow-style: none; 
                scrollbar-width: none; 
            }
            .slider-container::-webkit-scrollbar { 
                display: none; 
            }
        }
    </style>
    <?php
    return ob_get_clean();
}

add_shortcode('portfolio_player_portfolio', 'portfolio_player_portfolio_shortcode');