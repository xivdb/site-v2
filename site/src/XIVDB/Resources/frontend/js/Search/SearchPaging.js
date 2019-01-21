//
// Search Paging
//
class SearchPagingClass
{
    //
    // Empty search paging
    //
    reset()
    {
        $(SearchSettings.get('searchPagingContainerClass')).empty().html('');
    }

    //
    // Render paging
    //
    renderPaging(category, paging, totalResults, foundResults)
    {
        var html = SearchTemplater.templateSearchPaging({
            category: category,
        });

        // go through pages
        var buttonsHtml = [];
        for (var i in paging.pages)
        {
            buttonsHtml.push(SearchTemplater.templateSearchPagingButton({
                page: paging.pages[i],
                active: paging.pages[i] == paging.page ? true : false,
            }));
        }

        html = html.replace('[[PAGES]]', buttonsHtml.join(''));
        $(SearchSettings.get('searchPagingContainerClass')).append(html);

        // set next, prev and last
        this.setButtons(category, paging);

        // set search info 2
        this.setSearchInfo(category, paging, totalResults, foundResults);
    }

    //
    // Set next, prev and last
    //
    setButtons(category, paging)
    {
        var $container = $(SearchSettings.get('searchPagingContainerClass')).find(`[data-category="${category}"]`);

        // previous
        $container.find('.left button:last-child').attr('data-page', paging.prev);

        // next
        $container.find('.right button:first-child').attr('data-page', paging.next);

        // last
        $container.find('.right button:last-child').attr('data-page', paging.total);

        // if current page = first, disable first and previous
        // if current page = last, disable last and next
        $button = $container.find('.left button');
        (paging.page == 1)
            ? $button.addClass('disabled')
            : $button.removeClass('disabled');

        // if current page = last, disable last and next
        $button = $container.find('.right button');
        (paging.page == paging.total)
            ? $button.addClass('disabled')
            : $button.removeClass('disabled');
    }

    //
    // Set search info 2, specific to the page tab
    //
    setSearchInfo(category, paging, totalResults, foundResults)
    {
        var $container = $(SearchSettings.get('searchPagingContainerClass')).find(`[data-category="${category}"]`);
        var duration = ((Date.now() - SearchUI.start) / 1000).toFixed(2);

        var html = SearchTemplater.templateSearchInfo2({
            category: ucwords(category),
            found: foundResults,
            total: number_format(totalResults),
            page: paging.page,
            pages: paging.total,
            duration: duration,
        });

        // render html
        $container
            .find('.search-info')
            .html(html);
    }
}

var SearchPaging = new SearchPagingClass();
