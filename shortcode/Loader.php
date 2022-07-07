<?php

/**
 * Shortcode loader class file.
 */

declare(strict_types=1);

namespace WpDigitalDriveCompetitions\Shortcode;

use WpDigitalDriveCompetitions\Shortcode\FeaturedCompetitions\FeaturedCompetitions;
use WpDigitalDriveCompetitions\Shortcode\AllWinners\AllWinners;

use WpDigitalDriveCompetitions\Shortcode\RelatedCompetitions\RelatedCompetitions;

use WpDigitalDriveCompetitions\Shortcode\AllCompetitions\AllCompetitions;
use WpDigitalDriveCompetitions\Shortcode\EntryListsCompetition\EntryListsCompetition;
use WpDigitalDriveCompetitions\Shortcode\EndingSoonCompetitions\EndingSoonCompetitions;

/**
 * Shortcode Loader
 */
class Loader
{
    /**
     * Add hook to init the shortcode hook
     *
     * The resulting class is expected to return a string
     */
    public static function init()
    {
        // add_shortcode('wpplugin_quickexample', [self::class, "quickExample"]);
        // add_shortcode('wpplugin_bigexample', [self::class, "bigExample"]);
        $version = date("ymd-Gis", filemtime(WPDIGITALDRIVE_COMPETITIONS_PATH . 'assets/css/featured-competitions.css'));
        wp_register_style('featured-competition-style', WPDIGITALDRIVE_COMPETITIONS_URL . 'assets/css/featured-competitions.css?v=' . $version);
        wp_register_style('bootstrap-style', WPDIGITALDRIVE_COMPETITIONS_URL . 'assets/css/bootstrap.min.css?v=' . $version, true);
        wp_register_style('entrylists-datatable-style', WPDIGITALDRIVE_COMPETITIONS_URL . 'assets/css/dataTables.min.css?v=' . $version, true);
        wp_register_style('entry-lists-style', WPDIGITALDRIVE_COMPETITIONS_URL . 'assets/css/compiled/entry-lists.css?v=' . $version);
        wp_register_style('competition-listing-style', WPDIGITALDRIVE_COMPETITIONS_URL . 'assets/css/compiled/competition-listing.css?v=' . $version);

        wp_register_script('featured-competition-scripts', WPDIGITALDRIVE_COMPETITIONS_URL . 'assets/js/featured-competitions.js?v=' . $version, array('jquery'), '', true);
        wp_register_script('bootstrap-scripts', WPDIGITALDRIVE_COMPETITIONS_URL . 'assets/js/bootstrap.bundle.min.js?v=' . $version, array('jquery'), '', true);
        wp_register_script('datatable-scripts', WPDIGITALDRIVE_COMPETITIONS_URL . 'assets/js/dataTables.min.js?v=' . $version, array('jquery'), '', true);
        wp_register_script('datatable-bootstrap-script', WPDIGITALDRIVE_COMPETITIONS_URL . 'assets/js/dataTables.bootstrap5.min.js?v=' . $version, array('jquery'), '', true);
        wp_register_script('competition-listing-scripts', WPDIGITALDRIVE_COMPETITIONS_URL . 'assets/js/competition-listing.js?v=' . $version, array('jquery'), '', true);

        add_shortcode( 'display_attributes', [self::class, "attributes"] );
        add_shortcode( 'featured-competitions', [self::class, "displayFeaturedCompetition"] );
        add_shortcode( 'all-competitions', [self::class, "displayAllCompetition"] );
        add_shortcode( 'related-competitions', [self::class, "displayRelatedCompetition"] );
        add_shortcode( 'entry-lists-competition', [self::class, "displayEntryListsCompetition"] );
        add_shortcode( 'ending-soon-competitions', [self::class, "displayEndingSoonCompetition"] );
        add_shortcode( 'all-winners', [self::class, "displayWinners"] );
        add_shortcode( 'do-product-summary', [self::class, "doWooProductSummary"] );
    }

    public static function doWooProductSummary()
    {
        do_action( 'woocommerce_single_product_summary' );
    }

    public static function displayFeaturedCompetition($attr){
        if(!wp_style_is('competition-listing-style')) {
            wp_enqueue_style("competition-listing-style");
        }
        if(!wp_script_is('competition-listing-scripts')) {
            wp_enqueue_script("competition-listing-scripts");
        }
        //A useful function to know
        $attr = shortcode_atts(array(
            'per_page'        => '6',
            'orderby'      => 'id',
            'order'        => 'DESC',
            'hide_title'    => true,
            'heading_title' => 'Featured Competitions'
        ), $attr);

        $featuredCompetitions = new FeaturedCompetitions;
        $returnstring = $featuredCompetitions->display($attr);

        return $returnstring;
    }

    public static function displayAllCompetition($attr){
        if(!wp_style_is('competition-listing-style')) {
            wp_enqueue_style("competition-listing-style");
        }
        if(!wp_script_is('competition-listing-scripts')) {
            wp_enqueue_script("competition-listing-scripts");
        }
        //A useful function to know
        $attr = shortcode_atts(array(
            'per_page'      => '6',
            'orderby'       => 'id',
            'order'         => 'DESC',
            'hide_title'    => true,
            'heading_title' => 'All Competitions'
        ), $attr);

        $allCompetitions = new AllCompetitions;
        $returnstring = $allCompetitions->display($attr);

        return $returnstring;
    }

    public static function displayRelatedCompetition($attr) {
        if(!wp_style_is('competition-listing-style')) {
            wp_enqueue_style("competition-listing-style");
        }
        if(!wp_script_is('featured-competition-scripts')) {
            wp_enqueue_script("featured-competition-scripts");
        }
        //A useful function to know
        $attr = shortcode_atts(array(
            'per_page'        => '6',
            'orderby'      => 'id',
            'order'        => 'DESC',
        ), $attr);

        $allCompetitions = new RelatedCompetitions;
        $returnstring = $allCompetitions->display($attr);

        return $returnstring;
    }

    public static function displayEndingSoonCompetition($attr){
        if(!wp_style_is('competition-listing-style')) {
            wp_enqueue_style("competition-listing-style");
        }
        if(!wp_script_is('competition-listing-scripts')) {
            wp_enqueue_script("competition-listing-scripts");
        }
        //A useful function to know
        $attr = shortcode_atts(array(
            'per_page'      => '6',
            'orderby'       => 'id',
            'order'         => 'DESC',
            'hide_title'    => true,
            'heading_title' => 'Competitions Ending Soon'
        ), $attr);

        $endingSoonCompetitions = new EndingSoonCompetitions;
        $returnstring = $endingSoonCompetitions->display($attr);

        return $returnstring;
    }

    public static function displayEntryListsCompetition($attr){
        if(!wp_style_is('entry-lists-style')) {
            wp_enqueue_style("entry-lists-style");
        }
        if(!wp_style_is('entrylists-datatable-style')) {
            wp_enqueue_style("entrylists-datatable-style");
        }
        if(!wp_style_is('bootstrap-style')) {
            wp_enqueue_style("bootstrap-style");
        }
        if(!wp_script_is('datatable-scripts')) {
            wp_enqueue_script("datatable-scripts");
        }
        if(!wp_script_is('datatable-bootstrap-script')) {
            wp_enqueue_script("datatable-bootstrap-script");
        }
        if(!wp_script_is('bootstrap-scripts')) {
            wp_enqueue_script("bootstrap-scripts");
        }
        //A useful function to know
        $attr = shortcode_atts(array(
            'per_page'        => '6',
            'orderby'      => 'id',
            'order'        => 'DESC',
            'hide_title'    => true,
            'heading_title' => 'Entry Lists'
        ), $attr);

        $entryListsCompetitions = new EntryListsCompetition;
        $returnstring = $entryListsCompetitions->display($attr);

        return $returnstring;
    }

    public static function displayWinners($attr){
        if(!wp_style_is('competition-listing-style')) {
            wp_enqueue_style("competition-listing-style");
        }
        if(!wp_script_is('featured-competition-scripts')) {
            wp_enqueue_script("featured-competition-scripts");
        }
        //A useful function to know
        $attr = shortcode_atts(array(
            'per_page' => 9,
            'orderby'  => 'id',
            'order'    => 'DESC',
        ), $attr);

        $allCompetitions = new AllWinners;
        $returnstring = $allCompetitions->display($attr);

        return $returnstring;
    }
}
