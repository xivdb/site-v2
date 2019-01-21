//
// Search Builder
//
class SearchBuilderClass
{
    constructor()
    {
        this.data = {};
    }

    //
    // Add a 'GET' data attribute
    //
    add(type, value)
    {
        if (type == 'language') {
            return;
        }

        if (value || value.length > 0) {
            this.data[type] = value;
        }
    }

    //
    // Remove from search builder
    //
    remove(type)
    {
        delete this.data[type];
    }

    //
    // reset the builder
    //
    reset()
    {
        this.data = {};
    }

    //
    // get the 'GET' data
    //
    get(index)
    {
        if (index) {
            return this.data[index];
        }

        var data = this.data;
        return data;
    }
}

var SearchBuilder = new SearchBuilderClass();