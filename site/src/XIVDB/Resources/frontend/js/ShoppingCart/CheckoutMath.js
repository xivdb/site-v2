//
// Shopping Cart - CheckoutMath
//
class CheckoutMathClass
{
    constructor()
    {
        this.costs = [];
        this.costsTotal = 0;

        this.profits = [];
        this.profitsTotal = 0;
    }

    //
    // Run calculations!
    //
	calculate()
    {
        this.costs = [];
        this.costsTotal = 0;
        this.profits = [];
        this.profitsTotal = 0;

        // get costs
        this.getCosts();

        // get profits
        this.getProfits();
        this.profitsTotal = this.profitsTotal - this.costsTotal;

        // run figures
        this.runFigures();

        // Saving
        CheckoutStorage.save();
    }

    //
    // Get all costs
    //
    getCosts()
    {
        var $rows = $('.tool-cart-window-all table tr');

        // loop through each rows
        $rows.each((i, element) =>
        {
            // get row
            var $row = $(element),
                id = $row.attr('data-id');

            // cost data
            var data = {
                id: id,
                quantity: 0,
                cost: 0,
            };

            // quantity
            data.quantity = parseInt($row.find('input.quantity').val());

            // cost by default is nq cost
            data.cost = parseInt($row.find('input.cost').val());

            // if nq is inactive
            if ($row.find('input.cost').hasClass('inactive')) {
                data.cost = parseInt($row.find('input.costhq').val());
            }

            // fix some values
            if (isNaN(data.quantity)) data.quantity = 0;
            if (isNaN(data.cost)) data.cost = 0;

            // multiply cost by quantity
            data.cost = data.cost * data.quantity;

            // set values
            this.costsTotal = this.costsTotal + data.cost;
            this.costs.push(data);
        });
    }

    //
    // Get all profits
    //
    getProfits()
    {
        var $rows = $('.right.tool-basket div.item');

        // loop through each rows
        $rows.each((i, element) =>
        {
            // get row
            var $row = $(element),
                id = $row.attr('data-id');

            // cost data
            var data = {
                id: id,
                nqQuantity: 0,
                hqQuantity: 0,
                nqCost: 0,
                hqCost: 0,
            }

            // get quantity and costs
            data.nqQuantity = parseInt($row.find('input.final_quantity').val());
            data.hqQuantity = parseInt($row.find('input.final_quantity_hq').val());
            data.nqCost = parseInt($row.find('input.final_sale').val());
            data.hqCost = parseInt($row.find('input.final_sale_hq').val());

            // fix some values
            if (isNaN(data.nqQuantity)) data.nqQuantity = 0;
            if (isNaN(data.hqQuantity)) data.hqQuantity = 0;
            if (isNaN(data.nqCost)) data.nqCost = 0;
            if (isNaN(data.hqCost)) data.hqCost = 0;

            // multiply cost by quantity
            data.nqCost = data.nqCost * data.nqQuantity;
            data.hqCost = data.hqCost * data.hqQuantity;

            // set values
            this.profitsTotal = this.profitsTotal + (data.nqCost + data.hqCost);
            this.profits.push(data);
        });
    }

    // run figures and work out profit/loss
    runFigures()
    {
        // set profit values
        $('.final-cost em').text(number_format(this.costsTotal));
        $('.final-profit em').text(number_format(this.profitsTotal));

        // remove color values
        $('.final-profit em').removeClass('positive').removeClass('negative');

        // set color based on if in profit or not.
        $('.final-profit em').addClass(this.profitsTotal > 0 ? 'positive' : 'negative');
    }
}

var CheckoutMath = new CheckoutMathClass();
