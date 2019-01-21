//
// Handle the main navigation
//
class ManualUpdateClass
{
	watch()
	{
		var id = $('.character-profile').attr('data-id');

		$('html').on('click', '#manualUpdateCharacter', event => {
			$(event.currentTarget).loading();

			$.ajax({
				url: 'https://xivsync.com/character/update/' + id,
				cache: false,
				dataType: 'json',
				success: (response) => {
					$(event.currentTarget).loading(true);
					if (response.success) {
						let msg = `${response.message}\nPlease allow up to 5-10 minutes before it shows up on your profile!`;
						swal("Got it!", msg, "success");
						return;
					}

					let msg = `Could not request update at this moment, try again shortly.`;
					swal("Sorry!", msg, 'error');
				},
				error: (response) => {

				}
			});
		});
	}
}
