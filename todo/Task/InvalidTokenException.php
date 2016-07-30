<?php
namespace Task;
use Exception;
class InvalidTokenException extends Exception {
  public function __construct($code = 0, Exception $previous = null) {
      // なんらかのコード

      // 全てを正しく確実に代入する
    parent::__construct('Invalid auth token', $code, $previous);
  }
}
