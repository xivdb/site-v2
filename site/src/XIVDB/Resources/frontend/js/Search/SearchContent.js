//
// Search Content
//
class SearchContentClass
{
    constructor()
    {
        this.bull = '<em class="bull">&bull;</em>';
    }

    items(data)
    {
        data.extra = [];

        if (data.level_equip > 0) {
            data.extra.push(`<em class="blue">Lv ${data.level_equip}</em>`);
        }

        if (data.level_item > 0) {
            data.extra.push(`<em class="yellow">iLv ${data.level_item}</em>`);
        }

        if (data.category_name) {
            data.extra.push(`<em class="slot">${data.category_name}</em>`);
        }

        data.extra = data.extra.join(this.bull);
        return data;
    }

    quests(data)
    {
        data.extra = [];

        if (data.class_level_1 > 0) {
            data.extra.push(`<em class="blue">Lv ${data.class_level_1}</em>`);
        }

        if (data.category_name) {
            data.extra.push(`<em class="slot">${data.category_name}</em>`);
        }

        data.extra = data.extra.join(this.bull);
        return data;
    }

    actions(data)
    {
        data.extra = [];

        if (data.level > 0) {
            data.extra.push(`<em class="blue">Lv ${data.level}</em>`);
        }

        if (data.item_level > 0) {
            data.extra.push(`<em class="yellow">iLv ${data.item_level}</em>`);
        }

        if (data.class_name) {
            data.extra.push(`<em class="slot">${data.class_name}</em>`);
        }

        if (data.type_name) {
            data.extra.push(`<em class="slot">${data.type_name}</em>`);
        }

        data.extra = data.extra.join(this.bull);
        return data;
    }

    achievements(data)
    {
        data.extra = [];

        if (data.category_name) {
            data.extra.push(`<em class="slot">${data.category_name}</em>`);
        }

        if (data.kind_name) {
            data.extra.push(`<em class="slot">${data.kind_name}</em>`);
        }

        data.extra = data.extra.join(this.bull);
        return data;
    }

    recipes(data)
    {
        data.extra = [];

        if (data.level_view > 0) {
            data.extra.push(`<em class="blue">Lv ${data.level_view}</em>`);
        }

        if (data.level > 0) {
            data.extra.push(`<em class="yellow">${data.stars_html} ${data.level}</em>`);
        }

        if (data.class_name) {
            data.extra.push(`<em class="slot">${data.class_name}</em>`);
        }

        data.extra = data.extra.join(this.bull);
        return data;
    }

    instances(data)
    {
        data.extra = [];

        if (data.level > 0) {
            data.extra.push(`<em class="blue">Lv ${data.level}</em>`);
        }

        if (data.item_level > 0) {
            data.extra.push(`<em class="yellow">iLv ${data.item_level}</em>`);
        }

        if (data.content_name) {
            data.extra.push(`<em class="slot">${data.content_name}</em>`);
        }

        data.extra = data.extra.join(this.bull);
        return data;
    }

    fates(data)
    {
        data.extra = [];

        if (data.class_level > 0) {
            data.extra.push(`<em class="blue">Lv ${data.class_level}</em>`);
        }

        if (data.placename) {
            data.extra.push(`<em class="slot">${data.placename}</em>`);
        }

        data.extra = data.extra.join(this.bull);
        return data;
    }

    leves(data)
    {
        data.extra = [];

        if (data.class_level > 0) {
            data.extra.push(`<em class="blue">Lv ${data.class_level}</em>`);
        }

        if (data.assignment_type_name) {
            data.extra.push(`<em class="slot">${data.assignment_type_name}</em>`);
        }

        data.extra = data.extra.join(this.bull);
        return data;
    }

    places(data)
    {
        data.extra = [];

        if (data.region_name) {
            data.extra.push(`<em class="slot">${data.region_name}</em>`);
        }

        data.extra = data.extra.join(this.bull);
        return data;
    }

    gathering(data)
    {
        data.extra = [];

        if (data.level_view > 0) {
            data.extra.push(`<em class="blue">Lv ${data.level_view}</em>`);
        }

        if (data.level > 0) {
            data.extra.push(`<em class="yellow">${data.stars_html} ${data.level}</em>`);
        }

        if (data.type_name) {
            data.extra.push(`<em class="slot">${data.type_name}</em>`);
        }

        data.extra = data.extra.join(this.bull);
        return data;
    }

    npcs(data)
    {
        data.extra = [];

        if (data.title) {
            data.extra.push(`<em class="slot">${data.title}</em>`);
        }

        data.extra = data.extra.join(this.bull);
        return data;
    }

    enemies(data)
    {
        data.extra = '';

        return data;
    }

    emotes(data)
    {
        data.extra = [];

        if (data.command) {
            data.extra.push(`<em class="slot">${data.command}</em>`);
        }

        data.extra = data.extra.join(this.bull);
        return data;
    }

    status(data)
    {
        data.extra = '';

        return data;
    }

    titles(data)
    {
        data.extra = [];

        if (data.name_female) {
            data.extra.push(`<em class="slot"><i class="fa fa-venus"></i> ${data.name_female}</em>`);
        }

        data.extra = data.extra.join(this.bull);
        return data;
    }

    minions(data)
    {
        data.extra = [];

        if (data.race) {
            data.extra.push(`<em class="slot">${data.race}</em>`);
        }

        data.extra = data.extra.join(this.bull);
        return data;
    }

    mounts(data)
    {
        data.extra = '';

        return data;
    }

    weather(data)
    {
        data.extra = [];

        if (data.type) {
            data.extra.push(`<em class="slot">${data.type}</em>`);
        }

        data.extra = data.extra.join(this.bull);
        return data;
    }

    characters(data)
    {
        data.extra = [];

        if (data.server) {
            data.extra.push(`<em class="slot">${data.server}</em>`);
        }

        data.extra = data.extra.join(this.bull);
        return data;
    }
}

var SearchContent = new SearchContentClass();
