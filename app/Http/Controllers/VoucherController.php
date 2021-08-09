<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Guest;
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

    public function delete(string $id)
    {
        $date = new \DateTime();
        $timestamp = $date->getTimestamp();
        $validVoucher = Voucher::whereRaw(['end_time' => ['$gt' => $timestamp], '_id' => $id])->count();
        $guestsWithVoucher = Guest::whereRaw(['end' => ['$gt' => $timestamp], 'voucher_id' => $id])->count();

        $total = $validVoucher + $guestsWithVoucher;
        if($total < 1) {
            return response()->json(['message' => "Voucher no encontrado"], 404);
        }
        if($guestsWithVoucher > 0) {
            Guest::whereRaw(['end' => ['$gt' => $timestamp], 'voucher_id' => $id])->update(['end' => $timestamp]);
        }
        $voucher = Voucher::find($id);
        if(!is_null($voucher)) {
            $voucher->end = $timestamp;
            $voucher->valid = false;
            $voucher->save();    
        }
        return response()->json(['message' => "Voucher eliminado con éxito", 'data' => $voucher]);
    }

}
