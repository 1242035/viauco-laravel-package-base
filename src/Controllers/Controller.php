<?php

namespace Viauco\Base\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Notifications\Notifiable;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, Notifiable;

    public function __construct()
    {
        //$this->middleware('auth');
    }

    protected function _success($data)
    {
        $response = [
            'success' => true,
            'code'    => 200,
            'type'    => 'success',
            'message' => 'Success',
            'data'    => $data
        ];
        return response()->json( $response );
    }

    protected function _error($error = null, $code = 500, $type = 'error', $message='error')
    {
        return response()->json([
            'success' => false,
            'code'    => $code,
            'type'    => $type,
            'message' => $message,
            'params'  => request()->all(),
            'error'   => $error
        ]);
    }

    protected function _notFound($error = null, $code = 404, $type = 'notFound', $message='Not found')
    {
        return response()->json([
            'success' => false,
            'code'    => $code,
            'type'    => $type,
            'message' => $message,
            'params'  => request()->all(),
            'error'   => $error
        ]);
    }

    protected function _permissionDeny($error = null, $code = 403, $type = 'error', $message='permission deny')
    {
        return response()->json([
            'success' => false,
            'code'    => $code,
            'type'    => $type,
            'message' => $message,
            'params'  => request()->all(),
            'error'   => $error
        ]);
    }
}
