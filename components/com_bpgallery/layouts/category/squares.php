<?php
/**
 * @package     ${package}
 * @subpackage  ${subpackage}
 *
 * @copyright   Copyright (C) ${build.year} ${copyrights},  All rights reserved.
 * @license     ${license.name}; see ${license.url}
 * @author      ${author.name}
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Layout\FileLayout;
use Joomla\Registry\Registry;

defined('JPATH_BASE') or die;

/**
 * @var array      $displayData                Layout data.
 * @var int        $category_id                Category instance.
 * @var Registry   $params                     Parameters to use on this layout.
 * @var array      $items                      Images to display.
 * @var int        $thumbnail_width            Thumbnail width.
 * @var int        $thumbnail_height           Thumbnail height.
 * @var int        $thumbnail_method           Thumbnail generation method.
 * @var bool       $image_lightbox             Should be use lightbox for images.
 * @var int        $category_square_row_length Number of images in a row.
 * @var FileLayout $layoutThumbnail            Thumbnail layout file to use.
 */

extract($displayData, EXTR_SKIP);

// Include assets generated by Webpack
if ($params->def('include_component_assets', 1)) {
    BPGalleryHelperLayout::includeEntryPointAssets('square');
}
if ($params->def('include_theme_assets', 1)) {
    BPGalleryHelperLayout::includeEntryPointAssets('category-default');
}

// If lightbox is enabled
$container_id = 'com_bpgallery_category_squares_' . $category_id;
if ($params->def('images_lightbox', 1)) {
    $lightboxLayoutData = [
        'params'         => $params,
        'lightbox_query' => '#' . $container_id
    ];
    (new FileLayout('components.com_bpgallery.layouts.lightbox', JPATH_ROOT))->render($lightboxLayoutData);
}

// Responsive styles
$image_width = round(floor(100 / $category_square_row_length), 2);
Factory::getDocument()->addStyleDeclaration("
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
