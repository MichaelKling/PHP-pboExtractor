<?php
/**
 * Created by Ascendro S.R.L.
 * User: Michael
 * Date: 22.06.13
 * Time: 12:38
 */
class RAPConverter
{
    public $binaryString;
    public $result;

    protected function __construct(&$binaryString)
    {
        $this->binaryString = $binaryString;
    }

    public static function unRap(&$binaryString) {
        $rapConverter = new RAPConverter($binaryString);
        return $rapConverter->convert();
    }

    public static function isRapified(&$string)
    {
        if (strlen($string) > 4 && substr($string,0,4) == "\0raP") {
            return true;
        } else {
            return false;
        }
    }

    public function convert() {
        $this->result = $this->binaryString;
        //NOT IMPLEMENTED YET
        //Informatins about how to convert: http://community.bistudio.com/wiki/raP_File_Format_-_Elite
        return $this->result;
    }
}
