<?php
/**
 * @brief Grayscale, a theme for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Themes
 *
 * @copyright Philippe aka amalgame
 * @copyright GPL-2.0-only
 */
namespace themes\grayscale;

if (!defined('DC_RC_PATH')) {
    return;
}
// public part below

if (!defined('DC_CONTEXT_ADMIN')) {
    return;
}
// admin part below

# Behaviors
$GLOBALS['core']->addBehavior('adminPageHTMLHead', [__NAMESPACE__ . '\tplGrayscaleThemeAdmin', 'adminPageHTMLHead']);
$GLOBALS['core']->addBehavior('adminPopupMedia', [__NAMESPACE__ . '\tplGrayscaleThemeAdmin', 'adminPopupMedia']);

class tplGrayscaleThemeAdmin
{
    public static function adminPageHTMLHead()
    {
        if ($GLOBALS['core']->blog->settings->system->theme != 'grayscale') {
            return;
        }
        $grayscale_admin_js = $GLOBALS['core']->blog->settings->system->themes_url."/".$GLOBALS['core']->blog->settings->system->theme."/js/admin.js";

        echo '<script src="' . $grayscale_admin_js . '" ></script>';
    }

    public static function adminPopupMedia($editor = '')
    {
        if (empty($editor) || $editor != 'admin.blog.theme') {
            return;
        }
        $theme_url = $GLOBALS['core']->blog->settings->system->themes_url."/".$GLOBALS['core']->blog->settings->system->theme;
        
        return dcPage::jsLoad($theme_url . '/js/popup_media.js');
    }
}
