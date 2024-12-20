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
declare(strict_types=1);

namespace Dotclear\Theme\grayscale;

use Dotclear\App;
use Dotclear\Core\Process;
use Dotclear\Core\Backend\Page;
use Dotclear\Core\Backend\Notices;
use Exception;
use form;

class Config extends Process
{
    public static function init(): bool
    {
        // limit to backend permissions
        if (!self::status(My::checkContext(My::CONFIG))) {
            return false;
        }

        My::l10n('admin');

        App::backend()->standalone_config = (bool) App::themes()->moduleInfo(App::blog()->settings->system->theme, 'standalone_config');

        // Load contextual help
        App::themes()->loadModuleL10Nresources(My::id(), App::lang()->getLang());

        # random or default image behavior
        $behavior = App::blog()->settings->themes->get(App::blog()->settings->system->theme . '_behavior');
        $behavior = $behavior ? (unserialize($behavior) ?: []) : [];

        if (!is_array($behavior)) {
            $behavior = [];
        }

        if (!isset($behavior['default-image'])) {
            $behavior['default-image'] = 1;
        }

        # default or user defined images settings
        $images = App::blog()->settings->themes->get(App::blog()->settings->system->theme . '_images');
        $images = $images ? (unserialize($images) ?: []) : [];

        if (!is_array($images)) {
            $images = [];
        }

        if (!isset($images['default-image-url'])) {
            $images['default-image-url'] = My::fileURL('/img/intro-bg.jpg');
        }

        if (!isset($images['default-image-tb-url'])) {
            $images['default-image-tb-url'] = My::fileURL('/img/.intro-bg_s.jpg');
        }

        for ($i = 0; $i < 6; $i++) {
            if (!isset($images['random-image-' . $i . '-url'])) {
                $images['random-image-' . $i . '-url'] = My::fileURL('/img/bg-intro-' . $i . '.jpg');
            }

            if (!isset($images['random-image-' . $i . '-tb-url'])) {
                $images['random-image-' . $i . '-tb-url'] = My::fileURL('/img/.bg-intro-' . $i . '_s.jpg');
            }
        }

        if (!isset($behavior['use-featuredMedia'])) {
            $behavior['use-featuredMedia'] = 0;
        }

        $stickers = App::blog()->settings->themes->get(App::blog()->settings->system->theme . '_stickers');
        $stickers = $stickers ? (unserialize($stickers) ?: []) : [];

        $stickers_full = [];
        // Get all sticker images already used
        if (is_array($stickers)) {
            foreach ($stickers as $v) {
                $stickers_full[] = $v['image'];
            }
        }
        // Get social media images
        $stickers_images = ['fab fa-diaspora', 'fas fa-rss', 'fab fa-linkedin-in', 'fab fa-gitlab', 'fab fa-github', 'fab fa-twitter', 'fab fa-facebook-f',
            'fab fa-instagram', 'fab fa-mastodon', 'fab fa-pinterest', 'fab fa-snapchat', 'fab fa-soundcloud', 'fab fa-youtube', ];
        if (is_array($stickers_images)) {
            foreach ($stickers_images as $v) {
                if (!in_array($v, $stickers_full)) {
                    // image not already used
                    $stickers[] = [
                        'label' => null,
                        'url'   => null,
                        'image' => $v, ];
                }
            }
        }

        App::backend()->behavior = $behavior;
        App::backend()->images   = $images;
        App::backend()->stickers = $stickers;

        App::backend()->conf_tab = $_POST['conf_tab'] ?? 'images';

        return self::status();
    }

    /**
     * Processes the request(s).
     */
    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        if (!empty($_POST)) {
            try {
                // HTML
                if (App::backend()->conf_tab === 'images') {
                    # random or default image behavior
                    $behavior['default-image'] = $_POST['default-image'];

                    # use featured media for posts background images
                    $behavior['use-featuredMedia'] = (int) !empty($_POST['use-featuredMedia']);

                    # default image setting
                    if (!empty($_POST['default-image-url'])) {
                        $images['default-image-url'] = $_POST['default-image-url'];
                    } else {
                        $images['default-image-url'] = My::fileURL('/img/intro-bg.jpg');
                    }

                    # default image thumbnail settings
                    if (!empty($_POST['default-image-tb-url'])) {
                        $images['default-image-tb-url'] = $_POST['default-image-tb-url'];
                    } else {
                        $images['default-image-tb-url'] = My::fileURL('/img/.intro-bg_s.jpg') . '/';
                    }

                    for ($i = 0; $i < 6; $i++) {
                        # random images settings
                        if (!empty($_POST['random-image-' . $i . '-url'])) {
                            $images['random-image-' . $i . '-url'] = $_POST['random-image-' . $i . '-url'];
                        } else {
                            $images['random-image-' . $i . '-url'] = My::fileURL('/img/bg-intro-' . $i . '.jpg');
                        }

                        # random images thumbnail settings
                        if (!empty($_POST['random-image-' . $i . '-tb-url'])) {
                            $images['random-image-' . $i . '-tb-url'] = $_POST['random-image-' . $i . '-tb-url'];
                        } else {
                            $images['random-image-' . $i . '-tb-url'] = My::fileURL('/img/.bg-intro-' . $i . '_s.jpg');
                        }
                    }

                    App::backend()->behavior = $behavior;
                    App::backend()->images   = $images;
                }

                if (App::backend()->conf_tab === 'stickers') {
                    $stickers = [];
                    for ($i = 0; $i < count($_POST['sticker_image']); $i++) {
                        $stickers[] = [
                            'label' => $_POST['sticker_label'][$i],
                            'url'   => $_POST['sticker_url'][$i],
                            'image' => $_POST['sticker_image'][$i],
                        ];
                    }

                    $order = [];
                    if (empty($_POST['ds_order']) && !empty($_POST['order'])) {
                        $order = $_POST['order'];
                        asort($order);
                        $order = array_keys($order);
                    }
                    if (!empty($order)) {
                        $new_stickers = [];
                        foreach ($order as $i => $k) {
                            $new_stickers[] = [
                                'label' => $stickers[$k]['label'],
                                'url'   => $stickers[$k]['url'],
                                'image' => $stickers[$k]['image'],
                            ];
                        }
                        $stickers = $new_stickers;
                    }
                    App::backend()->stickers = $stickers;
                }
                App::blog()->settings->themes->put(App::blog()->settings->system->theme . '_behavior', serialize(App::backend()->behavior));
                App::blog()->settings->themes->put(App::blog()->settings->system->theme . '_images', serialize(App::backend()->images));
                App::blog()->settings->themes->put(App::blog()->settings->system->theme . '_stickers', serialize(App::backend()->stickers));

                // Blog refresh
                App::blog()->triggerBlog();

                // Template cache reset
                App::cache()->emptyTemplatesCache();

                Notices::message(__('Theme configuration upgraded.'), true, true);
            } catch (Exception $e) {
                App::error()->add($e->getMessage());
            }
        }

        return true;
    }

    /**
     * Renders the page.
     */
    public static function render(): void
    {
        if (!self::status()) {
            return;
        }

        if (!App::backend()->standalone_config) {
            echo '</form>';
        }

        echo '<div class="multi-part" id="images" title="' . __('Images') . '">';

        echo '<form id="theme_config" action="' . App::backend()->url()->get('admin.blog.theme', ['conf' => '1']) .
            '" method="post" enctype="multipart/form-data">';

        echo '<div class="fieldset">';

        echo '<h3>' . __('Display options') . '</h3>';

        echo '<p><label class="classic" for="default-image-1">' .
        form::radio(['default-image','default-image-1'], true, App::backend()->behavior['default-image']) .
        __('default image') . '</label></p>' .
        '<p><label class="classic" for="default-image-2">' .
        form::radio(['default-image','default-image-2'], false, !App::backend()->behavior['default-image']) .
        __('random image') . '</label></p>';

        if (App::plugins()->moduleExists('featuredMedia')) {
            echo '<p class="vertical-separator"><label class="classic" for="use-featuredMedia">' .
                form::checkbox('use-featuredMedia', '1', App::backend()->behavior['use-featuredMedia']) .
                __('Use featured media for posts') . '</label></p>';
        }

        echo '</div>';

        echo '<div class="fieldset">';

        echo '<h3>' . __('Default image') . '</h3>';

        echo '<div class="box theme">';

        echo '<p> ' .
        '<img id="default-image-thumb-src" alt="' . __('Thumbnail') . '" src="' . App::backend()->images['default-image-tb-url'] . '" width="240" height="160">' .
        '</p>';

        echo '<p class="grayscale-buttons"><button type="button" id="default-image-selector">' . __('Change') . '</button>' .
        '<button class="delete" type="button" id="default-image-selector-reset">' . __('Reset') . '</button>' .
        '</p>' ;

        echo '<p class="sr-only">' . form::field('default-image-url', 30, 255, App::backend()->images['default-image-url']) . '</p>';
        echo '<p class="sr-only">' . form::field('default-image-tb-url', 30, 255, App::backend()->images['default-image-tb-url']) . '</p>';

        echo '</div>';
        echo '</div>';

        echo '<div class="fieldset">';

        echo '<h3>' . __('Random images') . '</h3>';

        for ($i = 0; $i < 6; $i++) {
            echo '<div class="box theme">';

            echo '<p> ' .
            '<img id="random-image-' . $i . '-thumb-src" alt="' . __('Thumbnail') . '" src="' . App::backend()->images['random-image-' . $i . '-tb-url'] . '" width="240" height="160">' .
            '</p>';

            echo '<p class="grayscale-buttons"><button type="button" id="random-image-' . $i . '-selector">' . __('Change') . '</button>' .
            '<button class="delete" type="button" id="random-image-' . $i . '-selector-reset">' . __('Reset') . '</button>' . '</p>' ;

            echo '<p class="sr-only">' . form::field('random-image-' . $i . '-url', 30, 255, App::backend()->images['random-image-' . $i . '-url']) . '</p>';
            echo '<p class="sr-only">' . form::field('random-image-' . $i . '-tb-url', 30, 255, App::backend()->images['random-image-' . $i . '-tb-url']) . '</p>';

            echo '</div>';
        }

        echo '</div>';
        echo '<p><input type="hidden" name="conf_tab" value="images"></p>';
        echo '<p class="clear"><input type="submit" value="' . __('Save') . '">' . App::nonce()->getFormNonce() . '</p>';
        echo form::hidden(['theme-url'], My::fileURL(''));
        echo form::hidden(['change-button-id'], '');
        echo '</form>';
        echo '</div>'; // Close tab

        echo '<div class="multi-part" id="stickers" title="' . __('Stickers') . '">';
        echo '<form id="theme_config" action="' . App::backend()->url()->get('admin.blog.theme', ['conf' => '1']) .
            '" method="post" enctype="multipart/form-data">';

        echo '<div class="fieldset">';

        echo '<h4 class="pretty-title">' . __('Social links (footer)') . '</h4>';

        echo
        '<div class="table-outer">' .
        '<table class="dragable">' . '<caption class="sr-only">' . __('Social links (footer)') . '</caption>' .
        '<thead>' .
        '<tr>' .
        '<th scope="col">' . '</th>' .
        '<th scope="col">' . __('Image') . '</th>' .
        '<th scope="col">' . __('Label') . '</th>' .
        '<th scope="col">' . __('URL') . '</th>' .
            '</tr>' .
            '</thead>' .
            '<tbody id="stickerslist">';
        $count = 0;
        foreach (App::backend()->stickers as $i => $v) {
            $count++;
            echo
            '<tr class="line" id="l_' . $i . '">' .
            '<td class="handle">' . form::number(['order[' . $i . ']'], [
                'min'     => 0,
                'max'     => count(App::backend()->stickers),
                'default' => $count,
                'class'   => 'position',
            ]) .
            form::hidden(['dynorder[]', 'dynorder-' . $i], $i) . '</td>' .
            '<td class="linkimg">' . form::hidden(['sticker_image[]'], $v['image']) . '<i class="' . $v['image'] . '" title="' . $v['label'] . '"></i> ' . '</td>' .
            '<td scope="row">' . form::field(['sticker_label[]', 'dsl-' . $i], 20, 255, $v['label']) . '</td>' .
            '<td>' . form::field(['sticker_url[]', 'dsu-' . $i], 40, 255, $v['url']) . '</td>' .
                '</tr>';
        }
        echo
            '</tbody>' .
            '</table></div>';
        echo '</div>';
        echo '<p><input type="hidden" name="conf_tab" value="stickers"></p>';
        echo '<p class="clear">' . form::hidden('ds_order', '') . '<input type="submit" value="' . __('Save') . '">' . App::nonce()->getFormNonce() . '</p>';
        echo '</form>';

        echo '</div>'; // Close tab

        Page::helpBlock('grayscale');

        // Legacy mode
        if (!App::backend()->standalone_config) {
            echo '<form style="display:none">';
        }
    }
}
