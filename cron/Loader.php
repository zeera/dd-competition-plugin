<?php
declare(strict_types=1);
namespace WpDigitalDriveCompetitions\Cron;

/**
 * Cron loader.
 */
class Loader
{
    /**
     * Initialize the CRON hooks
     */
    public static function init()
    {
        //Add any cron actions
        self::registerCronActions();

        //Adding any custom time periods
        self::initCronSchedules();

        //Add the cron tasks.
        self::scheduleCrons();
    }

    public static function initCronSchedules(){
        //This is to create custom time periods for cronts to fire off on,
        //Created times here "cust_min_fifteen" can be used in wp_schedule_event
        add_filter('cron_schedules', function ($schedules)
        {
            $schedules['cust_min_fifteen'] = array(
                'interval' => 60*15,
                'display' => __('Once Every 15 Minutes')
            );
            return $schedules;
        });

    }

    public static function registerCronActions(){
        //We are registering an action with a given name, this should be used later on in the schedule cron
        //Or even call other actions defined elsewhere.
        add_action( 'wpplugin_crontest', [self::class, 'cronTest']); //$priority,accepted_args are the last twol
    }

    /**
     * Cron tasks hooks
     *
     * We check to see if the next cron even exists, in order to fix accidental removals or other updates that may remove the initial creation of the cron
     */
    public static function scheduleCrons()
    {
        //If we have args in the function make sure the wp_next_scheduled has the required args in it as well.
        if (!wp_next_scheduled('wpplugin_crontest')) {
            wp_schedule_event(time(), 'daily','wpplugin_crontest');
        }

    }

    //Just a simple basic cron, put into a seperate task for
    public static function cronTest(){
        //Do something in the cron
        error_log("test");
    }
}
