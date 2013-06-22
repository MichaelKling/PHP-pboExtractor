<?php
/**
 * Created by Ascendro S.R.L.
 * User: Michael
 * Date: 22.06.13
 * Time: 12:38
 */
class RAPConverter
{
    public static function isRapified($stream)
    {
        $string = fread($stream,4);
        rewind($stream);
        if (strlen($string) > 4 && substr($string,0,4) == "\0raP") {
            return true;
        } else {
            return false;
        }
    }

}
