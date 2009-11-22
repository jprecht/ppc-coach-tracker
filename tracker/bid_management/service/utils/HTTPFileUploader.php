<?php
class HTTPFileUploader {
    function postFile($host, $port, $resource, $filename) {
        $lineFeed = "\r\n";
        $marker = "--";
        $boundary = time();

        $file = file_get_contents($filename);
        $body = "$marker$boundary$lineFeed";
        $body .= "Content-Disposition: form-data; name=\"_fUpload\"; filename=\"$filename\"$lineFeed";
        $body .= $lineFeed.$file.$lineFeed;
        $body .= "$marker$boundary$marker$lineFeed";

        $head = "POST $resource HTTP/1.0$lineFeed";
        $head .= "Content-Type: multipart/form-data;boundary=$boundary$lineFeed";
        $head .= "Content-Length: ".strlen($body).$lineFeed;
        //$head .= "Host: $host$lineFeed";

        $request = $head.$lineFeed.$body;
        
        $fp = fsockopen($host, $port);

        if ( ! $fp ) {
            return false;
        }

        $page = "";
        fputs ( $fp, $request );
        while ( ! feof( $fp ) ) {
            $page .= fgets( $fp, 1024 );
        }
        fclose( $fp );

        $pos = strpos($page, "\r\n\r\n");
        $page = substr($page, $pos);

        return $page;
    }
}
?>