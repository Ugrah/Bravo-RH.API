<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

trait DataReturn {

    /**
     * @deprecated
     * @param $params
     * @return array
     */
    public function successDataReturn($message,$data=null) {
        $return = ['success'=> true,'message'=> $message,'code'=> ResponseAlias::HTTP_OK];
        if($data)
            $return+=['data' => $data];

        return $return;
    }

    /**
     * @deprecated
     * @param null $data
     * @param string $message
     * @return array
     */
    public function errorDataReturn($data=null, $message= 'error') {
        $return = ['success'=> false,'message'=> $message];
        if($data){
            if(gettype($data) == 'string')
                $data = [$data];
            $return+=['data' => $data];
        }
        return $return;
    }

    /**
     * @deprecated
     * @param $params
     * @return array
     */
    public function errorMessageDataReturn($message) {
        return ['success'=> false,'message'=> $message];
    }

    /**
     * Generate success type response.
     *
     * Returns the success data and message if there is any error
     *
     * @param array $data
     * @param string $message
     * @param integer $status_code
     * @return JsonResponse
     */
    public function responseSuccess($data, $message = "Successful", $status_code = ResponseAlias::HTTP_OK): JsonResponse
    {
        return response()->json([
            'success'  => true,
            'message' => $message,
            'errors'  => null,
            'data'    => $data,
        ], $status_code);
    }

    /**
     * Generate Error response.
     *
     * Returns the errors data if there is any error
     *
     * @param array $errors
     * @param string $message
     * @param int $status_code
     * @return JsonResponse
     */
    public function responseError($errors, $message = 'Data is invalid', $status_code = ResponseAlias::HTTP_BAD_REQUEST): JsonResponse
    {
        return response()->json([
            'success'  => false,
            'message' => $message,
            'errors'  => $errors,
            'data'    => null,
        ], $status_code);
    }

}
