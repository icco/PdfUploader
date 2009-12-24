<?php
require "config.php";
require "s3-php5-curl/S3.php";

define("BASE_BUCKET", "/resume");

class S3Wrapper {
   public static function listBuckets() {
      $s3 = new S3(AWS_ACCESS_KEY, AWS_SECRET_KEY);
      return $s3->listBuckets();
   }
}

?>
