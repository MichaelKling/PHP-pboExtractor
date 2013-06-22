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
        $pboString = file_get_contents($pboFile, FILE_USE_INCLUDE_PATH);
        return PBOExtractor::extractFromString($fileToExtract,$pboString);
    }

    public static function extractFromString($fileToExtract,$pboString) {
        PBOExtractor::$lastError = array('code' => 0, 'message' => "");
/*
        $then = microtime();
        printf("MEM:  %d<br/>\nPEAK: %d<br/>\n", memory_get_usage(), memory_get_peak_usage());
*/
        $i = 0;
        $headers = array();
        $searchedHeader = array();
        //Consume all the headers
        do {
            $header = PBOHeaderEntry::createByConsumeString($pboString);
            $headers[$i] = $header;
            if ($header->filename == $fileToExtract) {
                $searchedHeader = $header;
            }
            $i++;
        } while (strlen($pboString) > 0 && !$header->isEndOfHeader());
/*
        $now = microtime();
        printf("MEM:  %d<br/>\nPEAK: %d<br/>\n", memory_get_usage(), memory_get_peak_usage());
        echo "Header Elapsed: ".($now-$then)."<br/>\n";
*/
        if (empty($searchedHeader)) {
            PBOExtractor::$lastError = array('code' => 1, 'message' => "Could not find ".$fileToExtract);
            return false;
        }
/*
        $then = microtime();
        printf("MEM:  %d<br/>\nPEAK: %d<br/>\n", memory_get_usage(), memory_get_peak_usage());
*/
        $file = null;
        foreach ($headers as $header) {
            if ($header == $searchedHeader) {
                $file = PBOFileEntry::createByConsumeString($pboString,$header);
                break;
            } else {
                PBOFileEntry::consumeFile($pboString,$header);
            }
        }
/*
        $now = microtime();
        printf("MEM:  %d<br/>\nPEAK: %d<br/>\n", memory_get_usage(), memory_get_peak_usage());
        echo "Files Elapsed: ".($now-$then)."<br/>\n";
*/
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