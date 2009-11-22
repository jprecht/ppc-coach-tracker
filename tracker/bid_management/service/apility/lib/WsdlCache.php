<?php
  /**
  * A simple container for wrapping remote wsdl file caching
  *
  * @author Google <contact at opensource at google dot com>
  * @access  public
  */
  class APIlityWsdlCache {
    // remote url of wsdl file
    var $url;
    // absolute path of cache file
    var $cacheFile;
    // time in seconds that the cache is good for
    var $cacheLength;
    // indicates if the cache directory is valid
    var $_validPath = false;

    /**
     * This method creates and returns a WsdlCache object
     * when being initialized it validates the cache. If not
     * valid it automatically tries to create it.
     *
     * @param string  The remote url of the wsdl file.
     * @param string  The directory location where cache files are stored.
     * @param long    The number of seconds a cache file is good for.
     *
     * @access public
     */
    function APIlityWsdlCache($url, $dir, $timeout) {
      // remote path to wsdl file
      $this->url = $url;

      // directory to save cache files to
      $dir = $this->checkDirectorySeparator($dir);
      // validate path where cache files are stored
      $this->_validPath = $this->isDirectoryValid($dir);

      // find location of cache WSDL service file
      $this->cacheFile = $dir . md5($url).".wsdl";
      // set the cache timeout length
      $this->cacheLength = $timeout;

      // only attempt rebuilding the cache file if directory is valid
      if ($this->_validPath && !$this->isValid()){
        // if building the cache fails we want to make sure we clean up
        if (!$this->buildCacheFile()){
          unlink($this->cacheFile);
        }
      }
    }

    /**
     * Check to see if the cache file for specified url is valid
     *
     * @return boolean
     * @access public
     */
    function isValid(){
      // if path hasn't been validate we know that nothing is valid
      if (!$this->_validPath) {
        return false;
      }
      // check to see if the cache file exists
      if (!file_exists($this->cacheFile)) {
        return false;
      }
      // check to see if cache file hasn't timed out
      if (!($this->cacheLength > (time()-filemtime($this->cacheFile)))){
        return false;
      }
      return true;
    }

    /**
     * Return absolute path of cache file
     *
     * @return string absolute path of the wsdl cache file
     * @access public
     */
    function getFilePath(){
      return $this->cacheFile;
    }

    /**
     * Checks a directory to see if it is worthy for caching files
     *
     * @param string absolute directory path to check
     * @return boolean returns true if directory exists and is writable
     * @access private
     *
     * Todo: attempt to create directory if it doesn't exist?
     */
    function isDirectoryValid($dir){
      // if the directory is invalid return false
      if (!is_dir($dir)) {
        if (!SILENCE_STEALTH_MODE) trigger_error("<b>APIlity PHP library => Warning: </b>Cache Directory: ".$dir." does not exist or is invalid", E_USER_WARNING);
        return false;
      }
      // can we write to the directory?
      if (!is_writable($dir) && !chmod($dir, 0666)) {
        if (!SILENCE_STEALTH_MODE) trigger_error("<br /><b>APIlity PHP library => Warning: </b>Cache Directory: ".$dir." does not have write privileges", E_USER_WARNING);
        return false;
      }
      return true;
    }

    /**
     * Make sure directory ends in a directory separator
     *
     * @param string absolute directory path to check
     * @return string returns directory path
     * @access private
     */
    function checkDirectorySeparator($dir){
      // check to see if $dir has a Directory Separator on the end if not add it
      if (substr($dir, -1) != DIRECTORY_SEPARATOR) {
        $dir .= DIRECTORY_SEPARATOR;
      }
      return $dir;
    }

    /**
     * Tries to create/re-create cache file
     *
     * @return boolean returns true if creation of cache file was successful
     * @access private
     */
    function buildCacheFile() {
      $cacheHandler = fopen($this->cacheFile, "w");

       // open connection to the Google server via cURL
      $curlConnection = curl_init();
      curl_setopt($curlConnection, CURLOPT_URL, $this->url);
      curl_setopt($curlConnection, CURLOPT_SSL_VERIFYPEER, FALSE);
      curl_setopt($curlConnection, CURLOPT_SSL_VERIFYHOST, FALSE);
      // content length is not returned when using gzip (disabled for now)
      // curl_setopt($curlConnection, CURLOPT_ENCODING, "gzip");
      // no need to capture curls output buffer with this option
      curl_setopt($curlConnection, CURLOPT_FILE, $cacheHandler);

      curl_exec($curlConnection);

      $responseCode = curl_getinfo($curlConnection, CURLINFO_HTTP_CODE);
      $contentLength = curl_getinfo($curlConnection, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

      fclose($cacheHandler);

      // check response code
      if ($responseCode != 200) {
        if (!SILENCE_STEALTH_MODE) trigger_error("<b>APIlity PHP library => Warning: </b>Request for file (".$this->url.") was invalid, HTTP code: ".$responseCode.". The cURL error message is: " . curl_error($curlConnection), E_USER_WARNING);
        curl_close($curlConnection);
        return false;
      }
      // check file size
      if (filesize($this->cacheFile) != $contentLength){
        if (!SILENCE_STEALTH_MODE) trigger_error("<b>APIlity PHP library => Warning: </b>Cache file for: ". $this->url ." has an invalid file size", E_USER_WARNING);
        curl_close($curlConnection);
        return false;
      }

      if (curl_errno($curlConnection)) {
        if (!SILENCE_STEALTH_MODE) trigger_error("<b>APIlity PHP library => Warning: </b>Error while trying to cache wsdl file. The cURL error message is: " . curl_error($curlConnection), E_USER_WARNING);
        curl_close($curlConnection);
        return false;
      }
      // we built the new cache file with no problems
      curl_close($curlConnection);
      return true;
    }
  }
?>