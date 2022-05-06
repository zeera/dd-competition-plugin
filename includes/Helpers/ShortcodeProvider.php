<?php

/**
 * Shortcode Provider
 */

declare(strict_types=1);

namespace WpDigitalDriveCompetitions\Helpers;

use ReflectionClass;
use WpDigitalDriveCompetitions\Core\Controller;

/**
 * Shortcode Provider
 */
class ShortcodeProvider extends Controller
{
    public function get_shortcodes () {
        $valid_shortcode_files = [];
        $dirs = glob(WPDIGITALDRIVE_COMPETITIONS_PATH . 'shortcode/*' , GLOB_ONLYDIR);
        foreach( $dirs as $index => $dir ) {
            $tmp = explode('/', $dir);
            $shortcode_name = $this->camel2dashed(end($tmp));
            $nice_shortcode_name = $this->dashesToCamelCase($shortcode_name, true);
            if (file_exists(sprintf('%s/%s.php', $dir, $nice_shortcode_name))) {
                $class_name = $this->process_class_name($shortcode_name);
                if (apply_filters( 'WpDigitalDriveCompetitions_filter_allowed_shortcodes', true, $shortcode_name )) {
                    $tmpShortcodeName = str_replace('-', '', $shortcode_name);
                    $valid_shortcode_files[] = [
                        'class_name' => $class_name,
                        'id' => sprintf('%s', $shortcode_name),
                        'code' => sprintf('[%s]', $shortcode_name),
                        'document' => $this->get_shortcode_docs($class_name),
                    ];

                }
            }
        }

        return apply_filters( 'WpDigitalDriveCompetitions_filter_shortcodes', $valid_shortcode_files );
    }

    public function get_shortcode_docs ($class_name, $raw = false) {

        $doc_block = (new ReflectionClass($class_name))->getDocComment();

        if ($doc_block === null)
        {
            return null;
        }

        if ($raw) {
            return $doc_block;
        }

        $result = null;
        $lines = preg_split('/\R/', $doc_block);
        foreach($lines as $line)
        {
            $line = trim($line, "/* \t\x0B\0");
            if ($line === '')
            {
                continue;
            }

            if ($result != null)
            {
                $result .= ' ';
            }
            $result .= $line;
        }
        if($result != null) {
            return $result;
        }
        return 'No description found.';
    }

    public function process_class_name ($slug) {
        $tmp = ucwords($slug, '-');
        $tmp = str_replace('-', '', $tmp);

        return 'WpDigitalDriveCompetitions\\Shortcode\\' . $tmp . '\\' . $tmp;
    }

    public function dashesToCamelCase($string, $capitalizeFirstCharacter = false)
    {
        $str = str_replace('-', '', ucwords($string, '-'));
        if (!$capitalizeFirstCharacter) {
            $str = lcfirst($str);
        }
        return $str;
    }

    public function camel2dashed($className) {
        return strtolower(preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1-', $className));
    }
}
