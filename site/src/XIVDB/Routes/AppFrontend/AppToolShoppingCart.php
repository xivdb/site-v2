<?php

namespace XIVDB\Routes\AppFrontend;

use Symfony\Component\HttpFoundation\Request;

//
// AppToolShoppingCart
//
trait AppToolShoppingCart
{
    protected function _toolShoppingCart()
    {
        //
        // Shopping cart showcase/about page
        //
        $this->route('/shopping-cart', 'GET', function(Request $request)
        {
            $this->setLastUrl($request);
            return $this->respond('Tools/ShoppingCart/index.html.twig');
        });

        //
        // Update cart quantity
        //
        $this->route('/shopping-cart/update-quantity', 'GET|POST', function(Request $request)
        {
            $items = $request->get('items');
            $savedId = $request->get('save_id');

            // get the current user
            $user = $this->getUser();

            // if they're not logged in, error 404
            if (!$user) {
                return $this->error404();
            }

            $url = [];
            foreach($items as $id => $qty) {
                $url[] = sprintf('%sx%s', $id, $qty);
            }

            $url = implode('-', $url);

            // if saved id, update db entry
            if ($savedId) {
                $dbs = $this->getModule('database');
                $dbs->QueryBuilder
                    ->update('content_shopping_cart')
                    ->set('items', ':items')
                    ->where([
                        'id = :id',
                        'user = :user',
                    ])
                    ->bind('items', $url)
                    ->bind('id', $savedId)
                    ->bind('user', $user->id);

                $dbs->execute();
            }

            return $this->redirect('/shopping-cart/'. $url);
        });

        //
        // Shopping Cart Tool
        //
        $this->route('/shopping-cart/{list}', 'GET|POST', function(Request $request, $list)
        {

            $this->setLastUrl($request);

            $dbs = $this->getModule('database');

            // if saving
            if ($this->getUser() && $request->get('description'))
            {
                $dbs->QueryBuilder
                    ->insert('content_shopping_cart')
                    ->schema(['user', 'description', 'notes', 'items', 'basket_prices', 'cart_prices'])
                    ->values([':user', ':desc', ':notes', ':items', ':basket', ':cart'])
                    ->bind('user', $this->getUser()->id)
                    ->bind('desc', $request->get('description'))
                    ->bind('notes', $request->get('notes'))
                    ->bind('items', $list)
                    ->bind('basket', $request->get('basketprices'))
                    ->bind('cart', $request->get('cartprices'))
                    ->duplicate(['description', 'notes', 'items', 'basket_prices', 'cart_prices', 'updated'], true);

                $dbs->execute();
            }

            // get saved cart for this specific cart
            $loadlist = [];
            $saved = false;
            if ($this->getUser()) {
                $dbs->QueryBuilder
                    ->select('*', false)
                    ->from('content_shopping_cart')
                    ->where('items = :list')->bind('list', $list)
                    ->where('user = :user')->bind('user', $this->getUser()->id);

                $saved = $dbs->get()->one();

                // get all saved carts
                $dbs->QueryBuilder
                    ->reset()
                    ->select('*', false)
                    ->from('content_shopping_cart')
                    ->where('user = :user')->bind('user', $this->getUser()->id)
                    ->order('updated', 'desc');

                $loadlist = $dbs->get()->all();
            }

            // Create a cart
            $cart = $this->getModule('shoppingcart');

            // getcontent data
            foreach(explode('-', $list) as $i => $idqty)
            {
                // break after the max
                if ($i >= 30) {
                    break;
                }

                // get id and quantity
                list($id, $quantity) = explode('x', $idqty);
                if (!$quantity) {
                    $quantity = 1;
                }

                // add to cart
                $cart->add($id, $quantity);
            }

            // checkout cart and run statistics
            $cart->checkout();
            $cart->statistics();
            $cartdata = $cart->get();

            // Add to API
            if ($this->isApiRequest($request)) {
                return $this->json($cartdata);
            }

            // respond
            return $this->respond('Tools/ShoppingCart/app.html.twig', [
                'cart' => $cartdata,
                'saved' => $saved,
                'loadlist' => $loadlist,
            ]);
        });
    }
}
