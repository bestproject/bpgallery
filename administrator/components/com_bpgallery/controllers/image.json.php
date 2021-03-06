<?php

/**
 * @author        ${author.name} (${author.email})
 * @website        ${author.url}
 * @copyright    ${copyrights}
 * @license        ${license.url} ${license.name}
 * @package        ${package}
 * @subpackage        ${subpackage}
 */

defined('_JEXEC') or die;

use Joomla\Utilities\ArrayHelper;

/**
 * Image controller class for JSON format.
 */
class BPGalleryControllerImage extends JControllerAdmin
{

    /**
     * Image upload action.
     *
     * @return  string
     *
     * @throws Exception
     */
    public function upload()
    {
        // Remove the script time limit.
        @set_time_limit(0);

        // Do not cache the response to this, its a redirect, and mod_expires and google chrome browser bugs cache it forever!
        $app = JFactory::getApplication();
        $app->allowCache(false);
        $debugMode = $app->isClient('administrator') or $app->get('debug');

        // Set mime
        $app->mimeType = 'application/json';

        // Set content type
        $app->setHeader(
            'Content-Type',
            $app->mimeType . '; charset=' . $app->charSet
        );

        // Get uploaded file info
        $file = $this->input->files->get('image');

        // Check content type
        $permitted_types = array(
            'image/jpeg',
            'image/png',
            'image/gif'
        );
        if (!in_array($file['type'], $permitted_types)) {
            // Useful debug message
            if ($debugMode) {
                echo json_encode(['error' => JText::sprintf('COM_BPGALLERY_ERROR_UNSUPPORTED_FILE_S', $file['type'])]);
            }

            $this->close(415);
        }

        // Prepare data
        $data = [
            'catid' => $this->input->getInt('category_id'),
            'upload_image' => $file['tmp_name'],
            'upload_file_name' => $file['name'],
            'language' => $app->getUserStateFromRequest($this->option . '.images.filter.language', 'filter_language', '')
        ];

        // Access check.
        if (!$this->allowAdd($data)) {
            // Useful debug message
            if ($debugMode) {
                echo json_encode(['error' => JText::_('COM_BPGALLERY_ERROR_MISSING_ADD_PERMISSION')]);
            }

            $app->setHeader('status', 550);
            $app->close();
        }

        // Get application model
        /* @var $model BPGalleryModelImage */
        $model = $this->getModel();

        // Response holder
        $response = [];

        // If save process succesed
        if ($model->save($data)) {
            // Useful debug message
            if ($debugMode) {
                $response = array_merge($response, [
                    'result' => JText::_('COM_BPGALLERY_SUCCESS_ADDED'),
                    'data' => $data
                ]);
            }

            // Send proper status code
            $app->setHeader('status', 200);

            // save process failed
        } else {
            // Useful debug message
            if ($debugMode) {
                $response = array_merge($response, [
                    'error' => JText::_('COM_BPGALLERY_ERROR_SAVING_IMAGE_DATA'),
                    'data' => $data
                ]);
            }

            // Send proper status code
            $app->setHeader('status', 500);
        }

        // Set peak memory usage
        if ($app->get('debug')) {
            $response['peak_memory_usage'] = round(memory_get_peak_usage(true) / 1024
                / 1024, 2);
        }

        // Send response status code
        $app->sendHeaders();

        // Send response
        echo json_encode($response);

        // Close application
        $app->close();
    }

    /**
     * Method override to check if you can add a new record.
     *
     * @param array $data An array of input data.
     *
     * @return  boolean
     */
    protected function allowAdd($data = array())
    {
        $categoryId = ArrayHelper::getValue($data, 'catid', $this->input->getInt('filter_category_id'), 'int');
        $allow = null;

        if ($categoryId) {
            // If the category has been passed in the URL check it.
            $allow = JFactory::getUser()->authorise('core.create', $this->option . '.category.' . $categoryId);
        }

        if ($allow === null) {
            // In the absense of better information, revert to the component permissions.
            return parent::allowAdd($data);
        }

        return $allow;
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
    public function getModel(
        $name = 'Image',
        $prefix = 'BPGalleryModel',
        $config = array('ignore_request' => true)
    ) {
        return parent::getModel($name, $prefix, $config);
    }
}
