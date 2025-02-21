<?php

namespace App\Traits;

trait MyResponse
{
    public function response($success = FALSE, $statusCode = 500, $message = 'Internal Server Error', $rowData = []) {
        return [
            'success' => $success,
            'statusCode' => $statusCode,
            'message' => $message,
            'rowData' => $rowData,
        ];
    }
}