<?php
declare(strict_types=1);

namespace WpDigitalDriveCompetitions\Shortcode\EntryListsCompetition;
use WpDigitalDriveCompetitions\Models\TicketNumbers;
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
            $ticketNumbersModel = new TicketNumbers;
            $ticketNumbers = $ticketNumbersModel->getProductEntryList( $product->ID );
        ?>
        <div class="entry-list-single-page">
            <div class="entry-list-single-heading"><h1>Entry Lists For <?= $product->post_title; ?></h1></div>
            <table>
                <thead>
                    <tr>
                        <th>Ticket No.</th>
                        <th>Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($ticketNumbers): ?>
                        <?php foreach($ticketNumbers as $key => $tmpData): ?>
                            <?php
                                $userData = get_userdata( $tmpData['userid'] );
                            ?>
                            <tr>
                                <td><?php esc_attr_e($tmpData['ticket_number'], 'WpAdminStyle'); ?></td>
                                <td><?php esc_attr_e( $userData->first_name . ' ' . $userData->last_name, 'WpAdminStyle' ); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2">No Entry List</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
<?php
        $output = ob_get_clean();
        return $output;
    }
}
