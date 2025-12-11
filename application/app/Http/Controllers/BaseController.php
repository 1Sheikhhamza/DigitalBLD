<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Info(
 *   version="1.0.0",
 *   title="Doctors App APIs"
 * )
 */

class BaseController extends Controller
{
    public $user;


    public function __construct()
    {
        date_default_timezone_set('Asia/Dhaka');
    }

    /**
     * Return data with json response and response code success
     * @return JsonResponse
     */
    public function successMultipeParamResponse($result, $additionalData, $message, $responseCode)
    {
        $response = [
            'success' => true,
            'data'    => $result,
            'additional' => $additionalData,
            'message' => $message,
        ];

        return response()->json($response, $responseCode);
    }

    public function successResponse($result, $message, $responseCode)
    {
        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];

        return response()->json($response, $responseCode);
    }


    /**
     * Return error json response
     * @return JsonResponse
     */
    public function errorResponse($error, $errorMessages = [], $code = 404)
    {
        try {
            $response = [
                'success' => false,
                'message' => $error,
            ];

            if (!empty($errorMessages)) {
                $response['error'] = $errorMessages;
            }


            return response()->json($response, $code);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage());
        }
    }

    public function faildResponse($errorMessages, $code = 404)
    {
        try {
            $response = [
                'success' => false,
                'message' => $errorMessages,
            ];

            return response()->json($response, $code);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage());
        }
    }
}
