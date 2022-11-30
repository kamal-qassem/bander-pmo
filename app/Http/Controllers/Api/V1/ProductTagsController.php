<?php

namespace App\Http\Controllers\Api\V1;

use App\ProductTag;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductTag as ProductTagResource;
use App\Http\Requests\Admin\StoreProductTagsRequest;
use App\Http\Requests\Admin\UpdateProductTagsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;



class ProductTagsController extends Controller
{
    public function index()
    {
        

        return new ProductTagResource(ProductTag::with([])->get());
    }

    public function show($id)
    {
        if (Gate::denies('product_tag_view')) {
            return abort(401);
        }

        $product_tag = ProductTag::with([])->findOrFail($id);

        return new ProductTagResource($product_tag);
    }

    public function store(StoreProductTagsRequest $request)
    {
        if (Gate::denies('product_tag_create')) {
            return abort(401);
        }

        $product_tag = ProductTag::create($request->all());
        
        

        return (new ProductTagResource($product_tag))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateProductTagsRequest $request, $id)
    {
        if (Gate::denies('product_tag_edit')) {
            return abort(401);
        }

        $product_tag = ProductTag::findOrFail($id);
        $product_tag->update($request->all());
        
        
        

        return (new ProductTagResource($product_tag))
            ->response()
            ->setStatusCode(202);
    }

    public function destroy($id)
    {
        if (Gate::denies('product_tag_delete')) {
            return abort(401);
        }

        $product_tag = ProductTag::findOrFail($id);
        $product_tag->delete();

        return response(null, 204);
    }
}
