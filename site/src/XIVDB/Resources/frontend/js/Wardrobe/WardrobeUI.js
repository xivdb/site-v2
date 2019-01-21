//
// Wardrobe UI
//
class WardrobeUIClass
{
	constructor()
	{
		this.cssOpenButton = '[data-tool="wardrobe"]';
		this.cssWardrobePanel = '.tool-wardrobe';
		this.cssDraggableIcons = '.search-results .search-results-group-items .entity';
		this.cssDropZone = '.tool-wardrobe';
		this.isOpen = false;
	}

	//
	// Open wardrobe tool
	//
	open()
	{
		// hide all open panels
		SearchUI.hideAllActivePanels('wardrobe');

		// set as open
		this.isOpen = true;
		this.setIconsDraggable();
		$(this.cssWardrobePanel).addClass('open');
		$(this.cssOpenButton).addClass('enabled');

		// make wardrobe panel droppable
		$(this.cssDropZone).droppable({
			accept: this.cssDraggableIcons,
			activeClass: 'tool-wardrobe-dropzone-active',
			hoverClass: 'tool-wardrobe-dropzone-hover',
			drop: function( event, ui ) {
				var $droppedItem = $(ui.draggable);
				WardrobeItems.add($droppedItem.attr('data-id'));
			}
		});

		// Set search
		if (Search.theone != 'items') {
			Search.setOne('items').setPage(1);
		}

		Search.setString().runQuery();
		SearchUI.updateStickyNav();
	}

	//
	// Close wardrobe tool
	//
	close()
	{
		if (this.isOpen) {
			this.isOpen = false;
			$(this.cssWardrobePanel).removeClass('open');
			$(this.cssOpenButton).removeClass('enabled');
			$(this.cssDraggableIcons).removeClass('wardrobe-draggable').draggable('destroy');
		}
	}

	//
	// Attach draggable
	//
	setIconsDraggable()
	{
		// make icons draggable
		$(this.cssDraggableIcons).addClass('wardrobe-draggable').draggable({
			containment: 'html',
			revert: true,
			revertDuration: 0,
			start: () => {
				ttdrop();

				if (typeof XIVDBTooltips !== 'undefined') {
		            XIVDBTooltips.hide();
		        }
			}
		});
	}
}

// init
var WardrobeUI = new WardrobeUIClass();
