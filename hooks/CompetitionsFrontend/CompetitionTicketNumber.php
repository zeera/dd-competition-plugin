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

class CompetitionTicketNumber extends AdminHelper
{
    public function __construct()
    {
        //code here
    }

    public static function create( $order_id ) {
        $ticketNumbersModel = new TicketNumbers;
        $order = new \WC_Order( $order_id );

        if ( $order ) {
            if ( $order_items = $order->get_items() ) {
                foreach ( $order_items as $item_id => $item ) {
					if ( function_exists( 'wc_get_order_item_meta' ) ){
						$item_meta = wc_get_order_item_meta( $item_id, '' );
					} else{
						$item_meta = method_exists( $order, 'wc_get_order_item_meta' ) ? $order->wc_get_order_item_meta( $item_id ) : $order->get_item_meta( $item_id );
					}
                    $product_id = $item_meta['_product_id'][0];
                    $product_data = wc_get_product( $product_id );
                    $_my_competition_answer = $item_meta['_my_competition_answer'][0];

                    if ( $product_data && $product_data->get_type() == 'competition' ) {
                        if (apply_filters( 'add_ticket_numbers_from_order', true , $item, $order_id, $product_id ) ){
                            for ( $i = 0; $i < $item_meta['_qty'][0]; $i++ ) {
                                $uniqueTicketNumber = $ticketNumbersModel->generateTicketNumber();
                                $request = array(
                                    'userid' => $order->get_user_id(),
                                    'order_id' => $order_id,
                                    'ticket_number' => $uniqueTicketNumber,
                                    'answer' => $_my_competition_answer,
                                    'product_id' => $product_id
                                );
                                $result = $ticketNumbersModel->store($request);
                            }
                        }
                    }
                }
            }
        }

        return;
    }

    public static function addTicketNumberToOders(  $item_id, $item, $_product  ) {
        $ticketNumbersModel = new TicketNumbers;
        $product_id = $_product->id;
        $order_id = $item['order_id'];
        $ticketNumbers = $ticketNumbersModel->getTicketNumbersOnEachProduct($product_id, $order_id);
        if($ticketNumbers) {
            foreach($ticketNumbers as $tnKey => $ticketNumber) {
                echo '<p>Ticket Number: <strong>'. $ticketNumber['ticket_number'] .'</strong></p>';
            }
        }
    }

}
