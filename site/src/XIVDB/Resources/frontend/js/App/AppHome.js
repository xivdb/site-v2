//
// Home functionality
//
class AppHomeClass
{
    constructor()
    {
        this.bannerValue = 1;
        this.bannerSize = 1;
        this.bannerTimeout = null;
    }

    //
    // initBanners
    //
    initBanners(size)
    {
        var $container = $('.home-banners'),
            $active = $container.find('a.active'),
            $next = $active.next(),
            number = $active.attr('data-banner');

        // if number is same as size, reset
        if (number == size) {
            $next = $container.find('a[data-banner="1"]');
        }

        this.bannerSize = parseInt(size);
        this.bannerValue = parseInt(number);

        $container.find('.controls span em').text(this.bannerValue);

        // timer
        this.bannerTimeout = setTimeout(() => {
            $active.removeClass('active');
            $next.addClass('active');
            this.initBanners(size);
        }, 4000);
    }

    //
    // Go to the next banner
    //
    nextBanner()
    {
        // clear timeout
        clearTimeout(this.bannerTimeout);

        // get next value
        var number = parseInt(this.bannerValue) + 1;
        if (number > this.bannerSize) {
            number = 1;
        }

        // get next one
        $element = $('.home-banners').find(`a[data-banner="${number}"]`);
        $active  = $('.home-banners a.active');

        // switch it out
        $active.removeClass('active');
        $element.addClass('active');

        this.initBanners(this.bannerSize);
    }

    //
    // Go to the previous banner
    //
    prevBanner()
    {
        // clear timeout
        clearTimeout(this.bannerTimeout);

        // get next value
        var number = parseInt(this.bannerValue) - 1;
        if (number < 1) {
            number = parseInt(this.bannerSize);
        }

        // get next one
        $element = $('.home-banners').find(`a[data-banner="${number}"]`);
        $active  = $('.home-banners a.active');

        // switch it out
        $active.removeClass('active');
        $element.addClass('active');

        this.initBanners(this.bannerSize);
    }

    //
    // Init Tabs
    //
    initTabs()
    {
        // check local storage
        if (localStorage.getItem('homePosts')) {
            var tab = localStorage.getItem('homePosts');
            AppHome.setHomePostTab(tab);
        }

        // on button click
        $('.home-tabs').on('click', 'nav button[data-tab]', event => {
            var tab = $(event.currentTarget).attr('data-tab');
            localStorage.setItem('homePosts', tab);
            AppHome.setHomePostTab(tab);
        });
    }

    //
    // Set the tabe for the home posts
    //
    setHomePostTab(tab)
    {
        // unactive
        $('.home-tabs .active').removeClass('active');

        // set new tab active
        $('.home-tabs')
            .find(`div.home-tabs-contents[data-tab="${tab}"]`)
            .addClass('active');

        $('.home-tabs')
            .find(`nav button[data-tab="${tab}"]`)
            .addClass('active');
    }

    rotateScreenshots()
    {
        var $images = $('.home-screenshots'),
            $active = $images.find('a.active'),
            $next = $active.next().length ? $active.next() : $images.find('a').eq(0);

        setTimeout(() => {
            $active.removeClass('active');
            $next.addClass('active');
            this.rotateScreenshots();
        }, 3000);

    }
}

var AppHome = new AppHomeClass();
