$(document).ready(function () {

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	/*-----------------------------------/
	/*	TOP NAVIGATION AND LAYOUT
	/*----------------------------------*/

	$('.btn-toggle-fullwidth').on('click', function () {
		if (!$('body').hasClass('layout-fullwidth')) {
			$('body').addClass('layout-fullwidth');

		} else {
			$('body').removeClass('layout-fullwidth');
			$('body').removeClass('layout-default'); // also remove default behaviour if set
		}

		$(this).find('.lnr').toggleClass('lnr-arrow-left-circle lnr-arrow-right-circle');

		if ($(window).innerWidth() < 1025) {
			if (!$('body').hasClass('offcanvas-active')) {
				$('body').addClass('offcanvas-active');
			} else {
				$('body').removeClass('offcanvas-active');
			}
		}
	});

	$(window).on('load', function () {
		if ($(window).innerWidth() < 1025) {
			$('.btn-toggle-fullwidth').find('.icon-arrows')
				.removeClass('icon-arrows-move-left')
				.addClass('icon-arrows-move-right');
		}

		// adjust right sidebar top position
		$('.right-sidebar').css('top', $('.navbar').innerHeight());

		// if page has content-menu, set top padding of main-content
		if ($('.has-content-menu').length > 0) {
			$('.navbar + .main-content').css('padding-top', $('.navbar').innerHeight());
		}

		// for shorter main content
		if ($('.main').height() < $('#sidebar-nav').height()) {
			$('.main').css('min-height', $('#sidebar-nav').height());
		}
	});


	/*-----------------------------------/
	/*	SIDEBAR NAVIGATION
	/*----------------------------------*/

	$('.sidebar a[data-toggle="collapse"]').on('click', function () {
		if ($(this).hasClass('collapsed')) {
			$(this).addClass('active');
		} else {
			$(this).removeClass('active');
		}
	});

	if ($('.sidebar-scroll').length > 0) {
		$('.sidebar-scroll').slimScroll({
			height: '95%',
			wheelStep: 2,
		});
	}


	/*-----------------------------------/
	/*	PANEL FUNCTIONS
	/*----------------------------------*/

	// panel remove
	$('.panel .btn-remove').click(function (e) {

		e.preventDefault();
		$(this).parents('.panel').fadeOut(300, function () {
			$(this).remove();
		});
	});

	// panel collapse/expand
	var affectedElement = $('.panel-body');

	$('.panel .btn-toggle-collapse').clickToggle(
		function (e) {
			e.preventDefault();

			// if has scroll
			if ($(this).parents('.panel').find('.slimScrollDiv').length > 0) {
				affectedElement = $('.slimScrollDiv');
			}

			$(this).parents('.panel').find(affectedElement).slideUp(300);
			$(this).find('i.lnr-chevron-up').toggleClass('lnr-chevron-down');
		},
		function (e) {
			e.preventDefault();

			// if has scroll
			if ($(this).parents('.panel').find('.slimScrollDiv').length > 0) {
				affectedElement = $('.slimScrollDiv');
			}

			$(this).parents('.panel').find(affectedElement).slideDown(300);
			$(this).find('i.lnr-chevron-up').toggleClass('lnr-chevron-down');
		}
	);


	/*-----------------------------------/
	/*	PANEL SCROLLING
	/*----------------------------------*/

	if ($('.panel-scrolling').length > 0) {
		$('.panel-scrolling .panel-body').slimScroll({
			height: '430px',
			wheelStep: 2,
		});
	}

	if ($('#panel-scrolling-demo').length > 0) {
		$('#panel-scrolling-demo .panel-body').slimScroll({
			height: '175px',
			wheelStep: 2,
		});
	}

	/*-----------------------------------/
	/*	TODO LIST
	/*----------------------------------*/

	$('.todo-list input').change(function () {
		if ($(this).prop('checked')) {
			$(this).parents('li').addClass('completed');
		} else {
			$(this).parents('li').removeClass('completed');
		}
	});


	/*-----------------------------------/
	/* TOASTR NOTIFICATION
	/*----------------------------------*/

	if ($('#toastr-demo').length > 0) {
		toastr.options.timeOut = "false";
		toastr.options.closeButton = true;
		toastr['info']('Hi there, this is notification demo with HTML support. So, you can add HTML elements like <a href="#">this link</a>');

		$('.btn-toastr').on('click', function () {
			$context = $(this).data('context');
			$message = $(this).data('message');
			$position = $(this).data('position');

			if ($context == '') {
				$context = 'info';
			}

			if ($position == '') {
				$positionClass = 'toast-left-top';
			} else {
				$positionClass = 'toast-' + $position;
			}

			toastr.remove();
			toastr[$context]($message, '', {positionClass: $positionClass});
		});

		$('#toastr-callback1').on('click', function () {
			$message = $(this).data('message');

			toastr.options = {
				"timeOut": "300",
				"onShown": function () {
					alert('onShown callback');
				},
				"onHidden": function () {
					alert('onHidden callback');
				}
			}

			toastr['info']($message);
		});

		$('#toastr-callback2').on('click', function () {
			$message = $(this).data('message');

			toastr.options = {
				"timeOut": "10000",
				"onclick": function () {
					alert('onclick callback');
				},
			}

			toastr['info']($message);

		});

		$('#toastr-callback3').on('click', function () {
			$message = $(this).data('message');

			toastr.options = {
				"timeOut": "10000",
				"closeButton": true,
				"onCloseClick": function () {
					alert('onCloseClick callback');
				}
			}

			toastr['info']($message);
		});
	}

	// Floating label
	$("body").on("input propertychange", ".floating", function (e) {
		$(this).toggleClass("floating-with-value", !!$(e.target).val());
	}).on("focus", ".floating", function () {
		$(this).addClass("floating-with-focus");
	}).on("blur", ".floating", function () {
		$(this).removeClass("floating-with-focus");
	});

	$('#filter_date_from, #filter_date_to').datetimepicker({
		locale: 'ru'
	});

	$('.copy_btn').on('click', function (e) {
		var $temp = $('<input>');
		$('body').append($temp);
		var selected_text = $(this).closest('fieldset').find('span.field_value').text();
		$temp.val(selected_text).select();
		document.execCommand('copy');
		$temp.remove();
	});

	$('.doc_row').on('dblclick', function (e) {
		window.location.href = '/docs/' + $(this).data('doctypes_id') + '/' + $(this).data('id');
	});

	// Doc Buttons
	$('.doc_btn').on('click', function (e) {
		var $this = $(this);

		$('#status_id').val($this.data('status_id'));

		// Buttons: "Checked", "Agreed", "Rejected"
		if ($.inArray($this.data('status_id'), [3, 7, 4]) !== -1) {
			e.preventDefault();

			$('.add-content').html('');
			var add_content = '';

			if ($this.data('status_id') == 4) {
				$('.add-content').append('<textarea name="rejected_reason" class="form-control" placeholder="Причина отказа" maxlength="200" style="height:100px;"></textarea>');
			} else {
				var notagreed = 0;
				$('.status_class').each(function () {
					if ($(this).hasClass('agreed') && !$(this).is(':checked')) {
						add_content += '<li>Поле «' + $(this).data('field_name') + '» не подтверждено.</li>';
						notagreed = 1;
					}
				});
				if (notagreed) {
					$('.add-content').append('<ul>' + add_content + '</ul>');
					$('.add-content').append('<div style="margin-top:20px;">Подтвердите ' + $this.data('text') + ' документа.</div>');
				}
			}

			$("#modal").modal().one('click', '#confirm_modal_btn', function (e) {
				$this.closest('form').submit();
			});
		} else {
			$('#status_id').val($this.data('status_id'));
			$this.closest('form').submit();
		}
	});
});

// toggle function
$.fn.clickToggle = function (f1, f2) {
	return this.each(function () {
		var clicked = false;
		$(this).bind('click', function () {
			if (clicked) {
				clicked = false;
				return f2.apply(this, arguments);
			}

			clicked = true;
			return f1.apply(this, arguments);
		});
	});
}

