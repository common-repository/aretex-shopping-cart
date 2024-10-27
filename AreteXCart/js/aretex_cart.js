/*!
 * Shopping Cart Javascript for AreteX for Wordpress
 *
 * http://3BAlliance.com
 * 
 *
 * Copyright 2014 3B Alliance LLC
 * Released under the GPL 2.0 license or later
 *  
 */

var theAreteXCart; // The AreteX Cart Object

function atx_addtocart(form_id){

    jQuery(function ($) {                
    	
                      
        var data = {
		action: 'atx_add_to_cart',        
        form_data: $('#'+form_id).serialize()
	   };
        	
    	$.post(AreteXCoreJS.ajaxurl, data, function(response) {
    	  var obj = JSON.parse(response);                   
           atx_cart_summary();
          
    	});
        
    
    });  
}

function atx_empty_cart() {
   
    
    jQuery(function ($) {                
    	                              
        var data = {action: 'atx_empty_cart'}
        	
    	$.post(AreteXCoreJS.ajaxurl, data, function(response) {
    	   $('#aretex_cart_div').html(response);
            atx_cart_summary();
                                        
    	});
        
    
    });  
    
} 

function atx_update_cart(line_key) {
    jQuery(function ($) {
      
      $.ajax({
          type: 'GET',
          url: AreteXCoreJS.ajaxurl,
          data: {action: 'atx_update_dlg_content',
                line_key: line_key },
          success: function(data){
            $('#atx_cart_update_content').html(data);
            $( "#atx_update_cart_dialog" ).dialog( "open" );
             atx_cart_summary();
          },
          error: function(xhr, type, exception) { 
            // if ajax fails display error alert
            alert('Shopping Cart Ajax Error');
          }
    });
      
      
        
    });    
}

function atx_update_cart_now() {
    jQuery(function ($) {
        var data = {
    		action: 'atx_update_cart_items',        
            form_data: $('#atx_upd_cart_form').serialize()
  	     };
    
        	$.post(AreteXCoreJS.ajaxurl, data, function(response) {
        	   $('#aretex_cart_div').html(response);
               $( '#atx_update_cart_dialog' ).dialog( "close" );
               atx_cart_summary();
            
                                        
    	});
      });
}

function atx_cart_summary() {
    jQuery(function ($) {
        var data = {
    		action: 'atx_cart_summary',                   
  	     };
    
         $.post(AreteXCoreJS.ajaxurl, data, function(response) {
     	 $('.aretex_cart_summary').html(response);
                                                  
    	});
      });
    
}

function atx_delfromcart(line_key) {
    
    jQuery(function ($) {                
    	
                      
        var data = {
		action: 'atx_delete_from_cart',        
        line_key: line_key
	   };
        	
    	$.post(AreteXCoreJS.ajaxurl, data, function(response) {
    	   $('#aretex_cart_div').html(response);                   
           atx_cart_summary();
          
    	});
        
    
    });  
    
    
}

function atx_complete_checkout() {
    //atx_complete_checkout
    
     jQuery(function ($) {
      
      $.ajax({
          type: 'POST',
          url: AreteXCoreJS.ajaxurl,
          data: {action: 'atx_complete_checkout' },
          success: function(response){
          
           var obj = JSON.parse(response);                   
           var post_data = Array();
           post_data['data'] = obj.data;
           atx_postIt(obj.url,post_data);
          
          },
          error: function(xhr, type, exception) { 
            // if ajax fails display error alert
            alert('Shopping Cart Ajax Error');
          }
    });
      
      
        
    });  
    
}
