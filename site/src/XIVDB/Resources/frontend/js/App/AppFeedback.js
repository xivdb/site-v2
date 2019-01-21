var feedback =
{
    adb: false,
    viewing: 0,
    data: {},

    open: function(title) {
        feedback.populate();

        $('.feedback-form').show();

        if (title) {
            $('#feedback-title').val($('<textarea />').html(title).text());
            $('#feedback-section').val('Content');
        }
    },

    close: function() {
        $('.feedback-form').hide();
        $('.feedback-submit').enable('Submit Report');
    },

    reset: function() {
        $('#feedback-title').val('');
        $('#feedback-message').val('');
        $('#feedback-category').val('Feedback');
        $('#feedback-section').val('General');
    },

    populate: function() {
        feedback.data.res_width = $(window).width();
        feedback.data.res_height = $(window).height();
        feedback.data.browser = navigator.sayswho;
    },

    post: function() {
        $('.feedback-submit').disable('Submitting!');

        var data =
        {
            url: location.href,
            section: $('#feedback-section').val(),
            category: $('#feedback-category').val(),
            title: $('#feedback-title').val().trim(),
            message: $('#feedback-message').val().trim(),
            info: feedback.data,
        }

        $.ajax({
            url: '/feedback/submit',
            data: data,
            method: 'POST',
            success: function(data) {
                $('.feedback-submit').enable('Submit Report');
                $('.feedback-submit-return').html(render('#ui-feedback-ok', {
                    id: data
                }));
            },
            error: function(data, status, code) {
                console.error(data);
                console.error(status, code);
            }
        });
    },

    reply: function() {
        $('.feedback-reply-button').disable('<i class="fa fa-circle-o-notch fa-spin fa-fw"></i>');

        if ($('.feedback-reply-textarea').val().length < 1) {
            // too short
        } else {
            $.ajax({
                url: '/feedback/reply',
                data: {
                    fid: feedback.viewing,
                    message: $('.feedback-reply-textarea').val().trim(),
                },
                method: 'POST',
                success: function(response) {
                    if (response || response == 'true') {
                        location.reload();
                        return;
                    }

                    $('.feedback-panel').after('<div class="error">Could not post reply, try again later!</div>');
                    console.error(response);
                },
                error: function(data, status, code)
                {
                    console.error(data);
                    console.error(status, code);
                },
            });
        }
    }
}
