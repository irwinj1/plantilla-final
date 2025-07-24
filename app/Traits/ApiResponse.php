<?php

namespace App\Traits;

trait ApiResponse {
    protected function success($message = 'Success', $status = 200, $data = null, $pagination = [])
    {
        return response()->json([
            'message' => $message,
            'status' => $status,
            'data' => $data,
            'pagination' => $pagination
        ], $status);
    }

    protected function error($message = 'Error', $status = 500)
    {
        return response()->json([
            'error' => true,
            'message' => $message,
            'status' => $status
        ], $status);
    }
}