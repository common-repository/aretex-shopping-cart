<?php



if (! class_exists('AreteXWP_Cart')) {
    
    class AreteXWP_Cart extends Cart {
        
        protected $shipping_option;
        protected $tax_option;
        public $item_options;
        public $all_options;
        public $exclusive_item;
        public $display_options;
        
        public function addShippingOption($shipping_option) {
            $this->shipping_option = $shipping_option;
        }
        
        public function addTaxOption($tax_option) {
            $this->tax_option = $tax_option;
        }
        
        public function addItem($product_code,$new_qty,$options,$display_options,$all_options,$exclusivity=null,$allow_qty_change='true') {
         
          // error_log("Updating"); 
          $should_add = true;
          if ($exclusivity) {
            if (is_array($this->exclusive_item) && isset($this->exclusive_item[$exclusivity])) {
                if ($this->exclusive_item[$exclusivity] != $product_code) {                   
                  return false; // Don't allow          
                }
            }
                
          }
          if ($allow_qty_change == 'false') {
            foreach($this->items as $item) {
                if ($item->code == $product_code) {
                    $should_add = false;
                    break;
                }
            }
            
          }
           if ($should_add) {
            if ($exclusivity) {
                $this->exclusive_item[$exclusivity] = $product_code;
            }
            foreach($options as $option_name=>$value) {
                $item_options[$display_options[$option_name]] = $value;
            }
            $this->item_options[] = $item_options;
            $this->all_options[] = $all_options;
            $this->display_options[] = $display_options;
            $product = $this->GetProductInfo($product_code,$options);
            $tracking_code = $this->tracking_code; // Adding per Item Tracking Later ... 
            $offer = 'default';
            if (is_string($tracking_code))
            {
                $dash = strpos($tracking_code,'-');
                $offer = substr($tracking_code,0,$dash);                               
            }
            $item = new LocalItem($new_qty,$product,$options,$offer);
            $this->items[] = $item;                        
          }
          $del_list = array();
          foreach($this->items as $key=>$item) {
            if ($item->qty <= 0) {
               $del_list[] = $key;
            }
          }
          
          foreach($del_list as $key) {
            unset($this->items[$key]);
          }
          $this->items = array_values($this->items); // Reindex
          $this->totalOrder();
                        
        }
            
        public function totalOrder($shipping_option = null,$tax_option=null)
        {
            if ($shipping_option)
                $this->shipping_option = $shipping_option;
            if ($tax_option)
                $this->tax_option = $tax_option;
            
            return parent::totalOrder($this->tax_option,$this->shipping_option);
        }
    
    
        protected function GetProductInfo($product_code,&$options) {
            
          //   error_log('A');
             $product = AreteX_WPI::getProductDetailByCode($product_code);
           //  error_log("Product: ".var_export($product,true));            
          //  error_log('B');
             if (is_array($product->details->delivery->deliverables)) {
                foreach($product->details->delivery->deliverables as $deliverable) {
                   // error_log("Deliverable: ".var_export($deliverable,true));
                    $delivery_type = $deliverable->delivery_type;
                    if ($delivery_type == 'unspecified')
                        continue;
                    $descriptor = $deliverable->type_details->descriptor;
                  //  error_log("Delivery Type: $delivery_type - Descriptor: $descriptor");
                    if ((! empty($delivery_type)) && (! empty($descriptor))) {
                        global $wpdb;
                        
                       $table_name = $wpdb->prefix .'aretex_deliverable_options';
                       $sql = "SELECT * FROM $table_name WHERE deliverable_type='$delivery_type' AND deliverable_descriptor='$descriptor' ";
                       $opt_key = md5($sql);
                       $rows = AreteX_WPI::checkCache($opt_key);
                       if (! $rows) {
                         $rows = $wpdb->get_results($sql,ARRAY_A);
                          AreteX_WPI::cacheData($opt_key,$rows);
                       }
                      
                       
                       if (! empty($rows[0]['feature_class'])) {
                           
                           $class = $rows[0]['feature_class'];
                           $table_name = $wpdb->prefix .'aretex_features'; //`feature_name` = 'AreteX Paid Content' AND
                           $sql2 = "SELECT feature_path FROM $table_name WHERE feature_class='$class' AND `load_feature` = 'Y' AND `feature_installed` = 'Y'";
                           $feature_key = md5($sql2);
                           $rows2 = AreteX_WPI::checkCache($feature_key);
                           if (! $rows2) {                            
                                $rows2 = $wpdb->get_results( $sql2, ARRAY_A  );
                                 AreteX_WPI::cacheData($feature_key,$rows2);
                           }
                        //   error_log(var_export($rows2,true));
                           include_once($rows2[0]['feature_path']);

                           if (method_exists($class,'BuildOptions')) {
                              $del_opts = $class::BuildOptions();
                          //    error_log("Del Opts = ".var_export($del_opts,true));
                              if (is_array($del_opts)) {
                                  $options = array_merge($options,$del_opts);
                              }
                           } /*
                           else
                            error_log("no BuildOptions method"); */
                       }
                       
                    }
                    
                }
                // error_log('Deliverable Options: '.var_export($options,true));
             }
             
             
             if (empty($options['force_email'])) {
                $options['force_email'] = 'true';
            } 
          //   error_log('C');
            if (empty($options['email'])) {
                 $customer_id = AreteX_WPI::customerSignedUp();
            //     error_log('C1');
                 if ($customer_id && ctype_digit($customer_id)) {
              //        error_log('C2');
                     $url = get_option('aretex_cam_endpoint');                     
                     $contact_url = $url.'/account/'.$customer_id.'/contact'; 
                     $contact = AreteX_WPI::getGenericResourceByURI($contact_url,AreteX_WPI::use_cache,AreteX_WPI::user_id);
                     $options['email'] = $contact->email_address;
                //       error_log('C3');
                 }
                 else {
                    global $user_email;
                    get_currentuserinfo();
                    $options['email'] = $user_email;
                  //    error_log('C4');
                 }                                           
            }   
            
            
             
        //     error_log('D');
             
             return $product;
        }

    
   } 
    
}

?>