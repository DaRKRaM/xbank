(function($){
	$(function() {
		acf.data = feadata;
	});
	$.extend({
		updateNumberTo: function($number,$append,$obj) {
			var original = $obj.text().replace($append,'');
		
			var spin = function() {
				return Math.floor(Math.random() * 10);
			};
		
			var spinning = setInterval(function() {
				$obj.text(function() {
					var result = '';
					for (var i = 0; i < original.length; i++) {
						result += spin().toString();
					}
					return result;
				});
			}, 50);
		
			setTimeout(function() {
				clearInterval(spinning);
				$obj.text($number+$append);
			}, 100);
		},
		redirectPost: function(location, args)
		{
			var form = $('<form></form>');
			form.attr("method", "post");
			form.attr("action", location);
	
			$.each( args, function( key, value ) {
				var field = $('<input></input>');
	
				field.attr("type", "hidden");
				field.attr("name", key);
				field.attr("value", value);
	
				form.append(field);
			});
			$(form).appendTo('body').submit();
		}
	});

	$(window).scroll(function() {
		var $loadMores = $('.load-more-results');
		var $scroll = $(this);

		$.each( $loadMores, function(){
			var $loadMore = $(this);
			var hT = $loadMore.offset().top,
			hH = $loadMore.outerHeight(),
			wH = $(window).height(),
			wS = $scroll.scrollTop();
			if (wS > (hT+hH-wH) && (hT > wS) && (wS+wH > hT+hH)){			
				if( $loadMore.hasClass('loading') ) return;
				$loadMore.addClass('loading').find('span').removeClass('acf-hidden');
				
				var nextPage = parseInt( $loadMore.attr('data-page')) + 1;
				var items = parseInt( $loadMore.attr('data-count'));
				var total = parseInt( $loadMore.attr('data-total'));
				var ajaxData = {
					action:		'frontend_admin/forms/get_submissions',
					item_count: items,
					current_page: nextPage,
					form_id:	$loadMore.data('form'),
					load_more: 1,
				};
				// get HTML
				$.ajax({
					url: acf.get('ajaxurl'),
					data: acf.prepareForAjax(ajaxData),
					type: 'post',
					dataType: 'html',
					cache: false,
					success: function(response){
						$loadMore.before(response);
						$loadMore.attr('data-page',nextPage).attr('data-count',$loadMore.siblings('.fea-list-item').length);
						$loadMore.removeClass('loading').find('span').addClass('acf-hidden');

						if( nextPage == total ) $loadMore.remove();

					}
				});
			}
		});
		
	 });


	$("body").on("click", ".change-step", function (a) {
        changeFormStep( $(this).parents('form'), $(this).data("step") );
    });

	function changeFormStep( form, step ){
		var currentStep = form.find('input[name=_acf_step]');

        if(step==currentStep.val()) return false;

        var currentFields = form.find('.acf-field[data-step='+currentStep.val()+']');
        var stepFields = form.find('.acf-field[data-step='+step+']');
        if( stepFields ){
            form.find('.form-tab[data-step='+step+']').addClass('active');
            form.find('.form-tab[data-step='+currentStep.val()+']').removeClass('active');
            currentStep.val(step);
            form.find('.step-count').find('.current-step').text(step);
            currentFields.removeAttr('data-current').addClass('frontend-admin-hidden');
            stepFields.attr('data-current','1').removeClass('frontend-admin-hidden');
            $('body, html').animate({scrollTop:form.offset().top-100}, 'slow');

			form.find('.acf-loading').addClass('acf-hidden');
			form.find('.disabled').removeClass('disabled');
			return true;
        }
		
		return false;
	}

	/* $('body').on('input', 'input.image-preview', function(e){
	var reader = new FileReader();
	var file_input = $(this);
	reader.onload = function()
	{
	file_input.parents('.hide-if-value').addClass('frontend-admin-hidden').siblings('.show-if-value').addClass('show').find('img').attr('src',reader.result);
	}
	imagePreview = true;
	reader.readAsDataURL(e.target.files[0]);
	}); */

	$('body').on('click','a[data-name=remove]',function(e){
		if( typeof imagePreview != undefined ){
			$(this).parents('.show-if-value').removeClass('show').siblings('.hide-if-value').removeClass('frontend-admin-hidden').find('input.image-preview').val('');
		}
	});   

	$('body').on('click','.fea-new-form-window',function(e){
		e.preventDefault();	
		var clicked = $(this);			
		acf.getForm( clicked, 'admin_form' );
	});
	
	$(document).on('elementor/popup/show',(event, id, instance)=>{
		acf.doAction('append',$('#elementor-popup-modal-' + id))
	});
		
	$("body").on('input','.pa-custom-name input',function(a){
		$(this).closest('.acf-fields').siblings('.acf-frontend-blocks-layout-handle').find('.attr_name').text($(this).val());
	});

	acf.addFilter('relationship_ajax_data', function(ajaxData, $el){
		if( $el.$control().data('product_id') != '' ){
			ajaxData.product_id = $el.$control().data('product_id');
		}
		return ajaxData;
	});

	$("body").on('change','.posts-select',function(a){
		var $select =$(this); 
		var $form =$select.parents('.frontend-form-posts').siblings('form');
		$select.parents('.frontend-form-posts').append('<span class="acf-loading"></span>');
		var widget = $form.data('widget');
		var formData = $form.find('input[name=_acf_form]').val();

		var ajaxData = {
			action:		'frontend_admin/forms/change_form',
			draft: 		$(this).val(),
			form_data:	formData,
		};
		// get HTML
		$.ajax({
			url: acf.get('ajaxurl'),
			data: acf.prepareForAjax(ajaxData),
			type: 'post',
			dataType: 'json',
			cache: false,
			success: function(response){
				if(response.data.reload_form){
					$('body, html').animate({scrollTop:$form.offset().top}, 'slow');
					$form.siblings('.frontend-form-posts').remove();
					$form.replaceWith(response.data.reload_form);
					acf.doAction('append',$('.elementor-element-'+widget))
					$('.acf-loading').addClass('acf-hidden');
				}
			}
		});
	});
	$("body").on('click','span.close-msg',function(a){
		$(this).parents('.frontend-admin-message').remove();
	});
	
	$("body").on('input click',function(a){
		$('.acf-success-message').remove();
	});

	$('body').on('mouseenter','.choices a.edit-rel-post', function(event){
		var item = $(this).parents('.acf-rel-item');
		if( ! item.hasClass('disabled') ){
			item.addClass('disabled temporary');
		}
	});
	$('body').on('mouseleave','.choices a.edit-rel-post', function(event){
		var item = $(this).parents('.acf-rel-item');
		if( item.hasClass('temporary') ){
			item.removeClass('disabled temporary');
		}
	});


	$('body').on('click','.render-form', function(e){
		e.preventDefault();	
		var clicked = $(this);			
		acf.getForm( clicked );
	});

	
	$('.post-slug-field input').on('input keyup', function() {
		var c = this.selectionStart,
			r = /[`~!@#$%^&*()|+=?;:..’“'"<>,€£¥•،٫؟»«\s\{\}\[\]\\\/]+/gi,
			v = $(this).val();
		$(this).val(v.replace(r,'').toLowerCase());
		this.setSelectionRange(c, c);
	  }); 
	
	$('body').on('click', 'button.edit-password', function(){
		$(this).addClass('acf-hidden').parents('.acf-field-user-password').removeClass('edit_password').addClass('editing_password').siblings('.acf-field-user-password-confirm').removeClass('acf-hidden');
		$(this).after('<input type="hidden" name="edit_user_password" value="1"/>');
		$(this).siblings('.pass-strength-result').removeClass('acf-hidden')
	});
	
	$('body').on('click', 'button.cancel-edit', function(){
		$(this).siblings('button.edit-password').removeClass('acf-hidden').parents('.acf-field-user-password').addClass('edit_password').removeClass('editing_password').siblings('.acf-field-user-password-confirm').addClass('acf-hidden');
		$(this).parents('acf-input-wrap').siblings('acf-notice');
		$(this).siblings('input[name=edit_user_password]').remove();
		$(this).siblings('.pass-strength-result').addClass('acf-hidden');
	});

	$(function(){
		var forceEdit = $('.acf-field-user-password-confirm').siblings('.acf-field-user-password').hasClass('edit_password');

		if( ! forceEdit ){
			$('.acf-field-user-password-confirm').removeClass( 'acf-hidden' );
		}
	});

	var modalLevel = 1;
	var narrowfy = 0;
	var $controls = [];
	
	acf.showModal = function( $el, $width ){
		$width = $width || 600;
		var $key;
		if( $el.data('modal_id') ){
			$key = $el.data('modal_id');
		}else{		
			$key = acf.uniqid()+'-'+modalLevel;
		}
		var margin = 9+modalLevel;
		currentModal = $('#modal_'+$key);
		if(currentModal.length){
			currentModal.show();
			return false;
		}else{
			currentModal = $('<div id="modal_' + $key + '" class="fea-modal edit-modal" data-clear="1"><div class="fea-modal-content" style="margin:' + margin + '% auto;width:' + parseInt($width) + 'px;max-width:80%"><div class="fea-modal-inner"><span class="acf-icon -cancel close-modal"></span><div class="content-container"><div class="loading"><span class="acf-loading"></span></div></div></div></div></div>');
			$('body').append(currentModal);
			currentModal.show();
			$el.attr('data-modal_id',$key);
			return true;
		}
		
	}

	$('body').on('click','.fea-modal .close-modal', function(e){
		var modal = $(this).parents('.fea-modal');
		if(modal.data('clear')==1){
			modal.hide();
			modalLevel--;
			narrowfy-=20;
		}
	});
	
	acf.getForm = function( $clicked, $type ){			
		$type = $type || $clicked.data('name');

		if($type=='admin_form'){
			var $el = $clicked;
			var $form_action = 'admin_form';
		}else{
			var $el = $clicked.parents('.acf-field');	
			if($type=='edit_item'){
				var $form_action = $clicked.parents('.acf-rel-item').data('id');
			}else{
				var $form_action = 'add_item';
			}
		}
		var request = acf.showModal($clicked,$el.data('form_width')-narrowfy);
		$controls[modalLevel] = $clicked.parents('.acf-actions').siblings('.acf-relationship');
		modalLevel++;
		narrowfy+=20;
		
		if( request ){
			if( $el.data('type') == 'related_terms' ){
				$dataType = 'term';
			}else{
				$dataType = 'post';
			}

			var ajaxData = {
				action:		'frontend_admin/forms/add_form',
				field_key:	$el.data('key'),
				data_type: $dataType,
				parent_form: $el.parents('form').attr('id'),
				form_action: $form_action,
			};

			var formData = $clicked.siblings('.form-data');
			if(formData.length > 0){
				ajaxData.form = formData.val();
			}
			// get HTML
			$.ajax({
				url: acf.get('ajaxurl'),
				data: acf.prepareForAjax(ajaxData),
				type: 'post',
				dataType: 'html',
				success: acfFrontendShowForm
			});
		}
	}
	
	function acfFrontendShowForm( html ){			
		// update popup
		currentModal.find('.content-container').html(html);  
		acf.doAction('append',currentModal);  
	};


	$('body').on('click', '.fea-delete-button', function(e){
		var button = $(this);

		if( button.hasClass('disabled') ) return;

		button.addClass('disabled')

		var tooltip = acf.newTooltip({
			confirm: true,
        	text: button.data('confirm'),
			target: button,
			context: button.parents('acf-field'),
			confirm: function () {
				deleteObject(button);
			},
			cancel: function () {
				button.removeClass('disabled');
			}
		});
		
	});

	function deleteObject(button) {
		$(window).off('beforeunload');
		button.after('<span class="acf-loading"></span>')
		$form = button.parents('form');
		$form.find('button.fea-submit-button').addClass('disabled');

		var formData = new FormData($form[0]);          
		formData.append('action','frontend_admin/delete_object');
		formData.append('field',button.parents('.acf-field').data('key'));
		formData.append('delete_object',button.data('object'));
		$.ajax({
			url: acf.get('ajaxurl'),
			type: 'post',
			data: formData,
			cache: false,
			processData: false,
			contentType: false,
			success: function(response){
				if(response.success) {	   				  
					if( response.data.redirect ){
						if(response.data.delete_message){
							$.redirectPost(response.data.redirect, response.data);
						}else{
							window.location=response.data.redirect;
						} 		
					}
				}
			}
		});
	}

	$('body').on('click','.frontend-form button.fea-submit-button',function(e){
			e.preventDefault();
		$form=$(this).parents('form');
		$form.find('input[name=_acf_status]').val($(this).data('state'));
		if( $(this).hasClass('disabled')){
			return;
		}

		var spinner = $form.find('.acf-loading');
		if( spinner.length > 0 ){
			spinner.removeClass('acf-hidden')
		}else{
			spinner = $('<span class="acf-loading"></span>');
			$form.find('button.fea-submit-button').after(spinner);
		}

		$(this).addClass('disabled');
		$form = acf.applyFilters('frontend_admin/submit_form',$form);
		if( typeof $form == 'undefined' || ! $form ){
			spinner.removeClass('acf-hidden')
			$(this).removeClass('disabled');
			return;
		}

		stepCount = Number( $form.data('steps') );
		validateSteps = $form.data('validate-steps');
		currentStep = Number( $form.find('input[name=_acf_step]').val() );
		if( stepCount && ! validateSteps ){
			if( currentStep < stepCount ){
				currentStep++;
				if(changeFormStep($form,currentStep)){
					return;
				}
			}
		}
		
		args = {
			form: $form,
			reset: false,
			complete: acffSubmitForm,
			success: function(){
				return;
			},
		}
		acf.validateForm(args);
		
	});

	function acffSubmitForm( $form, $validator ){
		// check errors
		if( $validator.hasErrors() ) {
			var first = $validator.data.errors[0];
			var field = $form.find('input[name="'+first.input+'"]').closest('.acf-field');
			if( typeof stepCount != 'undefined' && field.data('step') < stepCount ){
				changeFormStep($form,field.data('step'));
			}

			$('.acf-loading').addClass('acf-hidden');
			$form.find('button.fea-submit-button').removeClass('disabled');
			var formModal = $form.closest('div.edit-modal');
			if( typeof formModal != 'undefined' ){
				$(formModal).animate({scrollTop:$form.offset().top-50}, 'slow');
			}

			return;
		}else{
			if( currentStep < stepCount ){
				currentStep++;
				if(changeFormStep($form,currentStep)){
					$validator.reset();
					return;
				}
			}
		}
		
		let $fileInputs = $('input[type="file"]:not([disabled])', $form)
		$fileInputs.each(function (i, input) {
			if (input.files.length > 0) {
				return;
			}
			$(input).prop('disabled', true);
		});

		var formData = new FormData($form[0]);       
		
		formData.append('action','frontend_admin/form_submit');

		// Re-enable empty file $fileInputs
		$fileInputs.prop('disabled', false);

		acf.lockForm($form);

		$.ajax({
			url: acf.get('ajaxurl'),
			type: 'post',
			data: formData,
			cache: false,
			processData: false,
			contentType: false,
			success: function(response){
				if(response.success) {	   
					var data = response.data;				  
					if( data.redirect ){
						window.location=data.redirect;							
					}else{
						acf.unlockForm($form);	
						successMessage='<div class="frontend-admin-message"><div class="acf-notice -success acf-success-message"><p class="success-msg">'+data.success_message+'</p><span class="frontend-admin-dismiss close-msg acf-notice-dismiss acf-icon -cancel small"></span></div></div>';
						if(data.append){
						//modal window ajax form
							modalFormSuccess( response );	
							$form.parents('.fea-modal').remove();         
						}else{
							if(data.submission){
								acf.updateSubmission(data);
							}
							if(data.reload_form){
								var $newForm = $(data.reload_form);
								$form.replaceWith($newForm);
								acf.doAction('append',$newForm);
								if(data.saved_message){
									$newForm.find('.save-progress-button').after('<p class="draft-saved">'+data.saved_message+'</p>');
									setTimeout(function(){$('body').find('.draft-saved').remove();}, 3000);
								}
								if(data.success_message){
									$newForm.prepend(successMessage);
								}
								
								var formModal = $newForm.closest('div.edit-modal');
								if( typeof formModal != 'undefined' ){
									formModal.scrollTop(0);
									if( data.close_modal ){
										formModal.hide();
									}									
								}else{
									$('body, html').animate({scrollTop:$newForm.offset().top-50}, 'slow');
								}
							}
							$form.find('.fea-submit-button').attr('disabled',false).removeClass('acf-hidden disabled');
							$('.acf-loading').addClass('acf-hidden');

						}						
					}
				}else{
					var errorMessage = $form.find('.acf-form-data').data('error');

					if( response.data ) errorMessage = response.data;

					$form.before('<div style="margin-top:10px" class="frontend-admin-message"><div class="acf-notice -error acf-error-message"><p class="error-msg">'+errorMessage+'</p><span class="frontend-admin-dismiss close-msg acf-notice-dismiss acf-icon -cancel small"></span></div></div>');
					
					$('body, html').animate({scrollTop:$form.offset().top-50}, 'slow');
					var formModal = $form.closest('div.edit-modal');
					if( typeof formModal != 'undefined' ){
						$(formModal).animate({scrollTop:$form.offset().top-50}, 'slow');
					}		
				}					
			}, 
		});  
	}

	acf.updateSubmission = function(data){
		var $submission = $('.fea-list-item[data-id='+data.submission+']');
		$submission.find('.item-title').html(data.submission_title);
		$submission.prepend(successMessage);
	}

	function modalFormSuccess( response ){
		
		var postData = response.data.append; 
		modalLevel--;
		narrowfy-=20;        

		if(postData.action == 'edit'){
			$('.acf-field div.values').find('span[data-id='+postData.id+']').html(postData.text+'<a href="#" class="acf-icon -minus small dark" data-name="remove_item"></a>');
			$('.acf-field div.choices').find('span[data-id='+postData.id+']').html(postData.text);
		}else{			
			var thisField = $controls[modalLevel];	
			thisField.find('div.values ul').append('<li><input type="hidden" name="' + thisField.find('div.selection').siblings('input').attr('name') + '[]" value="' + postData.id + '" /><span data-id="' + postData.id + '" class="acf-rel-item">' + postData.text + '<a href="#" class="acf-icon -minus small dark" data-name="remove_item"></a></span></li>');

			thisField.find('div.choices ul').prepend('<li><span class="acf-rel-item disabled" data-id="' + postData.id + '">' + postData.text + '<a href="#" class="acf-icon -pencil small dark edit-rel-post" data-name="edit_item"></a></span></li>');
			
		}       
	}


	var Field = acf.Field.extend({
		
		type: 'upload_files',
		
		events: {
			'click .acf-gallery-add':			'onClickAdd',
			'click div.acf-gallery-upload':		'onClickUpload',
			'click .acf-gallery-edit':			'onClickEdit',
			'click .acf-gallery-remove':		'onClickRemove',
			'click .acf-gallery-attachment': 	'onClickSelect',
			'click .acf-gallery-close': 		'onClickClose',
			'change .acf-gallery-sort': 		'onChangeSort',
			'click .acf-gallery-update': 		'onUpdate',
			'mouseover': 						'onHover',
			'showField': 						'render',
			'input .images-preview': 			'imagesPreview',
		},
		
		actions: {
			'validation_begin': 	'onValidationBegin',
			'validation_failure': 	'onValidationFailure',
			'resize':				'onResize'
		},
		
		onValidationBegin: function(){
			acf.disable( this.$sideData(), this.cid );
		},
		
		onValidationFailure: function(){
			acf.enable( this.$sideData(), this.cid );
		},
		
		$control: function(){
			return this.$('.acf-gallery');
		},
		
		$collection: function(){
			return this.$('.acf-gallery-attachments');
		},
		
		$attachments: function(){
			return this.$('.acf-gallery-attachment:not(.not-valid)');
		},

		$clone: function(){
			return this.$('.image-preview-clone');
		},
		
		$attachment: function( id ){
			return this.$('.acf-gallery-attachment[data-id="' + id + '"]');
		},
		
		$active: function(){
			return this.$('.acf-gallery-attachment.active');
		},

		$inValid: function(){
			return this.$('.acf-gallery-attachment.not-valid');
		},
		
		$main: function(){
			return this.$('.acf-gallery-main');
		},
		
		$side: function(){
			return this.$('.acf-gallery-side');
		},
		
		$sideData: function(){
			return this.$('.acf-gallery-side-data');
		},
		
		isFull: function(){
			var max = parseInt( this.get('max') );
			var count = this.$attachments().length;
			return ( max && count >= max );
		},
		
		getValue: function(){
			
			// vars
			var val = [];
			
			// loop
			this.$attachments().each(function(){
				val.push( $(this).data('id') );
			});
			
			// return
			return val.length ? val : false;
		},
		
		addUnscopedEvents: function( self ){
			
			// invalidField
			this.on('change', '.acf-gallery-side', function(e){
				self.onUpdate( e, $(this) );
			});
		},
		
		addSortable: function( self ){
			
			// add sortable
			this.$collection().sortable({
				items: '.acf-gallery-attachment',
				forceHelperSize: true,
				forcePlaceholderSize: true,
				scroll: true,
				start: function (event, ui) {
					ui.placeholder.html( ui.item.html() );
					ui.placeholder.removeAttr('style');
	   			},
	   			update: function(event, ui) {
					self.$input().trigger('change');
		   		}
			});
			
			// resizable
			this.$control().resizable({
				handles: 's',
				minHeight: 200,
				stop: function(event, ui){
					acf.update_user_setting('gallery_height', ui.size.height);
				}
			});
		},
		
		initialize: function(){

			// add unscoped events
			this.addUnscopedEvents( this );
			
			// render
			this.render();
		},
		
		render: function(){
			
			// vars
			var $sort = this.$('.acf-gallery-sort');
			var $add = this.$('.acf-gallery-add');
			var count = this.$attachments().length;
			
			// disable add
			if( this.isFull() ) {
				$add.addClass('disabled');
			} else {
				$add.removeClass('disabled');
			}
			
			// disable select
			if( !count ) {
				$sort.addClass('disabled');
			} else {
				$sort.removeClass('disabled');
			}
			
			// resize
			this.resize();
		},
		
		resize: function(){
			
			// vars
			var width = this.$control().width();
			var target = 150;
			var columns = Math.round( width / target );
						
			// max columns = 8
			columns = Math.min(columns, 8);
			
			// update data
			this.$control().attr('data-columns', columns);
		},
		
		onResize: function(){
			this.resize();
		},
		
		openSidebar: function(){
			
			// add class
			this.$control().addClass('-open');
			
			// hide bulk actions
			// should be done with CSS
			//this.$main().find('.acf-gallery-sort').hide();
			
			// vars
			var width = this.$control().width() / 3;
			width = parseInt( width );
			width = Math.max( width, 350 );
			
			// animate
			this.$('.acf-gallery-side-inner').css({ 'width' : width-1 });
			this.$side().animate({ 'width' : width-1 }, 250);
			this.$main().animate({ 'right' : width }, 250);
		},
		
		closeSidebar: function(){
			
			// remove class
			this.$control().removeClass('-open');
			
			// clear selection
			this.$active().removeClass('active');
			
			// disable sidebar
			acf.disable( this.$side() );
			
			// animate
			var $sideData = this.$('.acf-gallery-side-data');
			this.$main().animate({ right: 0 }, 250);
			this.$side().animate({ width: 0 }, 250, function(){
				$sideData.html('');
			});
		},
		
		onClickAdd: function( e, $el ){

			this.$control().css('height','400');

			// validate
			if( this.isFull() ) {
				this.showNotice({
					text: acf.__('Maximum selection reached'),
					type: 'warning'
				});
				return;
			}
			
			// new frame
			var frame = acf.newMediaPopup({
				mode:			'select',
				title:			acf.__('Add Image to Gallery'),
				field:			this.get('key'),
				multiple:		'add',
				library:		this.get('library'),
				allowedTypes:	this.get('mime_types'),
				selected:		this.val(),
				select:			$.proxy(function( attachment, i ) {
					this.appendAttachment( attachment, i );
				}, this)
			});
		},
		imagesPreview: function( e, $el ){
			this.$control().css('height','400');
			var form = this.$control().parents('form');
			form.find('button.fea-submit-button').addClass('disabled');	
			var numAttachments = this.$attachments().length;
			var maxNum = this.$control().data('max');
	
			const files = e.currentTarget.files;
			Object.keys(files).forEach(i=>{
				if(maxNum>0 && numAttachments>=maxNum){
					return false;
				}
				const file = files[i];
				const reader = new FileReader();
				reader.onload = (e) => {
					var container = this.$clone().clone();
					container.removeClass('acf-hidden image-preview-clone').addClass('acf-gallery-attachment acf-uploading').find('img').attr('src',reader.result);
					container.appendTo(this.$collection());
					if(file.type == 'application/pdf'){
						container.find('.margin').append('<span class="gi-file-name">'+file.name+'</span>');					}
					this.uploadImage(file,container,form);
				}
				numAttachments++;
				reader.readAsDataURL(e.target.files[i]);

			});
			if(numAttachments>=maxNum && maxNum>0){
				this.$('input.images-preview').prop('disabled',true);
			}
		},

		uploadImage: function(file,container,form){
			var self = this;
			var progPrc = container.find('.uploads-progress .percent');
			var progBar = container.find('.uploads-progress .bar');
			$.updateNumberTo('33','%',progPrc);
			progBar.animate({'width': '33%'}, 'slow');
			
			var nonce = this.$el.parents('form').find('input[name=_acf_nonce]').val();
			var fieldKey = this.get('key');
			var fileData = new FormData();
			fileData.append('action','acf/fields/upload_files/add_attachment');
			fileData.append('file',file);
			fileData.append('field_key',fieldKey);
			fileData.append('nonce',nonce);
			form.find('button.fea-submit-button').addClass('disabled');

			$.ajax({
				url: acf.get('ajaxurl'),
				data: acf.prepareForAjax(fileData),
				type: 'post',
				processData: false, 
				contentType: false, 
				cache: false
			}).done(function(response){
				form.find('button.fea-submit-button').removeClass('disabled');
				if(response.success){
					$.updateNumberTo('100','%',progPrc);
					progBar.animate({'width': '100%'}, 'slow');
					if(response.data.src){
						container.find('img').attr('src',response.data.src);
					}
					container.attr('data-id',response.data.id).find('.acf-gallery-remove').attr('data-id',response.data.id);
					var idInput = $('<input>').attr({
						type:"hidden",
						name:self.$input().attr('name')+"[]",
						value:response.data.id
					});
					container.prepend(idInput).removeClass('acf-uploading');
					setTimeout(function(){
						container.find('.uploads-progress').remove();
					  }, 100);
				}else{
					container.find('.uploads-progress').remove();
					container.addClass('not-valid').append('<p class="errors">'+response.data+'</p>').find('.margin').append('<p class="upload-fail">x</p>');
				}
			  } 
			);
		},

		onClickUpload: function( e, $el ){
						
			// validate
			if( this.isFull() ) {
				this.showNotice({
					text: acf.__('Maximum selection reached: '+this.$control().data('max')),
					type: 'warning'
				});
				return;
			}
			if(this.$inValid()){
				this.$inValid().remove();
				this.$('input.images-preview').prop('disabled',false);
			}

			
		},
		
		appendAttachment: function( attachment, i ){
			
			// vars
			attachment = this.validateAttachment( attachment );
			
			// bail early if is full
			if( this.isFull() ) {
				return;
			}
			
			// bail early if already exists
			if( this.$attachment( attachment.id ).length ) {
				return;
			}
			
			// html
			var html = [
			'<div class="acf-gallery-attachment" data-id="' + attachment.id + '">',
				'<input type="hidden" value="' + attachment.id + '" name="' + this.getInputName() + '[]">',
				'<div class="margin" title="">',
					'<div class="thumbnail">',
						'<img src="" alt="">',
					'</div>',
					'<div class="filename"></div>',
				'</div>',
				'<div class="actions">',
					'<a href="#" class="acf-icon -cancel dark acf-gallery-remove" data-id="' + attachment.id + '"></a>',
				'</div>',
			'</div>'].join('');
			var $html = $(html);
			
			// append
			this.$collection().append( $html );
			
			// move to beginning
			if( this.get('insert') === 'prepend' ) {
				var $before = this.$attachments().eq( i );
				if( $before.length ) {
					$before.before( $html );
				}
			}
			
			// render attachment
			this.renderAttachment( attachment );
			
			// render
			this.render();
			
			// trigger change
			this.$input().trigger('change');
		},
		
		validateAttachment: function( attachment ){
			
			// defaults
			attachment = acf.parseArgs(attachment, {
				id: '',
				url: '',
				alt: '',
				title: '',
				filename: '',
				type: 'image'
			});
			
			// WP attachment
			if( attachment.attributes ) {
				attachment = attachment.attributes;
				
				// preview size
				var url = acf.isget(attachment, 'sizes', this.get('preview_size'), 'url');
				if( url !== null ) {
					attachment.url = url;
				}
			}
			
			// return
			return attachment;
		},
		
		renderAttachment: function( attachment ){
			
			// vars
			attachment = this.validateAttachment( attachment );
			
			// vars
			var $el = this.$attachment( attachment.id );
			
			// Image type.
			if( attachment.type == 'image' ) {
				
				// Remove filename.
				$el.find('.filename').remove();
			
			// Other file type.	
			} else {	
				
				// Check for attachment featured image.
				var image = acf.isget(attachment, 'image', 'src');
				if( image !== null ) {
					attachment.url = image;
				}
				
				// Update filename text.
				$el.find('.filename').text( attachment.filename );
			}
			
			// Default to mimetype icon.
			if( !attachment.url ) {
				attachment.url = acf.get('mimeTypeIcon');
				$el.addClass('-icon');
			}
			
			// update els
		 	$el.find('img').attr({
			 	src:	attachment.url,
			 	alt:	attachment.alt,
			 	title:	attachment.title
			});
		 	
			// update val
		 	acf.val( $el.find('input'), attachment.id );
		},
		
		editAttachment: function( id ){
			
			// new frame
			var frame = acf.newMediaPopup({
				mode:		'edit',
				title:		acf.__('Edit Image'),
				button:		acf.__('Update Image'),
				attachment:	id,
				field:		this.get('key'),
				select:		$.proxy(function( attachment, i ) {
					this.renderAttachment( attachment );
					// todo - render sidebar
				}, this)
			});
		},
		
		onClickEdit: function( e, $el ){
			var id = $el.data('id');
			if( id ) {
				this.editAttachment( id );
			}
		},
		
		removeAttachment: function( id ){
			
			// close sidebar (if open)
			this.closeSidebar();
			
			// remove attachment
			this.$attachment( id ).remove();
			
			// render
			this.render();
			
			// trigger change
			this.$input().trigger('change');
		},
		
		onClickRemove: function( e, $el ){
			
			// prevent event from triggering click on attachment
			e.preventDefault();
			e.stopPropagation();
			
			//remove
			var id = $el.data('id');
			if( id ) {
				this.removeAttachment( id );
			}else{
				$el.parents('.acf-gallery-attachment').remove();     
			}
			var numAttachments = this.$attachments().length;
			var maxNum = this.$control().data('max');
			if(numAttachments<maxNum){
				this.$('input.images-preview').prop('disabled',false);
			}
		},
		
		selectAttachment: function( id ){
			
			// vars
			var $el = this.$attachment( id );
			
			// bail early if already active
			if( $el.hasClass('active') ) {
				return;
			}
			
			// step 1
			var step1 = this.proxy(function(){
				
				// save any changes in sidebar
				this.$side().find(':focus').trigger('blur');
				
				// clear selection
				this.$active().removeClass('active');
				
				// add selection
				$el.addClass('active');
				
				// open sidebar
				this.openSidebar();
				
				// call step 2
				step2();
			});
			
			// step 2
			var step2 = this.proxy(function(){
				
				// ajax
				var ajaxData = {
					action: 'acf/fields/gallery/get_attachment',
					field_key: this.get('key'),
					id: id
				};
				
				// abort prev ajax call
				if( this.has('xhr') ) {
					this.get('xhr').abort();
				}
				
				// loading
				acf.showLoading( this.$sideData() );
				
				// get HTML
				var xhr = $.ajax({
					url: acf.get('ajaxurl'),
					data: acf.prepareForAjax(ajaxData),
					type: 'post',
					dataType: 'html',
					cache: false,
					success: step3
				});
				
				// update
				this.set('xhr', xhr);
			});
			
			// step 3
			var step3 = this.proxy(function( html ){
				
				// bail early if no html
				if( !html ) {
					return;
				}
				
				// vars
				var $side = this.$sideData();
				
				// render
				$side.html( html );
				
				// remove acf form data
				$side.find('.compat-field-acf-form-data').remove();
				
				// merge tables
				$side.find('> table.form-table > tbody').append( $side.find('> .compat-attachment-fields > tbody > tr') );	
								
				// setup fields
				acf.doAction('append', $side);
			});
			
			// run step 1
			step1();
		},
		
		onClickSelect: function( e, $el ){
			var id = $el.data('id');
			if( id ) {
				this.selectAttachment( id );
			}
		},
		
		onClickClose: function( e, $el ){
			this.closeSidebar();
		},
		
		onChangeSort: function( e, $el ){
			
			// Bail early if is disabled.
			if( $el.hasClass('disabled') ) {
				return;
			}
			
			// Get sort val.
			var val = $el.val();
			if( !val ) {
				return;
			}
			
			// find ids
			var ids = [];
			this.$attachments().each(function(){
				ids.push( $(this).data('id') );
			});
			
			
			// step 1
			var step1 = this.proxy(function(){
				
				// vars
				var ajaxData = {
					action: 'acf/fields/gallery/get_sort_order',
					field_key: this.get('key'),
					ids: ids,
					sort: val
				};
				
				
				// get results
			    var xhr = $.ajax({
			    	url:		acf.get('ajaxurl'),
					dataType:	'json',
					type:		'post',
					cache:		false,
					data:		acf.prepareForAjax(ajaxData),
					success:	step2
				});
			});
			
			// step 2
			var step2 = this.proxy(function( json ){
				
				// validate
				if( !acf.isAjaxSuccess(json) ) {
					return;
				}
				
				// reverse order
				json.data.reverse();
				
				// loop
				json.data.map(function(id){
					this.$collection().prepend( this.$attachment(id) );
				}, this);
			});
			
			// call step 1
			step1();
		},
		
		onUpdate: function( e, $el ){
			
			// vars
			var $submit = this.$('.acf-gallery-update');
			
			// validate
			if( $submit.hasClass('disabled') ) {
				return;
			}
			
			// serialize data
			var ajaxData = acf.serialize( this.$sideData() );
			
			// loading
			$submit.addClass('disabled');
			$submit.before('<i class="acf-loading"></i> ');
			
			// append AJAX action		
			ajaxData.action = 'acf/fields/gallery/update_attachment';
			
			// ajax
			$.ajax({
				url: acf.get('ajaxurl'),
				data: acf.prepareForAjax(ajaxData),
				type: 'post',
				dataType: 'json',
				complete: function(){
					$submit.removeClass('disabled');
					$submit.closest('.frontend-form').find('.acf-loading').addClass('acf-hidden');
				}
			});
		},
		
		onHover: function(){
			
			// add sortable
			this.addSortable( this );
			
			// remove event
			this.off('mouseover');
		}
	});
	
	acf.registerFieldType( Field );
	
	// register existing conditions
	acf.registerConditionForFieldType('hasValue', 'upload_files');
	acf.registerConditionForFieldType('hasNoValue', 'upload_files');
	acf.registerConditionForFieldType('selectionLessThan', 'upload_files');
	acf.registerConditionForFieldType('selectionGreaterThan', 'upload_files');

	
	var Field = acf.models.RelationshipField.extend({		   
		type: 'product_grouped',
	});
	acf.registerFieldType( Field );	
	var Field = acf.models.RelationshipField.extend({		   
		type: 'product_cross_sells',
	});
	acf.registerFieldType( Field );	
	var Field = acf.models.RelationshipField.extend({		   
		type: 'product_upsells',
	});
	acf.registerFieldType( Field );	

	var Field = acf.models.FileField.extend({		   
		type: 'upload_file',
		$control: function () {
			return this.$('.acf-'+this.get('field_type')+'-uploader');
		},
		$input: function () {
			return this.$('input[type="hidden"]');
		},
		events: {
			'click a[data-name="add"]': 'onClickAdd',
			'click a[data-name="edit"]': 'onClickEdit',
			'click a[data-name="remove"]': 'onClickRemove',
			'change input[type="file"]': 'onChange',
			'input .file-preview': 'filePreview',
		},
		
		getRelatedType: function () {
			// vars
			var fieldType = this.get('field_type'); 	  
			return fieldType;
		},
		getRelatedPrototype: function () {
			return acf.getFieldType(this.getRelatedType()).prototype;
		},
		filePreview: function( e, $el ){	
			var reader = new FileReader();
			var control = this.$control();
			control.find('p.errors').remove();
			var file = e.target.files[0];
			reader.onload = function()
			{
				control.find('.hide-if-value').addClass('frontend-admin-hidden').siblings('.show-if-value').addClass('show').find('img').attr('src',reader.result);
				control.find('[data-name=filename]').html(file.name).attr('href','#');
				if(file.size < 1000000){
					var _size = Math.floor(file.size/1000) + 'KB';
				}else{
					var _size = Math.floor(file.size/1000000) + 'MB';  
				}
				control.find('[data-name=filesize]').html(_size);
			}
			imagePreview = true;
			reader.readAsDataURL(file);
			this.uploadFile(file);
		},

		uploadFile: function(file){
			var control = this.$control();
			var form = control.parents('form');
			control.find('.uploads-progress').removeClass('frontend-admin-hidden');
			var progPrc = control.find('.uploads-progress .percent');
			var progBar = control.find('.uploads-progress .bar');
			$.updateNumberTo('33','%',progPrc);
			progBar.animate({'width': '33%'}, 'slow');
			var nonce = this.$el.parents('form').find('input[name=_acf_nonce]').val();
			var fieldKey = this.get('key');
			var fileData = new FormData();
			fileData.append('action','acf/fields/upload_file/add_attachment');
			fileData.append('file',file);
			fileData.append('field_key',fieldKey);
			fileData.append('nonce',nonce);
			form.find('button.fea-submit-button').addClass('disabled');	
			control.find('.acf-actions').addClass('frontend-admin-hidden');		

			$.ajax({
				url: acf.get('ajaxurl'),
				data: acf.prepareForAjax(fileData),
				type: 'post',
				processData: false, 
				contentType: false, 
				cache: false
			}).done(function(response){
				if(response.success){
					$.updateNumberTo('100','%',progPrc);
					progBar.animate({'width': '100%'}, 'slow');
					if(response.data.src){
						control.find('img').attr('src',response.data.src).after('<p>'+response.data.title+'</p>');
					}
					control.children('input[type="hidden"]').val(response.data.id);
					form.find('button.fea-submit-button').removeClass('disabled');
					setTimeout(function(){
						control.find('.uploads-progress').addClass('frontend-admin-hidden');
						progPrc.text('0%');
						progBar.css('width', '0');
						control.find('.acf-actions').removeClass('frontend-admin-hidden');
					  }, 1000); 
				}else{
					control.find('.hide-if-value').removeClass('frontend-admin-hidden').siblings('.show-if-value').removeClass('show').find('img').attr('src','');
					control.addClass('not-valid').append('<p class="errors">'+response.data+'</p>').find('.margin').append('<p class="upload-fail">x</p>');
				}
			  } 
			);
		},
	});
	acf.registerFieldType( Field );
	var Field = acf.models.ImageField.extend({		   
		type: 'upload_image',
		$control: function () {
			return this.$('.acf-image-uploader');
		},
		$input: function () {
			return this.$('input[type="hidden"]');
		},
		events: {
			'click a[data-name="add"]': 'onClickAdd',
			'click a[data-name="edit"]': 'onClickEdit',
			'click a[data-name="remove"]': 'onClickRemove',
			'change input[type="file"]': 'onChange',
			'input .image-preview': 'imagePreview',
		},
		
		getRelatedType: function () {
			// vars
			var fieldType = this.get('field_type'); 	  
			return fieldType;
		},
		getRelatedPrototype: function () {
			return acf.getFieldType(this.getRelatedType()).prototype;
		},
		imagePreview: function( e, $el ){	
			var reader = new FileReader();
			var control = this.$control();
			control.find('p.errors').remove();
			reader.onload = function()
			{
				control.find('.hide-if-value').addClass('frontend-admin-hidden').siblings('.show-if-value').addClass('show').find('img').attr('src',reader.result);
			}
			imagePreview = true;
			reader.readAsDataURL(e.target.files[0]);
			this.uploadImage(e.target.files[0]);
		},

		uploadImage: function(file){
			var control = this.$control();
			var form = control.parents('form');
			control.find('.uploads-progress').removeClass('frontend-admin-hidden');
			var progPrc = control.find('.uploads-progress .percent');
			var progBar = control.find('.uploads-progress .bar');
			$.updateNumberTo('33','%',progPrc);
			progBar.animate({'width': '33%'}, 'slow');
			var nonce = this.$el.parents('form').find('input[name=_acf_nonce]').val();
			var fieldKey = this.get('key');
			var fileData = new FormData();
			fileData.append('action','acf/fields/upload_file/add_attachment');
			fileData.append('file',file);
			fileData.append('field_key',fieldKey);
			fileData.append('nonce',nonce);
			form.find('button.fea-submit-button').addClass('disabled');	
			control.find('.acf-actions').addClass('frontend-admin-hidden');		

			$.ajax({
				url: acf.get('ajaxurl'),
				data: acf.prepareForAjax(fileData),
				type: 'post',
				processData: false, 
				contentType: false, 
				cache: false
			}).done(function(response){
				if(response.success){
					$.updateNumberTo('100','%',progPrc);
					progBar.animate({'width': '100%'}, 'slow');
					if(response.data.src){
						control.find('img').attr('src',response.data.src).after('<p>'+response.data.title+'</p>');
					}
					control.children('input[type="hidden"]').val(response.data.id);
					form.find('button.fea-submit-button').removeClass('disabled');
					setTimeout(function(){
						control.find('.uploads-progress').addClass('frontend-admin-hidden');
						progPrc.text('0%');
						progBar.css('width', '0');
						control.find('.acf-actions').removeClass('frontend-admin-hidden');
					  }, 1000); 
				}else{
					control.find('.hide-if-value').removeClass('frontend-admin-hidden').siblings('.show-if-value').removeClass('show').find('img').attr('src','');
					control.addClass('not-valid').append('<p class="errors">'+response.data+'</p>').find('.margin').append('<p class="upload-fail">x</p>');
				}
			  } 
			);
		},
	});
	acf.registerFieldType( Field );

	var imageFields = ['featured_image','main_image','site_logo', 'site_favicon', 'upload_image'];

	$.each(imageFields, function(index, value){
		if( value != 'upload_image' ){
			var Field = acf.models.UploadImageField.extend({		   
				type: value,
			});
			acf.registerFieldType( Field );
		}
		acf.registerConditionForFieldType('hasValue', value);
		acf.registerConditionForFieldType('hasNoValue', value);
	});
})(jQuery);

(function($, undefined){
	
	var Field = acf.Field.extend({
		
		type: 'related_terms',
		
		data: {
			'ftype': 'select'
		},
		
		select2: false,
		
		wait: 'load',
		
		events: {
			'click a[data-name="add"]': 'onClickAdd',
			'click input[type="radio"]': 'onClickRadio',
		},
		
		$control: function(){
			return this.$('.acf-related-terms-field');
		},
		
		$input: function(){
			return this.getRelatedPrototype().$input.apply(this, arguments);
		},
		
		getRelatedType: function(){
			
			// vars
			var fieldType = this.get('ftype');
			
			// normalize
			if( fieldType == 'multi_select' ) {
				fieldType = 'select';
			}

			// return
			return fieldType;
			
		},
		
		getRelatedPrototype: function(){
			return acf.getFieldType( this.getRelatedType() ).prototype;
		},
		
		getValue: function(){
			return this.getRelatedPrototype().getValue.apply(this, arguments);
		},
		
		setValue: function(){
			return this.getRelatedPrototype().setValue.apply(this, arguments);
		},
		
		initialize: function(){
		
			// vars
			var $select = this.$input();
			
			// inherit data
			this.inherit( $select );
			
			// select2
			if( this.get('ui') ) {
				
				// populate ajax_data (allowing custom attribute to already exist)
				ajaxAction = 'acf/fields/related_terms/query';
				
				// select2
				this.select2 = acf.newSelect2($select, {
					field: this,
					ajax: this.get('ajax'),
					multiple: this.get('multiple'),
					placeholder: this.get('placeholder'),
					allowNull: this.get('allow_null'),
					ajaxAction: ajaxAction,
				});
			}
		},
		
		onRemove: function(){
			if( this.select2 ) {
				this.select2.destroy();
			}
		},
		
		onClickAdd: function( e, $el ){
			
			// vars
			var field = this;
			var popup = false;
			var $form = false;
			var $name = false;
			var $parent = false;
			var $button = false;
			var $message = false;
			var notice = false;
			
			// step 1.
			var step1 = function(){
				
				// popup
				popup = acf.newPopup({
					title: $el.attr('title'),
					loading: true,
					width: '300px'
				});
				
				// ajax
				var ajaxData = {
					action:		'acf/fields/related_terms/add_term',
					field_key:	field.get('key'),
					taxonomy:	field.get('taxonomy'),
				};
				
				// get HTML
				$.ajax({
					url: acf.get('ajaxurl'),
					data: acf.prepareForAjax(ajaxData),
					type: 'post',
					dataType: 'html',
					success: step2
				});
			};
			
			// step 2.
			var step2 = function( html ){
				
				// update popup
				popup.loading(false);
				popup.content(html);
				
				// vars
				$form = popup.$('form');
				$name = popup.$('input[name="term_name"]');
				$parent = popup.$('select[name="term_parent"]');
				$button = popup.$('.acf-submit-button');
				
				// focus
				$name.focus();
				
				// submit form
				popup.on('submit', 'form', step3);
			};
			
			// step 3.
			var step3 = function( e, $el ){
				
				// prevent
				e.preventDefault();
				e.stopImmediatePropagation();
				
				// basic validation
				if( $name.val() === '' ) {
					$name.focus();
					return false;
				}
				
				// disable
				acf.startButtonLoading( $button );
				
				// ajax
				var ajaxData = {
					action: 		'acf/fields/related_terms/add_term',
					field_key:		field.get('key'),
					taxonomy: 		field.get('taxonomy'),
					term_name:		$name.val(),
					term_parent:	$parent.length ? $parent.val() : 0
				};
				
				$.ajax({
					url: acf.get('ajaxurl'),
					data: acf.prepareForAjax(ajaxData),
					type: 'post',
					dataType: 'json',
					success: step4
				});
			};
			
			// step 4.
			var step4 = function( json ){
				
				// enable
				acf.stopButtonLoading( $button );
				
				// remove prev notice
				if( notice ) {
					notice.remove();
				}
				
				// success
				if( acf.isAjaxSuccess(json) ) {
					
					// clear name
					$name.val('');
					
					// update term lists
					step5( json.data );
					
					// notice
					notice = acf.newNotice({
						type: 'success',
						text: acf.getAjaxMessage(json),
						target: $form,
						timeout: 2000,
						dismiss: false
					});
					
				} else {
					
					// notice
					notice = acf.newNotice({
						type: 'error',
						text: acf.getAjaxError(json),
						target: $form,
						timeout: 2000,
						dismiss: false
					});
				}
				
				// focus
				$name.focus();
			};
			
			// step 5.
			var step5 = function( term ){
				
				// update parent dropdown
				var $option = $('<option value="' + term.term_id + '">' + term.term_label + '</option>');
				if( term.term_parent ) {
					$parent.children('option[value="' + term.term_parent + '"]').after( $option );
				} else {
					$parent.append( $option );
				}
				
				// add this new term to all taxonomy field
				var fields = acf.getFields({
					type: 'related_terms'
				});
				
				fields.map(function( otherField ){
					if( otherField.get('taxonomy') == field.get('taxonomy') ) {
						otherField.appendTerm( term );
					}
				});
				
				// select
				field.selectTerm( term.term_id );
			};
			
			// run
			step1();	
		},
		
		appendTerm: function( term ){
			
			if( this.getRelatedType() == 'select' ) {
				this.appendTermSelect( term );
			} else {
				this.appendTermCheckbox( term );
			}
		},
		
		appendTermSelect: function( term ){
			
			this.select2.addOption({
				id:			term.term_id,
				text:		term.term_label
			});
			
		},
		
		appendTermCheckbox: function( term ){
			
			// vars
			var name = this.$('[name]:first').attr('name');
			var $ul = this.$('ul:first');
			
			// allow multiple selection
			if( this.getRelatedType() == 'checkbox' ) {
				name += '[]';
			}
			
			// create new li
			var $li = $([
				'<li data-id="' + term.term_id + '">',
					'<label>',
						'<input type="' + this.get('ftype') + '" value="' + term.term_id + '" name="' + name + '" /> ',
						'<span>' + term.term_name + '</span>',
					'</label>',
				'</li>'
			].join(''));
			
			// find parent
			if( term.term_parent ) {
				
				// vars
				var $parent = $ul.find('li[data-id="' + term.term_parent + '"]');
				
				// update vars
				$ul = $parent.children('ul');
				
				// create ul
				if( !$ul.exists() ) {
					$ul = $('<ul class="children acf-bl"></ul>');
					$parent.append( $ul );
				}
			}
			
			// append
			$ul.append( $li );
		},
		
		selectTerm: function( id ){
			if( this.getRelatedType() == 'select' ) {
				this.select2.selectOption( id );
			} else {
				var $input = this.$('input[value="' + id + '"]');
				$input.prop('checked', true).trigger('change');
			}
		},
		
		onClickRadio: function( e, $el ){
			
			// vars
			var $label = $el.parent('label');
			var selected = $label.hasClass('selected');
			
			// remove previous selected
			this.$('.selected').removeClass('selected');
			
			// add active class
			$label.addClass('selected');
			
			// allow null
			if( this.get('allow_null') && selected ) {
				$label.removeClass('selected');
				$el.prop('checked', false).trigger('change');
			}
		}
	});
	
	acf.registerFieldType( Field );
	acf.registerConditionForFieldType('hasValue', 'related_terms');
	acf.registerConditionForFieldType('hasNoValue', 'related_terms');
	acf.registerConditionForFieldType('equalTo', 'related_terms');
	acf.registerConditionForFieldType('notEqualTo', 'related_terms');
	acf.registerConditionForFieldType('patternMatch', 'related_terms');
	acf.registerConditionForFieldType('contains', 'related_terms');
	acf.registerConditionForFieldType('selectionLessThan', 'related_terms');
	acf.registerConditionForFieldType('selectionGreaterThan', 'related_terms');	
	/* acf.registerConditionForFieldType('hasValue', 'post_status');
	acf.registerConditionForFieldType('hasNoValue', 'post_status');
	acf.registerConditionForFieldType('selectEqualTo', 'post_status');
	acf.registerConditionForFieldType('selectNotEqualTo', 'post_status'); */
})(jQuery);

acf.add_filter('select2_ajax_data', function( data, args, $input, field, instance ){

    if(field != false){
		$field_taxonomy = field.find('.acf-related-terms-field').data('taxonomy');
		data.taxonomy = $field_taxonomy;
	}
    return data;

});

(function($, undefined){
	
	var Field = acf.Field.extend({
		
		type: 'display_name',
		
		select2: false,
		
		wait: 'load',
		
		events: {
			'removeField': 'onRemove',
			'duplicateField': 'onDuplicate'
		},
		
		$input: function(){
			return this.$('select');
		},
		
		initialize: function(){
			
			// vars
			var $select = this.$input();
			
			// inherit data
			this.inherit( $select );
			
			// select2
			if( this.get('ui') ) {
				
				// populate ajax_data (allowing custom attribute to already exist)
				var ajaxAction = this.get('ajax_action');
				if( !ajaxAction ) {
					ajaxAction = 'acf/fields/' + this.get('type') + '/query';
				}
				
				// select2
				this.select2 = acf.newSelect2($select, {
					field: this,
					ajax: this.get('ajax'),
					multiple: this.get('multiple'),
					placeholder: this.get('placeholder'),
					allowNull: this.get('allow_null'),
					ajaxAction: ajaxAction,
				});
			}
		},

		
		onRemove: function(){
			if( this.select2 ) {
				this.select2.destroy();
			}
		},
		
		onDuplicate: function( e, $el, $duplicate ){
			if( this.select2 ) {
				$duplicate.find('.select2-container').remove();
				$duplicate.find('select').removeClass('select2-hidden-accessible');
			}
		}
	});
	
	acf.registerFieldType( Field );

	var tfFields = ['allow_comments'];

	$.each(tfFields, function(index, value){
		var Field = acf.models.TrueFalseField.extend({		   
			type: value,
		});
		acf.registerFieldType( Field );	
		acf.registerConditionForFieldType('equalTo', value);
		acf.registerConditionForFieldType('notEqualTo', value);
	});

	var Field = acf.Field.extend({
        
        type: 'list_items',
        wait: '',
        
        events: {
            'click a[data-event="add-row"]': 		'onClickAdd',
            'click a[data-event="duplicate-row"]':	'onClickDuplicate',
            'click a[data-event="remove-row"]': 	'onClickRemove',
			'click [data-event="collapse-row"]': 	'onClickCollapse',
            'showField':							'onShow',
            'unloadField':							'onUnload',
            'mouseover': 							'onHover',
        },
            
        $control: function(){
            return this.$('.acf-list-items:first');
        },
        
        $table: function(){
            return this.$('table:first');
        },
        
        $tbody: function(){
            return this.$('tbody:first');
        },
        
        $rows: function(){
            return this.$('tbody:first > tr').not('.acf-clone');
        },
        
        $row: function( index ){
            return this.$('tbody:first > tr:eq(' + index + ')');
        },
        
        $clone: function(){
            return this.$('tbody:first > tr.acf-clone');
        },
        
        $actions: function(){
            return this.$('.acf-actions:last');
        },
        
        $button: function(){
            return this.$('.acf-actions:last .button');
        },
        
        getValue: function(){
            return this.$rows().length;
        },
        
        allowRemove: function(){
            var min = parseInt( this.get('min') );
            return ( !min || min < this.val() );
        },
        
        allowAdd: function(){
            var max = parseInt( this.get('max') );
            return ( !max || max > this.val() );
        },
        
        addSortable: function( self ){
            
            // bail early if max 1 row
            if( this.get('max') == 1 ) {
                return;
            }
            
            // add sortable
            this.$tbody().sortable({
                items: '> tr',
                handle: '> td.order',
                forceHelperSize: true,
                forcePlaceholderSize: true,
                scroll: true,
                    stop: function(event, ui) {
                    self.render();
                    },
                    update: function(event, ui) {
                    self.$input().trigger('change');
                    }
            });
        },
        
        addCollapsed: function(){
            
            // vars
            var indexes = preference.load( this.get('key') );
            
            // bail early if no collapsed
            if( !indexes ) {
                return false;
            }
            
            // loop
            this.$rows().each(function( i ){
                if( indexes.indexOf(i) > -1 ) {
                    $(this).addClass('-collapsed');
                }
            });
        },
        
        addUnscopedEvents: function( self ){
            
            // invalidField
            this.on('invalidField', '.acf-row', function(e){
                var $row = $(this);
                if( self.isCollapsed($row) ) {
                    self.expand( $row );
                }
            });
        },
                
        initialize: function(){
            
            // add unscoped events
            this.addUnscopedEvents( this );
            
            // add collapsed
            //this.addCollapsed();
            
            // disable clone
            acf.disable( this.$clone(), this.cid );
            
            // render
            this.render();
        },
        
        render: function(){
            
            // update order number
            this.$rows().each(function( i ){
                $(this).find('> .order:not(.ids) > span').html( i+1 );
            });
            
            // Extract vars.
            var $controll = this.$control();
            var $button = this.$button();
            
            // empty
            if( this.val() == 0 ) {
                $controll.addClass('-empty');
            } else {
                $controll.removeClass('-empty');
            }
            
            // Reached max rows.
            if( !this.allowAdd() ) {
                $controll.addClass('-max');
                $button.addClass('disabled');
            } else {
                $controll.removeClass('-max');
                $button.removeClass('disabled');
            }
            
            // Reached min rows (not used).
            //if( !this.allowRemove() ) {
            //	$controll.addClass('-min');
            //} else {
            //	$controll.removeClass('-min');
            //}	
        },
        
        validateAdd: function(){
            
            // return true if allowed
            if( this.allowAdd() ) {
                return true;
            }
            
            // vars
            var max = this.get('max');
            var text = acf.__('Maximum rows reached ({max} rows)');
            
            // replace
            text = text.replace('{max}', max);
            
            // add notice
            this.showNotice({
                text: text,
                type: 'warning'
            });
            
            // return
            return false;
        },
        
        onClickAdd: function( e, $el ){
            
            // validate
            if( !this.validateAdd() ) {
                return false;
            }
            
            // add above row
            if( $el.hasClass('acf-icon') ) {
                this.add({
                    before: $el.closest('.acf-row')
                });
            
            // default
            } else {
                this.add();
            }
        },
        
        add: function( args ){
            
            // validate
            if( !this.allowAdd() ) {
                return false;
            }
            
            // defaults
            args = acf.parseArgs(args, {
                before: false
            });
            
            // add row
            var $el = acf.duplicate({
                target: this.$clone(),
                append: this.proxy(function( $el, $el2 ){
                    
                    // append
                    if( args.before ) {
                        args.before.before( $el2 );
                    } else {
                        $el.before( $el2 );
                    }
                    
                    // remove clone class
                    $el2.removeClass('acf-clone');
                    
                    // enable
                    acf.enable( $el2, this.cid );
                    
                    // render
                    this.render();

					$('html, body').animate({
						scrollTop: $($el2).offset().top-75,
					});
                })
            });
            
            // trigger change for validation errors
            this.$input().trigger('change');
            
            // return
            return $el;
        },
        
        onClickDuplicate: function( e, $el ){
            
            // Validate with warning.
            if( !this.validateAdd() ) {
                return false;
            }
            
            // get layout and duplicate it.
            var $row = $el.closest('.acf-row');
            this.duplicateRow( $row );
        },

        duplicateRow: function( $row ){
            
            // Validate without warning.
            if( !this.allowAdd() ) {
                return false;
            }
            
            // Vars.
            var fieldKey = this.get('key');
            
            // Duplicate row.
            var $el = acf.duplicate({
                target: $row,
                
                // Provide a custom renaming callback to avoid renaming parent row attributes.
                rename: function( name, value, search, replace ){
                    
                    // Rename id attributes from "field_1-search" to "field_1-replace".
                    if( name === 'id' ) {
                        return value.replace( fieldKey + '-' + search, fieldKey + '-' + replace );
                    
                    // Rename name and for attributes from "[field_1][search]" to "[field_1][replace]".
                    } else {
                        return value.replace( fieldKey + '][' + search, fieldKey + '][' + replace );
                    }
                },
                before: function( $el ){
                    acf.doAction('unmount', $el);
                },
                after: function( $el, $el2 ){
                    acf.doAction('remount', $el);
                },
            });
            
            // trigger change for validation errors
            this.$input().trigger('change');
            
            // Update order numbers.
            this.render();

            // Focus on new row.
            acf.focusAttention( $el );
                    
            // Return new layout.
            return $el;
        },

        validateRemove: function(){
            
            // return true if allowed
            if( this.allowRemove() ) {
                return true;
            }
            
            // vars
            var min = this.get('min');
            var text = acf.__('Minimum rows reached ({min} rows)');
            
            // replace
            text = text.replace('{min}', min);
            
            // add notice
            this.showNotice({
                text: text,
                type: 'warning'
            });
            
            // return
            return false;
        },
        
        onClickRemove: function( e, $el ){
            var $row = $el.closest('.acf-row');
            
            // Bypass confirmation when holding down "shift" key.
            if( e.shiftKey ) {
                return this.remove( $row );
            }

            // add class
            $row.addClass('-hover');
            
            // add tooltip
            var tooltip = acf.newTooltip({
                confirmRemove: true,
                target: $el,
                context: this,
                confirm: function(){
                    this.remove( $row );
                },
                cancel: function(){
                    $row.removeClass('-hover');
                }
            });
        },

        remove: function( $row ){
            
            // reference
            var self = this;
            
            // remove
            acf.remove({
                target: $row,
                endHeight: 0,
                complete: function(){
                    
                    // trigger change to allow attachment save
                    self.$input().trigger('change');
                
                    // render
                    self.render();
                    
                    // sync collapsed order
                    //self.sync();
                }
            });
        },
        
        isCollapsed: function( $row ){
            return $row.hasClass('-collapsed');
        },
        
        collapse: function( $row ){
            $row.addClass('-collapsed');
            acf.doAction('hide', $row, 'collapse');
        },
        
        expand: function( $row ){
            $row.removeClass('-collapsed');
            acf.doAction('show', $row, 'collapse');
			$('html, body').animate({
				scrollTop: $($row).closest('.acf-row').offset().top-75,
			});
        },
        
        onClickCollapse: function( e, $el ){
            
            // vars
            var $row = $el.closest('.acf-row');
            var isCollpased = this.isCollapsed( $row );
            
            // shift
            if( e.shiftKey ) {
                $row = this.$rows();
            }
            
            // toggle
            if( isCollpased ) {
                this.expand( $row );
            } else {
                this.collapse( $row );
            }	
        },
        
        onShow: function( e, $el, context ){
            
            // get sub fields
            var fields = acf.getFields({
                is: ':visible',
                parent: this.$el,
            });
            
            // trigger action
            // - ignore context, no need to pass through 'conditional_logic'
            // - this is just for fields like google_map to render itself
            acf.doAction('show_fields', fields);
        },
        
        onUnload: function(){
            
            // vars
            var indexes = [];
            
            // loop
            this.$rows().each(function( i ){
                if( $(this).hasClass('-collapsed') ) {
                    indexes.push( i );
                }
            });
            
            // allow null
            indexes = indexes.length ? indexes : null;
            
            // set
            preference.save( this.get('key'), indexes );
        },
        
        onHover: function(){
            
            // add sortable
            this.addSortable( this );
            
            // remove event
            this.off('mouseover');
        }
    });
    
    acf.registerFieldType( Field );
	acf.registerConditionForFieldType('hasValue', 'list_items');
    acf.registerConditionForFieldType('hasNoValue', 'list_items');
    acf.registerConditionForFieldType('lessThan', 'list_items');
    acf.registerConditionForFieldType('greaterThan', 'list_items');
	
})(jQuery);

(function($, undefined){
	var Field = acf.models.PostObjectField.extend({
		type: 'post_to_edit',
		events: {
			'change .acf-input > select': 	'onChangePost',
		},
		getType: function(){
			return 'post'; 
		},
		onChangePost: function( e, $el ){
			// Bail early if is disabled.
			if( $el.hasClass('disabled') ) {
				return;
			}
			var $form = $el.parents('.frontend-form');
			$form.addClass('disabled');
			$('body').append('<span class="acf-loading fixed-center"></span>');
			var formData = $form.find('input[name=_acf_form]').val();

			var ajaxData = {
				action:		'frontend_admin/forms/change_form',
				item_id: 		$el.val(),
				type: this.getType(),
				form_data:	formData,
			};
			// get HTML
			$.ajax({
				url: acf.get('ajaxurl'),
				data: acf.prepareForAjax(ajaxData),
				type: 'post',
				dataType: 'json',
				cache: false,
				success: function(response){
					if(response.data.reload_form){
						$form.removeClass('disabled');
						$('body').find('.acf-loading.fixed-center').hide();
						var newForm = $(response.data.reload_form);
						$form.replaceWith(newForm);
						acf.doAction('append',newForm)
					}
				}
			});

		},

	});
	acf.registerFieldType(Field);
	var Field = acf.models.PostToEditField.extend({
		type: 'product_to_edit',
		events: {
			'change .acf-input > select': 	'onChangePost',
		},
		getType: function(){
			return 'product'; 
		},
		
	});
	acf.registerFieldType(Field);
	var Field = acf.models.ImageField.extend({
		
		type: 'url_upload',
		
		$control: function(){
			return this.$('.acf-file-uploader');
		},
		
		$input: function(){
			return this.$('input[type="hidden"]');
		},
		
		validateAttachment: function( attachment ){
			
			// defaults
			attachment = attachment || {};
			
			// WP attachment
			if( attachment.id !== undefined ) {
				attachment = attachment.attributes;
			}
			
			// args
			attachment = acf.parseArgs(attachment, {
				url: '',
				alt: '',
				title: '',
				filename: '',
				filesizeHumanReadable: '',
				icon: '/wp-includes/images/media/default.png'
			});
						
			// return
			return attachment;
		},
		
		render: function( attachment ){
			
			// vars
			attachment = this.validateAttachment( attachment );
			var parent = this.$el.parents('form');
			var row = this.$el.parent('.acf-row');
			if( typeof row != 'undefined' ) parent = row;
			
			parent.find('[data-key="'+this.get('destination')+'"').find('.acf-url').addClass('-valid').find('input').val(attachment.url);
		},
		
		selectAttachment: function(){
			
			// vars
			var parent = this.parent();
			var multiple = (parent && parent.get('type') === 'repeater');
			
			// new frame
			var frame = acf.newMediaPopup({
				mode:			'select',
				title:			acf.__('Select File'),
				field:			this.get('key'),
				multiple:		multiple,
				library:		this.get('library'),
				allowedTypes:	this.get('mime_types'),
				select:			$.proxy(function( attachment, i ) {
					if( i > 0 ) {
						this.append( attachment, parent );
					} else {
						this.render( attachment );
					}
				}, this)
			});
		},
		
		editAttachment: function(){
			
			// vars
			var val = this.val();
			
			// bail early if no val
			if( !val ) {
				return false;
			}
			
			// popup
			var frame = acf.newMediaPopup({
				mode:		'edit',
				title:		acf.__('Edit File'),
				button:		acf.__('Update File'),
				attachment:	val,
				field:		this.get('key'),
				select:		$.proxy(function( attachment, i ) {
					this.render( attachment );
				}, this)
			});
		}
	});
	
	acf.registerFieldType( Field );

	var textFields = ['post_title','product_title','site_title','site_tagline','term_name','username', 'first_name','last_name','nickname'];
	var textLogic = ['hasValue', 'hasNoValue', 'equalTo', 'notEqualTo', 'patternMatch', 'contains'];

	$.each(textFields, function(index, value){
		$.each(textLogic, function(ind, logic){
			acf.registerConditionForFieldType(logic, value);
		});
	});
	
	var Field = acf.models.SelectField.extend({
		type: 'post_author'
	});
	acf.registerFieldType(Field);
	var Field = acf.models.SelectField.extend({
		type: 'product_author'
	});
	acf.registerFieldType(Field);
	acf.addFilter('select2_ajax_data', function (data, args, $input, field, select2) {

		if (!field) {
		return data;
		}

		const query_nonce = field.get('queryNonce');

		if (query_nonce && query_nonce.length) {
		data.author_query_nonce = query_nonce;
		}

		return data;
	});
	
	acf.addFilter('select2_ajax_data/name=mailchimp_lists', function (data, args, $input, field, select2) {

		if (!field) {
		return data;
		}

		var $el = field.$el;

		var $api = $el.siblings('.acf-field[data-key=api_key]').find('input[type=text]').val();

		if( $api ){
			data.api_key = $api;
		}
		return data;
	});
	       /*
     * Field: Select2
     */
	new acf.Model({

		filters: {
			'select2_args': 'select2Args',
			'select2_ajax_data': 'select2Ajax',
		},

		select2Args: function(options, $select, data, field, instance) {
			if (field.get('closeOnSelect')) {
				options.closeOnSelect =false;
			};

			// Allow Custom tags
			if (field.get('allowCustom')) {

				options.tags = true;

				options.createTag = function(params) {

					var term = $.trim(params.term);

					if (term === '')
						return null;

					var optionsMatch = false;

					this.$element.find('option').each(function() {

						if (this.value.toLowerCase() !== term.toLowerCase())
							return;

						optionsMatch = true;
						return false;

					});

					if (optionsMatch)
						return null;

					return {
						id: term,
						text: term
					};

				};


				options.insertTag = function(data, tag) {

					var found = false;

					$.each(data, function() {

						if ($.trim(tag.text).toUpperCase() !== $.trim(this.text).toUpperCase())
							return;

						found = true;
						return false;

					});

					if (!found)
						data.unshift(tag);

				};

			}

			options = acf.applyFilters('select2_args/type=' + field.get('type'), options, $select, data, field, instance);
			options = acf.applyFilters('select2_args/name=' + field.get('name'), options, $select, data, field, instance);
			options = acf.applyFilters('select2_args/key=' + field.get('key'), options, $select, data, field, instance);

			return options;

		},

		select2Ajax: function(ajaxData, data, $el, field, instance) {
			ajaxData = acf.applyFilters('select2_ajax_data/type=' + field.get('type'), ajaxData, data, $el, field, instance);
			ajaxData = acf.applyFilters('select2_ajax_data/name=' + field.get('name'), ajaxData, data, $el, field, instance);
			ajaxData = acf.applyFilters('select2_ajax_data/key=' + field.get('key'), ajaxData, data, $el, field, instance);
			
			if (ajaxData.action) {
				ajaxData = acf.applyFilters('select2_ajax_data/action=' + ajaxData.action, ajaxData, data, $el, field, instance);
			}

			return ajaxData;

		}

	});

	var Field = acf.Field.extend({
		type: 'frontend_blocks',
		wait: '',
		events: {
			'click [data-name="add-layout"]': 'onClickAdd',
			'click [data-name="duplicate-layout"]': 'onClickDuplicate',
			'click [data-name="remove-layout"]': 'onClickRemove',
			'click [data-name="collapse-layout"]': 'onClickCollapse',
			'showField': 'onShow',
			'unloadField': 'onUnload',
			'mouseover': 'onHover'
		},
		$control: function () {
		return this.$('.acf-frontend-blocks:first');
		},
		$layoutsWrap: function () {
		return this.$('.acf-frontend-blocks:first > .values');
		},
		$layouts: function () {
		return this.$('.acf-frontend-blocks:first > .values > .layout');
		},
		$layout: function (index) {
		return this.$('.acf-frontend-blocks:first > .values > .layout:eq(' + index + ')');
		},
		$clonesWrap: function () {
		return this.$('.acf-frontend-blocks:first > .clones');
		},
		$clones: function () {
		return this.$('.acf-frontend-blocks:first > .clones  > .layout');
		},
		$clone: function (name) {
		return this.$('.acf-frontend-blocks:first > .clones  > .layout[data-layout="' + name + '"]');
		},
		$actions: function () {
		return this.$('.acf-actions:last');
		},
		$button: function () {
		return this.$('.acf-actions:last .button');
		},
		$popup: function () {
		return this.$('.tmpl-popup:last');
		},
		getPopupHTML: function () {
		// vars
		var html = this.$popup().html();
		var $html = $(html); // count layouts
	
		var $layouts = this.$layouts();
	
		var countLayouts = function (name) {
			return $layouts.filter(function () {
			return $(this).data('layout') === name;
			}).length;
		}; // modify popup
	
	
		$html.find('[data-layout]').each(function () {
			// vars
			var $a = $(this);
			var min = $a.data('min') || 0;
			var max = $a.data('max') || 0;
			var name = $a.data('layout') || '';
			var count = countLayouts(name); // max
	
			if (max && count >= max) {
			$a.addClass('disabled');
			return;
			} // min
	
	
			if (min && count < min) {
			// vars
			var required = min - count;
	
			var title = acf.__('{required} {label} {identifier} required (min {min})');
	
			var identifier = acf._n('layout', 'layouts', required); // translate
	
	
			title = title.replace('{required}', required);
			title = title.replace('{label}', name); // 5.5.0
	
			title = title.replace('{identifier}', identifier);
			title = title.replace('{min}', min); // badge
	
			$a.append('<span class="badge" title="' + title + '">' + required + '</span>');
			}
		}); // update
	
		html = $html.outerHTML(); // return
	
		return html;
		},
		getValue: function () {
		return this.$layouts().length;
		},
		allowRemove: function () {
		var min = parseInt(this.get('min'));
		return !min || min < this.val();
		},
		allowAdd: function () {
		var max = parseInt(this.get('max'));
		return !max || max > this.val();
		},
		isFull: function () {
		var max = parseInt(this.get('max'));
		return max && this.val() >= max;
		},
		addSortable: function (self) {
		// bail early if max 1 row
		if (this.get('max') == 1) {
			return;
		} // add sortable
	
	
		this.$layoutsWrap().sortable({
			items: '> .layout',
			handle: '> .acf-frontend-blocks-layout-handle',
			forceHelperSize: true,
			forcePlaceholderSize: true,
			scroll: true,
			stop: function (event, ui) {
			self.render();
			},
			update: function (event, ui) {
			self.$input().trigger('change');
			}
		});
		},
		addCollapsed: function () {
		// vars
		var indexes = preference.load(this.get('key')); // bail early if no collapsed
	
		if (!indexes) {
			return false;
		} // loop
	
	
		this.$layouts().each(function (i) {
			if (indexes.indexOf(i) > -1) {
			$(this).addClass('-collapsed');
			}
		});
		},
		addUnscopedEvents: function (self) {
		// invalidField
		this.on('invalidField', '.layout', function (e) {
			self.onInvalidField(e, $(this));
		});
		},
		initialize: function () {
		// add unscoped events
		this.addUnscopedEvents(this); // add collapsed
	
		this.addCollapsed(); // disable clone
	
		acf.disable(this.$clonesWrap(), this.cid); // render
	
		this.render();
		},
		render: function () {
		// update order number
		this.$layouts().each(function (i) {
			$(this).find('.acf-frontend-blocks-layout-order:first').html(i + 1);
		}); // empty
	
		if (this.val() == 0) {
			this.$control().addClass('-empty');
		} else {
			this.$control().removeClass('-empty');
		} // max
	
	
		if (this.isFull()) {
			this.$button().addClass('disabled');
		} else {
			this.$button().removeClass('disabled');
		}

		},
		onShow: function (e, $el, context) {
		// get sub fields
		var fields = acf.getFields({
			is: ':visible',
			parent: this.$el
		}); // trigger action
		// - ignore context, no need to pass through 'conditional_logic'
		// - this is just for fields like google_map to render itself
		acf.doAction('show_fields', fields);
		},
		validateAdd: function () {
		// return true if allowed
		if (this.allowAdd()) {
			return true;
		} // vars
	
	
		var max = this.get('max');
	
		var text = acf.__('This field has a limit of {max} {label} {identifier}');
	
		var identifier = acf._n('layout', 'layouts', max); // replace
	
	
		text = text.replace('{max}', max);
		text = text.replace('{label}', '');
		text = text.replace('{identifier}', identifier); // add notice
	
		this.showNotice({
			text: text,
			type: 'warning'
		}); // return
	
		return false;
		},
		onClickAdd: function (e, $el) {
		// validate
		if (!this.validateAdd()) {
			return false;
		} // within layout
	
	
		var $layout = null;
	
		if ($el.hasClass('acf-icon')) {
			$layout = $el.closest('.layout');
			$layout.addClass('-hover');
		} // new popup
	
	
		var popup = new Popup({
			target: $el,
			targetConfirm: false,
			text: this.getPopupHTML(),
			context: this,
			confirm: function (e, $el) {
			// check disabled
			if ($el.hasClass('disabled')) {
				return;
			} // add
	
	
			this.add({
				layout: $el.data('layout'),
				before: $layout
			});
			},
			cancel: function () {
			if ($layout) {
				$layout.removeClass('-hover');
			}
			}
		}); // add extra event
	
		popup.on('click', '[data-layout]', 'onConfirm');
		},
		add: function (args) {
		// defaults
		args = acf.parseArgs(args, {
			layout: '',
			before: false
		}); // validate
	
		if (!this.allowAdd()) {
			return false;
		} // add row
	
	
		var $el = acf.duplicate({
			target: this.$clone(args.layout),
			append: this.proxy(function ($el, $el2) {
			// append
			if (args.before) {
				args.before.before($el2);
			} else {
				this.$layoutsWrap().append($el2);
			} // enable
	
	
			acf.enable($el2, this.cid); // render
	
			this.render();
			})
		}); // trigger change for validation errors
	
		this.$input().trigger('change'); // return
	
		return $el;
		},
		onClickDuplicate: function (e, $el) {
		// Validate with warning.
		if (!this.validateAdd()) {
			return false;
		} // get layout and duplicate it.
	
	
		var $layout = $el.closest('.layout');
		this.duplicateLayout($layout);
		},
		duplicateLayout: function ($layout) {
		// Validate without warning.
		if (!this.allowAdd()) {
			return false;
		} // Vars.
	
	
		var fieldKey = this.get('key'); // Duplicate layout.
	
		var $el = acf.duplicate({
			target: $layout,
			// Provide a custom renaming callback to avoid renaming parent row attributes.
			rename: function (name, value, search, replace) {
			// Rename id attributes from "field_1-search" to "field_1-replace".
			if (name === 'id' || name === 'for') {
				return value.replace(fieldKey + '-' + search, fieldKey + '-' + replace); // Rename name and for attributes from "[field_1][search]" to "[field_1][replace]".
			} else {
				return value.replace(fieldKey + '][' + search, fieldKey + '][' + replace);
			}
			},
			before: function ($el) {
			acf.doAction('unmount', $el);
			},
			after: function ($el, $el2) {
			acf.doAction('remount', $el);
			}
		}); // trigger change for validation errors
	
		this.$input().trigger('change'); // Update order numbers.
	
		this.render(); // Draw focus to layout.
	
		acf.focusAttention($el); // Return new layout.
	
		return $el;
		},
		validateRemove: function () {
		// return true if allowed
		if (this.allowRemove()) {
			return true;
		} // vars
	
	
		var min = this.get('min');
	
		var text = acf.__('This field requires at least {min} {label} {identifier}');
	
		var identifier = acf._n('layout', 'layouts', min); // replace
	
	
		text = text.replace('{min}', min);
		text = text.replace('{label}', '');
		text = text.replace('{identifier}', identifier); // add notice
	
		this.showNotice({
			text: text,
			type: 'warning'
		}); // return
	
		return false;
		},
		onClickRemove: function (e, $el) {
		var $layout = $el.closest('.layout'); // Bypass confirmation when holding down "shift" key.
	
		if (e.shiftKey) {
			return this.removeLayout($layout);
		} // add class
	
	
		$layout.addClass('-hover'); // add tooltip
	
		var tooltip = acf.newTooltip({
			confirmRemove: true,
			target: $el,
			context: this,
			confirm: function () {
			this.removeLayout($layout);
			},
			cancel: function () {
			$layout.removeClass('-hover');
			}
		});
		},
		removeLayout: function ($layout) {
		// reference
		var self = this; // vars
	
		var endHeight = this.getValue() == 1 ? 60 : 0; // remove
	
		acf.remove({
			target: $layout,
			endHeight: endHeight,
			complete: function () {
			// trigger change to allow attachment save
			self.$input().trigger('change'); // render
	
			self.render();
			}
		});
		},
		onClickCollapse: function (e, $el) {
		// vars
		var $layout = $el.closest('.layout'); // toggle
	
		if (this.isLayoutClosed($layout)) {
			this.openLayout($layout);
		} else {
			this.closeLayout($layout);
		}
		},
		isLayoutClosed: function ($layout) {
		return $layout.hasClass('-collapsed');
		},
		openLayout: function ($layout) {
		$layout.removeClass('-collapsed');
		acf.doAction('show', $layout, 'collapse');
		},
		closeLayout: function ($layout) {
		$layout.addClass('-collapsed');
		acf.doAction('hide', $layout, 'collapse'); // render
		// - no change could happen if layout was already closed. Only render when closing
	
		this.renderLayout($layout);
		},
		renderLayout: function ($layout) {
		// vars
		var $input = $layout.children('input');
		var prefix = $input.attr('name').replace('[acf_frontend_blocks_layout]', ''); // ajax data
	
		var ajaxData = {
			action: 'acf/fields/frontend_blocks/layout_title',
			field_key: this.get('key'),
			i: $layout.index(),
			layout: $layout.data('layout'),
			value: acf.serialize($layout, prefix)
		}; // ajax
	
		$.ajax({
			url: acf.get('ajaxurl'),
			data: acf.prepareForAjax(ajaxData),
			dataType: 'html',
			type: 'post',
			success: function (html) {
			if (html) {
				$layout.children('.acf-frontend-blocks-layout-handle').html(html);
			}
			}
		});
		},
		onUnload: function () {
		// vars
		var indexes = []; // loop
	
		this.$layouts().each(function (i) {
			if ($(this).hasClass('-collapsed')) {
			indexes.push(i);
			}
		}); // allow null
	
		indexes = indexes.length ? indexes : null; // set
	
		preference.save(this.get('key'), indexes);
		},
		onInvalidField: function (e, $layout) {
		// open if is collapsed
		if (this.isLayoutClosed($layout)) {
			this.openLayout($layout);
		}
		},
		onHover: function () {
		// add sortable
		this.addSortable(this); // remove event
	
		this.off('mouseover');
		}
	});
	acf.registerFieldType(Field);
/**
   *  Popup
   *
   *  description
   *
   *  @date	7/4/18
   *  @since	5.6.9
   *
   *  @param	type $var Description. Default.
   *  @return	type Description.
   */

 var Popup = acf.models.TooltipConfirm.extend({
    events: {
      'click [data-layout]': 'onConfirm',
      'click [data-event="cancel"]': 'onCancel'
    },
    render: function () {
      // set HTML
      this.html(this.get('text')); // add class

      this.$el.addClass('acf-frontend-blocks-popup');
    }
  });
  /**
   *  conditions
   *
   *  description
   *
   *  @date	9/4/18
   *  @since	5.6.9
   *
   *  @param	type $var Description. Default.
   *  @return	type Description.
   */
  // register existing conditions

  acf.registerConditionForFieldType('hasValue', 'frontend_blocks');
  acf.registerConditionForFieldType('hasNoValue', 'frontend_blocks');
  acf.registerConditionForFieldType('lessThan', 'frontend_blocks');
  acf.registerConditionForFieldType('greaterThan', 'frontend_blocks'); // state

  var preference = new acf.Model({
    name: 'this.collapsedLayouts',
    key: function (key, context) {
      // vars
      var count = this.get(key + context) || 0; // update

      count++;
      this.set(key + context, count, true); // modify fieldKey

      if (count > 1) {
        key += '-' + count;
      } // return


      return key;
    },
    load: function (key) {
      // vars
      var key = this.key(key, 'load');
      var data = acf.getPreference(this.name); // return

      if (data && data[key]) {
        return data[key];
      } else {
        return false;
      }
    },
    save: function (key, value) {
      // vars
      var key = this.key(key, 'save');
      var data = acf.getPreference(this.name) || {}; // delete

      if (value === null) {
        delete data[key]; // append
      } else {
        data[key] = value;
      } // allow null


      if ($.isEmptyObject(data)) {
        data = null;
      } // save


      acf.setPreference(this.name, data);
    }
  });

		
	
})(jQuery);

