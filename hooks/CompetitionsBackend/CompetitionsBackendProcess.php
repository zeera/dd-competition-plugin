<?php
declare(strict_types=1);

namespace WpDigitalDriveCompetitions\Hooks\CompetitionsBackend;

use WpDigitalDriveCompetitions\Helpers\AdminHelper;
use WpDigitalDriveCompetitions\Models\TicketNumber;

class CompetitionsBackendProcess extends AdminHelper
{
    /**
     * Adds "Import" button on module list page
     */
    public static function addCustomButton( $column, $postid )
    {
        if ( $column == 'action' ) {
            echo '<a href="'.WPDIGITALDRIVE_COMPETITIONS_SITEURL.'/wp-admin/admin.php?page=WpDigitalDriveCompetitions_entry_lists&product_id='. $postid .'" title="View Entry List">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-ticket-detailed" viewBox="0 0 16 16">
                <path d="M4 5.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5Zm0 5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5ZM5 7a1 1 0 0 0 0 2h6a1 1 0 1 0 0-2H5Z"/>
                <path d="M0 4.5A1.5 1.5 0 0 1 1.5 3h13A1.5 1.5 0 0 1 16 4.5V6a.5.5 0 0 1-.5.5 1.5 1.5 0 0 0 0 3 .5.5 0 0 1 .5.5v1.5a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 11.5V10a.5.5 0 0 1 .5-.5 1.5 1.5 0 1 0 0-3A.5.5 0 0 1 0 6V4.5ZM1.5 4a.5.5 0 0 0-.5.5v1.05a2.5 2.5 0 0 1 0 4.9v1.05a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5v-1.05a2.5 2.5 0 0 1 0-4.9V4.5a.5.5 0 0 0-.5-.5h-13Z"/>
                </svg>
                </a>';
        }
    }

    public static function showProductOrder($columns){
        //remove column
        unset( $columns['tags'] );

        //add column
        $columns['action'] = __( 'Action');

        return $columns;
    }

    public static function getPage( $key )
    {
        global $wpdb;
        $page = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $wpdb->posts
                WHERE post_type = 'page'
                AND post_name
                LIKE '%s'", '%'. $wpdb->esc_like( $key ) .'%'
            ), OBJECT
        );

        return $page;
    }
    public static function createEntryListPage()
    {

        $self = new self;
        $page = $self::getPage('entry-lists');
        if ( count($page) <= 0 ) {
            $my_post = array(
                'post_title'   => 'Entry List',
                'post_name' => 'entry-lists',
                'post_content' => '[entry-lists-competition]',
                'post_status'  => 'publish',
                'post_author'  => 1,
                'post_type'    => 'page'
            );
            wp_insert_post( $my_post );
        }
    }

    public static function exportApi()
    {
        $version = '2';
        $namespace = 'wp/v' . $version;
        register_rest_route($namespace, '/export', array(
            'methods'  => 'POST',
            'callback' =>  [self::class, "processDatatoCsv"],
        ));
    }

    public static function processDatatoCsv()
    {
        ob_start();
        $ticketNumber = new TicketNumber;
        $adminHelper = new AdminHelper;
        if( isset( $_POST ) ) {
            $allTickets = $ticketNumber->getAllTickets($_POST['product_id']);
            $delimiter = ",";
            $filename = "entry-lists_" . date('Y-m-d') . ".csv";

            // Create a file pointer
            $f = fopen('php://memory', 'w');

            // Set column headers
            if( $_POST['product_id'] ) {
                $fields = array( 'Ticket Number', 'Full Name', 'Order ID' );
            } else {
                $fields = array( 'Order ID', 'Full Name', 'Email', 'Phone Number', 'Product Name', 'Upload Date' );
            }
            fputcsv($f, $fields, $delimiter);

            // Output each row of the data, format line as csv and write to file pointer
            foreach ($allTickets as $key => $value) {
                $prefix = 'ON';
                $suffix = 'F';
                $new_order_id = $prefix . $value['order_id'] . $suffix;
                $orderID =  $value['cash_sale'] == 1 ? $value['order_id'] : $new_order_id;
                $assignedTicketNumber =  $value['ticket_number'] == 0 ? '-----' : $value['ticket_number'];
                $full_name = $value['full_name'];
                $product = wc_get_product( $value['product_id'] );
                if( $_POST['product_id'] ) {
                    $lineData = array($assignedTicketNumber, $full_name, $orderID);
                } else {
                    $lineData = array($orderID, $full_name, $value['email'], $value['phone_number'], $product->name, $value['date_created']);
                }
                fputcsv($f, $lineData, $delimiter);
            }

            // Move back to beginning of file
            fseek($f, 0);

            // disable caching
            $now = gmdate("D, d M Y H:i:s");
            header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
            header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
            header("Last-Modified: {$now} GMT");

            // force download
            header("Content-Type: application/force-download");
            header("Content-Type: application/octet-stream");
            header("Content-Type: application/download");

            // disposition / encoding on response body
            header("Content-Disposition: attachment;filename={$filename}");
            header("Content-Transfer-Encoding: binary");

            //output all remaining data on a file pointer
            fpassthru($f);
            return ob_get_clean();
            exit;
        }
    }

    /**
     * Hook: woocommerce_single_product_summary.
     *
     * @hooked woocommerce_template_single_title - 5
     * @hooked woocommerce_template_single_rating - 10
     * @hooked woocommerce_template_single_price - 10
     * @hooked woocommerce_template_single_excerpt - 20
     * @hooked woocommerce_template_single_add_to_cart - 30
     * @hooked woocommerce_template_single_meta - 40
     * @hooked woocommerce_template_single_sharing - 50
     * @hooked WC_Structured_Data::generate_product_data() - 60
     */
    public static function checkPage()
    {
        $adminHelper = new AdminHelper;
        $productData = get_queried_object();
        $productData = wc_get_product( $productData->ID );

        /** register bs styles and scripts
         * ===================================== */
        $version = date("ymd-Gis", filemtime(WPDIGITALDRIVE_COMPETITIONS_PATH . 'assets/css/bootstrap.min.css'));
        wp_register_style('competition-bootstrap-styles', WPDIGITALDRIVE_COMPETITIONS_URL . 'assets/css/bootstrap.min.css?v=' . $version);

        $version = date("ymd-Gis", filemtime(WPDIGITALDRIVE_COMPETITIONS_PATH . 'assets/css/compiled/single-product.css'));
        wp_register_style('competition-single-product-styles', WPDIGITALDRIVE_COMPETITIONS_URL . 'assets/css/compiled/single-product.css?v=' . $version);

        $version = date("ymd-Gis", filemtime(WPDIGITALDRIVE_COMPETITIONS_PATH . 'assets/js/bootstrap.bundle.min.js'));
        wp_register_script('competition-bootstrap-script', WPDIGITALDRIVE_COMPETITIONS_URL . 'assets/js/bootstrap.bundle.min.js?v=' . $version, array('jquery'), '', true);

        $version = date("ymd-Gis", filemtime(WPDIGITALDRIVE_COMPETITIONS_PATH . 'assets/js/single-product.js'));
        wp_register_script('competition-single-product-script', WPDIGITALDRIVE_COMPETITIONS_URL . 'assets/js/single-product.js?v=' . $version, array('jquery'), '', true);

        if(is_singular('product')) {
            if( $productData->get_type() == 'competition' ) {

                /** enqueue necessary styles and scripts
                 * ===================================== */
                wp_enqueue_style("competition-bootstrap-styles");
                wp_enqueue_style("competition-single-product-styles");
                wp_enqueue_script("competition-bootstrap-script");
                wp_enqueue_script("competition-single-product-script");

                /** remove default data arrangement
                 * ===================================== */
                remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
                remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
                remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
                remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
                // remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
                remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
                remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
            }
        }
    }

    public static function setWinnerPostType()
    {
        $labels = array(
            'name'                => _x( 'Winners', 'Post Type General Name', '' ),
            'singular_name'       => _x( 'Winners', 'Post Type Singular Name', '' ),
            'menu_name'           => __( 'Dashboard', '' ),
            'view_item'           => __( 'View Winners', '' ),
            'add_new_item'        => __( 'Add New Winners', '' ),
            'add_new'             => __( 'Add New', '' ),
            'edit_item'           => __( 'Edit Winners', '' ),
            'update_item'         => __( 'Update Winners', '' ),
            'search_items'        => __( 'Search Winners', '' ),
            'not_found'           => __( 'Not Found', '' ),
            'not_found_in_trash'  => __( 'Not found in Trash', '' ),
        );
        $args = array(
            'label'               => __( 'winners', '' ),
            'description'         => __( 'Winners', '' ),
            'labels'              => $labels,
            'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', ),
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'menu_position'       => 3,
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'capability_type'     => 'post',
            'show_in_rest' => true,
        );
        register_post_type( 'competition-winners', $args );
    }
}
