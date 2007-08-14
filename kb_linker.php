<?php
/*
Plugin Name: KB Linker
Plugin URI: http://adambrown.info/b/widgets/kb-linker/
Description: Looks for user-defined phrases in posts and automatically links them. Example: Link every occurrence of "Wordpress" to wordpress.org.
Author: Adam R. Brown
Version: 1.01
Author URI: http://adambrown.info/
*/

/* 
	IMPORTANT NOTE TO ANYBODY CONSIDERING ADDING THIS PLUGIN TO A WP-MU INSTALLATION:
	If you aren't sure whether you are using a WP-MU blog, then you aren't. Trust me. If this warning applies to you, then you will know it.
	For WP-MU administrators: You should not use this plugin. Your users could use it to place (potentially malicious) javascript into their blogs.
	This plugin is PERFECTLY SAFE for non-WP-MU blogs, so ignore this message if you're using regular wordpress (you probably are).
*/

/*		DEVELOPMENT NOTES. If you're trying to modify something, read the following comments.
		GENERAL NOTE:
			You will notice that I've included extensive commenting in the code below. I do not have time to support this plugin. The commenting is there to make this plugin
			easy to modify. Please try making modifications on your own before posting a support question at the plugin's URI.
			That being said, you are welcome to post well-informed support questions on my site.
		DATABASE STRUCTURE
			the options->KB Linker page will create a set of matching terms and URLs that gets stored as a list.
			structure of option "kb_linker":
				pairs => array( see below)
				text=>	same content as pairs, but in unprocessed form (for displaying in the option's form)
				plurals => 1, 0		if 1, we should look for variants of the keywords ending in s or es
*/

function kb_linker($content){
	$option = get_option('kb_linker');
	extract($option);
	// uncomment for testing (to override options):
	#$pairs = array( 'contributor'=>'http://google.com', 'a'=>'http://yahoo.com/', 'scripting'=>'scripting', 'don'=>'don', 'first post'=>'firstpost.org', 'first'=>'first.org', 'wp'=>'WP.ORG');
	if ( !is_array($pairs) )
		return $content;

	if ( 1 != $plurals ){	 // we do basically the same thing whether we're looking for plurals or not. The non-plurals case is easier, so here it is first:
		foreach ($pairs as $keyword => $url){
			// we don't want to link the keyword if it occurs within another link.
			// so let's find all instances where the keyword is in a link and replace it with something innocuous. Let's use &&&, since WP would pass that
			// to us as &amp;&amp;&amp; (if it occured in a post), so it would never be in the $content on its own.
			// this has two steps. First, look for the keyword as linked text:
			$content = preg_replace( '|(<a[^>]+>)(.*)('.$keyword.')(.*)(</a.*>)|U', '$1$2&&&$4$5', $content);
			// Second, look for the keyword inside tags. E.g. if they're linking every occurrence of "Google" manually, we don't want to find 
			// <a href="http://google.com"> and change it to <a href="http://<a href="http://www.google.com">.com">
			// More broadly, we don't want them linking anything that might be in a tag. (e.g. linking "strong" would screw up <strong>). Here goes:
			$content = preg_replace( '|(<[^>]*)('.$keyword.')(.*>)|U', '$1&&&$3', $content);
			// I'm sure a true master of regular expressions wouldn't need the previous two steps, and would simply write the following expression better. But this works.

			// now that we've taken the keyword out of any links it appears in, let's look for the keyword elsewhere.
			$content = preg_replace( '|(?<=[\s>;"\'/])('.$keyword.')(?=[\s<&.,!\'";:\-/])|i', '<a href="'.$url.'" class="kblinker">$1</a>', $content, 1);	// that "1" at the end limits it to replacing the keyword only once per post.
			
			/* some notes about that regular expression:
			(?<=[\s>;"\'])
				(?<=	marks it as a lookbehind assertion
				to ensure that we are linking only complete words, we want keyword preceded by one of space, tag (>), entity (;) or certain kinds of punctuation (escaped with \ when necessary)
			(?=[\s<&.,\'";:\-])
				(?=	marks this as a lookahead assertion
				again, we link only complete words. Must be followed by space, tag (<), entity (&), or certain kinds of punctuation. 
				Note that some of the punctuations are escaped with \
			*/
			
			// restore the keyword when it occurs in links and tags:
			$content = str_replace( '&&&', $keyword, $content);
		}
	}else{	// if they want us to look for plurals too:
		foreach ($pairs as $keyword => $url){
			// look for the keyword in existing links -- remove it. Exact same as in non-plural case.
			$content = preg_replace( '|(<a[^>]+>)(.*)('.$keyword.')(.*)(</a.*>)|U', '$1$2&&&$4$5', $content);
			
			// look for the keyword in tags -- remove it. Also same as non-plural case.
			$content = preg_replace( '|(<[^>]*)('.$keyword.')(.*>)|U', '$1&&&$3', $content);

			// now that we've taken the keyword out of any links it appears in, let's look for the keyword elsewhere.
			$content = preg_replace( '|(?<=[\s>;"\'/])('.$keyword.'s?)(?=[\s<&.,!\'";:\-/])|i', '<a href="'.$url.'" class="kblinker">$1</a>', $content, 1);	// that "1" at the end limits it to replacing once per post.
			
			// restore the keyword when it occurs in links:
			$content = str_replace( '&&&', $keyword, $content);
		}
	}
	return $content;
}


function kb_linker_options_page(){
	if ( $_POST['kb_linker'] ){
		$pairs = str_replace("\r", '', $_POST['kb_linker']);
		$pairs = explode("\n", $pairs);
		foreach( $pairs as $pair ){
			$pair = explode( "->", $pair );
			if ( ( '' != $pair[0] ) && ( '' != $pair[1] ) )
				$new[ $pair[0] ] = $pair[1];
		}
		$pairs = $new;	// contains the pairs as an array for use by the filter
		$text = $_POST['kb_linker'];	// contains the pairs as entered in the form for display below
		
		$plurals = ( 1 == $_POST['kb_plurals'] ) ? 1 : 0;
		$option = array( 'pairs'=>$pairs, 'text'=>$text, 'plurals'=>$plurals );	// store both versions of the option, pairs and text
		update_option( 'kb_linker', $option );
		print '<div id="message" class="updated fade"><p><strong>KB Linker options updated.</strong> <a href="'.get_bloginfo('url').'">View site &raquo;</a></p></div>';
	}else{
		$option = get_option('kb_linker');
		extract($option);
	}

	$checked = ( 1 == $plurals ) ? 'checked="checked"' : '' ;

	$sample = 'wordpress->http://wordpress.org/
google->http://www.google.com/
knuckleheads->http://www.house.gov/';

	print '
	<div class="wrap">
	<h2>KB Linker</h2>
	<p>KB Linker will link phrases you specify to sites you specify. For example, you could make it so that whenever "Wordpress" occurs in a post it is automatically linked to wordpress.org.</p>
	<p>Enter your keyword-URL pairs in the box below. Each pair should appear on its own line. Separate each keyword from its respective link with "->". Look at the bottom of this page for important details. Here are a few examples to get you going:</p>
	<blockquote><pre>'.$sample.'</pre></blockquote>
	<p>Alright, knock yourself out:</p>
	
	<form method="post" action="http://'.$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'].'">
	<textarea id="kb_linker" name="kb_linker" rows="10" cols="45" class="widefat">'.$text.'</textarea>
	<p><input type="checkbox" '.$checked.' name="kb_plurals" id="kb_plurals" value="1" /> Also link the keyword if it ends in <i>s</i> (i.e. plurals in certain languages)</p>
	<p class="submit" style="width:420px;"><input type="submit" value="Submit &raquo;" /></p>
	</form>
	
	<p>Considerations:</p>
	<ul>
		<li>URLs should be valid (i.e. begin with http://)</li>
		<li>The same URL can appear on more than one line (i.e. with more than one keyword).</li>
		<li>Because a word can only link to one site, a keyword should not appear on more than one line. If it does, only the last instance of the keyword will be matched to its URL.</li>
		<li>If one of your keywords is a substring of the other--e.g. "download wordpress" and "wordpress"--then you should list the shorter one later than the first one.</li>
		<li>Keywords are case-insensitive (e.g. "wordpress" is the same as "WoRdPrEsS").</li>
		<li>Spaces count, so "wordpress" is not the same as "wordpress ".</li>
		<li>Keywords will be linked only if they occur in your post as a word (or phrase), not as a partial word. So if one of your keywords is "a" (for some strange reason), it will be linked only when it occurs as the word "a"--when the letter "a" occurs within a word, it will not be linked.</li>
	</ul>
	</div>
	';
}

function kb_linker_admin_page(){
	add_submenu_page('options-general.php', 'KB Linker', 'KB Linker', 5, 'kb_linker.php', 'kb_linker_options_page');
}

add_filter('the_content', 'kb_linker');
add_action('admin_menu', 'kb_linker_admin_page');
?>