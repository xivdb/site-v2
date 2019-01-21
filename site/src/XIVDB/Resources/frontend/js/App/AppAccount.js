//
// Account
//
class AppAccountClass
{
    constructor()
    {
        this.search = 'https://xivsync.com/character/search';
        this.character = '/account/characters/verify/{id}';

        //
        // Click to show form
        //
        $('.acc-char-add').on('click', (event) => {
            $('.acc-char-add').slideUp(150);
            $('.acc-char-add-form').slideDown(150);
        });

        //
        // Click even for the search form
        //
        $('.acc-char-add-form').on('click', '.form button', (event) => {
            $(event.target).loading();
            this.setWaitingMessage(languages.custom(981));

            var data = {
                server: $('#character-server').val().trim(),
            };

            if (name = $('#character-name').val().trim()) {
                data.name = name;
            }

            $.ajax({
                url: this.search,
                cache: false,
                data: data,
                success: (data) => {

                    $(event.target).loading(true);
                    this.setWaitingMessage(false);

                    // show response
                    $('.acc-char-add-response').show();

                    // Get element
                    var $ele = $('.acc-char-add-response .acc-char-add-list');
                    $ele.html('');

                    // append faces
                    for (var i in data.data.results) {
                        var character = data.data.results[i];
                        $ele.append(`<button class="acc-char-add-btn" data-id="${character.id}">
                            <img src="${character.avatar}" width="32" height="32">
                            <div>${character.name}</div>
                        </button>`);
                    }
                },
                error: (data, status, code) => {
                    $(event.target).loading(true);
                    this.setWaitingMessage(languages.custom(982));
                    console.log(data, status, code);
                },
            });
        });

        //
        // Click event for the character
        //
        $('.acc-char-add-form').on('click', '.acc-char-add-response button', (event) => {
            var id = $(event.currentTarget).attr('data-id'),
                url = this.character.replace('{id}', id);

            this.setWaitingMessage(languages.custom(983));

            $.ajax({
                url: url,
                cache: false,
                success: (data) => {
                    if (data[0]) {
                        $('.acc-char-add-response').hide();
                        this.setWaitingMessage(languages.custom(984), 'fa fa-check-circle-o fa-2x');
                        return;
                    }

                    this.setWaitingMessage(data[1], 'fa fa-exclamation-triangle fa-2x');
                },
                error: (data, status, code) => {
                    this.setWaitingMessage(languages.custom(985));
                    console.log(data, status, code);
                },
            });
        });

        //
        // Change background selection
        //
        $('#site_background').on('change', (event) => {
            $('.acc-bg-preview').attr('data-preview', $(event.currentTarget).val());
        });
        $('.acc-bg-preview').attr('data-preview', $('#site_background').val());
    }

    //
    // Set the waiting message
    //
    setWaitingMessage(msg, icon)
    {
        if (!msg) {
            $('.acc-ajax-waiting').hide();
            return;
        }

        $('.acc-ajax-waiting').show().find('> div').html(msg);

        // if to change icon
        $('.acc-ajax-waiting i').attr('class', 'fa fa-refresh fa-spin fa-2x');
        if (icon) {
            $('.acc-ajax-waiting i').attr('class', icon);
        }
    }
}

$(() => { var AppAccount = new AppAccountClass(); })
