<?php
/**
 * @brief Grayscale, a theme for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Themes
 *
 * @author Philippe aka amalgame and contributors
 * @copyright GPL-2.0
 */

if (!defined('DC_RC_PATH')) {
    return;
}

$this->registerModule(
    "Grayscale",                           				// Name
    "Grayscale Bootstrap theme for Dotclear",  			// Description
    "Start Bootstrap and Philippe aka amalgame",        // Author
    '2.0',                                       		// Version
    [                                          			// Properties
        'requires'          => [['core', '2.18']], 		// Dependencies
        'standalone_config' => true,
        'type'              => 'theme',
        'tplset' 			=> 'mustek'
    ]
);
