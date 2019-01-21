//
// Search
//
class SearchClass
{
    constructor()
    {
        this.resultsFound = 0;
        this.resultsTotal = 0;
        this.firstCategory = null;
        this.page = 0;
        this.start = null;
        this.theone = 'All';
        this.timestamp = null;
    }

    //
    // Reset search
    //
    reset()
    {
        this.resultsFound = 0;
        this.resultsTotal = 0;
        this.firstCategory = null;
        this.page = 0;
        this.start = null;
        //this.theone = 'All'; - Commented out as I can run queries multiple times.
    }

    //
    // Init
    //
    init()
    {
        this.checkSearchOptions();
        SearchUI.setStickyNav();
        return this;
    }

    //
    // Check search options against user defined ones
    //
    checkSearchOptions()
    {
        // Auto-Search Delay
        SearchSettings.set('searchInputDelay', localStorage.getItem('searchInputDelay'))
        $('#searchInputDelay').val(SearchSettings.get('searchInputDelay'));

        // Auto-Search Enabled
        SearchSettings.set('searchAutoEnabled', localStorage.getItem('searchAutoEnabled'))
        $('#searchAutoEnabled').val(SearchSettings.get('searchAutoEnabled'));

        // Search Limit
        SearchSettings.set('searchResultsLimit', localStorage.getItem('searchResultsLimit'))
        $('#searchResultsLimit').val(SearchSettings.get('searchResultsLimit'));

        // Auto-Search Enabled
        SearchSettings.set('searchStickyNav', localStorage.getItem('searchStickyNav'))
        $('#searchStickyNav').val(SearchSettings.get('searchStickyNav'));

        // Stick search enabled
        SearchSettings.set('searchStrict', localStorage.getItem('searchStrict'))
        $('#searchStrict').val(SearchSettings.get('searchStrict'));
        return this;
    }

    //
    // Run an search query
    //
    runQuery()
    {
        ttdrop();

        // always set string
        this.setString();

        // begin
        SearchUI.setTimer();
        SearchUI.toggleSearchLoader(true);
        SearchUI.setSearchView();
        SearchUI.setFilterView();
        SearchTitle.preShow('Searching...');
        SearchEvents.setSearchInputState();
        SearchRender.resetActiveTab();

        this.reset();
        var _this = this;

        data = SearchBuilder.get();
        data['language'] = LANGUAGE;
        data['limit'] = SearchSettings.get('searchResultsLimit');
        data['strict'] = SearchSettings.get('searchStrict');

        if (Object.keys(SearchFilters.filterParameters).length == 0) {
            $('.filter-notice').removeClass('active');
            $('.filter-toggle').removeClass('on');
        } else {
            $('.filter-notice').addClass('active');
            $('.filter-toggle').addClass('on');
        }

        $.ajax({
            url: `${API_URL}/search`,
            data: data,
            cache: false,
            dataType: 'json',
            method: 'GET',
            success: function(results)
            {
                _this.render(results);
            },
            error: function(data, status, error)
            {
                console.error(data, status, error);
            },
            complete: function()
            {
                // Toggle stuff
                SearchUI
                    .toggleSearchLoader()
                    .showSearchResults()
                    .setStickyNav()
                    .updateStickyNav()
                    .scrollToSearch();

                SearchUrl.set();
            }
        });

        return this;
    }

    //
    // Render results
    //
    render(results)
    {
        // hide tooltips
        if (typeof XIVDBTooltips !== 'undefined') {
            XIVDBTooltips.hide();
        }

        ttdrop();

        // reset search
        SearchRender.reset();
        SearchPaging.reset();

        // loop through results
        for(var category in results)
        {
            // ensure lower case
            category = category.toLowerCase();

            // set data and total
            var data = results[category].results,
                paging = results[category].paging,
                total = parseInt(results[category].total);

            // ensure we have results
            if (data && data.length > 0)
            {
                // increase to total results
                this.resultsFound = this.resultsFound + parseInt(data.length);
                this.resultsTotal = this.resultsTotal + parseInt(total);

                // set the first category
                this.firstCategory = !this.firstCategory ? category : this.firstCategory;

                // set title
                var title = ucwords(category),
                    title = title == 'Npcs' ? 'NPCs' : title;

                // render
                SearchRender.renderSearchTab(category, title, total);
                SearchRender.renderSearchGroup(category);
                SearchRender.renderSearchResults(category, data);

                // render paging
                SearchPaging.renderPaging(category, paging, total, data.length);

                // show first category
                SearchRender.setActiveTab(this.firstCategory);
            }
        }

        // finished
        SearchUI.showResultsInfo(this.resultsFound, this.resultsTotal);

        // set title
        var searchTitle = null;
        if (SearchBuilder.get('one'))
        {
            searchTitle = SearchBuilder.get('string')
                ? `${SearchBuilder.get('string')} - ${number_format(this.resultsTotal)} ${ucwords(SearchBuilder.get('one'))}`
                : `${number_format(this.resultsTotal)} ${ucwords(SearchBuilder.get('one'))}`;
        }
        else
        {
            searchTitle = SearchBuilder.get('string')
                ? `${SearchBuilder.get('string')} - ${number_format(this.resultsTotal)} Results`
                : `${number_format(this.resultsTotal)} Results`;
        }


        SearchTitle.set(`${searchTitle} - Search`);
        SearchTitle.show();

        // if no results show no results banner
        // otherwise hide banner
        this.resultsTotal == 0
            ? SearchUI.showNoResults()
            : SearchUI.hideNoResults();

        // if cart open, reattach drag
        if (Cart.isOpen()) {
            CartUI.setIconsDraggable();
        }


        // if cart wardrobe, reattach drag
        if (Wardrobe.isOpen()) {
            WardrobeUI.setIconsDraggable();
        }

        // load icons
        setTimeout(() => {
            $('.entity img').unveil();
            $(window).trigger('lookup');
        }, 250)
    }

    //
    // Add string search, if it exists
    //
    setString()
    {
        var value = SearchUI.getInputText();
        if (value && value.length > 0) {
            SearchBuilder.add('string', value);
        }

        return this;
    }

    //
    // Set One
    //
    setOne(value)
    {
        this.theone = value;
        SearchBuilder.add('one', value);
        return this;
    }

    //
    // Set pagek
    //
    setPage(value)
    {
        SearchBuilder.add('page', value);
        return this;
    }

    //
    // Add a page
    //
    setOrder(value)
    {
        SearchBuilder.add('order', value);
        return this;
    }
}

var Search = new SearchClass();
