<?php
declare(strict_types=1);

namespace WpDigitalDriveCompetitions\Shortcode\EntryListsCompetition;

/**
 *Show Entry Lists Competition (Entry lists will be displayed once the competition closes.) -- parameters are per_page='6', orderby='id', order='desc' & heading_title='Entry Lists Competition'
 */
class EntryListsCompetition
{
    public static function display($attr) {
        ob_start();
        extract($attr);

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
                        'compare' => '>=',
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
                            <a href="<?= get_the_permalink(); ?>" class="entry-list-box">
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
<?php
        $output = ob_get_clean();
        return $output;
    }
}
