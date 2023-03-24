<?php

namespace Arkonsoft\Helpers;

use Exception;

abstract class TemplateHelper
{
    /**
     * It loads a template file from the plugin's templates folder, or from the theme's folder if the
     * template file exists there
     * 
     * @param string plugin_dir The plugin directory
     * @param string template_relative_path The path to the template file relative to the plugin's
     * templates folder.
     * @param array args an array of variables to be extracted and made available to the template
     * 
     */
    public static function get_template_part(string $plugin_dir, string $template_relative_path, array $args = [])
    {
        if (empty($plugin_dir) || empty($template_relative_path)) {
            if (WP_DEBUG) {
                throw new Exception("Invalid plugin dir or template relative path");
            }

            return;
        }

        $template_relative_path = self::standardize_file_path($template_relative_path);

        $plugin_name = basename($plugin_dir);

        if (locate_template($plugin_name . DIRECTORY_SEPARATOR . $template_relative_path)) {
            $template = locate_template($plugin_name . DIRECTORY_SEPARATOR . $template_relative_path);
        } else {
            // Template not found in theme's folder, use plugin's template as a fallback
            $template = $plugin_dir . DIRECTORY_SEPARATOR . $template_relative_path;
        }
        
        if (!file_exists($template)) {
            if (WP_DEBUG) {
                throw new Exception("Invalid plugin template name: {$template}");
            }

            return false;
        }

        extract($args);

        include $template;
    }

    /**
     * It loads a template file and returns the result as a string
     * 
     * @param string plugin_dir The plugin directory.
     * @param string template_relative_path The relative path to the template file from the plugin's
     * root directory.
     * @param array args an array of arguments to pass to the template
     * 
     * @return string The result of the template.
     */
    public static function get_template_part_result(string $plugin_dir, string $template_relative_path, array $args = []): string
    {
        ob_start();

        self::get_template_part($plugin_dir, $template_relative_path, $args);

        return ob_get_clean();
    }

    /**
     * It takes a string, removes the .php extension if it exists, and then adds the .php extension
     * back to the end of the string
     * 
     * @param string path The path to the file you want to include.
     * 
     * @return string The path to the file.
     */
    public static function standardize_file_path(string $path): string
    {
        $path = str_replace('.php', '', $path);

        $path .= '.php';

        return $path;
    }
}