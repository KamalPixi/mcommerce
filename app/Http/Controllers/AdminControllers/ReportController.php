<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\AppModels\OrderMaster;
use App\AppModels\Order;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $d = $request->validate([
          'date' => 'nullable|date|date_format:Y-m-d'
        ]);

        $order_master = OrderMaster::whereDate('created_at', (isset($d['date'])) ? $d['date'] : date("Y-m-d"))->orderBy('id', 'DESC')->paginate(15);
        $sales = array(
          'total_orders' => OrderMaster::whereDate('created_at', (isset($d['date'])) ? $d['date'] : date("Y-m-d"))->count(),
          'item_sold' => Order::whereDate('created_at', (isset($d['date'])) ? $d['date'] : date("Y-m-d"))->count(),
          'price' => OrderMaster::whereDate('created_at', (isset($d['date'])) ? $d['date'] : date("Y-m-d"))->sum('total_price'),
          'shipped_out' => OrderMaster::whereDate('created_at', (isset($d['date'])) ? $d['date'] : date("Y-m-d"))->where('status', 'shipped')->count(),
          'pending' => OrderMaster::whereDate('created_at', (isset($d['date'])) ? $d['date'] : date("Y-m-d"))->where('status', 'pending')->count(),
          'completed' => OrderMaster::whereDate('created_at', (isset($d['date'])) ? $d['date'] : date("Y-m-d"))->where('status', 'completed')->count()
        );

        return view('admin.reports.sales', compact('order_master', 'sales'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
