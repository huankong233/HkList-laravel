<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ResponseController extends Controller
{
    static public function response($code, $message, $data = null)
    {
        return response()->json([
            'code'    => $code,
            'message' => $message,
            'data'    => $data
        ], $code);
    }
}
