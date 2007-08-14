=== KB Linker ===
Contributors: adamrbrown
Donate link: http://adambrown.info/b/widgets/donate/
Tags: link, seo, links
Requires at least: 2.0
Tested up to: 2.2.1
Stable tag: trunk

Looks for user-defined phrases in posts and automatically links them to specified URLs. Example: Link every occurrence of "Wordpress" to wp.org.

== Description ==

KB Linker will link phrases you specify to sites you specify. For example, you could make it so that whenever “Wordpress” occurs in a post it is automatically linked to wordpress.org. All you do is enter keyword->URL pairs into the Options->KB Linker page in your admin screen. Enter pairs so that they look something like this:

`
    wordpress->http://wordpress.org/
    google->http://www.google.com/
    knuckleheads->http://www.house.gov/
`

You’ll find more detailed instructions on the admin page. But really, it’s pretty easy. And KB Linker does the hard work of (a) making sure that words aren’t already linked and (b) making sure only whole words, not partial words, get linked.

The plugin is commented extensively, so if you want to tweak its behavior, give it a go.

= Support =

If you post your support questions as comments below, I probably won't see them. If the commenting within the plugin file doesn't answer your questions, you can post support questions at the [KB Linker plugin page](http://adambrown.info/b/widgets/kb-linker/) on my site.

== Installation ==

1. Upload `kb_linker.php` to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Go to the new 'Options => KB Linker' admin page. Follow the instructions.

All done. Go to your blog's home page and admire all the automatically inserted links.

== License ==

This plugin is provided "as is" and without any warranty or expectation of function. I'll probably try to help you if you ask nicely, but I can't promise anything. You are welcome to use this plugin and modify it however you want, as long as you give credit where it is due.

== Screenshots ==

Ain't none. Sorry.

== Frequently Asked Questions ==

= Why would I want this? =

Well, that's obviously up to you. Maybe you don't. But if there is something that you're always linking to, this plugin might help you. For example, I've got a travel blog. I have set it up so that everytime I mention certain cities, the city's name gets linked automatically to a map of that city.

This plugin could also serve SEO (search engine optimization) purposes if you're into that sort of thing; you could use it to ensure plenty of internal cross-linking between your posts.

= I want the plugin to behave slightly differently. =

Take a look at the plugin file. I marked up the code heavily to make it easy to tweak. After looking at the code, if you have a question, you can post a comment on the plugin's page.

= I have a question that isn't addressed here. =

If you post your support questions as comments below, I probably won't see them. If the commenting within the plugin file doesn't answer your questions, you can post support questions at the [KB Linker plugin page](http://adambrown.info/b/widgets/kb-linker/) on my site.