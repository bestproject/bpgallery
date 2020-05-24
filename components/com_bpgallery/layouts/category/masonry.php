<?php

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\FileLayout;
use Joomla\Registry\Registry;

defined('JPATH_BASE') or die;

/**
 * @var array      $displayData              Layout data.
 * @var object     $category                 Category instance.
 * @var Registry   $params                   Parameters to use on this layout.
 * @var array      $items                    Images to display.
 * @var int        $thumbnail_width          Thumbnail width.
 * @var int        $thumbnail_height         Thumbnail height.
 * @var int        $thumbnail_method         Thumbnail generation method.
 * @var bool       $image_lightbox           Should be use lightbox for images.
 * @var bool       $gap                      Gap between images?
 * @var int        $category_masonry_columns Number of columns on desktop view.
 * @var FileLayout $layoutThumbnail          Thumbnail layout file to use.
 */

extract($displayData, EXTR_SKIP);

HTMLHelper::_('jquery.framework');

// Include assets generated by Webpack
if ($params->def('include_component_assets', 1)) {
    BPGalleryHelperLayout::includeEntryPointAssets('masonry');
}
if ($params->def('include_theme_assets', 1)) {
    BPGalleryHelperLayout::includeEntryPointAssets('category-default');
}

// If lightbox is enabled
$container_id = 'com_bpgallery_category_masonry_' . $category->id;
if ($params->def('images_lightbox', 1)) {
    $lightboxLayoutData = [
        'params'         => $params,
        'lightbox_query' => '#' . $container_id
    ];
    (new FileLayout('components.com_bpgallery.layouts.lightbox', JPATH_ROOT))->render($lightboxLayoutData);
}

// Run masonry
$doc = Factory::getDocument();
$doc->addScriptDeclaration("

    // Run Masonry Layout
    jQuery(function($){
        var gallery = $('#$container_id').masonry({});
        
        // Reload Masonry on image loaded event
        gallery.imagesLoaded().progress( function() {
            gallery.masonry('layout');
        });
    });
");

// Responsive styles
$image_width = round(floor(100 / $category_masonry_columns), 2);
$doc->addStyleDeclaration("
    @media screen and (max-width: 360px) {
        #$container_id .item {
            width: 100%
        }
    }
    @media screen and (min-width:361px) and (max-width: 800px) {
        #$container_id .item {
            width: 50%
        }
    }
    @media screen and (min-width:361px) and (min-width: 801px) {
        #$container_id .item {
            width: {$image_width}%
        }
    }
");

?>

<ul class="items<?php echo($gap ? '' : ' nogap') ?>" id="<?php echo $container_id ?>">
    <?php foreach ($items as $i => $item) :
        $imageDisplayData = [
            'item'             => $item,
            'thumbnail_width'  => $thumbnail_width,
            'thumbnail_height' => $thumbnail_height,
            'thumbnail_method' => $thumbnail_method,
            'image_lightbox'   => $image_lightbox,
        ];
        ?>
        <li class="item">
            <?php echo $layoutThumbnail->render($imageDisplayData) ?>
        </li>
    <?php endforeach; ?>
</ul>
