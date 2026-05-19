<?php

namespace App\Core;

/**
 * API Response Helper
 * Standardized JSON response format untuk semua API
 */
class ApiResponse
{
    /**
     * Success Response
     */
    public static function success($data = null, $message = 'Success', $code = 200)
    {
        http_response_code($code);
        return json_encode([
            'status' => 'success',
            'code' => $code,
            'message' => $message,
            'data' => $data,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Error Response
     */
    public static function error($message = 'Error', $code = 400, $errors = null)
    {
        http_response_code($code);
        return json_encode([
            'status' => 'error',
            'code' => $code,
            'message' => $message,
            'errors' => $errors,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Validation Error Response
     */
    public static function validationError($errors)
    {
        http_response_code(422);
        return json_encode([
            'status' => 'validation_error',
            'code' => 422,
            'message' => 'Validation failed',
            'errors' => $errors,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Unauthorized Response
     */
    public static function unauthorized($message = 'Unauthorized')
    {
        http_response_code(401);
        return json_encode([
            'status' => 'error',
            'code' => 401,
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Forbidden Response
     */
    public static function forbidden($message = 'Forbidden')
    {
        http_response_code(403);
        return json_encode([
            'status' => 'error',
            'code' => 403,
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Not Found Response
     */
    public static function notFound($message = 'Not found')
    {
        http_response_code(404);
        return json_encode([
            'status' => 'error',
            'code' => 404,
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Paginated Response
     */
    public static function paginated($data, $total, $page, $limit, $message = 'Success')
    {
        http_response_code(200);
        return json_encode([
            'status' => 'success',
            'code' => 200,
            'message' => $message,
            'data' => $data,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'total_pages' => ceil($total / $limit)
            ],
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
}
