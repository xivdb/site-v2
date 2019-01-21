//
// Search Render
//
class SearchRenderClass
{
    constructor()
    {
        this.activeTab = null;
        this.tooltipsTimer = null;
    }
    //
    // Reset search display
    //
    reset()
    {
        $(SearchSettings.get('searchResultsClass')).empty().html('');
        $(SearchSettings.get('searchResultsNavClass')).empty().html('');
        this.activeTab = null;
    }

    //
    // Reset the active tab
    //
    resetActiveTab()
    {
        this.activeTab = null;
    }

    //
    // Set a tab active
    //
    setActiveTab(category)
    {
        if (category == this.activeTab) {
            return;
        }

        this.activeTab = category;

        // remove any active tabs
        $(SearchSettings.get('searchResultsNavClass')).find('span[data-category].active').removeClass('active');
        $(SearchSettings.get('searchResultsClass')).find('.search-results-group.active').removeClass('active');
        $(SearchSettings.get('searchPagingContainerClass')).find('.search-one-paging.active').removeClass('active');

        // set tab active
        $(SearchSettings.get('searchResultsNavClass')).find(`span[data-category="${category}"]`).addClass('active');
        $(SearchSettings.get('searchResultsClass')).find(`.search-results-group-${category}`).addClass('active');
        $(SearchSettings.get('searchPagingContainerClass')).find(`.search-one-paging[data-category="${category}"]`).addClass('active');

        // set filter toggle button
        SearchFilters.setToggleButtonCategory(category);

        // get tooltips, delay to give time for render to become visible in browser
        clearTimeout(this.tooltipsTimer);
        this.tooltipsTimer = setTimeout(() => {
            if (typeof XIVDBTooltips !== 'undefined') {
                XIVDBTooltips.get();
                SearchUI.updateStickyNav();
            }
        }, 100);

        // if to hide patch list or not
        (category == 'characters')
            ? $('.filter-param-patches').hide()
            : $('.filter-param-patches').show();
    }

    //
    // Render a search tab
    //
    renderSearchTab(category, title, total)
    {
        var id = SearchSettings.get('searchResultsNavClass'),
            html = SearchTemplater.templateSearchTab({
                category: category,
                title: title,
                total: number_format(total)
            });

        // render search tabs
        $(id).append(html);
    }

    //
    // Render a search group
    //
    renderSearchGroup(category)
    {
        $(SearchSettings.get('searchResultsClass')).append(`<div class="search-results-group search-results-group-${category}"></div>`);
    }

    //
    // Render search results entities
    //
    renderSearchResults(category, results)
    {
        var id = `.search-results-group-${category}`,
            html = [];

        for(var i in results)
        {
            if (typeof SearchContent[category] !== 'undefined') {
                var data = SearchContent[category](results[i]);
                html.push(SearchTemplater.templateSearchEntity(data));
            }
        }

        $(id).html(html.join(''));
    }
}

var SearchRender = new SearchRenderClass();
