<?php namespace Palmabit\Catalog\Controllers;

use Illuminate\Support\MessageBag;
use Illuminate\Validation\Validator;
use View, Input, Redirect, App, Controller, L, Config;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Palmabit\Catalog\Presenters\PresenterProducts;
use Palmabit\Catalog\Validators\ProductCategoryValidator;
use Palmabit\Library\Exceptions\PalmabitExceptionsInterface;
use Palmabit\Library\Exceptions\NotFoundException;
use Palmabit\Library\Exceptions\ValidationException;
use Palmabit\Library\Form\FormModel;
use Palmabit\Catalog\Models\Product;
use Palmabit\Catalog\Validators\ProductImageValidator;
use Palmabit\Catalog\Validators\ProductValidator;
use Palmabit\Catalog\Validators\ProductFormOrderValidator;
use Palmabit\Catalog\Validators\ProductsProductsValidator;

class ProductsController extends Controller
{
    /**
     * Products repository
     *
     * @var \Palmabit\Catalog\Repository\EloquentProductReposiot
     */
    protected $r;

    /**
     */
    protected $v;
    /**
     * FormModel
     */
    protected $f;
    /**
     */
    protected $presenter;
    /**
     * FormModel Image
     */
    protected $f_img;
    /**
     * Image repository
     */
    protected $r_img;
    /**
     * @var \Palmabit\Catalog\Validators\ProductCategoryValidator
     */
    protected $vp_c;
    /**
     * @var \Palmabit\Catalog\Validators\ProductsProductsValidator
     */
    protected $v_pp;

    public function __construct(ProductValidator $v, ProductCategoryValidator $vpc, ProductsProductsValidator $vpp)
    {
        $is_admin = true;
        $this->r = App::make('product_repository', $is_admin);
        $this->v = $v;
        $this->f = new FormModel($this->v, $this->r);
        // immages
        $this->r_img = App::make('product_image_repository');
        $this->f_img = new FormModel(new ProductImageValidator(), $this->r_img);
        // product category
        $this->v_pc = $vpc;
        // products products
        $this->v_pp = $vpp;
    }

    public function lists()
    {
        $products = $this->r->all(Input::all());
        return View::make('catalog::products.show')->with(array("products" => $products));
    }

    public function getEdit()
    {
        $id = Input::get('id');
        try
        {
            $product = $this->r->find($id);
        } catch(NotFoundException $e)
        {
            $product = (new Product())->decorateLanguage(L::get_admin());
        }
        $this->presenter = new PresenterProducts($product->decorateLanguage(L::get_admin()));

        return View::make('catalog::products.edit')->with(["product" => $product, "id" => $id, "presenter" => $this->presenter]);
    }

    public function postEdit()
    {
        $input = Input::all();

        try
        {
            $obj = $this->f->process($input);
        } catch(PalmabitExceptionsInterface $e)
        {
            $errors = $this->f->getErrors();
            return Redirect::action("Palmabit\\Catalog\\Controllers\\ProductsController@getEdit", ["id" => Input::get('id')])->withInput()->withErrors($errors);
        }

        return Redirect::action("Palmabit\\Catalog\\Controllers\\ProductsController@getEdit", ["id" => $obj->id])
                       ->with(array("message" => "Prodotto modificato con successo."));
    }

    public function delete()
    {
        $input = Input::all();

        try
        {
            $this->f->delete($input);
        } catch(PalmabitExceptionsInterface $e)
        {
            $errors = $this->f->getErrors();
            return Redirect::action("Palmabit\\Catalog\\Controllers\\ProductsController@lists")->withErrors($errors);
        }

        return Redirect::action("Palmabit\\Catalog\\Controllers\\ProductsController@lists")
                       ->with(array("message" => "Prodotto eliminato con successo."));
    }


    public function postAttachCategory()
    {
        $product_id = Input::get('product_id');
        $category_id = Input::get('category_id');

        try
        {
            $this->v_pc->validate(Input::all());
            $this->r->associateCategory($product_id, $category_id);
        } catch(NotFoundException $e)
        {
            return Redirect::action("Palmabit\\Catalog\\Controllers\\ProductsController@getEdit", ["id" => $product_id])
                           ->withErrors(new MessageBag(["model" => "Prodotto non trovato."]));
        } catch(ValidationException $e)
        {
            return Redirect::action("Palmabit\\Catalog\\Controllers\\ProductsController@getEdit", ["id" => $product_id])
                           ->withErrors($this->v_pc->getErrors());
        }

        return Redirect::action("Palmabit\\Catalog\\Controllers\\ProductsController@getEdit", ["id" => $product_id])
                       ->with(["message_cat" => "Categoria associata con successo."]);
    }

    public function postDetachCategory()
    {
        $product_id = Input::get('product_id');
        $category_id = Input::get('category_id');

        try
        {
            $this->r->deassociateCategory($product_id, $category_id);
        } catch(NotFoundException $e)
        {
            return Redirect::action("Palmabit\\Catalog\\Controllers\\ProductsController@getEdit", ["id" => $product_id])
                           ->withErrors(new MessageBag(["model" => "Prodotto non trovato."]));
        }

        return Redirect::action("Palmabit\\Catalog\\Controllers\\ProductsController@getEdit", ["id" => $product_id])
                       ->with(["message_cat" => "Categoria deassociata con successo."]);
    }

    public function postImage()
    {
        $input = Input::all();

        try
        {
            $this->f_img->process($input);
        } catch(PalmabitExceptionsInterface $e)
        {
            $errors = $this->f_img->getErrors();
            return Redirect::action("Palmabit\\Catalog\\Controllers\\ProductsController@getEdit", ["id" => $input["product_id"]])->withInput()
                           ->withErrors($errors);
        }
        return Redirect::action("Palmabit\\Catalog\\Controllers\\ProductsController@getEdit", ["id" => $input["product_id"]])
                       ->with(["message_img" => "Immagine caricata con successo."]);
    }

    public function deleteImage()
    {
        $input = Input::all();

        try
        {
            $this->f_img->delete($input);
        } catch(PalmabitExceptionsInterface $e)
        {
            $errors = $this->f_img->getErrors();
            return Redirect::action("Palmabit\\Catalog\\Controllers\\ProductsController@getEdit", ["id" => $input["product_id"]])->withInput()
                           ->withErrors($errors);
        }
        return Redirect::action("Palmabit\\Catalog\\Controllers\\ProductsController@getEdit", ["id" => $input["product_id"]])
                       ->with(array("message_img" => "Immagine eliminata con successo."));
    }

    public function postFeatured($id, $product_id)
    {
        try
        {
            $this->r_img->changeFeatured($id, $product_id);
        } catch(PalmabitExceptionsInterface $e)
        {
            return Redirect::action("Palmabit\\Catalog\\Controllers\\ProductsController@getEdit", ["id" => $product_id])
                           ->withErrors(new MessageBag(["model" => $e->getMessage()]));
        }

        return Redirect::action("Palmabit\\Catalog\\Controllers\\ProductsController@getEdit", ["id" => $product_id])
                       ->with(array("message_img" => "Immagine in evidenza impostata con successo."));
    }

    public function postChangeOrder()
    {
        $input = Input::all();
        $validator = new ProductFormOrderValidator;
        $form_model = new FormModel($validator, $this->r);

        try
        {
            $obj = $form_model->process($input);
        } catch(PalmabitExceptionsInterface $e)
        {
            $errors = $form_model->getErrors();
            return Redirect::action("Palmabit\\Catalog\\Controllers\\ProductsController@lists")->withInput()->withErrors($errors);
        }

        return Redirect::action("Palmabit\\Catalog\\Controllers\\ProductsController@lists")
                       ->with(array("message" => "Ordine modificato con successo."));
    }

    public function postDetachProduct()
    {
        $first_product_id = Input::get('first_product_id');
        $second_product_id = Input::get('second_product_id');

        try
        {
            $this->v_pp->validate(Input::all());
            $this->r->detachProduct($first_product_id, $second_product_id);
        } catch(NotFoundException $e)
        {
            return Redirect::action("Palmabit\\Catalog\\Controllers\\ProductsController@getEdit", ["id" => $first_product_id])
                           ->withErrors(new MessageBag(["model" => "Prodotto non trovato."]));
        } catch(ValidationException $e)
        {
            return Redirect::action("Palmabit\\Catalog\\Controllers\\ProductsController@getEdit", ["id" => $first_product_id])
                           ->withErrors($this->v_pp->getErrors());
        }

        return redirect::action("Palmabit\\Catalog\\Controllers\\ProductsController@getEdit", ["id" => $first_product_id])
                       ->with(array("message_accessories" => "Prodotto rimosso con successo."));
    }

    public function postAttachProduct()
    {
        $first_product_id = Input::get('first_product_id');
        $second_product_id = Input::get('second_product_id');

        try
        {
            $this->v_pp->validate(Input::all());
            $this->r->attachProduct($first_product_id, $second_product_id);
        } catch(NotFoundException $e)
        {
            return Redirect::action("Palmabit\\Catalog\\Controllers\\ProductsController@getEdit", ["id" => $first_product_id])
                           ->withErrors(new MessageBag(["model" => "Prodotto non trovato."]));
        } catch(ValidationException $e)
        {
            return Redirect::action("Palmabit\\Catalog\\Controllers\\ProductsController@getEdit", ["id" => $first_product_id])
                           ->withErrors($this->v_pp->getErrors());
        }

        return Redirect::action("Palmabit\\Catalog\\Controllers\\ProductsController@getEdit", ["id" => $first_product_id])
                       ->with(array("message_accessories" => "Prodotto associato con successo."));
    }

    public function duplicate()
    {
        $id = Input::get('id');

        try
        {
            $obj = $this->r->duplicate($id);
        } catch(PalmabitExceptionsInterface $e)
        {
            return Redirect::action('Palmabit\Catalog\Controllers\ProductsController@lists', ["id" => $id])
                           ->withErrors(new MessageBag(["duplication" => "Problema nella duplicazione del prodotto."]));
        }
        return Redirect::action('Palmabit\Catalog\Controllers\ProductsController@lists', ["id" => $id])
                       ->withMessage("prodotto clonato con successo.");
    }
}
