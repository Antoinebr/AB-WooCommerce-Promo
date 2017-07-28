<?php 
/**
* Plugin Name: AB WooCommerce Promo
* Plugin URI: http://antoinebrossault.com
* Description: Tgit remote add origin https://github.com/Antoinebr/AB-WooCommerce-Promo.git
* Version: 1
* Author: Antoine Brossault
*/


class AbwcPromo
{

    private $WC_Cart;

    private $minPrice;

    private $minItemsNumber;

    private $giftItem;
    


    public function __construct($options)
    {
    
      
        global $woocommerce;

        $this->WC_Cart = $woocommerce->cart;

        $this->minPrice = $options['minPrice'];

        $this->minItemsNumber = $options['minItemsNumber'];

        $this->giftItem =  get_product($options['gitItemID']);

    }



    private function is_promo_admisible()
    {
        
        return ( 

            $this->WC_Cart->total >= $this->minPrice  
            && 

            $this->WC_Cart->get_cart_contents_count() >= $this->minItemsNumber 
            &&

            $this->is_gift_in_stock()


            ) ? true : false;
   
     }



    private function is_promo_can_be_added_soon()
    {
        
        return ( 

            $this->WC_Cart->get_cart_contents_count() === 1
            &&

            $this->is_gift_in_stock()

            ) ? true : false;
   
     }



     private function is_gift_already_added()
     {
        
        $isAlreadyAdded = false;

         foreach($this->WC_Cart->get_cart() as $cart_item_key => $values ) {

            if($this->giftItem->id == $values['data']->id) 
                $isAlreadyAdded = true;

         }

         return $isAlreadyAdded;

     }



     private function is_gift_in_stock()
     {

        return ($this->giftItem->stock_status === 'instock') ? true : false;

     }



    private function add_gift_to_cart()
    {

        return $this->WC_Cart->add_to_cart($this->giftItem->id,1);

    }



    private function remove_gift_from_cart()
    {

        $cartId = $this->WC_Cart->generate_cart_id($this->giftItem->id);

        $cartItemId = $this->WC_Cart->find_product_in_cart($cartId);

        if($cartItemId)
            $this->WC_Cart->set_quantity($cartItemId,0);

    }



    public function maybe_add_gift()
    {

        if( $this->is_promo_admisible() && !$this->is_gift_already_added() ){
            
            $this->add_gift_to_cart();
         
        }


        if( !$this->is_promo_admisible() && $this->is_gift_already_added() ) {
            
            $this->remove_gift_from_cart();

        }


        if( $this->is_promo_can_be_added_soon() ) {
            
            wc_add_notice( "<i class='fa fa-info-circle fa-1x'></i> Achetez un bracelet supplémentaire pour obtenir un outil pour bracelet de montre gratuit !", 'success' );
        
        }

    }


}



add_action( "woocommerce_cart_updated", function(){

 
    $promo = new AbwcPromo(array(
                'minPrice' => 18,
                'minItemsNumber' => 2,
                'gitItemID' => get_field('gift-Product-ID','option')
            ));

    $promo->maybe_add_gift();


});

