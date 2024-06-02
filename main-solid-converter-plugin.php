<?php
/*
Plugin Name: Solid Unit Converter
Version: 1.0
Description: A extensible unit converter.
Author: Tech Bizz Pvt.Ltd.
*/

defined('ABSPATH') || exit;
use Techbizz\UnitConverterModule\Controllers\MainController;
use Techbizz\UnitConverterModule\Managers\UnitConverterManager;

require_once(plugin_dir_path(__FILE__) . 'class-plugin-adapter.php');
require_once(plugin_dir_path(__FILE__) . 'solid-converter/vendor/autoload.php');

if (class_exists('PluginAdapter')) {
    new PluginAdapter(new MainController(new UnitConverterManager));
}

