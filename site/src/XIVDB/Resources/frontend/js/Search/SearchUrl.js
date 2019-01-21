//
// Search Url
//
class SearchUrlClass
{
    constructor()
    {
        this.search = null;
        this.filters = null;
        this.hasPushed = false;
        this.popReady = false;

        // popstate, don't do it on mobile
        if (!isOnMobile()) {
            window.onpopstate = function(event) {
                if (this.popReady) {
                    // force page reload so filters pre-populate
                    window.location = window.location.href;
                }
            };
        }
    }

    //
    // Reset the url
    //
    reset()
    {
        window.history.replaceState(null, null, `/`);
    }

    //
    // Set the url
    //
    set()
    {
        var parameters = SearchBuilder.get(),
            string = parameters.string;

        delete parameters['language'];

        // split spaces by pluses
        string = string ? string.replace(/ /g, '+') : null;

        // Remove string as it will be added as a different parameter
        delete parameters.string;

        // serialise the object
        var serialise = $.param(parameters);

        // build Url
        var url = this.buildUrl(string, serialise);

        // pop ready
        this.popReady = true;
    }

    //
    // Set search
    //
    setSearch(search)
    {
        this.search = search;
    }

    //
    // Set filters
    //
    setFilters(filters)
    {
        this.filters = filters;
    }

    //
    // Set initial state
    //
    setInitialState()
    {
        Search.runQuery();
    }

    //
    // Build search url
    //
    buildUrl(string, filters)
    {
        // setup url parameters
        var urlParameters = [];

        // if string
        if (string && string.length > 0) {
            string = string ? string.replace(/ /g, '+') : null;
            urlParameters.push(`search=${string}`);
        }

        // if filters
        if (filters) {
            filters = btoa(unescape(encodeURIComponent(filters)), true)
            urlParameters.push(`filters=${filters}`);
        }

        // prepare the url string
        var urlString = urlParameters.join('&'),
            urlString = (urlString.length > 1) ? `/?${urlString}` : '/';

        // on first one just replace
        if (!this.hasPushed) {
            this.hasPushed = true;
            return window.history.replaceState(null, null, urlString);
        }

        // push history
        window.history.pushState(null, null, urlString);
    }

    //
    // Reload values
    //
    reloadSearchValues()
    {
        if (this.search)
        {
            $(SearchSettings.get('searchInputClass')).val(this.search);
            Search.setString();
        }

        if (this.filters)
        {
            var filters = this.filters;

            // base64 > escape > decode
            filters = decodeURIComponent(escape(atob(filters, true)));

            // replace all left over encodes
            filters = decodeURIComponent(filters);

            // split
            filters = filters.split('&');

            // reload filters
            SearchFilters.reload(filters);
        }

        // If either search or filters
        if (this.search || this.filters)
        {
            // Search
            Search.runQuery();
        }
    }
}

var SearchUrl = new SearchUrlClass();
