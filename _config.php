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

$s = $GLOBALS['core']->blog->settings->themes->get($GLOBALS['core']->blog->settings->system->theme . '_random');
$s = @unserialize($s);

if (!is_array($s)) {
    $s = [];
}

if (!isset($s['random-image'])) {
    $s['random-image'] = 1;
}


// Load contextual help
if (file_exists(dirname(__FILE__) . '/locales/' . $_lang . '/resources.php')) {
    require dirname(__FILE__) . '/locales/' . $_lang . '/resources.php';
}

if (!empty($_POST)) {
    try {
        # HTML
        $s['random-image'] = $_POST['random-image'];
        
        $core->blog->settings->addNamespace('themes');
        $core->blog->settings->themes->put($core->blog->settings->system->theme . '_random', serialize($s));

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
    
echo '<h4 class="pretty-title">' . __('Main background image') . '</h4>';

echo '<p><label class="classic" for="random-image-1">'.
    form::radio(array('random-image','random-image-1'), true, $s['random-image']).
    __('default image').'</label></p>'.
    '<p><label class="classic" for="random-image-2">'.
    form::radio(array('random-image','random-image-2'), false, !$s['random-image']).
    __('random image').'</label></p>';

echo '<p class="clear"><input type="submit" value="' . __('Save') . '" />' . $core->formNonce() . '</p>';
echo '</form>';


dcPage::helpBlock('grayscale');

// Legacy mode
if (!$standalone_config) {
    echo '<form style="display:none">';
}
