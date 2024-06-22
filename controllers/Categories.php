<?php namespace Winter\Catalogue\Controllers;

use BackendMenu;
use Flash;
use Lang;
use Backend\Classes\Controller;
use Winter\Catalogue\Models\Category;

class Categories extends Controller
{
    public $implement = [
        \Backend\Behaviors\FormController::class,
        \Backend\Behaviors\ListController::class,
        \Backend\Behaviors\ReorderController::class
    ];

    public $requiredPermissions = ['winter.catalogue.access_categories'];

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Smart.Catalogue', 'catalogue', 'categories');
    }

    public function index_onDelete()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {
            foreach ($checkedIds as $categoryId) {
                if ((!$category = Category::find($categoryId))) {
                    continue;
                }

                $category->delete();
            }

            Flash::success(Lang::get('winter.catalogue::lang.category.delete_success'));
        }

        return $this->listRefresh();
    }
}
