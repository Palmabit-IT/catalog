<?php namespace Palmabit\Catalog\Controllers;

use BaseController, View, Input, Redirect, App;
use Palmabit\Library\Exception\PalmabitExceptionsInterface;
use Palmabit\Library\Form\FormModel;
use Palmabit\Catalog\Validators\CategoryValidator;
use Palmabit\Catalog\Models\Category;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Palmabit\Catalog\Presenters\PresenterCategory;

class CategoryController extends BaseController
{
    /**
     * Repository for the categories
     * @var
     */
    protected $repo;
    /**
     * Category validator
     *
     * @var CategoryValidator
     */
    protected $v;
    /**
     * @var Palmabit\Library\Form\FormModel
     */
    protected $f;
    /**
     * @var Palmabit\Catalog\Presenters\PresenterCategoria
     */
    protected $p;

    public function __construct(CategoryValidator $v)
    {
        $is_admin = true;
        $this->repo = App::make('category_repository', $is_admin);
        $this->v = $v;
        $this->f = new FormModel($this->v, $this->repo);
    }

    public function lists()
    {
        $cats = $this->repo->all();

        return View::make('catalog::category.show')->with( array("categories" => $cats) );
    }

    public function getEdit()
    {
        $slug_lang = Input::get('slug_lang');

        try
        {
            $categories = $this->repo->findBySlugLang($slug_lang);

        }
        catch(ModelNotFoundException $e)
        {
            $categories = new Category();
        }
        $this->p = new PresenterCategory($categories);

        return View::make('catalog::category.edit')->with( ["categories" => $categories, "slug_lang" => $slug_lang, "presenter" => $this->p] );
    }

    public function postEdit()
    {
       $input = Input::all();

       try
       {
           $obj = $this->f->process($input);
       }
       catch(PalmabitExceptionsInterface $e)
       {
           $errors = $this->f->getErrors();
           return Redirect::action("Palmabit\\Catalog\\Controllers\\CategoryController@getEdit")->withInput()->withErrors($errors);
       }

       return Redirect::action("Palmabit\\Catalog\\Controllers\\CategoryController@getEdit",["slug_lang" => $obj->slug_lang])->with(["message"=>"Categoria modificata con successo."]);
    }

    public function delete()
    {
        $input = Input::all();

        try
        {
            $this->f->delete($input);
        }
        catch(PalmabitExceptionsInterface $e)
        {
            $errors = $this->f->getErrors();
            return Redirect::action("Palmabit\\Catalog\\Controllers\\CategoryController@lists")->withErrors($errors);
        }

        return Redirect::action("Palmabit\\Catalog\\Controllers\\CategoryController@lists")->with(array("message"=>"Categoria eliminata con successo."));
    }
}