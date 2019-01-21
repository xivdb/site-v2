//
// Search Settings
//
class SearchSettingsClass
{
    constructor()
    {
        this.settings =
        {
            searchAutoEnabled: 'on',
            searchInputDelay: 1000,
            searchResultsLimit: 60,
            searchStickyNav: 'on',
            searchFlexbox: 'on',
            searchStrict: 'off',
            searchInputClass: '.search input.core-search',
            searchInputGlass: '.search button',
            searchLoaderClass: '.search-loading',
            searchContentClass: '.search-content',
            searchResultsClass: '.search-results',
            searchResultsNavClass: '.search-results-nav',
            searchHeaderClass: '.search-header',
            searchPagingContainerClass: '.search-paging',
            searchViewClass: '.search-view-options > button',
            searchNoResultsClass: '.search-no-results',
            searchExtendedToggle: '.search-filter-options > button.filter-extend',
            searchInteractionClass: '.search-interaction',
            searchToggleToolsClass: '.search-header button#toggleSearchTools',
            searchToggleOptionsClass: '.search-header button#toggleSearchOptions',

            filterToggleClass: '.filter-toggle',
            filterParamClass: '.filter-param',
            filterItemAttributesClass: '.filter-special-attributes',
            filterClassJobsClass: '.filter-special-classjobs',
            filterSearchButtonClass: '.filter-panel-footer .filter-search',
            filterClearButtonClass: '.filter-panel-footer .filter-reset',

            filterPanelOptions: '.filter-panel-options',
            filterPanelTabs: '.filter-panel-tabs',

            siteContentsClass: '.site-container',
            attributeHQIcon: '/img/ui/hq.png',
        }
    }

    //
    // Get a search setting
    //
    get(option)
    {
        return this.settings[option];
    }

    //
    // Set an option
    //
    set(option, value)
    {
        if (value) {
            this.settings[option] = value;
        }

        return;
    }
}

var SearchSettings = new SearchSettingsClass();
