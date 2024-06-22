<?php

namespace Winter\Catalogue\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Flash;
use Lang;
use Winter\Catalogue\Models\Post;

class Posts extends Controller
{
    public $implement = [
        \Backend\Behaviors\FormController::class,
        \Backend\Behaviors\ListController::class,
        \Backend\Behaviors\ImportExportController::class
    ];

    public $requiredPermissions = ['winter.catalogue.access_other_posts', 'winter.catalogue.access_posts'];

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Smart.Catalogue', 'catalogue', 'posts');
    }

    public function index()
    {
        $this->vars['postsTotal'] = Post::count();
        $this->vars['postsPublished'] = Post::isPublished()->count();
        $this->vars['postsDrafts'] = $this->vars['postsTotal'] - $this->vars['postsPublished'];

        $this->asExtension('ListController')->index();
    }

    public function create()
    {
        BackendMenu::setContextSideMenu('new_post');

        $this->bodyClass = 'compact-container';
        $this->addCss('/plugins/smart/catalogue/assets/css/winter.catalogue-preview.css');
        $this->addJs('/plugins/winter/catalogue/assets/js/post-form.js');

        return $this->asExtension('FormController')->create();
    }

    public function update($recordId = null)
    {
        $this->bodyClass = 'compact-container';
        $this->addCss('/plugins/smart/catalogue/assets/css/winter.catalogue-preview.css');
        $this->addJs('/plugins/smart/catalogue/assets/js/post-form.js');

        return $this->asExtension('FormController')->update($recordId);
    }

    public function export()
    {
        $this->addCss('/plugins/smart/catalogue/assets/css/winter.catalogue-export.css');

        return $this->asExtension('ImportExportController')->export();
    }

    public function listExtendQuery($query)
    {
        if (!$this->user->hasAnyAccess(['winter.catalogue.access_other_posts'])) {
            $query->where('user_id', $this->user->id);
        }
    }

    public function formExtendQuery($query)
    {
        if (!$this->user->hasAnyAccess(['winter.catalogue.access_other_posts'])) {
            $query->where('user_id', $this->user->id);
        }
    }

    public function formExtendModel($model)
    {
        if ($model->exists && !empty($model->slug) && $model->preview_page) {
            $model->setUrl($model->preview_page, (new \Cms\Classes\Controller()));
        }
    }

    public function formExtendFieldsBefore($widget)
    {
        if (!$model = $widget->model) {
            return;
        }

        // @TODO: This shouldn't engage when the translate plugin is present but disabled
        // Fix can be more restrictive checks here or finishing changes to the class loader so that
        // disabled plugins cannot even have their classes loaded.
        if ($model instanceof Post && $model->isClassExtendedWith('Winter.Translate.Behaviors.TranslatableModel')) {
            $widget->tabs['fields']['content']['type'] = 'Winter\Catalogue\FormWidgets\MLCatalogueMarkdown';
        }
    }

    public function index_onDelete()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {

            foreach ($checkedIds as $postId) {
                if ((!$post = Post::find($postId)) || !$post->canEdit($this->user)) {
                    continue;
                }

                $post->delete();
            }

            Flash::success(Lang::get('winter.catalogue::lang.post.delete_success'));
        }

        return $this->listRefresh();
    }

    /**
     * {@inheritDoc}
     */
    public function listInjectRowClass($record, $definition = null)
    {
        if (!$record->published) {
            return 'safe disabled';
        }
    }

    public function formBeforeCreate($model)
    {
        $model->user_id = $this->user->id;
    }

    public function onRefreshPreview()
    {
        $data = post('Post');

        $previewHtml = Post::formatHtml($data['content'], true);

        return [
            'preview' => $previewHtml
        ];
    }
}
