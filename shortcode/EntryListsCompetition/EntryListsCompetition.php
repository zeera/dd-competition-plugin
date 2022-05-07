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
        </div>
<?php
        $output = ob_get_clean();
        return $output;
    }
}
