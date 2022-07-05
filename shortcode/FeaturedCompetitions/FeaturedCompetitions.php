<?php
declare(strict_types=1);

namespace WpDigitalDriveCompetitions\Shortcode\FeaturedCompetitions;

/**
 *Show Featured Competitions -- parameters are per_page='6', orderby='id', order='desc' & heading_title='Featured Competitions'
 */
class FeaturedCompetitions
{
    public static function display($attr) {
        ob_start();
        extract($attr);

        $tax_query[] = array(
            'taxonomy' => 'product_visibility',
            'field'    => 'name',
            'terms'    => 'featured',
            'operator' => 'IN', // or 'NOT IN' to exclude feature products
        );
        $query = new \WP_Query( array(
            'post_type'           => 'product',
            'post_status'         => 'publish',
            'ignore_sticky_posts' => 1,
            'posts_per_page'      => $per_page,
            'orderby'             => $orderby,
            'order'               => $order,
            'tax_query'           => $tax_query // <===
        ) );

?>
    <div class="competition-listing-section">
        <?php if(!$hide_title): ?>
            <div class="competition-list-heading">
                <h2 class="section-title"><?= $heading_title; ?></h2>
            </div>
        <?php endif; ?>
        <?php if( $query->have_posts() ): ?>
            <div class="competition-listing-wrapper">
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
                        $competitionStatus = "";
                        if(date("m/d/Y g:i a") > date('m/d/Y g:i a', strtotime(get_post_meta( get_the_id(), '_draw_date_and_time')[0]))) {
                            $competitionStatus = "closed";
                        } elseif(date("m/d/Y") == date('m/d/Y', strtotime(get_post_meta( get_the_id(), '_draw_date_and_time')[0]))) {
                            $competitionStatus = "draw-today";
                        } elseif(date("m/d/Y", strtotime("+ 1 day")) == date('m/d/Y', strtotime(get_post_meta( get_the_id(), '_draw_date_and_time')[0]))) {
                            $competitionStatus = "draw-tomorrow";
                        }
                    ?>
                    <div class="competition-item" data-enddate="<?= date('m/d/Y g:i a', strtotime($end_date)); ?>">
                        <a href="<?= get_the_permalink(); ?>" class="competition-box">
                            <div class="competition-feat-img">
                                <?= wp_get_attachment_image( $product->get_image_id(), 'full' ); ?>
                            </div>
                            <div class="competition-content">
                                <?php if($product->get_type() == 'competition'): ?>
                                    <div class="draw-date-wrap <?= $competitionStatus; ?>">
                                        <?php if(get_post_meta(get_the_id(), '_draw_date_and_time')): ?>
                                            <?php if($competitionStatus == "closed"): ?>
                                                <h4>CLOSED</h4>
                                            <?php elseif($competitionStatus == "draw-today"): ?>
                                                <h4>DRAW TODAY</h4>
                                            <?php elseif($competitionStatus == "draw-tomorrow"): ?>
                                                <h4>DRAW TOMORROW</h4>
                                            <?php else: ?>
                                                <h4>Draw <?= date('D d F', strtotime(get_post_meta( get_the_id(), '_draw_date_and_time')[0])); ?></h4>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <h4>No Draw Date</h4>
                                        <?php endif; ?>
                                    </div>
                                    <div class="countdown-wrap">
                                        <div class="countdown">
                                            <div class="count-item">
                                                <div class="count-value e-m-days">00</div>
                                                <label>Days</label>
                                            </div>
                                            <div class="count-item">
                                                <div class="count-value e-m-hours">00</div>
                                                <label>Hr</label>
                                            </div>
                                            <div class="count-item">
                                                <div class="count-value e-m-minutes">00</div>
                                                <label>Min</label>
                                            </div>
                                            <div class="count-item">
                                                <div class="count-value e-m-seconds">00</div>
                                                <label>Sec</label>
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
                                    <?= get_the_title(); ?>
                                </div>
                                <?php if(get_field('extra_info')): ?>
                                    <div class="extras">
                                        <h4><?= get_field('extra_info'); ?></h4>
                                    </div>
                                <?php endif; ?>
                                <div class="btn-wrap">
                                    <div class="btn-addtocart <?= ($competitionStatus == "closed") ? 'closed' : ''; ?>"><?= ($competitionStatus == "closed") ? 'Closed' : 'Enter Now'; ?></div>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endwhile; wp_reset_query(); ?>
            </div>
            <!-- <div class="competition-listing-viewall-btn">
                <a href="<?php //get_permalink( wc_get_page_id( 'shop' ) ) ?>" class="btn-viewall">VIEW ALL COMPETITIONS</a>
            </div> -->
        <?php endif; ?>
    </div>
<?php
        $output = ob_get_clean();
        return $output;
    }

}
?>
