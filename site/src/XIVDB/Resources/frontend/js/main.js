var app =
{
    init: function()
    {
        // auto size all textareas (except on mobile, too laggy)
        if (!isOnMobile() && $('textarea').length > 0) {
            autosize($('textarea'));
        }

        app.menu();
        app.dropdowns();
        app.subdropdowns();

        // toggle history box
        $('.history').on('mouseenter', function () {
            $('.history-box').show();
        }).on('mouseleave', function () {
            $('.history-box').hide();
        });

        app.misc();
        app.profileTabs();

        $('.btn-menu-mobile').on('click', function () {
            $('.user-bar').toggle();
        });

        // Whenever one ofthe buttons on a table is clicked
        $('html .table-pager .btn').on('click', function() {
            if (typeof XIVDBTooltips !== 'undefined') {
                XIVDBTooltips.get();
            }
        });
    },

    enforceNumbers: function()
    {
        $('html').on('keyup', 'input[type="number"]', function(event) {
            var maxlength = $(this).attr('maxlength'),
                inputValue = $(this).val().substring(0, maxlength);

            $(this).val(inputValue);
        });
    },

    menu: function()
    {
        var timer = null;
        $('.menu > span').on('mouseenter', function()
        {
            var _this = this;
            timer = setTimeout(function() {
                $(_this).find('> .dropdown-container').show();
            },
            200);
        })
        .on('mouseleave', function()
        {
            clearTimeout(timer);
            $(this).find('> .dropdown-container').hide();
        });
    },

    dropdowns: function()
    {
        $('.dropdown-menu').menuAim({
            activate: function (row) {
                $('.dropdown-active').removeClass('dropdown-active');
                $(row).addClass('hover');
                $('#' + $(row).attr('data-submenu')).addClass('dropdown-active');
            },
            deactivate: function (row) {
                $(row).removeClass('hover');
                $('#' + $(row).attr('data-submenu')).removeClass('dropdown-active');
            },
        });
    },

    subdropdowns: function()
    {
        $('.dropdown-menu span').on('click', function() {
            var $node = $(this),
                $parent = $node.parents('.subdropdown-container'),
                id = $node.attr('data-submenu');

            $parent.find('.active').removeClass('active');
            $node.addClass('active');
            $('#sub-' + id).addClass('active');
        });
    },

    // misc stuff
    misc: function()
    {
        $('html').on('click', '.lang-close', function() {
            $('.language-selection').hide();
        });

        $('html').on('click', '.language-button', function() {
            $('.language-selection').show();
        });
    },

    // show loading or not
    loading: function(show)
    {
        var $loading = $('.loading');

        if (show) {
            $loading.fadeIn(100);
            return;
        }

        $loading.fadeOut(100);
    },

    profileTabs: function()
    {

    },
}

// run ini on ready
$(function() {
    app.init();
    Search.init();

    // unveil
    $('img').unveil();

    // temp analytic checking
    $.ajax({
        url: 'https://staging.xivapi.com/debug/xivdb',
        data: {
            route: window.location.pathname.trim().toLowerCase()
        }
    });
});
