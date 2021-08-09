<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

use App\Guest;
use App\Voucher;

class VoucherController extends Controller
{
    protected $client = null;

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

    public function create(Request $request, string $site) {
        $this->sendLogin($site);
        try {
            $response = $this->postRequest('api/s/'.$site.'/cmd/hotspot', $request->all());
            $body = $response->getBody();
            $result = json_decode($body);
            $time = $result->data[0]->create_time;
            $voucher = Voucher::where('create_time', $time)->first();
            return response()->json($voucher);
        } catch(\Exception $e) {
            return response()->json(['message' => 'No fue posible crear el Voucher'], 400);
        }
    }

    protected function createClient()
    {
        if(is_null($this->client)){
            $server = env('UNIFI_CONTROLLER');
            $this->client = new Client([
                'base_uri'  =>  $server,
                'verify'    =>  false,
                'headers'   =>  [
                    'Content-Type' => 'application/json'
                ],
                'cookies'    =>  true
            ]);
        }
    }

    protected function sendLogin(string $site)
    {
        $this->createClient();
        $user = $_SERVER['PHP_AUTH_USER'];
        $pass = $_SERVER['PHP_AUTH_PW'];
        $payload = [
            'username'  =>  $user,
            'password'  =>  $pass,
            'site_name' =>  $site
        ];
        try {
            $response = $this->client->post('api/login', [
                'body' => json_encode($payload)
            ]);
        } catch(\Exception $e) {
            return;
        }
    }

    protected function postRequest(string $url, $payload)
    {
        $this->createClient();
        return $this->client->post($url, [
            'body' => json_encode($payload)
        ]);
    }

}
