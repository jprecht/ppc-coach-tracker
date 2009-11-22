<?php
class StringUtils {
    function unicodeToAscii($str) {
        $newStr = "";
        for($i=0;$i<strlen($str); $i++) {
            $ord = ord($str[$i]);
            if(($ord < 127 && $ord > 31) || $ord == 9 || $ord == 10 || $ord == 13) {
                $newStr .= $str[$i];
            }
        }
        return $newStr;
    }

    function asciiToUnicode($str) {
        $newStr = "";
        $str = "$str";
        for($i=0;$i<strlen($str); $i++) {
            $newStr .= $str[$i];
            $newStr .= chr(0);
        }
        return $newStr;
    }
}
?>