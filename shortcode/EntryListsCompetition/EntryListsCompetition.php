<?php
declare(strict_types=1);

namespace WpDigitalDriveCompetitions\Shortcode\EntryListsCompetition;
use WpDigitalDriveCompetitions\Models\TicketNumber;
/**
 *Show Entry Lists Competition (Entry lists will be displayed once the competition closes.) -- parameters are per_page='6', orderby='id', order='desc' & heading_title='Entry Lists Competition'
 */
class EntryListsCompetition
{
    public static function display($attr) {
        ob_start();
        extract($attr);

        $isSingle = false;
        if(isset($_GET['competition_name']) && !empty($_GET['competition_name'])) {
            $isSingle = true;
        }
?>
    <?php if(!$isSingle): ?>
        <?php
            $query = new \WP_Query( array(
                'post_type'           => 'product',
                'post_status'         => 'publish',
                'ignore_sticky_posts' => 1,
                'posts_per_page'      => $per_page,
                'orderby'             => $orderby,
                'order'               => $order,
                'meta_query' => array(
                    'relation' => 'AND',
                        array(
                            'key' => '_end_date_and_time',
                            'value' => date('Y-m-d H:i:s'),
                            'compare' => '<=',
                            'type' => 'datetime'
                        ),
                )
            ) );
        ?>
        <div class='entry-lists-competition-section'>
            <div class="entry-lists-competition-heading">
                <h2 class="section-title"><?= $heading_title ?></h2>
            </div>
            <?php if( $query->have_posts() ): ?>
                <div class="entry-lists-competition-wrapper">
                    <?php while ($query->have_posts()) : $query->the_post(); ?>
                        <?php
                            $product = wc_get_product( get_the_id() );
                        ?>
                        <div class="entry-list-item">
                            <a href="<?= get_permalink( get_page_by_path( 'entry-lists' ) ).'?competition_name='.$product->slug; ?>" class="entry-list-box">
                                <div class="entry-list-feat-img">
                                    <?= wp_get_attachment_image( $product->get_image_id(), 'full' ); ?>
                                </div>
                                <div class="entry-list-content">
                                    <?php if($product->get_type() == 'competition'): ?>
                                        <div class="draw-date-wrap">
                                            <?php if(get_post_meta(get_the_id(), '_draw_date_and_time')): ?>
                                                <h4><?= date('M d, Y - g a', strtotime(get_post_meta( get_the_id(), '_draw_date_and_time')[0])); ?></h4>
                                            <?php else: ?>
                                                <h4>No Draw Date</h4>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    <h3 class="entry-list-title"><?= get_the_title(); ?></h3>
                                    <div class="btn-wrap">
                                        <div class="btn-download">Download Entry Lists</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endwhile; wp_reset_query(); ?>
                </div>
            <?php endif; ?>
        </div>
    <?php else:  ?>
        <?php
            $product = get_page_by_path( $_GET['competition_name'], OBJECT, 'product' );
            $ticketNumbersModel = new TicketNumber;
            $ticketNumbers = $ticketNumbersModel->getProductEntryList( $product->ID );
        ?>
        <div class="card w-100 mw-100 p-0">
            <div class="card-header">
                Entry List
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table
                        data-ppp-options="<?php echo get_option('data_per_page_options') ? get_option('data_per_page_options') : ''; ?>"
                        data-ppp="<?php echo get_option('data_per_page') ? get_option('data_per_page') : ''; ?>"
                        id="entrylist_table"
                        class="table table-striped entrylist_table"
                        style="width:100% border:0;">
                        <thead class="table-dark">
                            <tr>
                                <th style="border:0;">Ticket No.</th>
                                <th style="border:0;">Name</th>
                                <th style="border:0;">Order ID</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($ticketNumbers): ?>
                                <?php foreach($ticketNumbers as $key => $tmpData): ?>
                                    <?php
                                        $full_name = $tmpData['full_name'];
                                    ?>
                                    <tr>
                                        <td style="border:0;"><?php ($tmpData['ticket_number']) == 0 ? '--' : esc_attr_e($tmpData['ticket_number'], 'WpAdminStyle'); ?></td>
                                        <td style="border:0;"><?php esc_attr_e( $full_name, 'WpAdminStyle' ); ?></td>
                                        <?php
                                            $prefix = 'ON';
                                            $suffix = 'F';
                                            $new_order_id = $prefix . $tmpData['order_id'] . $suffix;
                                            $orderID =  $tmpData['cash_sale'] == 1 ? $tmpData['order_id'] : $new_order_id ;
                                        ?>
                                        <td style="border:0;"><?php esc_attr_e( $orderID, 'WpAdminStyle' ); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <tfoot class="table-dark">
                            <tr>
                                <th style="border:0;">Ticket No.</th>
                                <th style="border:0;">Name</th>
                                <th style="border:0;">Order ID</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <style>
            .card a {
                text-decoration: none;
            }
            .card #entrylist_table_length {
                margin-bottom: 20px;
            }
            @media (min-width: 768px) {
                .card #entrylist_table_length {
                    margin-bottom: initial;
                }
            }
            .card #entrylist_table_filter input {
                max-width: 160px;
                width: 100%;
            }
        </style>
        <script>
            jQuery(document).ready(function($) {
                $('.entrylist_table').each(function () {
                    var ppp = $(this).data('ppp');
                    var pppOptions = $(this).data('ppp-options');
                    var pppOptionsArr = pppOptions.split(',');
                    pppOptionsArr.unshift(`${ppp}`);
                    $(this).DataTable({
                        "pageLength": parseInt(ppp),
                        "lengthMenu": pppOptionsArr,
                        "responsive": true,
                        'autoWidth': true,
                    });
                });
            });
        </script>
    <?php endif; ?>
<?php
        $output = ob_get_clean();
        return $output;
    }
}
