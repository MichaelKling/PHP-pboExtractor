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

    public static function createByConsumeStream($stream) {
        $pboHeaderEntry = new PBOHeaderEntry();

        $pboHeaderEntry->filename = "";
        do {
            $char = fread($stream,1);
            if ($char != "\0") {
                $pboHeaderEntry->filename .= $char;
            }
        } while (!feof($stream) && $char != "\0");



        $longValue = fread($stream,4);
        $array = unpack("V",$longValue);
        $pboHeaderEntry->packingMethod = $array[1];

        $longValue = fread($stream,4);
        $array = unpack("V",$longValue);
        $pboHeaderEntry->originalSize = $array[1];

        $longValue = fread($stream,4);
        $array = unpack("V",$longValue);
        $pboHeaderEntry->reserved = $array[1];

        $longValue = fread($stream,4);
        $array = unpack("V",$longValue);
        $pboHeaderEntry->timestamp = $array[1];

        $longValue = fread($stream,4);
        $array = unpack("V",$longValue);
        $pboHeaderEntry->datasize = $array[1];

        return $pboHeaderEntry;
    }

    public function isEndOfHeader() {
        return  (($this->filename == "") && ($this->packingMethod != PBOHeaderEntry::PACKINGMETHOD_PRODUCTENTRY) && ($this->datasize == 0));
    }
}

class PBOFileEntry {
    public $content = null;
    public $isCompressed = false;
    public $isRapified = false;
    public $pboHeader = null;
    public static function createByConsumeStream($stream,$header) {
        $file = new PBOFileEntry();

        $file->pboHeader = $header;

        if ($file->content) {
            fclose($file->content);
            $file->content = null;
        }
        $file->content = fopen("php://memory", 'r+');
        if ($header->datasize > 0) {
            stream_copy_to_stream($stream,$file->content,$header->datasize);
        }
        rewind($file->content);

        if ($header->packingMethod == PBOHeaderEntry::PACKINGMETHOD_PACKED) {
            $file->isCompressed = true;
            //Compressed files not supported yet.
            return $file;
        }

        if (RAPConverter::isRapified($file->content)) {
            $file->isRapified = true;
        }

        return $file;
    }

    public static function consumeFile($stream,$header) {
        if ($header->datasize > 0) {
            fread($stream,$header->datasize);
        }
    }

    public function __destruct()
    {

    }
}