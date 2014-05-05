<?php namespace Palmabit\Catalog\Controllers;

use BaseController, View, Input, Redirect, App;
use Illuminate\Support\MessageBag;
use Palmabit\Library\Exceptions\InvalidException;
use Palmabit\Library\Exceptions\NotFoundException;
use Palmabit\Library\Exceptions\PalmabitExceptionsInterface;
use Palmabit\Library\Exceptions\ValidationException;
use Palmabit\Library\Form\FormModel;
use Palmabit\Catalog\Validators\CategoryValidator;
use Palmabit\Catalog\Validators\CategoryImageValidator;
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
     * @var \Palmabit\Library\Form\FormModel
     */
    protected $f;
    /**
     * @var \Palmabit\Catalog\Presenters\PresenterCategoria
     */
    protected $p;

    public function __construct(CategoryValidator $v, CategoryImageValidator $vi)
    {
        $is_admin = true;
        $this->repo = App::make('category_repository', $is_admin);
        $this->v = $v;
        $this->v_i = $vi;
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
        catch(NotFoundException $e)
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

    /**
     * @todo refactor
     */
    public function postSetParent()
    {
        $id = Input::get('id');
        $parent_id = Input::get('parent_id');
        $slug_lang = Input::get('slug_lang');

        try
        {
            $this->repo->setParent($id, $parent_id);
        }
        catch(PalmabitExceptionsInterface $e)
        {
            return Redirect::action("Palmabit\\Catalog\\Controllers\\CategoryController@getEdit", ['slug_lang' => $slug_lang])->withInput()->withErrors(new MessageBag(["model"=> "Non è possibile associare la categoria."]));
        }

        return Redirect::action("Palmabit\\Catalog\\Controllers\\CategoryController@getEdit",["slug_lang" => $slug_lang])->with([ "message_tree" => "Padre modificato con successo." ]);
    }

    /**
     * @todo refactor
     */
    public function postSetParentList()
    {
        $id = Input::get('id');
        $parent_id = Input::get('parent_id');

        try
        {
            $this->repo->setParent($id, $parent_id);
        }
        catch(PalmabitExceptionsInterface $e)
        {
            return Redirect::action("Palmabit\\Catalog\\Controllers\\CategoryController@lists")->withErrors(new MessageBag(["model"=> "Non è possibile associare la categoria."]));
        }

        return Redirect::action("Palmabit\\Catalog\\Controllers\\CategoryController@lists")->with([ "message" => "Padre modificato con successo." ]);
    }

    public function postUpdateImage()
    {
        $id = Input::get('id');
        $input = Input::all();
        $slug_lang = Input::get('slug_lang');

        try
        {
            if (! $this->v_i->validate($input)) throw new ValidationException;
            $obj = $this->repo->updateImage($id, $input);
        }
        catch(InvalidException $e)
        {
            return Redirect::action("Palmabit\\Catalog\\Controllers\\CategoryController@getEdit", ['slug_lang' => $slug_lang])->withInput()->withErrors(new MessageBag(["model"=> "Errore nell'associazione dell'immagine."]));
        }
        catch(ValidationException $e)
        {
            return Redirect::action("Palmabit\\Catalog\\Controllers\\CategoryController@getEdit", ['slug_lang' => $slug_lang])->withInput()->withErrors($this->v_i->getErrors());
        }

        return Redirect::action("Palmabit\\Catalog\\Controllers\\CategoryController@getEdit",["slug_lang" => $slug_lang])->with([ "message_img" => "Immagine modificata con successo." ]);
    }
}