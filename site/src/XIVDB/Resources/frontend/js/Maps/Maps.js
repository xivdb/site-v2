//
// XIVDB Maps Class
//
class XIVDBMapsClass
{
    constructor()
    {
        this.Markers = new XIVDBMapsMarkerClass();

        this.classname = null;
        this.mapLayers = {};
        this.mapLayerIndex = 0;
        this.map = null;
        this.options = {};
        this.events = {};

        // selectors
        this.$map = null;
        this.$embed = null;
        this.$panzoom = null;
        this.$pans = null;
        this.$frame = null;
        this.$selector = null;
    }

    init()
    {
		this.validateOptions();
        this.watcher();
    }

    reset()
    {
        this.Markers.list = {};
        this.mapLayers = {};
        this.mapLayerIndex = 0;
        this.map = null;

        // selectors
        this.$map = null;
        this.$embed = null;
        this.$panzoom = null;
        this.$pans = null;
        this.$frame = null;
        this.$selector = null;
    }

    watcher()
    {
        $('html').on('change', '.xivdb-map-selector', event => {
            let value = $(event.currentTarget).find('option:selected').val();
            this.mapLayerIndex = value;
            this.mapLoader();
            this.Markers.render();
        });
    }

    // move to a specific layer
    moveToLayer(index)
    {
        this.$selector.find('select').val(index);
        this.mapLayerIndex = index;
        this.mapLoader();
    }

    //
    // Load map data from API
    //
    getLayers(id, callback)
    {
        // set map data
        $.ajax({
            url: this.options.apiUrl + '/maps/get/layers/placename',
            data: {
                id: id,
            },
            cache: false,
            dataType: 'json',
            method: 'GET',
            success: (data) => {
                callback(data);
            },
            error: (data, status, error) => {
                console.error('XIVDB Maps Load Error');
                console.error(data.responseText, status, error);
            }
        });
    }

    //
    // Embed a map
    //
    embed(classname, id, callback)
    {
        this.reset();
        this.classname = classname;
        this.domHandler();

        // get map layers
        this.getLayers(id, data => {
            if (!data.status) {
                return console.error('Failed to get layers');
            }

            this.mapLayers = data.data;
            this.populateZones();
            this.mapLoader();
            callback();
        });
    }

    //
    // Embed a map
    //
    domHandler()
    {
        // inject html
        this.$section = $(this.classname);
        this.$section.html(XIVDBMapsHtml.getEmbedHtml());
        this.$section.css({
            height: this.options.height ? this.options.height : '100%',
            width: this.options.width ? this.options.width : '100%',
        });

        // set frame
        this.$frame = this.$section.find('.xivdb-map-frame');

        // embed map
        this.$embed = this.$section.find('.xivdb-map-embed');
        this.$embed.css({
            height: this.options.height ? this.options.height : '100%',
            width: this.options.width ? this.options.width : '100%',
        });

        // add panning
        this.$pan = this.$section.find('.xivdb-map-pan');
        this.$panzoom = this.$pan.panzoom({
            transition: false,
        });

        // add zooming
        this.$panzoom.parent().on('mousewheel.focal', (event) => {
            event.preventDefault();
            var delta = event.delta || event.originalEvent.wheelDelta;
            var zoomOut = delta ? delta < 0 : event.originalEvent.deltaY > 0;

            // add zooms
            this.$panzoom.panzoom('zoom', zoomOut, {
                increment: this.options.zoomIncrement,
                animate: this.options.zoomAnimate,
                minScale: this.options.zoomMinScale,
                maxScale: this.options.zoomMaxScale,
                focal: event,
            });
        });

        // set frame height/width
        this.$frame.width(this.options.frameWidth).height(this.options.frameHeight);
        this.$pan.width(this.options.frameWidth).height(this.options.frameHeight);

        // selector
        this.$selector = this.$section.find('.xivdb-map-selector');

        // set marker icon container
        this.Markers.setContainer(this.$frame.find('.xivdb-map-icons'));
        return this;
    }

    //
    // Populate zones
    //
    populateZones()
    {
        // populate selector
        if (this.mapLayers.length > 1) {
            // add select
            this.$selector.html('<select></select>');

            // add zones
            for(let i in this.mapLayers) {
                let layer = this.mapLayers[i],
                    zonename = 'Layer #' + (parseInt(i) + 1);

                if (typeof layer.zone != 'undefined' && layer.zone != 0) {
                    zonename = `${zonename} - ${layer.zone}`;
                }

                let html = `<option value="${i}">${zonename}</option>`;
                this.$selector.find('select').append(html);
            }
        }
    }


    //
    // Load a map from its placename ID
    //
    mapLoader()
    {
        // load map file
        this.map = this.mapLayers[this.mapLayerIndex];
        this.setMapData();

        let mapImage = XIVDBMapsHtml.getMapImageHtml(this.map.image);

        // inject map
        this.$frame.find('.xivdb-map-img').html(mapImage);
        this.center();

        if (this.events.mapLayerUpdate) {
            this.events.mapLayerUpdate(this.mapLayerIndex);
        }

        return this;
    }

    //
    // Focus on a specific marker
    //
    focus(marker)
    {
        setTimeout(() => {
            var embedWidth = this.$embed.width(),
                embedHeight = this.$embed.height(),
                markerLeft = marker.pos.x,
                markerTop = marker.pos.y,
                markerWidth = $(`#${marker.id}`).width(),
                markerHeight = $(`#${marker.id}`).height();

            var left = markerLeft - (embedWidth / 2) + (markerWidth / 2),
                top = markerTop  - (embedHeight / 2) + (markerHeight / 2);

            this.$panzoom
                .panzoom('pan', -(left), -(top))
                .panzoom('zoom', this.options.zoomDefault);
        }, 200);

        return this;
    }

    //
    // Center a map
    //
    center()
    {
        var embedWidth = this.$embed.width(),
            embedHeight = this.$embed.height(),
            mapWidth = this.options.frameWidth,
            mapHeight = this.options.frameHeight;

        var left = (mapWidth / 2) - (embedWidth / 2),
            top = (mapHeight / 2) - (embedHeight / 2);

        this.$panzoom
            .panzoom('pan', -(left), -(top))
            .panzoom('zoom', this.options.zoomDefault);

        return this;
    }

    /**
     * Set map data
     */
    setMapData()
    {
        $('.xivdb-map-name').html(`${this.map.placename} - ${this.map.region}`);
    }

    // --- OPTIONS -----------------------------------------------

    //
    // Add events
    //
    setEvent(eventname, callback)
    {
        // register events
        this.events[eventname] = callback;
        this.Markers.events[eventname] = callback;

        return this;
    }

    //
    // Set the options
    //
    setOptions(options)
    {
        // Options passed
        this.options = options;
		return this;
    }

    //
    // Set a specific option
    //
    setOption(key, value)
    {
        this.options[key] = value;
        return this;
    }

    //
    // Get an option
    //
    getOption(setting)
    {
        return this.options[setting];
    }

    //
    // Verifies tooltips options and sets the default on missing ones
    //
    validateOptions()
    {
        for(var option in xivdb_maps_default) {
            if (typeof this.options[option] === 'undefined') {
                this.options[option] = xivdb_maps_default[option];
            }
        }
    }
}
