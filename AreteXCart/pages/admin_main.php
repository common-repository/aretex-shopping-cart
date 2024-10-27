<div style="padding: 10px;" > 

<h2>AreteX&trade; eCommerce Services - Shopping Cart Extension</h2>

The AreteX shopping cart plugin allows you to use AreteX to sell multiple items, rather than just one item at a time (as with a "Buy Now" button).
<h3>Limitations</h3>
<p>You may only add "Single Price" items to the shopping cart.  Recurring billing items need to still use the "Buy Now" button.</p>
<p>You may not use a "Add to Cart" for membership deliverables, as they require registration fields.</p>
<h3>Shopping Cart Page</h3>
<p>
In order for your customers to see what is in their cart you will need to create a shopping cart page.<br/> 
The only content you need to put on the page is the shortcode:<br />
<div style="font-size: 115%;">
<strong>[aretex_show_cart]</strong>
</div>
</p>
<p>That shortcode will display the contents of the shopping cart similar to the one below. 
The actual look and feel (colors, fonts etc.) will be based on your theme and css.
</p>
<div style="margin: 10px;">
<img src="<?php echo plugins_url( 'images/ShowCartPage.png', __FILE__ ); ?>" />

</div> 


<h3>Shopping Cart Summary Widget</h3>
<p>Ths shopping cart summary widget shows the number of items in the shopping cart and the total cost of those items.</p>
<p>The "Show Cart" button should link to the Shopping Cart Page (see above). Be sure to set the <strong><em>Cart Detail Link</em></strong> 
to your shopping cart summary page.  The full URL will always work, but if you use the "Post Name" permalink setting, you only need to enter the page name into 
<em><strong>Cart Detail Link</strong></em> field.  Below is a screen capture of the shopping cart widget setup.</p>
<div style="margin: 10px;">
<img src="<?php echo plugins_url( 'images/widgetsetup.png', __FILE__ ); ?>" />

</div>

<h3><em>"Add to Cart"</em> Shortcode</h3>
<p>
You can use an "Add to Cart" button either instead of, or in addition to, the "Buy Now" button.  
Use the following shortcode:</p>

<div style="font-size: 115%;">
<strong>[aretex_add_to_cart productcode="YOURPRODUCTCODE"]</strong>
</div>
<p>
Be sure to put the product code <em><strong>not the delvierable code</strong></em> of the product you are selling into the 
product code attribute.  When your customer selects "Add to Cart", the cart summary widget will automatically be updated via AJAX.
</p>
<p>The content of the shopping cart will be saved in session.  
If your customer is logged in, the shopping cart content will be saved to their user meta data. </p>

<h3>CSS Styles</h3>
<p>The following CSS classes can be used to customize your styles. You may use these styles to match the shopping cart elments to your theme.</p>
<table   cellspacing="0" style="font-family: 'Courier New','Courier', monospace; border-style: solid; border-color: black; border-width: 2px;">
<tr><th>Class</th><th>Purpose</th></tr>
<tr><td style="margin: 0p; padding: 2px; border-style: solid; border-color: black; border-width: 1px;"><strong>aretex_cart</strong></td><td style="margin: 0p; padding: 2px; border-style: solid; border-color: black; border-width: 1px;">Outer div wrapper of table generated by <strong>aretex_show_cart</strong> shortcode.</td></tr>
<tr><td style="margin: 0p; padding: 2px; border-style: solid; border-color: black; border-width: 1px;"><strong>aretex_checkout_button</strong></td><td style="margin: 0p; padding: 2px; border-style: solid; border-color: black; border-width: 1px;">This class is for the buttons "Empty Cart" and "Complete Checkout" at the bottom of the cart generated by the <strong>aretex_show_cart</strong> shortcode.</td></tr>
<tr><td style="margin: 0p; padding: 2px; border-style: solid; border-color: black; border-width: 1px;"><strong>aretex_cart_qty</strong></td><td style="margin: 0p; padding: 2px; border-style: solid; border-color: black; border-width: 1px;">This class is for the span that contains the "Items" quantity in the show cart widget.</td></tr>
<tr><td style="margin: 0p; padding: 2px; border-style: solid; border-color: black; border-width: 1px;"><strong>aretex_cart_ttl</strong></td><td style="margin: 0p; padding: 2px; border-style: solid; border-color: black; border-width: 1px;">This class is for the span that contains the "Total" dollar amount in the show cart widget.</td></tr>
<tr><td style="margin: 0p; padding: 2px; border-style: solid; border-color: black; border-width: 1px;"><strong>aretex_atc_button_div</strong></td><td style="margin: 0p; padding: 2px; border-style: solid; border-color: black; border-width: 1px;">This class is for the div that contains the "Add to Cart" button.</td></tr>
<tr><td style="margin: 0p; padding: 2px; border-style: solid; border-color: black; border-width: 1px;"><strong>aretex_cart_item_button</strong></td><td style="margin: 0p; padding: 2px; border-style: solid; border-color: black; border-width: 1px;">This class is for the item delete button in the shopping cart.</td></tr>

<tr><td style="margin: 0p; padding: 2px; border-style: solid; border-color: black; border-width: 1px;"><strong>aretex_cart_widget</strong></td><td style="margin: 0p; padding: 2px; border-style: solid; border-color: black; border-width: 1px;">This class is in the div that wraps the cart summary widget</td></tr>
<tr><td style="margin: 0p; padding: 2px; border-style: solid; border-color: black; border-width: 1px;"><strong>aretex_cart_summary</strong></td><td style="margin: 0p; padding: 2px; border-style: solid; border-color: black; border-width: 1px;"><em>Any</em> element of this class will be populated with the shopping cart summary via AJAX any time the cart's contents change.</td></tr>


</table>

</div>