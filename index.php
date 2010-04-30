<?php

require('processPDF.php');

if (isset($_POST['fname']) &&
    isset($_POST['lname']) &&
    isset($_FILES['file'])) {
   $fname = ucwords($_POST['fname']);
   $lname = ucwords($_POST['lname']);
   $major = $_POST['major'] == 'null' ? $_POST['OtherBox'] : $_POST['major'];
   $major = strtoupper($major);
   $result = MainClass::parsePDF($_FILES['file'], $fname, $lname, $major);
}

?>
<html>
   <head>
      <title>Cal Poly ACM Resume Book</title>
      <script src="http://ajax.googleapis.com/ajax/libs/mootools/1.2.4/mootools-yui-compressed.js"></script>
      <style>
      <!--
         body {
            width: 500px;
            margin-top: 55px;
            margin-left: auto;
            margin-right: auto;
         }
         
         table {
            width: 450px;
            margin-left: auto;
            margin-right: auto;
         }
         
         h1 {
            padding: 5px 0;
            font-family: sans-serif;
         }
         
         h2 {
            margin-top: 5px;
            margin-bottom: 60px;
         }
         
         #yay {
            background-color: LightGreen;
            border-color: DarkGreen;
         }
         
         .notify {
            padding: 15px;
            margin-bottom: 20px;
            border: 3px solid;
         }

         #nay {
            background-color: LightCoral;
            border-color: DarkRed;
         }
         
         label {
            font-weight: 900;
            font-family: serif;
         }
      -->
      </style>
      <!--
         This page was coded by Nathaniel "Nat" Welch, for Cal Poly's 
         Association of Computing Machinery. The source code is available at 
         http://github.com/icco/PdfUploader.
      -->
   </head>
   <body>
      <h1>Cal Poly <acronym title="Association of Computing Machinery">ACM</acronym> Resume Book</h1>
      <h2>Please input your first and last name, your major, and select your resume to upload.</h2>
      <?php if (isset($result) && $result) { ?>
         <div id="yay" class="notify">
            <h3>Success!</h3>
            <p>
               Thank you for uploading your resume <?php print "$fname $lname"; ?>.
            </p>
            <p> 
               The resume you submitted is <a href="<?php echo $result; ?>">available for viewing</a> 
               if you wish. If you do not like what you uploaded, you can overwrite 
               it by putting in your name and uploading a new file.
            </p> 
         </div>
      <?php } else if (isset($result) && $result === false) { ?>
         <div id="nay" class="notify">
            <h3>Failure!</h3>
            <p>
               Either you did not upload a PDF, you are missing a field (First and Last name 
               and your Major) or there is a problem with our servers. Both of your names need 
               to be between 2 and 30 characters, with no spaces. Your Major, if you selected 
               Other, must be all caps and not longer than four characters. If you did all of 
               this, and still got this error, please wait a minute and try again. If 
               problems persist, please <a href="mailto:nwelch@calpoly.edu">email us</a>.
            </p>
         </div>
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
                  <select name="major" id="major" style="width: 262px;"
                     onchange="javascript: 
                        if (this.value == 'null') 
                           $('OtherBox').setStyle('display', 'block'); 
                        else 
                           $('OtherBox').setStyle('display', 'none');
                     ;" >
                     <option selected>CSC</option>
                     <option>CPE</option>
                     <option>SE</option>
                     <option>EE</option>
                     <option value="null">Other</option>
                  </select>
               </td>
               <td>
                  <input type="text" name="OtherBox" id="OtherBox" 
                        style="display: none; width: 75px" maxlength="5" />
               </td>
            </tr>
            <tr>
               <td><label for="id_file">File: </label></td>
               <td><input name="file" id="id_file" type="file"></td>
            </tr>
            <tr>
               <td colspan="2">
                  <button type="submit" class="bigbutton" 
                   style="width: 105px; height: 60px; margin: 10px;">Upload</button>
               </td>
            </tr>
         </form>
      </table>

      <?php include('google.html'); ?>

   </body>
</html>

