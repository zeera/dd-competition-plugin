<?php
/**
 * Activation hook class file.
 */
declare(strict_types=1);

namespace WpDigitalDriveCompetitions\Hooks\CompetitionsBackend;
use WpDigitalDriveCompetitions\Install\DatabaseStructure;

/**
 * Activation hook.
 */
class ActivationDeactivationHook
{
    /**
     * Hook called when the plugin is activated
     */
    public static function activate()
    {
        // Install the databases
        DatabaseStructure::install();
    }

    /**
     * Hook called when the plugin is de-activated
     */
    public static function deactivate()
    {
    }
}
