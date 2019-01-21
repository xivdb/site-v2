//
// Search Tab Title
//
class SearchTitleClass
{
    constructor()
    {
        this.title = `Search - ${SITETITLE}`;
    }

    //
    // Set the title text to something (configed, not visually)
    //
    set(text)
    {
        this.title = `${text} - ${SITETITLE}`;
    }

    //
    // Show a title
    //
    show()
    {
        document.title = this.title;
    }

    //
    // Show a title before a main title
    //
    preShow(text)
    {
        document.title = `${text} - ${SITETITLE}`;
    }
}

var SearchTitle = new SearchTitleClass();