<!DOCTYPE html>
<html xmlns="http<?php echo (is_ssl())? 's' : ''; ?>://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

	<title>
	<?php
	if ( defined('WPSEO_VERSION') ) {
		wp_title('');
	} else {
		bloginfo('name'); ?> <?php wp_title(' - ', true, 'left');
	}
	?>
	</title>

	<?php global $data; ?>

	<?php $theme_info = wp_get_theme();
	if ($theme_info->parent_theme) {
		$template_dir =  basename(get_template_directory());
		$theme_info = wp_get_theme($template_dir);
	}
	?>
	<style type="text/css"><?php echo $theme_info->get( 'Name' ) . "_" . $theme_info->get( 'Version' ); ?>{color:green;}</style>

	<?php $GLOBALS['backup_wp_query'] = $wp_query; ?>

	<?php if($data['google_body'] && $data['google_body'] != 'Select Font'): ?>
	<?php $gfont[urlencode($data['google_body'])] = '"' . urlencode($data['google_body']) . ':400,400italic,700,700italic:latin,greek-ext,cyrillic,latin-ext,greek,cyrillic-ext,vietnamese"'; ?>
	<?php endif; ?>

	<?php if($data['google_nav'] && $data['google_nav'] != 'Select Font' && $data['google_nav'] != $data['google_body']): ?>
	<?php $gfont[urlencode($data['google_nav'])] = '"' . urlencode($data['google_nav']) . ':400,400italic,700,700italic:latin,greek-ext,cyrillic,latin-ext,greek,cyrillic-ext,vietnamese"'; ?>
	<?php endif; ?>

	<?php if($data['google_headings'] && $data['google_headings'] != 'Select Font' && $data['google_headings'] != $data['google_body'] && $data['google_headings'] != $data['google_nav']): ?>
	<?php $gfont[urlencode($data['google_headings'])] = '"' . urlencode($data['google_headings']) . ':400,400italic,700,700italic:latin,greek-ext,cyrillic,latin-ext,greek,cyrillic-ext,vietnamese"'; ?>
	<?php endif; ?>

	<?php if($data['google_footer_headings'] && $data['google_footer_headings'] != 'Select Font' && $data['google_footer_headings'] != $data['google_body'] && $data['google_footer_headings'] != $data['google_nav'] && $data['google_footer_headings'] != $data['google_headings']): ?>
	<?php $gfont[urlencode($data['google_footer_headings'])] = '"' . urlencode($data['google_footer_headings']) . ':400,400italic,700,700italic:latin,greek-ext,cyrillic,latin-ext,greek,cyrillic-ext,vietnamese"'; ?>
	<?php endif; ?>

	<?php if($gfont): ?>
	<?php
	if(is_array($gfont) && !empty($gfont)) {
		$gfonts = implode($gfont, ', ');
	}
	?>
	<?php endif; ?>
	<script type="text/javascript">
	WebFontConfig = {
		<?php if(!empty($gfonts)): ?>google: { families: [ <?php echo $gfonts; ?> ] },<?php endif; ?>
		custom: { families: ['FontAwesome'], urls: ['<?php bloginfo('template_directory'); ?>/fonts/fontawesome.css'] }
	};
	(function() {
		var wf = document.createElement('script');
		wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
		  '://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
		wf.type = 'text/javascript';
		wf.async = 'true';
		var s = document.getElementsByTagName('script')[0];
		s.parentNode.insertBefore(wf, s);
	})();
	</script>

	<?php
	wp_deregister_style( 'style-css' );
	wp_register_style( 'style-css', get_stylesheet_uri() );
	wp_enqueue_style( 'style-css' );
	?>
	<!--[if lte IE 8]>
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/ie8.css" />
	<![endif]-->

	<!--[if IE]>
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/ie.css" />
	<![endif]-->

	<?php global $data,$woocommerce; ?>

	<?php
	if(is_page('header-2')) {
		$data['header_right_content'] = 'Social Links';
		if($data['scheme_type'] == 'Dark') {
			$data['header_top_bg_color'] = '#29292a';
			$data['header_icons_color'] = 'Light';
			$data['snav_color'] = '#ffffff';
			$data['header_top_first_border_color'] = '#3e3e3e';
		} else {
			$data['header_top_bg_color'] = '#ffffff';
			$data['header_icons_color'] = 'Dark';
			$data['snav_color'] = '#747474';
			$data['header_top_first_border_color'] = '#efefef';
		}
	} elseif(is_page('header-3')) {
		$data['header_right_content'] = 'Social Links';
	} elseif(is_page('header-4')) {
		$data['header_left_content'] = 'Social Links';
		$data['header_right_content'] = 'Navigation';
	} elseif(is_page('header-5')) {
		$data['header_right_content'] = 'Social Links';
	}
	?>

	<?php if($data['responsive']): ?>
	<?php $isiPad = (bool) strpos($_SERVER['HTTP_USER_AGENT'],'iPad');
	if(!$isiPad || !$data['ipad_potrait']): ?>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<?php endif; ?>
	<?php
	wp_deregister_style( 'media-css' );
	wp_register_style( 'media-css', get_bloginfo('template_directory').'/css/media.css', array(), null, 'all');
	wp_enqueue_style( 'media-css' );
	?>
		<?php if(!$data['ipad_potrait']): ?>
		<?php
		wp_deregister_style( 'ipad-css' );
		wp_register_style( 'ipad-css', get_bloginfo('template_directory').'/css/ipad.css', array(), null, 'all');
		wp_enqueue_style( 'ipad-css' );
		?>
		<?php if (is_tablet($_SERVER['HTTP_USER_AGENT'])): ?>
		<style type="text/css">
		.mobile-topnav-holder {
			padding-top: 10px!important;
		    padding-bottom: 10px!important;
		}
		</style>
		<?php endif; ?>
		<?php else: ?>
		<style type="text/css">
		@media only screen and (min-device-width: 768px) and (max-device-width: 1024px) and (orientation: portrait){
			#wrapper .ei-slider{width:100% !important;}
		}
		@media only screen and (min-device-width: 768px) and (max-device-width: 1024px) and (orientation: landscape){
			#wrapper .ei-slider{width:100% !important;}
		}
		</style>
		<?php endif; ?>
	<?php else: ?>
		<style type="text/css">
		@media only screen and (min-device-width : 768px) and (max-device-width : 1024px){
			#wrapper .ei-slider{width:100% !important;}
		}
		</style>
		<?php $isiPhone = (bool) strpos($_SERVER['HTTP_USER_AGENT'],'iPhone');
		if($isiPhone):
		?>
		<style type="text/css">
		@media only screen and (min-device-width : 320px) and (max-device-width : 480px){
			#wrapper .ei-slider{width:100% !important;}
		}
		</style>
		<?php endif; ?>
	<?php endif; ?>

	<?php if(!$data['use_animate_css']): ?>
	<?php if(wp_is_mobile()): ?>
	<?php if(!$data['disable_mobile_animate_css']):
	    wp_deregister_style( 'animate-css' );
	    wp_register_style( 'animate-css', get_bloginfo('template_directory').'/css/animate-custom.css', array(), null, 'all');
		wp_enqueue_style( 'animate-css' );
	?>
	<style type="text/css">
	.animated { visibility:hidden;}
	</style>
	<?php else: ?>
	<style type="text/css">
	.animated { visibility:visible;}
	</style>
	<?php endif; ?>
	<?php else:
	    wp_deregister_style( 'animate-css' );
	    wp_register_style( 'animate-css', get_bloginfo('template_directory').'/css/animate-custom.css', array(), null, 'all');
		wp_enqueue_style( 'animate-css' );
	?>
	<style type="text/css">
	.animated { visibility:hidden;}
	</style>
	<?php endif; ?>
	<?php else: ?>
	<style type="text/css">
	.animated { visibility:visible;}
	</style>
	<?php endif; ?>

	<!--[if lt IE 10]>
	<style type="text/css">
	.animated { visibility:visible;}
	</style>
	<![endif]-->

	<?php if (is_tablet($_SERVER['HTTP_USER_AGENT'])): ?>
	<style type="text/css">
	#wrapper {overflow: hidden !important;}
	</style>
	<?php endif; ?>

	<?php if (is_tablet($_SERVER['HTTP_USER_AGENT']) && !$data['header_sticky_tablet']): ?>
	<style type="text/css">
	body #header.sticky-header,body #header.sticky-header.sticky{display:none !important;}
	</style>
	<?php endif; ?>

	<?php if(wp_is_mobile()): ?>
	<style type="text/css">
	.fullwidth-box { background-attachment: scroll !important; }
	</style>
	<?php if(!$data['status_totop_mobile']): ?>
	<style type="text/css">
	#toTop {display: none !important;}
	</style>
	<?php else: ?>
	<style type="text/css">
	#toTop {bottom: 30px !important; border-radius: 4px !important; height: 28px; padding-bottom:10px !important; line-height:28px; z-index: 10000;}
	#toTop:hover {background-color: #333333 !important;}
	</style>
	<?php endif; ?>
	<?php endif; ?>

	<?php if(wp_is_mobile() && $data['mobile_slidingbar_widgets']): ?>
	<style type="text/css">
	#slidingbar-area{display:none !important;}
	</style>
	<?php endif; ?>
	<?php if(wp_is_mobile() && !$data['header_sticky_mobile'] && !is_tablet($_SERVER['HTTP_USER_AGENT'])): ?>
	<style type="text/css">
	body #header.sticky-header,body #header.sticky-header.sticky{display:none !important;}
	</style>
	<?php endif; ?>

	<?php if(wp_is_mobile() && !is_tablet($_SERVER['HTTP_USER_AGENT'])): ?>
	<style type="text/css">
	.header-v5 #header .logo { float: none !important; }
	</style>
	<?php endif; ?>

	<?php if($data['favicon']): ?>
	<link rel="shortcut icon" href="<?php echo $data['favicon']; ?>" type="image/x-icon" />
	<?php endif; ?>

	<?php if($data['iphone_icon']): ?>
	<!-- For iPhone -->
	<link rel="apple-touch-icon-precomposed" href="<?php echo $data['iphone_icon']; ?>">
	<?php endif; ?>

	<?php if($data['iphone_icon_retina']): ?>
	<!-- For iPhone 4 Retina display -->
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo $data['iphone_icon_retina']; ?>">
	<?php endif; ?>

	<?php if($data['ipad_icon']): ?>
	<!-- For iPad -->
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo $data['ipad_icon']; ?>">
	<?php endif; ?>

	<?php if($data['ipad_icon_retina']): ?>
	<!-- For iPad Retina display -->
	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo $data['ipad_icon_retina']; ?>">
	<?php endif; ?>

	<?php if($data['status_totop']): ?>
	<style type="text/css">
	#toTop {display: none !important;}
	</style>
	<?php endif; ?>

	<?php wp_head(); ?>

	<?php
	if((get_option('show_on_front') && get_option('page_for_posts') && is_home()) ||
	    (get_option('page_for_posts') && is_archive() && !is_post_type_archive()) && !(is_tax('product_cat') || is_tax('product_tag')) || (get_option('page_for_posts') && is_search())) {
		$c_pageID = get_option('page_for_posts');
	} else {
		if(isset($post)) {
			$c_pageID = $post->ID;
		}

		if(class_exists('Woocommerce')) {
			if(is_shop() || is_tax('product_cat') || is_tax('product_tag')) {
				$c_pageID = get_option('woocommerce_shop_page_id');
			}
		}
	}
	?>

	<!--[if lte IE 8]>
	<script type="text/javascript">
	jQuery(document).ready(function() {
	var imgs, i, w;
	var imgs = document.getElementsByTagName( 'img' );
	for( i = 0; i < imgs.length; i++ ) {
	    w = imgs[i].getAttribute( 'width' );
	    imgs[i].removeAttribute( 'width' );
	    imgs[i].removeAttribute( 'height' );
	}
	});
	</script>
	<![endif]-->
	<script type="text/javascript">
	/*@cc_on
	  @if (@_jscript_version == 10)
	    document.write('<style type="text/css">.search input{padding-left:5px;}header .tagline{margin-top:3px !important;}.star-rating span:before {letter-spacing: 0;}.avada-select-parent .avada-select-arrow,.gravity-select-parent .select-arrow,.wpcf7-select-parent .select-arrow,.select-arrow{background: #fff;}.star-rating{width: 5.2em!important;}.star-rating span:before {letter-spacing: 0.1em!important;}</style>');
	  @end
	@*/
	<?php
	$lang = '';
	if(defined('ICL_LANGUAGE_CODE')) {
		global $sitepress;
		if(ICL_LANGUAGE_CODE != 'en' && ICL_LANGUAGE_CODE != 'all') {
			$lang = '_'.ICL_LANGUAGE_CODE;
			if(!get_option($theme_name.'_options'.$lang)) {
				update_option($theme_name.'_options'.$lang, get_option($theme_name.'_options'));
			}
		} elseif( ICL_LANGUAGE_CODE == 'all' ) {
			$lang = '_' . $sitepress->get_default_language();
		} else {
			$lang = '';
		}
	}

	ob_start();
	include_once get_template_directory() . '/framework/dynamic_js.php';
	$dynamic_js = ob_get_contents();
	ob_get_clean();

	$upload_dir = wp_upload_dir();
	$filename = trailingslashit($upload_dir['basedir']) . 'avada' . $lang . '.js';

	global $wp_filesystem;
	if( empty( $wp_filesystem ) ) {
	    require_once( ABSPATH .'/wp-admin/includes/file.php' );
	    WP_Filesystem();
	}

	if( $wp_filesystem ) {
		$js_file_status = $wp_filesystem->get_contents( $filename );

		if( ! trim( $js_file_status ) ) { // if js file creation fails
			echo $dynamic_js;
		}
	} else { // if filesystem api fails
		echo $dynamic_js;
	}
	?>
	</script>

	<style type="text/css">
	<?php
	ob_start();
	include_once get_template_directory() . '/framework/dynamic_css.php';
	$dynamic_css = ob_get_contents();
	ob_get_clean();

	$upload_dir = wp_upload_dir();
	$filename = trailingslashit($upload_dir['basedir']) . 'avada' . $lang . '.css';

	global $wp_filesystem;
	if( empty( $wp_filesystem ) ) {
	    require_once( ABSPATH .'/wp-admin/includes/file.php' );
	    WP_Filesystem();
	}

	if( is_page('header-2') || is_page('header-3') || is_page('header-4') || is_page('header-5') ) {
		$header_demo = true;
	} else {
		$header_demo = false;
	}

	if( $wp_filesystem ) {
		$css_file_status = $wp_filesystem->get_contents( $filename );

		if( ! trim( $css_file_status ) || $header_demo == true ) { // if css file creation fails
			echo $dynamic_css;
		}
	} else { // if filesystem api fails
		echo $dynamic_css;
	}
	?>
	<?php if($data['layout'] == 'Boxed'): ?>
	body{
		<?php if(get_post_meta($c_pageID, 'pyre_page_bg_color', true)): ?>
		background-color:<?php echo get_post_meta($c_pageID, 'pyre_page_bg_color', true); ?>;
		<?php else: ?>
		background-color:<?php echo $data['bg_color']; ?>;
		<?php endif; ?>

		<?php if(get_post_meta($c_pageID, 'pyre_page_bg', true)): ?>
		background-image:url(<?php echo get_post_meta($c_pageID, 'pyre_page_bg', true); ?>);
		background-repeat:<?php echo get_post_meta($c_pageID, 'pyre_page_bg_repeat', true); ?>;
			<?php if(get_post_meta($c_pageID, 'pyre_page_bg_full', true) == 'yes'): ?>
			background-attachment:fixed;
			background-position:center center;
			-webkit-background-size: cover;
			-moz-background-size: cover;
			-o-background-size: cover;
			background-size: cover;
			<?php endif; ?>
		<?php elseif($data['bg_image']): ?>
		background-image:url(<?php echo $data['bg_image']; ?>);
		background-repeat:<?php echo $data['bg_repeat']; ?>;
			<?php if($data['bg_full']): ?>
			background-attachment:fixed;
			background-position:center center;
			-webkit-background-size: cover;
			-moz-background-size: cover;
			-o-background-size: cover;
			background-size: cover;
			<?php endif; ?>
		<?php endif; ?>

		<?php if($data['bg_pattern_option'] && $data['bg_pattern'] && !(get_post_meta($c_pageID, 'pyre_page_bg_color', true) || get_post_meta($c_pageID, 'pyre_page_bg', true))): ?>
		background-image:url("<?php echo get_bloginfo('template_directory') . '/images/patterns/' . $data['bg_pattern'] . '.png'; ?>");
		background-repeat:repeat;
		<?php endif; ?>
	}
	#wrapper{
		background:#fff;
		width:1000px;
		margin:0 auto;
	}
	.wrapper_blank { display: block; }
	@media only screen and (min-width: 801px) and (max-width: 1014px){
		#wrapper{
			width:auto;
		}
	}
	@media only screen and (min-device-width: 801px) and (max-device-width: 1014px){
		#wrapper{
			width:auto;
		}
	}
	<?php endif; ?>

	<?php if($data['layout'] == 'Wide'): ?>
	#wrapper{
		width:100%;
	}
	//.wrapper_blank { display: block; }
	@media only screen and (min-width: 801px) and (max-width: 1014px){
		#wrapper{
			width:auto;
		}
	}
	@media only screen and (min-device-width: 801px) and (max-device-width: 1014px){
		#wrapper{
			width:auto;
		}
	}
	<?php endif; ?>

	<?php if(get_post_meta($c_pageID, 'pyre_page_bg_layout', true) == 'boxed'): ?>
	body{
		<?php if(get_post_meta($c_pageID, 'pyre_page_bg_color', true)): ?>
		background-color:<?php echo get_post_meta($c_pageID, 'pyre_page_bg_color', true); ?>;
		<?php else: ?>
		background-color:<?php echo $data['bg_color']; ?>;
		<?php endif; ?>

		<?php if(get_post_meta($c_pageID, 'pyre_page_bg', true)): ?>
		background-image:url(<?php echo get_post_meta($c_pageID, 'pyre_page_bg', true); ?>);
		background-repeat:<?php echo get_post_meta($c_pageID, 'pyre_page_bg_repeat', true); ?>;
			<?php if(get_post_meta($c_pageID, 'pyre_page_bg_full', true) == 'yes'): ?>
			background-attachment:fixed;
			background-position:center center;
			-webkit-background-size: cover;
			-moz-background-size: cover;
			-o-background-size: cover;
			background-size: cover;
			<?php endif; ?>
		<?php elseif($data['bg_image']): ?>
		background-image:url(<?php echo $data['bg_image']; ?>);
		background-repeat:<?php echo $data['bg_repeat']; ?>;
			<?php if($data['bg_full']): ?>
			background-attachment:fixed;
			background-position:center center;
			-webkit-background-size: cover;
			-moz-background-size: cover;
			-o-background-size: cover;
			background-size: cover;
			<?php endif; ?>
		<?php endif; ?>

		<?php if($data['bg_pattern_option'] && $data['bg_pattern'] && !(get_post_meta($c_pageID, 'pyre_page_bg_color', true) || get_post_meta($c_pageID, 'pyre_page_bg', true))): ?>
		background-image:url("<?php echo get_bloginfo('template_directory') . '/images/patterns/' . $data['bg_pattern'] . '.png'; ?>");
		background-repeat:repeat;
		<?php endif; ?>
	}
	#wrapper{
		background:#fff;
		width:1000px;
		margin:0 auto;
	}
	.wrapper_blank { display: block; }
	@media only screen and (min-width: 801px) and (max-width: 1014px){
		#wrapper{
			width:auto;
		}
	}
	@media only screen and (min-device-width: 801px) and (max-device-width: 1014px){
		#wrapper{
			width:auto;
		}
	}
	<?php endif; ?>

	<?php if(get_post_meta($c_pageID, 'pyre_page_bg_layout', true) == 'wide'): ?>
	#wrapper{
		width:100%;
	}
	//.wrapper_blank { display: block; }
	@media only screen and (min-width: 801px) and (max-width: 1014px){
		#wrapper{
			width:auto;
		}
	}
	@media only screen and (min-device-width: 801px) and (max-device-width: 1014px){
		#wrapper{
			width:auto;
		}
	}
	<?php endif; ?>

	<?php if(get_post_meta($c_pageID, 'pyre_page_title_custom_subheader', true) != ''): ?>
	.page-title ul {line-height: 40px;}
	<?php endif; ?>

	<?php if(get_post_meta($c_pageID, 'pyre_page_title_bar_bg', true)): ?>
	.page-title-container{
		background-image:url(<?php echo get_post_meta($c_pageID, 'pyre_page_title_bar_bg', true); ?>) !important;
	}
	<?php elseif($data['page_title_bg']): ?>
	.page-title-container{
		background-image:url(<?php echo $data['page_title_bg']; ?>) !important;
	}
	<?php endif; ?>

	<?php if(get_post_meta($c_pageID, 'pyre_page_title_bar_bg_color', true)): ?>
	.page-title-container{
		background-color:<?php echo get_post_meta($c_pageID, 'pyre_page_title_bar_bg_color', true); ?>;
	}
	<?php elseif($data['page_title_bg_color']): ?>
	.page-title-container{
		background-color:<?php echo $data['page_title_bg_color']; ?>;
	}
	<?php endif; ?>

	#header{
		<?php if($data['header_bg_image']): ?>
		background-image:url(<?php echo $data['header_bg_image']; ?>);
		<?php if($data['header_bg_repeat'] == 'repeat-y' || $data['header_bg_repeat'] == 'no-repeat'): ?>
		background-position: center center;
		<?php endif; ?>
		background-repeat:<?php echo $data['header_bg_repeat']; ?>;
			<?php if($data['header_bg_full']): ?>
			background-attachment:scroll;
			background-position:center center;
			-webkit-background-size: cover;
			-moz-background-size: cover;
			-o-background-size: cover;
			background-size: cover;
			<?php endif; ?>

		<?php if($data['header_bg_parallax']): ?>
		background-attachment: fixed;
		background-position:top center;
		<?php endif; ?>
		<?php endif; ?>
	}

	#header{
		<?php if(get_post_meta($c_pageID, 'pyre_header_bg_color', true)): ?>
		background-color:<?php echo get_post_meta($c_pageID, 'pyre_header_bg_color', true); ?> !important;
		<?php endif; ?>
		<?php if(get_post_meta($c_pageID, 'pyre_header_bg', true)): ?>
		background-image:url(<?php echo get_post_meta($c_pageID, 'pyre_header_bg', true); ?>);
		<?php if(get_post_meta($c_pageID, 'pyre_header_bg_repeat', true) == 'repeat-y' || get_post_meta($c_pageID, 'pyre_header_bg_repeat', true) == 'no-repeat'): ?>
		background-position: center center;
		<?php endif; ?>
		background-repeat:<?php echo get_post_meta($c_pageID, 'pyre_header_bg_repeat', true); ?>;
			<?php if(get_post_meta($c_pageID, 'pyre_header_bg_full', true) == 'yes'): ?>
			background-attachment:fixed;
			background-position:center center;
			-webkit-background-size: cover;
			-moz-background-size: cover;
			-o-background-size: cover;
			background-size: cover;
			<?php endif; ?>
		<?php endif; ?>
	}

	#main{
		<?php if($data['content_bg_image'] && !get_post_meta($c_pageID, 'pyre_wide_page_bg_color', true)): ?>
		background-image:url(<?php echo $data['content_bg_image']; ?>);
		background-repeat:<?php echo $data['content_bg_repeat']; ?>;
			<?php if($data['content_bg_full']): ?>
			background-attachment:fixed;
			background-position:center center;
			-webkit-background-size: cover;
			-moz-background-size: cover;
			-o-background-size: cover;
			background-size: cover;
			<?php endif; ?>
		<?php endif; ?>

		<?php if($data['main_top_padding'] && !get_post_meta($c_pageID, 'pyre_main_top_padding', true)): ?>
		padding-top: <?php echo $data['main_top_padding']; ?> !important;
		<?php endif; ?>

		<?php if($data['main_bottom_padding'] && !get_post_meta($c_pageID, 'pyre_main_bottom_padding', true)): ?>
		padding-bottom: <?php echo $data['main_bottom_padding']; ?> !important;
		<?php endif; ?>
	}

	#main{
		<?php if(get_post_meta($c_pageID, 'pyre_wide_page_bg_color', true)): ?>
		background-color:<?php echo get_post_meta($c_pageID, 'pyre_wide_page_bg_color', true); ?> !important;
		<?php endif; ?>
		<?php if(get_post_meta($c_pageID, 'pyre_wide_page_bg', true)): ?>
		background-image:url(<?php echo get_post_meta($c_pageID, 'pyre_wide_page_bg', true); ?>);
		background-repeat:<?php echo get_post_meta($c_pageID, 'pyre_wide_page_bg_repeat', true); ?>;
			<?php if(get_post_meta($c_pageID, 'pyre_wide_page_bg_full', true) == 'yes'): ?>
			background-attachment:fixed;
			background-position:center center;
			-webkit-background-size: cover;
			-moz-background-size: cover;
			-o-background-size: cover;
			background-size: cover;
			<?php endif; ?>
		<?php endif; ?>

		<?php if(get_post_meta($c_pageID, 'pyre_main_top_padding', true)): ?>
		padding-top:<?php echo get_post_meta($c_pageID, 'pyre_main_top_padding', true); ?> !important;
		<?php endif; ?>

		<?php if(get_post_meta($c_pageID, 'pyre_main_top_padding', true)): ?>
		padding-bottom:<?php echo get_post_meta($c_pageID, 'pyre_main_top_padding', true); ?> !important;
		<?php endif; ?>

	}

	.page-title-container{
		<?php if($data['page_title_bg_full']): ?>
		-webkit-background-size: cover;
		-moz-background-size: cover;
		-o-background-size: cover;
		background-size: cover;
		<?php endif; ?>

		<?php if(get_post_meta($c_pageID, 'pyre_page_title_bar_bg_full', true) == 'yes'): ?>
		-webkit-background-size: cover;
		-moz-background-size: cover;
		-o-background-size: cover;
		background-size: cover;
		<?php elseif(get_post_meta($c_pageID, 'pyre_page_title_bar_bg_full', true) == 'no'): ?>
		-webkit-background-size: auto;
		-moz-background-size: auto;
		-o-background-size: auto;
		background-size: auto;
		<?php endif; ?>

		<?php if($data['page_title_bg_parallax']): ?>
		background-attachment: fixed;
		background-position:top center;
		<?php endif; ?>

		<?php if(get_post_meta($c_pageID, 'pyre_page_title_bg_parallax', true) == 'yes'): ?>
		background-attachment: fixed;
		background-position:top center;
		<?php elseif(get_post_meta($c_pageID, 'pyre_page_title_bg_parallax', true) == 'no'): ?>
		background-attachment: scroll;
		<?php endif; ?>

	}

	<?php if(get_post_meta($c_pageID, 'pyre_page_title_height', true)): ?>
	.page-title-container{
		height:<?php echo get_post_meta($c_pageID, 'pyre_page_title_height', true); ?> !important;
	}
	<?php elseif($data['page_title_height']): ?>
	.page-title-container{
		height:<?php echo $data['page_title_height']; ?> !important;
	}
	<?php endif; ?>

	<?php if(is_single() && get_post_meta($c_pageID, 'pyre_fimg_width', true)): ?>
	<?php if(get_post_meta($c_pageID, 'pyre_fimg_width', true) != 'auto'): ?>
	#post-<?php echo $c_pageID; ?> .post-slideshow {max-width:<?php echo get_post_meta($c_pageID, 'pyre_fimg_width', true); ?> !important;}
	<?php else: ?>
	.post-slideshow .flex-control-nav{position:relative;text-align:left;margin-top:10px;}
	<?php endif; ?>
	#post-<?php echo $c_pageID; ?> .post-slideshow img{max-width:<?php echo get_post_meta($c_pageID, 'pyre_fimg_width', true); ?> !important;}
		<?php if(get_post_meta($c_pageID, 'pyre_fimg_width', true) == 'auto'): ?>
		#post-<?php echo $c_pageID; ?> .post-slideshow img{width:<?php echo get_post_meta($c_pageID, 'pyre_fimg_width', true); ?> !important;}
		<?php endif; ?>
	<?php endif; ?>

	<?php if(is_single() && get_post_meta($c_pageID, 'pyre_fimg_height', true)): ?>
	#post-<?php echo $c_pageID; ?> .post-slideshow, #post-<?php echo $c_pageID; ?> .post-slideshow img{max-height:<?php echo get_post_meta($c_pageID, 'pyre_fimg_height', true); ?> !important;}
	#post-<?php echo $c_pageID; ?> .post-slideshow .slides { max-height: 100%; }
	<?php endif; ?>

	<?php if(get_post_meta($c_pageID, 'pyre_page_title_bar_bg_retina', true)): ?>
	@media only screen and (-webkit-min-device-pixel-ratio: 2), only screen and (min-device-pixel-ratio: 2) {
		.page-title-container {
			background-image: url(<?php echo get_post_meta($c_pageID, 'pyre_page_title_bar_bg_retina', true); ?>) !important;
			-webkit-background-size:cover;
			   -moz-background-size:cover;
			     -o-background-size:cover;
			        background-size:cover;
		}
	}
	<?php elseif($data['page_title_bg_retina']): ?>
	@media only screen and (-webkit-min-device-pixel-ratio: 2), only screen and (min-device-pixel-ratio: 2) {
		.page-title-container {
			background-image: url(<?php echo $data['page_title_bg_retina']; ?>) !important;
			-webkit-background-size:cover;
			   -moz-background-size:cover;
			     -o-background-size:cover;
			        background-size:cover;
		}
	}
	<?php endif; ?>

	<?php if(get_post_meta($c_pageID, 'pyre_hundredp_padding', true)): ?>
	.width-100 .fullwidth-box {
		margin-left: -<?php echo get_post_meta($c_pageID, 'pyre_hundredp_padding', true); ?>; margin-right: -<?php echo get_post_meta($c_pageID, 'pyre_hundredp_padding', true); ?>;
	}
	<?php elseif($data['hundredp_padding']): ?>
	.width-100 .fullwidth-box {
		margin-left: -<?php echo $data['hundredp_padding'] ?>; margin-right: -<?php echo $data['hundredp_padding'] ?>;
	}
	<?php endif; ?>

	<?php if((float) $wp_version < 3.8): ?>
	#wpadminbar *{color:#ccc !important;}
	#wpadminbar .hover a, #wpadminbar .hover a span{color:#464646 !important;}
	<?php endif; ?>
	<?php echo $data['custom_css']; ?>
	</style>

	<?php echo $data['google_analytics']; ?>

	<?php echo $data['space_head']; ?>

	<!--[if lte IE 8]>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/respond.js"></script>
	<![endif]-->
</head>
<?php
	$body_class = '';
	$wrapper_class = '';
	if(is_page_template('blank.php')):
	$body_class = 'body_blank';
	$wrapper_class = ' class="wrapper_blank"';
endif; ?>

<body <?php body_class(array($avada_color_scheme,$body_class)); ?>>
	<div id="wrapper" <?php echo $wrapper_class; ?>>
	<?php if($data['slidingbar_widgets'] && !is_page_template('blank.php')): ?>
	<?php get_template_part('slidingbar'); ?>
	<?php endif; ?>
		<?php if(!is_page_template('blank.php')): ?>
		<div class="header-wrapper">
			<?php
			if($data['header_layout']) {
				if(is_page('header-2')) {
					get_template_part('framework/headers/header-v2');
				} elseif(is_page('header-3')) {
					get_template_part('framework/headers/header-v3');
				} elseif(is_page('header-4')) {
					get_template_part('framework/headers/header-v4');
				} elseif(is_page('header-5')) {
					get_template_part('framework/headers/header-v5');
				} else {
					get_template_part('framework/headers/header-'.$data['header_layout']);
				}
			} else {
				if(is_page('header-2')) {
					get_template_part('framework/headers/header-v2');
				} elseif(is_page('header-3')) {
					get_template_part('framework/headers/header-v3');
				} elseif(is_page('header-4')) {
					get_template_part('framework/headers/header-v4');
				} elseif(is_page('header-5')) {
					get_template_part('framework/headers/header-v5');
				} else {
					get_template_part('framework/headers/header-'.$data['header_layout']);
				}
			}
			?>
		</div>
		<?php
		// sticky header
		get_template_part('framework/headers/sticky-header');
		?>
	<?php endif; ?>
	<div id="sliders-container">
	<?php
	if(is_search()) {
		$slider_page_id = '';
	}
	?>
	<?php if(!is_search()): ?>
	<?php wp_reset_query(); ?>
	<?php
	// Layer Slider
	$slider_page_id = '';
	if(!is_home() && !is_front_page() && !is_archive() && isset($post)) {
		$slider_page_id = $post->ID;
	}
	if(!is_home() && is_front_page() && isset($post)) {
		$slider_page_id = $post->ID;
	}
	if(is_home() && !is_front_page()){
		$slider_page_id = get_option('page_for_posts');
	}
	if(class_exists('Woocommerce')) {
		if(is_shop()) {
			$slider_page_id = get_option('woocommerce_shop_page_id');
		}
	}
	if(get_post_meta($slider_page_id, 'pyre_slider_type', true) == 'layer' && (get_post_meta($slider_page_id, 'pyre_slider', true) || get_post_meta($slider_page_id, 'pyre_slider', true) != 0)): ?>
	<?php
	// Get slider
	$ls_table_name = $wpdb->prefix . "layerslider";
	$ls_id = get_post_meta($slider_page_id, 'pyre_slider', true);
	$ls_slider = $wpdb->get_row("SELECT * FROM $ls_table_name WHERE id = ".(int)$ls_id." ORDER BY date_c DESC LIMIT 1" , ARRAY_A);
	$ls_slider = json_decode($ls_slider['data'], true);
	?>
	<style type="text/css">
	#layerslider-container{max-width:<?php echo $ls_slider['properties']['width'] ?>;}
	</style>
	<div id="layerslider-container">
		<div id="layerslider-wrapper">
		<?php if($ls_slider['properties']['skin'] == 'avada'): ?>
		<div class="ls-shadow-top"></div>
		<?php endif; ?>
		<?php echo do_shortcode('[layerslider id="'.get_post_meta($slider_page_id, 'pyre_slider', true).'"]'); ?>
		<?php if($ls_slider['properties']['skin'] == 'avada'): ?>
		<div class="ls-shadow-bottom"></div>
		<?php endif; ?>
		</div>
	</div>
	<?php endif; ?>
	<?php
	// Flex Slider
	if(get_post_meta($slider_page_id, 'pyre_slider_type', true) == 'flex' && (get_post_meta($slider_page_id, 'pyre_wooslider', true) || get_post_meta($slider_page_id, 'pyre_wooslider', true) != 0)) {
		echo do_shortcode('[wooslider slide_page="'.get_post_meta($slider_page_id, 'pyre_wooslider', true).'" slider_type="slides" limit="'.$data['flexslider_number'].'"]');
	}
	?>
	<?php
	if(get_post_meta($slider_page_id, 'pyre_slider_type', true) == 'rev' && get_post_meta($slider_page_id, 'pyre_revslider', true) && !$data['status_revslider'] && function_exists('putRevSlider')) {
		putRevSlider(get_post_meta($slider_page_id, 'pyre_revslider', true));
	}
	?>
	<?php
	if(get_post_meta($slider_page_id, 'pyre_slider_type', true) == 'flex2' && get_post_meta($slider_page_id, 'pyre_flexslider', true)) {
		include_once(get_template_directory().'/flexslider.php');
	}
	?>
	<?php
	// ThemeFusion Elastic Slider
	if(!$data['status_eslider']) {
		if(get_post_meta($slider_page_id, 'pyre_slider_type', true) == 'elastic' && (get_post_meta($slider_page_id, 'pyre_elasticslider', true) || get_post_meta($slider_page_id, 'pyre_elasticslider', true) != 0)) {
			include_once(get_template_directory().'/elastic-slider.php');
		}
	}
	?>
	<?php endif; ?>
	</div>
	<?php if(get_post_meta($slider_page_id, 'pyre_fallback', true)): ?>
	<style type="text/css">
	@media only screen and (max-width: 940px){
		#sliders-container{display:none;}
		#fallback-slide{display:block;}
	}
	@media only screen and (min-device-width: 768px) and (max-device-width: 1024px) and (orientation: portrait){
		#sliders-container{display:none;}
		#fallback-slide{display:block;}
	}
	</style>
	<div id="fallback-slide">
		<img src="<?php echo get_post_meta($slider_page_id, 'pyre_fallback', true); ?>" alt="" />
	</div>
	<?php endif; ?>
	<?php wp_reset_query(); ?>
	<?php if($data['page_title_bar']): ?>
	<?php if(((is_page() || is_single() || is_singular('avada_portfolio')) && get_post_meta($c_pageID, 'pyre_page_title', true) == 'yes') && !is_woocommerce() && !is_bbpress()) : ?>
	<div class="page-title-container">
		<div class="page-title">
			<div class="page-title-wrapper">
				<div class="page-title-captions">
					<?php if(get_post_meta($c_pageID, 'pyre_page_title_text', true) != 'no'): ?>
					<h1 class="entry-title">
					<?php if(get_post_meta($c_pageID, 'pyre_page_title_custom_text', true) != ''): ?>
					<?php echo get_post_meta($c_pageID, 'pyre_page_title_custom_text', true); ?>
					<?php else: ?>
					<?php the_title(); ?>
					<?php endif; ?>
					</h1>
					<?php if(get_post_meta($c_pageID, 'pyre_page_title_custom_subheader', true) != ''): ?>
					<h3>
					<?php echo get_post_meta($c_pageID, 'pyre_page_title_custom_subheader', true); ?>
					</h3>
					<?php endif; ?>
					<?php endif; ?>
				</div>
					<?php if($data['breadcrumb']): ?>
					<?php if($data['page_title_bar_bs'] == 'Breadcrumbs'): ?>
					<?php themefusion_breadcrumb(); ?>
					<?php else: ?>
					<?php get_search_form(); ?>
					<?php endif; ?>
					<?php endif; ?>
			</div>
		</div>
	</div>
	<?php endif; ?>
	<?php if(is_home() && !is_front_page() && get_post_meta($slider_page_id, 'pyre_page_title', true) == 'yes'): ?>
	<div class="page-title-container">
		<div class="page-title">
			<div class="page-title-wrapper">
			<div class="page-title-captions">
			<?php if(get_post_meta($c_pageID, 'pyre_page_title_text', true) != 'no'): ?>
			<h1 class="entry-title"><?php echo $data['blog_title']; ?></h1>
			<?php endif; ?>
			</div>
			<?php if($data['breadcrumb']): ?>
			<?php if($data['page_title_bar_bs'] == 'Breadcrumbs'): ?>
			<?php themefusion_breadcrumb(); ?>
			<?php else: ?>
			<?php get_search_form(); ?>
			<?php endif; ?>
			<?php endif; ?>
			</div>
		</div>
	</div>
	<?php endif; ?>
	<?php if(is_search()): ?>
	<div class="page-title-container">
		<div class="page-title">
			<div class="page-title-wrapper">
			<div class="page-title-captions">
			<h1 class="entry-title"><?php echo __('Search results for:', 'Avada'); ?> <?php echo get_search_query(); ?></h1>
			</div>
			<?php if($data['breadcrumb']): ?>
			<?php if($data['page_title_bar_bs'] == 'Breadcrumbs'): ?>
			<?php themefusion_breadcrumb(); ?>
			<?php else: ?>
			<?php get_search_form(); ?>
			<?php endif; ?>
			<?php endif; ?>
			</div>
		</div>
	</div>
	<?php endif; ?>
	<?php if(is_404()): ?>
	<div class="page-title-container">
		<div class="page-title">
			<div class="page-title-wrapper">
			<div class="page-title-captions">
			<h1 class="entry-title"><?php echo __('Error 404 Page', 'Avada'); ?></h1>
			</div>
			</div>
		</div>
	</div>
	<?php endif; ?>
	<?php if(is_archive() && !is_woocommerce() && !is_bbpress()): ?>
	<div class="page-title-container">
		<div class="page-title">
			<div class="page-title-wrapper">
			<div class="page-title-captions">
			<h1 class="entry-title">
				<?php if ( is_day() ) : ?>
					<?php printf( __( 'Daily Archives: %s', 'Avada' ), '<span>' . get_the_date() . '</span>' ); ?>
				<?php elseif ( is_month() ) : ?>
					<?php printf( __( 'Monthly Archives: %s', 'Avada' ), '<span>' . get_the_date( _x( 'F Y', 'monthly archives date format', 'Avada' ) ) . '</span>' ); ?>
				<?php elseif ( is_year() ) : ?>
					<?php printf( __( 'Yearly Archives: %s', 'Avada' ), '<span>' . get_the_date( _x( 'Y', 'yearly archives date format', 'Avada' ) ) . '</span>' ); ?>
				<?php elseif ( is_author() ) : ?>
					<?php
					$curauth = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author));
					?>
					<?php echo $curauth->nickname; ?>
				<?php else : ?>
					<?php single_cat_title(); ?>
				<?php endif; ?>
			</h1>
			</div>
			<?php if($data['breadcrumb']): ?>
			<?php if($data['page_title_bar_bs'] == 'Breadcrumbs'): ?>
			<?php themefusion_breadcrumb(); ?>
			<?php else: ?>
			<?php get_search_form(); ?>
			<?php endif; ?>
			<?php endif; ?>
			</div>
		</div>
	</div>
	<?php endif; ?>
	<?php
	if(class_exists('Woocommerce')):
	if($woocommerce->version && is_woocommerce() && ((is_product() && get_post_meta($c_pageID, 'pyre_page_title', true) == 'yes') || (is_shop() && get_post_meta($c_pageID, 'pyre_page_title', true) == 'yes')) && !is_search()):
	?>
	<div class="page-title-container">
		<div class="page-title">
			<div class="page-title-wrapper">
			<div class="page-title-captions">
			<?php if(get_post_meta($c_pageID, 'pyre_page_title_text', true) != 'no'): ?>
			<h1 class="entry-title">
				<?php
				if(is_product()) {
					if(get_post_meta($c_pageID, 'pyre_page_title_custom_text', true) != '') {
						echo get_post_meta($c_pageID, 'pyre_page_title_custom_text', true);
					} else {
						the_title();
					} ?>
					</h1>
					<?php if(get_post_meta($c_pageID, 'pyre_page_title_custom_subheader', true) != ''): ?>
					<h3>
					<?php echo get_post_meta($c_pageID, 'pyre_page_title_custom_subheader', true); ?>
					</h3>
					<?php endif;
				} else {
					woocommerce_page_title();
				}
				?>
			</h1>
			<?php endif; ?>
			</div>
			<?php if($data['breadcrumb']): ?>
			<?php if($data['page_title_bar_bs'] == 'Breadcrumbs'): ?>
			<?php woocommerce_breadcrumb(array(
				'wrap_before' => '<ul class="breadcrumbs">',
				'wrap_after' => '</ul>',
				'before' => '<li>',
				'after' => '</li>',
				'delimiter' => ''
			)); ?>
			<?php else: ?>
			<?php get_search_form(); ?>
			<?php endif; ?>
			<?php endif; ?>
			</div>
		</div>
	</div>
	<?php endif; ?>
	<?php if(is_tax('product_cat') || is_tax('product_tag')): ?>
	<div class="page-title-container">
		<div class="page-title">
			<div class="page-title-wrapper">
			<div class="page-title-captions">
			<h1 class="entry-title">
				<?php if ( is_day() ) : ?>
					<?php printf( __( 'Daily Archives: %s', 'Avada' ), '<span>' . get_the_date() . '</span>' ); ?>
				<?php elseif ( is_month() ) : ?>
					<?php printf( __( 'Monthly Archives: %s', 'Avada' ), '<span>' . get_the_date( _x( 'F Y', 'monthly archives date format', 'Avada' ) ) . '</span>' ); ?>
				<?php elseif ( is_year() ) : ?>
					<?php printf( __( 'Yearly Archives: %s', 'Avada' ), '<span>' . get_the_date( _x( 'Y', 'yearly archives date format', 'Avada' ) ) . '</span>' ); ?>
				<?php elseif ( is_author() ) : ?>
					<?php
					$curauth = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author));
					?>
					<?php echo $curauth->nickname; ?>
				<?php else : ?>
					<?php single_cat_title(); ?>
				<?php endif; ?>
			</h1>
			</div>
			<?php if($data['breadcrumb']): ?>
			<?php if($data['page_title_bar_bs'] == 'Breadcrumbs'): ?>
			<?php themefusion_breadcrumb(); ?>
			<?php else: ?>
			<?php get_search_form(); ?>
			<?php endif; ?>
			<?php endif; ?>
			</div>
		</div>
	</div>
	<?php endif; ?>
	<?php endif; // end class check if for woocommerce ?>
	<?php
	if( class_exists('bbPress')):
	if(is_bbpress()): ?>
	<div class="page-title-container">
		<div class="page-title">
			<div class="page-title-wrapper">
			<div class="page-title-captions">
			<?php if(get_post_meta($c_pageID, 'pyre_page_title_text', true) != 'no'): ?>
			<h1 class="entry-title"><?php the_title(); ?></h1>
			<?php endif; ?>
			</div>
			<?php if($data['breadcrumb']): ?>
			<?php if($data['page_title_bar_bs'] == 'Breadcrumbs'): ?>
			<?php bbp_breadcrumb( array ( 'before' => '<ul class="breadcrumbs">', 'after' => '</ul>', 'sep' => ' ', 'crumb_before' => '<li>', 'crumb_after' => '</li>', 'home_text' => __('Home', 'Avada')) ); ?>
			<?php else: ?>
			<?php get_search_form(); ?>
			<?php endif; ?>
			<?php endif; ?>
			</div>
		</div>
	</div>
	<?php endif; ?>
	<?php endif; ?>
	<?php endif; ?>
	<?php if(is_page_template('contact.php') && $data['recaptcha_public'] && $data['recaptcha_private']): ?>
	<script type="text/javascript">
	 var RecaptchaOptions = {
	    theme : '<?php echo $data['recaptcha_color_scheme']; ?>'
	 };
 	</script>
 	<?php endif; ?>
	<?php if(is_page_template('contact.php') && $data['gmap_address'] && !$data['status_gmap']): ?>
	<style type="text/css">
	#gmap{
		width:<?php echo $data['gmap_width']; ?>;
		margin:0 auto;
		<?php if($data['gmap_width'] != '100%'): ?>
		margin-top:55px;
		<?php endif; ?>

		<?php if($data['gmap_height']): ?>
		height:<?php echo $data['gmap_height']; ?>;
		<?php else: ?>
		height:415px;
		<?php endif; ?>
	}
	</style>
	<?php
	$data['gmap_address'] = addslashes($data['gmap_address']);
	$addresses = explode('|', $data['gmap_address']);
	$markers = '';
	if($data['map_popup']) {
		$map_popup = "false";
	} else {
		$map_popup = "true";
	}
	foreach($addresses as $address_string) {
		$markers .= "{
			address: '{$address_string}',
			html: {
				content: '{$address_string}',
				popup: {$map_popup}
			}
		},";
	}
	?>
	<script type='text/javascript'>
	jQuery(document).ready(function($) {
		jQuery('#gmap').goMap({
			address: '<?php echo $addresses[0]; ?>',
			maptype: '<?php echo $data['gmap_type']; ?>',
			zoom: <?php echo $data['map_zoom_level']; ?>,
			scrollwheel: <?php if($data['map_scrollwheel']): ?>false<?php else: ?>true<?php endif; ?>,
			scaleControl: <?php if($data['map_scale']): ?>false<?php else: ?>true<?php endif; ?>,
			navigationControl: <?php if($data['map_zoomcontrol']): ?>false<?php else: ?>true<?php endif; ?>,
	        <?php if(!$data['map_pin']): ?>markers: [<?php echo $markers; ?>],<?php endif; ?>
		});
	});
	</script>
	<div class="gmap" id="gmap">
	</div>
	<?php endif; ?>
	<?php if(is_page_template('contact-2.php') && $data['gmap_address'] && !$data['status_gmap']): ?>
	<style type="text/css">
	#gmap{
		width:940px;
		margin:0 auto;
		margin-top:55px;

		height:415px;
	}
	</style>
	<?php
	$data['gmap_address'] = addslashes($data['gmap_address']);
	$addresses = explode('|', $data['gmap_address']);
	$markers = '';
	if($data['map_popup']) {
		$map_popup = "false";
	} else {
		$map_popup = "true";
	}
	foreach($addresses as $address_string) {
		if(!$data['map_pin']) {
			$markers .= "{
				address: '{$address_string}',
				html: {
					content: '{$address_string}',
					popup: {$map_popup}
				}
			},";
		} else {
			$markers .= "{
				address: '{$address_string}'
			},";
		}
	}
	?>
	<script type='text/javascript'>
	jQuery(document).ready(function($) {
		jQuery('#gmap').goMap({
			address: '<?php echo $addresses[0]; ?>',
			maptype: '<?php echo $data['gmap_type']; ?>',
			zoom: <?php echo $data['map_zoom_level']; ?>,
			scrollwheel: <?php if($data['map_scrollwheel']): ?>false<?php else: ?>true<?php endif; ?>,
			scaleControl: <?php if($data['map_scale']): ?>false<?php else: ?>true<?php endif; ?>,
			navigationControl: <?php if($data['map_zoomcontrol']): ?>false<?php else: ?>true<?php endif; ?>,
			<?php if(!$data['map_pin']): ?>markers: [<?php echo $markers; ?>],<?php endif; ?>
		});
	});
	</script>
	<div class="gmap" id="gmap">
	</div>
	<?php endif; ?>
	<?php
	$main_css = '';
	$row_css = '';
	$main_class = '';

	if (is_woocommerce()) {
		$custom_fields = get_post_custom_values('_wp_page_template', $c_pageID);
		if(is_array($custom_fields) && !empty($custom_fields)) {
			$page_template = $custom_fields[0];
		} else {
			$page_template = '';
		}
	}

	if(is_page_template('100-width.php') || is_page_template('blank.php') ||get_post_meta($slider_page_id, 'pyre_portfolio_width_100', true) == 'yes' || $page_template == '100-width.php') {
		$main_css = 'padding-left:0px;padding-right:0px;';
		if($data['hundredp_padding'] && !get_post_meta($c_pageID, 'pyre_hundredp_padding', true)) {
			$main_css = 'padding-left:'.$data['hundredp_padding'].';padding-right:'.$data['hundredp_padding'];
		}
		if(get_post_meta($c_pageID, 'pyre_hundredp_padding', true)) {
			$main_css = 'padding-left:'.get_post_meta($c_pageID, 'pyre_hundredp_padding', true).';padding-right:'.get_post_meta($c_pageID, 'pyre_hundredp_padding', true);
		}
		$row_css = 'max-width:100%;';
		$main_class = 'width-100';
	}
	?>
	<div id="main" class="clearfix <?php echo $main_class; ?>" style="<?php echo $main_css; ?>">
		<div class="avada-row" style="<?php echo $row_css; ?>">
			<?php wp_reset_query(); ?>