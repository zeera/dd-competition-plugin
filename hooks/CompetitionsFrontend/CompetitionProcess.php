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
        //code here
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

    public static function validateAnswer( $cart_item_key,  $product_id,  $quantity,  $variation_id,  $variation,  $cart_item_data )
    {
        $answer = $_POST['competition_answer'];

        if( !$answer ) {
            add_action( 'woocommerce_after_add_to_cart_button', [self::class, 'displayNotice'] );
        }
    }

    public static function displayNotice()
    {
        ?>
            <div class="competition-notice">
                <div class="alert alert-danger" role="alert">
                    Please select an answer!
                </div>
            </div>
        <?php
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
}
