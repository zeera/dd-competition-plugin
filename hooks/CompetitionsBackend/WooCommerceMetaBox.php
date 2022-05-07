<?php
/**
 * =====================================
 * Competition Fields
 * =====================================
 * File Description
 * =====================================
 */

declare(strict_types=1);

namespace WpDigitalDriveCompetitions\Hooks\CompetitionsBackend;

use WpDigitalDriveCompetitions\Helpers\AdminHelper;

class WooCommerceMetaBox extends AdminHelper
{
    private $boxes;

    public static function loadPlugin() {
        require_once 'WcCompetitionProductType.php';
    }

    public static function add_competition_product_type( $types ) {
        $types[ 'competition' ] = __( 'Competition' );
        return $types;
    }

    public static function installTaxonomy()
    {
        if( !get_term_by('slug', 'competition', 'product_type') ) {
            wp_insert_term( 'competition', 'product_type' );
        }
    }

    public static function enable_js_on_wc_product() {
        global $post, $product_object;
        if ( ! $post ) { return; }
        if ( 'product' != $post->post_type ) :
            return;
        endif;
        $adminHelper = new AdminHelper();
        $is_competition = $product_object && 'competition' == $product_object->get_type() ? true : false;
        ?>
            <script type='text/javascript'>
                jQuery(document).ready(function () {
                    jQuery( '#general_product_data .options_group.pricing' ).addClass( 'show_if_competition' ).show();
                    jQuery( '.wc-tabs .general_tab' ).addClass( 'show_if_competition' ).show();

                    // jQuery( '#inventory_product_data .options_group ._manage_stock_field' ).addClass( 'show_if_competition' ).show();
                    // jQuery( '#inventory_product_data .options_group .stock_fields' ).addClass( 'show_if_competition' ).show();
                });
            </script>
        <?php
    }

    public static function competitionTab($tabs)
    {
        $tabs[ 'general' ][ 'class' ][] = 'show_if_competition';
        // $tabs['inventory']['class'][] = 'show_if_competition';

        $tabs['competition_tab'] = [
            'label' => __('Competitions', 'txtdomain'),
            'target' => 'competition_product_data',
            'class' => 'show_if_competition',
            'priority' => 25
        ];

        return $tabs;
    }

    public static function competionProductData()
    {
        global $woocommerce, $post, $product_object;
        ?>
            <div id="competition_product_data" class="panel woocommerce_options_panel hidden">
                <?php
                    woocommerce_wp_text_input([
                        'id' => '_draw_date_and_time',
                        'class' => 'datetimepicker',
                        'label' => __('Draw Date/Time', 'txtdomain'),
                        'wrapper_class' => 'show_if_competition',
                    ]);

                    woocommerce_wp_text_input([
                        'id' => '_end_date_and_time',
                        'class' => 'datetimepicker',
                        'label' => __('End Date/Time', 'txtdomain'),
                        'wrapper_class' => 'show_if_competition',
                    ]);

                    $maximumTicket = $product_object->get_meta('_maximum_ticket', true) ? $product_object->get_meta('_maximum_ticket', true) : 10;
                    woocommerce_wp_text_input([
                        'id' => '_maximum_ticket',
                        'class' => 'input_text required',
                        'label' => __('Maximum Ticket', 'txtdomain'),
                        'type' => 'number',
                        'value' => $maximumTicket,
                        'wrapper_class' => 'show_if_competition',
                        'custom_attributes' => [
                            'step' => 'any',
                            'min' => '1'
                        ]
                    ]);

                    woocommerce_wp_text_input([
                        'id' => '_maximum_ticket_per_user',
                        'class' => 'input_text required',
                        'label' => __('Maximum Ticket Per User', 'txtdomain'),
                        'type' => 'number',
                        'wrapper_class' => 'show_if_competition',
                        'custom_attributes' => [
                            'step' => 'any',
                            'min' => '1'
                        ]
                    ]);

                    $defaultBasket = $product_object->get_meta('_default_basket', true) ? $product_object->get_meta('_default_basket', true) : 10;
                    woocommerce_wp_text_input([
                        'id' => '_default_basket',
                        'label' => __('Default Basket Quantity', 'txtdomain'),
                        'type' => 'number',
                        'value' => $defaultBasket,
                        'wrapper_class' => 'show_if_competition',
                        'custom_attributes' => [
                            'step' => 'any',
                            'min' => '1'
                        ]
                    ]);

                    ?>
                        <hr>
                    <?php
                    woocommerce_wp_checkbox([
                        'id' => '_show_question',
                        'label' => __('Show Question', 'txtdomain'),
                        'wrapper_class' => 'show_if_competition',
                    ]);

                    woocommerce_wp_text_input([
                        'id' => '_question',
                        'label' => __('Question', 'txtdomain'),
                        'wrapper_class' => 'show_if_competition',
                    ]);

                    woocommerce_wp_text_input([
                        'id' => '_answer_1',
                        'label' => __('Answer 1', 'txtdomain'),
                        'wrapper_class' => 'show_if_competition',
                    ]);

                    woocommerce_wp_text_input([
                        'id' => '_answer_2',
                        'label' => __('Answer 2', 'txtdomain'),
                        'wrapper_class' => 'show_if_competition',
                    ]);

                    woocommerce_wp_text_input([
                        'id' => '_answer_3',
                        'label' => __('Answer 3', 'txtdomain'),
                        'wrapper_class' => 'show_if_competition',
                    ]);
                ?>
                <hr>
                <?php
                    woocommerce_wp_text_input([
                        'id' => '_correct_answer',
                        'label' => __('Correct Answer', 'txtdomain'),
                        'wrapper_class' => 'show_if_competition',
                    ]);
                ?>
            </div>
        <?php
    }

    public static function competitionProductDataStore( $post_id )
    {
        global $woocommerce, $post, $product_object;
        $adminHelper = new AdminHelper();
        $product = wc_get_product($post_id);

        $product->update_meta_data('_draw_date_and_time', sanitize_text_field($_POST['_draw_date_and_time']));
        $product->update_meta_data('_end_date_and_time', sanitize_text_field($_POST['_end_date_and_time']));

        $showQuestion = isset($_POST['_show_question']) ? 'yes' : '';
        $product->update_meta_data('_show_question', $showQuestion);

        $isAnswer1 = isset($_POST['_is_answer_1']) ? 'yes' : '';
        $product->update_meta_data('_is_answer_1', $isAnswer1);
        $isAnswer2 = isset($_POST['_is_answer_2']) ? 'yes' : '';
        $product->update_meta_data('_is_answer_2', $isAnswer2);
        $isAnswer3 = isset($_POST['_is_answer_3']) ? 'yes' : '';
        $product->update_meta_data('_is_answer_3', $isAnswer3);

        $product->update_meta_data('_question', sanitize_text_field($_POST['_question']));
        $product->update_meta_data('_answer_1', sanitize_text_field($_POST['_answer_1']));
        $product->update_meta_data('_answer_2', sanitize_text_field($_POST['_answer_2']));
        $product->update_meta_data('_answer_3', sanitize_text_field($_POST['_answer_3']));
        $product->update_meta_data('_correct_answer', sanitize_text_field($_POST['_correct_answer']));
        $product->update_meta_data('_maximum_ticket', wc_clean($_POST['_maximum_ticket']));
        $product->update_meta_data('_maximum_ticket_per_user', wc_clean($_POST['_maximum_ticket_per_user']));
        $product->update_meta_data('_default_basket', wc_clean($_POST['_default_basket']));

        $product->save();

        $maximumTicket = $product->get_meta('_maximum_ticket', true) ? $product->get_meta('_maximum_ticket', true) : 10;
        if( $maximumTicket > 0 ) {
            update_post_meta( $post_id, '_manage_stock', 'yes' );
            update_post_meta( $post_id, '_stock', wc_clean( $_POST['_maximum_ticket'] ));
        }
    }
}
