<?php
/*
Plugin Name: True Metabox
Plugin URI: https://rudrastyh.com/plugins/meta-boxes-options-pages
Description: Great decision for creating admin UI
Version: 2.3
Author: Misha Rudrastyh
Author URI: http://rudrastyh.com

Copyright 2014-2017 Misha Rudrastyh ( http://rudrastyh.com )

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/*
 * Posts Metabox
 */
require_once plugin_dir_path( __FILE__ ) . '/class/class.metabox.php';
require_once plugin_dir_path( __FILE__ ) . '/class/class.terms.php';
require_once plugin_dir_path( __FILE__ ) . '/class/class.options.php';
require_once plugin_dir_path( __FILE__ ) . '/class/class.upgrade.php';

add_action( 'admin_enqueue_scripts', 'tmb_admin_enqueues' );
function tmb_admin_enqueues( $hook ){

	if ( !in_array( $hook, array('post.php','post-new.php','edit-tags.php', 'term.php','options-general.php','options-writing.php','options-reading.php','options-discussion.php','options-media.php','options-permalink.php') )
	&& substr($hook,0,14) != 'settings_page_' ) {
       return;
    }
	wp_enqueue_style( 'wp-color-picker' );
	
	wp_enqueue_style('tmbadmincss', plugin_dir_url( __FILE__ ) . 'css/admin.css' );
	//wp_enqueue_style('chosencss', plugin_dir_url( __FILE__ ) . 'css/chosen.min.css' );
	
	if ( ! did_action( 'wp_enqueue_media' ) )
		wp_enqueue_media();
	
	//wp_enqueue_script('chosenjs', plugin_dir_url( __FILE__ ) . 'js/chosen.jquery.min.js', array('jquery') );
	wp_enqueue_script('select2', plugin_dir_url( __FILE__ ) . 'js/select2.min.js', array('jquery') );
	
	wp_enqueue_script('tmbadmin', plugin_dir_url( __FILE__ ) . 'js/admin.js', array('jquery','wp-color-picker','select2'), '2.3' );
	
	wp_localize_script( 'tmbadmin', 'tmb_object', array( 
		// uploader
		'insertImage' => __( 'Insert image', 'truemetabox' ),
		'useThisImage' => __( 'Use this image', 'truemetabox' ),
		'insertFile' => __( 'Insert file', 'truemetabox' ),
		'useThisFile' => __( 'Use this file', 'truemetabox' ),
		'uploadImage' => __( 'Upload Image', 'truemetabox' ),
		'areYouSure' => __( 'Are you sure?', 'truemetabox' ),
		// select2
		'inputTooShort1' => __( 'Please enter 1 or more characters.', 'truemetabox' ),
		'inputTooShort2' => __( 'Please enter %qty% or more characters.', 'truemetabox' ),
		'noResults' => __( 'No results found', 'truemetabox' ),
		'searching' => __( 'Searching...', 'truemetabox' )
	) );
	
	wp_enqueue_script('tmbmap', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyBVoovHcXQcbOlVDtl5CoYeh7KejGELfLY&libraries=places&language=' . str_replace('-','_',get_bloginfo('language')), array('jquery') );
}



function tmb_image_uploader_field( $name, $value = '') {
	$image = ' button">' . __('Upload image', 'truemetabox');
	$display = 'none';
	if( $image_attributes = wp_get_attachment_image_src( $value, 'full' ) ) {
		
		$src = $image_attributes[0];
		$image = '"><img src="' . $src . '" style="max-width:95%;display:block;" />';
		$display = 'inline-block';
	} 
	return '
	<div>
		<a href="javascript:void(0)" class="true_upload_image_button' . $image . '</a>
		<input type="hidden" name="' . $name . '" id="' . $name . '" value="' . $value . '" />
		<a href="javascript:void(0)" class="true_remove_image_button" style="display:inline-block;display:' . $display . '">' . __('Remove image', 'truemetabox') . '</a>
	</div>
	';
}

function tmb_youtube_vimeo_iframe( $url ) {

	preg_match(
		'/\/\/(www\.)?vimeo.com\/(\d+)($|\/)/',
		$url,
		$matches
	);
	
	$width = '420';
	$height = '270';
		
	if( isset( $matches[2] ) ) {
		return '<iframe src="http://player.vimeo.com/video/'.$matches[2].'?title=0&amp;byline=0&amp;portrait=0&amp;badge=0&amp;color=ffffff" width="'.$width.'" height="'.$height.'" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
	} else {
		preg_match(
			'/[\\?\\&]v=([^\\?\\&]+)/',
			$url,
			$matches
		);

		if( isset( $matches[1] ) ) {
			return '<iframe class="dt-youtube" width="' .$width. '" height="'.$height.'" src="//www.youtube.com/embed/'.$matches[1].'?rel=0" frameborder="0" allowfullscreen></iframe>';
		} else {
			if (FALSE === strpos($url, 'youtu.be')) {
				parse_str(parse_url($url, PHP_URL_QUERY), $id);
				$id = ( isset( $id['v'] ) ) ? $id['v'] : '';
			} else {
				$id = basename($url);
			}
			if( $id ) {
				return '<iframe class="dt-youtube" width="' .$width. '" height="'.$height.'" src="//www.youtube.com/embed/'.$id.'?rel=0" frameborder="0" allowfullscreen></iframe>';
			} else {
				return __('No video','truemetabox');
			}
		}
	}
}

function tmb_give_me_video(){
	echo tmb_youtube_vimeo_iframe( $_POST['video'] );
	die;
}

add_action('wp_ajax_truevid', 'tmb_give_me_video');
add_action('wp_ajax_nopriv_truevid', 'tmb_give_me_video');

/* posts */
add_action( 'wp_ajax_tmbmishagetposts', 'tmb_misha_all_the_posts' );
function tmb_misha_all_the_posts(){
	$return = array();
	$q = new WP_Query( array( 
		'post_type' => explode(',', $_GET['post_type']),
		's'=> $_GET['q'],
		'post_status' => 'publish',
		'ignore_sticky_posts' => 1,
		'posts_per_page' => 50
	) );
	if( $q->have_posts() ) :
		while( $q->have_posts() ) : $q->the_post();	
			// shorten the title a little
			$title = ( mb_strlen( $q->post->post_title ) > 50 ) ? mb_substr( $q->post->post_title, 0, 49 ) . '...' : $q->post->post_title;
			$return[] = array( $q->post->ID, $title );
		endwhile;
	endif;
	echo json_encode( $return );
	die;
}

/* terms */
add_action( 'wp_ajax_tmbmishagetterms', 'tmb_misha_all_the_terms' );
function tmb_misha_all_the_terms(){
	$return = array();
	
	$taxonomies = explode(',', $_GET['taxonomy'] );
	
	$terms = get_terms( array(
		'taxonomy' => $taxonomies,
		'hide_empty' => 0,
		'search' => $_GET['q'] 
	) );
	
	if( $terms ) {
		foreach($terms as $term){
			$term_name = ( mb_strlen( $term->name ) > 50 ) ? mb_substr( $term->name, 0, 49 ) . '...' : $term->name;
			
			if( count( $taxonomies > 1 ) )
				$term_name .= ' (' . $term->taxonomy . ')'; 
				
			$return[] = array( $term->term_id, $term_name );
		}
	}
	echo json_encode( $return );
	die;
}

function tmb_map( $m, $lat, $lng, $value, $description = ''){
	$return = '<div id="map' . $m . '" style="height:400px;" class="misha_map"></div>
				<div class="truemapparams">
				<p><label>' . __('Latitude', 'truemetabox') . ' <input type="text" id="' . $m . '_lat" name="' . $m . '_lat" placeholder="' . __('Latitude', 'truemetabox') . '" value="' .$lat. '" /></label><label style="margin-left: 5px;">' . __('Longitude', 'truemetabox') . ' <input type="text" id="' . $m . '_lng" name="' . $m . '_lng" placeholder="' . __('Longitude', 'truemetabox') . '" value="' . $lng . '" /></label> <button class="button true_upd_map" id="true_upd_map' . $m . '">' . __('Update map', 'truemetabox') . '</button></p>
				<p><label>' . __('Address', 'truemetabox') . ' <input type="text" id="' . $m . '" name="' . $m . '" class="regular-text" placeholder="' . __('Type to autocomplete', 'truemetabox') . '" value="' . $value . '" /></label> <button class="button true_locate_me" id="true_locate_me' . $m . '"><span class="dashicons dashicons-location-alt" style="margin-top:2px"></span> ' . __('Locate me', 'truemetabox') . '</button> <span class="spinner" style="float:none;vertical-align:top"></span></p>';
				if( $description )
					$return .= '<p><span class="description">' . $description . '</span></p>';

				$return .= '</div>
    						<script>
							var map' . $m . ';
							function initMap' . $m . '() {
								var myLatLng' . $m . ' = {lat: ' . $lat . ', lng: ' . $lng . '};
  								var map' . $m . ' = new google.maps.Map(document.getElementById(\'map' . $m . '\'), {zoom: 4, disableDefaultUI: true, scrollwheel: false, zoomControl: true, center: myLatLng' . $m . '});
								var marker' . $m . ' = new google.maps.Marker({position: myLatLng' . $m . ',map: map' . $m . ',draggable: true});
								
								google.maps.event.addListener(marker' . $m . ', "dragstart", function (event) {
									marker' . $m . '.setAnimation(3);
								});
								google.maps.event.addListener(marker' . $m . ', \'dragend\', function() {
									jQuery(\'#' . $m . '_lat\').val(marker' . $m . '.getPosition().lat());
  									jQuery(\'#' . $m . '_lng\').val(marker' . $m . '.getPosition().lng());
    								geocodePosition' . $m . '(marker' . $m . '.getPosition());
    								marker' . $m . '.setAnimation(4);
								});

								jQuery(\'#true_upd_map' . $m . '\').click(function(){
									var lat = Number(jQuery(\'#' . $m . '_lat\').val());
									var lng = Number(jQuery(\'#' . $m . '_lng\').val());
									var pos = {lat: lat, lng: lng};
									marker' . $m . '.setPosition(pos);
									map' . $m . '.setCenter(pos);
									geocodePosition' . $m . '(marker' . $m . '.getPosition());
									return false;
								});


								jQuery(\'#true_locate_me' . $m . '\').click(function(){
									var locateMeButton = jQuery(this);
									locateMeButton.next().css(\'visibility\',\'visible\');
									if (navigator.geolocation) {
										navigator.geolocation.getCurrentPosition(function(position) {
											var pos = {lat: position.coords.latitude,lng: position.coords.longitude};
											marker' . $m . '.setPosition(pos);
											map' . $m . '.setCenter(pos);
											jQuery(\'#' . $m . '_lat\').val(marker' . $m . '.getPosition().lat());
  											jQuery(\'#' . $m . '_lng\').val(marker' . $m . '.getPosition().lng());
   											geocodePosition' . $m . '(marker' . $m . '.getPosition());
											locateMeButton.next().css(\'visibility\',\'hidden\');
										}, function(){
											alert("Error: The Geolocation service failed");
										});
									} else {
										alert("Error: Your browser doesn\'t support geolocation.");
									}
									return false;
								});


								function geocodePosition' . $m . '(pos) {
									geocoder = new google.maps.Geocoder();
									geocoder.geocode({latLng: pos}, function(results, status) {
            							if (status == google.maps.GeocoderStatus.OK) {
                							jQuery("#' . $m . '").val( results[0].formatted_address.replace("Unnamed Road, ","") );
            							} else {
                							alert(\'Cannot determine address at this location.\');
            							}
       								});
								}

								autocomplete' . $m . ' = new google.maps.places.Autocomplete((document.getElementById(\'' . $m . '\')),{types: [\'geocode\']});
								autocomplete' . $m . '.addListener(\'place_changed\', fillInAddress' . $m . ');

								function fillInAddress' . $m . '() {
									var pos = autocomplete' . $m . '.getPlace().geometry.location;
									marker' . $m . '.setPosition(pos);
									map' . $m . '.setCenter(pos);
									jQuery(\'#' . $m . '_lat\').val(marker' . $m . '.getPosition().lat());
  									jQuery(\'#' . $m . '_lng\').val(marker' . $m . '.getPosition().lng());
   									geocodePosition' . $m . '(marker' . $m . '.getPosition());
  
								}
							}
							jQuery(function($){
								initMap' . $m . '();
							});
							</script>';
	return $return;
}


new tmbUpdater();


add_action( 'plugins_loaded', 'true_load_plugin_textdomain' );
 
function true_load_plugin_textdomain() {
	load_plugin_textdomain( 'truemetabox', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' ); 
}
