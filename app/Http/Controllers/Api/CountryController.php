<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;

class CountryController extends Controller
{
    public function index(){
        $countries = Country::all();
        return response()->json([
            'status' => true,
            'message' => 'Countries Retrieved Successfully',
            'data' => $countries
        ], 200);
    }
}
