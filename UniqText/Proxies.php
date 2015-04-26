<?php
/**
 * Class Proxies
 * include "Proxies.php";
 * include "Browser.php";
 * $test = new Proxies("0Vuo4IzN7FnECznmQjcv");
 * $temp = $test->next();
 * while($temp != null) {
 *      echo $temp."<br>";
 *      $temp = $test->next();
 * }
 */
include "Browser.php";
class Proxies {
    private $i;
    private  $kkey;
    private $max_proxy = 15;
    public  $array;

    function __construct($key)
    {
        $this->kkey =$key;
        $this->array = Array();
        $this->i = 0;

        $browser = new Browser(null,"http://api.best-proxies.ru/feeds/proxylist.txt?key=".$key."&type=http",null);
        $html = $browser->get();
        preg_match_all("|([^:]+[:]{1}[^:\n]+)|", $html, $temp);
        $this->array = $temp[0];
    }
    public function get(){
        if($this->i >= count($this->array)) {
            return null;
        }
        return trim($this->array[$this->i]);
    }
    /**
     * @return string
     * Returned null if list is ended
     */
    public function next() {
        if($this->i >= count($this->array) || $this->i > $this->max_proxy) {
            $this->array = Array();
            $this->i = 0;

            $browser = new Browser(null,"http://api.best-proxies.ru/feeds/proxylist.txt?key=".$this->kkey."&type=http",null);
            $html = $browser->get();
            preg_match_all("|([^:]+[:]{1}[^:\n]+)|", $html, $temp);
            $this->array = $temp[0];
            return null;
        }
        return trim($this->array[$this->i++]);
    }

}