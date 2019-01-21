//
// Map Html
//
class XIVDBMapsHtmlClass
{
    //
    // Get the embed html
    //
    getEmbedHtml()
    {
        return `
        <div class="xivdb-map-embed">
			<div class="xivdb-map-selector"></div>
			<div class="xivdb-map-name">---</div>
            <div class="xivdb-map-pan">
                <div class="xivdb-map-frame">
                    <div class="xivdb-map-img"></div>
                    <div class="xivdb-map-icons"></div>
                </div>
            </div>
        </div>`;
    }

    //
    // Get Embed Map img (uncached)
    //
    getMapImageHtml(path)
    {
        return `<img src="${path}?t=${Date.now()}">`;
    }
}
