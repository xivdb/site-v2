//
// Home functionality
//
class AppContentScreenshotsClass
{
    //
    // Check for lightbox request in the url
    //
    checkForLightbox()
    {
        // get hash
        var hash = window.location.hash;

        // check for lightbox hash
        if (hash.indexOf('#lb=') > -1)
        {
            var imageId = hash.replace('#lb=', '');

            // if ID
            if (imageId)
            {
                // switch to screenshots tab
                $('.tab-nav span[data-tab="screenshots"]').trigger('click');

                // image url
                var imageUrl = $(`img[data-img-id="${imageId}"]`).attr('src');
                if (imageUrl) {
                    imageUrl = imageUrl.replace('small_', '');

                    $('.lightbox a').attr('href', imageUrl);
                    $('.lightbox .lightbox-image').html(`<img src="${imageUrl}">`);
                    $('.lightbox').show();
                }
            }
        }

        // watch on others
        $('.screenshots a').on('click', function(event) {
            event.preventDefault();

            var $image = $(this).find('img[data-img-id]')
                imageUrl = $image.attr('src'),
                imageUrl = imageUrl.replace('small_', ''),

            $('.lightbox a').attr('href', imageUrl);
            $('.lightbox .lightbox-image').html(`<img src="${imageUrl}">`);
            $('.lightbox').show();
        });

        // close lightbox
        $(document).keyup(function(e) {
            if (e.keyCode == 27) {
                $('.lightbox').hide();
            }
        });
    }
}

var AppContentScreenshots = new AppContentScreenshotsClass();
