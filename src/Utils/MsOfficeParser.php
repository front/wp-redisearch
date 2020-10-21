<?php

namespace WpRediSearch\Utils;

/*
* this class is used to convert any doc,docx file to simple text format.
* some codes from doc2txt.class by Gourav Mehta
* Example of usage
* $docObj = new DocxConversion("test.docx");
* $docObj = new DocxConversion("test.xlsx");
* echo $docObj->convertToText();
*/
class MsOfficeParser {
  public static function getText( $file_path ) {
    if(isset($file_path) && !file_exists($file_path)) {
      return "File Not exists";
    }
    $fileInfo = pathinfo($file_path);
    $file_ext  = $fileInfo['extension'];
    if($file_ext == "doc" || $file_ext == "docx" || $file_ext == "xlsx" || $file_ext == "pptx"){
      if($file_ext == "doc") {
        return self::parseDoc($file_path);
      } elseif($file_ext == "docx") {
        return self::parseDocx($file_path);
      } elseif($file_ext == "xlsx") {
        return self::parseXlsx($file_path);
      } elseif($file_ext == "pptx") {
        return self::parsePptx($file_path);
      }
    } else {
        return "Unsupported file type";
    }
  }

  /**
   * Extract Microsoft Office Word before 2007 file contents
   *
   * @param string $file_path
   * @return string $file_content
   */
  private static function parseDoc( $file_path ) {
    $fileHandle = fopen($file_path, "r");
    $line = @fread($fileHandle, filesize($file_path));
    $lines = explode(chr(0x0D),$line);
    $file_content = "";
    foreach($lines as $thisline) {
      $pos = strpos($thisline, chr(0x00));
      if (($pos !== FALSE)||(strlen($thisline)==0)) {
        } else {
          $file_content .= $thisline." ";
        }
      }
     $file_content = preg_replace("/[^a-zA-Z0-9\s\,\.\-\n\r\t@\/\_\(\)]/","",$file_content);
    return $file_content;
  }

  /**
   * Extract Microsoft Office Word 2007+ file contents
   *
   * @param string $file_path
   * @return string $file_content
   */
  private static function parseDocx( $file_path ) {
    $file_content = '';
    $zip = zip_open( $file_path );
    if (!$zip || is_numeric( $zip ) ) return false;
    while ( $zip_entry = zip_read( $zip ) ) {
      if ( zip_entry_open( $zip, $zip_entry ) == FALSE ) continue;
      if ( zip_entry_name( $zip_entry ) != "word/document.xml" ) continue;
      $file_content .= zip_entry_read( $zip_entry, zip_entry_filesize( $zip_entry ) );
      zip_entry_close( $zip_entry );
    }// end while
    zip_close( $zip );
    $file_content = str_replace('</w:r></w:p></w:tc><w:tc>', ' ', $file_content);
    $file_content = str_replace('</w:r></w:p>', ' ', $file_content);
    $file_content = strip_tags($file_content);
    return $file_content;
  }
  
  /**
  * Extract Microsoft Office Excel file contents
  *
  * @param string $file_path
  * @return string $file_content
  */
  private static function parseXlsx( $file_path ) {
    $xml_filename = "xl/sharedStrings.xml"; //content file name
    $zip_handle = new ZipArchive;
    $file_content = "";
    if (true === $zip_handle->open($file_path)) {
      if (($xml_index = $zip_handle->locateName($xml_filename)) !== false) {
        $file_content = $zip_handle->getFromIndex($xml_index);
      } else {
        $file_content .="";
      }
      $zip_handle->close();
    } else {
    $file_content .="";
    }
    return $file_content;
  }

  /**
   * Extract Microsoft Office Powerpoint file contents
   *
   * @param string $file_path
   * @return string $file_content
   */
  private static function parsePptx( $file_path ) {
    $zip_handle = new ZipArchive;
    $file_content = "";
    if ( true === $zip_handle->open( $file_path ) ) {
      $slide_number = 1; //loop through slide files
      while(($xml_index = $zip_handle->locateName( "ppt/slides/slide".$slide_number.".xml")) !== false ){
        $xml_datas = $zip_handle->getFromIndex( $xml_index );
        $dom_document = new DOMDocument();
        $xml_handle = $dom_document->loadXML( $xml_datas, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING );
        $file_content .= strip_tags( $xml_handle->saveXML() );
        $slide_number++;
      }
      if ( $slide_number == 1 ) {
        $file_content .="";
      }
      $zip_handle->close();
    } else {
      $file_content .="";
    }
    return $file_content;
  }
}