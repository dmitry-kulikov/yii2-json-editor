<?php

namespace kdn\yii2;

/**
 * Class OutputHelper.
 * @package kdn\yii2
 */
class OutputHelper
{
    /**
     * Call a user function given with an array of parameters and catch its output.
     * @param callable $callback function to be called
     * @param array $arguments parameters to be passed to the function, as an indexed array
     * @return array array containing result returned by function and its output.
     * @throws \Exception|\Throwable
     */
    public static function catchOutput($callback, $arguments = [])
    {
        $obInitialLevel = ob_get_level();
        ob_start();
        ob_implicit_flush(false);
        try {
            return [
                'result' => call_user_func_array($callback, $arguments),
                'output' => ob_get_clean(),
            ];
        } catch (\Exception $e) {
            static::exceptionHandler($obInitialLevel);
            throw $e;
        } catch (\Throwable $e) { // before PHP 7, \Exception did not implement the \Throwable interface
            static::exceptionHandler($obInitialLevel);
            throw $e;
        }
    }

    /**
     * Closes the output buffer opened above if it has not been closed already.
     * @param int $obInitialLevel initial level of the output buffering mechanism
     */
    protected static function exceptionHandler($obInitialLevel)
    {
        while (ob_get_level() > $obInitialLevel) {
            if (!@ob_end_clean()) {
                ob_clean();
            }
        }
    }
}
