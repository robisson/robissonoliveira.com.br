/*jslint browser: true, continue: true, regexp: true, plusplus: true, sloppy: true */
/*global $DGD */
/*global jQuery */
/*global wp */
/*global pagenow */

$DGD.iframe = false;
$DGD.iframeDocument = false;

$DGD.setPreviewObj = function () {
    $DGD.previewObj = {
        'trigger': {"action": "scroll", "scroll": 50, "delaytime": 0, "element": ""},
        'height': "auto",
        "width": 300,
        "vpos": "bottom",
        "hpos": "right",
        "include_css": '1',
        "theme": "default",
        'jsCss': {"padding": "10", "margin": "10", "backgroundImageUrl": "", "backgroundColor": "", "boxShadow": "", "borderColor": "", "borderWidth": "0px", "borderRadius": ""},
        'transition': {"effect": "slide", "from": "b", "speed": 400},
        'social': {"facebook": 0, "twitter": 0, "google": 0, "pinterest": 0, "stumbleupon": 0, "linkedin": 0},
        'lightbox': {"enabled": 0, "color": "#000000", "opacity": "0.7", "blur": 0},
        'closeImageUrl': '',
        "hide_mobile": "1",
        "submit_auto_close": 5,
        "delay_auto_close": 40,
        "hide_submitted": "1",
        "cookieLifetime": 1,
        "receiver_email": false,
        "thankyou": "You are subscribed. Thank You!",
        "widget_enabled": "1",
        "tabhtml": "Subscribe!",
        "tab": "1",
        "id": "dgd_scrollbox-preview",
        "voff": 0,
        "hoff": 0
    };
};

$DGD.hasParam = function (obj, param) {
    return (typeof obj === 'object' && (typeof obj[param] === 'string' || typeof obj[param] === 'number' || typeof obj[param] === 'boolean'));
};

$DGD.stringArrayToObject = function (input, box) {
    var m;
    m = input.name.match(/dgd_stb(\[(\w+)\])(\[(\w+)\])*/);
    if (m !== null) {
        if ($DGD.hasParam(box, m[2])) {
            box[m[2]] = input.value;
        } else if ($DGD.hasParam(box[m[2]], [m[4]])) {
            box[m[2]][m[4]] = input.value;
        }
    }
};

$DGD.serializeObject = function (form, box) {
    var inputs = form.serializeArray();
    Object.keys(inputs).forEach(function (i) {
        $DGD.stringArrayToObject(inputs[i], box);
    });
};

$DGD.initIframe = function () {
    var iframeref;
    $DGD.iframe = document.getElementById('dgd_preview_frame');
    if (!$DGD.iframe) {
        iframeref = document.createElement('iframe');
        iframeref.id = 'dgd_preview_frame';
        document.body.appendChild(iframeref);
        $DGD.iframe = document.getElementById('dgd_preview_frame');
    } else {
        $DGD.iframe.removeAttribute("style");
    }
    if (!$DGD.iframeDocument) {
        $DGD.iframeDocument = $DGD.iframe.contentDocument || $DGD.iframe.contentWindow.document;
    }
    $DGD.iframeDocument.addEventListener('keyup', $DGD.closePreview, false);
    $DGD.iframeDocument.addEventListener('click', $DGD.closePreview, false);
    $DGD.iframeDocument.addEventListener('touchstart', $DGD.closePreview, false);
};

$DGD.closePreview = function () {
    if ($DGD.iframeDocument) {
        $DGD.iframeDocument.replaceChild(document.implementation.createHTMLDocument('Preview').documentElement, $DGD.iframeDocument.documentElement);
        $DGD.removeClass($DGD.iframe, 'activate');
        $DGD.removeClass(document.body, 'dgd_preview_mode');
    }
};

$DGD.makeItTransparent = function (doc) {
    //  thanks to: http://pankajparashar.com/posts/modify-pseudo-elements-css/
    var style = document.createElement("style");
    doc.head.appendChild(style);
    if (style.sheet.addRule) {    // IE, Chrome (Safari?)
        style.sheet.addRule('body::before', 'background: transparent');
    } else if (style.sheet.insertRule) {  // Firefox
        style.sheet.insertRule('body::before { background: transparent }', 0);
    }
    doc.body.style.background = 'transparent';
};

$DGD.loremIpsum = function () {
    return '<article class="page type-page hentry dgd_preview dgd_blurme"><div class="entry-content">' +
        '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam pellentesque dolor sit amet cursus tristique. Suspendisse molestie, neque et pretium auctor, quam nulla porttitor quam, ac tristique lacus risus et elit. Etiam a vehicula ipsum. Cras hendrerit urna dignissim leo efficitur, eget elementum eros venenatis. Nullam sem orci, gravida ut tempus ac, rutrum eu sapien. Vivamus vitae nisl nisl. Nulla eget magna mauris.</p>' +
        '<p>Etiam ex nisl, rutrum non odio nec, porta molestie nunc. Vestibulum tincidunt purus eget iaculis elementum. Morbi efficitur purus at diam ultricies gravida. Pellentesque at mi in ante auctor pretium. Morbi ac fringilla tellus, sed iaculis ex. Vivamus nec vestibulum nunc. Donec dictum, neque eu aliquet rutrum, mauris elit hendrerit metus, et lobortis metus quam in tortor. Integer efficitur risus quis lacinia ornare. Donec a lectus pharetra, faucibus dolor eget, euismod nibh. Nam facilisis eros nec eros dapibus, vel placerat libero iaculis. Morbi condimentum et tortor non finibus. Cras sit amet porttitor ipsum. Cras placerat orci non porta vestibulum.</p> ' +
        '<p>Nulla facilisi. Donec vitae ornare dui. Nulla condimentum rutrum tortor, at interdum elit consequat vel. Nullam in purus ultricies, facilisis nisl ac, tincidunt magna. Aliquam blandit finibus efficitur. Curabitur rhoncus ex non felis molestie tincidunt. Quisque a tortor eros. Praesent eu arcu at dui imperdiet aliquam. Vivamus fringilla eros eu mi tempus finibus quis nec ligula. Sed hendrerit arcu quis justo dapibus ullamcorper. Aenean ultricies velit vel arcu fermentum bibendum. Nulla consectetur vel lorem a imperdiet.</p> ' +
        '<p>Morbi aliquet tortor nunc, id ultricies neque vestibulum eget. Donec faucibus rutrum est eget gravida. Curabitur eu maximus nibh. Nam lacinia, enim vel consequat vehicula, arcu ex vulputate mi, non viverra ex ipsum non purus. Donec enim velit, imperdiet eu mollis sed, consectetur ac ex. Fusce feugiat ligula arcu, vitae posuere velit lacinia ac. Pellentesque dapibus interdum consectetur. Nam pharetra sollicitudin purus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi commodo consectetur elit eget finibus. Duis suscipit viverra consectetur. Suspendisse volutpat fermentum ligula, sit amet tristique ex fringilla in. Integer feugiat nisi vitae libero volutpat, eu pretium neque rhoncus. Nunc accumsan purus at sapien varius, quis posuere orci varius.</p> ' +
        '<p>Nam sed faucibus mi. Phasellus facilisis aliquam magna ut tempus. In hac habitasse platea dictumst. Morbi pharetra nisi quis odio luctus auctor. Sed rutrum imperdiet mi, vel pretium dolor. Suspendisse lacus ligula, luctus eu pharetra a, consequat ut tellus. Ut diam nulla, cursus id nibh quis, aliquet faucibus nulla. Aenean tincidunt ipsum eros, id aliquet lectus maximus nec. Mauris nec ante at felis euismod efficitur eu lacinia risus. Vestibulum tempor, nibh sit amet consequat ultricies, ex leo ultricies enim, at elementum metus eros in tellus. Nullam fermentum erat vel neque fermentum, quis dictum massa viverra. Proin sit amet nisi vitae mi congue iaculis in a elit.</p> ' +
        '<p>In euismod lacus eu ullamcorper ultricies. Cras at justo ut purus faucibus convallis. Donec ut nisl a eros blandit accumsan. Maecenas placerat eleifend tellus, et lacinia risus condimentum tincidunt. Donec malesuada volutpat est quis congue. Nunc placerat a nibh in tempus. Morbi sagittis velit ante, sed volutpat velit vehicula non. Integer aliquam sem turpis, ac consectetur quam viverra at. Fusce ac feugiat risus. Curabitur sed nisi justo. Nunc suscipit, elit in consequat placerat, leo enim eleifend velit, sit amet interdum lorem quam ac lorem. Quisque egestas mauris luctus lorem facilisis, eget imperdiet quam lobortis. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nullam pulvinar vel nisl vel semper.</p> ' +
        '</div></article>' +
        '<div class="dgd_overlay"></div>';
};

$DGD.generatePreviewPage = function (data) {
    var old_html = document.implementation.createHTMLDocument('old'),
        new_html = document.implementation.createHTMLDocument('Preview');

    old_html.documentElement.innerHTML = data;
    $DGD.iframe.style.height = ($DGD.screenheight - 32) + 'px';

    jQuery(old_html).find('head link[rel="stylesheet"], head style, head script').each(function () {
        if (typeof this === 'object' && this.outerHTML.length > 0) {
            new_html.head.appendChild(this);
        }
    });

    jQuery(old_html).find('body link[rel="stylesheet"], body style, body script').each(function () {
        if (typeof this === 'object' && this.outerHTML.length > 0) {
            new_html.body.appendChild(this);
        }
    });

    $DGD.previewObj.html = jQuery(data).find('div.entry-content').html();
    $DGD.addClass(document.body, 'dgd_preview_mode');
    $DGD.addClass($DGD.iframe, 'activate');
    new_html.body.innerHTML = $DGD.loremIpsum();
    $DGD.iframeDocument.replaceChild(new_html.documentElement, $DGD.iframeDocument.documentElement);
    $DGD.makeItTransparent($DGD.iframeDocument);

    $DGD.generateBox($DGD.previewObj, $DGD.iframeDocument.body);
    $DGD.previewObj.div = jQuery('#dgd_scrollbox-preview', $DGD.iframeDocument);
    $DGD.overlay = jQuery('.dgd_overlay', $DGD.iframeDocument);
    $DGD.placeBox($DGD.previewObj);
    $DGD.resizeBox($DGD.previewObj);
    $DGD.calculateBoxPlacement($DGD.previewObj);
    $DGD.showBox($DGD.previewObj);
    if (typeof $DGD.previewObj.lightbox === 'object' && $DGD.previewObj.lightbox.enabled && $DGD.previewObj.lightbox.blur) {
        jQuery('#page, .dgd_blurme', $DGD.iframeDocument).addClass('dgd_blur');
    } else {
        jQuery('#page, .dgd_blurme', $DGD.iframeDocument).removeClass('dgd_blur');
    }
};

$DGD.renderAjaxError = function (jqXHR, textStatus, errorThrown) {
    $DGD.echo(textStatus + ' ' + errorThrown);
};

$DGD.getHtmlAndShow = function () {
    var url = jQuery('form#post').attr('action') + '?t=' + Date.now(),
        previewField = jQuery('input#wp-preview'); // IE9+
        // Bug: consider using DOMParser, http://stackoverflow.com/questions/3103962/converting-html-string-into-dom-elements

    if (wp.autosave) {
        wp.autosave.server.triggerSave();
    }
    previewField.val('dopreview');

    jQuery.ajax({
        type: "POST",
        url: url,
        data: jQuery("form#post").serialize(), // serializes the form's elements.
        success: $DGD.generatePreviewPage,
        error: $DGD.renderAjaxError
    });
    previewField.val('');

    return false; // avoid to execute the actual submit of the form.
};

$DGD.showPreview = function (e) {
    e.preventDefault();
    if (!$DGD.iframe) {
        $DGD.initIframe();
    }
    window.scrollTo(0, 0); // scroll to top
    $DGD.closePreview();
    $DGD.setPreviewObj();
    $DGD.serializeObject(jQuery('form#post'), $DGD.previewObj);
    $DGD.getHtmlAndShow();
    return false;
};

$DGD.showDebug = function (e) {
    //  var iframe = document.getElementById('dgd_preview_frame');
    e.preventDefault();
    return false;
};

$DGD.replacePreviewButton = function () {
    if (pagenow === 'dgd_scrollbox' && typeof wp === 'object') {
        jQuery('#preview-action a#post-preview').remove();
        jQuery('#preview-action').append('<a href="#" class="preview button" id="scrollbox-preview">Preview Scrollbox</a>');
        jQuery('#preview-action a#scrollbox-preview').click(function (event) {$DGD.showPreview(event); });
    }
};

jQuery(document).ready(function () { $DGD.replacePreviewButton(); });

/*
Hints for future me and anybody else:

*) Dont ask how to make it work on IE8.
*) iframe object must exist in static html, adding it dynamically does not render it in Firefox.
*) iframe object must be put outside of side_box, otherwise it will not overlay entire screen (currently: will be added into footer)

*/