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

l10n::set(dirname(__FILE__).'/locales/'.$_lang.'/main');

$core->addBehavior('publicHeadContent', array('grayscalePublic','publicHeadContent'));
$core->addBehavior('publicFooterContent', array('grayscalePublic','publicFooterContent'));

# Simple menu template functions
$core->tpl->addValue('GrayscaleSimpleMenu', ['tplGrayscaleSimpleMenu', 'GrayscaleSimpleMenu']);

class grayscalePublic
{
    public static function publicHeadContent($core)
    {
        # Settings
        $s = $GLOBALS['core']->blog->settings->themes->get($GLOBALS['core']->blog->settings->system->theme . '_random');
        $s = @unserialize($s);

        if (!is_array($s)) {
            $s = [];
        }

        if (!isset($s['default-image'])) {
            $s['default-image'] = 1;
        }
        
        $grayscale_random_css_url = $GLOBALS['core']->blog->settings->system->themes_url."/".$GLOBALS['core']->blog->settings->system->theme."/css/random.css";

        if ($s['default-image'] == 1) {
            return;
        } else {
            echo
            "<link rel='stylesheet' type='text/css' href='". $grayscale_random_css_url ."' media='screen' />";
        }
    }
    public static function publicFooterContent($core)
    {
        
        # Settings
        $s = $GLOBALS['core']->blog->settings->themes->get($GLOBALS['core']->blog->settings->system->theme . '_random');
        $s = @unserialize($s);

        if (!is_array($s)) {
            $s = [];
        }

        if (!isset($s['default-image'])) {
            $s['default-image'] = 1;
        }

        if ($s['default-image'] == 1) {
            return;
        } else {
            echo
        '<script>'."\n".
            "$(document).ready(function() {
            var round = parseInt(Math.random()*6);
                $('header.masthead').addClass('round'+round);
            });".
        "</script>\n";
        }
    }
}

class tplGrayscaleSimpleMenu
{
    # Template function
    public static function GrayscaleSimpleMenu($attr)
    {
        global $core;

        if (!(boolean) $core->blog->settings->system->simpleMenu_active) {
            return '';
        }

        $class       = isset($attr['class']) ? trim($attr['class']) : '';
        $id          = isset($attr['id']) ? trim($attr['id']) : '';
        $description = isset($attr['description']) ? trim($attr['description']) : '';

        if (!preg_match('#^(title|span|both|none)$#', $description)) {
            $description = '';
        }

        return '<?php echo tplGrayscaleSimpleMenu::displayMenu(' .
        "'" . addslashes($class) . "'," .
        "'" . addslashes($id) . "'," .
        "'" . addslashes($description) . "'" .
            '); ?>';
    }

    public static function displayMenu($class = '', $id = '', $description = '')
    {
        global $core;

        $ret = '';

        if (!(boolean) $core->blog->settings->system->simpleMenu_active) {
            return $ret;
        }

        $menu = $core->blog->settings->system->simpleMenu;
        if (is_array($menu)) {
            // Current relative URL
            $url     = $_SERVER['REQUEST_URI'];
            $abs_url = http::getHost() . $url;

            // Home recognition var
            $home_url       = html::stripHostURL($core->blog->url);
            $home_directory = dirname($home_url);
            if ($home_directory != '/') {
                $home_directory = $home_directory . '/';
            }

            // Menu items loop
            foreach ($menu as $i => $m) {
                # $href = lien de l'item de menu
                $href = $m['url'];
                $href = html::escapeHTML($href);

                # Cope with request only URL (ie ?query_part)
                $href_part = '';
                if ($href != '' && substr($href, 0, 1) == '?') {
                    $href_part = substr($href, 1);
                }

                $targetBlank = ((isset($m['targetBlank'])) && ($m['targetBlank'])) ? true : false;

                # Active item test
                $active = false;
                if (($url == $href) ||
                    ($abs_url == $href) ||
                    ($_SERVER['URL_REQUEST_PART'] == $href) ||
                    (($href_part != '') && ($_SERVER['URL_REQUEST_PART'] == $href_part)) ||
                    (($_SERVER['URL_REQUEST_PART'] == '') && (($href == $home_url) || ($href == $home_directory)))) {
                    $active = true;
                }
                $title = $span = '';

                if ($m['descr']) {
                    if (($description == 'title' || $description == 'both') && $targetBlank) {
                        $title = html::escapeHTML(__($m['descr'])) . ' (' .
                        __('new window') . ')';
                    } elseif ($description == 'title' || $description == 'both') {
                        $title = html::escapeHTML(__($m['descr']));
                    }
                    if ($description == 'span' || $description == 'both') {
                        $span = ' <span class="simple-menu-descr">' . html::escapeHTML(__($m['descr'])) . '</span>';
                    }
                }

                if (empty($title) && $targetBlank) {
                    $title = __('new window');
                }
                if ($active && !$targetBlank) {
                    $title = (empty($title) ? __('Active page') : $title . ' (' . __('active page') . ')');
                }

                $label = html::escapeHTML(__($m['label']));

                $item = new ArrayObject([
                    'url'    => $href,   // URL
                    'label'  => $label,  // <a> link label
                    'title'  => $title,  // <a> link title (optional)
                    'span'   => $span,   // description (will be displayed after <a> link)
                    'active' => $active, // status (true/false)
                    'class'  => ''      // additional <li> class (optional)
                ]);

                # --BEHAVIOR-- publicSimpleMenuItem
                $core->callBehavior('publicSimpleMenuItem', $i, $item);

                $ret .= '<li class="nav-item li' . ($i + 1) .
                    ($item['active'] ? ' active' : '') .
                    ($i == 0 ? ' li-first' : '') .
                    ($i == count($menu) - 1 ? ' li-last' : '') .
                    ($item['class'] ? ' ' . $item['class'] : '') .
                    '">' .
                    '<a class="nav-link js-scroll-trigger" href="' . $href . '"' .
                    (!empty($item['title']) ? ' title="'. $label . ' - ' . $item['title'] . '"' : '') .
                    (($targetBlank) ? ' target="_blank" rel="noopener noreferrer"' : '') . '>' .
                    '<span class="simple-menu-label">' . $item['label'] . '</span>' .
                    $item['span'] . '</a>' .
                    '</li>';
            }
            // Final rendering
            if ($ret) {
                $ret = '<ul ' . ($id ? 'id="' . $id . '"' : '') . ' class="simple-menu' . ($class ? ' ' . $class : '') . '">' . "\n" . $ret . "\n" . '</ul>';
            }
        }

        return $ret;
    }
}
