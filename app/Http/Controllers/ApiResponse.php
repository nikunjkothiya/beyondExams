<?php

namespace App\Http\Controllers;

use Log;

class ApiResponse extends Controller
{
    public function sendResponse($code, $message, $data)
    {
        if ($code == 200 || $code == 201) {
            return response([
                'status' => 'success',
                'status_code' => $code,
                'message' => $message,
                'data' => $data
            ], $code);
        } else {
            Log::error($message);
            Log::error(json_encode($data));
            return response([
                'status' => 'error',
                'status_code' => $code,
                'message' => $message,
                'data' => $data
            ], $code);
        }
    }
}
