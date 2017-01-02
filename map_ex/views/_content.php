<?php
/*
MapEx plugin for Widgetkit 2.
Author: Ramil Valitov
E-mail: ramilvalitov@gmail.com
Web: http://www.valitov.me/
Git: https://github.com/rvalitov/widgetkit-map-ex
*/

require_once(__DIR__.'/WidgetkitExPlugin.php');
use WidgetkitEx\MapEx\WidgetkitExPlugin;
$cssprefix=WidgetkitExPlugin::getCSSPrefix($app);

// Media Width
$media_width = '{wk}-width-' . $settings['media_breakpoint'] . '-' . $settings['media_width'];
$media_width = str_replace('{wk}', $cssprefix, $media_width);

switch ($settings['media_width']) {
    case '1-5':
        $content_width = '4-5';
        break;
    case '1-4':
        $content_width = '3-4';
        break;
    case '3-10':
        $content_width = '7-10';
        break;
    case '1-3':
        $content_width = '2-3';
        break;
    case '2-5':
        $content_width = '3-5';
        break;
    case '1-2':
        $content_width = '1-2';
        break;
}

$content_width = '{wk}-width-' . $settings['media_breakpoint'] . '-' . $content_width;
$content_width = str_replace('{wk}', $cssprefix, $content_width);

// Title Size
switch ($settings['title_size']) {
    case 'large':
        $title_size = '{wk}-heading-large {wk}-margin-top-remove';
        break;
    default:
        $title_size = '{wk}-' . $settings['title_size'] . ' {wk}-margin-top-remove';
}
$title_size = str_replace('{wk}', $cssprefix, $title_size);

// Link Style
switch ($settings['link_style']) {
    case 'button':
        $link_style = '{wk}-button';
        break;
    case 'primary':
        $link_style = '{wk}-button {wk}-button-primary';
        break;
    case 'button-large':
        $link_style = '{wk}-button {wk}-button-large';
        break;
    case 'primary-large':
        $link_style = '{wk}-button {wk}-button-large {wk}-button-primary';
        break;
    case 'button-link':
        $link_style = '{wk}-button {wk}-button-link';
        break;
    default:
        $link_style = '';
}
$link_style = str_replace('{wk}', $cssprefix, $link_style);

// Media Border
$border = ($settings['media_border'] != 'none') ? '{wk}-border-' . $settings['media_border'] : '';
$border = str_replace('{wk}', $cssprefix, $border);

// Link Target
$link_target = ($settings['link_target']) ? ' target="_blank"' : '';
$link_target = str_replace('{wk}', $cssprefix, $link_target);

// Social Buttons
$socials = '';
if ($settings['social_buttons']) {
    $socials .= $item['twitter'] ? '<div><a class="{wk}-icon-button {wk}-icon-twitter" href="'. $item->escape('twitter') .'"></a></div>': '';
    $socials .= $item['facebook'] ? '<div><a class="{wk}-icon-button {wk}-icon-facebook" href="'. $item->escape('facebook') .'"></a></div>': '';
    $socials .= $item['google-plus'] ? '<div><a class="{wk}-icon-button {wk}-icon-google-plus" href="'. $item->escape('google-plus') .'"></a></div>': '';
    $socials .= $item['email'] ? '<div><a class="{wk}-icon-button {wk}-icon-envelope-o" href="mailto:'. $item->escape('email') .'"></a></div>': '';
}
$socials = str_replace('{wk}', $cssprefix, $socials);

// Second Image as Overlay
$media2 = '';
if ($settings['media_overlay'] == 'image') {
    foreach ($item as $field) {
        if ($field != 'media' && $item->type($field) == 'image') {
            $media2 = $field;
            break;
        }
    }
}

// Media Type
$attrs  = array('class' => '');
$width  = $item['media.width'];
$height = $item['media.height'];

if ($item->type('media') == 'image') {
    $attrs['alt'] = strip_tags($item['title']);

    $attrs['class'] .= '{wk}-responsive-width ' . $border;
    $attrs['class'] .= ($settings['media_animation'] != 'none'  && !$media2) ? ' {wk}-overlay-' . $settings['media_animation'] : '';

    $width  = ($settings['image_width'] != 'auto') ? $settings['image_width'] : '';
    $height = ($settings['image_height'] != 'auto') ? $settings['image_height'] : '';
}

if ($item->type('media') == 'video') {
    $attrs['class'] = '{wk}-responsive-width';
    $attrs['controls'] = true;
}

if ($item->type('media') == 'iframe') {
    $attrs['class'] = '{wk}-responsive-width';
}

$attrs['width']  = ($width) ? $width : '';
$attrs['height'] = ($height) ? $height : '';
$attrs['class'] = str_replace('{wk}', $cssprefix, $attrs['class']);

if (($item->type('media') == 'image') && ($settings['image_width'] != 'auto' || $settings['image_height'] != 'auto')) {
    $media = $item->thumbnail('media', $width, $height, $attrs);
} else {
    $media = $item->media('media', $attrs);
}

// Second Image as Overlay
if ($media2) {

    $attrs['class'] .= ' {wk}-overlay-panel {wk}-overlay-image';
    $attrs['class'] .= ($settings['media_animation'] != 'none') ? ' {wk}-overlay-' . $settings['media_animation'] : '';
	$attrs['class'] = str_replace('{wk}', $cssprefix, $attrs['class']);

    $media2 = $item->thumbnail($media2, $width, $height, $attrs);
}

// Link and Overlay
if ($item['link'] && ($settings['media_overlay'] == 'link' || $settings['media_overlay'] == 'icon' || $settings['media_overlay'] == 'image')) {

    $media = '<div class="{wk}-overlay {wk}-overlay-hover ' . $border . '">' . $media;

    if ($media2) {
        $media .= $media2;
    }

    if ($settings['media_overlay'] == 'icon') {
        $media .= '<div class="{wk}-overlay-panel {wk}-overlay-background {wk}-overlay-icon {wk}-overlay-' . $settings['overlay_animation'] . '"></div>';
    }

    $media .= '<a class="{wk}-position-cover" href="' . $item->escape('link') . '"' . $link_target . '></a>';
    $media .= '</div>';
}

if ($socials && $settings['media_overlay'] == 'social-buttons') {
    $media  = '<div class="{wk}-overlay {wk}-overlay-hover ' . $border . '">' . $media;
    $media .= '<div class="{wk}-overlay-panel {wk}-overlay-background {wk}-overlay-' . $settings['overlay_animation'] . ' {wk}-flex {wk}-flex-center {wk}-flex-middle {wk}-text-center"><div>';
    $media .= '<div class="{wk}-grid {wk}-grid-small" data-{wk}-grid-margin>' . $socials . '</div>';
    $media .= '</div></div>';
    $media .= '</div>';
}
$media = str_replace('{wk}', $cssprefix, $media);
?>

<div class="<?php echo $cssprefix?>-text-<?php echo $settings['text_align']; ?>">

    <?php if ($item['media'] && $settings['media'] && $settings['media_align'] == 'top') : ?>
    <div class="<?php echo $cssprefix?>-margin <?php echo $cssprefix?>-text-center"><?php echo $media; ?></div>
    <?php endif; ?>

    <?php if ($item['media'] && $settings['media'] && in_array($settings['media_align'], array('left', 'right'))) : ?>
    <div class="<?php echo $cssprefix?>-grid" data-<?php echo $cssprefix?>-grid-margin>
        <div class="<?php echo $media_width ?><?php if ($settings['media_align'] == 'right') echo ' '. $cssprefix. '-float-right .' .$cssprefix. '-flex-order-last-' . $settings['media_breakpoint'] ?>">
            <?php echo $media; ?>
        </div>
        <div class="<?php echo $content_width ?>">
            <div class="<?php echo $cssprefix?>-panel">
    <?php endif; ?>

    <?php if ($item['title'] && $settings['title']) : ?>
    <h3 class="<?php echo $title_size; ?>"><?php echo $item['title']; ?></h3>
    <?php endif; ?>

    <?php if ($item['media'] && $settings['media'] && $settings['media_align'] == 'bottom') : ?>
    <div class="<?php echo $cssprefix?>-margin <?php echo $cssprefix?>-text-center"><?php echo $media; ?></div>
    <?php endif; ?>

    <?php if ($item['content'] && $settings['content']) : ?>
    <div class="<?php echo $cssprefix?>-margin"><?php echo $item['content']; ?></div>
    <?php endif; ?>

    <?php if ($socials && ($settings['media_overlay'] != 'social-buttons')) : ?>
    <div class="<?php echo $cssprefix?>-grid <?php echo $cssprefix?>-grid-small <?php echo $cssprefix?>-flex-<?php echo $settings['text_align']; ?>" data-<?php echo $cssprefix?>-grid-margin><?php echo $socials; ?></div>
    <?php endif; ?>

    <?php if ($item['link'] && $settings['link']) : ?>
    <p><a<?php if($link_style) echo ' class="' . $link_style . '"'; ?> href="<?php echo $item->escape('link'); ?>"<?php echo $link_target; ?>><?php echo $app['translator']->trans($settings['link_text']); ?></a></p>
    <?php endif; ?>

    <?php if ($item['media'] && $settings['media'] && in_array($settings['media_align'], array('left', 'right'))) : ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>