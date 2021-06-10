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

if (!isset($s['default-image'])) {
    $s['default-image'] = 1;
}

if (!isset($s['user-default-image'])) {
    $s['user-default-image'] = $GLOBALS['core']->blog->settings->system->themes_url."/".$GLOBALS['core']->blog->settings->system->theme."/img/intro-bg.jpg";;
}


// Load contextual help
if (file_exists(dirname(__FILE__) . '/locales/' . $_lang . '/resources.php')) {
    require dirname(__FILE__) . '/locales/' . $_lang . '/resources.php';
}

if (!empty($_POST)) {
    try {
        # HTML
        $s['default-image'] = $_POST['default-image'];
        $s['default-image'] = $_POST['default-image'];
        
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

echo '<p><label class="classic" for="default-image-1">'.
    form::radio(array('default-image','default-image-1'), true, $s['default-image']).
    __('default image').'</label></p>'.
    '<p><label class="classic" for="default-image-2">'.
    form::radio(array('default-image','default-image-2'), false, !$s['default-image']).
    __('random image').'</label></p>';

echo '<h4 class="pretty-title">' . __('Default image') . '</h4>';

echo '<p> ' .
    '<img alt="' . __('Image URL:') . ' " src="'. $s['user-default-image'] .'" width="400" />' .
    '</p>';

echo '<p><label for="user-default-image" class="classic">' . __('Image URL:') . '</label> ' .
    form::field('user-default-image', 30, 255, html::escapeHTML($s['user-default-image'])) .
    '</p>';

echo 
    '<p class="s-featuredmedia"><a href="' . $core->adminurl->get('admin.media.item', ['popup' => 1, 'select' => 1, 'file' => NULL]) . '">' .
    __('Add a featured media for this entry') . '</a></p>';

echo '<p class="clear"><input type="submit" value="' . __('Save') . '" />' . $core->formNonce() . '</p>';
echo '</form>';


dcPage::helpBlock('grayscale');

// Legacy mode
if (!$standalone_config) {
    echo '<form style="display:none">';
}
