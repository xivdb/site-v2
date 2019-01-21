<?php
/**
 * Build search menus
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\SearchMenus;

class SearchMenuBuilder
{
    private $string = [];

    //
    // Get filter built
    //
    public function get()
    {
        $string = implode('&', $this->string);
        $string = base64_encode($string);

        // reset
        $this->string = [];
        return sprintf('/?filters=%s', $string);
    }

    //
    // Add a string
    //
    public function string($string)
    {
        $string = str_ireplace(' ', '+', $string);
        return sprintf('/?search=%s', $string);
    }

    //
    // Add a single filter
    //
    public function one($value)
    {
        $this->add('one', $value);
        return $this;
    }

    //
    // Add a filter
    //
    public function add($index, $value)
    {
        $value = urlencode($value);
        $this->string[] = sprintf('%s=%s', $index, $value);
        return $this;
    }

    //
    // Order
    //
    public function order($index, $direction)
    {
        $this->add('order_field', $index);
        $this->add('order_direction', $direction);
        return $this;
    }
}
