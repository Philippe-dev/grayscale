<?php
/**
 * @brief Grayscale, a theme for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Themes
 *
 * @author Start Bootstrap and Philippe aka amalgame
 *
 * @copyright Philippe Hénaff philippe@dissitou.org
 * @copyright GPL-2.0
 */

namespace Dotclear\Theme\Grayscale;

use ArrayObject;
use dcCore;
use dcNsProcess;
use l10n;
use http;
use html;

class Frontend extends dcNsProcess
{
    public static function init(): bool
    {
        self::$init = defined('DC_RC_PATH');

        return self::$init;
    }

    public static function process(): bool
    {
        if (!self::$init) {
            return false;
        }

        l10n::set(__DIR__ . '/../locales/' . dcCore::app()->lang . '/main');

        # Templates
        dcCore::app()->addBehavior('publicHeadContent', [self::class, 'publicHeadContent']);
        dcCore::app()->addBehavior('publicFooterContent', [self::class, 'publicFooterContent']);
        dcCore::app()->tpl->addValue('grayscaleSimpleMenu', [self::class, 'grayscaleSimpleMenu']);
        dcCore::app()->tpl->addValue('grayscaleSocialLinks', [self::class, 'grayscaleSocialLinks']);

        return true;
    }

    public static function publicHeadContent()
    {
        # Settings
        if (preg_match('#^http(s)?://#', dcCore::app()->blog->settings->system->themes_url)) {
            $theme_url = http::concatURL(dcCore::app()->blog->settings->system->themes_url, '/' . dcCore::app()->blog->settings->system->theme);
        } else {
            $theme_url = http::concatURL(dcCore::app()->blog->url, dcCore::app()->blog->settings->system->themes_url . '/' . dcCore::app()->blog->settings->system->theme);
        }

        $sb = dcCore::app()->blog->settings->themes->get(dcCore::app()->blog->settings->system->theme . '_behavior');
        $sb = $sb ? (unserialize($sb) ?: []) : [];

        if (!is_array($sb)) {
            $sb = [];
        }

        if (!isset($sb['default-image'])) {
            $sb['default-image'] = 1;
        }

        if (!isset($sb['use-featuredMedia'])) {
            $sb['use-featuredMedia'] = 0;
        }

        $si = dcCore::app()->blog->settings->themes->get(dcCore::app()->blog->settings->system->theme . '_images');
        $si = $si ? (unserialize($si) ?: []) : [];

        if (!is_array($si)) {
            $si = [];
        }

        if (!isset($si['default-image-url'])) {
            $si['default-image-url'] = $theme_url . '/img/intro-bg.jpg';
        }

        if (!isset($si['default-image-tb-url'])) {
            $si['default-image-tb-url'] = $theme_url . '/img/.intro-bg_s.jpg';
        }

        for ($i = 0; $i < 6; $i++) {
            if (!isset($si['random-image-' . $i . '-url'])) {
                $si['random-image-' . $i . '-url'] = $theme_url . '/img/bg-intro-' . $i . '.jpg';
            }
            if (!isset($si['random-image-' . $i . '-tb-url'])) {
                $si['random-image-' . $i . '-tb-url'] = $theme_url . '/img/.bg-intro-' . $i . '_s.jpg';
            }
        }

        # check if post has featured media
        if (dcCore::app()->ctx->posts !== null && dcCore::app()->plugins->moduleExists('featuredMedia')) {
            dcCore::app()->ctx->featured = new \ArrayObject(dcCore::app()->media->getPostMedia(dcCore::app()->ctx->posts->post_id, null, 'featured'));
            foreach (dcCore::app()->ctx->featured as $featured_i => $featured_f) {
                $GLOBALS['featured_i'] = $featured_i;
                $GLOBALS['featured_f'] = $featured_f;
            }
            if (isset($featured_f->file_url)) {
                $featuredImageUrl = $featured_f->file_url;
            }
        }

        $rs = '<style>';
        if ($sb['use-featuredMedia'] && !empty($featuredImageUrl)) {
            $rs .= '.intro { background-image: url("' . $featuredImageUrl . '"); }';
        } else {
            if ($sb['default-image']) {
                $rs .= '.intro { background-image: url("' . $si['default-image-url'] . '"); }';
            } else {
                for ($i = 0; $i < 6; $i++) {
                    $rs .= '.intro.round' . $i . ' {' .
                    'background: #555 url(' . $si['random-image-' . $i . '-url'] . ');' .
                    'background-size: cover;' .
                    'background-position: center;' .
                '}';
                }
                $rs .= '.intro { background-image: none; }';
            }
        }
        $rs .= '</style>';
        echo $rs;
    }
    public static function publicFooterContent()
    {
        # Settings
        $sb = dcCore::app()->blog->settings->themes->get(dcCore::app()->blog->settings->system->theme . '_behavior');
        $sb = $sb ? (unserialize($sb) ?: []) : [];

        if (!is_array($sb)) {
            $sb = [];
        }

        if (!isset($sb['default-image'])) {
            $sb['default-image'] = 1;
        }

        if ($sb['default-image'] == 1) {
            return;
        }
        echo
        '<script>' . "\n" .
            "$(document).ready(function() {
            var round = parseInt(Math.random()*6);
                $('header.intro').addClass('round'+round);
            });" .
        "</script>\n";
    }

    public static function grayscaleSimpleMenu(ArrayObject $attr): string
    {
        if (!(bool) dcCore::app()->blog->settings->system->simpleMenu_active) {
            return '';
        }

        $class       = isset($attr['class']) ? trim($attr['class']) : '';
        $id          = isset($attr['id']) ? trim($attr['id']) : '';
        $description = isset($attr['description']) ? trim($attr['description']) : '';

        if (!preg_match('#^(title|span|both|none)$#', $description)) {
            $description = '';
        }

        return '<?php echo ' . self::class . '::displayMenu(' .
        "'" . addslashes($class) . "'," .
        "'" . addslashes($id) . "'," .
        "'" . addslashes($description) . "'" .
            '); ?>';
    }

    public static function displayMenu(string $class = '', string $id = '', string $description = ''): string
    {
        $ret = '';

        if (!(bool) dcCore::app()->blog->settings->system->simpleMenu_active) {
            return $ret;
        }

        $menu = dcCore::app()->blog->settings->system->simpleMenu;
        if (is_array($menu)) {
            // Current relative URL
            $url     = $_SERVER['REQUEST_URI'];
            $abs_url = http::getHost() . $url;

            // Home recognition var
            $home_url       = html::stripHostURL(dcCore::app()->blog->url);
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
                if (($url == $href) || ($abs_url == $href) || ($_SERVER['URL_REQUEST_PART'] == $href) || (($href_part != '') && ($_SERVER['URL_REQUEST_PART'] == $href_part)) || (($_SERVER['URL_REQUEST_PART'] == '') && (($href == $home_url) || ($href == $home_directory)))) {
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
                    'class'  => '',      // additional <li> class (optional)
                ]);

                # --BEHAVIOR-- publicSimpleMenuItem
                dcCore::app()->callBehavior('publicSimpleMenuItem', $i, $item);

                $ret .= '<li class="nav-item li' . ($i + 1) .
                    ($item['active'] ? ' active' : '') .
                    ($i == 0 ? ' li-first' : '') .
                    ($i == count($menu) - 1 ? ' li-last' : '') .
                    ($item['class'] ? ' ' . $item['class'] : '') .
                    '">' .
                    '<a class="nav-link js-scroll-trigger" href="' . $href . '"' .
                    (!empty($item['title']) ? ' title="' . $label . ' - ' . $item['title'] . '"' : '') .
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

     public static function grayscaleSocialLinks($attr)
     {
         return '<?php echo ' . self::class . '::grayscaleSocialLinksHelper(); ?>';
     }
    public static function grayscaleSocialLinksHelper()
    {
        # Social media links
        $res = '';

        $style = dcCore::app()->blog->settings->themes->get(dcCore::app()->blog->settings->system->theme . '_stickers');

        if ($style === null) {
            $default = true;
        } else {
            $style = $style ? (unserialize($style) ?: []) : [];

            $style = array_filter($style, self::class . '::cleanSocialLinks');

            $count = 0;
            foreach ($style as $sticker) {
                $res .= self::setSocialLink($count, ($count == count($style)), $sticker['label'], $sticker['url'], $sticker['image']);
                $count++;
            }
        }

        if ($res != '') {
            return $res;
        }
    }
    protected static function setSocialLink($position, $last, $label, $url, $image)
    {
        return
            '<a class="social-icon" title="' . $label . '" href="' . $url . '"><span class="sr-only">' . $label . '</span>' .
            '<i class="' . $image . '"></i>' .
            '</a>' . "\n";
    }

    protected static function cleanSocialLinks($style)
    {
        if (is_array($style)) {
            if (isset($style['label']) && isset($style['url']) && isset($style['image'])) {
                if ($style['label'] != null && $style['url'] != null && $style['image'] != null) {
                    return true;
                }
            }
        }

        return false;
    }
}