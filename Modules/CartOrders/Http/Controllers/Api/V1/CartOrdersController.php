<?php

namespace Modules\CartOrders\Http\Controllers\Api\V1;

use Modules\CartOrders\Entities\CartOrder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\CartOrders\Http\Requests\Admin\StoreCartOrdersRequest;
use Modules\CartOrders\Http\Requests\Admin\UpdateCartOrdersRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class CartOrdersController extends Controller
{
    public function index()
    {
        return Order::all();
    }

    public function show($id)
    {
        return Order::findOrFail($id);
    }

    public function update(UpdateCartOrdersRequest $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->update($request->all());
        

        return $order;
    }

    public function store(StoreCartOrdersRequest $request)
    {
        $order = Order::create($request->all());
        

        return $order;
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return '';
    }
}
