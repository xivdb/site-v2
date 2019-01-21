//
// Shopping Cart Events
//
class CartClass
{
	constructor()
	{
		this.cssOpenButton = '[data-tool="shoppingcart"]';
		this.cssCartPanel = '.tool-cart';
		this.cssSearchLinks = '.search-results a';
		this.cssRemoveItem = '.tool-cart .tool-cart-items .cart-item button#remove';
		this.cssQuantity = '.tool-cart .tool-cart-items .cart-item input#quantity';
		this.cssCheckout = '.tool-cart button.tool-cart-checkout';
	}

	//
	// Watch for events
	//
	watch()
	{
		// shoppign cart icon clicked, which either opens
		// or closes the tool, based on visibility
		$('html').on('click', this.cssOpenButton, (event) => {
			$(this.cssCartPanel).hasClass('open') ? CartUI.close() : CartUI.open();
		});

		// listen on search results being clicked, if the cart is open, prevent going
		// to the page as it could of been accidental
		$('html').on('click', this.cssSearchLinks, (event) => {
			if ($(this.cssCartPanel).hasClass('open')) {
				event.preventDefault()
			}
		});

		// on removing an item
		$('html').on('click', this.cssRemoveItem, (event) => {
			var id = $(event.currentTarget).attr('data-item-id');
			CartItems.remove(id);
		});

		// on quantity change
		$('html').on('keyup', this.cssQuantity, (event) => {
			var id = $(event.currentTarget).attr('data-item-id'),
				value = $(event.currentTarget).val();

			CartItems.updateQuantity(id, value);
		});

		// on checkout button!
		$('html').on('click', this.cssCheckout, (event) => {
			this.checkout();
		});

		// on clicking menu button
		$('html').on('click', '[data-action="shopping-cart"]', (event) => {
			$('.dropdown-container').hide();
			CartUI.open();
		});
	}

	//
	// Checkout with the items!!!
	//
	checkout()
	{
		// get items
		var items = CartItems.get();

		// generate url
		var url = [];
		for(var i in items) {
			var item = items[i];

			// ensure correct quantity
			if (item.quantity > 0 && item.quantity < 999) {
				url.push(`${item.id}x${item.quantity}`);
			}
		}

		// only checkout if we have items
		if (url.length > 0) {
			// go to url
			url = 'shopping-cart/' + url.join('-');
			window.location = url;
		}
	}

	//
	// Is cart open?
	//
	isOpen()
	{
		return $(this.cssCartPanel).hasClass('open');
	}
}

// Watch for events
var Cart = new CartClass();
$(() => { Cart.watch(); });
