<?php

namespace App\Helpers;

class ApiResponse
{
    public static function success(
        string $message = 'Success',
        int $code = 200,
        $data = null,
        array $meta = []
    ) {
        $response = [
            'success' => true,
            'code'    => $code,
            'message' => $message,
        ];

        if (!is_null($data)) {
            $response['data'] = $data;
        }

        if (!empty($meta)) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $code);
    }

    public static function error(
        string $message = 'Error',
        int $code = 400,
        array $errors = []
    ) {
        $response = [
            'success' => false,
            'code'    => $code,
            'message' => $message,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }
}