<?php
/**
 * Smarthome Function Helper.
 *
 * @package     Smarthome
 * @subpackage  Lib_Smarthome
 * @author      Robert Gies <mail@rgies.com>
 * @copyright   Copyright © 2014 by Robert Gies
 * @license     New BSD License
 * @date        2014-01-10
 */

/**
 * Class Lib_Core_FunctionHelper.
 */
class Lib_Core_FunctionHelper
{
    /**
     * Check if null values found in the result array.
     *
     * @param array $result Result from script execution
     * @return boolean True if valid
     */
    public static function validateScriptResult(array &$result)
    {
        $ret = true;
        foreach ($result as $key=>$value)
        {
            if ($value == 'null')
            {
                $result[$key] = null;
                $ret = false;
            }
        }

        return $ret;
    }

}