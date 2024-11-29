<?php

namespace App\Http\Helpers;

use Illuminate\Http\Exceptions\HttpResponseException;

class Helper
{
    public static function sendError($message, $errors = [], $code = 401)
    {
        $response = ['message' => $message, 'status' => $code];

        if (!empty($errors)) {
            $response['data'] = $errors;
        }

        throw new HttpResponseException(response()->json($response));
    }

    public static function replaceField($data, $replaceData = [], $newFields = [])
    {
        // replce fields
        foreach ($replaceData as $string1 => $string2) {
            if (isset($data[$string1])) {
                $data[$string2] = $data[$string1];
                unset($data[$string1]);
            }
        }
        // add new fields
        foreach ($newFields as $key => $value) {
            $data[$key] = $value;
        }

        return $data;
    }
}
