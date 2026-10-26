<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    public function index(Request $request)
    {
        $data = Buyer::with('user', 'orders.orderItems.listing', 'orders.delivery', 'orders.payments', 'orders.seller')
            ->where('user_id', auth()->id())->get();
        return response()->json($data);
    }
}
