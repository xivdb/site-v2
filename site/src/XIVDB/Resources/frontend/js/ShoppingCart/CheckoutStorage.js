//
// Shopping Cart - CheckoutStorage
//
class CheckoutStorageClass
{
    constructor()
    {
        this.itemCosts = {};
        this.itemProfits = {};
    }

    //
    // Load cart values
    //
    load()
    {
        // if prices
        if (cartprices = localStorage.getItem('cartprices'))
        {
            // parse
            this.itemCosts = JSON.parse(cartprices);
            for(var id in this.itemCosts)
            {
                var values = this.itemCosts[id];
                $(`.list-entity[data-id="${id}"] input.cost`).val(values.nq);
                $(`.list-entity[data-id="${id}"] input.costhq`).val(values.hq);
            }
        }

        // if profits
        if (profits = localStorage.getItem('basketprices'))
        {
            // parse
            this.itemProfits = JSON.parse(profits);
            for(var id in this.itemProfits)
            {
                var values = this.itemProfits[id];
                $(`.item[data-id="${id}"] input.final_sale`).val(values.finalSale);
                $(`.item[data-id="${id}"] input.final_sale_hq`).val(values.finalSaleHq);
                $(`.item[data-id="${id}"] input.final_quantity`).val(values.finalQuantity);
                $(`.item[data-id="${id}"] input.final_quantity_hq`).val(values.finalQuantityHq);
            }
        }

        CheckoutMath.calculate();
    }

    //
    // Save cart values
    //
    save()
    {
        var $rows = $('.tool-cart-window-all table tr');

        // loop through each rows
        $rows.each((i, element) =>
        {
            // get row
            var $row = $(element),
                id = $row.attr('data-id'),
                nqCost = parseInt($row.find('input.cost').val()),
                hqCost = parseInt($row.find('input.costhq').val());

            // fix costs
            if (isNaN(nqCost)) nqCost = 0;
            if (isNaN(hqCost)) hqCost = 0;

            // add to list
            this.itemCosts[id] = {
                nq: nqCost,
                hq: hqCost
            };
        });

        // save to local stoage
        localStorage.removeItem('cartprices');
        localStorage.setItem('cartprices', JSON.stringify(this.itemCosts));
        $('#cartprices').val(JSON.stringify(this.itemCosts));

        // basket
        var $rows = $('.right.tool-basket .item');

        $rows.each((i, element) =>
        {
            var $row = $(element),
                id = $row.attr('data-id'),
                finalSale = parseInt($row.find('input.final_sale').val()),
                finalSaleHq = parseInt($row.find('input.final_sale_hq').val()),
                finalQuantity = parseInt($row.find('input.final_quantity').val()),
                finalQuantityHq = parseInt($row.find('input.final_quantity_hq').val());

            // fix costs
            if (isNaN(finalSale)) finalSale = 0;
            if (isNaN(finalSaleHq)) finalSaleHq = 0;
            if (isNaN(finalQuantity)) finalQuantity = 0;
            if (isNaN(finalQuantityHq)) finalQuantityHq = 0;

            // add to list
            this.itemProfits[id] = {
                finalSale: finalSale,
                finalSaleHq: finalSaleHq,
                finalQuantity: finalQuantity,
                finalQuantityHq: finalQuantityHq
            };
        });

        // save to local stoage
        localStorage.removeItem('basketprices');
        localStorage.setItem('basketprices', JSON.stringify(this.itemProfits));
        $('#basketprices').val(JSON.stringify(this.itemProfits));
    }
}

var CheckoutStorage = new CheckoutStorageClass();
