<?php
/*
Plugin Name: WordCamp Miami AJAX Examples
Plugin URI:
Description:
Version: 0.0.1
Author: Josh Pollock
Author URI: http://JoshPress.net
*/

/**
 * Setup Script
 */
add_action( 'wp_enqueue_scripts', function() {
	wp_enqueue_script( 'wc_miami_ajax', plugin_dir_url( __FILE__ ) . 'js/example.js', array ( 'jquery' ), false, true );

	//Setup data for JavaScript
	$data = array(
		'nonce' => wp_create_nonce(  'wc_miami_ajax' ),
		'ajaxURL' => admin_url( 'admin-ajax.php' ),
		'failMessage' => __( 'Request Failed With Code ', 'wc_miami_ajax')
	);

	wp_localize_script( 'wc_miami_ajax', 'wc_miami_ajax', $data );

});

/**
 * Respond to example one
 */
add_action( 'wp_ajax_show_me_hobbes', 'wc_miami_ajax_show_me_hobbes' );
add_action( 'wp_ajax_nopriv_show_me_hobbes', 'wc_miami_ajax_show_me_hobbes' );
function wc_miami_ajax_show_me_hobbes() {
	//get URL for Hobbes and send back
	$id = 23;
	$img = wp_get_attachment_image_src( $id );
	die( $img[0] );

}



/**
 * Shortcode to output example one HTML
 */
add_shortcode( 'example_one_html', 'wc_miami_ajax_example_one_html' );
function wc_miami_ajax_example_one_html() {
	$img = wp_get_attachment_image_src( 13 );
	?>
	<input type="checkbox" id="show_me_hobbes" value="<?php esc_attr_e( 'Show Me Hobbes', 'wc_miami_ajax'); ?>">Show Me Hobbes<br>

	<img src="<?php echo esc_url( $img[0]) ?>" id="dogs" />
	<div id="spinner" style="display: none;"><img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif') ); ?>" /></div>

<?php }


/**
 * Respond to example 2 POST request, the dog chooser.
 */
add_action( 'wp_ajax_choose_dog', 'wc_miami_ajax_dog_chooser' );
add_action( 'wp_ajax_nopriv_choose_dog', 'wc_miami_ajax_dog_chooser' );
function wc_miami_ajax_dog_chooser() {
	//double check that the data is valid. If not, die.
	if ( ! isset( $_POST[ 'dog' ] ) || ! in_array( $_POST[ 'dog' ], array( 'josie', 'hobbes' )  ) )  {
		status_header( '400' );
		die();
	}

	$dog = $_POST[ 'dog' ];
	if ( 'josie' == $dog ) {
		$dog = 24;
	}else{
		$dog = 23;
	}

	//if user is logged in validate nonce and then save their choice
	if ( is_user_logged_in() ) {
		if ( ! isset( $_POST[ 'nonce' ] ) || ! wp_verify_nonce( $_POST[ 'nonce' ], 'wc_miami_ajax') ) {
			status_header( '401' );
			die();

		}
		update_user_meta( get_current_user_id(), 'dog', $dog );

	}

	//get The URL for the
	$img = wp_get_attachment_image_src( $dog );

	wp_send_json_success( array( 'dog' => $img[0] ) );

}

/**
 * Respond to example 2 GET request, to check dog.
 */
add_action( 'wp_ajax_dog_check', function() {

	//verify nonce and only respond if valid
	if ( isset( $_GET[ 'nonce' ] ) || wp_verify_nonce( strip_tags( $_GET[ 'nonce' ] ), 'wc_miami_ajax' ) ) {
		$dog = (int) get_user_meta( get_current_user_id(), 'dog', true );
		if ( 0 != $dog && is_int( $dog ) && in_array( $dog, array( 24, 23 ) ) ) {
			$img = wp_get_attachment_image_src( $dog );
			die( $img[0]);
		}

	}

});


/**
 * Shortcode to output example two HTML
 */
add_shortcode( 'example_two_html', 'wc_miami_ajax_example_two_html' );
function wc_miami_ajax_example_two_html() {
		$img = wp_get_attachment_image_src( 503 );
	?>
	<select id="dog-selector">
		<option value="none"><?php _e( 'Choose A Dog', 'wc_miamia_ajax' ); ?></option>
		<option value="hobbes">Hobbes</option>
		<option value="josie">Josie</option>
	</select>

	<img src="<?php echo esc_url( $img[0]) ?>" id="dogs" />
	<div id="spinner" style="display: none;"><img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif') ); ?>" /></div>

<?php }
