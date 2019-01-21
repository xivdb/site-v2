//
// Search Filters
//
class SearchFiltersClass
{
    constructor()
    {
        this.filterParameters = {};
        this.filterItemAttributes = {};
        this.filterPanelActive = null;
        this.isFiltered = false;
    }

    //
    // Reset the filters
    //
    reset()
    {
        this.hidePanel();

        this.filterParameters = {};
        this.filterItemAttributes = {};
        this.filterPanelActive = null;

        // empty all filter params
        $(SearchSettings.get('filterParamClass')).find('input').val('');
        $(SearchSettings.get('filterParamClass'))
            .find('select').removeAttr('selected')
            .find('option:first').attr('selected', 'selected');

        // remove any active states
        $(SearchSettings.get('filterParamClass')).find('.active').removeClass('active');
        $(`${SearchSettings.get('filterParamClass')}.active`).removeClass('active');

        // empty item attributes
        SearchFiltersItemAttributes.reset();

        // empty class jobs
        SearchFiltersClassJobs.reset();
    }

    //
    // Reload some filters
    //
    reload(filters)
    {
        if (!filters) return;

        var filterObject = {}
            filterCategory = null,
            filterPage = null,
            filterItemAttributes = {},
            filterClassJobs = {};

        // go through filters and split them out
        // also grabs "the one"
        for(var i in filters) {
            var data = filters[i].split('=');

            // class jobz
            if (data[0] == 'classjobs') {
                filterClassJobs = data[1].split(',');
                continue;
            }

            // attributes
            if (data[0] == 'attributes') {
                filterItemAttributes = data[1].split(',');
                continue;
            }

            // one
            if (data[0] == 'one') {
                filterCategory = data[1];
                continue;
            }

            // one
            if (data[0] == 'page') {
                filterPage = data[1];
                continue;
            }

            // filters
            filterObject[data[0]] = data[1]
        }

        // if class job filters
        if (filterClassJobs)
        {
            this.isFiltered = true;
            var $cjPanel = $(`.filter-panel-${filterCategory} .filter-special-classjobs`);

            for(var i in filterClassJobs)
            {
                var cjId = filterClassJobs[i],
                    $btn = $cjPanel.find(`[data-id="${cjId}"]`);

                $btn.addClass('active');
                $btn.parents('.filter-param').addClass('active');
                SearchFiltersClassJobs.setFilter(filterCategory, cjId);
            }
        }

        // if item attribute filters
        if (filterItemAttributes)
        {
            this.isFiltered = true;

            var $fPanel = $(`.filter-panel-${filterCategory} .filter-special-attributes`);
            for(var i in filterItemAttributes)
            {
                var data = filterItemAttributes[i].split('|');

                // populate the form
                $fPanel.find('select.filter-param-attribute-1').val(data[0]);
                $fPanel.find('select.filter-param-attribute-2').val(data[1]);
                $fPanel.find('input.filter-param-attribute-3').val(data[2]);
                $fPanel.find('select.filter-param-attribute-4').val(data[3]);
                $fPanel.find('button.filter-param-button').trigger('click');
            }
        }

        // if object filters
        if (filterObject)
        {
            this.isFiltered = true;

            var $panel = $(`.filter-panel[data-filter-type="${filterCategory}"]`);
            for(var field in filterObject) {
                var value = filterObject[field],
                    $element = $panel.find(`[data-filter-field="${field}"]`);

                $element.val(value);
                $element.trigger('keyup').trigger('change');
            }
        }

        // set the one
        if (filterCategory)
        {
            this.isFiltered = true;
            Search.setOne(filterCategory);
        }

        // set the one
        if (filterPage)
        {
            this.isFiltered = true;
            Search.setPage(filterPage);
        }
    }

    //
    // Set the category to the filter button
    //
    setToggleButtonCategory(category)
    {
        $(SearchSettings.get('filterToggleClass'))
            .find('strong')
            .text(category.toUpperCase());

        $(SearchSettings.get('filterToggleClass'))
            .attr('data-category', category);
    }

    //
    // Toggle the filters
    //
    togglePanel(category)
    {
        $(`.filter-panel-${category}`).toggle().toggleClass('open');

        // Click general
        $(`.filter-panel-${category}`).find('[data-option="general"]').trigger('click');

        // update sticky nav
        SearchUI.updateStickyNav();
    }

    //
    // hide all filters
    //
    hidePanel()
    {
        $(`.filter-panel`).hide();

        // trigger click on general so that the default filters load correctly.
        $('.filter-panel-options button[data-option="general"]').trigger('click');
    }

    //
    // Handle a filter
    //
    handleFilter(event)
    {
        if (!event || typeof event.target === 'undefined') {
            console.error('No event or target assigned ...');
        }

        // get element
        var $element = $(event.currentTarget),
            $parent = $element.parents('.filter-param'),
            filterType = $element.parents('.filter-panel').attr('data-filter-type'),
            filterField = $element.attr('data-filter-field'),
            filterValue = $element.val();

        // Numeric check
        if ($.isNumeric(filterValue)) {
            filterValue = parseInt(filterValue);
        }

        // if editing a different filter panel than the one active, reset filters
        if (filterValue && this.filterPanelActive && this.filterPanelActive != filterType) {
            this.setFilterType(filterType);
        }

        // force set based on filter value
        var isFilterActive = false;
        $parent.find('input, select').each((i, element) => {
            if ($(element).val() && $(element).val().length > 0) {
                isFilterActive = true;
            }
        });

        // if filter active, set active class
        isFilterActive
            ? $parent.addClass('active')
            : $parent.removeClass('active');

        // if filter is active, append into filterParameters, otherwise delete
        filterValue.toString().length > 0
            ? this.setFilterParameter(filterField, filterValue)
            : this.deleteFilterParameter(filterField);

        if (this.filterParameters || this.filterItemAttributes) {
            this.isFiltered = true;
        }
    }

    //
    // Set a filter parameter
    //
    setFilterParameter(key, value)
    {
        this.filterParameters[key] = value;
        SearchBuilder.add(key, value);
    }

    //
    // Handle a checkbox filter
    //
    handleCheckboxFilter(event)
    {
        var $element = $(event.currentTarget),
            $parent = $element.parents('.filter-param'),
            isFilterActive = $element.prop('checked'),
            filterType = $element.parents('.filter-panel').attr('data-filter-type'),
            filterField = $element.attr('data-filter-field');

        // if editing a different filter panel than the one active, reset filters
        if (this.filterPanelActive && this.filterPanelActive != filterType) {
            this.setFilterType(filterType);
        }

        // if filter active, set active class
        isFilterActive ? $parent.addClass('active') : $parent.removeClass('active');

        // if filter is active, append into filterParameters, otherwise delete
        isFilterActive ?
            this.setFilterParameter(filterField, 'true') :
            this.deleteFilterParameter(filterField);

        if (this.filterParameters || this.filterItemAttributes) {
            this.isFiltered = true;
        }
    }

    //
    // Set a filter type
    //
    setFilterType(filterType)
    {
        SearchBuilder.reset();

        this.filterParameters = {};
        this.filterItemAttributes = {};
        this.filterPanelActive = null;

        this.resetFilterParameters();
        this.setActiveFilterPanel(filterType);
    }

    //
    // Delete a filter parameter
    //
    deleteFilterParameter(key)
    {
        delete this.filterParameters[key];
        SearchBuilder.remove(key);
    }

    //
    // Reset filters
    //
    resetFilterParameters()
    {
        this.filterParameters = {};
    }

    //
    // Set active filter panel
    //
    setActiveFilterPanel(type)
    {
        this.filterPanelActive = type;
    }

    //
    // Handle filter activation
    //
    isFilterActivated($parent)
    {
        var numOfPopulatedChoices = 0;

        $parent.find('input').each(function() {
            if ($(this).val()) {
                numOfPopulatedChoices++;
            }
        });

        $parent.find('select').each(function() {
            if ($(this).val()) {
                numOfPopulatedChoices++;
            }
        });

        return numOfPopulatedChoices > 0 ? true : false;
    }

    //
    // Is filtered search
    //
    isFilteredSearch()
    {
        return this.isFiltered;
    }
}

var SearchFilters = new SearchFiltersClass();
