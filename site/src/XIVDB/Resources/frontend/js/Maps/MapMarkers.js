//
// XIVDB Maps Markers
// - Handles all map markers
//
class XIVDBMapsMarkerClass
{
	constructor()
	{
		this.$container = null;
		this.activeMapid = null;
		this.list = {};
		this.events = {};

		$('html').on('click', '.xivdb-map-marker', event => {
		    let marker = $(event.target).attr('id');
                marker = this.list[this.activeMapid][marker];

            if (this.events.markerOnClick) {
                this.events.markerOnClick(marker);
            }
        });
	}

	//
	// Set container
	//
	setContainer(container)
	{
		this.$container = container;
		return this;
	}

	//
	// Load markers
	//
    load()
    {
        let map = XIVDBMaps.map;

        // set map data
        $.ajax({
            url: XIVDBMaps.options.apiUrl + '/maps/get/markers?placename=' + map.placename_id,
            cache: false,
            dataType: 'json',
            method: 'GET',
            success: (data) => {
                // clear any markers
                this.clear();

                // if markers, begin adding them
                if (data.length > 0) {
                    for(let i in data) {
                        // create marker object
                        let point = data[i],
                            marker = {
                                id: point.hash,
                                map: point.map_id,
                                icon: point.icon.image,
                                size: point.icon.size,
                                x: point.app_position.ingame.x,
                                y: point.app_position.ingame.y,
                                focus: false,
                                name: point.content_name,
                                tooltip: point.tooltip,
                            }

                        // calculate position
                        marker.pos = XIVDBMapsConverter.gameTo2d(marker.x, marker.y, XIVDBMaps.map.size_factor, marker.size);
                        marker.id = marker.id ? marker.id : 'mm' + (Math.floor((Math.random() * 99999999) + 1));

                        if (!this.list[marker.map]) {
                            this.list[marker.map] = {};
                        }

                        this.list[marker.map][marker.id] = marker;
                    }

                    this.render();
                }
            },
            error: (data, status, error) => {
                console.error('XIVDB Maps Load Error');
                console.error(data.responseText, status, error);
            }
        });
    }

    //
    // Render markers
    //
    render()
    {
        // remove visual icons
        this.$container.html('');

        // get markers for current layer
        let mapId = XIVDBMaps.mapLayers[XIVDBMaps.mapLayerIndex].id,
            markers = this.list[mapId];

        this.activeMapid = mapId;

        // render
        for(let i in markers) {
            this.add(markers[i]);
        }
    }

	//
	// Add an icon to the map
	//
	add(data)
	{
	    if (!data.pos) {
            // calculate position
            data.pos = XIVDBMapsConverter.gameTo2d(data.x, data.y, XIVDBMaps.map.size_factor, data.size);
            data.id = data.id ? data.id : 'mm' + (Math.floor((Math.random() * 99999999) + 1));
        }

		let size = `width:${data.size}px;height:${data.size}px`;
			styles = `left:${data.pos.x}px;top:${data.pos.y}px;`,
			marker = '';

		if (data.icon) {
			marker = `<img class="xivdb-map-marker xivdb-map-marker-img" src="${data.icon}" style="${styles}${size}" id="${data.id}" data-tt="${data.tooltip}">`;
		} else {
			marker = `<span class="xivdb-map-marker xivdb-map-marker-${data.color}" style="${styles}${size}" id="${data.id}" data-tt="${tooltip}"></span>`;
		}

		// add to list
		this.$container.append(marker);

		// if to focus it
		if (data.focus) {
			XIVDBMaps.focus(data);
		}

		return this;
	}

	//
    // Clear icons
    //
    clear()
    {
        this.list = {};
    }

	//
	// onclick event
	//
	onClick(callback)
	{
		$('html').on('click', '.xivdb-map-marker', event => {
			let id = $(event.currentTarget).attr('id');
			callback({
				id: id,
				marker: this.list[id],
				element: $(event.currentTarget),
			});
		});
	}
}
