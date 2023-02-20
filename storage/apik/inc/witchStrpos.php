<?php

class witchStrpos {

    public $howMany = 0;
    public $actPos = 0;
    public $returnPos = 0;

    /*
     * position of character 'x' in the text
     */
    public function withcPos($inWhat, $what, $witch) {
        $pos = strpos($inWhat, $what);
        $this->actPos = $this->actPos + $pos;
        $string = substr($inWhat, $pos + 1);
        $this->howMany++;
        if ($this->howMany == $witch) {
            $this->returnPos = $this->actPos;
            $this->init();
        } else {
            $this->withcPos($string, $what, $witch);
        }
    }

    /*
     * return position
     */
    public function getReturnPos() {
        return $this->returnPos;
    }

    public function init() {
        $this->actPos = 0;
        $this->howMany = 0;
    }

    public function getPos($string, $witch)
    {
        $this->withcPos($string , "'", $witch);
        return $this->returnPos;
    }

    public function getSubstrMark($string, $witch)
    {
        $beginPos = $this->getPos($string, $witch);
        $endPos = $this->getPos($string, $witch + 1);

        return substr($string, $beginPos + ($witch - 1) , ($endPos + 2) - $beginPos);
    }

    public function getSubstr($string, $witch)
    {
        $beginPos = $this->getPos($string, $witch);
        $endPos = $this->getPos($string, $witch + 1);

        return substr($string, $beginPos + $witch , $endPos  - $beginPos);
    }

}


