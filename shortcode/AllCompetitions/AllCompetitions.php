<?php
declare(strict_types=1);

namespace WpDigitalDriveCompetitions\Shortcode\AllCompetitions;

/**
 *Show All Competitions -- parameters are per_page='6', orderby='id', order='desc' & heading_title='Featured Competitions'
 */
class AllCompetitions
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
        ) );
?>
    <div class='featured-competition-section'>
        <div class="featured-competition-heading">
            <h2 class="section-title"><?= $heading_title; ?></h2>
        </div>
        <?php if( $query->have_posts() ): ?>
            <div class="featured-competitions-wrapper">
                <?php while ($query->have_posts()) : $query->the_post(); ?>
                    <?php
                        $product = wc_get_product( get_the_id() );
                        $price = $product->get_price_html();
                        $end_date = get_post_meta(get_the_id(), '_end_date_and_time') ? get_post_meta(get_the_id(), '_end_date_and_time')[0] : '';
                        $total_sold = get_post_meta( $product->id, 'total_sales', true );
                        $stock_qty = get_post_meta( $product->id, '_maximum_ticket' ) ? get_post_meta( $product->id, '_maximum_ticket' )[0] : '--';
                        $sales_percentage = 0;
                        if($stock_qty != "--") {
                            $sales_percentage = ((int)$total_sold/(int)$stock_qty) * 100;
                        }
                    ?>
                    <div class="competition-item" data-enddate="<?= $end_date; ?>">
                        <a href="<?= get_the_permalink(); ?>" class="competition-box">
                            <div class="competition-feat-img">
                                <?= wp_get_attachment_image( $product->get_image_id(), 'full' ); ?>
                            </div>
                            <div class="competition-content">
                                <?php if($product->get_type() == 'competition'): ?>
                                    <div class="draw-date-wrap">
                                        <?php if(get_post_meta(get_the_id(), '_draw_date_and_time')): ?>
                                            <?php if(date("D jS M") > date('D jS M', strtotime(get_post_meta( get_the_id(), '_draw_date_and_time')[0]))): ?>
                                                <h4>EXPIRED</h4>
                                            <?php else: ?>
                                                <h4>Draw <?= date('D jS M', strtotime(get_post_meta( get_the_id(), '_draw_date_and_time')[0])); ?></h4>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <h4>No Draw Date</h4>
                                        <?php endif; ?>
                                    </div>
                                    <div class="countdown-wrap">
                                        <div class="countdown">
                                            <div class="count-item">
                                                <div class="count-value e-m-days">00</div>
                                                <label>DAYS</label>
                                            </div>
                                            <div class="count-item">
                                                <div class="count-value e-m-hours">00</div>
                                                <label>HOUR</label>
                                            </div>
                                            <div class="count-item">
                                                <div class="count-value e-m-minutes">00</div>
                                                <label>MIN</label>
                                            </div>
                                            <div class="count-item">
                                                <div class="count-value e-m-seconds">00</div>
                                                <label>SEC</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="sold-wrap">
                                        <div class="progress-wrapper">
                                            <div class="mini-label"><?= $total_sold .'/'. $stock_qty; ?></div>
                                            <div class="progress-bar" style="width:<?= $sales_percentage; ?>%;">
                                                <div class="percentage-marker">
                                                    <span><?= $sales_percentage; ?>%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <div class="price-wrap">
                                    <h4><?= $price; ?> per entry</h4>
                                </div>
                                <div class="short-desc-wrap">
                                    <?= get_the_excerpt(); ?>
                                </div>
                                <div class="btn-wrap">
                                    <div class="btn-addtocart">Enter Now</div>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endwhile; wp_reset_query(); ?>
            </div>
            <div class="featured-competition-viewall-btn">
                <a href="<?= get_permalink( wc_get_page_id( 'shop' ) ) ?>" class="btn-viewall">VIEW ALL COMPETITIONS</a>
            </div>
        <?php endif; ?>
    </div>
<?php
        $output = ob_get_clean();
        return $output;
    }

}
?>
