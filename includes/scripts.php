<?php
/*
 * Portfolio Player - Scripts and Styles
 * Filename: includes/scripts.php
 * Description: Enqueues scripts and styles for the Portfolio Player plugin.
 */

if (!defined('ABSPATH')) {
    exit;
}

function portfolio_player_enqueue_scripts() {
    // 載入核心依賴
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-sortable', false, ['jquery'], null, true);
    wp_enqueue_style('plyr', 'https://cdn.plyr.io/3.6.8/plyr.css', [], '3.6.8');
    wp_enqueue_script('plyr', 'https://cdn.plyr.io/3.6.8/plyr.js', [], '3.6.8', true);

    // 獲取當前用戶的設定
    $user_id = get_current_user_id();
    $colors = get_user_meta($user_id, 'portfolio_player_colors', true) ?: [
        'background' => '#282828',
        'highlight' => '#1db954',
        'artist' => '#b3b3b3',
        'title' => '#ffffff'
    ];
    $styles = get_user_meta($user_id, 'portfolio_player_styles', true) ?: [
        'padding' => 20,
        'artist_font_size' => 13,
        'title_font_size' => 16
    ];

    // 內聯腳本：播放器邏輯
    wp_add_inline_script('plyr', '
        document.addEventListener("DOMContentLoaded", function() {
            const player = new Plyr("#audio", { 
                controls: ["play", "progress", "current-time"],
                loadSprite: true,
                iconUrl: "https://cdn.plyr.io/3.6.8/plyr.svg"
            });
            const playlistItems = document.querySelectorAll("#playlist li");
            function formatDuration(seconds) {
                const minutes = Math.floor(seconds / 60);
                const secs = Math.floor(seconds % 60);
                return `${minutes.toString().padStart(2, "0")}:${secs.toString().padStart(2, "0")}`;
            }
            playlistItems.forEach(item => {
                const src = item.getAttribute("data-src");
                const tempAudio = new Audio(src);
                tempAudio.addEventListener("loadedmetadata", () => {
                    const duration = tempAudio.duration;
                    const durationElement = item.querySelector(".song-duration");
                    durationElement.textContent = formatDuration(duration);
                });
                tempAudio.addEventListener("error", () => {
                    const durationElement = item.querySelector(".song-duration");
                    durationElement.textContent = "無效";
                    item.style.color = "#ff0000";
                });
                item.addEventListener("click", () => {
                    playlistItems.forEach(i => i.classList.remove("playing"));
                    item.classList.add("playing");
                    player.source = {
                        type: "audio",
                        sources: [{ src: item.getAttribute("data-src"), type: "audio/mpeg" }]
                    };
                    player.play();
                });
            });
            if (playlistItems.length > 0) {
                const firstItem = playlistItems[0];
                firstItem.classList.add("playing");
                player.source = {
                    type: "audio",
                    sources: [{ src: firstItem.getAttribute("data-src"), type: "audio/mpeg" }]
                };
            }
            player.on("ended", function() {
                const currentItem = document.querySelector("#playlist li.playing");
                const nextItem = currentItem.nextElementSibling || document.querySelector("#playlist li:first-child");
                if (nextItem) {
                    playlistItems.forEach(i => i.classList.remove("playing"));
                    nextItem.classList.add("playing");
                    player.source = {
                        type: "audio",
                        sources: [{ src: nextItem.getAttribute("data-src"), type: "audio/mpeg" }]
                    };
                    player.play();
                }
            });
        });
    ');

    // 內聯樣式：播放器樣式
    wp_add_inline_style('plyr', '
        #player-container { 
            width: 100%; 
            max-width: 100%; 
            margin: 0; 
            padding: 20px 0; 
            background-color: transparent !important; 
        }
        #playlist { 
            list-style: none; 
            padding: ' . esc_attr($styles['padding']) . 'px 0; 
            max-height: 400px; 
            overflow-y: auto; 
            background-color: transparent !important; 
        }
        #playlist::-webkit-scrollbar { 
            width: 0; 
        }
        #playlist { 
            scrollbar-width: none; 
        }
        #playlist li { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            padding: 10px; 
            background-color: transparent !important; 
            margin-bottom: 2px; 
            color: #b3b3b3; 
            cursor: pointer; 
            border-radius: 0; 
            border: none; 
        }
        #playlist li:hover { 
            background-color: #3e3e3e; 
        }
        #playlist li.playing { 
            background-color: #3e3e3e !important; 
            color: ' . esc_attr($colors['highlight']) . ' !important; 
        }
        .song-info { 
            flex: 1; 
        }
        .song-genre { 
            font-size: 10px; 
            font-weight: 700; 
        }
        .song-title { 
            font-size: ' . esc_attr($styles['title_font_size']) . 'px !important; 
            color: ' . esc_attr($colors['title']) . '; 
            font-weight: 900; 
        }
        .song-artist { 
            font-size: ' . esc_attr($styles['artist_font_size']) . 'px !important; 
            color: ' . esc_attr($colors['artist']) . '; 
        }
        .song-duration { 
            font-size: 14px; 
            color: #b3b3b3; 
            min-width: 50px; 
            text-align: right; 
        }
        .portfolio-player-template .plyr--audio .plyr__controls { 
            background-color: transparent !important; 
            padding: 0; 
            width: 100%; 
            max-width: 100%; 
            margin: 0; 
            box-sizing: border-box; 
        }
        .plyr__control[data-plyr="play"] { 
            color: #ffffff; 
            margin-right: 20px; 
        }
        .plyr__control[data-plyr="play"]:hover { 
            background-color: ' . esc_attr($colors['highlight']) . '; 
            color: #ffffff; 
        }
        .plyr__progress { 
            flex: 1; 
            margin-right: 20px; 
            margin-left: 10px; 
        }
        .plyr__progress__buffer { 
            background-color: #333 !important; 
        }
        .plyr__progress__container input[type="range"]::-webkit-slider-runnable-track { 
            background-color: #333 !important; 
        }
        .plyr__progress__container input[type="range"]::-webkit-slider-thumb { 
            background-color: ' . esc_attr($colors['highlight']) . ' !important; 
        }
        .plyr__progress__container input[type="range"] { 
            color: ' . esc_attr($colors['highlight']) . ' !important; 
        }
        .plyr__time { 
            color: #ffffff; 
            font-size: 14px; 
            min-width: 50px; 
            margin-left: 50px; 
        }
    ');
}
add_action('wp_enqueue_scripts', 'portfolio_player_enqueue_scripts');