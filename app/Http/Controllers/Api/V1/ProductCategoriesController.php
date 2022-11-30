<?php

namespace App\Http\Controllers\Api\V1;

use App\ProductCategory;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCategory as ProductCategoryResource;
use App\Http\Requests\Admin\StoreProductCategoriesRequest;
use App\Http\Requests\Admin\UpdateProductCategoriesRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use App\Http\Controllers\Traits\FileUploadTrait;


class ProductCategoriesController extends Controller
{
    public function index()
    {
        

        return new ProductCategoryResource(ProductCategory::with([])->get());
    }

    public function show($id)
    {
        if (Gate::denies('product_category_view')) {
            return abort(401);
        }

        $product_category = ProductCategory::with([])->findOrFail($id);

        return new ProductCategoryResource($product_category);
    }

    public function store(StoreProductCategoriesRequest $request)
    {
        if (Gate::denies('product_category_create')) {
            return abort(401);
        }

        $product_category = ProductCategory::create($request->all());
        
        if ($request->hasFile('photo')) {
            $product_category->addMedia($request->file('photo'))->toMediaCollection('photo');
        }

        return (new ProductCategoryResource($product_category))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateProductCategoriesRequest $request, $id)
    {
        if (Gate::denies('product_category_edit')) {
            return abort(401);
        }

        $product_category = ProductCategory::findOrFail($id);
        $product_category->update($request->all());
        
        if (! $request->input('photo') && $product_category->getFirstMedia('photo')) {
            $product_category->getFirstMedia('photo')->delete();
        }
        if ($request->hasFile('photo')) {
            $product_category->addMedia($request->file('photo'))->toMediaCollection('photo');
        }

        return (new ProductCategoryResource($product_category))
            ->response()
            ->setStatusCode(202);
    }

    public function destroy($id)
    {
        if (Gate::denies('product_category_delete')) {
            return abort(401);
        }

        $product_category = ProductCategory::findOrFail($id);
        $product_category->delete();

        return response(null, 204);
    }
}
