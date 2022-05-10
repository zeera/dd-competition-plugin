<?php
/**
 * =====================================
 * Competition Fields
 * =====================================
 * File Description
 * =====================================
 */

declare(strict_types=1);

namespace WpDigitalDriveCompetitions\Hooks\CompetitionsFrontend;

use WpDigitalDriveCompetitions\Helpers\AdminHelper;
use WpDigitalDriveCompetitions\Models\TicketNumbers;

class CompetitionProcess extends AdminHelper
{
    public function __construct()
    {
        $this->ticketNumbers = new TicketNumbers();
    }

    public static function drawDateTimeShow()
    {
        $adminHelper = new AdminHelper();
        $currentPostID = get_the_ID();
        $drawDate =  get_post_meta($currentPostID, '_draw_date_and_time');
        $endDate =  get_post_meta($currentPostID, '_end_date_and_time');
        $maxEntries =  get_post_meta($currentPostID, '_maximum_ticket');
        $showQuestion =  get_post_meta($currentPostID, '_show_question');
        $question =  get_post_meta($currentPostID, '_question');
        $answer_1 =  get_post_meta($currentPostID, '_answer_1');
        $answer_2 =  get_post_meta($currentPostID, '_answer_2');
        $answer_3 =  get_post_meta($currentPostID, '_answer_3');
        ?>
            <div class="competition meta-draw-end-time">
                <p>Draw Date/Time - <?php echo $drawDate[0]; ?></p>
                <p>End of Draw - <?php echo $endDate[0]; ?></p>
            </div>
            <div class="competition meta-max-entries">
                <p><?php echo $maxEntries[0]; ?> Max Entries</p>
            </div>
            <?php if ($showQuestion[0] == 'yes'): ?>
                <hr>
                <div class="competition question-ans">
                    <h4><?php echo $question[0]; ?></h4>
                    <input class="competition_answer" type="radio" name="competition_answer" value="<?php echo $answer_1[0]; ?>" id="<?php echo $answer_1[0]; ?>">
                    <label for="<?php echo $answer_1[0]; ?>"><?php echo $answer_1[0]; ?></label><br>
                    <input class="competition_answer" type="radio" name="competition_answer" value="<?php echo $answer_2[0]; ?>" id="<?php echo $answer_2[0]; ?>">
                    <label for="<?php echo $answer_2[0]; ?>"><?php echo $answer_2[0]; ?></label><br>
                    <input class="competition_answer" type="radio" name="competition_answer" value="<?php echo $answer_3[0]; ?>" id="<?php echo $answer_3[0]; ?>">
                    <label for="<?php echo $answer_3[0]; ?>"><?php echo $answer_3[0]; ?></label><br>
                </div>
                <hr>
            <?php endif; ?>
        <?php
    }

    public static function cartScripts()
    {
        $version = date("ymd-Gis", filemtime(WPDIGITALDRIVE_COMPETITIONS_PATH . 'assets/css/add-to-cart.css'));
        wp_register_style('cart-styles', WPDIGITALDRIVE_COMPETITIONS_URL . 'assets/css/add-to-cart.css?v=' . $version);

        $version = date("ymd-Gis", filemtime(WPDIGITALDRIVE_COMPETITIONS_PATH . 'assets/js/add-to-cart.js'));
        wp_register_script('cart-scripts', WPDIGITALDRIVE_COMPETITIONS_URL . 'assets/js/add-to-cart.js?v=' . $version, array('jquery'), '', true);

        if( is_product() ) {
            wp_enqueue_style("cart-styles");
            wp_enqueue_script("cart-scripts");
        }
    }

    public static function validateAnswer( $passed, $product_id, $quantity )
    {
        $adminHelper = new AdminHelper;
        $answer = $_POST['competition_answer'];
        if ( !$answer ) {
            wc_add_notice( __( ' Please select an answer!', 'woocommerce' ), 'error' );
            $passed = false;
            return $passed;
        }
        $cartQty = self::getCartItems($product_id);
        $passed = self::validateItems($quantity, $product_id, '', $cartQty);

        return $passed;
    }

    public static function onCartUpdate( $cart_updated ) {
        global $woocommerce;
        $items = $woocommerce->cart->get_cart();
        $cart_updated = true;
        $adminHelper = new AdminHelper();
        if( $items ) {
            foreach ($items as $key => $item) {
                // $adminHelper->dd($item, true);
                $cartQty = $item['quantity'];
                $product_id = $item['product_id'];
                $cartItemKey = $item['key'];
                $cart_updated = self::validateItems($cartQty, $product_id, $cartItemKey);
                return $cart_updated;
            }
        }
    }

    public static function validateItems($qty, $productID, $cartItemKey = '', $cartQty = 0)
    {
        $ticketNumbers = new TicketNumbers;
        $adminHelper = new AdminHelper;

        $productData = wc_get_product( $productID );
        $maxQtyUser = get_post_meta($productID, '_maximum_ticket_per_user', true);
        $current_user = \wp_get_current_user();
        $totalBought = $ticketNumbers->getTotalBoughtPerUser($productID, $current_user->ID);
        $totalBought = (int) $totalBought + (int) $cartQty;
        $remainingCredits = (int) $maxQtyUser - (int) $totalBought;
        $cart = WC()->cart;
        if( $productData && $productData->get_type() == 'competition' ) {
            if ($totalBought < $maxQtyUser) {
                if( $remainingCredits == $maxQtyUser ) {
                    if( $qty > $maxQtyUser ) {
                        if( $cartItemKey ) {
                            $cart->cart_contents[ $cartItemKey ]['quantity'] = $maxQtyUser;
                        }
                        wc_add_notice(__('You can only bought at least ' .$maxQtyUser. ' ticket for this product: <strong> ' .$productData->name. ' </strong>', 'woocommerce'), 'error');
                        return false;
                    }
                    return true;
                } else if ( $qty > $remainingCredits ) {
                    if( $cartItemKey ) {
                        $cart->cart_contents[ $cartItemKey ]['quantity'] = $remainingCredits;
                    }
                    wc_add_notice(__('You have ' .$remainingCredits. ' remaining tickets for this product: <strong> ' .$productData->name. ' </strong>', 'woocommerce'), 'error');
                    return false;
                } else {
                    return true;
                }
            } else {
                wc_add_notice( __( 'You have reached the maximum ticket for this product: <strong> ' .$productData->name. ' </strong>', 'woocommerce' ), 'error' );
                return false;
            }
        } else {
            return true;
        }
    }

    public static function getCartItems($productID)
    {
        $qty = 0;

        foreach (WC()->cart->get_cart() as $cart_item) {
            $product_in_cart = $cart_item['product_id'];
            if ($product_in_cart === $productID) {
                $qty = (int) $qty + (int) $cart_item['quantity'];
            }
        }

        return $qty;
    }

    public static function addCartItemData ( $cartItemData, $productId, $variationId ) {
        $answer = $_POST['competition_answer'];
        $cartItemData['_my_competition_answer'] = $answer;
        return $cartItemData;
    }

    public static function getCartItemFromSession( $cartItemData, $cartItemSessionData, $cartItemKey ) {
        if ( isset( $cartItemSessionData['_my_competition_answer'] ) ) {
            $cartItemData['_my_competition_answer'] = $cartItemSessionData['_my_competition_answer'];
        }

        return $cartItemData;
    }

    public static function getItemData( $data, $cartItem ) {
        if ( isset( $cartItem['_my_competition_answer'] ) ) {
            $data[] = array(
                'name' => '<strong>Answer</strong>',
                'value' => $cartItem['_my_competition_answer']
            );
        }

        return $data;
    }

    public static function addOrderItemMeta( $itemId, $values, $key ) {
        if ( isset( $values['_my_competition_answer'] ) ) {
            wc_add_order_item_meta( $itemId, '_my_competition_answer', $values['_my_competition_answer'] );
        }
    }

    public static function filterWcOrderItemDisplayMetaKey( $display_key, $meta, $item ) {
        // Change displayed label for specific order item meta key
        if( is_admin() && $item->get_type() === 'line_item' && $meta->key === '_my_competition_answer' ) {
            $display_key = __("Answer", "woocommerce" );
        }
        return $display_key;
    }
}
