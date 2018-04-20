<?php
if( !class_exists('trueTaxonomyMetaBox') ) {
class trueTaxonomyMetaBox {
	/* construct function, add meta box and save action hooks */
	function __construct($options) {
		$this->options = $options;
		$this->prefix = $this->options['id'] .'_';
		
		foreach( $this->options['taxonomy'] as $taxonomy ) :
			//if( isset( $this->options['capability'] ) ) {
				//if ( current_user_can( $this->options['capability']) ) {
					add_action( $taxonomy . '_add_form_fields', array( &$this, 'fill' ), 10, 1 );
					add_action( $taxonomy . '_edit_form_fields', array( &$this, 'fill' ), 10, 1 );
					add_action( 'created_' . $taxonomy, array( &$this, 'save' ), 10, 2 );
					add_action( 'edited_' . $taxonomy, array( &$this, 'save' ), 10, 2 );
				//}
			//}
		endforeach;
		
	}
	
	function fill( $term ){
		
		/* wp_nonce needed for security reason */
		wp_nonce_field( $this->options['id'], $this->options['id'].'_wpnonce', false, true );
		
		
		if( isset( $this->options['args'] ) ):
			
			
			if( isset( $_GET['tag_ID'] ) ) {
				$wrap1 = '<tr class="misha-field form-field term-group-wrap"><th scope="row"><label for="feature-group">';
				$wrap2 = '</label></th><td>';
				$wrap3 = '</td></tr>';
				$html = '';
			} else {
				$html = '<script>
								jQuery(function($){
									var numberOfTags = 0;
									if( !$(\'#the-list\').children(\'tr\').first().hasClass(\'no-items\') )
										numberOfTags = $(\'#the-list\').children(\'tr\').length;
									$(document).ajaxComplete(function( event, xhr, settings ){
            							newNumberOfTags = $(\'#the-list\').children(\'tr\').length;
            							if( parseInt(newNumberOfTags) > parseInt(numberOfTags) ) {
            								numberOfTags = newNumberOfTags;
            								$(\'.misha-field\').find(\'.wp-picker-clear\').click();
            								$(\'.tmbselect2\').val(\'\').trigger(\'change\');
            								$(\'.tmb_get_posts\').val(\'\').trigger(\'change\');
            								$(\'.tmb_get_terms\').val(\'\').trigger(\'change\');
            								$(\'.true_remove_image_button\').each(function(){
												$(this).hide().prev().val(\'\').prev().addClass(\'button\').html(\'Upload image\');
											});
											$(".misha_editor").each(function(i){
            									var editorid = $(this).find("textarea").attr("id");
            									tinyMCE.get(editorid).setContent("");
            								});
            							}
            							
        							});
        							$("#submit").mouseenter(function(){
        								$(".misha_editor").each(function(i){
        									if( $(this).find(".wp-editor-wrap").hasClass("tmce-active") ) {
        										var editorid = $(this).find("textarea").attr("id");
        										$(this).find("textarea").val(tinyMCE.get(editorid).getContent());
        									}
										});
        							});
        							});
        							</script>';
				$wrap1 = '<div class="misha-field form-field term-group"><label for="feature-group">';
				$wrap2 = '</label>';
				$wrap3 = '</div>';
			}
			
			/* for each option defined */
			foreach ( $this->options['args'] as $param ):
			
				$value = '';
				
				/* if user capability fits */
				if ( !isset($param['capability']) )
					$param['capability'] = 'edit_posts';

				//if ( current_user_can( $param['capability']) ):
					
					if( isset( $param['default'] ) )
							$value = $param['default'];
							
					if( isset( $_GET['tag_ID'] ) )
						if( $saved_value = get_term_meta( $term->term_id, $this->prefix . $param['id'], true ) )
							$value = $saved_value;
					
					
					switch ( $param['type'] ) :
					
						/* <input type=text> */
						case 'text':{
							$field = '<input name="' . $this->prefix .$param['id'] . '" type="text" id="' . $this->prefix .$param['id'] . '" value="' . esc_attr($value) . '" ';
							if( isset( $param['placeholder'] ) ) 
								$field .= 'placeholder="' . $param['placeholder'] . '" ';
							$field .= 'class="regular-text" />';
							if( isset( $param['description'] ) )
								$field .= '<p class="description">' . $param['description'] . '</p>';
							break;
						}
						
						/* colorpicker */
						case 'color':{
							$field = '<input name="' . $this->prefix .$param['id'] . '" type="text" id="' . $this->prefix .$param['id'] . '" value="' . esc_attr($value) . '" ';
							if( isset( $param['placeholder'] ) ) 
								$field .= 'placeholder="' . $param['placeholder'] . '" ';
							$field .= 'class="true-color-field" /><br />';
							if( isset( $param['description'] ) )
								$field .= '<p class="description">' . $param['description'] . '</p>';
							
							break;
						}
						
						/* <textarea> */
						case 'textarea':{
							$field = '<textarea name="' . $this->prefix .$param['id'] . '" type="' . $param['type'] . '" rows="5" id="' . $this->prefix .$param['id'] . '" ';
							if( isset( $param['placeholder'] ) )
								$field .= 'placeholder="' . $param['placeholder'] . '" ';
							$field .= 'class="large-text" />' . esc_html($value) . '</textarea>';
							if( isset( $param['description'] ) )
								$field .= '<p class="description">' . $param['description'] . '</p>';
						
							break;											
						}
						
						case 'checkbox':{
							$field = '<label for="' . $this->prefix .$param['id'] . '"><input name="' . $this->prefix .$param['id'] . '" type="checkbox" id="' . $this->prefix .$param['id'] . '"';
							if( $value == 'on')
								$field .= ' checked="checked"';
							$field .= ' />' . $param['label'];
							if( isset( $param['description'] ) )
								$field .= '<p class="description">' . $param['description'] . '</p>';
							$param['label'] = '';
							break;						
						}
						
						/* <select> */
						case 'select':{
							$field =  '<select name="' . $this->prefix .$param['id'] . '" id="' . $this->prefix .$param['id'] . '">';
							foreach( $param['args'] as $name=>$val ):
								$field .= '<option value="' . $val . '"';
								if( $value == $val )
									$field .= ' selected="selected"';
								$field .= '>' . $name . '</option>';
							endforeach;
							$field .= '</select>';
							if( isset( $param['description'] ) )
								$field .= '<p class="description">' . $param['description'] . '</p>';
							break;					
						}
						
						case 'multiselect':{
							$field = '<select name="' . $this->prefix .$param['id'] . '[]" id="' . $this->prefix .$param['id'] . '" multiple="multiple" class="tmbselect2" style="max-width:25em;width:95%"';
							
							if( isset( $param['placeholder'] ) )
								$field .= ' data-placeholder="' . $param['placeholder'] . '" ';
							else
								$field .= ' data-placeholder="' . __('Select some options', 'truemetabox') . '" ';
							
							$field .= '>'; 
							
							foreach( $param['args'] as $name=>$val ):
								$field .= '<option value="' . $val . '"';
								if( is_array($value) && in_array($val,$value) )
									$field .= ' selected="selected"';
								$field .= '>' . $name . '</option>';
							endforeach;
							$field .= '</select>';
							if( isset( $param['description'] ) )
								$field .= '<p class="description">' . $param['description'] . '</p>';
				
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
							
							$field = '<select class="tmb_get_posts" name="' . $this->prefix .$param['id'] . '[]" id="' . $this->prefix .$param['id'] . '" ' . $multiple . ' data-posttype="' . $param['post_type'] . '" style="max-width:25em;width:95%"';
							
							if( isset( $param['placeholder'] ) )
								$field .= ' data-placeholder="' . $param['placeholder'] . '" ';
							else
								$field .= ' data-placeholder="' . __('Search Posts...', 'truemetabox') . '" ';
							
							$field .= '>'; 
							
							//print_r( $value );
							if( $value ) {
								foreach( $value as $value_id ) {
									$title = get_the_title( $value_id );
									$title = ( mb_strlen( $title ) > 50 ) ? mb_substr( $title, 0, 49 ) . '...' : $title;
									$field .=  '<option value="' . $value_id . '" selected="selected">' . $title . '</option>';
								}
							}
							
							$field .= '</select>';
							if( isset( $param['description'] ) )
								$field .= '<p class="description">' . $param['description'] . '</p>';
						
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
							
							$field = '<select class="tmb_get_terms" name="' . $this->prefix .$param['id'] . '[]" id="' . $this->prefix .$param['id'] . '" ' . $multiple . ' data-taxonomy="' . $param['taxonomy'] . '" style="max-width:25em;width:95%"';
							
							if( isset( $param['placeholder'] ) ) {
								$field .= ' data-placeholder="' . $param['placeholder'] . '" ';
							} else {
								$label = 'Search terms';
								if( $txn = get_taxonomy( $param['taxonomy'] ) ) {
									$label = $txn->labels->search_items;
								}
								$field .= ' data-placeholder="' . $label . '..." ';
							}
							$field .= '>'; 
							
							//print_r( $value );
							if( $value ) {
								foreach( $value as $value_id ) {
									if( $term = get_term( $value_id ) ) {
										$term_name = ( mb_strlen( $term->name ) > 50 ) ? mb_substr( $term->name, 0, 49 ) . '...' : $term->name;
										$field .=  '<option value="' . $value_id . '" selected="selected">' . $term_name . '</option>';
									}
								}
							}
							
							$field .= '</select>';
							if( isset( $param['description'] ) )
								$field .= '<p class="description">' . $param['description'] . '</p>';
							
							break;			
						}
						
						/* <input type=radio> */
						case 'radio':{
							$i = 0;
							$field = '';
							foreach( $param['args'] as $name=>$val ):
								$field .= '<p><label for="' . $this->prefix .$param['id'] . $i . '"><input type="radio" name="' . $this->prefix .$param['id'] . '" id="' . $this->prefix .$param['id'] . $i . '"';
								
								if( $value == $val )
									$field .= ' checked="checked"';
									
								$field .= ' value="' . $val . '">' . $name . '</label></p>';
								$i++;
							endforeach;
							if( isset( $param['description'] ) )
								$field .= '<p class="description">' . $param['description'] . '</p>';
							
							break;						
						}
						
						
						/* image */
						case 'image':{
							$field = tmb_image_uploader_field( $this->prefix . $param['id'], $value );
							if( isset( $param['description'] ) )
								$field .=  '<p class="description">' . $param['description'] . '</p>';
							
							break;
						}
						
						/* file */
						case 'file':{
							$field = '<input name="' . $this->prefix .$param['id'] . '" type="text" id="' . $this->prefix .$param['id'] . '" value="' . esc_attr($value) . '" ';
							if( isset( $param['placeholder'] ) ) 
								$field .= 'placeholder="' . $param['placeholder'] . '" ';
							$field .= 'class="regular-text true-file" /><br />' . __('Paste the file URL above or', 'truemetabox') . ' <a href="javascript:void()" class="true_upload_file_button">' . __('click here to upload', 'truemetabox') . '</a>.<br />';
							if( isset( $param['description'] ) )
								$field .= '<p class="description">' . $param['description'] . '</p>';
							break;
						}
						
						
						/* editor */
						case 'editor':{
							
							
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
							$field = '<div style="max-width:95%" class="misha_editor">' . $editor_contents . '</div>';
							
							if( isset( $param['description'] ) )
								$field .= '<p class="description">' . $param['description'] . '</p>';
							
							break;											
						}
						
						
						
						/* google maps */
						case 'map':{
							$m = $this->prefix . $param['id'];
							$default_lat = (isset($param['default_lat'])) ? $param['default_lat'] : '-25.363';
							$default_lng = (isset($param['default_lng'])) ? $param['default_lng'] : '131.044';
							$lat = $default_lat;
							$lng = $default_lng;
							if( isset( $_GET['tag_ID'] ) ) {
								$lat = ($lat = get_term_meta($term->term_id, $this->prefix . $param['id'] . '_lat' , true)) ? esc_attr( $lat ) : $default_lat;
								$lng = ($lng = get_term_meta($term->term_id, $this->prefix . $param['id'] . '_lng' , true)) ? esc_attr( $lng ) : $default_lng;
							}
							$description = ( isset( $param['description'] ) ) ? $param['description'] : '';
							
							$field = tmb_map( $this->prefix . $param['id'], $lat, $lng, esc_attr( $value ), $description );
							
							break;	
						
						}
						
					endswitch;
					$html .= $wrap1 . $param['label'] . $wrap2 . $field . $wrap3;
				//endif;
				
			endforeach;
			
			
			echo $html; 
		endif;
		
	}
	
	function save( $term_id, $taxonomy_id ){
	
		if( !isset( $_POST[ $this->options['id'].'_wpnonce' ] ))
			return;
		if ( !wp_verify_nonce( $_POST[ $this->options['id'].'_wpnonce' ], $this->options['id'] ) )
			return;
			
			
		foreach ( $this->options['args'] as $param ) {
			if ( !isset($param['capability']) )
				$param['capability'] = 'edit_posts';
			
			if ( current_user_can( $param['capability'] ) ) {
				if ( isset( $_POST[ $this->prefix . $param['id'] ] ) && $_POST[ $this->prefix . $param['id'] ] ) {
					if($param['type'] == 'map') {
						update_term_meta( $term_id, $this->prefix . $param['id'] . '_lat', trim($_POST[ $this->prefix . $param['id'] . '_lat' ]) );
						update_term_meta( $term_id, $this->prefix . $param['id'] . '_lng', trim($_POST[ $this->prefix . $param['id'] . '_lng' ]) );
					}
					update_term_meta( $term_id, $this->prefix . $param['id'], $_POST[ $this->prefix . $param['id'] ] );
				} else {
					delete_term_meta( $term_id, $this->prefix . $param['id'] );
				}
			}
		}
	
    }
}
}
	