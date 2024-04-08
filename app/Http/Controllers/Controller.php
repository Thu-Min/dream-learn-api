<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected function apiResponse($success, $message = '', $data = [], $code = '')
    {
        return response()->json([
            'success' => $success,
            'message' => $message,
            'data' => $data
        ], $code);
    }
}
