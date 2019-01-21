//
// Shopping Cart - CheckoutFind
//
class CheckoutFindClass
{
	constructor()
	{
		this.cssFindPlaceholder = '.tool-cart-placeholder';
		this.cssFindLoading = '.tool-cart-loader';
		this.cssMaterialDetails = '.cart-material-details';
		this.cssMaterialTemplate = '#ui-cart-material-details';
		this.cssCraftTreeRow = '#ui-cart-tree-row';
	}

	//
	// Find information about an item clicked
	//
	find(id)
	{
		// hide tooltips
        if (typeof XIVDBTooltips !== 'undefined') {
            XIVDBTooltips.hide();
        }

		this.loading(1);

		$.ajax({
            url: `${API_URL}/item/${id}`,
            data: {
				language: LANGUAGE
			},
            cache: false,
            dataType: 'json',
            method: 'GET',
            success: (data) =>
            {
                this.renderDetails(data);
            },
            error: (data, status, error) =>
            {
                console.error(data, status, error);
            },
            complete: () =>
            {
				this.loading(2);

				if (typeof XIVDBTooltips !== 'undefined') {
					XIVDBTooltips.getDelayed();
	            }
            }
        });
	}

	//
	// Render details
	//
	renderDetails(item)
	{
		// generate cart item html
		var html = render(this.cssMaterialTemplate, item);

		// add item to cart vissually
		$(this.cssMaterialDetails).html(html);

		// if synth tree
		if (item.craftable && typeof item.craftable[0] !== 'undefined')
		{
			this.renderCraftingTree(item.craftable[0], 1);
		}
	}

	//
	// Render a crafting tree (Recurrsive)
	//
	renderCraftingTree(recipe, tier)
	{
		for(var i in recipe._tree)
		{
			// get material
			var material = recipe._tree[i];
				material.tier = tier;

			// row html
			var html = render(this.cssCraftTreeRow, material);

			// append to tree
			$(this.cssMaterialDetails)
				.find('.cart-extended-tree')
				.append(html);

			// if sub tree
			if (typeof material._synths !== 'undefined')
			{
				var synthId = Object.keys(material._synths)[0];
					synthNext = material._synths[synthId];

				this.renderCraftingTree(synthNext, tier + 1);
			}
		}
	}

	//
	// Set loading
	//
	loading(state)
	{
		if (state == 1)
		{
			$(this.cssFindPlaceholder).addClass('off');
			$(this.cssFindLoading).addClass('on');
			$(this.cssMaterialDetails).addClass('off');
			return;
		}
		else if (state == 2)
		{
			$(this.cssMaterialDetails).removeClass('off');
			$(this.cssFindLoading).removeClass('on');
			return;
		}

		$(this.cssFindPlaceholder).removeClass('off');
		$(this.cssFindLoading).removeClass('on');
	}
}

// Watch for events
var CheckoutFind = new CheckoutFindClass();
