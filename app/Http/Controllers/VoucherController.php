<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Voucher;

class VoucherController extends Controller
{
    public function index()
    {
        return response()->json(Voucher::get());
    }

    public function actives()
    {
        return response()->json(Voucher::where('valid', 'exists', false)->get());
    }

    public function byId(string $id)
    {
        $voucher = Voucher::find($id);
        if(is_null($voucher)) {
            return response()->json(['message' => "Voucher no encontrado"], 404);
        }
        return response()->json($voucher);
    }

    public function byCode(string $code)
    {
        $voucher = Voucher::where("code", $code)->first();
        if(is_null($voucher)) {
            return response()->json(['message' => "Voucher no encontrado"], 404);
        }
        return response()->json($voucher);
    }


}
