<?php
namespace lib;
use lib\ToJson;

class JsonArray implements ToJson {
  private $ary;
  public function __construct($ary) {
    $this->ary = $ary;
  }
  public function toArray() {
    $result = array();
    foreach($this->ary as $key => $value) {
      $result[$key] = $value->toArray();
    }
    return $result;
  }
  public function toJson() {
    return json_encode($this->toArray());
  }
}
