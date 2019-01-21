//
// Shopping Cart - Checkout
//
class CheckoutClass
{
	constructor()
	{
		this.cssListEntityButtonToggle = '.list-entity button.toggle';
		this.cssListEntityButtonFind = '.list-entity button.find';
		this.cssWindowMinifyButton = '.tool-cart-page .tool-toggle-window';
		this.cssBasketItem = '.right.tool-basket button';
		this.cssToolInputs = '.tool-cart-page input';
		this.cssHqButtons = '.list-entity button.hqbtn';
		this.cssSaveButton = '.action-bar button.save-cart';
		this.cssLoadButton = '.action-bar button.load-cart';
	}

	watch()
	{
		//
		// Toggle the visibility of one of the detailed items
		// when viewing an item in the basket
		//
		$('html').on('click', this.cssListEntityButtonToggle, (event) => {
			var id = $(event.currentTarget).attr('data-id');
			CheckoutUI.toggleTableRow(id);
		});

		//
		// Find detail information about a detailed item
		//
		$('html').on('click', this.cssListEntityButtonFind, (event) => {
			var id = $(event.currentTarget).attr('data-id');
			CheckoutFind.find(id);
		});

		//
		// Toggle which view should be shown in the tool window
		//
		$('html').on('click', this.cssBasketItem, (event) => {
			var id = $(event.currentTarget).attr('data-view');
			CheckoutUI.switchView(id);
		});

		//
		// Event for whenever an input keyup is done
		//
		$('html').on('keyup', this.cssToolInputs, (event) => {
			var $input = $(event.currentTarget);

			// if it's an entity
			if ($input.attr('data-var') == 'entityCost') {
				var id = $input.parents('.list-entity').attr('data-id'),
					costClass = $input.hasClass('costhq') ? 'costhq' : 'cost',
					value = $input.val().trim();

				// find other inputs
				$(`.list-entity[data-id="${id}"] input.${costClass}:not(:focus)`).val(value);
			}

			CheckoutMath.calculate();
		});

		//
		// Toggle a crafting tier in the cart
		//
		$('html').on('click', this.cssWindowMinifyButton, (event) => {
			var $button = $(event.currentTarget),
				$parent = $button.parents('.list-block').find('.list-data'),
				$notice = $button.parents('.list-block').find('.list-notice'),
				open = $button.attr('data-open') == '1' ? true : false;

			if (open)
			{
				$button.attr('data-open', 0);
				$parent.hide();
				$notice.show();
				return;
			}

			$button.attr('data-open', 1);
			$parent.show();
			$notice.hide();
		});

		//
		// Toggle HQ
		//
		$('html').on('click', this.cssHqButtons, (event) => {
			var $button = $(event.currentTarget),
				on = $button.hasClass('on');

			// set status if on or off
			on ? $button.removeClass('on') : $button.addClass('on');

			// get cost inputs
			var $nqInput = $button.parents('.list-entity').find('.cost');
				$hqInput = $button.parents('.list-entity').find('.costhq');

			// remove inactive on both
			$nqInput.removeClass('inactive');
			$hqInput.removeClass('inactive');

			// set inactive on non used input
			on ? $hqInput.addClass('inactive') : $nqInput.addClass('inactive');

			// recalculate
			CheckoutMath.calculate();
		});

		//
		// On Save open
		//
		$('html').on('click', this.cssSaveButton, (event) => {
			$('.panel-save').toggleClass('open');
		});

		//
		// On Save open
		//
		$('html').on('click', this.cssLoadButton, (event) => {
			$('.panel-load').toggleClass('open');
		});

		//
		// Tabs
		//
        $('html').on('click', '.tool-cart-tabs button', event =>
        {
            var tab = $(event.currentTarget).attr('data-tab');

            $('.tool-cart-tabs-page.active, .tool-cart-tabs button.active').removeClass('active');
            $(`.tool-cart-tabs-page[data-tab="${tab}"], .tool-cart-tabs button[data-tab="${tab}"]`).addClass('active');
        });
	}
}

// Watch for events
var Checkout = new CheckoutClass();
$(() => {
	Checkout.watch();
	CheckoutStorage.load();
});
