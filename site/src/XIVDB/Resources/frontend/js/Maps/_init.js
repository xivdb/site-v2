var xivdb_maps_default = {
    apiUrl: API_URL,

	// default height in px
    height: 500,
    width: 500,

    zoomDefault: 1,
    zoomIncrement: 0.1,
    zoomAnimate: true,
    zoomMinScale: 0.2,
    zoomMaxScale: 4,

    frameWidth: 2048,
    frameHeight: 2048,
}

let XIVDBMaps = new XIVDBMapsClass(),
    XIVDBMapsConverter = new XIVDBMapsConverterClass(),
    XIVDBMapsHtml = new XIVDBMapsHtmlClass();

XIVDBMaps
	.setOptions(typeof xivdb_maps !== 'undefined' ? xivdb_maps : xivdb_maps_default)
	.init();
