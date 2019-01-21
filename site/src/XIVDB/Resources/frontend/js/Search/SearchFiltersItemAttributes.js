//
// Search Filter Item Attributes
//
class SearchFiltersItemAttributesClass
{
    constructor()
    {
        this.parameters = {};
    }

    //
    // Reset item attributes
    //
    reset()
    {
        this.parameters = {};
        this.toggleAttributeFilters();

        $('.filter-special-attributes-set').empty().html('');
        $('.filter-param-attribute-3').val('1');
    }

    //
    // Handle item attributes filter (the special case)
    //
    handleItemAttributeFilter(event)
    {
        var name = $('.filter-param-attribute-1 option:selected').text(),
            id = $('.filter-param-attribute-1').val(),
            condition = $('.filter-param-attribute-2').val(),
            value = $('.filter-param-attribute-3').val(),
            quality = $('.filter-param-attribute-4').val(),
            key = `${id}_${condition}`,
            conditionArray = {
                'gt' : '>',
                'lt' : '<',
            };

        // check if it already exists or not
        if (typeof this.parameters[key] !== 'undefined') {
            return this.updateFilter(id, condition, value, quality);
        }

        // open search filter set
        this.toggleAttributeFilters(true);

        // append template
        var html = SearchTemplater.templateFilterAttributeBLock({
            id: id,
            name: name,
            condition: condition,
            conditionSymbol: conditionArray[condition],
            value: value,
            quality: quality == '1' ? `<img src="${SearchSettings.get('attributeHQIcon')}">` : '',
        });

        $('.filter-special-attributes-set').append(html);

        // append onto item attributes
        this.setFilter(id, condition, value, quality);
    }

    //
    // Remove an item attribute
    //
    handleItemAttributeFilterRemoval(event)
    {
        var $element = $(event.currentTarget);
            filterId = $element.attr('data-filter-value');

        // delete element and remove from attributes
        delete this.parameters[filterId];
        $element.remove();

        // re attach filters
        this.attachFilters();
        ttdrop();
    }

    //
    // Set a item attribute parameter
    //
    setFilter(id, condition, value, quality)
    {
        var key = `${id}_${condition}`,
            value = `${id}|${condition}|${value}|${quality}`;

        this.parameters[key] = value;
        this.attachFilters();
    }

    //
    // Update an existing filter
    //
    updateFilter(id, condition, value, quality)
    {
        var key = `${id}_${condition}`;

        // set quality and value
        $(`[data-filter-value="${key}"]`)
            .find('.filter-param-attribute-name em')
            .html(quality == '1' ? `<img src="${SearchSettings.get('attributeHQIcon')}">` : '');

        $(`[data-filter-value="${key}"]`)
            .find('.filter-param-attribute-value')
            .html(value);

        this.setFilter(id, condition, value, quality);
    }

    //
    // Attach filters to main search
    //
    attachFilters()
    {
        var attributes = $.map(this.parameters, function(v) {
            return v;
        });

        // if no attributes, delete them
        if (attributes.length == 0) {
            this.toggleAttributeFilters();
            SearchFilters.deleteFilterParameter('attributes');

            // make inactive
            $(SearchSettings.get('filterItemAttributesClass'))
                .find(SearchSettings.get('filterParamClass'))
                .removeClass('active');

            return;
        }

        SearchFilters.setActiveFilterPanel('items');
        SearchFilters.setFilterParameter('attributes', attributes.join(','));

        // make panel active
        $(SearchSettings.get('filterItemAttributesClass'))
            .find(SearchSettings.get('filterParamClass'))
            .addClass('active');
    }

    //
    // Toggle attribute filters
    //
    toggleAttributeFilters(isOpen)
    {
        if (isOpen) {
            $('.filter-special-attributes-empty').hide();
            $('.filter-special-attributes-set').show();
        } else {
            $('.filter-special-attributes-empty').show();
            $('.filter-special-attributes-set').hide();
        }
    }
}

var SearchFiltersItemAttributes = new SearchFiltersItemAttributesClass();
