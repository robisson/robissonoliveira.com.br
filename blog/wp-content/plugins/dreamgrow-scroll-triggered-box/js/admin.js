/*jslint browser: true, plusplus: true, sloppy: true */
/*global $DGD */
/*global jQuery */
/*global tb_show */
/*global tb_remove */
/*global console */
/*global pagenow */


if (typeof $DGD !== 'object') {
    var $DGD = { 'debug': true };
}

if (typeof $DGD.echo !== 'object') {
    $DGD.echo = function (str) {
        if ($DGD.debug) {console.log(str); }
    };
}

$DGD.select2D = {};

$DGD.select2D.paint = function (ver, hor) {
    var row = 0, col = 0;
    switch (ver) {
    case 'bottom':
        row++;
        row++;
        break;
    case 'center':
        row++;
        break;
    }
    switch (hor) {
    case 'right':
        col++;
        col++;
        break;
    case 'center':
        col++;
        break;
    }
    jQuery('#dgd_pos_selector .selected').removeClass('selected');
    jQuery('#dgd_pos_selector').find('tr:eq(' + row + ')').find('a:eq(' + col + ')').addClass('selected');
};

$DGD.select2D.choose = function (ver, hor) {
    jQuery('#hpos_selector').val(hor);
    jQuery('#vpos_selector').val(ver);
    $DGD.select2D.paint(ver, hor);
};

$DGD.select2D.init = function () {
    var ver = jQuery('#vpos_selector').val(),
        hor = jQuery('#hpos_selector').val();
    $DGD.select2D.paint(ver, hor);
};

$DGD.showTab = function (elem, tab) {
    jQuery(elem).parent('ul').next('.dgd_tab_container').find('.dgd_tab_content').addClass('hide');
    jQuery(elem).parent('ul').next('.dgd_tab_container').find('.' + tab).removeClass('hide');
    jQuery(elem).parent('ul').find('li').removeClass('selected');
    jQuery(elem).addClass('selected');
};

$DGD.attachColorPicker = function () {
    try {
        jQuery('.dgd-popup-color-picker').wpColorPicker();
    } catch (ignore) {
    }
};

$DGD.imageUploadCallback = function () {
    $DGD.restore_send_to_editor = window.send_to_editor;
    jQuery('#upload_bg_image_button').click(function () {
        var formfield = jQuery(this).prev('input');
        tb_show('Choose background image', 'media-upload.php?type=image&amp;TB_iframe=true');
        window.send_to_editor = function (html) {
            var imgurl = jQuery('img', html).attr('src'),
                imgwidth,
                imgheight;
            jQuery(formfield).val(imgurl);
            imgwidth = jQuery('img', html).attr('width');
            imgheight = jQuery('img', html).attr('height');
            jQuery('#dgd_stb_height').append('<option value="' + imgheight + '" selected="selected">' + imgheight + '</option>');
            jQuery('#dgd_stb_width').append('<option value="' + imgwidth + '" selected="selected">' + imgwidth + '</option>');
            tb_remove();
            window.send_to_editor = $DGD.restore_send_to_editor;
        };
        return false;
    });

    jQuery('#upload_close_image_button').click(function () {
        var formfield = jQuery(this).prev('input');
        tb_show('Choose close button image', 'media-upload.php?type=image&amp;TB_iframe=true');
        window.send_to_editor = function (html) {
            var imgurl = jQuery('img', html).attr('src');
            jQuery(formfield).val(imgurl);
            tb_remove();
            window.send_to_editor = $DGD.restore_send_to_editor;
        };
        return false;
    });
};

$DGD.paypalSubmit = function (hosted_button_id) {
    var form = document.createElement('form'),
        addInput = function (type, name, value) {
            var i = document.createElement('input');
            i.setAttribute('type', type);
            i.setAttribute('name', name);
            i.setAttribute('value', value);
            form.appendChild(i);
        };
    form.setAttribute('method', 'post');
    form.setAttribute('action', 'https://www.paypal.com/cgi-bin/webscr');
    form.setAttribute('target', '_blank');
    addInput('hidden', 'cmd', '_s-xclick');
    addInput('hidden', 'hosted_button_id', hosted_button_id);  // 'B4NCTTDR9MEPW'
    addInput('hidden', 'x', Math.floor(Math.random() * 127) + 10); // input type='image', using random integer as x
    addInput('hidden', 'y', Math.floor(Math.random() * 36) + 5);   // input type='image', using random integer as y
    document.body.appendChild(form);
    form.submit();
};

$DGD.oldVersionsSupport = function () {
    // prevent broken icon to show on WP<3.8
    jQuery('#menu-posts-dgdscrollbox').removeClass('menu-icon-dgdscrollbox').addClass('menu-icon-settings');
    jQuery('img[src="http://dashicons-welcome-comments"]').remove();
};

jQuery(document).ready(function () {
    if (pagenow === 'dgd_scrollbox') {
        $DGD.select2D.init();
        $DGD.attachColorPicker();
        $DGD.imageUploadCallback();
    }
    $DGD.oldVersionsSupport();
});