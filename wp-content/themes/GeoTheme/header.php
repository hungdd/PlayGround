<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head profile="http://gmpg.org/xfn/11">
<?php global $goe_locate; if($goe_locate){echo $goe_locate;} ?>
<title><?php if(isset($_REQUEST['ptype']) && $_REQUEST['ptype']=='post_listing'){ echo HEADER_ADD_PLACE_SEO;?>&nbsp;|&nbsp;<?php bloginfo('name'); }else?>
<?php if(isset($_REQUEST['ptype']) && $_REQUEST['ptype']=='post_event'){ echo HEADER_ADD_EVENT_SEO;?>&nbsp;|&nbsp;<?php bloginfo('name'); }else?>
<?php if(isset($_REQUEST['ptype']) && $_REQUEST['ptype']=='preview'){ echo HEADER_ADD_PREVIEW_SEO;?>&nbsp;|&nbsp;<?php bloginfo('name'); }else?>
<?php if(isset($_REQUEST['ptype']) && ($_REQUEST['ptype'] == 'register' || $_REQUEST['ptype'] == 'login')){ echo HEADER_LOGIN_REGISTRATION_SEO;?>&nbsp;|&nbsp;<?php bloginfo('name'); }else?>
<?php if(isset($_GET['ptype']) && ($_GET['ptype'] == 'return' || $_GET['ptype'] == 'payment_success')){ echo HEADER_SUCCESS_PAGE_SEO;?>&nbsp;|&nbsp;<?php bloginfo('name'); }else?>
<?php if ( is_home() ) { ?><?php  bloginfo('description'); ?>&nbsp;|&nbsp;<?php bloginfo('name'); ?><?php } ?>
<?php if ( is_search() ) { ?>Search Results&nbsp;|&nbsp;<?php bloginfo('name'); ?><?php } ?>
<?php if ( is_author() ) { ?>Author Archives&nbsp;|&nbsp;<?php bloginfo('name'); ?><?php } ?>
<?php if ( is_single() ) { ?><?php wp_title(''); ?>&nbsp;|&nbsp;<?php bloginfo('name');?><?php } ?>
<?php if ( is_page() ) { ?><?php if(wp_title('',false)){ wp_title('');}else{bloginfo('description');} ?>&nbsp;|&nbsp;<?php bloginfo('name');?><?php } ?>
<?php if ( is_archive() ) { ?>
<?php 
if(is_category())
{
single_cat_title(); 
}else
{
global $wp_query, $post;
$current_term = $wp_query->get_queried_object();	
echo $current_term->name;	if($_SESSION['location_name']){echo ' - '.$_SESSION['location_name'];}
}
?>
&nbsp;|&nbsp;<?php bloginfo('name'); ?><?php } ?>
<?php if ( is_month() ) { ?><?php the_time('F'); ?>&nbsp;|&nbsp;<?php bloginfo('name'); ?><?php } ?>
<?php if (function_exists('is_tag')) { if ( is_tag() ) { ?><?php //single_tag_title("", true); 
} } ?>
</title>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<?php if (is_home()) { ?>
<?php 
$location_meta = get_location_meta_desc();
if($location_meta){?>
<meta name="description" content="<?php echo stripslashes($location_meta); ?>" />
<?php }elseif ( get_option('ptthemes_meta_description') <> "" ) { ?>
<meta name="description" content="<?php echo stripslashes(get_option('ptthemes_meta_description')); ?>" />
<?php } ?>
<?php if ( get_option('ptthemes_meta_keywords') <> "" ) { ?>
<meta name="keywords" content="<?php echo stripslashes(get_option('ptthemes_meta_keywords')); ?>" />
<?php } ?>
<?php if ( get_option('ptthemes_meta_author') <> "" ) { ?>
<meta name="author" content="<?php echo stripslashes(get_option('ptthemes_meta_author')); ?>" />
<?php } ?>
<?php } 
############################## META DESCRIPTION & KEYWORDS FIX BY STIOFAN HEBTECH ####################################
else{
$current_term = $wp_query->get_queried_object();
?>
<meta name="description" content="<?php if (have_posts() && is_single() OR is_page()){while(have_posts()){the_post();
$out_excerpt = str_replace(array("\r\n", "\r", "\n"), "", get_the_excerpt());
//echo apply_filters('the_excerpt_rss', $out_excerpt);
echo strip_tags($out_excerpt);
}}
elseif(is_category() || is_tag()){
if(is_category()){echo "Posts related to Category: ".ucfirst(single_cat_title("", FALSE));}
elseif(is_tag()){ echo "Posts related to Tag: ".ucfirst(single_tag_title("", FALSE));}
}
elseif($current_term->taxonomy=='placecategory'){echo str_replace('%location_name%', $_SESSION['location_name'], strip_tags($current_term->description));}
elseif($current_term->taxonomy=='eventcategory'){echo str_replace('%location_name%', $_SESSION['location_name'], strip_tags($current_term->description));}
else{ ?>
<?php echo stripslashes(get_option('ptthemes_meta_description')); //DEFAULT DESCRIPTION IF NONE FOUND ?>
<?php } ?>" />
<meta name="keywords" content="<?php 
if($post->post_type=='place'){
$place_tags = wp_get_post_terms($post->ID, 'place_tags', array("fields" => "names"));
$place_cats = wp_get_post_terms($post->ID, 'placecategory', array("fields" => "names"));	
echo implode(", ", array_merge((array)$place_cats, (array)$place_tags));	
}elseif($post->post_type=='event'){
$event_tags = wp_get_post_terms($post->ID, 'event_tags', array("fields" => "names"));
$event_cats = wp_get_post_terms($post->ID, 'eventcategory', array("fields" => "names"));	
echo implode(", ", array_merge((array)$event_cats, (array)$event_tags));	
}else{
$posttags = get_the_tags();
if ($posttags) {
foreach($posttags as $tag) {
echo $tag->name . ' '; 
}
}else{
$tags = get_tags(array('orderby' => 'count', 'order' => 'DESC'));
$xt = 1;
foreach ($tags as $tag) {
if ($xt <= 20) {
echo $tag->name.", ";
}
$xt++;
}
}}
?>" />
<?php
} 
############################## END META DESCRIPTION & KEYWORDS FIX BY STIOFAN HEBTECH ################################
?>
<link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_url'); ?>" media="screen" />
<?php $stylesheet = get_option('ptthemes_alt_stylesheet');
	if($stylesheet != 'none' && $stylesheet != '' ){?>
<link href="<?php bloginfo('template_directory'); ?>/skins/<?php echo $stylesheet; ?>" rel="stylesheet" type="text/css" />
<?php } ?>
<?php if ( get_option('ptthemes_favicon') <> "" ) { ?>
<link rel="shortcut icon" type="image/png" href="<?php echo get_option('ptthemes_favicon'); ?>" />
<?php } ?>
<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php if ( get_option('ptthemes_feedburner_url') <> "" ) { echo get_option('ptthemes_feedburner_url'); } else { echo get_bloginfo_rss('rss2_url'); } ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<?php if ( get_option('ptthemes_scripts_header') <> "" ) { echo stripslashes(get_option('ptthemes_scripts_header')); } ?>
<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory'); ?>/library/css/print.css" media="print" />
<style type="text/css">
<!--
.win #categories_strip ul li a { padding: 8px 15px 7px 15px; display:block; }
-->
</style>

<?php wp_head(); ?>
<?php
if ( is_singular() && get_option( 'thread_comments' ) )
wp_enqueue_script( 'comment-reply' );
?>
<?php if ( get_option('ptthemes_customcss') ) { ?>
<link href="<?php bloginfo('template_directory'); ?>/custom.css" rel="stylesheet" type="text/css">
<?php } ?> 
<?php 
global $wpdb;
$post_type_js = get_post_type();
if($post_type_js=='place' || $post_type_js=='event' || $_REQUEST['ptype']=='preview' || $_REQUEST['ptype']=='preview_event'){?>
<script type='text/javascript' src='<?php bloginfo('template_directory'); ?>/library/js/post.custom.js'></script>
<script type="text/javascript">
jQuery.ajax({
            cache: true,
            async: true,
            dataType: "script",
            url: 'http://s7.addthis.com/js/250/addthis_widget.js#username=<?php if(get_option('ptthemes_addthis_username')){echo get_option('ptthemes_addthis_username');}else{ echo 'ra-4facd1303678e5c0';}?>'
        });
</script>
<?php } if ( get_option('ptthemes_google_button') ) { ?>
<script type="text/javascript" src="https://apis.google.com/js/plusone.js">
 { parsetags: 'explicit' }
 </script>
<?php }?>
</head>
<body <?php body_class(); ?> >
<div id="location_select_wrapper">
<div id="location_select" >
<?php if(get_option('ptthemes_enable_multicity_flag')){ get_location_menu();} ?>
</div>
</div>

<div id="page" class="<?php if ( is_home() ) {  echo "top_bg"; } else { echo "top_bg_in"; } ?>" >  
<div id="header_outer">
<div id="header" class="clearfix">
<div class="header_left">
<?php $logo_section = dynamic_sidebar('Logo Section'); if(!$logo_section){ ?>
<?php if (!function_exists('show_logo')) { 
function show_logo(){
if ( get_option('ptthemes_show_blog_title') ) { ?>
<div class="blog-title"><a href="<?php echo get_option('home'); ?>/"><?php bloginfo('name'); ?></a> 
<p class="blog-description"><?php bloginfo('description'); ?></p>
</div> 
<?php } else { ?>
<div class="logo"> 
<a href="<?php echo get_option('home'); ?>/">
<img src="<?php if ( get_option('ptthemes_logo_url') != "" ) { echo get_option('ptthemes_logo_url'); } elseif(get_option('ptthemes_alt_stylesheet')=="none") { echo get_bloginfo('template_directory').'/skins/1-default/logo.png'; } else { echo get_bloginfo('template_directory').'/skins/'.str_replace('.css','',get_option('ptthemes_alt_stylesheet')).'/logo.png'; } ?>" alt="<?php bloginfo('name'); ?>"   /></a>
<p class="blog-description"><?php bloginfo('description'); ?></p>
</div>      
<?php } }} show_logo(); }?>
</div> <!-- header left #end -->
<div class="header_right">
<?php if(wp_nav_menu( array( 'theme_location' => 'top_menu','fallback_cb' =>false,'echo' =>false))){wp_nav_menu( array( 'theme_location' => 'top_menu','fallback_cb' =>false,'echo' =>true));}
	elseif(function_exists('dynamic_sidebar') && dynamic_sidebar('Top Navigation') ){}else{  ?>
<ul>    
<li class="hometab <?php if ( is_home() && !isset($_REQUEST['page']) ) { ?> current_page_item <?php } ?>"><a href="<?php echo get_option('home'); ?>/"><?php _e('Home'); ?></a></li>
<?php wp_list_pages('title_li=&depth=0&exclude=' . get_inc_pages("pag_exclude_") .'&sort_column=menu_order');  ?>
</ul>
<?php }
global $current_user;
if($current_user->data->ID) { $display_name = $current_user->data->display_name; ?>
<ul class="user_login">
<li class="welcome"> <span><?php echo WELCOME_TEXT;?>, </span>  <a href="<?php echo get_author_posts_url($current_user->data->ID);?>" title="<?php echo $display_name;?>">  <?php echo gt_user_short($display_name);?></a></li>
<li class="userin"><a href="<?php echo site_url();?>/?ptype=login&amp;action=logout" class="signin"><?php echo LOGOUT_TEXT;?></a></li>
<?php }else{ ?>
<ul class="user_login">
<li class="welcome"><span><?php echo WELCOME_TEXT;?>, <strong><?php echo GUEST_TEXT;?></strong></span> </li>
<li class="userin"><a href="<?php echo site_url();?>/?ptype=login&amp;page1=sign_in" class="signin"><?php echo SIGN_IN_TEXT;?></a></li>
<?php }?>
</ul>
<?php 

global $ct_on, $child_dir;
if($ct_on && file_exists($child_dir.'/header_searchform.php')){include_once ($child_dir.'/header_searchform.php');}
else{include(TEMPLATEPATH."/header_searchform.php");}
?>
</div> <!-- header right #end -->
</div> <!-- header #end -->

<?php  if(wp_nav_menu( array( 'theme_location' => 'main_menu','fallback_cb' =>false,'echo' =>false))){
	echo '<div id="categories_strip">';
	wp_nav_menu( array( 'theme_location' => 'main_menu','fallback_cb' =>'nav_nav','echo' =>true, 'container' => 'div','container_id'  => 'main_nav_menu'));
	dynamic_sidebar('Header Navigation Right'); 
	echo '</div>';
}else{
	?>
    
<div id="categories_strip2"> 
<?php  
if (function_exists('dynamic_sidebar') && dynamic_sidebar('Main Navigation') ){?>
<div id="fix_loc_select"><?php dynamic_sidebar('Header Navigation Right');echo '</div></div>';}else{ echo '</div><div id="categories_strip"><div id="main_nav_menu">'; ?>
<style>#categories_strip2{display:none;}</style>
<ul>
<li class="<?php if ( is_home() ) { ?>current_page_item<?php } ?> home" ><a href="<?php echo get_option('home'); ?>/"><?php _e('Home');?></a></li>
<?php  if(is_array(get_option('ptthemes_placecategory')))
{
$cat_arr = get_option('ptthemes_placecategory');
if(is_array(get_option('ptthemes_placecategory')) && $cat_arr[0]!='')
{
$blog_cat = implode(',',get_option('ptthemes_placecategory')); 
$catlist =  wp_list_categories('title_li=&include=' . $blog_cat .'&echo=0&taxonomy=placecategory'); 
if(!strstr($catlist,'No categories'))
{
echo $catlist;
}
}
}
?>  
<?php  if(is_array(get_option('ptthemes_eventcategory')))
{
$cat_arr = get_option('ptthemes_eventcategory');
if(is_array(get_option('ptthemes_eventcategory')) && $cat_arr[0]!='')
{
$event_cat = implode(',',get_option('ptthemes_eventcategory')); 
$catlist =  wp_list_categories('title_li=&echo=0&include=' . $event_cat .'&taxonomy=eventcategory'); 
if(!strstr($catlist,'No categories'))
{
echo $catlist;
}
}
}
?>  
<?php
$catlist_blog='';
$blog_cat = get_option('ptthemes_blogcategory');
if(is_array(get_option('ptthemes_blogcategory')) && $blog_cat[0]!='')
{
$blog_cat = implode(',',get_option('ptthemes_blogcategory'));  
$catlist_blog =  wp_list_categories('title_li=&include=' . $blog_cat .'&echo=0');
}
if(!strstr($catlist_blog,'No categories'))
{
echo $catlist_blog;
}
?> 
<?php if(get_option('is_user_addevent')=='0'){}else{ ?>
<li class="<?php if ( is_home() && (isset($_REQUEST['ptype']) && $_REQUEST['ptype']=='post_listing') ) { ?>current_page_item<?php } ?>" ><a href="<?php echo site_url(); ?>/?ptype=post_listing"><?php echo ADD_LISTING;?></a></li>
<?php }?>
<?php if(get_option('is_user_eventlist')=='0'){}else{ ?>
<li class="<?php if ( is_home() && (isset($_REQUEST['ptype']) && $_REQUEST['ptype']=='post_event')) { ?>current_page_item<?php } ?>" ><a href="<?php echo site_url(); ?>/?ptype=post_event"><?php echo ADD_EVENT;?></a></li>
<?php }?>
</ul>
<?php dynamic_sidebar('Header Navigation Right');}?>
</div>
<?php }?>
</div>
</div>  <!-- header outer#end -->