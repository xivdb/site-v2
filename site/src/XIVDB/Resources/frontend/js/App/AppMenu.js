/**
 * Menu class
 */
class AppMenuClass
{
    watch()
    {
        $('.user-menu-open').on('click', event => {
            $('.mobile-menu').addClass('active');
        });

        $('.user-menu-close').on('click', event => {
            $('.mobile-menu').removeClass('active');
        });
    }
}

$(function() {
    var AppMenu = new AppMenuClass();
    AppMenu.watch();
});

//