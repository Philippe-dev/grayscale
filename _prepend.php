<?php
/**
 * @brief Éditorial, a theme for Dotclear 2
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
    return false;
}
// admin part below

# Behaviors
$GLOBALS['core']->addBehavior('adminPageHTMLHead', [__NAMESPACE__ . '\tplEditorialThemeAdmin', 'adminPageHTMLHead']);

class tplEditorialThemeAdmin
{
    public static function adminPageHTMLHead()
    {
        global $core;
        if ($core->blog->settings->system->theme != 'grayscale') {
            return;
        }
        
        echo "
        <script>
        $(function() {
            $('#user-default-image-selector').on('click', function (e) {
                window.open('media.php?plugin_id=blog_theme&popup=true&select=1&link_type=attachment', 'dc_popup', 'alwaysRaised=yes,dependent=yes,toolbar=yes,height=500,width=760,' + 'menubar=no,resizable=yes,scrollbars=yes,status=no');
                e.preventDefault();
                return false;
            });
        });
        </script>";;
    }
}
