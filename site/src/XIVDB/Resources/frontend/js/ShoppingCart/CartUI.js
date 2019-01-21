//
// Shopping Cart UI
//
class CartUIClass
{
	constructor()
	{
		this.cssOpenButton = '[data-tool="shoppingcart"]';
		this.cssCartPanel = '.tool-cart';
		this.cssDraggableIcons = '.search-results .search-results-group-items .entity';
		this.cssDropZone = '.tool-cart';
		this.isOpen = false;
	}

	//
	// Open shopping cart tool
	//
	open()
	{
		// hide all open panels
		SearchUI.hideAllActivePanels('cart');

		// set as open
		this.isOpen = true;
		this.setIconsDraggable();
		$(this.cssCartPanel).addClass('open');
		$(this.cssOpenButton).addClass('enabled');

		// make shopping cart panel droppable
		$(this.cssDropZone).droppable({
			accept: this.cssDraggableIcons,
			activeClass: 'tool-cart-dropzone-active',
			hoverClass: 'tool-cart-dropzone-hover',
			drop: function( event, ui ) {
				var $droppedItem = $(ui.draggable);

				// get item data
				var item = {
					id: $droppedItem.attr('data-id'),
					name: $droppedItem.find('.data .name').text(),
					icon: $droppedItem.find('.icon img').attr('src'),
					quantity: 1,
				}

				CartItems.add(item);
			}
		});

		// Set search
		// Set search
		if (Search.theone != 'items') {
			Search.setOne('items').setPage(1);
		}

		Search.setString().runQuery();
		SearchUI.updateStickyNav();
	}

	//
	// Close shopping cart tool
	//
	close()
	{
		if (this.isOpen) {
			this.isOpen = false;
			$(this.cssCartPanel).removeClass('open');
			$(this.cssOpenButton).removeClass('enabled');
			$(this.cssDraggableIcons).removeClass('cart-draggable').draggable('destroy');
		}
	}

	//
	// Attach draggable
	//
	setIconsDraggable()
	{
		// make icons draggable
		$(this.cssDraggableIcons).addClass('cart-draggable').draggable({
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

// Watch for events
var CartUI = new CartUIClass();
