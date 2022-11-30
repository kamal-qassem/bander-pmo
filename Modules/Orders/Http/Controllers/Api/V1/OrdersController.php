<?php

namespace Modules\Orders\Http\Controllers\Api\V1;

use Modules\Orders\Entities\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Orders\Http\Requests\Admin\StoreOrdersRequest;
use Modules\Orders\Http\Requests\Admin\UpdateOrdersRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class OrdersController extends Controller
{
    public function index()
    {
        return Order::all();
    }

    public function show($id)
    {
        return Order::findOrFail($id);
    }

    public function update(UpdateOrdersRequest $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->update($request->all());
        

        return $order;
    }

    public function store(StoreOrdersRequest $request)
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
