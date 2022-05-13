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
use WpDigitalDriveCompetitions\Hooks\CompetitionsBackend\CompetitionEmail;
use WpDigitalDriveCompetitions\Models\TicketNumbers;

class CompetitionTicketNumber
{
    public function __construct()
    {
        //code here
    }

    public static function create( $order_id ) {
        $ticketNumbersModel = new TicketNumbers;
        $competitionEmail = new CompetitionEmail;
        $adminHelper = new AdminHelper;
        $order = new \WC_Order( $order_id );
        $billing_first_name  = $order->get_billing_first_name() ?? '';
        $billing_last_name   = $order->get_billing_last_name() ?? '';
        $billing_company     = $order->get_billing_company() ?? '';
        $billing_address_1   = $order->get_billing_address_1() ?? '';
        $billing_address_2   = $order->get_billing_address_2() ?? '';
        $billing_city        = $order->get_billing_city() ?? '';
        $billing_state       = $order->get_billing_state() ?? '';
        $billing_postcode    = $order->get_billing_postcode() ?? '';
        $billing_country     = $order->get_billing_country() ?? '';
        $billing_email       = $order->get_billing_email() ?? '';
        $billing_phone       = $order->get_billing_phone() ?? '';
        $order_date_created  = $order->get_date_created()->date('Y-m-d') ?? '';
        $order_date_modified = $order->get_date_modified()->date('Y-m-d') ?? '';

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
                    $correctAnswer = get_post_meta($product_id, '_correct_answer');
                    $question = get_post_meta($product_id, '_question');
                    $_my_competition_answer = $item_meta['_my_competition_answer'][0];
                    $_competition_guest_email = $item_meta['_competition_guest_email'][0];

                    if ( $product_data && $product_data->get_type() == 'competition' ) {
                        $adminHelper->dd('test', true, true);
                        if( $_my_competition_answer == $correctAnswer[0] ) {
                            $ticketNumbers = [];
                            if (apply_filters( 'add_ticket_numbers_from_order', true , $item, $order_id, $product_id ) ) {
                                for ( $i = 0; $i < $item_meta['_qty'][0]; $i++ ) {
                                    $uniqueTicketNumber = $ticketNumbersModel->generateTicketNumberByProduct($product_id);
                                    $request = array(
                                        'userid' => $order->get_user_id() ? $order->get_user_id() : 0,
                                        'email' => $billing_email,
                                        'order_id' => $order_id,
                                        'ticket_number' => $uniqueTicketNumber,
                                        'answer' => $_my_competition_answer,
                                        'product_id' => $product_id,
                                        'item_id' => $item_id,
                                    );
                                    $result = $ticketNumbersModel->store($request);
                                    if( $result ) {
                                        array_push($ticketNumbers, $uniqueTicketNumber);
                                    }
                                }
                            }
                            //send email
                            $emailArgs = [
                                'answer' => $_my_competition_answer,
                                'correct_answer' => $correctAnswer[0],
                                'competition_name' => $product_data->name,
                                'question' => $question[0],
                                'ticket_number' => $ticketNumbers,
                                'email' => $billing_email,
                                'subject' => get_bloginfo().' - Competition',
                                'status' => 'correct',
                            ];

                            $competitionEmail->setEmail($emailArgs);
                        } else {
                            $emailArgs = [
                                'answer' => $_my_competition_answer,
                                'competition_name' => $product_data->name,
                                'question' => $question[0],
                                'email' => $_competition_guest_email != '' ? $_competition_guest_email : $billing_email,
                                'subject' => get_bloginfo().' - Competition(Incorrect)',
                                'status' => 'incorrect',
                            ];

                            $competitionEmail->setEmail($emailArgs);
                        }
                    }
                }
            }
        }

        return;
    }

    public static function addTicketNumberToOders( $item_id, $item, $_product ) {
        $ticketNumbersModel = new TicketNumbers;
        $product_id = $_product->id;
        $order_id = $item['order_id'];
        $ticketNumbers = $ticketNumbersModel->getTicketNumbersOnEachProduct( $product_id, $order_id, $item_id );
        if($ticketNumbers) {
            foreach($ticketNumbers as $tnKey => $ticketNumber) {
                echo '<p>Ticket Number: <strong>'. $ticketNumber['ticket_number'] .'</strong></p>';
            }
        }
    }

}
