<?php
/*
MarketPress ΕΛ.ΤΑ weight-based Shipping Plugin
Author: Aristeides Stathopoulos
Version: 1.1
*/

class MP_Shipping_ELTA extends MP_Shipping_API {

  //private shipping method name. Lowercase alpha (a-z) and dashes (-) only please!
  var $plugin_name = 'elta';

  //public name of your method, for lists and such.
  var $public_name = '';

  //set to true if you need to use the shipping_metabox() method to add per-product shipping options
  var $use_metabox = false;
	
	//set to true if you want to add per-product weight shipping field
	var $use_weight = true;

  /**
   * Runs when your class is instantiated. Use to setup your plugin instead of __construct()
   */
	function on_creation() {
    //declare here for translation
    $this->public_name = __('Δέματα ΕΛ.ΤΑ.', 'mp');
	}

  /**
   * Echo anything you want to add to the top of the shipping screen
   */
	function before_shipping_form($content) {
		return $content;
  }
  
  /**
   * Echo anything you want to add to the bottom of the shipping screen
   */
	function after_shipping_form($content) {
		return $content;
  }
  
  /**
   * Echo a table row with any extra shipping fields you need to add to the shipping checkout form
   */
	function extra_shipping_field($content) {
		return $content;
  }
  
  /**
   * Use this to process any additional field you may add. Use the $_POST global,
   *  and be sure to save it to both the cookie and usermeta if logged in.
   */
	function process_shipping_form() {

  }

	/**
   * Echo a settings meta box with whatever settings you need for you shipping module.
   *  Form field names should be prefixed with mp[shipping][plugin_name], like "mp[shipping][plugin_name][mysetting]".
   *  You can access saved settings via $settings array.
   */
	function shipping_settings_box($settings) {
	global $mp;
    ?>
    <div class="alert alert-success">
    	<?php _e('by choosing to send your products with ΕΛ.ΤΑ, the shipping is calculated based on your products weight.', 'mp') ?> 
    </div>
    <div class="alert alert-info">
    	<strong><?php __('Attention:') ?></strong>
    	<?php _e('If you have not set the weight of a product, the it will be calculated as FREE shipping.', 'mp') ?>
    </div>
    <div class="alert alert-error">
    	<strong><?php _e('What you should keep in mind:', 'mp') ?></strong>
    	<p><?php _e('Shipping costs are automatically calculated for domestic, EU and international destinations.
    	However, ΕΛ.ΤΑ. does not accept for EU and international shippings packages heavier than 20-25 Kg.
    	In case your products are heavy, we recomend choosing from the above list of supported countries only Greece.', 'mp') ?></p>
    </div>
    <?php

  }

  /**
   * Filters posted data from your form. Do anything you need to the $settings['shipping']['plugin_name']
   *  array. Don't forget to return!
   */
	function process_shipping_settings($settings) {

    return $settings;
  }

  /**
   * Echo any per-product shipping fields you need to add to the product edit screen shipping metabox
   *
   * @param array $shipping_meta, the contents of the post meta. Use to retrieve any previously saved product meta
   * @param array $settings, access saved settings via $settings array.
   */
	function shipping_metabox($shipping_meta, $settings) {

  }

  /**
   * Save any per-product shipping fields from the shipping metabox using update_post_meta
   *
   * @param array $shipping_meta, save anything from the $_POST global
   * return array $shipping_meta
   */
	function save_shipping_metabox($shipping_meta) {

    return $shipping_meta;
  }

  /**
* Use this function to return your calculated price as an integer or float
*
* @param int $price, always 0. Modify this and return
* @param float $total, cart total after any coupons and before tax
* @param array $cart, the contents of the shopping cart for advanced calculations
* @param string $address1
* @param string $address2
* @param string $city
* @param string $state, state/province/region
* @param string $zip, postal code
* @param string $country, ISO 3166-1 alpha-2 country code
* @param string $selected_option, if a calculated shipping module, passes the currently selected sub shipping option if set
*
* return float $price
*/
function calculate_shipping($price, $total, $cart, $address1, $address2, $city, $state, $zip, $country, $selected_option) {
	global $mp;
    $settings = get_option('mp_settings');

    //calculate extra shipping
    $weights = array();
    foreach ($cart as $product_id => $variations) {
	    $shipping_meta = get_post_meta($product_id, 'mp_shipping', true);
			foreach ($variations as $variation => $data) {
			  if (!$data['download'])
	      	$weights[] = $shipping_meta['weight'] * $data['quantity'];
			}
    }
    $totalweight = array_sum($weights);

	if ($totalweight <= 1){
		if ( in_array($settings['base_country'], $mp->eu_countries) ){
			if (in_array($country, $mp->eu_countries)) {
				if ($country == $settings['base_country'])
					$price = 3.5;
				else $price = 25.27;
			}
		else {$price = 29.26;}
    	}
	}
	else if ($totalweight >= 1 && $totalweight < 24 ){
		if ( in_array($settings['base_country'], $mp->eu_countries) ) {
			if (in_array($country, $mp->eu_countries)) {
				if ($country == $settings['base_country']) { //greece gia times < 24 kg
					$price = (intval($totalweight) * 0.67) + 2.9;
				} else {$price = 25.27+(($totalweight-1)*4.36);}
			} else {$price = 29.26+(($totalweight-1)*5.64);}
		}
	}
    return $price;
  }

}

//register plugin - uncomment to register
mp_register_shipping_plugin( 'MP_Shipping_ELTA', 'elta', __('Δέματα ΕΛ.ΤΑ.', 'mp') );
?>
