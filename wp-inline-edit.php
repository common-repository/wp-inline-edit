<?php
/*
Plugin Name: Wp Inline Edit
Plugin URI:  http://webgarb.com/wp-inline-edit/
Description: Wp Inline Edit will add ability to author/admin/editor to edit your post/page from page/post itself without going to wp-admin edit page.
Version: 1.0
Author: Webgarb
Author URI: http://Webgarb.com
*/
/*
License : http://www.gnu.org/copyleft/gpl.html
Copyright (c) 2012-2013 WebGarb 
Last Update : April 26 2012
*/
define("WP_INLINE_EDIT_VERSION",1.0);
define("WP_INLINE_EDIT_PATH",plugins_url("",__FILE__));
define("WP_INLINE_EDIT_DIR",str_replace("\/","/",dirname(__FILE__)) );


function wp_inline_enqueue_scripts() {
		wp_enqueue_script( 'jquery' );
}    
add_action('wp_enqueue_scripts', 'wp_inline_enqueue_scripts');

add_action("wp_footer","wp_inline_edit_wp_footer");
function wp_inline_edit_wp_footer() {
	if(!current_user_can('edit_published_posts')) {
		return false;
	}
	if(is_single() OR is_page()):
	?>
	<script type="text/javascript">
	<!--
	/* Post Title */
	jQuery(".wpined-title").each(function() {
	
	jQuery(this).dblclick(function(){
	 wpined_loader("Loading PLease Wait");
	jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", {action:"wp_inline_edit",t:"title",r:"get", postID: jQuery(this).attr("rel") },
   	function(d) {
	 wpined_loader_close();
    jQuery.globalEval(d);
	wpined_title_blur();
 	});

	});
	
	}); //jQuery(".wpined-title").each()
	
	function wpined_title_blur() {
	
	jQuery(".wpined-title").each(function() {
		
	jQuery(this).prev("textarea").blur(function() {
	 wpined_loader("Title Saving Please Wait");
	jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", {action:"wp_inline_edit",t:"title",r:"save", postID: jQuery(this).next().attr("rel"),content:jQuery(this).val() },
   	function(d) {
	 wpined_loader_close(); 
    jQuery.globalEval(d);
 	});
	
	});
	
	}); //jQuery(".wpined-title").each(
	
		
	}
	
	/* Post Content */
	
	jQuery(".wpined-con").dblclick(function(){
	wpined_loader("Loading Please Wait");
	jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", {action:"wp_inline_edit",t:"content",r:"get", postID: jQuery(this).attr("rel") },
   	function(d) {
	
    jQuery.globalEval(d);
	wpined_loader_close();
	wpined_content_blur();
 	});
	
	});
	
	function wpined_content_blur() {
	
	jQuery(".wpined-con").prev("textarea").blur(function() {
	wpined_loader("Content Saving Please Wait");
	jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", {action:"wp_inline_edit",t:"content",r:"save", postID: jQuery(this).next().attr("rel"),content:jQuery(this).val() },
   	function(d) {
    jQuery.globalEval(d);
 		wpined_loader_close();
	});
	
	});
		
		
	}
	
	function wpined_loader(m) {
		if(jQuery("#wpined_loader").length == 0) {
			jQuery("body").append('<div id="wpined_loader"> <img src="<?php echo WP_INLINE_EDIT_PATH ?>/images/loader.gif" /><img src="<?php echo WP_INLINE_EDIT_PATH ?>/images/wp-inline-edit_loader.gif" /> <p>'+m+'</p> </div>');
		}
		
		jQuery("#wpined_loader").
		css("top",jQuery(window).scrollTop()+(jQuery(window).height() / 2)-jQuery("#wpined_loader").height()-20 +"px").
		css("left",(jQuery(window).width() / 2)-(jQuery("#wpined_loader").width() / 2)+"px")
		.find("p").html(m);
		jQuery("#wpined_loader").fadeIn();
	}
	function wpined_loader_close() {
		jQuery("#wpined_loader").fadeOut();
	}
	
/*
 * jQuery autoResize (textarea auto-resizer)
 * @copyright James Padolsey http://james.padolsey.com
 * @version 1.04
 */

(function(a){a.fn.autoResize=function(j){var b=a.extend({onResize:function(){},animate:true,animateDuration:150,animateCallback:function(){},extraSpace:20,limit:1000},j);this.filter('textarea').each(function(){var c=a(this).css({resize:'none','overflow-y':'hidden'}),k=c.height(),f=(function(){var l=['height','width','lineHeight','textDecoration','letterSpacing'],h={};a.each(l,function(d,e){h[e]=c.css(e)});return c.clone().removeAttr('id').removeAttr('name').css({position:'absolute',top:0,left:-9999}).css(h).attr('tabIndex','-1').insertBefore(c)})(),i=null,g=function(){f.height(0).val(a(this).val()).scrollTop(10000);var d=Math.max(f.scrollTop(),k)+b.extraSpace,e=a(this).add(f);if(i===d){return}i=d;if(d>=b.limit){a(this).css('overflow-y','');return}b.onResize.call(this);b.animate&&c.css('display')==='block'?e.stop().animate({height:d},b.animateDuration,b.animateCallback):e.height(d)};c.unbind('.dynSiz').bind('keyup.dynSiz',g).bind('keydown.dynSiz',g).bind('change.dynSiz',g)});return this}})(jQuery);
	//-->
	</script>
	
	<style type="text/css">
	.wpined-parent {
		font-size:inherit !important;
		font-family:inherit !important;
		font:inherit !important;
		color:inherit !important;
		background-color:transparent !important;
		border:none !important;
		border-color:none !important;
		display:none;
		line-height:inherit;
		margin:0px;
		padding:0px;
		outline: none;
		background:none!important;
	}
	#wpined_loader {
		background:white;
		-webkit-border-radius: 10px;
		border-radius: 10px;
		-webkit-box-shadow:  0px 0px 10px 4px #646464;
        box-shadow:  0px 0px 10px 4px #646464;
		position:absolute;
		z-index:200;
		padding:20px;
		text-align:center;
	}
	#wpined_loader p {
	padding-top:5px;
	}
	</style>
	<?php
	endif;
}

add_filter("the_content","wp_inline_edit_the_content");

function wp_inline_edit_the_content($content) {
	if(!wp_inline_edit_parent_exists("the_content")) {
		return $content;
	} 
	if(!current_user_can('edit_published_posts')) {
		return $content;
	}
	if(is_single() OR is_page()):
	global $post;
	$return .= '
	<span class="wpined-con" rel="'.$post->ID.'">';
	$return .= $content;
	$return .= '</span>'; 
	return $return;
	
	else:
	return $content; 
	endif;
}

add_filter("the_title","wp_inline_edit_the_title");

function wp_inline_edit_the_title($title) {
	if(!wp_inline_edit_parent_exists("the_title")) {
		return $title;
	} 
	if(!current_user_can('edit_published_posts')) {
		return $title;
	}
	if(is_single() OR is_page()):
	global $post;
	$return .= '
	<span class="wpined-title" rel="'.$post->ID.'">';
	$return .= $title;
	$return .= '</span>'; 
	return $return;
	
	else:
	return $title; 
	endif;
}



function wp_inline_edit_ajax() {
		
	$postID = intval($_POST["postID"]);
	if(empty($postID)) {  return false;	}
	if(!current_user_can('edit_post', $postID) ) { //only post author can edit
		return false;
	}
	if($_POST["t"] == "title") {
	
	if($_POST["r"] == "get") {
	
	$post_details = wp_get_single_post($postID);
	$js_title = $post_details->post_title;
	$js_title = str_replace("
","",$js_title);
	$js_title = str_replace('"','\"',$js_title);
	echo '
	jQuery(".wpined-title").each(function() {
	jQuery(this).before("<textarea>");
	var inpt = jQuery(this).prev("textarea");
	var pt = jQuery(this).parent();
	inpt.val("'.$js_title.'");
	inpt.css("width",pt.width())
	inpt.css("height",jQuery(this).height())
	.css("font-size",pt.css("font-size"))
	.css("background-color","transparent")
	.css("font-family",pt.css("font-family"))
	.css("color",pt.css("color"))
	.css("border","none")
	.css("box-shadow","none");
	inpt.autoResize();
	jQuery(this).hide();
	});
	';
		
	}	
	
	if($_POST["r"] == "save") {
		
		wp_update_post( array("ID"=>$_POST["postID"],"post_title"=>$_POST["content"]) );
		$post_details = wp_get_single_post($postID);
		$post_title = apply_filters("the_title",$post_details->post_title);
		$post_title = str_replace("
","",$post_title);
	$post_title = str_replace("\n",'',$post_title);
	$post_title = str_replace('"','\"',$post_title);
		echo ' 
		jQuery(".wpined-title").each(function() {
		jQuery(this).prev("textarea").remove();
		jQuery(this).html("'.$post_title.'");
		jQuery(this).show();
		});
		';
		
		exit;
	}
		
	
	} 
	elseif($_POST["t"] == "content") {
		
		if($_POST["r"] == "get") {
	
	$post_details = wp_get_single_post($postID);
	$js_content = $post_details->post_content;
	$js_content = str_replace("
","\\n",$js_content);
	$js_content = str_replace("\n",'\\n',$js_content);
	$js_content = str_replace('"','\"',$js_content);
	echo '
	jQuery(".wpined-con").each(function() {
	jQuery(this).prev("textarea").remove();
	jQuery(this).before("<textarea>");
	var inpt = jQuery(this).prev("textarea");
	var pt = jQuery(this).parent();
	inpt.val("'.$js_content.'");
	inpt.css("width",pt.width())
	inpt.css("height",pt.height())
	.css("font-size",pt.css("font-size"))
	.css("background-color","transparent")
	.css("font-family",pt.css("font-family"))
	.css("color",pt.css("color"))
	.css("border","none")
	.css("line-height","auto")
	.css("box-shadow","none");
	inpt.autoResize({  animateDuration : 300,  extraSpace : 40});
	jQuery(this).hide();
	});
	';
		
	}
	
	if($_POST["r"] == "save") {
		
		wp_update_post( array("ID"=>$_POST["postID"],"post_content"=>$_POST["content"]) );
		$post_details = wp_get_single_post($postID);
		$post_content = apply_filters("the_content",$post_details->post_content);
		$post_content = str_replace('
',"",$post_content);
	$post_content = str_replace("\n",'',$post_content);
	$post_content = str_replace('"','\"',$post_content);
		echo ' 
		jQuery(".wpined-con").each(function() {
		jQuery(this).prev("textarea").remove();
		jQuery(this).html("'.$post_content.'");
		jQuery(this).show();
		});
		';
		
		exit;
	}		
		
		
	}
		exit;

}
add_action("wp_ajax_wp_inline_edit","wp_inline_edit_ajax");

function wp_inline_edit_parent_exists($func) {
	$array = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
	foreach($array as $a) {	$aa[] = $a["function"];	}
	if(in_array($func,$aa)) { return true; }
	return false;
}

function wp_inline_edit_register_deactivation_hook() {
	update_option("wp_inline_edit_install","");
}
register_deactivation_hook( __FILE__, 'wp_inline_edit_register_deactivation_hook' );

if(get_option("wp_inline_edit_install") == "") {
	add_action('admin_notices',create_function("",'
	echo \'<p><center> <a href="http://webgarb.com/wp-inline-edit/" target="_blank"><img src="'.WP_INLINE_EDIT_PATH.'/images/logo.png" alt="Logo" /></a><br /><h3>Thanks for installing <a href="http://webgarb.com/wp-inline-edit/" target="_blank">WP Inline Edit</a></h3><p>Have a happy editing :)</p></center></p>\';
	update_option("wp_inline_edit_install","1");
	'));   
}
  


?>