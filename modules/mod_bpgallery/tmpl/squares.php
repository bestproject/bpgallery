<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_category
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;

// Include assets generated by Webpack
BPGalleryHelperLayout::includeEntryPointAssets('square');
BPGalleryHelperLayout::includeEntryPointAssets('category-default');
JHtml::_('behavior.core');

if ($params->def('images_lightbox', 1)) {
    HTMLHelper::_('jquery.framework');
    BPGalleryHelperLayout::includeEntryPointAssets('lightbox');

    $lightbox_options = [
        'type' => 'image',
        'gallery' => [
            'enabled' => true,
            'tCounter' => '<span class="mfp-counter">' . Text::_('MOD_BPGALLERY_LIGHTBOX_N_OF_X') . '</span>',
            'tPrev' => Text::_('MOD_BPGALLERY_LIGHTBOX_PREV'),
            'tNext' => Text::_('MOD_BPGALLERY_LIGHTBOX_NEXT'),
        ],
        'tClose' => Text::_('COM_BPGALLERY_LIGHTBOX_CLOSE'),
        'closeBtnInside' => true,
        'zoom' => [
            'enabled' => true,
            'duration' => 300,
            'easing' => 'ease-in-out'
        ]
    ];
    $lightbox_options = json_encode($lightbox_options);

    $doc->addScriptDeclaration("
    
        // Run lightbox for BP Gallery Module
        jQuery(function($){
            var lightbox_options = $lightbox_options;
            lightbox_options.zoom.opener = function(openerElement) {
                return openerElement.is('img') ? openerElement : openerElement.find('img');
            }
            $('#$module_id.bpgallery-category .image-link').magnificPopup(lightbox_options);
        });
    ");
}
$image_width = round(floor(100 / (int)$category_square_row_length), 2);
$doc->addStyleDeclaration("
    @media screen and (max-width: 360px) {
        .bpgallery-category-square .items .image-link {
            width: 100%
        }
    }
    @media screen and (min-width:361px) and (max-width: 800px) {
        .bpgallery-category-square .items .image-link {
            width: 50%
        }
    }
    @media screen and (min-width:361px) and (min-width: 801px) {
        .bpgallery-category-square .items .image-link {
            width: {$image_width}%
        }
    }
");

?>
<div class="modbpgallery bpgallery-category bpgallery-category-square<?php echo $moduleclass_sfx ?>"
     id="<?php echo $module_id ?>">

    <?php if (empty($list)) : ?>
        <p> <?php echo JText::_('MOD_BPGALLERY_NO_IMAGES'); ?>     </p>
    <?php else : ?>
        <ul class="items">
            <?php foreach ($list as $i => $item) :
                $url_thumbnail = BPGalleryHelper::getThumbnail($item, 200, 200, BPGalleryHelper::METHOD_CROP);
                $url_medium = BPGalleryHelper::getThumbnail($item, 600, 600, BPGalleryHelper::METHOD_CROP);
                $url_full = BPGalleryHelper::getThumbnail($item, 1920, 1080, BPGalleryHelper::METHOD_FIT);
                $url = Route::_(BPGalleryHelperRoute::getImageRoute($item->slug, $item->catid, $item->language));
                $alt = empty($item->alt) ? $item->title : $item->alt;
                ?>
                <a href="<?php echo $image_lightbox ? $url_full : $url ?>"
                    <?php if ($image_lightbox): ?>
                        target="_blank"
                    <?php endif ?>
                   class="image-link"
                   title="<?php echo $item->title ?>">
                <span class="inner">
                    <span class="overlay"></span>
                    <img
                            src="<?php echo $url_thumbnail ?>" alt="<?php echo $alt ?>" class="image"
                            srcset="<?php echo $url_thumbnail ?> 200w, <?php echo $url_medium ?> 600w, <?php echo $url_full ?> 1920w"
                            sizes="(max-width: 800px) 600px, (min-width:801px) 200px, 1920px"
                    >
                </span>
                </a>
            <?php endforeach; ?>
        </ul>
    <?php endif ?>

</div>
