<?php

/**
 * AreteXCart
 * 
 * @package AreteX For WordPress
 * @author 3B Alliance, LLC
 * @copyright 2014
 * @access public
 * 
 * Encapsulates the functionality for the AreteX ecommerce services plugin for 
 * Wordpress.
 */
if ( ! class_exists( 'AreteXCart' ) ) {
    
    
    
    class AreteXCart {
        /**
         * AreteXCart::__construct()
         * Register the action hooks for the plugin.
         * @return void
         */
        public function __construct() {            
            add_action('plugins_loaded', array( &$this, 'on_load' ), 1 ); 
                                             
            add_action('admin_enqueue_scripts', array(&$this, 'on_queue_admin_scripts'));
            add_action('init', array(&$this,'on_init'), 1);                                                 
            add_action('wp_enqueue_scripts', array(&$this, 'on_queue_scripts'));
            
            add_action('admin_menu', array( &$this, 'admin_menu' ),15);
            
                        // register widget
            add_action('widgets_init', create_function('', 'return register_widget("AreteXCartSummaryWidget");'));
      
        }
        
       
                
         public static function core_failure_notice() {
    ?>
    <div class="error">
        <p><?php _e( 'AreteX Cart Feature Failure: Must have AreteX Core Installed and Active with valid License', 'aretex-cart' ); ?></p>
    </div>
    <?php
        }
        
        static public function install() {
            
            /*
                1. Be sure AreteX is loaded and activated
                2. Load main AreteX Plugin
                3.       
            */
           
            
            $aretex_core_path = get_option('aretex_core_path');
            $folders = explode(DIRECTORY_SEPARATOR,$aretex_core_path);
            $len = count($folders);
            $path = $folders[$len-3];           
            if ( is_plugin_active( $path.'/ecommerce-services.php' ) ) {
              //plugin is activated            
              if (! class_exists('AreteX_plugin')) {
                require_once($aretex_core_path.'AreteX_plugin.class.php');                                              
              }
              
              self::reLoadFeature();
              
              return;              
            }
            
            echo 'AreteX&trade; eCommerce Services Not Found';
            exit();
            
            /*
            add_option('option');
            
            self::add_roles();
            
                                    
            
            */
            
            
         }
         
         
         static protected function reLoadFeature() {
            foreach (new DirectoryIterator(plugin_dir_path( __FILE__ ).'features') as $fileInfo) {
                if($fileInfo->isDot()) continue;
                    $filename = $fileInfo->getFilename();
                
               
                if (strpos($filename,'.class')) {
                    $params = AreteX_plugin::ParamsFromComments($fileInfo->getPathname());                                     
                    $feature_name = $params['FeatureName'];
                    if (empty($feature_name))
                        continue;
                        
                   global $wpdb;                              
                   $table_name = $wpdb->prefix .'aretex_features';
                   $rows = $wpdb->get_results( "SELECT * FROM $table_name WHERE feature_name='$feature_name'", ARRAY_A  );
                   if (empty($rows[0]['feature_name'])) {
                      $data = array();
                      $data['feature_name'] = $feature_name;
                      $data['feature_class'] = $params['FeatureClass'];
                      $data['description'] = $params['Description'];
                      $data['feature_path'] = $fileInfo->getPathname();
                      $data['menu_path']= $params['AreteXMenuPath'];
                      $data['parameters'] = serialize($params);
                      $data['load_feature'] = $params['LoadFeature'];
                      $data['feature_version'] = $params['FeatureVersion'];  
                      $data['aretex_server_version'] = $params['AretexServerVersion'];
                      $data['replacement_for'] = $params['ReplacementFor'];                                          
                      $wpdb->replace( $table_name, $data, null ); 
                   }
                   else {
                        $data = $rows[0];
                        $data['feature_path'] = $fileInfo->getPathname();
                        $wpdb->replace( $table_name, $data, null ); 
                        
                    }  
                    
                }
                 
                    
            }
        }

         
         
         public static function deactivate() {
             
        //    delete_option( 'option');
            
        }
        
        /**
         * AreteX_plugin::on_queue_admin_scripts()
         * Register and enqueue the javascripts /css for the admin UI. 
         *  - jQuery
         *  - jQuery UI
         *  - Responsive Grid System
         *  - Tree View
         * @return void
         */
        public function on_queue_admin_scripts(){
            
            // jQuery, jQuery UI
            wp_enqueue_script('json2');
            wp_enqueue_script('jquery');
           
           
          
            
            wp_enqueue_script( 'jquery-ui-core' );
           
            wp_enqueue_script( 'jquery-ui-widget' );
            wp_enqueue_script( 'jquery-ui-position' );
            wp_enqueue_script( 'jquery-ui-menu' );
            wp_enqueue_script( 'jquery-ui-progressbar' );
            wp_enqueue_script( 'jquery-ui-mouse' );
            wp_enqueue_script( 'jquery-ui-accordion' );
            wp_enqueue_script( 'jquery-ui-autocomplete' );
            wp_enqueue_script( 'jquery-ui-slider' );
            wp_enqueue_script( 'jquery-ui-tabs' );
            wp_enqueue_script( 'jquery-ui-sortable' );
            wp_enqueue_script( 'jquery-ui-draggable' );
            wp_enqueue_script( 'jquery-ui-droppable' );
            wp_enqueue_script( 'jquery-ui-datepicker' );
            wp_enqueue_script( 'jquery-ui-resize' );
            wp_enqueue_script( 'jquery-ui-dialog' );
            wp_enqueue_script( 'jquery-ui-button' );
            wp_enqueue_script( 'jquery-ui-resizable' );
            wp_enqueue_script( 'jquery-ui-selectable' );
            wp_enqueue_script( 'jquery-ui-spinner' );
            wp_enqueue_script( 'jquery-ui-tooltip' );
            
            wp_register_style('jquery-ui', plugins_url( 'css/jquery-ui-1.9.2.custom.min.css', __FILE__ ));
            wp_enqueue_style( 'jquery-ui' );
            
            
            // Responsive Grid System
            // Credit: http://www.responsivegridsystem.com/
            wp_register_style('rg-col', plugins_url( 'css/col.css', __FILE__ ));
            wp_register_style('c2-col', plugins_url( 'css/2cols.css', __FILE__ ));
            wp_register_style('c3-col', plugins_url( 'css/3cols.css', __FILE__ ));
            wp_register_style('c4-col', plugins_url( 'css/4cols.css', __FILE__ ));
            wp_register_style('c5-col', plugins_url( 'css/5cols.css', __FILE__ ));
            wp_register_style('c12-col', plugins_url( 'css/12cols.css', __FILE__ ));
            wp_register_style('c10-col', plugins_url( 'css/10cols.css', __FILE__ ));
            
            wp_enqueue_style('rg-col' );
            wp_enqueue_style('c2-col');
            wp_enqueue_style('c3-col');
            wp_enqueue_style('c4-col');
            wp_enqueue_style('c5-col');
            wp_enqueue_style('c12-col');
            wp_enqueue_style('c10-col');
            
                        
          
            
 

          
            
        }
        
        
        public function on_init() {
            if (!session_id())
                session_start();
            
        }
        
       
        public function on_queue_scripts(){
            
            // jQuery, jQuery UI
            wp_enqueue_script('json2');
            wp_enqueue_script('jquery');
            

            // Need jQuery UI
            wp_enqueue_script( 'jquery-ui-core' );
            wp_enqueue_script( 'jquery-ui-widget' );
            wp_enqueue_script( 'jquery-ui-position' );
            wp_enqueue_script( 'jquery-ui-menu' );
            wp_enqueue_script( 'jquery-ui-progressbar' );
            wp_enqueue_script( 'jquery-ui-mouse' );
            wp_enqueue_script( 'jquery-ui-accordion' );
            wp_enqueue_script( 'jquery-ui-autocomplete' );
            wp_enqueue_script( 'jquery-ui-slider' );
            wp_enqueue_script( 'jquery-ui-tabs' );
            wp_enqueue_script( 'jquery-ui-sortable' );
            wp_enqueue_script( 'jquery-ui-draggable' );
            wp_enqueue_script( 'jquery-ui-droppable' );
            wp_enqueue_script( 'jquery-ui-datepicker' );
            wp_enqueue_script( 'jquery-ui-resize' );
            wp_enqueue_script( 'jquery-ui-dialog' );
            wp_enqueue_script( 'jquery-ui-button' );
            wp_enqueue_script( 'jquery-ui-resizable' );
            wp_enqueue_script( 'jquery-ui-selectable' );
            wp_enqueue_script( 'jquery-ui-spinner' );
            wp_enqueue_script( 'jquery-ui-tooltip' );
           
           wp_enqueue_script('aretex-cart-js',plugins_url('js/aretex_cart.js', __FILE__ ),array('jquery','jquery-ui-dialog','aretex-core-js'));
           
           // Need some settings in here for jquery ui style ...
           
           wp_enqueue_style('aretex_cart-front-ui-css',
                '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css',
                false,
                '1.00.00a',
                false);
           
                       

    
            
        }
        
        public function on_load() {
            // Do "Self Check"
            
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); 
            $aretex_core_path = get_option('aretex_core_path');           
            $folders = explode(DIRECTORY_SEPARATOR,$aretex_core_path);
            $len = count($folders);
            $path = $folders[$len-3];           
           
                
            if ( is_plugin_active( $path.'/ecommerce-services.php' ) ) {
              //plugin is activated 
                                
              if (! class_exists('AreteX_plugin')) {              
                require_once($aretex_core_path.'AreteX_plugin.class.php');                                              
              }
            }
            else {
               
                add_action( 'admin_notices',  array('AreteXCart','core_failure_notice') );
                return;
                
            }
            
            
            
        }
        
       


        
        
        public function admin_head(){
            
            echo $str;
           
        }
        
               
        
        

        public function admin_menu(){

            
                $aretex_core_path = get_option('aretex_core_path');
                $folders = explode(DIRECTORY_SEPARATOR,$aretex_core_path);
                $len = count($folders);
                $path = $folders[$len-3];           
                if ( is_plugin_active( $path.'/ecommerce-services.php' ) ) {
                    
                 add_submenu_page('AreteX_Main_Admin_Menu', 
                                  'AreteX&trade; eCommerce Services Shopping Cart', 
                                  'Shopping Cart', 
                                  'manage_options', 
                                  'AreteX_Shopping_Cart',array('AreteXCart','admin_page') );
                 }
                 
        }
        
        
        
        /**
         * AreteX_plugin::admin_page()
         * 
         * Display main plugin page, or registration if AreteX license not valid.
         * 
         * @return void
         */
        public static function admin_page() {
            
              
               include(plugin_dir_path( __FILE__ ) . 'pages/admin_main.php');   
        }               
           
        
        
        
  
    
    

}


}


// Credit: http://www.wpexplorer.com/create-widget-plugin-wordpress/
if (! class_exists('AreteXCartSummaryWidget')) {
    class AreteXCartSummaryWidget extends WP_Widget {
    
    	// constructor
    	public function __construct() {
    	   parent::WP_Widget(false, $name = __('AreteX Cart Summary', 'AreteXCartSummaryWidget') );
            if (!session_id())
                session_start();
    	}
    
    
    	// widget form creation
    	public function form($instance) {	
    	   
            // Check values
            if( $instance) {
                 $title = esc_attr($instance['title']);
                 $detail_link = esc_attr($instance['detail_link']);
                 $button_text = esc_attr($instance['button_text']);            
            } else {
                 $title = 'Shopping Cart';
                 $detail_link = 'show-cart';
                 $button_text = 'Show Cart';
            }
            ?>
            
            <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Cart Summary Box Title', 'AreteXCartSummaryWidget'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
            </p>
            
            <p>
            <label for="<?php echo $this->get_field_id('detail_link'); ?>"><?php _e('Cart Detail Link:', 'AreteXCartSummaryWidget'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('detail_link'); ?>" name="<?php echo $this->get_field_name('detail_link'); ?>" type="text" value="<?php echo $detail_link; ?>" />
            </p>
            
           
            <p>
            <label for="<?php echo $this->get_field_id('button_text'); ?>"><?php _e('Button Text:', 'AreteXCartSummaryWidget'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('button_text'); ?>" name="<?php echo $this->get_field_name('button_text'); ?>" type="text" value="<?php echo $button_text; ?>" />
            </p>
           
            <?php

    	}
    
    	// widget update
    	public function update($new_instance, $old_instance) {
    		  $instance = $old_instance;
              // Fields
              $instance['title'] = strip_tags($new_instance['title']);
              $instance['detail_link'] = strip_tags($new_instance['detail_link']); 
              $instance['button_text'] =  strip_tags($new_instance['button_text']);             
             return $instance;
    	}
    
    	// widget display
    	public function widget($args, $instance) {
    		extract( $args );
           // these are the widget options
           $title = apply_filters('widget_title', $instance['title']);
           $detail_link = $instance['detail_link'];
           echo $before_widget;
           // Display the widget
           echo '<div class="widget-text wp_widget_plugin_box aretex_cart_widget">';
        
           // Check if title is set
           if ( $title ) {
              echo $before_title . $title . $after_title;
           }
        
           // Check if text is set
            $aretex_core_path = get_option('aretex_core_path');
             if (!session_id()) {
                session_start();                
             }            
            if (file_exists($aretex_core_path .'AreteXClientEngine/Checkout.class.php')) {
                require_once($aretex_core_path .'AreteXClientEngine/Checkout.class.php');
                require_once($aretex_core_path.'AreteXClientEngine/Crypton/Crypton.php');
                require_once($aretex_core_path .'AreteX_WPI.class.php');
                require_once(plugin_dir_path( __FILE__ ).'features/AreteXWP_Cart.php');
            }
           AreteX_shopping_cart::fixObject($_SESSION['aretex_shopping_cart']);
           if (is_object($_SESSION['aretex_shopping_cart'])) {
             $total_items = 0;
             if (is_array($_SESSION['aretex_shopping_cart']->items)) {
                foreach($_SESSION['aretex_shopping_cart']->items as $item) {
                    $total_items += $item->qty;
                }                
             }
             
             echo '<span class="aretex_cart_summary">';
             echo '<strong>Items: </strong><span class="aretex_cart_qty">'.$total_items.'</span></br>';
             echo '<strong>Total: </strong><span class="aretex_cart_ttl">$'.number_format($_SESSION['aretex_shopping_cart']->total_due,2).'</span></br><br/>';
             echo '</span>';

           }
           else {
             echo '<span class="aretex_cart_summary">';
             echo '<strong>Items: </strong><span class="aretex_cart_qty">0</span></br>';
             echo '<strong>Total: </strong><span class="aretex_cart_ttl">$0.00</span></br><br/>';
             echo '</span>';

           }
          
           
           if(! $button_text ) {
                $button_text = 'Show Cart';
           }
           
           
$output =<<<CART_BOX2
            <form action="$detail_link" class="aretex_cart_widget_form" method="GET">
            <input class="aretex_cart_submit" type="submit" value="$button_text" />
            </form>            
CART_BOX2;
           echo $output;
           echo '</div>';
           echo $after_widget;
            
            
    	}
    }

}




?>