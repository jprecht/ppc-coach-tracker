<?php

/**
 * The FileOutputFormat object represents the format of a file.
 *
 * @package ewsPHP
 */
class ewsFileOutputFormat {
	/**#@+
	 * @access private
	 */
	var $fileOutputType;
	var $zipped;
	/**#@-*/
	/**
	 * The FileOutputFormat object represents the format of a file.
	 *
	 * @param string [CSV, CSV_EXCEL, TSV, XML] $fileOutputType
	 * @param boolean $zipped
	 * @access private
	 * @return ewsFileOutputFormat
	 */
	function ewsFileOutputFormat($fileOutputType, $zipped) {
		$this->fileOutputType = $fileOutputType;
		$this->zipped = $zipped;
	}
	
	function getFileOutputType(){
		return $this->fileOutputType;
	}
	function getZipped(){
		return $this->zipped;
	}
}

?>