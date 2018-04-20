<?php
if( !class_exists('trueMetaBox') ) {
class trueMetaBox {
	/* construct function, add meta box and save action hooks */
	function __construct($options) {
		$this->options = $options;
		$this->prefix = $this->options['id'] .'_';
		add_action( 'add_meta_boxes', array( &$this, 'create' ) );
		add_action( 'save_post', array( &$this, 'save' ), 1, 2 );
	}
	
	/* function that creates the metabox */
	function create() {
		/* for each post type defined */
		foreach ($this->options['post_type'] as $post_type):
			/* if user capability fits */
			if( isset( $this->options['capability'] ) )
				if ( current_user_can( $this->options['capability']) )
					add_meta_box( $this->options['id'], $this->options['name'], array(&$this, 'fill'), $post_type, 'normal', $this->options['priority']);
		endforeach;
	}
	
	/* meta box html */
	function fill(){
		global $post;
		
		/* wp_nonce needed for security reason */
		wp_nonce_field( $this->options['id'], $this->options['id'].'_wpnonce', false, true );
		
		
		if( isset( $this->options['args'] ) ):
		
			$metabox_html = '<table class="form-table"><tbody>';
			
			/* for each option defined */
			foreach ( $this->options['args'] as $param ):
				/* if user capability fits */
				if ( !isset($param['capability']) )
					$param['capability'] = 'edit_posts';

				if ( current_user_can( $param['capability']) ):
					$metabox_html .= '<tr>';
					
					/* get option value and set default parameters */
					if( !$value = get_post_meta($post->ID, $this->prefix . $param['id'] , true) )
						if( isset( $param['default'] ) )
							$value = $param['default'];
					
					switch ( $param['type'] ) :
					
						/* <input type=text> */
						case 'text':{
							$metabox_html .= '<th style="font-weight:normal"><label for="' . $this->prefix . $param['id'] . '">' . $param['label'] . '</label></th><td><input name="' . $this->prefix .$param['id'] . '" type="text" id="' . $this->prefix .$param['id'] . '" value="' . esc_attr($value) . '" ';
							if( isset( $param['placeholder'] ) ) 
								$metabox_html .= 'placeholder="' . $param['placeholder'] . '" ';
							$metabox_html .= 'class="regular-text" /><br />';
							if( isset( $param['description'] ) )
								$metabox_html .= '<span class="description">' . $param['description'] . '</span>';
							$metabox_html .= '</td>';
							break;
						}
						
						/* colorpicker */
						case 'color':{
							$metabox_html .= '<th style="font-weight:normal"><label for="' . $this->prefix . $param['id'] . '">' . $param['label'] . '</label></th><td><input name="' . $this->prefix .$param['id'] . '" type="text" id="' . $this->prefix .$param['id'] . '" value="' . esc_attr($value) . '" ';
							if( isset( $param['placeholder'] ) ) 
								$metabox_html .= 'placeholder="' . $param['placeholder'] . '" ';
							$metabox_html .= 'class="true-color-field" /><br />';
							if( isset( $param['description'] ) )
								$metabox_html .= '<span class="description">' . $param['description'] . '</span>';
							$metabox_html .= '</td>';
							break;
						}
						
						/* <textarea> */
						case 'textarea':{
							$metabox_html .= '<th style="font-weight:normal"><label for="' . $this->prefix .$param['id'] . '">' . $param['label'] . '</label></th><td><textarea name="' . $this->prefix .$param['id'] . '" type="' . $param['type'] . '" rows="5" id="' . $this->prefix .$param['id'] . '" ';
							if( isset( $param['placeholder'] ) )
								$metabox_html .= 'placeholder="' . $param['placeholder'] . '" ';
							$metabox_html .= 'class="large-text" />' . esc_html($value) . '</textarea><br />';
							if( isset( $param['description'] ) )
								$metabox_html .= '<span class="description">' . $param['description'] . '</span>';
							$metabox_html .= '</td>';
							break;											
						}
						
						/* <input type=checkbox> */
						case 'checkbox':{
							$metabox_html .= '<th style="font-weight:normal"><label for="' . $this->prefix .$param['id'] . '">' . $param['label'] . '</label></th><td><label for="' . $this->prefix .$param['id'] . '"><input name="' . $this->prefix .$param['id'] . '" type="checkbox" id="' . $this->prefix .$param['id'] . '"';
							if( $value == 'on')
								$metabox_html .= ' checked="checked"';
							$metabox_html .= ' />';
							if( isset( $param['description'] ) )
								$metabox_html .= '<span class="description">' . $param['description'] . '</span>';
							$metabox_html .= '</td>';
							break;						
						}
						
						/* <select> */
						case 'select':{
							$metabox_html .= '<th style="font-weight:normal"><label for="' . $this->prefix .$param['id'] . '">' . $param['label'] . '</label></th><td><select name="' . $this->prefix .$param['id'] . '" id="' . $this->prefix .$param['id'] . '">';
							foreach( $param['args'] as $name=>$val ):
								$metabox_html .= '<option value="' . $val . '"';
								if( $value == $val )
									$metabox_html .= ' selected="selected"';
								$metabox_html .= '>' . $name . '</option>';
							endforeach;
							$metabox_html .= '</select><br />';
							if( isset( $param['description'] ) )
								$metabox_html .= '<span class="description">' . $param['description'] . '</span>';
							$metabox_html .= '</td>';
							break;					
						}
						
						/* <select multiple> */
						case 'multiselect':{
							$metabox_html .= '<th style="font-weight:normal"><label for="' . $this->prefix .$param['id'] . '">' . $param['label'] . '</label></th><td><select name="' . $this->prefix .$param['id'] . '[]" id="' . $this->prefix .$param['id'] . '" multiple="multiple" class="tmbselect2" style="max-width:25em;width:100%"';
							
							if( isset( $param['placeholder'] ) )
								$metabox_html .= ' data-placeholder="' . $param['placeholder'] . '" ';
							else
								$metabox_html .= ' data-placeholder="' . __('Select some options', 'truemetabox') . '" ';
							
							$metabox_html .= '>'; 
							
							foreach( $param['args'] as $name=>$val ):
								$metabox_html .= '<option value="' . $val . '"';
								if( is_array($value) && in_array($val,$value) )
									$metabox_html .= ' selected="selected"';
								$metabox_html .= '>' . $name . '</option>';
							endforeach;
							$metabox_html .= '</select><br />';
							if( isset( $param['description'] ) )
								$metabox_html .= '<span class="description">' . $param['description'] . '</span>';
							//$metabox_html .= '<script>jQuery(function($){ $("#' . $this->prefix .$param['id'] . '").select2(); });</script>';
							$metabox_html .= '</td>';
							break;					
						}
						
						/* posts dropdown with the search */
						case 'getposts':{
							// should we allow multimple results?
							$multiple = ( isset( $param['multiple'] ) && $param['multiple'] == true ) ? 'multiple="multiple"' : '';
							// what post types to get
							if( isset( $param['post_type'] ) && $param['post_type'] ) {
								if( is_array( $param['post_type'] ) ) $param['post_type'] = join(',',$param['post_type']);
								$param['post_type'] = $param['post_type'];
							} else {
								$param['post_type'] = 'post';
							}
							
							$metabox_html .= '<th style="font-weight:normal"><label for="' . $this->prefix .$param['id'] . '">' . $param['label'] . '</label></th><td><select class="tmb_get_posts" name="' . $this->prefix .$param['id'] . '[]" id="' . $this->prefix .$param['id'] . '" ' . $multiple . ' data-posttype="' . $param['post_type'] . '" style="max-width:25em;width:99%"';
							
							if( isset( $param['placeholder'] ) )
								$metabox_html .= ' data-placeholder="' . $param['placeholder'] . '" ';
							else
								$metabox_html .= ' data-placeholder="' . __('Search Posts...', 'truemetabox') . '" ';
							
							$metabox_html .= '>'; 
							
							//print_r( $value );
							if( $value ) {
								foreach( $value as $value_id ) {
									$title = get_the_title( $value_id );
									$title = ( mb_strlen( $title ) > 50 ) ? mb_substr( $title, 0, 49 ) . '...' : $title;
									$metabox_html .=  '<option value="' . $value_id . '" selected="selected">' . $title . '</option>';
								}
							}
							
							$metabox_html .= '</select><br />';
							if( isset( $param['description'] ) )
								$metabox_html .= '<span class="description">' . $param['description'] . '</span>';
							$metabox_html .= '</td>';
							break;			
						}
						
						/* terms dropdown with the search */
						case 'getterms':{
							// should we allow multimple results?
							$multiple = ( isset( $param['multiple'] ) && $param['multiple'] == true ) ? 'multiple="multiple"' : '';
							// what taxonomy to get
							if( isset( $param['taxonomy'] ) && $param['taxonomy'] ) {
								if( is_array( $param['taxonomy'] ) )
									$param['taxonomy'] = join(',',$param['taxonomy']);
							} else {
								$param['taxonomy'] = 'category';
							}
							
							$metabox_html .= '<th style="font-weight:normal"><label for="' . $this->prefix .$param['id'] . '">' . $param['label'] . '</label></th><td><select class="tmb_get_terms" name="' . $this->prefix .$param['id'] . '[]" id="' . $this->prefix .$param['id'] . '" ' . $multiple . ' data-taxonomy="' . $param['taxonomy'] . '" style="max-width:25em;width:99%"';
							
							if( isset( $param['placeholder'] ) ) {
								$metabox_html .= ' data-placeholder="' . $param['placeholder'] . '" ';
							} else {
								$label = 'Search terms';
								if( $txn = get_taxonomy( $param['taxonomy'] ) ) {
									$label = $txn->labels->search_items;
								}
								$metabox_html .= ' data-placeholder="' . $label . '..." ';
							}
							$metabox_html .= '>'; 
							
							//print_r( $value );
							if( $value ) {
								foreach( $value as $value_id ) {
									if( $term = get_term( $value_id ) ) {
										$term_name = ( mb_strlen( $term->name ) > 50 ) ? mb_substr( $term->name, 0, 49 ) . '...' : $term->name;
										$metabox_html .=  '<option value="' . $value_id . '" selected="selected">' . $term_name . '</option>';
									}
								}
							}
							
							$metabox_html .= '</select><br />';
							if( isset( $param['description'] ) )
								$metabox_html .= '<span class="description">' . $param['description'] . '</span>';
							$metabox_html .= '</td>';
							break;			
						}
						
						/* <input type=radio> */
						case 'radio':{
							$i = 0;
							$metabox_html .= '<th style="font-weight:normal"><label for="' . $this->prefix .$param['id'] . $i . '">' . $param['label'] . '</label></th><td>';
							foreach( $param['args'] as $name=>$val ):
								$metabox_html .= '<p><label for="' . $this->prefix .$param['id'] . $i . '"><input type="radio" name="' . $this->prefix .$param['id'] . '" id="' . $this->prefix .$param['id'] . $i . '"';
								
								if( $value == $val )
									$metabox_html .= ' checked="checked"';
									
								$metabox_html .= ' value="' . $val . '">' . $name . '</label></p>';
								$i++;
							endforeach;
							if( isset( $param['description'] ) )
								$metabox_html .= '<p><span class="description">' . $param['description'] . '</span></p>';
							$metabox_html .= '</td>';
							break;						
						}
						
						/* attachment image */
						case 'image':{
							$metabox_html .= '<th style="font-weight:normal"><label for="' . $this->prefix . $param['id'] . '">' . $param['label'] . '</label></th><td>';
							$metabox_html .= tmb_image_uploader_field( $this->prefix . $param['id'], $value );
							$metabox_html .= '<br />';
							if( isset( $param['description'] ) )
								$metabox_html .= '<span class="description">' . $param['description'] . '</span>';
							if( isset( $param['attach'] ) )
								$metabox_html .= '<input type="hidden" name="'.$this->prefix . $param['id'].'_attach" value="yes" />';
							$metabox_html .= '</td>';
							break;
						}
						
						/* file */
						case 'file':{
							$metabox_html .= '<th style="font-weight:normal"><label for="' . $this->prefix . $param['id'] . '">' . $param['label'] . '</label></th><td><input name="' . $this->prefix .$param['id'] . '" type="text" id="' . $this->prefix .$param['id'] . '" value="' . esc_attr($value) . '" ';
							if( isset( $param['placeholder'] ) ) 
								$metabox_html .= 'placeholder="' . $param['placeholder'] . '" ';
							$metabox_html .= 'class="regular-text true-file" /><br />' . __('Paste the file URL above or', 'truemetabox') . ' <a href="javascript:void()" class="true_upload_file_button">' . __('click here to upload', 'truemetabox') . '</a>.<br />';
							if( isset( $param['description'] ) )
								$metabox_html .= '<span class="description">' . $param['description'] . '</span>';
							$metabox_html .= '</td>';
							break;
						}
						
						/* editor */
						case 'editor':{
							$metabox_html .= '<th style="font-weight:normal"><label for="' . $this->prefix .$param['id'] . '">' . $param['label'] . '</label></th><td>';
							
							$editor_args = array(
								'textarea_rows'=>7,
								'teeny'=>true
							);
							if( isset( $param['editor_args'] ) )
								$editor_args = wp_parse_args( $param['editor_args'], $editor_args );
							
							ob_start();

							// Echo the editor to the buffer
							wp_editor( $value, $this->prefix .$param['id'], $editor_args);

							// Store the contents of the buffer in a variable
							$editor_contents = ob_get_clean();
							$metabox_html .= $editor_contents;
							
							if( isset( $param['description'] ) )
								$metabox_html .= '<span class="description">' . $param['description'] . '</span>';
							$metabox_html .= '</td>';
							break;											
						}
						
						/* video with preview */
						case 'video':{
							$metabox_html .= '<th style="font-weight:normal"><label for="' . $this->prefix . $param['id'] . '">' . $param['label'] . '</label></th><td><input name="' . $this->prefix .$param['id'] . '" type="text" id="' . $this->prefix .$param['id'] . '" value="' . esc_attr($value) . '" ';
							if( isset( $param['placeholder'] ) ) 
								$metabox_html .= 'placeholder="' . $param['placeholder'] . '" ';
							$metabox_html .= 'class="regular-text true-video-preview" /><button type="submit" class="true_v_preview_button button">' . __('Preview', 'truemetabox') . '</button><span class="spinner" style="float:none;vertical-align:top"></span><br />';
							if( isset( $param['description'] ) )
								$metabox_html .= '<span class="description">' . $param['description'] . '</span>';
							$metabox_html .= '<div class="true_v_iframe">' . tmb_youtube_vimeo_iframe( $value ) . '</div></td>';
							break;
						}
						
						/* google maps */
						case 'map':{
							$description = ( isset( $param['description'] ) ) ? $param['description'] : '';
							$default_lat = (isset($param['default_lat'])) ? $param['default_lat'] : '-25.363';
							$default_lng = (isset($param['default_lng'])) ? $param['default_lng'] : '131.044';
							$lat = ($lat = get_post_meta($post->ID, $this->prefix . $param['id'] . '_lat' , true)) ? esc_attr( $lat ) : $default_lat;
							$lng = ($lng = get_post_meta($post->ID, $this->prefix . $param['id'] . '_lng' , true)) ? esc_attr( $lng ) : $default_lng;
							$metabox_html .= '<th style="font-weight:normal"><label for="' . $this->prefix . $param['id'] . '">' . $param['label'] . '</label></th><td>';
							$metabox_html .= tmb_map( $this->prefix . $param['id'], $lat, $lng, esc_attr( $value ), $description );
							
							$metabox_html .= '</td>';
							break;	
						
						}
						
						/* omg table 
						case 'table':{
							
							$metabox_html .= '<td colspan="2">';
							
							$metabox_html .= '<div class="misha_attr"><table cellpadding="0" cellspacing="0" class="attributes">';
								
							$thead = '';
							$tbody = '';
							$value = array();
							foreach( $param['columns'] as $column ) {
								$thead .= '<th';
								if( isset( $column['width'] ) )
									$thead .= ' style="width:' . $column['width'] . '" ';			
								$thead .= '>' . $column['title'] . '</th>';
								
								switch( $column['type'] ) {
									case 'text':{
										$column_field = '<input type="text" name="'  . $param['id'] . '_' . $column['id'] . '" ';
										if( isset( $column['placeholder'] ) ) 
											$column_field .= 'placeholder="' . $column['placeholder'] . '" ';
										$column_field .= 'class="regular-text" />';
										break;
									}
									case 'checkbox':{
										$column_field = '<input name="'  . $param['id'] . '_' . $column['id'] . '" type="checkbox"';
										//if( $value == 'on')
										//	$column_field .= ' checked="checked"';
										$column_field .= ' />';
										$column_field .= '</td>';
										break;						
									}
									
								}
								$tbody .= '<td>' . $column_field . '</td>';
							}
							
							$metabox_html .= '<thead><tr><th class="handleth">&nbsp;</th>' . $thead . '<th class="removeth">&nbsp;</th></tr></thead>';
							$metabox_html .= '<tbody><tr><td class="handletd"></td>' . $tbody . '<td class="removetd"><a href="javaScript:void(0)" class="button">&times;</a></td></tr></tbody>';
							
							
							$metabox_html .= '</table></div></td>';
							break;
						}
						*/
						
						
					endswitch;
					$metabox_html .= '</tr>';
				endif;
			endforeach;
			
			$metabox_html .= '</tbody></table>';
			
			/* echo metabox content*/
			echo $metabox_html;
			
		endif;
	}
	
	
	function save( $post_id, $post ){
	
		/* if wp_nonce didn't match */
		if( !isset( $_POST[ $this->options['id'].'_wpnonce' ] ))
			return;
		if ( !wp_verify_nonce( $_POST[ $this->options['id'].'_wpnonce' ], $this->options['id'] ) )
			return;
			
		/* if current user can not edit posts */
		if ( !current_user_can( 'edit_post', $post_id ) )
			return;
			
		/* if this post type do not have metabox */
		if ( !in_array($post->post_type, $this->options['post_type']))
			return;
			
		foreach ( $this->options['args'] as $param ) {
			if ( !isset($param['capability']) )
					$param['capability'] = 'edit_posts';
					
			if ( current_user_can( $param['capability'] ) ) {
				if ( isset( $_POST[ $this->prefix . $param['id'] ] ) && ( is_array($_POST[ $this->prefix . $param['id'] ] ) || trim( $_POST[ $this->prefix . $param['id'] ] ) ) ) {
					if($param['type'] == 'map') {
						update_post_meta( $post_id, $this->prefix . $param['id'] . '_lat', trim($_POST[ $this->prefix . $param['id'] . '_lat' ]) );
						update_post_meta( $post_id, $this->prefix . $param['id'] . '_lng', trim($_POST[ $this->prefix . $param['id'] . '_lng' ]) );
					}
					update_post_meta( $post_id, $this->prefix . $param['id'], $_POST[ $this->prefix . $param['id'] ] );
				} else {
					delete_post_meta( $post_id, $this->prefix . $param['id'] );
				}
			}
		}
	}
}
}