<?php
function getstr($string, $length, $encoding  = 'utf-8') {  
$string = trim($string);  
    if($length && strlen($string) > $length) {  
            //截断字符  
            $wordscut = '';  
            if(strtolower($encoding) == 'utf-8') {  
                //utf8编码  
                $n = 0;  
                $tn = 0;  
                $noc = 0;  
                while ($n < strlen($string)) {  
                    $t = ord($string[$n]);  
                    if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {  
                        $tn = 1;  
                        $n++;  
                        $noc++;  
                    } elseif(194 <= $t && $t <= 223) {  
                        $tn = 2;  
                        $n += 2;  
                        $noc += 2;  
                    } elseif(224 <= $t && $t < 239) {  
                        $tn = 3;  
                        $n += 3;  
                        $noc += 2;  
                    } elseif(240 <= $t && $t <= 247) {  
                        $tn = 4;  
                        $n += 4;  
                        $noc += 2;  
                    } elseif(248 <= $t && $t <= 251) {  
                        $tn = 5;  
                        $n += 5;  
                        $noc += 2;  
                    } elseif($t == 252 || $t == 253) {  
                        $tn = 6;  
                        $n += 6;  
                        $noc += 2;  
                    } else {  
                        $n++;  
                    }  
                    if ($noc >= $length) {  
                        break;  
                    }  
                }  
                if ($noc > $length) {  
                    $n -= $tn;  
                }  
                $wordscut = substr($string, 0, $n);  
            } else {  
                for($i = 0; $i < $length - 1; $i++) {  
                    if(ord($string[$i]) > 127) {  
                        $wordscut .= $string[$i].$string[$i + 1];  
                        $i++;  
                    } else {  
                        $wordscut .= $string[$i];  
                    }  
                }  
            }  
            $string = $wordscut;  
        }  
        return trim($string);  
    }
?>
