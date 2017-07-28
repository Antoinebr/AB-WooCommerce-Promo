# AB WooCommerce Promo

This plugin allow you to offer a product on WC when requirements are fulfilled


## usage 

```php 

add_action( "woocommerce_cart_updated", function(){

 
    $promo = new AbwcPromo(array(
                'minPrice' => 18,
                'minItemsNumber' => 2,
                'gitItemID' => get_field('gift-Product-ID','option')
            ));

    $promo->maybe_add_gift();


});

```

## TODO 

* Display the WC otice on an other hook 
* Add Actions / Filters