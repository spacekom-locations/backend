<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @method sendData sends the given data in an envelope
     * @param $data array of data to be sent
     * @param array $messages array of messages
     * @param int $httpCode http response code
     * @param array $HTTPCode array of http header to be in the response
     */
    public function sendData($data, array $messages = [], int $HTTPCode = Response::HTTP_OK, array $headers = [])
    {
        $envelope = [
            'hasError' => false,
            'messages' => $messages,
            'data' => $data,
        ];

        return response($envelope, $HTTPCode, $headers);

    }


    /**
     * @method sendError sends the given errors in an envelope
     * @param array $messages array of messages
     * @param int $httpCode http response code
     * @param array $HTTPCode array of http header to be in the response
     */
    
    public function sendError( $messages, $HTTPCode = 500, $headers = [])
    {
        $envelope = [
            'hasError' => true,
            'messages' => $messages
        ];

        return response($envelope, $HTTPCode, $headers);
    }
}
