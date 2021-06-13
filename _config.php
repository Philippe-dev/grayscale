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


if (!defined('DC_CONTEXT_ADMIN')) {
    return;
}

l10n::set(dirname(__FILE__) . '/locales/' . $_lang . '/admin');

$standalone_config = (boolean) $core->themes->moduleInfo($core->blog->settings->system->theme, 'standalone_config');
$theme_url = $GLOBALS['core']->blog->settings->system->themes_url."/".$GLOBALS['core']->blog->settings->system->theme;

# default or random image background
$sr = $GLOBALS['core']->blog->settings->themes->get($GLOBALS['core']->blog->settings->system->theme . '_random');
$sr = @unserialize($sr);

if (!is_array($sr)) {
    $sr = [];
}

if (!isset($sr['default-image'])) {
    $sr['default-image'] = 1;
}

# default or user defined images
$si = $GLOBALS['core']->blog->settings->themes->get($GLOBALS['core']->blog->settings->system->theme . '_images');
$si = @unserialize($si);

if (!isset($si['default-image-url'])) {
    $si['default-image-url'] = $theme_url.'/img/intro-bg.jpg';
}
$parts = pathinfo($si['default-image-url']);
$default_image_s = $parts['dirname'].'/.'.$parts['filename'].'_s.'.str_ireplace('jpeg','jpg', $parts['extension']);


for ($i = 0; $i < 6; $i++) {
    if (!isset($si['random-image-'.$i.'-url'])) {
        $si['random-image-'.$i.'-url'] = $theme_url.'/img/bg-intro-'. $i .'.jpg';
    }
    $parts = pathinfo($si['random-image-'.$i.'-url']);
    ${'random-image-' . $i . '-small-url'} = $parts['dirname'].'/.'.$parts['filename'].'_s.'.str_ireplace('jpeg','jpg', $parts['extension']);
}

// Load contextual help
if (file_exists(dirname(__FILE__) . '/locales/' . $_lang . '/resources.php')) {
    require dirname(__FILE__) . '/locales/' . $_lang . '/resources.php';
}

if (!empty($_POST)) {
    try {
        $sr['default-image'] = $_POST['default-image'];

        
        if (!empty($_POST['default-image-url'])) {
            $si['default-image-url'] = $_POST['default-image-url'];
        } else {
            $si['default-image-url'] = $theme_url.'/img/intro-bg.jpg';
        }

        $parts = pathinfo($si['default-image-url']);
        $default_image_s = $parts['dirname'].'/.'.$parts['filename'].'_s.'.str_ireplace('jpeg','jpg', $parts['extension']);

        for ($i = 0; $i < 6; $i++) {
            if (!empty($_POST['random-image-'.$i.'-url'])) {
                $si['random-image-'.$i.'-url'] = $_POST['random-image-'.$i.'-url'];
            } else {
                $si['random-image-'.$i.'-url'] = $theme_url.'/img/bg-intro-'. $i .'.jpg';
            }
            $parts = pathinfo($si['random-image-'.$i.'-url']);
            ${'random-image-' . $i . '-small-url'} = $parts['dirname'].'/.'.$parts['filename'].'_s.'.str_ireplace('jpeg','jpg', $parts['extension']);
        }
        $core->blog->settings->addNamespace('themes');
        $core->blog->settings->themes->put($core->blog->settings->system->theme . '_random', serialize($sr));
        $core->blog->settings->themes->put($core->blog->settings->system->theme . '_images', serialize($si));

        // Blog refresh
        $core->blog->triggerBlog();

        // Template cache reset
        $core->emptyTemplatesCache();

        dcPage::success(__('Theme configuration upgraded.'), true, true);
    } catch (Exception $e) {
        $core->error->add($e->getMessage());
    }
}

// Legacy mode
if (!$standalone_config) {
    echo '</form>';
}
    echo '<form id="theme_config" action="' . $core->adminurl->get('admin.blog.theme', ['conf' => '1']) .
    '" method="post" enctype="multipart/form-data">';
    
    echo '<h3>' . __('Behavior') . '</h3>';

    echo '<h4 class="pretty-title">' . __('Main background image') . '</h4>';

    echo '<p><label class="classic" for="default-image-1">'.
    form::radio(array('default-image','default-image-1'), true, $sr['default-image']).
    __('default image').'</label></p>'.
    '<p><label class="classic" for="default-image-2">'.
    form::radio(array('default-image','default-image-2'), false, !$sr['default-image']).
    __('random image').'</label></p>';

    echo '<h3>' . __('Images') . '</h3>';

    echo '<h4 class="pretty-title">' . __('Default image') . '</h4>';

    echo '<div class="box theme">';

    echo '<p> ' .
    '<img alt="' . __('Image URL:') . ' '. $default_image_s .'" src="'. $default_image_s .'" />' .
    '</p>';

    echo '<p><button type="button" id="default-image-selector">' . __('Change') . '</button>' .
    '<button type="button" id="default-image-selector-reset">' . __('Reset') . '</button>' .
    '</p>' ;

    echo '<p>'. form::field('default-image-url', 30, 255, $si['default-image-url']) .'</p>';

    echo '</div>';

    echo '<h4 class="pretty-title">' . __('Random images') . '</h4>';

    for ($i = 0; $i < 6; $i++) {
        echo '<div class="box theme">';

        echo '<p><img alt="' . __('Image URL:') . ' " src="'. ${'random-image-' . $i . '-small-url'} .'" /></p>';

        echo '<p><button type="button" id="random-image-'.$i.'-selector">' . __('Change') . '</button>' .
        '<button type="button" id="random-image-'.$i.'-selector-reset">' . __('Reset') . '</button>' .'</p>' ;

        echo '<p>'. form::field('random-image-'.$i.'-url', 30, 255, $si['random-image-'.$i.'-url']) .'</p>';

        echo '</div>';
    }

    echo '<h3>' . __('Test') . '</h3>';


    echo '<p><label for="default-image" class="classic">' . __('Image URL:') . '</label> ' .
    form::field('default-image', 30, 255, $default_image_s) .
    '</p>' ;

    echo '<p class="clear"><input type="submit" value="' . __('Save') . '" />' . $core->formNonce() . '</p>';
    echo '</form>';


dcPage::helpBlock('grayscale');

// Legacy mode
if (!$standalone_config) {
    echo '<form style="display:none">';
}
