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
    'Grayscale',
    'Grayscale Bootstrap 5 theme for Dotclear',
    'Philippe aka amalgame and contributors',
    '2.7',
    [
        'requires'          => [['core', '2.24']],
        'standalone_config' => true,
        'type'              => 'theme',
        'tplset'            => 'mustek',
    ]
);
