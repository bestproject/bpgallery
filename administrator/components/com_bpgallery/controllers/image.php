<?php

/**
 * @author		Artur Stępień (artur.stepien@bestproject.pl)
 * @website		www.bestproject.pl
 * @copyright	Copyright (C) 2017 Best Project, Inc. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

defined('_JEXEC') or die;

use Joomla\Utilities\ArrayHelper;

/**
 * Image controller class.
 */
class BPGalleryControllerImage extends JControllerAdmin
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 */
	protected $text_prefix = 'COM_BPGALLERY_IMAGE';

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JControllerLegacy
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JModelLegacy  The model.
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'Image', $prefix = 'BPGalleryModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}

    /**
     * Image upload action.
     * 
     * @return  string
     */
    public function upload() {

        // Get image model
        $model = $this->getModel();

        // If user can manage this category and upload was successful
        return 'ssss';
            // Return success

        // Upload failed
        
            // Return error
        

    }
}