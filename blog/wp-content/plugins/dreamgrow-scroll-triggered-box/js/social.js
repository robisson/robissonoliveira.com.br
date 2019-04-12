function DgdCreateSocialButtons(box) {
	this.ul=false;
	this.container=false;

	this.addFbButton = function () {
		if (typeof (FB) != "undefined") {
			FB.init({ status: true, cookie: true, xfbml: true });
		} else {
			jQuery.getScript("//connect.facebook.net/en_US/all.js#xfbml=1", function () {
				FB.init({ status: true, cookie: true, xfbml: true });
			});
		}	
		this.ul.append('<li class="fb '+box.social.facebook+'"><div class="fb-like" data-send="false" data-share="false" data-action="like" data-layout="'+box.social.facebook+'" data-width="200" data-show-faces="false"></div></li>');
	}

	this.addTwitterButton = function () {
		 if (typeof (twttr) != "undefined") {
			twttr.widgets.load();
		} else {
			jQuery.getScript("//platform.twitter.com/widgets.js");
		}

		this.ul.append('<li class="twitter '+box.social.twitter+'"><a href="https://twitter.com/share" data-url="'+dgdStbAjax.permalink+'" data-text="'+dgdStbAjax.title+'" class="twitter-share-button" >Tweet</a></li>');
		if(box.social.twitter=='no-count') {
			this.ul.find('.twitter a').attr('data-count', 'none');
		} else if (box.social.twitter=='vertical') {
			this.ul.find('.twitter a').attr('data-count', 'vertical');
		}
	}

	this.addGoogleButton = function () {
		if (typeof (gapi) != "undefined") {
			jQuery(".g-plusone").each(function () {
				gapi.plusone.render($(this).get(0));
			});
		} else {
			jQuery.getScript("https://apis.google.com/js/plusone.js");
		}

		this.ul.append('<li class="google '+box.social.google+'"><div class="g-plusone"></div></li>');
		if(box.social.google=='annotation') {
			this.ul.find('.google div').attr('data-size', 'medium');
			this.ul.find('.google div').attr('data-annotation', 'none');
		} else {
			this.ul.find('.google div').attr('data-size', box.social.google);
		}
	}

	this.addLinkedinButton = function () {
		if (typeof (IN) != "undefined") {
			IN.parse();
		} else {
			jQuery.getScript("//platform.linkedin.com/in.js");
		}	
		this.ul.append('<li class="linkedin '+box.social.linkedin+'"><script type="IN/Share"'+(box.social.linkedin != 'none' ? ' data-counter="'+box.social.linkedin+'"' : '')+'></script></li>');
	}

	this.addStumbleuponButton = function () {
		jQuery.getScript("//platform.stumbleupon.com/1/widgets.js");
		this.ul.append('<li class="stumbleupon s'+box.social.stumbleupon+'"><su:badge layout="'+box.social.stumbleupon+'"></su:badge></li>');
	}

	this.addPinterestButton = function () {
		jQuery.getScript("//assets.pinterest.com/js/pinit.js");
		this.ul.append('<li class="pinterest '+box.social.pinterest+'"><a href="http://pinterest.com/pin/create/button/?url='+dgdStbAjax.permalink+'&media='+dgdStbAjax.thumbnail+'" class="pin-it-button" count-layout="'+box.social.pinterest+'"><img border="0" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" /></a></li>');
	}

	if(box.div.find('.inscroll').length>0) {
		this.container=box.div.find('.inscroll');
	} else if (box.div.find('#inscroll').length>0) {
		this.container=box.div.find('#inscroll');
	} else {
		this.container=box.div;
	}

	if(!jQuery(this.container).find('ul.stb_social').length) {
		// add ul if needed
		jQuery(this.container).append('<ul class="stb_social"></ul>');
	}
	this.ul=jQuery(this.container).find('ul.stb_social');

	if(box.social && this.ul.length>0) {
		if (box.social.facebook) this.addFbButton();
		if (box.social.twitter) this.addTwitterButton();
		if (box.social.google) this.addGoogleButton();
		if (box.social.linkedin) this.addLinkedinButton();
		if (box.social.stumbleupon) this.addStumbleuponButton();
		if (box.social.pinterest) this.addPinterestButton();
	}
}
