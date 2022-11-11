<?php
/**
 * ## Question 3
 * Given:
 * - An item contains the following properties:
 * `id`
 * `name`
 * `quantity`
 * `price`
 * - A customer contains the following properties:
 * `first_name`
 * `last_name`
 * Addresses
 * - An address contains the following properties:
 * `line_1`
 * `line_2`
 * `city`
 * `state`
 * `zip`
 *
 * - An instance of a cart can have only one customer and multiple items.
 * - A tax rate of 7%
 * - Access to shipping rate api (no need to find a working one, simply assume the methods exist elsewhere in the system and access them as you will)
 *
 * Question:
 *
 * Please write two or more classes that allow for the setting and retrieval of the following information:
 * - Customer Name
 * - Customer Addresses
 * - Items in Cart
 * - Where Order Ships
 * - Cost of item in cart, including shipping and tax
 * - Subtotal and total for all items
 *
 */

/**
 * Class base - to save time I created generic setters/getters for specific properties,
 * although it will only set the value of a property if it exists in the class in the first place.
 * There is also the option for it to throw an exception if an invalid value is sent into
 * get/set (although I don't use it for this example).
 * All other classes inherit from this class.
 */
class base {
    protected $debug_mode = "silent_fail"; //silent_fail vs throw_error

    public function get($property = "") {
        if(!$property) {return false;}

        if(property_exists(get_class($this), $property)) {
            return $this->{$property};
        }

        if($this->debug_mode == "throw_error") {
            throw new Exception("The property '$property' does not exist.");
        }
        return false;
    }

    public function set($property = "", $value = null) {
        if(!$property) {return false;}

        if(property_exists(get_class($this), $property)) {
            $this->{$property} = $value;
            return true;
        }

        if($this->debug_mode == "throw_error") {
            throw new Exception("Cannot set value to '$value'. The property '$property' does not exist.");
        }
        return false;
    }
}
class customer extends base {
    protected $first_name;
    protected $last_name;
    protected $addresses = [];

    public function get($property = "") {
        if($property == "name") {
            return $this->get_name();
        } else {
            return parent::get($property);
        }
    }

    public function set($property = "", $value = "") {
        if($property == "name") {
            $name_array = explode(" ", $value);
            return $this->set_name($name_array[0], implode(" ", array_splice($name_array, 1))); //last name is all parts of name_array after first name.
        } else {
            return parent::set($property, $value);
        }
    }

    public function get_name() {
        return trim($this->get("first_name") . " " . $this->get("last_name"));
    }

    public function set_name($first_name = "", $last_name = "") {
        $this->set("first_name", $first_name);
        $this->set("last_name", $last_name);
        return true;
    }

    public function get_addresses() {
        return $this->addresses;
    }

    public function add_address($address_array = []) {
        if(empty($address_array))
            return false;

        $address = new address();
        $address->load($address_array);
        $this->addresses[] = $address;
        return true;
    }
}

class address extends base {
    protected $line_1;
    protected $line_2;
    protected $city;
    protected $state;
    protected $zip;

    public function load($address_array = []) {
        if(empty($address_array))
            return false;

        foreach($address_array as $key => $value) {
            if(property_exists(get_class($this), $key)) {
                $this->set($key, $value);
            }
        }
        return true;
    }
}
class item extends base {
    protected $id;
    protected $name;
    protected $quantity;
    protected $price;

    protected $subtotal;
    protected $tax;
    protected $shipping;
    protected $total;

    public function load($item_array = []) {
        if(empty($item_array))
            return false;

        foreach($item_array as $key => $value) {
            if(property_exists(get_class($this), $key)) {
                $this->set($key, $value);
            }
        }
        return true;
    }

    public function calculate_tax($tax_rate = 0.7) {
        $this->tax = number_format($this->subtotal * $tax_rate, 2);
        return $this->tax;
    }

    /**
     * @param int $shipping_cost - calculated shipping cost based off of theoretical API and shipping address.
     * @param int $item_price_percentage - percentage of total that item's cost comprises. Ex. 2 items, one is 25% of total, other is 75%. Item 1's price percentage is 0.25.
     */
    public function calculate_shipping($shipping_cost = 0, $item_price_percentage = 0) {
        $this->shipping = round($shipping_cost * $item_price_percentage, 2);
    }

    public function calculate_subtotal() {
        $this->subtotal = round($this->price * $this->quantity, 2);
        return $this->subtotal;
    }

    public function calculate_total() {
        $this->total = $this->subtotal + $this->shipping + $this->tax;
        return $this->total;
    }
}

class cart_order extends base {
    protected $customer;
    protected $items = [];
    protected $shipping_address;

    protected $subtotal;
    protected $tax;
    protected $shipping;
    protected $total;

    protected $tax_rate = 0.07; //hard-coded for this exercise


    public function get_items() {
        return $this->items;
    }

    public function add_item($item_array = []) {
        $item = new item();
        $item->load($item_array);
        $this->items[] = $item;
        return true;
    }

    public function set_shipping_address($address = null) {
        if(!$address)
            return false;

        $this->shipping_address = $address;
    }

    public function calculate() {
        foreach($this->items as $item) {
            $this->subtotal += $item->calculate_subtotal();
            $this->tax += $item->calculate_tax($this->tax_rate);
        }

        $shipping_cost = $this->calculate_total_shipping($this->shipping_address);
        $this->shipping = $shipping_cost;

        //we have to re-iterate over items because we have not yet totaled the full cost of all cart items.
        foreach($this->items as $item) {
            $percentage = $item->get("subtotal") / $this->subtotal;
            $this->shipping += $item->calculate_shipping($this->shipping, $percentage);
            $this->total += $item->calculate_total();
        }
    }

    public function calculate_total_shipping($shipping_address = null) {
        //assume API magic. for the purposes of this exercise, we will just assume shipping is $4.27.
        return 4.27;
    }
}

//set up customer
$customer = new customer();
$customer->set("name", "Bob Smith");

echo "TEST: set name: <br>\n";
print_r($customer);
echo "<br>\n--------------------<br>\n";

//set up multiple addresses
$address1['line1'] = "123 Abc St";
$address1['line2'] = "";
$address1['city'] = "Seattle";
$address1['state'] = "WA";
$address1['zip'] = "98101";

$address2['line1'] = "4545 Def Blvd";
$address2['line2'] = "";
$address2['city'] = "Lynnwood";
$address2['state'] = "WA";
$address2['zip'] = "98037";

$customer->add_address($address1);
$customer->add_address($address2);

echo "TEST: set addresses: <br>\n";
print_r($customer);
echo "<br>\n<--------------------<br>\n";

//set up cart
$cart = new cart_order();
$cart->set("customer", $customer);

echo "TEST: set up new cart with customer: <br>\n";
print_r($cart);
echo "<br>\n--------------------<br>\n";

//set up items
$item1 = ['id' => 123, 'name' => 'apple', 'quantity' => 3, 'price' => 3.00];
$item2 = ['id' => 234, 'name' => 'pack of pencils', 'quantity' => 1, 'price' => 2.50];

$cart->add_item($item1);
$cart->add_item($item2);

$shipping_address = $address2;
$cart->set_shipping_address($shipping_address);
echo "TEST: add 2 items to cart and set shipping address: <br>\n";
print_r($cart);
echo "<br>\n--------------------<br>\n";

//calculate items' totals, etc.
$cart->calculate();
print_r($cart);
echo "TEST: calculate shipping, tax, subtotals, and totals in cart plus each item: <br>\n";
print_r($cart);
echo "<br>\n--------------------<br>\n";
