//
// Search Event
//
class SearchEventsClass
{
    constructor()
    {
        this.searchInputTimer = null;
        this.searchInputState = null;
    }

    //
    // Watch the search input text box
    //
    watchSearchInput()
    {
        var _this = this;
        $(SearchSettings.get('searchInputClass')).on('keyup', event =>
        {
            if (event.keyCode == 13) {
                // set search input and fetch results
                _this.searchInputState = SearchUI.getInputText();
                Search.setPage(1);
                Search.setString();
                Search.runQuery();
                return;
            }

            // if disabled, don't do anything
            if (SearchSettings.get('searchAutoEnabled') == 'off') {
                return;
            }

            // don't auto search on mobile, too laggy!
            if (isOnMobile()) {
                return;
            }

            // clear any existing timer
            clearTimeout(_this.searchInputTimer);

            // has input not changed? this helps prevent random
            // keyups from trigger a search
            if (_this.searchInputState == SearchUI.getInputText() || SearchUI.getInputText().length == 0) {
                return;
            }

            // set timeout
            _this.searchInputTimer = setTimeout(() => {
                // set search input and fetch results
                _this.searchInputState = SearchUI.getInputText();
                Search.setPage(1);
                Search.setString();
                Search.runQuery();
            },
            SearchSettings.get('searchInputDelay'));
        });
    }

    //
    // Set search input state
    //
    setSearchInputState()
    {
        this.searchInputState = SearchUI.getInputText();
    }

    //
    // Watch the search button mag-glass
    //
    watchSearchButton()
    {
        //
        // the search icon in the search input
        //
        $(SearchSettings.get('searchInputGlass')).on('click', event =>
        {
            Search.setPage(1);
            Search.setString();
            Search.runQuery();
        });

        //
        // the search button on filters
        //
        $(SearchSettings.get('filterSearchButtonClass')).on('click', event =>
        {
            // set filter one
            var type = $(event.currentTarget).parents('.filter-panel').attr('data-filter-type');
            Search.setOne(type);

            Search.setPage(1);
            Search.setString();
            Search.runQuery();
        });

        //
        // the reset button on filters
        //
        $(SearchSettings.get('filterClearButtonClass')).on('click', event =>
        {
            // get "the one"
            var theone = Search.theone;

            SearchUrl.reset();
            SearchBuilder.reset();
            SearchFilters.reset();

            // reset the one
            //Search.setOne(theone);

            // search
            SearchUI.resetInputText();
            SearchUI.updateStickyNav();
            Search.runQuery();
        });

        //
        // search view buttons
        //
        $(SearchSettings.get('searchViewClass')).on('click', event =>
        {
            var view = $(event.currentTarget).attr('data-search-view');
            SearchUI.setSearchView(view);
        });
    }

    //
    // Watch tab nav click
    //
    watchSearchTabs()
    {
        // switch search tab
        var element = `${SearchSettings.get('searchResultsNavClass')} > span[data-category]`;
        $('.site').on('click', element, event =>
        {
            // change category
            var category = $(event.currentTarget).attr('data-category');
            $(SearchSettings.get('filterToggleClass')).removeClass('active');
            SearchRender.setActiveTab(category);

            // hide filters
            SearchFilters.hidePanel(category);

            // if extended filters are enable, ensure they load properly
            var view = localStorage.getItem('searchExtendedToggle');
            if (view && view == 'true') {
                SearchUI.toggleExtendedFilters(true);
            }

            setTimeout(() => {
                SearchUI.updateStickyNav();
                $(window).trigger('lookup');
            }, 50);
        });
    }

    //
    // Watch search options
    //
    watchSearchOptions()
    {
        // Toggle options panel
        $(SearchSettings.get('searchToggleOptionsClass')).on('click', event => {
            SearchUI
                .hideAllActivePanels('options')
                .togglePanel('.search-options')
                .updateStickyNav();

			if ($('.search-options').hasClass('open')) {
				$(SearchSettings.get('searchToggleOptionsClass')).addClass('active');
			} else {
				$(SearchSettings.get('searchToggleOptionsClass')).removeClass('active');
			}
        });

        // Toggle tools panel
        $(SearchSettings.get('searchToggleToolsClass')).on('click', event => {
            SearchUI
                .hideAllActivePanels('tools')
                .togglePanel('.search-tools')
                .updateStickyNav();

			if ($('.search-tools').hasClass('open')) {
				$(SearchSettings.get('searchToggleToolsClass')).addClass('active');
			} else {
				$(SearchSettings.get('searchToggleToolsClass')).removeClass('active');
			}
        });

        // Auto search delay change
        $('#searchInputDelay').on('keyup', event => {
            var value = parseInt($(event.target).val());

            // ensure if valud
            if (value && value > 50) {
                SearchSettings.set('searchInputDelay', value);
                localStorage.setItem('searchInputDelay', value);
            }
        });

        // Auto search delay change
        $('#searchAutoEnabled').on('change', event => {
            var value = $(event.target).val();
            SearchSettings.set('searchAutoEnabled', value);
            localStorage.setItem('searchAutoEnabled', value);
        });

        // Auto search delay change
        $('#searchResultsLimit').on('change', event => {
            var value = parseInt($(event.target).val());

            if (value >= 15 && value <= 90) {
                SearchSettings.set('searchResultsLimit', value);
                localStorage.setItem('searchResultsLimit', value);
            }
        });

        // Sticky nav change
        $('#searchStickyNav').on('change', event => {
            var value = $(event.target).val();
            SearchSettings.set('searchStickyNav', value);
            localStorage.setItem('searchStickyNav', value);
            (value == 'on')
                ? SearchUI.setStickyNav().updateStickyNav()
                : SearchUI.removeStickyNav();
        });

        // Sticky nav change
        $('#searchStrict').on('change', event => {
            var value = $(event.target).val();
            SearchSettings.set('searchStrict', value);
            localStorage.setItem('searchStrict', value);
        });
    }

    //
    // Watch filter button click
    //
    watchFilterToggle()
    {
        $(SearchSettings.get('filterToggleClass')).on('click', event => {
            var activeCategory = $(event.currentTarget).attr('data-category');
            $(event.currentTarget).toggleClass('active');

            SearchFilters.togglePanel(activeCategory);
            SearchUI.updateStickyNav();
        });
    }

    //
    // Watch filters
    //
    watchFilterValues()
    {
        // normal filters
        $(SearchSettings.get('searchHeaderClass')).on('click', 'input[type="checkbox"]', event => { event.stopPropagation(); SearchFilters.handleCheckboxFilter(event) });
        $(SearchSettings.get('searchHeaderClass')).on('keyup', 'input[data-filter-field]', event => { event.stopPropagation(); SearchFilters.handleFilter(event) });
        $(SearchSettings.get('searchHeaderClass')).on('change', 'select[data-filter-field]', event => { event.stopPropagation(); SearchFilters.handleFilter(event) });

        // special: attribute filters
        $(SearchSettings.get('filterItemAttributesClass')).on('click', 'button.filter-param-button', event => { event.stopPropagation(); SearchFiltersItemAttributes.handleItemAttributeFilter(event) });
        $(SearchSettings.get('filterItemAttributesClass')).on('click', 'span.filter-param-attribute-block', event => { event.stopPropagation(); SearchFiltersItemAttributes.handleItemAttributeFilterRemoval(event) });

        // special: job class filters
        $(SearchSettings.get('filterClassJobsClass')).on('click', 'button', event => { event.stopPropagation(); SearchFiltersClassJobs.handleClassJobFilter(event) });
    }

    //
    // Watch filter tabs
    //
    watchFilterTabs()
    {
        $(SearchSettings.get('filterPanelOptions')).on('click', 'button', event =>
        {
            // get tab option
            var $button = $(event.currentTarget),
                tab = $button.attr('data-option');

            // hide active tab
            $button.parents('.filter-panel').find(`${SearchSettings.get('filterPanelTabs')}.active`).removeClass('active');
            $(SearchSettings.get('filterPanelOptions')).find('button.active').removeClass('active');

            // set new tab active
            $button.parents('.filter-panel').find(`${SearchSettings.get('filterPanelTabs')}[data-tab="${tab}"]`).addClass('active');
            $button.addClass('active');
            SearchUI.updateStickyNav();
        });
    }

    //
    // Watch paging buttons
    //
    watchPagingButtons()
    {
        $(SearchSettings.get('searchPagingContainerClass')).on('click', 'button', event =>
        {
            // get tab option
            var $button = $(event.currentTarget),
                page = $button.attr('data-page'),
                category = $button.parents('.search-one-paging').attr('data-category');

            // if page and category
            if (parseInt(page) && category) {
                Search
                    .setOne(category)
                    .setPage(page)
                    .runQuery();
            }
        });
    }

    //
    // Watch for an existing search
    //
    watchExistingSearch()
    {
        let search = getParameterByName('search'),
            filters = getParameterByName('filters');

        if (search) {
            SearchUrl.setSearch(search);
        }

        if (filters) {
            SearchUrl.setFilters(filters);
        }

        if (search || filters) {
            SearchUrl.reloadSearchValues();
        }
    }
}

var SearchEvents = new SearchEventsClass();

// initialise search events
$(function()
{
    SearchEvents.watchSearchButton();
    SearchEvents.watchSearchInput();
    SearchEvents.watchSearchTabs();
    SearchEvents.watchSearchOptions();

    SearchEvents.watchFilterToggle();
    SearchEvents.watchFilterValues();
    SearchEvents.watchFilterTabs();

    SearchEvents.watchPagingButtons();
    SearchEvents.watchExistingSearch();
});
