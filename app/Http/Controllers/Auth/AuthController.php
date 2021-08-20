<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\ScrapeSiam;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class AuthController extends Controller {

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function auth(Request $request) {
        try {
            $response = ScrapeSiam::run($request);
            return response()->json($response, 200);
        } catch (Exception $ex) {
            return response()->json([
                'message' => 'error',
                'error_message' => $ex->getMessage(),
                'error_file' => $ex->getFile(),
                'error_line' => $ex->getLine()
            ], 408);
        }
    }
}
