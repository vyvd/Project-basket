<?php

/**
 * Helper class with general helper functions
 */
class UtilHelpers
{
    /**
     * Validates callback variable
     *
     * Checks if is a closure callback: function ()
     * or class callback: [$this, 'function']
     * and calls the relevant callback
     *
     * Returns config data for callbackHandler function
     *
     * Returns false on error
     * @param $callbackData
     * @return false|string[]
     */
    public static function validateCallback($callbackData)
    {
        $callbackObject = false;
        //Check callback data has 2 array items for a class function callback and sets relevant vars
        if (is_array($callbackData) && count($callbackData) == 2) {
            $callbackObject = $callbackData[0];
            $callbackMethod = $callbackData[1];
        }
        //Check callback is an instance of a Closure function and sets relevant vars
        elseif ($callbackData instanceof \Closure) {
            $callbackMethod = $callbackData;
        }
        else {
            return false;
        }

        //Initialise result data array
        $callbackResult = [
            'status' => 'error'
        ];

        $callbackResult['callback_method'] = $callbackMethod;
        $callbackResult['callback_object'] = $callbackObject;

        if ($callbackMethod instanceof \Closure) {
            $callbackResult['status'] = 'success';
            $callbackResult['callback_type'] = 'closure';
        } elseif (
            class_exists(get_class($callbackObject)) &&
            method_exists($callbackObject, $callbackMethod)
        ) {
            $callbackResult['status'] = 'success';
            $callbackResult['callback_type'] = 'class_method';
        }
        return $callbackResult;
    }

    /**
     * Handles callback data
     * Checks if is a closure callback: function ()
     * or class callback: [$this, 'function']
     * and calls the relevant callback
     *
     * Returns false on error
     *
     * @param $callbackData
     * @param ...$callbackMethodParams
     * @return false|mixed|string
     */
    public static function callbackHandler($callbackData, ...$callbackMethodParams)
    {
        $validateCallback = self::validateCallback($callbackData);
        $callbackResult = false;
        if (!is_array($validateCallback)) {
            return false;
        }
        if (
            !isset($validateCallback['status']) ||
            $validateCallback['status'] === 'error' ||
            !isset($validateCallback['callback_type'])
        ) {
            return false;
        }

        $callbackMethod = $validateCallback['callback_method'];
        $callbackObject = $validateCallback['callback_object'];
        switch ($validateCallback['callback_type']) {
            case 'closure':
                if ($callbackMethod instanceof \Closure) {
                    $callbackResult = $callbackMethod(...$callbackMethodParams);
                }
                break;
            case 'class_method':
                if (
                    class_exists(get_class($callbackObject)) &&
                    method_exists($callbackObject, $callbackMethod)
                ) {
                    $callbackResult = $callbackObject->{$callbackMethod}(...$callbackMethodParams);
                }
                break;
            default:
                return $callbackMethod;
        }
        return $callbackResult;
    }

    public static function getNameFromNumber($num) {
        $numeric = ($num - 1) % 26;
        $letter = chr(65 + $numeric);
        $num2 = intval(($num - 1) / 26);
        if ($num2 > 0) {
            return self::getNameFromNumber($num2) . $letter;
        } else {
            return $letter;
        }
    }
}