<?php

/**
 * Shortcode loader class file.
 */

declare(strict_types=1);

namespace WpDigitalDriveCompetitions\Shortcode;

use WpDigitalDriveCompetitions\Shortcode\FeaturedCompetitions\FeaturedCompetitions;
use WpDigitalDriveCompetitions\Shortcode\AllCompetitions\AllCompetitions;
use WpDigitalDriveCompetitions\Shortcode\EntryListsCompetition\EntryListsCompetition;

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

        wp_register_script('featured-competition-scripts', WPDIGITALDRIVE_COMPETITIONS_URL . 'assets/js/featured-competitions.js?v=' . $version, array('jquery'), '', true);

        add_shortcode( 'display_attributes', [self::class, "attributes"] );
        add_shortcode( 'featured-competitions', [self::class, "displayFeaturedCompetition"] );
        add_shortcode( 'all-competitions', [self::class, "displayAllCompetition"] );
        add_shortcode( 'entry-lists-competition', [self::class, "displayEntryListsCompetition"] );
    }

    public static function displayFeaturedCompetition($attr){
        if(!wp_style_is('featured-competition-style')) {
            wp_enqueue_style("featured-competition-style");
        }
        if(!wp_script_is('featured-competition-scripts')) {
            wp_enqueue_script("featured-competition-scripts");
        }
        //A useful function to know
        $attr = shortcode_atts(array(
            'per_page'        => '6',
            'orderby'      => 'id',
            'order'        => 'DESC',
            'heading_title' => 'Featured Competitions'
        ), $attr);

        $featuredCompetitions = new FeaturedCompetitions;
        $returnstring = $featuredCompetitions->display($attr);

        return $returnstring;
    }

    public static function displayAllCompetition($attr){
        if(!wp_style_is('featured-competition-style')) {
            wp_enqueue_style("featured-competition-style");
        }
        if(!wp_script_is('featured-competition-scripts')) {
            wp_enqueue_script("featured-competition-scripts");
        }
        //A useful function to know
        $attr = shortcode_atts(array(
            'per_page'        => '6',
            'orderby'      => 'id',
            'order'        => 'DESC',
            'heading_title' => 'All Competitions'
        ), $attr);

        $allCompetitions = new AllCompetitions;
        $returnstring = $allCompetitions->display($attr);

        return $returnstring;
    }

    public static function displayEntryListsCompetition($attr){
        if(!wp_style_is('featured-competition-style')) {
            wp_enqueue_style("featured-competition-style");
        }
        if(!wp_script_is('featured-competition-scripts')) {
            wp_enqueue_script("featured-competition-scripts");
        }
        //A useful function to know
        $attr = shortcode_atts(array(
            'per_page'        => '6',
            'orderby'      => 'id',
            'order'        => 'DESC',
            'heading_title' => 'Entry Lists'
        ), $attr);

        $entryListsCompetitions = new EntryListsCompetition;
        $returnstring = $entryListsCompetitions->display($attr);

        return $returnstring;
    }
}
