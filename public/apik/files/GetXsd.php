<?php

class XSD {

    /*
     * field name
     *
     * @param $type - string
     *
     * @return string
     */
    public function fieldLongString($type) {
        $pos = strpos($type, "(");
        if (!empty($pos)) {
            $endPos = strpos($type, ")");
            $string = substr($type, $pos + 1, $endPos - ( $pos + 1));
            return $string;
        } else {
            return "";
        }
    }

    /*
     * xsd loader
     *
     * @param $xsdfile - string
     *
     * @return json
     */
    public function makeDataArray($xsdFile) {

        $file = PATH_XML.$xsdFile.'.xsd';
        $xmlfile = file_get_contents($file);
        $xmlfile = preg_replace('/(<\?xml[^?]+?)utf-16/i', '$1utf-8', $xmlfile);
        $xmlfile = str_replace('msprop:', ' ', $xmlfile);

        $dom = new DOMDocument;
        $dom->preserveWhiteSpace = FALSE;
        $dom->loadXML($xmlfile);

        $dom->save(PATH_XML.$xsdFile.'mas.xml');

        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = true;
        $file = PATH_XML.$xsdFile.'mas.xml';
        $doc->load($file);
        $xmlfile = file_get_contents($file);

        $xmlDataString = str_replace($doc->lastChild->prefix.':',"",$xmlfile);
        $ob = simplexml_load_string($xmlDataString);
        $json = json_encode($ob);
        return json_decode($json, true);

    }

    /*
     * json processing
     *
     * @param $xsdfile - string
     *
     * @return $array
     */
    public function getXSD ($xsdFile) {

        $phpDataArray = $this->makeDataArray($xsdFile);

        $keys = array_keys($phpDataArray);
        $values = array_values($phpDataArray);
        $must = true;
        $array_merged = array();
        $fields = array();

        for ( $i = 0; $i < count($keys); $i++) {
            $keys1 = array_keys($values[$i]);
            $values1 = array_values($values[$i]);
            for ($j = 0; $j < count($values1); $j++) {
                if (gettype($values1[$j]) == "array") {
                    $keys2 = array_keys($values1[$j]);
                    $values2 = array_values($values1[$j]);
                    for ($k = 0; $k < count($values2); $k++) {
                        if ( gettype($values2[$k]) == "array") {
                            $keys3 = array_keys($values2[$k]);
                            $values3 = array_values($values2[$k]);
                            for ($l = 0; $l < count($values3); $l++) {
                                if( gettype($values3[$l]) == "array") {
                                    $keys4 = array_keys($values3[$l]);
                                    $values4 = array_values($values3[$l]);
                                    for ( $m = 0; $m < count($values4); $m++) {
                                        if (gettype($values4[$m]) == "array") {
                                            $keys5 = array_keys($values4[$m]);
                                            $values5 = array_values($values4[$m]);
                                            for( $n =0; $n < count($values5); $n++) {
                                                if (gettype($values5[$n]) == "array") {
                                                    $keys6 = array_keys($values5[$n]);
                                                    $values6 = array_values($values5[$n]);
                                                    for ($o = 0; $o < count($values6); $o++) {
                                                        if ( gettype($values6[$o]) != "array") {
                                                            if ( $values6[$o] != 'PlugIn') {
                                                                $must = true;
                                                                $tableName = $values6[$o];
                                                                $fields = array();
                                                            } else {
                                                                $must = false;
                                                            }
                                                        }
                                                        if (gettype($values6[$o]) == "array" && $must) {
                                                            $keys7 = array_keys($values6[$o]);
                                                            $values7 = array_values($values6[$o]);
                                                            for ($p = 0; $p < count($values7); $p++) {
                                                                if (gettype($values7[$p]) == "array" ) {
                                                                    $keys8 = array_keys($values7[$p]);
                                                                    $values8 = array_values($values7[$p]);
                                                                    for ($q = 0; $q < count($values8); $q++) {
                                                                        if (gettype($values8[$q]) == "array") {
                                                                            $keys9 = array_keys($values8[$q]);
                                                                            $values9 = array_values($values8[$q]);
                                                                            for ($r = 0; $r < count($values9); $r++) {
                                                                                if (gettype($values9[$r]) == "array") {
                                                                                    $keys10 = array_keys($values9[$r]);
                                                                                    $values10 = array_values($values9[$r]);
                                                                                    $any = array($keys10[0] => $values10[0], $keys10[10] => $values10[10], $keys10[11] => $this->fieldLongString($values10[10]));
                                                                                    array_push($fields, $any);
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                    if (isset($fields) && !empty($fields)) {
                                                        $k = array([ 'table' => $tableName, 'value' => $fields]);
                                                        $array_merged = array_merge($array_merged, $k);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            return $array_merged;

                        }
                    }
                }
            }
        }
    }
}
