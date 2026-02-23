<?php
/* TOP_COMMENT_START
 * Copyright (C) 2022, Champion Consulting, LLC  dba ChampionCMS - All Rights Reserved
 *
 * This file is part of Champion Core. It may be used by individuals or organizations generating less than $400,000 USD per year in revenue, free-of-charge. Individuals or organizations generating over $400,000 in annual revenue who continue to use Champion Core after 90 days for non-evaluation and non-development use must purchase a paid license. 
 *
 * Proprietary
 * You may modify this source code for internal use. Resale or redistribution is prohibited.
 *
 * You can get the latest version at: https://cms.championconsulting.com/
 *
 * Dated June 2023
 *
TOP_COMMENT_END */

$key     = $GLOBALS['tag_var1'];
$productsku  = $GLOBALS['tag_var2'];
$success  = $GLOBALS['tag_var3'];
$cancel  = $GLOBALS['tag_var4'];
$btntext  = $GLOBALS['tag_var5'];

$key     = \trim($key,     '"');
$productsku = \trim($productsku, '"');
$success  = \trim($success,  '"');
$cancel   = \trim($cancel,   '"');
$btntext  = \trim($btntext,  '"');

$key     = \trim($key);
$productsku = \trim($productsku);
$success  = \trim($success);
$cancel   = \trim($cancel);
$btntext  = \trim($btntext);
?>

<!--Stripe-->
<!-- Load Stripe.js on your website. -->
<script src="https://js.stripe.com/v3"></script>

<button style="background-color:#6772E5;color:#fff;padding:12px;border:0;border-radius:4px;font-size:1em;cursor: pointer;" id="checkout-button-<?php echo $productsku; ?>" role="link">
  <?php echo $btntext; ?> 
</button> 

<div id="error-message"></div>

<script>
  var stripe = Stripe('<?php echo $key; ?>');

  var checkoutButton = document.getElementById('checkout-button-<?php echo $productsku; ?>');
  checkoutButton.addEventListener('click', function () {
    // When the customer clicks on the button, redirect
    // them to Checkout.
    stripe.redirectToCheckout({
      items: [{sku: '<?php echo $productsku; ?>', quantity: 1}],

      // Do not rely on the redirect to the successUrl for fulfilling
      // purchases, customers may not always reach the success_url after
      // a successful payment.
      // Instead use one of the strategies described in
      // https://stripe.com/docs/payments/checkout/fulfillment
      successUrl: 'https://<?php echo $success; ?>',
      cancelUrl: 'https://<?php echo $cancel; ?>',
	  locale: 'auto'
    })
    .then(function (result) {
      if (result.error) {
        // If `redirectToCheckout` fails due to a browser or network
        // error, display the localized error message to your customer.
        var displayError = document.getElementById('error-message');
        displayError.textContent = result.error.message;
      }
    });
  });
</script>