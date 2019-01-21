//
// Search UI
//
class SearchUIClass
{
    constructor()
    {
        this.start = 0;
        this.view = 'grid';
    }

    //
    // Hide all active panels
    //
    hideAllActivePanels(exclude)
    {
        if (exclude != 'cart') {
            CartUI.close();
        }

        if (exclude != 'wardrobe') {
            WardrobeUI.close();
        }

        if (exclude != 'options') {
            $(SearchSettings.get('searchToggleOptionsClass')).removeClass('active');
            $('.search-options').removeClass('open');
        }

        if (exclude != 'tools') {
            $(SearchSettings.get('searchToggleToolsClass')).removeClass('active');
            $('.search-tools').removeClass('open');
        }

        return this;
    }

    //
    // Open a search panel
    //
    togglePanel(className)
    {
        $(className).toggleClass('open');
        return this;
    }

    //
    // Scroll to the search
    //
    scrollToSearch()
    {
        $('html, body').scrollTop($(SearchSettings.get('searchHeaderClass')).offset().top);
        return this;
    }

    //
    // Get the text in the search input
    //
    getInputText()
    {
        return $(SearchSettings.get('searchInputClass')).val().trim();
    }

    //
    // Reset the text input
    //
    resetInputText()
    {
        $(SearchSettings.get('searchInputClass')).val('');
        return this;
    }

    //
    // Toggle the Search Loading popup
    //
    toggleSearchLoader(isShow)
    {
        if (isShow) {
            $(SearchSettings.get('searchLoaderClass')).show();
            return this;
        }

        $(SearchSettings.get('searchLoaderClass')).hide();
        return this;
    }

    //
    // Show search results
    //
    showSearchResults()
    {
        $(SearchSettings.get('siteContentsClass')).hide();
        $(SearchSettings.get('searchContentClass')).show();
        return this;
    }

    //
    // Start timer
    //
    setTimer()
    {
        this.start = Date.now();
        return this;
    }

    //
    // Show results info
    //
    showResultsInfo(found, total)
    {
        found = number_format(found);
        total = number_format(total);

        $('.search-numbers').html(SearchTemplater.templateSearchInfo1({
            total: total,
            found: found,
        }));

        return this;
    }

    //
    // Change the search view
    //
    setSearchView(view)
    {
        // if no view passed, try loading from local storage
        if (!view) {
            view = localStorage.getItem('searchView', view);
            if (view) {
                this.setSearchView(view);
            }
            return this;
        }

        // change view
        var $section = $(SearchSettings.get('searchResultsClass'));
        $section.removeClass('grid').removeClass('list');
        $section.addClass(view);

        // set buttons
        $(SearchSettings.get('searchHeaderClass')).find('button[data-search-view].active').removeClass('active');
        $(SearchSettings.get('searchHeaderClass')).find(`[data-search-view="${view}"]`).addClass('active');

        localStorage.setItem('searchView', view);
        this.view = view;
        return this;
    }

    //
    // Set filter view
    //
    setFilterView()
    {
        return this;
    }

    //
    // Get the current searchview
    //
    getSearchView()
    {
        return this.view;
    }

    //
    // Show the no results banner
    //
    showNoResults()
    {
        $(SearchSettings.get('searchResultsNavClass')).hide();
        $(SearchSettings.get('searchResultsClass')).hide();
        $(SearchSettings.get('searchNoResultsClass')).show();
        return this;
    }

    //
    // Hide the no results banner
    //
    hideNoResults()
    {
        $(SearchSettings.get('searchResultsNavClass')).show();
        $(SearchSettings.get('searchResultsClass')).show();
        $(SearchSettings.get('searchNoResultsClass')).hide();
        return this;
    }

    //
    // Toggle extended filters
    //
    toggleExtendedFilters(enable)
    {
        if (enable)
        {
            $('.filter-panel-options').hide();
            $('.filter-panel-tabs').addClass('active');
        }
        else
        {
            $('.filter-panel-options').show();
            $('.filter-panel-tabs').removeClass('active');
            $('.filter-panel-options button:first-of-type').addClass('active');
            $('.filter-panel-tabs[data-tab="general"]').addClass('active');
        }
    }

    // -------------------------------------------------------
    // Sticky Nav
    // -------------------------------------------------------

    //
    // Set sticky nav
    //
    setStickyNav()
    {
        if (!isOnMobile() && SearchSettings.get('searchStickyNav') == 'on') {
            $(SearchSettings.get('searchInteractionClass')).sticky({
                topSpacing: 0,
                zIndex: 100,
            });
        }
        return this;
    }

    //
    // Update sticky
    //
    updateStickyNav()
    {
        if (isOnMobile()) {
            return this;
        }

        $(SearchSettings.get('searchInteractionClass')).sticky('update');
        return this;
    }

    //
    // Remove sticky
    //
    removeStickyNav()
    {
        if (isOnMobile()) {
            return this;
        }

        $(SearchSettings.get('searchInteractionClass')).unstick();
        return this;
    }

}

var SearchUI = new SearchUIClass();
