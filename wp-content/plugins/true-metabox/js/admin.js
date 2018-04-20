jQuery(function($){

	if (typeof wpColorPicker == 'function')
		$('input.true-color-field').wpColorPicker();
	
	$('body').on('click', '.true_upload_image_button', function(e){
		e.preventDefault();

    	var button = $(this),
    		custom_uploader = wp.media({title: tmb_object.insertImage ,library : {type : 'image'},button: {text: tmb_object.useThisImage}, multiple: false})
			.on('select', function() {
				var attachment = custom_uploader.state().get('selection').first().toJSON();
				console.log( attachment);
				$(button).removeClass('button').html('<img class="true_pre_image" src="' + attachment.url + '" style="max-width:95%;display:block;" />').next().val(attachment.id).next().show();
			}).on('close',function(){
				//alert('closed');
			})
			.open();
	});
	
	$('body').on('click', '.true_remove_image_button', function(){
		var r = confirm( tmb_object.areYouSure );
		if (r == true) {
			$(this).hide().prev().val('').prev().addClass('button').html( tmb_object.uploadImage );
		}
		return false;
	});
	
	
	$('body').on('click', '.true_upload_file_button', function(e){
		e.preventDefault();

    	var button = $(this),
    		custom_uploader = wp.media({title: tmb_object.insertFile,button: {text: tmb_object.useThisFile },multiple: false})
			.on('select', function() {
				var attachment = custom_uploader.state().get('selection').first().toJSON();
				$(button).prev().prev().val(attachment.url);
			})
			.open();
	});
	
	$('.true_v_preview_button').click(function(){
		var pbutton = $(this);
		
		$.ajax({
			type:'POST',
			url:ajaxurl,
			data:'action=truevid&video=' + pbutton.prev().val(),
			beforeSend:function(xhr){
				pbutton.next().css('visibility','visible').parent().find('.true_v_iframe').empty();
			},
			success:function(data){
				pbutton.next().css('visibility','hidden').parent().find('.true_v_iframe').html(data);
				
			}
		});
		return false;
	});
	
	$('.tmbselect2').select2({allowClear:true});
	
	
	
	$('.tmb_get_posts').select2({
  		ajax: {
    	url: ajaxurl,
    	dataType: 'json',
    	delay: 250,
    	allowClear:true,
    	data: function (params) {
      		return {
        		q: params.term, page: params.page, action: 'tmbmishagetposts', post_type: $(this).attr('data-posttype')
      		};
    	},
    	processResults: function( data ) {
			var terms = [];
			if ( data ) {
				console.log(terms);
				$.each( data, function( index, text ) {
					terms.push( { id: text[0], text: text[1]  } );
				});
			}
			return {
				results: terms
			};
		},
		cache: true
		},
		minimumInputLength: 3,
		language: {
		
			inputTooShort: function( args ) {
				var remainingChars = args.minimum - args.input.length;

				if ( 1 === remainingChars ) {
					return tmb_object.inputTooShort1;
				}
				return tmb_object.inputTooShort2.replace( '%qty%', remainingChars );
				//return 'aaaa';
				//return wc_enhanced_select_params.i18n_input_too_short_n.replace( '%qty%', remainingChars );
			},
			
			noResults: function() {
				return tmb_object.noResults;
			},
			
			searching: function() {
				return tmb_object.searching;
			}
			
		}
	});
	
	
	$('.tmb_get_terms').select2({
  		ajax: {
    	url: ajaxurl,
    	dataType: 'json',
    	delay: 250,
    	allowClear: true,
    	data: function (params) {
      		return {
        		q: params.term, page: params.page, action: 'tmbmishagetterms', taxonomy: $(this).attr('data-taxonomy')
      		};
    	},
    	processResults: function( data ) {
			var terms = [];
			if ( data ) {
				console.log(terms);
				$.each( data, function( index, text ) {
					terms.push( { id: text[0], text: text[1]  } );
				});
			}
			return {
				results: terms
			};
		},
		cache: true
		},
		minimumInputLength: 3,
		language: {
		
			inputTooShort: function( args ) {
				var remainingChars = args.minimum - args.input.length;

				if ( 1 === remainingChars ) {
					return tmb_object.inputTooShort1;
				}
				return tmb_object.inputTooShort2.replace( '%qty%', remainingChars );
				//return 'aaaa';
				//return wc_enhanced_select_params.i18n_input_too_short_n.replace( '%qty%', remainingChars );
			},
			
			noResults: function() {
				return tmb_object.noResults;
			},
			
			searching: function() {
				return tmb_object.searching;
			}
			
		}
	});
	
	
});