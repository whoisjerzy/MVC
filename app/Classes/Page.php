<?php

namespace BARTENDER\Classes;

class Page
{
    public function outputJSON($json)
    {
        header('Content-Type: application/json');
        echo $json;
    }

    private $TagAr = array("@@base@@" => "");
    private $RepeatAr = array();

    public function LoadAPartFromFile($tag, $s, $PartTag)
    {
        $s1 = file_get_contents($s);

        $PartTag = is_null($PartTag) ? "" : $PartTag;

        $p = strpos($s1, $PartTag);
        if ($p === false) {
            $s1 = "";
        } else {
            $s1 = substr($s1, $p + strlen($PartTag), 10000000);
            $p = strpos($s1, $PartTag);
            if ($p) $s1 = substr($s1, 0, $p);
        }

        if (isset($this->TagAr[$tag]))
            $this->TagAr[$tag] .= $s1;
        else
            $this->TagAr += array($tag => $s1);
        if (strpos($s1, "@@StartRow-"))
            $this->FindRepeatSections();
    }

    public function Echo($tag, $s)
    {
        if (isset($this->TagAr[$tag]))
            $this->TagAr[$tag] .= $s;
        else
            $this->TagAr += array($tag => $s);

        $s = is_null($s) ? "" : $s;
        if (strpos($s, "@@StartRow-"))
            $this->FindRepeatSections();
    }

    private function FindRepeatSections()
    {
        $f = 1;
        $c = 0;
        while (($f == 1) && ($c < 5)) {
            $f = 0;
            foreach ($this->TagAr as $key => $value) {
                while (strpos($value, "@@StartRow-")) {
                    $p = strpos($value, "@@StartRow-");
                    $c++;
                    $f = 1;
                    $s1 = substr($value, $p + 11, 10000000);
                    $p = strpos($s1, "@@");
                    $rname = substr($s1, 0, $p);
                    $StartName = "@@StartRow-" . $rname . "@@";
                    $EndName = "@@EndRow-" . $rname . "@@";

                    $p = strpos($value, $StartName);
                    $s1 = substr($value, 0, $p);
                    $s2 = substr($value, $p + strlen($StartName), 10000000);

                    $p = strpos($s2, $EndName);
                    $Middle = substr($s2, 0, $p);
                    $s2 = substr($s2, $p + strlen($EndName), 10000000);

                    $this->TagAr[$key] = $s1 . "@@" . $rname . "@@" . $s2;
                    $value = $this->TagAr[$key];
                    if (!isset($this->RepeatAr["@@" . $rname . "@@"]))
                        $this->RepeatAr["@@" . $rname . "@@"] = array($Middle, 0);
                }
            }
        }
    }

    public function AddRow($tag, $arr)
    {
        $s = $this->RepeatAr[$tag][0];
        foreach ($arr as $key => $value) {
            $s = str_replace($key, $value, $s);
        }
        $this->Echo($tag, $s);
        $this->RepeatAr[$tag][1] += 1;
    }

    public function PrintPage()
    {
        $f = 1;
        $c = 1;
        while ($f == 1 && $c < 15) {
            $c++;
            $f = 0;
            foreach ($this->TagAr as $key => $value) {
                foreach ($this->TagAr as $key1 => $value1) {
                    if (!($key == $key1)) {

                        $key1 = is_null($key1) ? "" : $key1;
                        $this->TagAr[$key] = is_null($this->TagAr[$key]) ? "" : $this->TagAr[$key];

                        if (strpos($this->TagAr[$key], $key1)) {
                            $value1 = is_null($value1) ? "" : $value1;
                            $this->TagAr[$key] = str_replace($key1, $value1, $this->TagAr[$key]);
                            // $f = 1;
                        }
                    }
                }
            }
        }
        foreach ($this->RepeatAr as $key => $value) {
            if ($value[1] == 0)
                $this->TagAr["@@base@@"] = str_replace($key, "", $this->TagAr["@@base@@"]);
        }
        // ~translate~Πελατες~Customers~
        if (isset($_SESSION["lang"])) {
            while (strpos($this->TagAr['@@base@@'], "~translate~")) {
                $p = strpos($this->TagAr['@@base@@'], "~translate~");
                $p1 = $p + 10;
                $p2 = strpos($this->TagAr['@@base@@'], "~", $p1 + 1);
                $p3 = strpos($this->TagAr['@@base@@'], "~", $p2 + 1);
                if ($_SESSION["lang"] == 0)  // greek
                    $this->TagAr['@@base@@'] = substr($this->TagAr['@@base@@'], 0, $p) .
                        substr($this->TagAr['@@base@@'], $p1 + 1, $p2 - $p1 - 1) .
                        substr($this->TagAr['@@base@@'], $p3 + 1, 1000000);
                if ($_SESSION["lang"] == 1)  // english
                    $this->TagAr['@@base@@'] = substr($this->TagAr['@@base@@'], 0, $p) .
                        substr($this->TagAr['@@base@@'], $p2 + 1, $p3 - $p2 - 1) .
                        substr($this->TagAr['@@base@@'], $p3 + 1, 1000000);
            }
        }
        echo ($this->TagAr['@@base@@']);
    }
}
