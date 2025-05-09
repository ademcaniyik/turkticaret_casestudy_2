<?php

namespace App\Helpers;

class Response
{
    public static function json($data = null, string $status = 'success', ?string $message = null, int $code = 200, array $meta = []): void
    {
        http_response_code($code);
        header('Content-Type: application/json');

        $response = [
            'status' => $status
        ];

        if ($message !== null) {
            $response['message'] = $message;
        }

        if ($data !== null) {
            $response['data'] = $data;
        }

        if (!empty($meta)) {
            $response['meta'] = $meta;
        }

        echo json_encode($response);
        exit;
    }

    public static function error(string $message, int $code = 400, array $errors = []): void
    {
        $response = [
            'status' => 'error',
            'message' => $message
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        echo json_encode($response);
        exit;
    }

    public static function validationError(array $errors): void
    {
        self::error('Validation failed', 422, $errors);
    }

    public static function notFound(string $message = 'Resource not found'): void
    {
        self::error($message, 404);
    }
}