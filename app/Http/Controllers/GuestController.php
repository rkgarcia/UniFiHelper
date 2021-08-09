<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Guest;

class GuestController extends Controller
{
    public function index()
    {
        return response()->json(Guest::get());
    }

    public function actives()
    {
        $date = new \DateTime();
        $actives = Guest::whereRaw(['end' => ['$gt' => $date->getTimestamp()]])->get();
        return response()->json($actives);
    }

    public function expireds()
    {
        $date = new \DateTime();
        $actives = Guest::whereRaw(['end' => ['$lt' => $date->getTimestamp()]])->get();
        return response()->json($actives);
    }

    public function byId(string $id)
    {
        $guest = Guest::find($id);
        if(is_null($guest)) {
            return response()->json(['message' => "Cliente no encontrado"], 404);
        }
        return response()->json($guest);
    }

    public function byVoucherId(string $id)
    {
        return response()->json(Guest::where('voucher_id', $id)->get());
    }

    public function byVoucherCode(string $code)
    {
        return response()->json(Guest::where('voucher_code', $code)->get());
    }
}
