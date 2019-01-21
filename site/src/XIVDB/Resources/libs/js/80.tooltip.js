//
// Custom tooltips
//
$(function()
{
    var mpos = [];

    $('body').append('<div class="datatt"></div>');

    $('html').mousemove(function(e) {
        mpos[0] = e.pageY;
        mpos[1] = e.pageX;
    });

    $('html').on('mousemove', '*[data-tt]', function()
    {
        var text = $(this).attr('data-tt'),
            position = $(this).attr('data-tt-position'),
            mtop = mpos[0],
            mleft = mpos[1];

        $('.datatt').html(text);

        var top = (mtop - ($('.datatt').outerHeight() + 20)),
            left = (mleft - ($('.datatt').outerWidth() / 2));

        $('.datatt').show().css({
            top: top + 'px',
            left: left + 'px',
        });

    }).on('mouseleave', '*[data-tt]', function() {
        $('.datatt').hide().html('');
    });
});

function ttdrop()
{
    $('.datatt').hide().html('');
}
