//
// Shopping Cart - CheckoutUI
//
class CheckoutUIClass
{

	constructor()
	{
		this.cssListEntity = '.list-entity';
	}

	//
	// Toggles the table row, simply just changes
	// the opacity, allows you to "filter out" a
	// sort of todo list
	//
	toggleTableRow(id)
	{
		var $row = $(`${this.cssListEntity}[data-id="${id}"]`);
		$row.hasClass('toggleoff') ? $row.removeClass('toggleoff') : $row.addClass('toggleoff');
	}

	//
	// Change view
	//
	switchView(id)
	{
		// remove active
		$(`.tool-cart-window.active`).removeClass('active');
		$(`.right.tool-basket button.active`).removeClass('active');

		// add active
		$(`.tool-cart-window.tool-cart-window-${id}`).addClass('active');
		$(`.right.tool-basket button[data-view="${id}"]`).addClass('active');
	}
}

// Watch for events
var CheckoutUI = new CheckoutUIClass();
