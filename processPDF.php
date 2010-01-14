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

   public static function listBucket($name, $folder) {
      $s3 = new S3(AWS_ACCESS_KEY, AWS_SECRET_KEY);
      if (($contents = $s3->getBucket($name, $folder, null, null, '/')) !== false) {
         return $contents;
      } else {
         return array();
      }
   }

   public static function getFileInfo($path) {
      $s3 = new S3(AWS_ACCESS_KEY, AWS_SECRET_KEY);
      $bucketName = BASE_BUCKET;
      $info = $s3->getObjectInfo($bucketName, $path);
      return "S3::getObjecInfo(): Info for {$bucketName}/".$path.': '.print_r($info, 1);
   }

   public static function getUrl($path) {
      $bucket = BASE_BUCKET;
      return "http://s3.amazonaws.com/{$bucket}/{$path}";
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

   public static function getResumeArray() {
      $folder = MainClass::getSchoolYear()."/";
      $files = S3wrapper::listBucket(BASE_BUCKET, $folder);
      $ret = array();

      foreach ($files as $file) {
         $t = array();
         $path = basename($file['name']);
         $a = explode('_', $path);
         $t['fname'] = $a[1];
         $t['lname'] = $a[0];
         $t['major'] = substr($a[2], 0, strrpos($a[2],'.'));
         $t['link'] = S3wrapper::getUrl($folder.$path);

         $ret[] = $t;
      }

      return $ret;
   }

   public static function parsePDF($fileArray, $fname, $lname, $major) {
      // Check to make sure root bucket exists
      if (S3wrapper::createBucket(BASE_BUCKET)) {
         self::log("Created bucket " . BASE_BUCKET);
      }

      if (is_null($fname) 
       || is_null($lname)
       || is_null($major) 
       || is_null($fileArray)) {
         self::log("Missing data in post array.");
         return false;
       }

      if (($ret = self::verifyPdfUpload($fileArray, $meta)) !== false) {
         self::log("PDF verification failed. $ret");
         return false;
      }

      // Create folder for this year 
      $folder =  MainClass::getSchoolYear() . "/";
      $fileName = "{$lname}_{$fname}_{$major}.pdf";

      // Send File
      $ret = S3Wrapper::putFile($fileArray['tmp_name'], $folder.$fileName);
      self::log(S3Wrapper::getFileInfo($folder.$fileName));

      if ($ret)
         self::log("SUCCESS: Put of {$fileName} to {$folder}.");
      else
         self::log("FAILURE: Put of {$fileName} to {$folder}. The file had meta of " . print_r($meta, true) . ".");

      return $ret ? S3Wrapper::getUrl($folder.$fileName) : false;
   }

   public static function getSchoolYear() {
      $month = (int)date('n');
      switch ($month) {
      case 1:
      case 2:
      case 3:
      case 4:
      case 5:
      case 6:
         $ret = date('Y', strtotime("-1 year")) . "-" . date('Y');
         break;
      case 7:
      case 8:
      case 9:
      case 10:
      case 11:
      case 12:
         $ret = date('Y') . "-" . date('Y', strtotime("+1 year"));
         break;
      default:
         $ret = date('Y');
      }

      return $ret;
   }

   public static function verifyPdfUpload($fileArr, &$meta) {
      $error = false; 
      $fileError = $fileArr['error'];

      if ($fileArr['name'] == "") 
         return "No File Uploaded";

      $pdfMime = array(
         "application/pdf",
         "application/x-pdf",
         "application/acrobat",
         "applications/vnd.pdf",
         "text/pdf",
         "text/x-pdf"
      );

      if (!$fileArr["tmp_name"]) 
         return "There was an error saving your file, please try again.";

      $fileType = exec("file -b --mime-type " . $fileArr["tmp_name"]);
      $meta = array(
         'content-type' => $fileType
      );

      $temp = explode(";",$fileType);

      if ($fileArr['size'] > 8 * 1024 * 1024)
         $error = _('Please upload only files smaller than 8 MB!');
      else if (!in_array($temp[0], $pdfMime))
         $error = _("Please upload a PDF.");
      else if ($fileError) {
         // Adopted from php.net
         switch ($fileError) {
            case UPLOAD_ERR_INI_SIZE: 
            case UPLOAD_ERR_FORM_SIZE: {
               $error = _("Please only upload files smaller than 8 MB.");
               break;
            } case UPLOAD_ERR_PARTIAL: {
               $error = _('The upload was incomplete. Please try again.');
               break;
            } case UPLOAD_ERR_NO_FILE: {
               $error = _('No file was uploaded.');
               break;
            } case UPLOAD_ERR_NO_TMP_DIR: {
               $error = _('The server does not have a temporary folder.');
               break;
            } case UPLOAD_ERR_CANT_WRITE: {
               $error = _('Failed to write file to disk.');
               break;
            } case UPLOAD_ERR_EXTENSION: {
               $error = _('File upload stipped by extension.');
               break;
            } default: {
               $error = _('Unknown upload error');
            } 
         }
      }

      return $error;
   }
}
?>
