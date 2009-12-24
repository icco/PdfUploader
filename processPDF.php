<?php
require "config.php";
require "s3-php5-curl/S3.php";

define("BASE_BUCKET", "resumes.cpacm");

// Deal with uploaded content
MainClass::parsePDF($_FILES['file'], $_POST['fname'], $_POST['lname']);
header('Location: .');

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
      $s3 = new S3(AWS_ACCESS_KEY, AWS_SECRET_KEY);
      $acl = S3::ACL_PUBLIC_READ;
      $bucketName = BASE_BUCKET;

      $put = $s3->putObject(
         S3::inputFile($file),
         $bucketName,
         $path,
         $acl
      );

      return $put;
   }

   public static getFileInfo($path) {
      $s3 = new S3(AWS_ACCESS_KEY, AWS_SECRET_KEY);
      $bucketName = BASE_BUCKET;
      $info = $s3->getObjectInfo($bucketName, $path);
      return "S3::getObjecInfo(): Info for {$bucketName}/".$path.': '.print_r($info, 1);
   }
}

Class MainClass {
   public static function log($message) {
      $date = date("c");
      $write = "[{$date}] {$message}\n";
      $fd = fopen("./notifications.log", 'a');
      fwrite($fd, $write);
      fclose($fd);
   }

   public static function parsePDF($fileArray, $fname, $lname) {
      // Check to make sure root bucket exists
      if (S3wrapper::createBucket(BASE_BUCKET)) {
         self::log("Created bucket " . BASE_BUCKET);
      }

      if (is_null($fname) || is_null($lname) || is_null($fileArray))
         return false;

      // Create folder for this year 
      $folder = date('Y') . "/";
      $fileName = "{$fname}{$lname}.pdf";

      // Send File
      $ret = S3Wrapper::putFile($fileArray['tmp_name'], $folder.$fileName);
      self::log(S3Wrapper::getFileInfo($folder.$fileName));

      if ($ret)
         self::log("SUCCESS: Put of {$fileName} to {$folder}.");
      else
         self::log("FAILURE: Put of {$fileName} to {$folder}.");

      return $ret;
   }
}
?>
