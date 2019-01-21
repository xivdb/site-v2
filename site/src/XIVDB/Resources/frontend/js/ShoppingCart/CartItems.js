//
// Shopping Cart Items
//
class CartItemsClass
{
	constructor()
	{
		this.items = {};
		this.cssDropZone = '.tool-cart-items';
		this.cssCheckoutButton = '.tool-cart-checkout';
	}

	//
	// Get the items
	//
	get()
	{
		return this.items;
	}

	//
	// Add an item to the cart
	//
	add(item)
	{
		// if item already exists, then do nothing
		if (this.items[item.id]) {
			return;
		}

		// add to object
		this.items[item.id] = item;

		// hide dropzone title
		$(this.cssDropZone).find('em').hide();

		// enable checkout button
		$(this.cssCheckoutButton).removeClass('disabled');

		// generate cart item html
		var html = render('#ui-cart-item', item);

		// add item to cart visually
		$(this.cssDropZone).append(html);
	}

	//
	// Remove an item
	//
	remove(id)
	{
		delete this.items[id];
		$('#cartitem' + id).remove();

		// if no items, show the em text
		if (Object.size(this.items) == 0) {
			$(this.cssDropZone).find('em').show();
			$(this.cssCheckoutButton).addClass('disabled');
		}

		// drop tooltips
		ttdrop();
	}

	//
	// Update an item quantity
	//
	updateQuantity(id, value)
	{
		this.items[id].quantity = parseInt(value);
	}
}

// Watch for events
var CartItems = new CartItemsClass();
