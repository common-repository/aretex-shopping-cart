<?php
/**
 * Plugin Name: AreteX&trade; Shopping Cart
 * Plugin URI: https://aretex.org
 * Description:  Shopping Cart for AreteX eCommerce Services. AreteX eCommerce Services must be installed and active.
 * Version: 1.01.01
 * Author: 3B Alliance, LLC
 * Author URI: http://3balliance.com
 * License: GPL 2 or Later 
 */

/* Copyright 2013 3B Alliance, LLC (email : support@3balliance.com)
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.
    
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    GNU General Public License for more details.
    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/


require_once( plugin_dir_path( __FILE__ ) . 'AreteXCart/AreteXCart.class.php' );
register_activation_hook( __FILE__, array( 'AreteXCart', 'install' ) );
register_deactivation_hook( __FILE__, array( 'AreteXCart', 'deactivate' ) );

//create plugin object
global $AreteXCart;
$AreteXCart = new AreteXCart();



?>
