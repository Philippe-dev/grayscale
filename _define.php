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
    'Grayscale',                           				// Name
    'Grayscale Bootstrap 5 theme for Dotclear',  		// Description
    'Philippe aka amalgame and contributors',           // Author
    '2.5.2',                                       		// Version
    [                                          			// Properties
        'requires'          => [['core', '2.19']], 		// Dependencies
        'standalone_config' => true,
        'type'              => 'theme',
        'tplset'            => 'mustek',
    ]
);
