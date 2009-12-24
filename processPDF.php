<?php
require "config.php";
require "s3-php5-curl/S3.php";

define("BASE_BUCKET", "resumes.cpacm");

class S3Wrapper {
   public static function listBuckets() {
      $s3 = new S3(AWS_ACCESS_KEY, AWS_SECRET_KEY);
      return $s3->listBuckets();
   }

   public static function createBucket($name) {
      $s3 = new S3(AWS_ACCESS_KEY, AWS_SECRET_KEY);
      return $s3->putBucket($name, S3::ACL_PUBLIC_READ);
   }

   public static function putFile($file, $path) {
      $put = $s3->putObject(
         S3::inputFile($file),
         BASE_BUCKET,
         $path,
         S3::ACL_PUBLIC_READ,
         array(),
         array( 
            "Expires" => gmdate("D, d M Y H:i:s T", strtotime("+1 year"))
         )
      );
     
      return $put;
   }
}

Class MainClass {
   public static function log($message) {
      $date = date("c");
      $write = "[{$date}] {$message}";
      $fd = fopen("./notifications.log", 'a');
      fwrite($fd, $write);
      fclose($fd);
   }

   public static function parsePDF($fileArray) {
      // Check to make sure root bucket exists
      if (S3wrapper::createBucket(BASE_BUCKET)) {
         self::log("Created bucket " . BASE_BUCKET);
      }

      // Create folder for this year 
      $folder = "/" . date('Y');

      // Send File
      S3Wrapper::putFile($fileArray[''], $folder);
   }
}
?>
