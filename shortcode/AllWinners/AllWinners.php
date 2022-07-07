<?php
declare(strict_types=1);

namespace WpDigitalDriveCompetitions\Shortcode\AllWinners;

/**
 *Show All Competitions -- parameters are per_page='6', orderby='id', order='desc' & heading_title='Featured Competitions'
 */
class AllWinners
{
    public static function display($attr) {
        ob_start();
        extract($attr);
        $page_query = get_queried_object();

        $args = array(
            'post_type'      => 'competition_winners',
            'post_status'    => 'publish',
            'posts_per_page' => $per_page,
            'orderby'        => $orderby,
            'paged'          => get_query_var('paged') ?? 1,
            'order'          => $order,
        );

        $query = new \WP_Query( $args );
    ?>
        <div class="competition-listing-section">
            <?php if( $query->have_posts() ): ?>
                <div class="competition-listing-wrapper">
                    <?php while ($query->have_posts()) : $query->the_post(); ?>
                        <div class="competition-item">
                            <div class="competition-box winners">
                                <div class="competition-feat-img">
                                    <?= wp_get_attachment_image(get_post_thumbnail_id( get_the_ID() ) , 'full' ); ?>
                                </div>
                                <div class="competition-content">
                                    <h3><?= get_field('competition_title'); ?></h3>
                                    <div class="details">
                                        <h4>Winner: <?= get_the_title(); ?></h4>
                                        <h5>Location: <?= get_field('location'); ?></h5>
                                        <h6>Winning Number: <?= get_field('winning_number'); ?></h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; wp_reset_query(); ?>
                </div>
                <div class="pagination">
                    <?php
                        $big = 999999999;
                        $pagination = paginate_links( array(
                            // 'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                            'format' => '?paged=%#%',
                            'prev_next' => true,
                            'prev_text' => __('<i class="fa fa-angle-left"></i>'),
                            'next_text' => __('<i class="fa fa-angle-right"></i>'),
                            'current' => max( 1, get_query_var('paged') ),
                            'total' => $query->max_num_pages
                        ) );
                        echo $pagination;
                    ?>
                </div>
            <?php endif; ?>
        </div>
    <?php
        $output = ob_get_clean();
        return $output;
    }
}
?>
