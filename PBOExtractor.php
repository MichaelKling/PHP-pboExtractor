<?php
/**
 * Created by Ascendro S.R.L.
 * User: Michael
 * Date: 19.06.13
 * Time: 08:44
 */

define('PBOEXTRACTOR_BASE', dirname(__FILE__) . '/');

require_once PBOEXTRACTOR_BASE . 'PBODataTypes.php';
require_once PBOEXTRACTOR_BASE . 'RAPConverter.php';

class PBOExtractor {
    public static $lastError = array('code' => 0, 'message' => "");

    protected function __construct()
    {
    }

    public static function getLastErrorCode() {
        return PBOExtractor::$lastError['code'];
    }

    public static function getLastErrorMessage() {
        return PBOExtractor::$lastError['message'];
    }

    public static function lastRunSuccesfull() {
        return PBOExtractor::$lastError['code'] == 0;
    }

    public static function extract($fileToExtract,$pboFile) {
        $pboStream = fopen($pboFile, 'r');
        $result = PBOExtractor::extractFromStream($fileToExtract,$pboStream);
        fclose($pboStream);
        return $result;
    }

    public static function extractFromStream($fileToExtract,$pboStream) {
        PBOExtractor::$lastError = array('code' => 0, 'message' => "");

        $i = 0;
        $headers = array();
        $searchedHeader = array();
        //Consume all the headers
        do {
            $header = PBOHeaderEntry::createByConsumeStream($pboStream);
            $headers[$i] = $header;
            if ($header->filename == $fileToExtract) {
                $searchedHeader = $header;
            }
            $i++;
        } while (!feof($pboStream) && !$header->isEndOfHeader());

        if (empty($searchedHeader)) {
            PBOExtractor::$lastError = array('code' => 1, 'message' => "Could not find ".$fileToExtract);
            return false;
        }

        $file = null;
        foreach ($headers as $header) {
            if ($header == $searchedHeader) {
                $file = PBOFileEntry::createByConsumeStream($pboStream,$header);
                break;
            } else {
                PBOFileEntry::consumeFile($pboStream,$header);
            }
        }

        if ($file->isCompressed) {
            PBOExtractor::$lastError = array('code' => 2, 'message' => "File ".$fileToExtract." is compressed. Compressed files are not supported yet.");
            return $file;
        }

        if ($file->isRapified) {
            PBOExtractor::$lastError = array('code' => 3, 'message' => "File ".$fileToExtract." is raPified. raPified files are not supported yet.");
            return $file;
        }

        return $file;
    }

}