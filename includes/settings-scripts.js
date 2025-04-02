jQuery(document).ready(function($) {
    // Tab Switching
    $('.tab').on('click', function() {
        var tabId = $(this).data('tab');
        $('.tab').removeClass('active');
        $('.tab-content').removeClass('active');
        $(this).addClass('active');
        $('#' + tabId).addClass('active');
    });

    // Add Song
    $('#add-song').on('click', function() {
        var index = $('#songs-table tbody tr').length;
        var newRow = `
            <tr>
                <td><input type="text" name="songs[${index}][genre]" value="" /></td>
                <td><input type="text" name="songs[${index}][title]" value="" /></td>
                <td><input type="text" name="songs[${index}][artist]" value="" /></td>
                <td><input type="url" name="songs[${index}][url]" value="" /></td>
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
        `;
        $('#songs-table tbody').append(newRow);
    });

    // Remove Song
    $(document).on('click', '.remove-song', function() {
        $(this).closest('tr').remove();
    });

    // Sortable Songs
    $('#songs-table tbody').sortable({
        handle: '.dashicons-move',
        placeholder: 'ui-state-highlight',
        update: function(event, ui) {
            var order = $(this).sortable('toArray', { attribute: 'data-index' });
            var rows = $(this).find('tr');
            rows.each(function(index) {
                var newIndex = order.indexOf($(this).attr('data-index'));
                if (newIndex === -1) newIndex = index;
                $(this).find('input').each(function() {
                    var name = $(this).attr('name');
                    if (name) {
                        var newName = name.replace(/\[\d+\]/, '[' + newIndex + ']');
                        $(this).attr('name', newName);
                    }
                });
            });
        }
    }).disableSelection();

    // Color Picker Sync
    function syncColorInputs(colorInputId, hexInputId) {
        var $colorInput = $('#' + colorInputId);
        var $hexInput = $('#' + hexInputId);

        $colorInput.on('input', function() {
            $hexInput.val($(this).val());
        });

        $hexInput.on('input', function() {
            var hex = $(this).val();
            if (/^#[0-9A-F]{6}$/i.test(hex)) {
                $colorInput.val(hex);
            }
        });
    }

    syncColorInputs('player_background_color', 'player_background_hex');
    syncColorInputs('highlight_color', 'highlight_hex');
    syncColorInputs('title_color', 'title_hex');
    syncColorInputs('artist_color', 'artist_hex');

    // Copy URL
    $('#copy-url').on('click', function() {
        var url = $('#portfolio-url').text();
        navigator.clipboard.writeText(url).then(function() {
            alert('網址已複製到剪貼簿！');
        }, function(err) {
            alert('複製失敗，請手動複製：' + url);
        });
    });

    // Go to URL
    $('#go-to-url').on('click', function() {
        var url = $('#portfolio-url').text();
        window.open(url, '_blank');
    });

    // Custom URL Preview
    $('#custom_url_suffix').on('input', function() {
        var suffix = $(this).val();
        var baseUrl = 'https://ourdaysrecords.com/music/';
        $('#url-preview').text('最終作品集網址: ' + baseUrl + (suffix || '[你輸入的]'));
    });
});