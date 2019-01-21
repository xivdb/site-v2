//
// Submit Content Information Class
//
class SubmitInformationClass
{
	constructor()
	{
		this.$form = $('.content-submit-information');

		$('html').on('click', '.tc-submit', () => {
			this.$form.addClass('active');
			this.$form.find('.content-title').text(window.location.pathname);
		});

		$('html').on('click', '.csi-close', () => {
			this.$form.removeClass('active');
		});

		// Tab selection
		$('html').on('click', '.csi-menu > span', (event) => {
			var $btn = $(event.currentTarget),
				tab = $btn.attr('data-tab');

			$('.csi-menu, .csi-tabs').find('.active').removeClass('active');

			$btn.addClass('active');
			$('.csi-tabs #csi-' + tab).addClass('active');
		});

		// duplicate map position
		$('html').on('click', '.posrow button#duplicate', (event) => {
			var $row = $(event.currentTarget).parent(),
				$newRow = $row.clone(),
				selectEntry = $row.find('select').val();

			$newRow.insertAfter($row);
			$newRow.find('select').val(selectEntry);
		});

		$('html').on('submit', '#form_positions', (event) => {
			event.preventDefault();

			var positions = [];

			$('.posrow').each((i, element) => {
				var $row = $(element);

				positions.push({
					pos_map: $row.find('#pos_map').val(),
					pos_x: $row.find('#pos_x').val(),
					pos_z: $row.find('#pos_z').val(),
					pos_notes: $row.find('#pos_notes').val(),
					content_id: $row.find('#content_id').val(),
					content_type: $row.find('#content_type').val(),
				});
			});

			var data = {
				url: window.location.href,
				data: positions,
			}

			$.ajax({
				url: '/feedback/content/submit/map',
				data: data,
				method: 'POST',
				cache: false,
				success: function(data) {
					if (data == 'ok') {
						$('#form_positions .submit-box').html('<div class="alert alert-success">Thank you! Your map positions have been sent and will added to the site once a moderator has checked it! :)</div>');
						return;
					}

					console.error(data);
				},
				error: function(d1, d2, d3) {
					console.error(d1,d2,d3);
				}
			});
		});

		$('html').on('submit', '#form_textinfo', (event) => {
			event.preventDefault();

			var message = $('#textinfo').val();

			$.ajax({
				url: '/feedback/content/submit/information',
				data: {
					url: window.location.href,
					message: message,
				},
				method: 'POST',
				cache: false,
				success: function(data) {
					if (data == 'ok') {
						$('#form_textinfo .submit-box').html('<div class="alert alert-success">Thank you! Your information will be added to the site once a moderator has checked it!</div>');
						return;
					}
					console.error(data);
				},
				error: function(d1, d2, d3) {
					console.error(d1,d2,d3);
				}
			});
		});
	}
}

$(function() {
	var SubmitInformation = new SubmitInformationClass();
});
