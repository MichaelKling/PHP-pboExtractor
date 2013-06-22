<?php
/**
 * Created by Ascendro S.R.L.
 * User: Michael
 * Date: 19.06.13
 * Time: 08:44
 */


class PBOHeaderEntry {
    const PACKINGMETHOD_UNCOMPRESSED = 0x00000000;
    const PACKINGMETHOD_PACKED = 0x43707273;
    const PACKINGMETHOD_PRODUCTENTRY = 0x56657273;

    public $filename;
    public $packingMethod;
    public $originalSize;
    public $reserved;
    public $timestamp;
    public $datasize;

    public static function createByConsumeString(&$string) {
        //Taking out a buffer in order to not
        $temporaryHeaderString = substr( $string, 0, 100 + 4*5);

        $pboHeaderEntry = new PBOHeaderEntry();

        $fileNameLength = strpos( $temporaryHeaderString, "\0");
        $pboHeaderEntry->filename = substr( $temporaryHeaderString, 0, $fileNameLength);
        $temporaryHeaderString = substr( $temporaryHeaderString, $fileNameLength+1);

        $array = unpack("V",$temporaryHeaderString);
        $pboHeaderEntry->packingMethod = $array[1];
        $temporaryHeaderString = substr( $temporaryHeaderString, 4);

        $array = unpack("V",$temporaryHeaderString);
        $pboHeaderEntry->originalSize = $array[1];
        $temporaryHeaderString = substr( $temporaryHeaderString, 4);

        $array = unpack("V",$temporaryHeaderString);
        $pboHeaderEntry->reserved = $array[1];
        $temporaryHeaderString = substr( $temporaryHeaderString, 4);

        $array = unpack("V",$temporaryHeaderString);
        $pboHeaderEntry->timestamp = $array[1];
        $temporaryHeaderString = substr( $temporaryHeaderString, 4);

        $array = unpack("V",$temporaryHeaderString);
        $pboHeaderEntry->datasize = $array[1];
        $temporaryHeaderString = substr( $temporaryHeaderString, 4);

        $string = substr( $string, $fileNameLength+1 + 4*5);

        return $pboHeaderEntry;
    }

    public function isEndOfHeader() {
        return  (($this->filename == "") && ($this->packingMethod != PBOHeaderEntry::PACKINGMETHOD_PRODUCTENTRY) && ($this->datasize == 0));
    }
}

class PBOFileEntry {
    public $rawData = "";
    public $content = "";
    public $isCompressed = false;
    public $isRapified = false;
    public static function createByConsumeString(&$string,$header) {
        $file = new PBOFileEntry();
        $file->rawData = substr($string,0,$header->datasize);
        $string = substr($string,$header->datasize);

        if ($header->packingMethod == PBOHeaderEntry::PACKINGMETHOD_PACKED) {
            $file->isCompressed = true;
            //Compressed files not supported yet.
            return $file;
        }

        $file->isRapified = $file->isRapified();
        if ($file->isRapified) {
            $file->content =  RAPConverter::unRap($file->rawData);
        } else {
            $file->content = &$file->rawData;
        }
        return $file;
    }

    public static function consumeFile(&$string,$header) {
        $string = substr($string,$header->datasize);
        return $string;
    }

    public function isRapified() {
        return RAPConverter::isRapified($this->rawData);
    }
}