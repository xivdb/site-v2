//
// Search Filter Item Attributes
//
class SearchFiltersClassJobsClass
{
    constructor()
    {
        this.parameters = {};
    }

    //
    // Reset class jobs view
    //
    reset()
    {
        this.parameters = {};
    }

    //
    // Handle class job filter (the special case)
    //
    handleClassJobFilter(event)
    {
        var $button = $(event.currentTarget),
            classId = $button.attr('data-id');

        // if exists, remove it
        if (typeof this.parameters[classId] !== 'undefined') {
            $button.removeClass('active');
            this.removeFilter(classId);

            // if no buttons active, make param non active
            var anyActiveButtons = $button.parents(SearchSettings.get('filterParamClass')).find('button.active').length;
            if (!anyActiveButtons) {
                $button
                    .parents(SearchSettings.get('filterParamClass'))
                    .removeClass('active');
            }

            return;
        }

        // set filter type
        var filterType = $button.parents('.filter-panel-items').attr('data-filter-type');

        // add to param list
        this.setFilter(filterType, classId);

        // make button active
        $button.addClass('active');

        // make param block active
        $button
            .parents(SearchSettings.get('filterParamClass'))
            .addClass('active');
    }

    //
    // Set a class job parameter
    //
    setFilter(filterType, id)
    {
        this.parameters[id] = id;
        this.attachFilters(filterType);
    }

    //
    // Remove a filter
    //
    removeFilter(id)
    {
        delete this.parameters[id];
        this.attachFilters();
    }

    //
    // Attach filters to main search
    //
    attachFilters(filterType)
    {
        var classjobs = this.parameters,
            classjobs = Object.toArray(classjobs);

        // if no classjobs, delete them
        if (classjobs.length == 0) {
            SearchFilters.deleteFilterParameter('classjobs');
            this.reset();
            return;
        }

        SearchFilters.setActiveFilterPanel(filterType);
        SearchFilters.setFilterParameter('classjobs', classjobs.join(','));
    }

}

var SearchFiltersClassJobs = new SearchFiltersClassJobsClass();