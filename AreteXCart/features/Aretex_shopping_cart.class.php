<?php

/**
 * @FeatureName: AreteX Shopping Cart
 *                   -- The Feature Name must be unique
 * @Description: Enables adds shopping cart functionality to AreteX eCommerce Services.
 * @FeatureType: Functionality
 * @FeatureClass: AreteX_shopping_cart
 * @AreteXMenuPath: 
 * @AreteXMenuTitle: Shopping Cart
 * @IconPath:               
 * @IconName: Shopping Cart
 * @LoadFeature: Y
 *        -- Valid Values are: Y (Load it without asking); 
 *                             N (Do not load it - Do not ask); 
 *                             Q (Ask about loading it);
 * @FeatureVersion: 1.00.00
 * @AretexServerVersion: 2.19.01
 * 
 * 
 */

/**
 * Copyright 2014, 3B Alliance, LLC. Some rights reserved.
 * 
 * Licensed under GPL v2
 * 
 * Provided "AS IS" without warranty
 * 
 * */
 
 
if ( ! class_exists( 'AreteX_shopping_cart' ) ) {
    
     class AreteX_shopping_cart {
        private static $exists; 
         
        public function __construct() {
        if (!session_id())
                session_start();
            if (self::$exists) // force singleton pattern.
                return;
            self::install();
    		self::$exists = true;
            
            $this->add_admin_ajax_actions();
            $this->add_user_ajax_actions();                                                       
            $this->add_shortcodes();            
        }
        
        public static function install() {
            
            $license =  get_option('aretex_license_key');
            $valid_license = false;
            $license_key = get_option('aretex_license_key');
            if (! empty($license_key)){
                if (! class_exists('AreteX_WPI'))
                {
                    $aretex_core_path = get_option('aretex_core_path');
                    if (empty($aretex_core_path))
                    {
                       // add_action( 'admin_notices',  array('AreteX_paid_content','core_failure_notice') );
                        return;
                    }
                       
                    if (file_exists($aretex_core_path .'AreteX_WPI.class.php'))
                       require_once($aretex_core_path .'AreteX_WPI.class.php');
                    else
                    {                    
                       // add_action( 'admin_notices',  array('AreteX_paid_content','core_failure_notice') );
                        return;                    
                    }
                    
               
                }
                
                if (class_exists('AreteX_WPI'))        
                    $valid_license = AreteX_WPI::validate_license($license_key);
                
            }
            
            if (! $valid_license) {              
               // add_action( 'admin_notices',  array('AreteX_paid_content','core_failure_notice') );
                return;
            }
            else
            {
                // See if it already exists ...
                $installed = self::checkInstallation();
                if (! $installed)
                    return;
                
                if ($installed['feature_installed'] == 'N' && $installed['load_feature'] == 'Y') {
                     global $wpdb;
                     $table_name = $wpdb->prefix .'aretex_features';
                  //   self::build_db_tables();
                     $installed['feature_installed'] = 'Y';
                     $wpdb->replace( $table_name, $installed, null ); 
                     
                     
                }
                  
                  
                
            }
        }
        
        protected static function checkInstallation() {
              global $wpdb;
              $table_name = $wpdb->prefix .'aretex_features';
              $feature_name = 'AreteX Shopping Cart';
              $rows = $wpdb->get_results( "SELECT * FROM $table_name WHERE feature_name='$feature_name' ", ARRAY_A  );
              if (! empty($rows[0]['feature_name']))
              {
                 return $rows[0];
              }
              
              return null;
        }
        
        protected function add_admin_ajax_actions() {
            
             add_action('wp_ajax_atx_add_to_cart',array(&$this,'atx_add_to_cart'));
             add_action('wp_ajax_atx_empty_cart',array(&$this,'atx_empty_cart'));
             add_action('wp_ajax_atx_delete_from_cart',array(&$this,'atx_delete_from_cart'));
             add_action('wp_ajax_atx_update_dlg_content',array(&$this,'atx_update_dlg_content'));
             add_action('wp_ajax_atx_update_cart_items',array(&$this,'atx_update_cart_items'));
             add_action('wp_ajax_atx_complete_checkout',array(&$this,'atx_complete_checkout'));
             add_action('wp_ajax_atx_cart_summary',array(&$this,'atx_cart_summary'));
        }
        
        
        
        protected function add_user_ajax_actions() {
            
            add_action('wp_ajax_nopriv_atx_add_to_cart',array(&$this,'atx_add_to_cart'));             
            add_action('wp_ajax_nopriv_atx_empty_cart',array(&$this,'atx_empty_cart'));
            add_action('wp_ajax_nopriv_atx_delete_from_cart',array(&$this,'atx_delete_from_cart'));
            add_action('wp_ajax_nopriv_atx_update_dlg_content',array(&$this,'atx_update_dlg_content'));
            add_action('wp_ajax_nopriv_atx_update_cart_items',array(&$this,'atx_update_cart_items'));            
            add_action('wp_ajax_nopriv_atx_complete_checkout',array(&$this,'atx_complete_checkout'));
            add_action('wp_ajax_nopriv_atx_cart_summary',array(&$this,'atx_cart_summary'));
            
        }
       
       public function atx_update_dlg_content() {
             if (!session_id())
                session_start();
                
            $aretex_core_path = get_option('aretex_core_path');
            if (empty($aretex_core_path)) {             
                echo 'Error';
                die(); 
            }
            
            if (file_exists($aretex_core_path .'AreteXClientEngine/Checkout.class.php')) {
                require_once($aretex_core_path .'AreteXClientEngine/Checkout.class.php');
                require_once($aretex_core_path.'AreteXClientEngine/Crypton/Crypton.php');
                require_once($aretex_core_path .'AreteX_WPI.class.php');
                require_once(plugin_dir_path( __FILE__ ).'AreteXWP_Cart.php');
            }
            else {  
                echo 'Error';
                die();                  
            }
            
            self::fixObject($_SESSION['aretex_shopping_cart']);
            
            $line_key = $_GET['line_key'];
            
            $options = $_SESSION['aretex_shopping_cart']->all_options[$line_key];
            $item = $_SESSION['aretex_shopping_cart']->items[$line_key];
            $form = '';
            $form_id  = self::build_updc_form($line_key,$item->code,$item->qty,$options,$item->options,$form);
            echo $form;
            die();
             
             
            
       }
       
       public function atx_delete_from_cart() {
             if (!session_id())
                session_start();
       
            
            $aretex_core_path = get_option('aretex_core_path');
            if (empty($aretex_core_path)) {             
                echo 'Error';
                die(); 
            }
            
            if (file_exists($aretex_core_path .'AreteXClientEngine/Checkout.class.php')) {
                require_once($aretex_core_path .'AreteXClientEngine/Checkout.class.php');
                require_once($aretex_core_path.'AreteXClientEngine/Crypton/Crypton.php');
                require_once($aretex_core_path .'AreteX_WPI.class.php');
                require_once(plugin_dir_path( __FILE__ ).'AreteXWP_Cart.php');
            }
            else {  
                echo 'Error';
                die();                  
            }
            
            self::fixObject($_SESSION['aretex_shopping_cart']);
            
            $line_key = (int) $_POST['line_key'];
            $item =  $_SESSION['aretex_shopping_cart']->items[$line_key];
            $skip_exclusivity = false;
            foreach($_SESSION['aretex_shopping_cart']->items as $key=>$itm) {
                if ($key != $line_key && $itm->code == $item->code) {
                    $skip_exclusivity = true;
                }
            }
            

            
            if (! $skip_exclusivity) {
                $del_exclu = array();                     
                foreach($_SESSION['aretex_shopping_cart']->exclusive_item as $exclusivity=>$code) {
                    if ($code == $item->code)
                        $del_exclu[] = $exclusivity;
                }
                foreach($del_exclu as $exclusivity) {
                    unset($_SESSION['aretex_shopping_cart']->exclusive_item[$exclusivity]);
                }

            }
            unset($_SESSION['aretex_shopping_cart']->items[$line_key]);

            $_SESSION['aretex_shopping_cart']->items = array_values($_SESSION['aretex_shopping_cart']->items);

                 
            unset($_SESSION['aretex_shopping_cart']->item_options[$line_key]);
            $_SESSION['aretex_shopping_cart']->item_options = array_values($_SESSION['aretex_shopping_cart']->item_options );
            unset($_SESSION['aretex_shopping_cart']->all_options[$line_key]);
            $_SESSION['aretex_shopping_cart']->all_options = array_values($_SESSION['aretex_shopping_cart']->all_options);
            
            unset($_SESSION['aretex_shopping_cart']->display_options[$line_key]);
            $_SESSION['aretex_shopping_cart']->display_options = array_values($_SESSION['aretex_shopping_cart']->display_options);


            self::populate_session_cart(false); 
            
            if ( is_user_logged_in() ) {
                global $user_login; 
                global $user_ID;
                get_currentuserinfo(); // Populate the globals ... 
                
                update_user_meta($user_ID, 'aretex_cart', $_SESSION['aretex_shopping_cart']);
                
            }
                                              
            $str = self::build_cart_table();
            echo $str;
            die();

        
       }
        
       public function atx_empty_cart() {
        
         if (!session_id())
                session_start();
                
            $aretex_core_path = get_option('aretex_core_path');
            if (empty($aretex_core_path)) {
              //  error_log("Failing 1");
                echo 'Error';
                die(); 
            }
            
            if (file_exists($aretex_core_path .'AreteXClientEngine/Checkout.class.php')) {
                require_once($aretex_core_path .'AreteXClientEngine/Checkout.class.php');
                require_once($aretex_core_path.'AreteXClientEngine/Crypton/Crypton.php');
                require_once($aretex_core_path .'AreteX_WPI.class.php');
                require_once(plugin_dir_path( __FILE__ ).'AreteXWP_Cart.php');
            }
            else {  
             //    error_log("Failing 2");
                echo 'Error';
                die();                  
            }      

             
            $_SESSION['aretex_shopping_cart'] = self::setup_cart();
             
            if ( is_user_logged_in() ) {
                global $user_login; 
                global $user_ID;
                get_currentuserinfo(); // Populate the globals ... 
                
                update_user_meta($user_ID, 'aretex_cart', $_SESSION['aretex_shopping_cart']);
                
            }
            
            
            $str = self::build_cart_table();
            echo $str;
            die();
        
       }
       
       protected static function build_cart_table() {
            $str = self::start_cart_table();
            foreach($_SESSION['aretex_shopping_cart']->items as $key=>$item) {
                    $str .= self::cart_item($key,$item,$_SESSION['aretex_shopping_cart']->item_options[$key]);                    
            }
            $str .= self::end_cart_table();
                      
            
            return $str; 
        
       }
        
        protected function setup_cart() {

          
            
            $tracking = AreteX_WPI::getCurrentTrackingCode(); 
            $cart = new AreteXWP_Cart();
            
            if ($tracking->valid) {
                $cart->tracking_code = $tracking->standard;
                $cart->tracking_code_signature = $tracking->validation;
            }
            else {
                $cart->tracking_code = null;
                $cart->tracking_code_signature = null;
            }
            $cart->txn_type = TxnType::sale;
 
            
            return $cart;              
        }
        
        public function atx_complete_checkout() {
            $cart = self::checkout_data();
            if ( is_user_logged_in() ) {
                global $user_login; 
                global $user_ID;
                get_currentuserinfo(); // Populate the globals ...
                $customer_id = get_user_meta($user_ID, 'atx_customer_id', true);
                if (! $customer_id)
                {
                    update_user_meta( $user_ID, 'atx_customer_id', 'Pending'); 
                    // Ask AreteX for Customer Data Next Time User Logs in
    
                }
             }

            
             $url = get_option('aretex_pcs_in_endpoint');
            $url .= '/begin_cc_co.php';
            $return['data'] = $cart; // Was already JSON encoded ...
            $return['url'] = $url; 
            
            $return = json_encode($return);
            
          //  error_log("Button Code: ".var_export($button_code,true));
          
           // we are ready to check out so we're done with the cart 
            $_SESSION['aretex_shopping_cart'] = self::setup_cart();
             
            if ( is_user_logged_in() ) {
                global $user_login; 
                global $user_ID;
                get_currentuserinfo(); // Populate the globals ... 
                
                update_user_meta($user_ID, 'aretex_cart', $_SESSION['aretex_shopping_cart']);
                
            }
 
            echo $return; 
            
           	die(); // this is required to return a proper result
                      
           
            
        }
        
        public static function checkout_data() {
        
            $license_key = get_option('aretex_license_key');
            $app_key = get_option('aretex_api_key');
            $crypt_keys = AreteX_WPI::GetKeys();
            global $user_login; 
            global $user_ID;
            get_currentuserinfo(); // Populate the globals ... 
            $customer_id = AreteX_WPI::customerSignedUp();
            if (! ctype_digit($customer_id)) {
                $customer_id = 0;
            }
                                                                         
            $idv = new CartIdenityValidation($license_key,$app_key,'master',$customer_id,false,$user_login); 
                     // Yes, Idenity is misspelled ... I'll get to it.
            $signed_idv = new SignedCartIdenityValidation();
            $signed_idv->cartIdentity = base64_encode(json_encode($idv));
            $signed_idv->signature = AreteX_API::Sign($signed_idv->cartIdentity, $crypt_keys['privatekey'] );
    
            
            
            $aretex_core_path = get_option('aretex_core_path');
            if (file_exists($aretex_core_path .'AreteXClientEngine/Checkout.class.php')) {
                    require_once($aretex_core_path .'AreteXClientEngine/Checkout.class.php');
                    require_once($aretex_core_path.'AreteXClientEngine/Crypton/Crypton.php');
                    require_once($aretex_core_path .'AreteX_WPI.class.php');
                    require_once(plugin_dir_path( __FILE__ ).'AreteXWP_Cart.php');
                }
                else {  
                 //    error_log("Failing 2");
                    echo 'Error';
                    die();                  
                }      
    
            
            
            self::fixObject($_SESSION['aretex_shopping_cart']);
            $items = array();
            foreach($_SESSION['aretex_shopping_cart']->items as $item)
                $items[] = class_cast_2('Item',$item);
                
         
        
            $cart =  class_cast_2('Cart',$_SESSION['aretex_shopping_cart']);
            $cart->items = $items;
            
                   
            $cart->summary_page = SummaryPageType::skip_to_cc;
            $cart->totalOrder();
            
            
            
            $checkout = new Checkout();
            $checkout->identity_validation = base64_encode(json_encode($signed_idv));
            $checkout->cart = base64_encode(json_encode($cart));
            $message = $checkout->identity_validation.$checkout->cart;
            $checkout->checkout_validation = AreteX_API::Sign($message ,$crypt_keys['privatekey']);
            
            return json_encode($checkout);
   
    }
 
        
    protected function updateCartTracking($cart) {
            $tracking = AreteX_WPI::getCurrentTrackingCode(); 
            if ($tracking->valid) {
                $cart->tracking_code = $tracking->standard;
                $cart->tracking_code_signature = $tracking->validation;
            }
            else {
                $cart->tracking_code = null;
                $cart->tracking_code_signature = null;
            }
            
            return $cart;
    }
        
  
  public function atx_cart_summary() {
             $aretex_core_path = get_option('aretex_core_path');

             if (!session_id()) {
                session_start();                
             }            
            if (file_exists($aretex_core_path .'AreteXClientEngine/Checkout.class.php')) {
                require_once($aretex_core_path .'AreteXClientEngine/Checkout.class.php');
                require_once($aretex_core_path.'AreteXClientEngine/Crypton/Crypton.php');
                require_once($aretex_core_path .'AreteX_WPI.class.php');
                require_once(plugin_dir_path( __FILE__ ).'AreteXWP_Cart.php');
            }
           AreteX_shopping_cart::fixObject($_SESSION['aretex_shopping_cart']);
          
           if (is_object($_SESSION['aretex_shopping_cart'])) {
             $total_items = 0;
             if (is_array($_SESSION['aretex_shopping_cart']->items)) {
                foreach($_SESSION['aretex_shopping_cart']->items as $item) {
                    $total_items += $item->qty;
                }                
             }
             

             echo '<strong>Items: </strong><span class="aretex_cart_qty">'.$total_items.'</span></br>';
             echo '<strong>Total: </strong><span class="aretex_cart_ttl">$'.number_format($_SESSION['aretex_shopping_cart']->total_due,2).'</span></br><br/>';

           }
           die();
  }
  
       public function atx_update_cart_items() {
            session_start();
            $form_data = array(); 
            parse_str($_POST['form_data'],$form_data);
            
            $aretex_core_path = get_option('aretex_core_path');
            if (empty($aretex_core_path)) {
              //  error_log("Failing 1");
                echo json_encode('Error');
                die(); 
            }
            
            if (file_exists($aretex_core_path .'AreteXClientEngine/Checkout.class.php')) {
                require_once($aretex_core_path .'AreteXClientEngine/Checkout.class.php');
                require_once($aretex_core_path.'AreteXClientEngine/Crypton/Crypton.php');
                require_once($aretex_core_path .'AreteX_WPI.class.php');
                require_once(plugin_dir_path( __FILE__ ).'AreteXWP_Cart.php');
            }
            else {  
             //    error_log("Failing 2");
                echo json_encode('Error');
                die();                  
            }
            
            self::fixObject($_SESSION['aretex_shopping_cart']);
            $line_key = $form_data['line_key'];
            if ($form_data['quantity'] <= 0) {
                unset($_SESSION['aretex_shopping_cart']->items[$line_key]);
                                
            }
            else {
                 $_SESSION['aretex_shopping_cart']->items[$line_key]->qty = $form_data['quantity'];
                 $_SESSION['aretex_shopping_cart']->items[$line_key]->options = $form_data['option'];
            }
            
            $display_options  = $_SESSION['aretex_shopping_cart']->display_options[$line_key];
           
            $options = $_SESSION['aretex_shopping_cart']->items[$line_key]->options;
            
             foreach($options as $option_name=>$value) {
                 $_SESSION['aretex_shopping_cart']->item_options[$line_key][$display_options[$option_name]] = $value;
            }
            
             self::populate_session_cart();            
            $cart = self::build_cart_table();
            
            echo $cart;
            
            die();

        
       }
        
        public function atx_add_to_cart() {
            //error_log("Add to Cart:".var_export($_POST,true));
            if (!session_id())
                session_start();
            $form_data = array(); 
            parse_str($_POST['form_data'],$form_data);
        //    error_log("Form Data:".var_export($form_data,true));
            
            $aretex_core_path = get_option('aretex_core_path');
            if (empty($aretex_core_path)) {
              //  error_log("Failing 1");
                echo json_encode('Error');
                die(); 
            }
            
            if (file_exists($aretex_core_path .'AreteXClientEngine/Checkout.class.php')) {
                require_once($aretex_core_path .'AreteXClientEngine/Checkout.class.php');
                require_once($aretex_core_path.'AreteXClientEngine/Crypton/Crypton.php');
                require_once($aretex_core_path .'AreteX_WPI.class.php');
                require_once(plugin_dir_path( __FILE__ ).'AreteXWP_Cart.php');
            }
            else {  
             //    error_log("Failing 2");
                echo json_encode('Error');
                die();                  
            }
            
            self::fixObject($_SESSION['aretex_shopping_cart']);
            if (! is_object($_SESSION['aretex_shopping_cart']) ) {
                
                $_SESSION['aretex_shopping_cart'] = self::setup_cart();
            }
            if ( is_user_logged_in() ) {
                global $user_login; 
                global $user_ID;
                get_currentuserinfo(); // Populate the globals ... 
                
                $user_cart =  get_user_meta($user_ID, 'aretex_cart', true);
                
            }
            if (is_array($user_cart->items) && empty($_SESSION['aretex_shopping_cart']->items)) {               
                 $_SESSION['aretex_shopping_cart'] = $user_cart;
                 $_SESSION['aretex_shopping_cart']->totalOrder();
            }
            
            if (is_array($form_data['option']))
                $option = $form_data['option'];
            else
                $option = array(); 
            
            $_SESSION['aretex_shopping_cart']->addItem($form_data['product_code'],$form_data['quantity'],$option,$form_data['display_option'],$form_data['all_options'],$form_data['exclusivity'],$form_data['allow_qty_change']);
          //  error_log("OK");
            if ( is_user_logged_in() ) {
                global $user_login; 
                global $user_ID;
                get_currentuserinfo(); // Populate the globals ... 
                
                update_user_meta($user_ID, 'aretex_cart', $_SESSION['aretex_shopping_cart']);
                
            }

             echo json_encode('OK');
           	die(); 
        }
        
        protected function add_shortcodes() {
            
          //   add_shortcode('a_shortcode', array( 'AreteX_shopping_cart', 'a_shortcode' ) );
    
          add_shortcode('aretex_add_to_cart', array( 'AreteX_shopping_cart', 'aretex_add_to_cart' ) );          
          add_shortcode('aretex_show_cart', array( 'AreteX_shopping_cart', 'aretex_show_cart' ) );
                       
            
        }
        
        static protected function make_select($name,$option_list) {
            $str = '<select name="option['.$name.']">';
            $start = strpos($option_list,'(');
            $end = strpos($option_list,')');
            $list = substr($option_list,$start,$end);
            $list = trim($list,'()');
            $options = explode(',',$list);
            if (is_array($options)) {
                foreach($options as $option) {
                    $option = trim($option);
                    $str .= "<option>$option</option>";
                }
                
            }
            $str .= "</select>";
            
            return $str;
            
        }
        
        static protected function make_text_input($name,$detail) {
            
            $start = strpos($detail,'(');
            $end = strpos($detail,')');
            $num = substr($detail,$start,$end);
            if (ctype_digit($num)) {
               $size = 'size="'.$num.'" maxlength="'.$num.'"';
            }
            else
                $size = '';
            
            $str = '<input type="text" name="option['.$name.']" '.$size.' />';
            
            return $str;
        }
        
        static function parse_option_input($label_name,$option_detail) {
            
            $input = '';
            $option_name = strtolower($label_name);
            $option_name = str_replace(' ','_',$option_name);
            $option_detail = trim($option_detail);
            list($option_detail,$deliverable_code) = explode('|',$option_detail); 
            if (! empty($deliverable_code)) {
                $input .= '<input type="hidden" name="deliverable_option['.$option_name.']"  value="'.$deliverable_code.'"/>';
                
            }
            $input .= '<input type="hidden" name="display_option['.$option_name.']" value="'.$label_name.'" />';
            switch(strtoupper($option_detail[0])) {
                case 'S':
                    $input .= self::make_select($option_name,$option_detail);
                break;
                case 'T':
                    $input .= self::make_text_input($option_name,$option_detail);
                break;
                
            }
            
            return $input;
            
        }
        
        static function parse_options($content) {
            if (empty($content))
                return '';
            $content = strip_tags($content);
            $str = '';
            $options = strstr($content,'options:');
            $start = strstr($options,'{');
            $endpos = strpos($start,'}');
            $options = substr($start,0,$endpos);
            $options = trim($options);
            $options = trim($options,'{}');
            $options = preg_split('/\r\n|\r|\n/', $options);
            foreach($options as $option) {
                $option = trim($option);
                if (empty($option)) {
                    continue;
                }
                list($label,$detail) = explode(':',$option);
                $line = '<div class="aretex_atc_option"><label class="aretex_atc_label">'.$label.'</label> ';
                $line .= self::parse_option_input($label,$detail) .'</div>';
                $str .= $line;                
            }
             
            return $str;
            
        }
        
        
        static function build_updc_form($line_key,$product_code,$quantity,$options,$item_options,&$form) {                        
            $options = base64_decode($options);
            $options = unserialize($options);
            $allow_qty = $options['allow_qty'];
            $options = $options['all_options'];
            
            $jsValSet = '';
            if (is_array($item_options)) {
                foreach($item_options as $opt_name=>$opt_value) {
                    $jsValSet .= "jQuery(('input[name=\"option[$opt_name]\"]')).val('$opt_value');\n";
                }
            }
            
            if (is_array($item_options)) {
                foreach($item_options as $opt_name=>$opt_value) {
                    $jsValSet .= "jQuery(('select[name=\"option[$opt_name]\"]')).val('$opt_value');\n";
                }
            }

            
            if (strtolower($allow_qty) == 'true') {
                $qty = '<div class="aretex_atc_option"><label class="aretex_atc_label">Quantity</label> ';
                $qty .= '<input type="number" size="5" name="quantity" value="'.$quantity.'"/></div><input type="hidden" name="allow_qty_change" value="true" />';
            }
            else {
              $qty = '<input type="hidden" name="quantity" value="1" /><input type="hidden" name="allow_qty_change" value="false" />';   
            }
               
            $form =<<<END_ATC_FORM
            <div class="aretex_atc_button_div">
            <form class="aretex_atc_form" id="atx_upd_cart_form">
            $qty            
            $options
            <input type="hidden" name="product_code" value="$product_code" />
            <input type="hidden" name="exclusivity" value="$exclusivity" />
            <input type="hidden" name="all_options" value="$all_options" />
            <input type="hidden" name="line_key" value="$line_key" />
            </form>
            </div>           
            <script>
            $jsValSet
            </script>
END_ATC_FORM;
            return $form;
          }

        
        
        static function build_atc_form($product_code,$content,$allow_qty,$exclusivity,&$form) {
            $form_id = uniqid('atc_');            
            $options = self::parse_options($content);
            $edit_opts['allow_qty'] = $allow_qty;
            $edit_opts['all_options'] = $options;
            $edit_opts = serialize($edit_opts);
            $all_options = base64_encode($edit_opts);
            
            if (strtolower($allow_qty) == 'true') {
                $qty = '<div class="aretex_atc_option"><label class="aretex_atc_label">Quantity</label> ';
                $qty .= '<input type="number" size="5" name="quantity" value="1"/></div><input type="hidden" name="allow_qty_change" value="true" />';
            }
            else {
              $qty = '<input type="hidden" name="quantity" value="1" /><input type="hidden" name="allow_qty_change" value="false" />';   
            }
               
            $form =<<<END_ATC_FORM
            <div class="aretex_atc_button_div">
            <form class="aretex_atc_form" id="$form_id">
            $qty            
            $options
            <input type="hidden" name="product_code" value="$product_code" />
            <input type="hidden" name="exclusivity" value="$exclusivity" />
            <input type="hidden" name="all_options" value="$all_options" />
            </form>
            </div>
END_ATC_FORM;
            return $form_id;
          }
          
          protected static function get_user_role() {
            if ( is_user_logged_in() ) {
                $user_id = get_current_user_id();
    	        $user = new WP_User( $user_id );
                if ( !empty( $user->roles ) && is_array( $user->roles ) ) {
            		foreach ( $user->roles as $role )
            			return $role;
            	}
            }
            
            return '';
        }
        
        protected static function check_role($in_role,$not_in_role) {
            $role = self::get_user_role();
            if (is_array($in_role)) {
                if (in_array($role,$in_role)) {
                    return true;
                }
                else
                    return false;
            }
            if (is_array($not_in_role)) {
                if (! in_array($role,$not_in_role)) {
                    return true;
                }
                else
                    return false;
            }
            return true;
        }

          
        /**
         * [aretex_add_to_cart productcode="abc" allow_qty="true" exclusivity="membership"]
         * <button>Add to Cart</button>
         * options: {
         *   Size: S(Small, Medium, Large)
         *   Engraving: T(10) | Deliverable ID        
         * }
         * [/aretex_add_to_cart]
         * */
        
        static function aretex_add_to_cart($atts,$content=null) {
            extract( shortcode_atts( array(
        		'productcode' => null,
        		'allow_qty' => 'false',                
                'exclusivity'=>null,
                'in_role' =>null,
                'not_in_role' => null
        	   ), $atts ) );
               
            if (! empty($in_role)) {
                $in_role = explode(',',$in_role);
            }
            if (! empty($not_in_role)) {
                $not_in_role = explode(',',$not_in_role);
            }
            
            if (! self::check_role($in_role,$not_in_role)) {
                return '';
            }
 
               
         //   error_log("Attributes:".var_export($atts,true)."\nContent:$content");
            $aretex_core_path = get_option('aretex_core_path');
            require_once($aretex_core_path. 'simple_html_dom.php');
            if ($content) {
                $content =  do_shortcode($content); // Deal with embedded shortcodes first, in this case ... 
                $html = str_get_html($content);                
            }
            else {
                $content = '';
                $default_button = true;
            }
       //     error_log("HTML:".var_export($html,true));
            $form = '';
            $form_id = self::build_atc_form($productcode,$content,$allow_qty,$exclusivity,$form);
            if ($default_button) {
                if ($form_id)
                        $on_click = " onclick=\"atx_addtocart('".$form_id."');\"";
                $outertext = '<button '.$on_click.' style="cursor: pointer; " >Add to Cart</button>';
            }
            else {
                $img = $html->find("img", 0);
                if ($img) {
                     
                    $outertext = $img->outertext;
                    $style = $img->style;
                    
                    if ($form_id)
                        $on_click = " onclick=\"atx_addtocart('".$form_id."');\"";
                                
                    
                    $replace = "img $on_click style=\"cursor: pointer; $style\" ";
                    $needle = 'img';
                    $pos = strpos($outertext,$needle);
                    if ($pos !== false) {
                        $outertext = substr_replace($outertext,$replace,$pos,strlen($needle));
                    }
                
                }
                else {
                   $button = $html->find("button", 0);
                   $outertext = $button->outertext;
                   $style = $button->style;
                   
                   if ($form_id)
                        $on_click = " onclick=\"atx_addtocart('".$form_id."');\"";
                   
                    $replace = "button $on_click style=\"cursor: pointer; $style\" ";
                    $needle = 'button';
                    $pos = strpos($outertext,$needle);
                    if ($pos !== false) {
                        $outertext = substr_replace($outertext,$replace,$pos,strlen($needle));
                    }
                    
                }
            }
           $form .= $outertext;           
           return $form;
            
        }
        
        
        public function fixObject (&$object)
        {
          if (!is_object ($object) && gettype ($object) == 'object')
            return ($object = unserialize (serialize ($object)));
          return $object;
        }
        
        /**
         * For Now Just: [aretex_show_cart]
         * */
        static public function aretex_show_cart($atts,$content=null) {

           self::populate_session_cart();

           $cart = self::cart_start();
         
           foreach($_SESSION['aretex_shopping_cart']->items as $key=>$item) {
                $cart .= self::cart_item($key,$item,$_SESSION['aretex_shopping_cart']->item_options[$key]);
                
           } 
           $cart .= self::cart_end();
           return $cart;
            
        }
        
       
        
        static protected function populate_session_cart($from_user = true) {
             $aretex_core_path = get_option('aretex_core_path');
             if (!session_id()) {
                session_start();                
             }            
            if (file_exists($aretex_core_path .'AreteXClientEngine/Checkout.class.php')) {
                require_once($aretex_core_path .'AreteXClientEngine/Checkout.class.php');
                require_once($aretex_core_path.'AreteXClientEngine/Crypton/Crypton.php');
                require_once($aretex_core_path .'AreteX_WPI.class.php');
                require_once(plugin_dir_path( __FILE__ ).'AreteXWP_Cart.php');
            }
            else {                    
               return "Error {$aretex_core_path}AreteXClientEngine/Checkout.class.php does not exist";                 
            }
        
            self::fixObject($_SESSION['aretex_shopping_cart']);
            
             if (! is_object($_SESSION['aretex_shopping_cart']) ) {
               
                $_SESSION['aretex_shopping_cart'] = self::setup_cart();
            }
            else {
                $_SESSION['aretex_shopping_cart'] = self::updateCartTracking($_SESSION['aretex_shopping_cart']);
            }
           
            if ( is_user_logged_in() && $from_user ) {
                global $user_login; 
                global $user_ID;
                get_currentuserinfo(); // Populate the globals ... 
                
                $user_cart =  get_user_meta($user_ID, 'aretex_cart', true);
                
            }
            if (is_array($user_cart->items) && empty($_SESSION['aretex_shopping_cart']->items)) {               
                 $_SESSION['aretex_shopping_cart'] = $user_cart;
                 $_SESSION['aretex_shopping_cart']->totalOrder();
            } 
            
            // Refresh for coupons and stuff ...
            
            $tracking_code = $_SESSION['aretex_shopping_cart']->tracking_code;  
            $offer = 'default';
            if (is_string($tracking_code))
            {
                $dash = strpos($tracking_code,'-');
                $offer = substr($tracking_code,0,$dash);                               
            }


            foreach( $_SESSION['aretex_shopping_cart']->items as $key=>$item) {
                $_SESSION['aretex_shopping_cart']->items[$key]->refreshPrice($offer);
            } 
            
             $_SESSION['aretex_shopping_cart']->totalOrder();
            
            
        }
        
         static function start_cart_table() {
            $str = '<table id="aretex_cart_table"><thead><tr><th>&nbsp;</th><th>Quantity</th><th>Item ID</th><th>Item Name</th><th style="text-align: right;">Total Price</th></tr></thead><tbody>';
            return $str;
            
        }
        
        static protected function cart_start() {
            $str = '<div class="aretex_cart" id="aretex_cart_div">';
            $str .= self::start_cart_table();
            return $str;
        }
        
        static function end_cart_table() {
            $cart = $_SESSION['aretex_shopping_cart'];
            $str .= '<tr><td colspan="4">&nbsp;</td><td ><hr style="width: 100%;" /></td></tr>';
            $str .= '<tr><td colspan="3"></td><td><strong>Tax</strong></td><td style="text-align: right;">'.number_format($cart->total_tax,2).'</td></tr>';
            $str .= '<tr><td colspan="3"></td><td><strong>Shipping</strong></td><td style="text-align: right;">'.number_format($cart->total_shipping,2).'</td></tr>';
            $str .= '<tr><td colspan="3"></td><td><strong>Total</strong></td><td style="text-align: right;">'.number_format($cart->total_due,2).'</td></tr>';
            $str .= '</tbody></table>';
            $str .= '<button style="float: left;" onclick="atx_empty_cart();" class="aretex_checkout_button">Empty Cart</button><button style="float: right;" onclick="atx_complete_checkout();" class="aretex_checkout_button">Complete Checkout</button>';
            
            return $str;
        }
        
        static protected function cart_end() {
            // Totals go here
            $str .= self::end_cart_table();
            $str .= '</div>';
            $mod_dlg =  <<<END_MDLG
            <div  id="atx_update_cart_dialog" title="Update Shopping Cart">
            <div id="atx_cart_update_content"></div>
</div>
            <script>
    jQuery( "#atx_update_cart_dialog" ).dialog({
			autoOpen: false,
			width: 300,
			buttons: [
				{
					text: "Update",
					click: function() {
					    atx_update_cart_now();
						
					}
				}
			]
		});
            </script>            
END_MDLG;
            $str .= $mod_dlg;
            return $str;
        }
        
        static protected function should_have_update($line_key,$item_options) {
         
            $line_options =  $_SESSION['aretex_shopping_cart']->all_options[$line_key];
           // error_log("Line Options = ".var_export($line_options,true));
            
            $options = base64_decode($line_options);
            $options = unserialize($options);
            $allow_qty = $options['allow_qty'];
            $options = $options['all_options'];
            $should = false;
             if (is_array($item_options)) {
                $should = true;
             }
            if (strtolower($allow_qty) == 'true') { 
                $should = true;
            }
            
            return $should;
        }
        
        static protected function cart_item($key,$item,$item_options) {
            if (self::should_have_update($key,$item_options)) {
                $update_button = '<button onclick="atx_update_cart('."'$key'".');" class="aretx_cart_item_button">Update</button>';
            }
            else {
                $update_button = '';
            }
            
            $str .= '<tr><td>'.$update_button.'&nbsp;<button onclick="atx_delfromcart('."'$key'".');" class="aretex_cart_item_button">Remove</button></td>'.
                    '<td>'.$item->qty.'</td>'.
                    '<td>'.$item->code.'</td>'.
                    '<td>'.$item->name.'</td>'.
                    '<td style="text-align: right;">'.number_format($item->pricing->item_pay_now,2).'</td></tr>';
            // Options go here
            if (is_array($item_options)) {
                foreach($item_options as $label=>$val) {                                            
                    $str .= '<tr><td colspan="3"></td><td><strong>'.$label.': </strong>'.$val.'</td><td></td></tr>';
  
                }
            }
            return $str;
        }
        
        static protected function add_roles() {
            
        }
        
                
        
 
            
    }
        
        
        
        


    
}    
?>