<?php

require('processPDF.php');

if (isset($_POST['fname']) &&
    isset($_POST['lname']) &&
    isset($_FILES['file'])) {
   $fname = ucwords($_POST['fname']);
   $lname = ucwords($_POST['lname']);
   $major = $_POST['major'];
   $result = MainClass::parsePDF($_FILES['file'], $fname, $lname, $major);
}

?>
<html>
   <head>
      <title>Cal Poly ACM Resume Book</title>
   </head>
   <body>
      <h1>Cal Poly ACM's Resume Book</h1>
      <h2>Please input your first and last name and select your resume to upload.</h2>
      <?php if (isset($result) && $result) { ?>
         <h3>Success!</h3>
         <p>
            Thank you for uploading your resume <?php print "$fname $lname"; ?>.
         </p>
         <p> 
         The resume you submited is <a href="<?php echo $result; ?>">available for viewing</a> 
            if you wish. If you do not like what you uploaded, you can overwrite 
            it by putting in your name and uploading a new file.
         </p> 
      <?php } else if (isset($result) && $result === false) { ?>
         <h3>Failure!</h3>
         <p>
            Either you did not upload a PDF, you are missing a field (First and Last name...)
            , or there is a problem with our 
            servers. Please wait a minute and try again. If problems persist, 
            please <a href="mailto:nwelch@calpoly.edu">email us</a>.
         </p>
   <?php } ?>
      <table>
         <form method="post" enctype="multipart/form-data">
            <tr>
               <td><label for="fname">First Name: </label></td>
               <td><input name="fname" id="fname" style="width: 262px;"></td>
            </tr>
            <tr>
               <td><label for="lname">Last Name: </label></td> 
               <td><input name="lname" id="lname" style="width: 262px;"></td>
            </tr>
            <tr>
               <td><label for="major">Major: </label></td>
               <td>
                  <select name="major" id="major" style="width: 262px;">
                     <option selected>CSC</option>
                     <option>CPE</option>
                     <option>SE</option>
                     <option>EE</option>
                  </select>
               </td>
            </tr>
            <tr>
               <td><label for="id_file">File: </label></td>
               <td><input name="file" id="id_file" type="file"></td>
            </tr>
            <tr>
               <td colspan="2">
                  <button type="submit" class="bigbutton" 
                   style="width: 95px; height: 60px; margin: 10px;">Upload</button>
               </td>
            </tr>
         </form>
      </table>
   </body>
</html>

