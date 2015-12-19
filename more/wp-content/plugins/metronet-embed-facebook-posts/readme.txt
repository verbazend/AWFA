=== Metronet Embed Facebook posts ===
Contributors: metronet, ryanhellyer
Donate link: http://www.metronet.no/
Tags: facebook, simple, embed, shortcode
Requires at least: 3.6
Stable tag: 1.2.1


Easily embed Facebook posts into your pages

== Description ==

Easily embed Facebook posts into your pages. Facebook posts can be added into WordPress posts or pages via oEmbed or a shortcode.

You can see a demonstration <a href="http://random.ryanhellyer.net/embedded-facebook-post/">here</a>.


== Installation ==

Install and activate the plugin.

If you have oEmbed support enabled in WordPress, then any Facebook post URLs will be automatically embedded.

Alternatively, you can embed Facebook posts by adding the URL for the post to the facebookpost shortcode:
`[facebookpost https://www.facebook.com/metronet/posts/10151735688640772]`


== Frequently Asked Questions ==

= Why should I use this plugin? =

If you want to easily embed Facebook posts in your WordPress posts and pages

= Does it work for WordPress version x.x.x? =

We only provide support for the latest version of WordPress.

= Can I customize how the post is displayed on my web page? =

No. Facebook does not allow this.

= If I embed a very long post, will it get truncated? =

Yes. Facebook truncates the posts with a "see more" option.

= How do I find public posts on Facebook to embed? =

Facebook provides a "<a href="https://developers.facebook.com/docs/plugins/embedded-posts/best-practices/">best practices guide for finding public Facebook posts to embed</a>".

== Screenshots ==

1. An example embedded Facebook post


== Changelog ==

= 1.2.1 =
* Added escaping to URL output

= 1.2 =
* Added JavaScript to footer

= 1.1 =
* Addition of oembed support.

= 1.0 =
* Initial plugin release


== Credits ==

Thanks to the following (in no particular order) for help with the development of this plugin:<br />

* <a href="http://blog.uysalmustafa.com/">Mustafa Uysal</a> - Made the initial plugin suggestion<br />
* <a href="http://ronalfy.com/">Ronald Huereca</a> - Suggested using oEmbed<br />
* <a href="http://twitter.com/lanche86/">Milan Ivanović</a> - Provided assistance with regex issues<br />
* <a href="http://konstruktors.com/">Kaspars Dambis</a> - Suggested moving scripts to footer and only loading when embedded<br />
