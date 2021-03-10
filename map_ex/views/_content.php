<?php
/*
MapEx plugin for Widgetkit 2.
Author: Ramil Valitov
E-mail: ramilvalitov@gmail.com
Web: http://www.valitov.me/
Git: https://github.com/rvalitov/widgetkit-map-ex
*/

// Media Width
$media_width = '{wk}-width-' . $settings['media_width'] . '@' . $settings['media_breakpoint'];

switch ($settings['media_width']) {
    case '1-5':
        $content_width = '4-5';
        break;
    case '1-4':
        $content_width = '3-4';
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

$content_width = '{wk}-width-' . $content_width . '@' . $settings['media_breakpoint'];

// Title Size
switch ($settings['title_size']) {
    case 'medium':
        $title_size = '{wk}-heading-medium {wk}-margin-remove-top';
        break;
    case 'large':
        $title_size = '{wk}-heading-large {wk}-margin-remove-top';
        break;
    default:
        $title_size = '{wk}-' . $settings['title_size'] . ' {wk}-margin-remove-top';
}

// Link Style
switch ($settings['link_style']) {
    case 'button':
        $link_style = '{wk}-button {wk}-button-default';
        break;
    case 'primary':
        $link_style = '{wk}-button {wk}-button-primary';
        break;
    case 'button-large':
        $link_style = '{wk}-button {wk}-button-large {wk}-button-default';
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

// Media Border
$border = ($settings['media_border'] != 'none') ? '{wk}-border-' . $settings['media_border'] : '';

// Link Target
$link_target = ($settings['link_target']) ? ' target="_blank"' : '';

// Social Buttons
$socials = '';
if ($settings['social_buttons']) {
    $socials .= $item['twitter'] ? '<div><a class="{wk}-icon-button" {wk}-icon="twitter" href="'. $item->escape('twitter') .'"></a></div>': '';
    $socials .= $item['facebook'] ? '<div><a class="{wk}-icon-button" {wk}-icon="facebook" href="'. $item->escape('facebook') .'"></a></div>': '';
    $socials .= $item['email'] ? '<div><a class="{wk}-icon-button" {wk}-icon="mail" href="mailto:'. $item->escape('email') .'"></a></div>': '';
}

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
    $attrs['class'] .= ($settings['media_animation'] != 'none'  && !$media2) ? ' {wk}-transition-' . $settings['media_animation']. ' {wk}-transition-opaque' : '';

    $width  = ($settings['image_width'] != 'auto') ? $settings['image_width'] : '';
    $height = ($settings['image_height'] != 'auto') ? $settings['image_height'] : '';
}

if ($item->type('media') == 'video') {
    $attrs['class'] = '{wk}-responsive-width';
    $attrs['controls'] = true;
}

if ($item->type('media') == 'iframe') {
    $attrs['{wk}-responsive'] = true;
}

$attrs['width']  = ($width) ? $width : '';
$attrs['height'] = ($height) ? $height : '';

if (($item->type('media') == 'image') && ($settings['image_width'] != 'auto' || $settings['image_height'] != 'auto')) {
    $media = $item->thumbnail('media', $width, $height, $attrs);
} else {
    $media = $item->media('media', $attrs);
}

// Second Image as Overlay
if ($media2) {

    $attrs['class'] .= ' {wk}-position-cover';
    $attrs['class'] .= ($settings['media_animation'] != 'none') ? ' {wk}-transition-' . $settings['media_animation'] : '{wk}-transition-fade';

    $media2 = $item->thumbnail($media2, $width, $height, $attrs);
}

// Link and Overlay
if ($item['link'] && ($settings['media_overlay'] == 'link' || $settings['media_overlay'] == 'icon' || $settings['media_overlay'] == 'image')) {

    $media = '<div class="{wk}-inline-clip {wk}-transition-toggle ' . $border . '">' . $media;

    if ($media2) {
        $media .= $media2;
    }

    if ($settings['media_overlay'] == 'icon') {
        $media .= '<div class="{wk}-overlay-primary {wk}-position-cover {wk}-transition-fade">';
        $media .= '<div class="{wk}-position-center"><span class="' . ($settings['overlay_animation'] != 'fade' ? '{wk}-transition-opaque {wk}-transition-' . $settings['overlay_animation'] : '') . '" {wk}-overlay-icon></span></div>';
        $media .= '</div>';
    }

    $media .= '<a class="{wk}-position-cover" href="' . $item->escape('link') . '"' . $link_target . '></a>';
    $media .= '</div>';
}

if ($socials && $settings['media_overlay'] == 'social-buttons') {
    $media  = '<div class="{wk}-inline-clip {wk}-transition-toggle' . $border . '">' . $media;
    $media .= '<div class="{wk}-overlay {wk}-overlay-primary {wk}-position-cover {wk}-transition-' . $settings['overlay_animation'] . ' {wk}-flex {wk}-flex-center {wk}-flex-middle {wk}-text-center"><div>';
    $media .= '<div class="{wk}-grid {wk}-grid-small" {wk}-grid>' . $socials . '</div>';
    $media .= '</div></div>';
    $media .= '</div>';
}
?>

<div class="{wk}-text-<?= $settings['text_align'] ?>">

    <?php if ($item['media'] && $settings['media'] && $settings['media_align'] == 'top') : ?>
    <div class="{wk}-margin {wk}-text-center"><?= $media ?></div>
    <?php endif; ?>

    <?php if ($item['media'] && $settings['media'] && in_array($settings['media_align'], array('left', 'right'))) : ?>
    <div class="{wk}-grid" {wk}-grid>
        <div class="<?= $media_width ?><?php if ($settings['media_align'] == 'right') echo '  {wk}-flex-last@' . $settings['media_breakpoint'] ?>">
            <?= $media ?>
        </div>
        <div class="<?= $content_width ?>">
            <div class="{wk}-panel">
    <?php endif; ?>

    <?php if ($item['title'] && $settings['title']) : ?>
    <<?= $settings['title_element'] ?> class="<?= $title_size ?>"><?= $item['title'] ?></<?= $settings['title_element'] ?>>
    <?php endif; ?>

    <?php if ($item['media'] && $settings['media'] && $settings['media_align'] == 'bottom') : ?>
    <div class="{wk}-margin {wk}-text-center"><?= $media ?></div>
    <?php endif; ?>

    <?php if ($item['content'] && $settings['content']) : ?>
    <div class="{wk}-margin"><?= $item['content'] ?></div>
    <?php endif; ?>

    <?php if ($socials && ($settings['media_overlay'] != 'social-buttons')) : ?>
    <div class="{wk}-grid {wk}-grid-small {wk}-flex-<?= $settings['text_align'] ?>" {wk}-grid><?= $socials ?></div>
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
