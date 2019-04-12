=== Dreamgrow Scroll Triggered Box ===
Contributors: pk2000,madisn,Eero Hermlin
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=B4NCTTDR9MEPW
Tags: call to action, pop-up, newsletter signup popup, newsletter sign-up, scroll triggered pop-up, subscription pop-up, Lightbox, CSS3, blur, page overlay,
Requires at least: 3.0.1
Tested up to: 4.5.2
Stable tag: 2.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Scroll Triggered Box will boost your conversion rates! The plugin displays a pop-up box with customizable content. Drawing your visitor's attention to call-to-action will dramatically improve your conversion.

== Description ==

If there’s anything that can be called the silver bullet in getting people to take action then this is it! Scroll triggered box has consistently increased conversions rate by several times. Newsletter sign-up, Facebook like button, social bookmarking or something else you want to draw attention to after people have engaged with your website. Scroll triggered box will increase the conversion rate many times over.

The box is designed to get the attention of the visitors who have engaged with your site. The box triggers on certain event you specify, such as percentage of scroll, reaching an end of the post or comments. This verifies that visitors are engaged with the content and presenting them with a call-to-action. For up to date information about future plans check our plugin page at [dreamgrow.com](http://www.dreamgrow.com/dreamgrow-scroll-triggered-box/).

= Make More Visitors Convert =

Scroll triggered box will make sure that more visitors to your site respond to your call-to action. Some examples what other sites are using it for:

* Getting more subscribers to your newsletter
* Making people share your posts
* Driving visitors deeper with related posts
* Filling out a contact form
* Displaying best offers

Highly customizable, you can display any content, any call-to-action with this plugin. Sharing, related content, signups, sales promotions, anything you want.

= What do you get with this plugin? =

* Set the amount of days for the box to stay hidden if visitor closes it
* Design templates to save you time with the appearance of the box
* Fully customization if you want to use your own design (HTML and CSS)
* Control the position of the box (pages, posts, frontpage, left, right)
* When to trigger the box (% of scroll, specific element)

== Installation ==

1. Upload the zip-file from within WordPress plugins section or use the automatic installation option.
2. Enter the HTML-code for your box's content and save.

== Screenshots ==

1. Standard WordPress editor with multi language support.
2. Select posts, pages, categories and tags that show the box. Display the box top, bottom, left, right or middle. Or if you so wish several boxes per page.
3. Tons of settings to make each box behave exactly as you want.

== Frequently Asked Questions ==

= Box does not show up. Box shows only in one browser and not in another =

* Possibly you have checked "Where and how to trigger?"->"show"->"Admin only" and not logged in, or logged in only in one browser.
* If you have closed box from "x" (close button in upper right corner) or made submission, it waits till the time defined in "Popup frequency" setting. To overcome this you must either:
 * delete cookie (it's named like "dgd_scrollbox-00000"), or 
 * set "Popup frequency" setting to "Each time", or
 * use browser in "incognito mode" (where it does not use cookies from previous browser sessions). 
* If box uses setting "close forever after submission" and there is made submission (even mistakenly pressed "submit" followed by lightning-fast back button press counts), it will not appear any more as well. To overcome this you must delete cookie or use browser in "incognito mode" (please see previous point). 
* If you use some cache ("performance") plugin, then it could happen that boxes will appear only to new posts/pages. That's because older pages are already cached without box. To get boxes working also for pages created before box creation, you must use your cache plugin empty (purge) cache option.
* It might happen also that you have checked "Hide on mobiles", but one of your browsers uses actually some mobile browser user-agent string.

= I don't get any e-mails =

Please check following:

* is "Actions after form submission"->"Send submitted values to email" filled with valid e-mail address, and
* does your WordPress installation allow sending e-mails to that e-mail (sometimes some domains e-mails are not sent out), and
* maybe the notification e-mails are ended up into "spam" folder of your Inbox.


= How to use some other plugin's submission form (Gravity Forms, Contact Form 7 ...) with Scroll Triggered Box  =

* When using any 3rd party submission form, "Actions after form submission"->"Send submitted values to email" field must be empty.  
* Prefer shortcode over Widget option.
* Please note that options in "Actions after form submission" and "Auto close" sections work only together with plugin own submission form.

= How to integrate Scrollboxes with MailChimp =

You must use some MailChimp plugin what provides MailChimp submission form either by shortcode or Widget. 

= How I can use Widgets in boxes =

* Open WP admin menu->Appearance->Widgets. Drag needed widget to Widget area named "Scrollbox". 
* In Scrollbox Admin screen, please enable using Widgets by checking "Scrollbox design"->"Enable Widget area". (This must be done separately, as you may have several Scrollboxes but want to show Widget only one of them).
* If Widget is all you want to show, you can remove existing box default content completely. 
* If Widget contains some sort of submission form (feedback, comment insertion, user registration...) you must take into account suggestions for 3rd party submission form plugin users above.

= There's only "email" field but I would like to get names or user questions/feedback also =

* You can manually add unlimited number of text, textarea, select fields in HTML format inside of existing `<form>` tags, they are included to notification e-mail automatically.
* Example about adding feedback field: open scroll box editor in HTML mode and add `<input name="Feedback" type="text" value="" />`. HTML content of Scrollbox will become something like this:
`<h5>Sign up for our Newsletter</h5>
<ul>
	<li>Fresh trends</li>
	<li>Cases and examples</li>
	<li>Research and statistics</li>
</ul>
Enter your email and stay on top of things,
<form class="stbContactForm" action="#" method="post">
E-mail: <input id="email" name="email" required="required" type="email" value="" />
Message: <input name="feedback" type="text" value="" />
<input class="stb-submit" type="submit" value="Subscribe" />
</form>
<p class="stbMsgArea"></p>`

And it works. PS. e-mail field is not even mandatory.


= I'm using it with some 3rd party submission form, but now some options does not work =

Options in "Actions after form submission" and "Auto close" section will work only with plugin own submission form and when "Actions after form submission"->"Send submitted values to email" is filled.

= Preview (or view) button does not work =

Sorry, but preview is not developed yet.

= Some of the box contents do not fit into the box area =

Adjust settings in "Scrollbox design"->"Popup box dimensions" 

= Idea is OK, but existing templates do not fit to my site or my needs =

* You don't have to limit yourself with default css and default html. Open the editor in "HTML" mode, clear everything and start from scratch. Only sky is the limit. 
* In "Scrollbox design"->"Theme", choose "I'm using my own theme". In this case plugin default styles for text and button are not used and you can use purely your own CSS definitions for any of the elements.
* There are many settings in Admin screen "Scrollbox design" section, try them out.
* Try using custom background image ("Scrollbox design"->"Background Image"). Plugin supports also iamges with transparent areas (.gif, .png) and even .png files opacity (e.g. alpha channels or partial transparency). With using partially transparent background image you can create really eye-catching Scrollboxes. NB. Box will be resized to match with background image. When using transparency or opacity in background image, background color field must be empty.

= I need to do amendments to one of plugin templates =

Best way to change some of plugin styles is with adding style definition to your own theme css file and overwriting plugin css values with !important keyword. 
Example. Changing submit button border, text color and background color needs adding of following block into  some of your theme css file:
`input.stb-submit {
    color: navy !important;
    border: 2px solid black !important;
    background-color: whitesmoke !important;
}`

Avoid changing any plugin files under plugin directory, including css files. Such changes will get lost during upgrades. Overwriting with !important is sufficient. 

= I want to use structured feedback message instead of just one plain sentence =

Go ahead, "Thank you" field allows you to use HTML.

= I would like to redirect users to another page after submission =

There's no "user-friendly" way at the moment, but it can be achieved by entering into ""Thank you" message" field this piece of HTML: 
`<script>window.location='http://yourdomain.com/path';</script>` 
(Of course use actual URL instead of 'http://yourdomain.com/path').

= What values can be used for Element triggering =

"Element" field takes any single <a href="http://api.jquery.com/category/selectors/">jQuery selector</a>. General examples about them:

<ul>
<li>`#comments` - select HTML element having ID of 'comments'. <a href="http://api.jquery.com/id-selector/">jQuery about #ID selector</a></li>
<li>`.comment-content` - select HTML element with a class of 'comment-content'. <a href="http://api.jquery.com/class-selector/">jQuery about .class selector</a></li>
<li>`body` - select HTML body tag content `<body>...</body>` (e.g. whole page). <a href="http://api.jquery.com/element-selector/">jQuery about Element selector</a></li>
</ul>
And those can be combined, like this:

<ul>
<li>`h1.entry-title` - select h1 element having class 'entry-title'</li>
<li>In case of separate triggering objects, <a href="http://api.jquery.com/multiple-selector/">use comma separated list</a></li>
</ul>
Here some common elements you can use on most WordPress themes:

<ul>
<li>`body` - Entire page</li>
<li>`.site-title` - Site title</li>
<li>`.site-description` - Site description ('Just another WordPress site')</li>
<li>`.nav-menu` - Navbar</li>
<li>`.entry-title` - Post title</li>
<li>`.entry-date` - Post date</li>
<li>`.author` - Post Author</li>
<li>`.entry-content` - Post content</li>
<li>`#comments` - Comments area</li>
<li>`.comment-content` - Comment text (inside comments area)</li>
<li>`#respond` - Comment form</li>
<li>`#comment` - Comment form textarea</li>
<li>`input#submit` - Comment submit button</li>
<li>`#secondary` - Secondary area (Widgets area)</li>
<li>`.widget_search` - Search Widget</li>
<li>`.widget_recent_entries` - Recent Entries Widget</li>
<li>`.widget_archive` - Archive Widget</li>
<li>`.widget_categories` - Categories Widget</li>
<li>`.widget_meta` - Meta Widget</li>
<li>`#colophon` - Page Footer ("Proudly powered by WordPress")</li>
<li>`.dgd_stb_box` - Any scrollbox content (Yes, you can trigger new box from another scroll triggered box)</li>
</ul>


== Changelog ==

= 2.3 =
* Added "Lightbox" (page overlay) possibility;
* Added CSS3 content blurring option to be used with "Lightbox" (Works with browsers supporting CSS3);
* Added option for leaving box open while scrolling back up;
* Added support for 100% width and 100% height;
* Added support for Textarea and Select elements in Forms;
* Added Box resizing to screen width if screen is narrower than in box definition;
* Improved support for MailChimp native html code;
* Fixed "Scroll to element" functionality
* Fixed conflict with Weaver II Pro Theme
* Fixed conflict with IN

= 2.2.0 =
* Added "Tab" for reopening closed Scrollbox
* "Preview Scrollbox" button in Edit screen gives live preview (not guaranteed to work on older than WP 4.0)

= 2.1.3 =
* Added Show->Posts page option

= 2.1.2 =
* Scrollbox allow empty DIV, P and SPAN tags (to work together with MailChimp native code, for example)
* Bugfix: Scrollbox will now appear also if first page is set to blog page

= 2.1.1 =
* Added Widgets support!
* Added support for JavaScript minification tools
* Bugfix: content area stuff is showing in scrollbox
* Bugfix: "fade-in" not working

= 2.1 =
* Tested and fixed usage together with some third party plugins / shortcodes (example: Contact Form 7, Gravity Forms, WooCommerce, Use Google Libraries)
* Added custom triggering options (time delay, element mouseover, element click). Now is also possible to trigger without scroll.
* In addition to pages inclusion list also pages exclusion list exists
* Added editable and translatable "Thank you" message
* Added option to auto close after defined period of showing
* Added option to close forever for submitted users
* Bugfix: submit button background was wrong on some themes

= 2.0.3 =
* bugfix: Add Media button didn't work

= 2.0.2 =
* Bugfix: Some pages functionality was broken if no boxes to show

= 2.0.1 =
* Bugfix: Scrollbox default content was used also for other page types

= 2.0.0 =
* Support for multiple boxes each having own design and content
* Added visual editor option
* Improved box showing options: possible to show box only to certain Category(ies) or Tag(s)
* Improved box placement options: boxes can be centered, or placed to any edge or corner
* Added Background image option
* Added "Fade-in" effect option

= 1.4 =
* Added shortcode support for box content
* Added option hide the box in mobile devices
* Added option to show on Custom Post Types
* Fixed compatibility issues with other plugins (small js update)
* Fixed is_set bugs
* Updated facebook like button
* Updated texts and admin screenshot

= 1.3.2 =
* Fixed default settings bug.
* Fixed default form not submitting email.
* Added email field to settings page.
* Updated settings page to work with WP 3.8

= 1.3.1 =
* Fixed a WPML bug on admin.
* Fixed Pin It button.
* Added information about box visibility and option to show the box again.

= 1.3 =
* Added option to include social buttons (Facebook, Twitter, Google+, Pinterest, Stumbleupon, LinkedIN).

= 1.2.2 =
* Fixed a minor bug in the default settings.

= 1.2.1 =
* Fixed an issue where user settings were lost after updating. Settings are now restored from the previous update (1.1).

= 1.2 =
* Updated jquery.cookie.js to version 1.3.
* Moved js files to footer.
* Added namespace to js functions and variables.
* Rebuilt entire css.
* Added option to center the box.

= 1.1 =
* Added WPML support. Different HTML for every language.
* Default form now sends email to the site administrator.

= 1.0.1 =
* Fixed themes.

= 1.0 =
Release

== ToDo list ==

* Add some new Themes/Templates