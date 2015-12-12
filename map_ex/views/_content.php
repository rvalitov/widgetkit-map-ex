<?php

// Media Width
$media_width = 'uk-width-' . $settings['media_breakpoint'] . '-' . $settings['media_width'];

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

$content_width = 'uk-width-' . $settings['media_breakpoint'] . '-' . $content_width;

// Title Size
switch ($settings['title_size']) {
    case 'large':
        $title_size = 'uk-heading-large uk-margin-top-remove';
        break;
    default:
        $title_size = 'uk-' . $settings['title_size'] . ' uk-margin-top-remove';
}

// Link Style
switch ($settings['link_style']) {
    case 'button':
        $link_style = 'uk-button';
        break;
    case 'primary':
        $link_style = 'uk-button uk-button-primary';
        break;
    case 'button-large':
        $link_style = 'uk-button uk-button-large';
        break;
    case 'primary-large':
        $link_style = 'uk-button uk-button-large uk-button-primary';
        break;
    case 'button-link':
        $link_style = 'uk-button uk-button-link';
        break;
    default:
        $link_style = '';
}

// Media Border
$border = ($settings['media_border'] != 'none') ? 'uk-border-' . $settings['media_border'] : '';

// Link Target
$link_target = ($settings['link_target']) ? ' target="_blank"' : '';

// Social Buttons
$socials = '';
if ($settings['social_buttons']) {
    $socials .= $item['twitter'] ? '<div><a class="uk-icon-button uk-icon-twitter" href="'. $item->escape('twitter') .'"></a></div>': '';
    $socials .= $item['facebook'] ? '<div><a class="uk-icon-button uk-icon-facebook" href="'. $item->escape('facebook') .'"></a></div>': '';
    $socials .= $item['google-plus'] ? '<div><a class="uk-icon-button uk-icon-google-plus" href="'. $item->escape('google-plus') .'"></a></div>': '';
    $socials .= $item['email'] ? '<div><a class="uk-icon-button uk-icon-envelope-o" href="mailto:'. $item->escape('email') .'"></a></div>': '';
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

    $attrs['class'] .= 'uk-responsive-width ' . $border;
    $attrs['class'] .= ($settings['media_animation'] != 'none'  && !$media2) ? ' uk-overlay-' . $settings['media_animation'] : '';

    $width  = ($settings['image_width'] != 'auto') ? $settings['image_width'] : '';
    $height = ($settings['image_height'] != 'auto') ? $settings['image_height'] : '';
}

if ($item->type('media') == 'video') {
    $attrs['class'] = 'uk-responsive-width';
    $attrs['controls'] = true;
}

if ($item->type('media') == 'iframe') {
    $attrs['class'] = 'uk-responsive-width';
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

    $attrs['class'] .= ' uk-overlay-panel uk-overlay-image';
    $attrs['class'] .= ($settings['media_animation'] != 'none') ? ' uk-overlay-' . $settings['media_animation'] : '';

    $media2 = $item->thumbnail($media2, $width, $height, $attrs);
}

// Link and Overlay
if ($item['link'] && ($settings['media_overlay'] == 'link' || $settings['media_overlay'] == 'icon' || $settings['media_overlay'] == 'image')) {

    $media = '<div class="uk-overlay uk-overlay-hover ' . $border . '">' . $media;

    if ($media2) {
        $media .= $media2;
    }

    if ($settings['media_overlay'] == 'icon') {
        $media .= '<div class="uk-overlay-panel uk-overlay-background uk-overlay-icon uk-overlay-' . $settings['overlay_animation'] . '"></div>';
    }

    $media .= '<a class="uk-position-cover" href="' . $item->escape('link') . '"' . $link_target . '></a>';
    $media .= '</div>';

}

if ($socials && $settings['media_overlay'] == 'social-buttons') {
    $media  = '<div class="uk-overlay uk-overlay-hover ' . $border . '">' . $media;
    $media .= '<div class="uk-overlay-panel uk-overlay-background uk-overlay-' . $settings['overlay_animation'] . ' uk-flex uk-flex-center uk-flex-middle uk-text-center"><div>';
    $media .= '<div class="uk-grid uk-grid-small" data-uk-grid-margin>' . $socials . '</div>';
    $media .= '</div></div>';
    $media .= '</div>';
}

?>

<div class="uk-text-<?php echo $settings['text_align']; ?>">

    <?php if ($item['media'] && $settings['media'] && $settings['media_align'] == 'top') : ?>
    <div class="uk-margin uk-text-center"><?php echo $media; ?></div>
    <?php endif; ?>

    <?php if ($item['media'] && $settings['media'] && in_array($settings['media_align'], array('left', 'right'))) : ?>
    <div class="uk-grid" data-uk-grid-margin>
        <div class="<?php echo $media_width ?><?php if ($settings['media_align'] == 'right') echo ' uk-float-right uk-flex-order-last-' . $settings['media_breakpoint'] ?>">
            <?php echo $media; ?>
        </div>
        <div class="<?php echo $content_width ?>">
            <div class="uk-panel">
    <?php endif; ?>

    <?php if ($item['title'] && $settings['title']) : ?>
    <h3 class="<?php echo $title_size; ?>"><?php echo $item['title']; ?></h3>
    <?php endif; ?>

    <?php if ($item['media'] && $settings['media'] && $settings['media_align'] == 'bottom') : ?>
    <div class="uk-margin uk-text-center"><?php echo $media; ?></div>
    <?php endif; ?>

    <?php if ($item['content'] && $settings['content']) : ?>
    <div class="uk-margin"><?php echo $item['content']; ?></div>
    <?php endif; ?>

    <?php if ($socials && ($settings['media_overlay'] != 'social-buttons')) : ?>
    <div class="uk-grid uk-grid-small uk-flex-<?php echo $settings['text_align']; ?>" data-uk-grid-margin><?php echo $socials; ?></div>
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