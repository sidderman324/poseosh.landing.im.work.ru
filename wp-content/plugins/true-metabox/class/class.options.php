<?php

class trueOptionspage {
	/*
	 * @MishaRudrastyh 
	 * хуки
	 */
	function __construct($options) {
		$this->options = $options;
		if( $this->options['slug'] != 'general' )
			add_action( 'admin_menu', array( $this, 'add_page' ) );
		add_action( 'admin_init', array( $this, 'settings_fields') );
		add_action( 'update_option', array( $this, 'map_update'), 30, 1 );
		add_action( 'add_option', array( $this, 'map_update'), 30, 1 );
	}

	/*
	 * @MishaRudrastyh 
	 * функция добавления options page
	 */
	function add_page() {
		add_options_page(
			$this->options['title'],
			$this->options['menuname'],
			$this->options['capability'],
			$this->options['slug'],
			array($this,'page_body')
		);
	}

	/*
	 * @MishaRudrastyh 
	 * функция содержимого страницы
	 */
	function page_body() {
		?>
		<div class="wrap">
		<h2><?php echo $this->options['title'] ?></h2>
		<form method="POST" action="options.php">
			<?php 
				settings_fields( $this->options['slug'] );
				do_settings_sections( $this->options['slug'] );
				submit_button();
			?>
		</form>
		</div>
		<?php
	}
	
	/*
	 * @MishaRudrastyh 
	 * генерируем секции и поля
	 */
	function settings_fields(){
		
		foreach ( $this->options['sections'] as $section ) {
		
			if( $section['id'] != 'default')
				add_settings_section( $section['id'], $section['name'], null, $this->options['slug'] );
			
			foreach( $section['fields'] as $field ) {
				
				$field['name'] = $field['label'];
				
				$field['id'] = $this->options['slug'] . '_' . $field['id'];
				/* проверяем на страндартное значение */
				if( $value = get_option( $field['id'] ) ) {
					$field['value'] = $value;
				} else {
					if( isset( $field['default'] ) ) 
						$field['value'] = $field['default'];
					else
						$field['value'] = '';
				}
				
	
				/*
				 * 3й аргумент - название функции! тут она совпадает с типом поля!
				 */
				add_settings_field( $field['id'],'<label for="' . $field['id'] . '">' . $field['name'] . '</label>',array( $this, $field['type']), $this->options['slug'], $section['id'], $field );
				register_setting( $this->options['slug'], $field['id'], array( $this, 'sanitize_cb') );
			}
			
		}
		
		/*add_settings_field(
	'myprefix_setting-id',
	'This is the setting title',
	'myprefix_setting_callback_function',
	'general',
	'default',
	array( 'label_for' => 'myprefix_setting-id' )
);*/
		
	}
	
 
 	
 	function text( $field = array() ) {
 		echo '<input name="' . $field['id'] . '" id="' . $field['id'] . '" type="text" class="regular-text" value="' . esc_attr( $field['value'] ) . '" />';
 		if( isset( $field['description'] ) )
			echo '<p class="description">' . $field['description'] . '</p>';
 	}
 	
 	function color( $field = array() ) {
 		echo '<input name="' . $field['id'] . '" id="' . $field['id'] . '" type="text" class="true-color-field" value="' . esc_attr( $field['value'] ) . '" />';
 		if( isset( $field['description'] ) )
			echo '<p class="description">' . $field['description'] . '</p>';
 	}
 	
 	function textarea( $field = array() ) {
 		echo '<textarea name="' . $field['id'] . '" id="' . $field['id'] . '" class="large-text" rows="10">' . esc_html( $field['value'] ) . '</textarea>';
 		if( isset( $field['description'] ) )
			echo '<p class="description">' . $field['description'] . '</p>';
 	}
 	
 	function checkbox( $field = array() ) {
 		echo '<label for="' . $field['id'] . '"><input name="' . $field['id'] . '" id="' . $field['id'] . '" type="checkbox" value="1" class="code" ' . checked( 1, $field['value'], false ) . ' /> ' . $field['label'] . '</label>';
 		if( isset( $field['description'] ) )
			echo '<p class="description">' . $field['description'] . '</p>';
 	}
 	
 	function select( $field = array() ) {
 		$select = '<select name="' . $field['id'] . '" id="' . $field['id'] . '">';
 		foreach( $field['args'] as $name=>$val ):
			$select .= '<option value="' . $val . '"';
			if( $field['value'] == $val )
				$select .= ' selected="selected"';
			$select .= '>' . $name . '</option>';
		endforeach;
		$select .= '</select>';
 		if( isset( $field['description'] ) )
			$select .= '<p class="description">' . $field['description'] . '</p>';
		echo $select;
 	}
 	
 	/* <select multiple> */
 	function multiselect( $field = array() ) {
 		$select = '<select name="' . $field['id'] . '[]" id="' . $field['id'] . '" multiple="multiple" class="tmbselect2" style="max-width:25em;width:99%"';
 		
 		if( isset( $field['placeholder'] ) )
			$select .= ' data-placeholder="' . $field['placeholder'] . '" ';
		else
			$select .= ' data-placeholder="' . __('Select some options', 'truemetabox') . '" ';

 		$select .= '>';
 		
 		foreach( $field['args'] as $name=>$val ):
			$select .= '<option value="' . $val . '"';
			if( is_array( $field['value'] ) && in_array($val,$field['value']) )
				$select .= ' selected="selected"';
			$select .= '>' . $name . '</option>';
		endforeach;
		
		$select .= '</select>';
 		if( isset( $field['description'] ) )
			$select .= '<p class="description">' . $field['description'] . '</p>';
		echo $select;
	}
	
	/* get posts */
 	function getposts( $field = array() ) {
 		$multiple = ( isset( $field['multiple'] ) && $field['multiple'] == true ) ? 'multiple="multiple"' : '';
 		if( isset( $field['post_type'] ) && $field['post_type'] ) {
			if( is_array( $field['post_type'] ) ) $field['post_type'] = join(',',$field['post_type']);
			$field['post_type'] = $field['post_type'];
		} else {
			$field['post_type'] = 'post';
		}
							
 		$select = '<select class="tmb_get_posts" name="' . $field['id'] . '[]" id="' . $field['id'] . '" ' . $multiple . ' data-posttype="' . $field['post_type'] . '" style="max-width:25em;width:99%"';
 		if( isset( $field['placeholder'] ) )
			$select .= ' data-placeholder="' . $field['placeholder'] . '" ';
		else
			$select .= ' data-placeholder="' . __('Search Posts...', 'truemetabox') . '" ';

 		$select .= '>';
		
		if( $field['value'] ) {
			foreach( $field['value'] as $value_id ) {
				$title = get_the_title( $value_id );
				$title = ( mb_strlen( $title ) > 50 ) ? mb_substr( $title, 0, 49 ) . '...' : $title;
				$select .=  '<option value="' . $value_id . '" selected="selected">' . $title . '</option>';
			}
		}
		
		$select .= '</select>';
 		if( isset( $field['description'] ) )
			$select .= '<p class="description">' . $field['description'] . '</p>';
		echo $select;
	}
	
	
	/* get terms */
 	function getterms( $field = array() ) {
 		$multiple = ( isset( $field['multiple'] ) && $field['multiple'] == true ) ? 'multiple="multiple"' : '';
 	
			
		// what taxonomy to get
		if( isset( $field['taxonomy'] ) && $field['taxonomy'] ) {
			if( is_array( $field['taxonomy'] ) )
				$field['taxonomy'] = join(',',$field['taxonomy']);
		} else {
			$field['taxonomy'] = 'category';
		}
							
 		$select = '<select class="tmb_get_terms" name="' . $field['id'] . '[]" id="' . $field['id'] . '" ' . $multiple . ' data-taxonomy="' . $field['taxonomy'] . '" style="max-width:25em;width:99%"';
 		if( isset( $field['placeholder'] ) ) {
			$select .= ' data-placeholder="' . $field['placeholder'] . '" ';
		} else {
			$label = 'Search terms';
			if( $txn = get_taxonomy( $field['taxonomy'] ) ) {
				$label = $txn->labels->search_items;
			}
			$select .= ' data-placeholder="' . $label . '..." ';
		}
		
 		$select .= '>';
		
		if( $field['value'] ) {
			
			foreach( $field['value'] as $value_id ) {
				
				if( $term = get_term( $value_id ) ) {
					$term_name = ( mb_strlen( $term->name ) > 50 ) ? mb_substr( $term->name, 0, 49 ) . '...' : $term->name;
					$select .=  '<option value="' . $value_id . '" selected="selected">' . $term_name . '</option>';
				}
			}
		}
		
		$select .= '</select>';
 		if( isset( $field['description'] ) )
			$select .= '<p class="description">' . $field['description'] . '</p>';
		echo $select;
	}
	
	
	
	
	
 	
 	function radio( $field = array() ) {
 		$radio = '<fieldset>'; $i=1;
 		foreach( $field['args'] as $name=>$val ):
			$radio .= '<label for="' . $field['id'] . $i . '"><input type="radio" name="' . $field['id'] . '" id="' . $field['id'] . $i . '"';
			if( $field['value'] == $val )
				$radio .= ' checked="checked"';
			$radio .= ' value="' . $val . '">' . $name . '</label><br />';
			$i++;
		endforeach;
		if( isset( $field['description'] ) )
			$radio .= '<p class="description">' . $field['description'] . '</p>';
		echo $radio . '</fieldset>';
 	
 	}
 	
 	function image( $field = array() ) {
 		$image = tmb_image_uploader_field( $field['id'], $field['value'] );
		if( isset( $field['description'] ) )
			$image .= '<p class="description">' . $field['description'] . '</p>';
		echo $image;
 	}
 	
 	function file( $field = array() ) {
 		$file = '<input name="' . $field['id'] . '" type="text" id="' . $field['id'] . '" value="' . esc_attr( $field['value'] ) . '" ';
		if( isset( $field['placeholder'] ) ) 
			$file .= 'placeholder="' . $field['placeholder'] . '" ';
		$file .= 'class="regular-text true-file" /><br />' . __('Paste the file URL above or', 'truemetabox') . ' <a href="javascript:void()" class="true_upload_file_button">' . __('click here to upload', 'truemetabox') . '</a>.';
		if( isset( $field['description'] ) )
			$file .= '<p class="description">' . $field['description'] . '</p>';
		echo $file;
 	
 	}
 	
 	function editor( $field = array() ) {
 		$editor_args = array(
			'textarea_rows'=>7,
			'teeny'=>true
		);
		if( isset( $field['editor_args'] ) )
			$editor_args = wp_parse_args( $field['editor_args'], $editor_args );
			
		wp_editor( $field['value'], $field['id'], $editor_args);

		if( isset( $field['description'] ) )
			echo '<p class="description">' . $field['description'] . '</p>';
	}
	
	function video( $field = array() ) {
		$output = '<input name="' . $field['id'] . '" type="text" id="' . $field['id'] . '" value="' . esc_attr( $field['value'] ) . '" ';
		if( isset( $field['placeholder'] ) ) 
			$output .= 'placeholder="' . $field['placeholder'] . '" ';
		$output .= 'class="regular-text true-video-preview" /><button type="submit" class="true_v_preview_button button">' . __('Preview', 'truemetabox') . '</button><span class="spinner" style="float:none;vertical-align:top"></span><br />';
		if( isset( $field['description'] ) )
			$output .= '<p class="description">' . $field['description'] . '</p>';
		$output .= '<div class="true_v_iframe">' . tmb_youtube_vimeo_iframe( $field['value'] ) . '</div>';
		echo $output;
	}
	
	function map( $field = array() ) {
	
		$description = ( isset( $field['description'] ) ) ? $field['description'] : '';
		$default_lat = (isset($field['default_lat'])) ? $field['default_lat'] : '-25.363';
		$default_lng = (isset($field['default_lng'])) ? $field['default_lng'] : '131.044';
		$lat = ($lat = get_option( $field['id'] . '_lat' )) ? esc_attr( $lat ) : $default_lat;
		$lng = ($lng = get_option( $field['id'] . '_lng' )) ? esc_attr( $lng ) : $default_lng;
		echo '<div class="misha-field">' . tmb_map( $field['id'], $lat, $lng, esc_attr( $field['value'] ), $description ) . '</div>';
							
	
	}
	
	
	function map_update( $option_id  ) {
		if( isset( $_POST[ $option_id . '_lat'] ) && isset( $_POST[ $option_id . '_lng'] ) ) {
			update_option( $option_id . '_lat', $_POST[ $option_id . '_lat'] );
			update_option( $option_id . '_lng', $_POST[ $option_id . '_lng'] );
		}
	}
	
	function sanitize_cb( $input ){
		
		//print_r($input);
		$output = $input;
		if( !is_array( $input ) )
			$output = stripslashes( $input );
    	return $output;
	}

}


