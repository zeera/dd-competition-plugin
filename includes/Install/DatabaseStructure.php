<?php
declare(strict_types=1);
namespace WpDigitalDriveCompetitions\Install;

/**
 * Database Structure
 */
class DatabaseStructure
{
    /**
     * Hook called when the plugin is activated
     */
    public static function install()
    {
        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $sql = [];


        $data_table = $wpdb->prefix."ticket_numbers";

        $sql = " CREATE TABLE IF NOT EXISTS $data_table (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `userid` bigint(20) unsigned NOT NULL,
                `full_name` VARCHAR(300) DEFAULT NULL,
                `club_name` VARCHAR(300) DEFAULT NULL,
                `email` VARCHAR(300) DEFAULT NULL,
                `phone_number` VARCHAR(300) DEFAULT NULL,
                `order_id` bigint(20) UNSIGNED NOT NULL,
                `cash_sale` TINYINT NOT NULL DEFAULT '0',
                `ticket_number` bigint(20) DEFAULT NULL,
                `answer` varchar(200) DEFAULT NULL,
                `product_id` bigint(20) UNSIGNED NOT NULL,
                `item_id` bigint(20) UNSIGNED NOT NULL,
                `date_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `created_by` VARCHAR(128) NOT NULL,
                `last_updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `last_updated_by` VARCHAR(128) NOT NULL,
                    PRIMARY KEY (`id`)
                ); ";

        dbDelta($sql);
    }
}
