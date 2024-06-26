<?php

namespace Smart\Catalogue\FormWidgets;

use Backend\FormWidgets\MarkdownEditor;
use Exception;
use Input;
use Lang;
use Response;
use System\Models\File;
use SystemException;
use ValidationException;
use Validator;
use Smart\Catalogue\Models\Post as PostModel;

/**
 * Special markdown editor for the Create/Edit Post form.
 *
 * @package smart\catalogue
 * @author Alexey Bobkov, Samuel Georges
 */
class CatalogueMarkdown extends MarkdownEditor
{
    /**
     * {@inheritDoc}
     */
    public function init()
    {
        $this->viewPath = base_path().'/modules/backend/formwidgets/markdowneditor/partials';

        $this->checkUploadPostback();

        parent::init();
    }

    /**
     * {@inheritDoc}
     */
    protected function loadAssets()
    {
        $this->assetPath = '/modules/backend/formwidgets/markdowneditor/assets';
        parent::loadAssets();
    }

    /**
     * Disable HTML cleaning on the widget level since the PostModel will handle it
     *
     * @return boolean
     */
    protected function shouldCleanHtml()
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function onRefresh()
    {
        $content = post($this->formField->getName());

        $previewHtml = PostModel::formatHtml($content, true);

        return [
            'preview' => $previewHtml
        ];
    }

    /**
     * Handle images being uploaded to the catalogue post
     *
     * @return void
     */
    protected function checkUploadPostback()
    {
        if (!post('X_BLOG_IMAGE_UPLOAD')) {
            return;
        }

        $uploadedFileName = null;

        try {
            $uploadedFile = Input::file('file');

            if ($uploadedFile)
                $uploadedFileName = $uploadedFile->getClientOriginalName();

            $validationRules = ['max:'.File::getMaxFilesize()];
            $validationRules[] = 'mimes:jpg,jpeg,bmp,png,gif';

            $validation = Validator::make(
                ['file_data' => $uploadedFile],
                ['file_data' => $validationRules]
            );

            if ($validation->fails()) {
                throw new ValidationException($validation);
            }

            if (!$uploadedFile->isValid()) {
                throw new SystemException(Lang::get('cms::lang.asset.file_not_valid'));
            }

            $fileRelation = $this->model->content_images();

            $file = new File();
            $file->data = $uploadedFile;
            $file->is_public = true;
            $file->save();

            $fileRelation->add($file, $this->sessionKey);
            $result = [
                'file' => $uploadedFileName,
                'path' => $file->getPath()
            ];

            $response = Response::make()->setContent($result);
            $this->controller->setResponse($response);

        } catch (Exception $ex) {
            $message = $uploadedFileName
                ? Lang::get('cms::lang.asset.error_uploading_file', ['name' => $uploadedFileName, 'error' => $ex->getMessage()])
                : $ex->getMessage();

            $result = [
                'error' => $message,
                'file' => $uploadedFileName
            ];

            $response = Response::make()->setContent($result);
            $this->controller->setResponse($response);
        }
    }
}
