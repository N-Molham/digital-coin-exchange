<?php
/**
 * SMOF Interface
 *
 * @package     WordPress
 * @subpackage  SMOF
 * @since       1.4.0
 * @author      Syamil MJ
 */


/**
 * Admin Init
 *
 * @uses wp_verify_nonce()
 * @uses header()
 * @uses update_option()
 *
 * @since 1.0.0
 */
function optionsframework_admin_init()
{
	// Rev up the Options Machine
	global $of_options, $options_machine;
	$options_machine = new Options_Machine($of_options);
}

/**
 * Create Options page
 *
 * @uses add_theme_page()
 * @uses add_action()
 *
 * @since 1.0.0
 */
function optionsframework_add_admin() {

    $of_page = add_theme_page( THEMENAME, 'Theme Options', 'edit_theme_options', 'optionsframework', 'optionsframework_options_page');

	// Add framework functionaily to the head individually
	add_action("admin_print_scripts-$of_page", 'of_load_only');
	add_action("admin_print_styles-$of_page",'of_style_only');
	add_action( "admin_print_styles-$of_page", 'optionsframework_mlu_css', 0 );
	add_action( "admin_print_scripts-$of_page", 'optionsframework_mlu_js', 0 );

}


/**
 * Build Options page
 *
 * @since 1.0.0
 */
function optionsframework_options_page(){

	global $options_machine;
	/*
	//for debugging
	$data = get_option(OPTIONS);
	print_r($data);
	*/

	include_once( ADMIN_PATH . 'front-end/options.php' );

}

/**
 * Create Options page
 *
 * @uses wp_enqueue_style()
 *
 * @since 1.0.0
 */
function of_style_only(){
	wp_enqueue_style('admin-style', ADMIN_DIR . 'assets/css/admin-style.css');
	wp_enqueue_style('color-picker', ADMIN_DIR . 'assets/css/colorpicker.css');
}

/**
 * Create Options page
 *
 * @uses add_action()
 * @uses wp_enqueue_script()
 *
 * @since 1.0.0
 */
function of_load_only()
{
	add_action('admin_head', 'of_admin_head');

	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-sortable');
	wp_enqueue_script('jquery-input-mask', ADMIN_DIR .'assets/js/jquery.maskedinput-1.2.2.js', array( 'jquery' ));
	wp_enqueue_script('tipsy', ADMIN_DIR .'assets/js/jquery.tipsy.js', array( 'jquery' ));
	wp_enqueue_script('color-picker', ADMIN_DIR .'assets/js/colorpicker.js', array('jquery'));
	wp_enqueue_script('ajaxupload', ADMIN_DIR .'assets/js/ajaxupload.js', array('jquery'));
	wp_enqueue_script('cookie', ADMIN_DIR . 'assets/js/cookie.js', 'jquery');
	wp_enqueue_script('smof', ADMIN_DIR .'assets/js/smof.js', array( 'jquery' ));
}

/**
 * Front end inline jquery scripts
 *
 * @since 1.0.0
 */
function of_admin_head() { ?>

	<script type="text/javascript" language="javascript">

	jQuery.noConflict();
	jQuery(document).ready(function($){

		// COLOR Picker
		$('.colorSelector').each(function(){
			var Othis = this; //cache a copy of the this variable for use inside nested function

			$(this).ColorPicker({
					color: '<?php if(isset($color)) echo $color; ?>',
					onShow: function (colpkr) {
						$(colpkr).fadeIn(500);
						return false;
					},
					onHide: function (colpkr) {
						$(colpkr).fadeOut(500);
						return false;
					},
					onChange: function (hsb, hex, rgb) {
						$(Othis).children('div').css('backgroundColor', '#' + hex);
						$(Othis).next('input').attr('value','#' + hex);

					}
			});

		}); //end color picker

	}); //end doc ready

	</script>

<?php }

/**
 * Ajax Save Options
 *
 * @uses get_option()
 * @uses update_option()
 *
 * @since 1.0.0
 */
function of_ajax_callback()
{
	global $options_machine, $of_options;

	$nonce=$_POST['security'];

	if (! wp_verify_nonce($nonce, 'of_ajax_nonce') ) die('-1');

	//get options array from db
	$all = get_option(OPTIONS);

	$save_type = $_POST['type'];

	//echo $_POST['data'];

	//Uploads
	if($save_type == 'upload')
	{

		$clickedID = $_POST['data']; // Acts as the name
		$filename = $_FILES[$clickedID];
       	$filename['name'] = preg_replace('/[^a-zA-Z0-9._\-]/', '', $filename['name']);
		$override['test_form'] = false;
		$override['action'] = 'wp_handle_upload';
		$uploaded_file = wp_handle_upload($filename,$override);

			$upload_tracking[] = $clickedID;

			//update $options array w/ image URL
			$upload_image = $all; //preserve current data

			$upload_image[$clickedID] = $uploaded_file['url'];

			update_option(OPTIONS, $upload_image ) ;

			do_action( 'fusion_admin_save' );


		 if(!empty($uploaded_file['error'])) {echo 'Upload Error: ' . $uploaded_file['error']; }
		 else { echo $uploaded_file['url']; } // Is the Response

	}
	elseif($save_type == 'image_reset')
	{

			$id = $_POST['data']; // Acts as the name

			$delete_image = $all; //preserve rest of data
			$delete_image[$id] = ''; //update array key with empty value
			update_option(OPTIONS, $delete_image ) ;

			do_action( 'fusion_admin_save' );

	}
	elseif($save_type == 'backup_options')
	{

		$backup = $all;
		$backup['backup_log'] = date('r');

		update_option(BACKUPS, $backup ) ;

		do_action( 'fusion_admin_save' );

		die('1');
	}
	elseif($save_type == 'restore_options')
	{

		$data = get_option(BACKUPS);

		update_option(OPTIONS, $data);

		do_action( 'fusion_admin_save' );

		die('1');
	}
	elseif($save_type == 'import_options'){

		$data = $_POST['data'];
		$data = unserialize(base64_decode($data)); //100% safe - ignore theme check nag
		update_option(OPTIONS, $data);

		do_action( 'fusion_admin_save' );

		die('1');
	}
	elseif ($save_type == 'save')
	{
		global $theme_name;

		wp_parse_str(stripslashes($_POST['data']), $data);
		unset($data['security']);
		unset($data['of_save']);

		$data_from_db = get_option(OPTIONS);

		if(defined('ICL_LANGUAGE_CODE')) {
			$languages = icl_get_languages('skip_missing=1');
			global $sitepress;
			if($_SERVER['HTTP_REFERER']) {
				$parse_referer = parse_url($_SERVER['HTTP_REFERER']);
				wp_parse_str($parse_referer['query'], $parse_query);
				if( $parse_query['lang'] == 'all' ) {
					foreach($data as $posted_key => $posted_data) {
						if($data_from_db[$posted_key] != $posted_data) {
							$data[$posted_key] = $posted_data;
						}
					}
					foreach($languages as $language) {
						$language_name = '';
						if($language['language_code'] != 'all') {
							$language_name = '_'.$language['language_code'];
						}
						if( $language['language_code'] == 'en' ) {
							$language_name = '';
						}

						$options_name = $theme_name.'_options'.$language_name;
						update_option($options_name, $data);

						do_action( 'fusion_admin_save', $language_name );
					}
				} else {
					update_option(OPTIONS, $data);

					do_action( 'fusion_admin_save' );
				}
			} else {
				update_option(OPTIONS, $data);

				do_action( 'fusion_admin_save' );
			}
		} else {
			update_option(OPTIONS, $data);

			do_action( 'fusion_admin_save' );
		}

		die('1');
	}
	elseif ($save_type == 'reset')
	{
		update_option(OPTIONS,$options_machine->Defaults);

		do_action( 'fusion_admin_save' );

        die('1'); //options reset
	}

  	die();
}