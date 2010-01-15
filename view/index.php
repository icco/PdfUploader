<?php
require('../processPDF.php');
$resumes = MainClass::getResumeArray();
?>
<html>
   <head>
      <title>Cal Poly ACM Resume Book Viewer</title>
      <style>
      <!--
         body {
            width: 500px;
            margin-top: 55px;
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

         table.sortable thead {
            background-color:#eee;
            color:#666666;
            font-weight: bold;
            cursor: pointer;
         }

         table {
            width: 700px;
            margin-left: -100px;
         }
         
         th {
            padding: 5px 15px;
         }

         td {
            text-align: center;
            padding: 3px 0;
         }
         
         tr {
            border: 0px black solid;
         }
         
         tr.norm {
            background-color: skyblue;
         }
         
         tr.alt {
            background-color: grey;
         }

         a, a:hover, a:visited {
            color: black;
            font-family: sans-serif;
            font-weight: 900;
         }
      -->
      </style>
      <!--
         This page was coded by Nathaniel "Nat" Welch, for Cal Poly's 
         Association of Computing Machinery. The source code is available at 
         http://github.com/icco/PdfUploader.
         -->
   <script src="sorttable.js"></script>
   </head>
   <body>
      <h1>Cal Poly <acronym title="Association of Computing Machinery">ACM</acronym> Resume Book</h1>
      <h2>Use this page to browse resumes for this year's students.</h2>
      <table class="sortable">
      <?php
         if (!empty($resumes)) { 
      ?>
         <thead>
            <tr>
               <th>First Name</th>
               <th>Last Name</th>
               <th>Major</th>
            </tr>
         </thead>
         <tbody>
      <?php }
         foreach ($resumes as $key => $resume) {
            $alt = "no-style";
            // $alt = $key % 2 != 0 ? "alt" : "norm";
            $rowString = "<tr class=\"{$alt}\">";
            $rowString .= "<td>{$resume['fname']}</td>";
            $rowString .= "<td>{$resume['lname']}</td>";
            $rowString .= "<td>{$resume['major']}</td>";
            $rowString .= "<td><a href=\"{$resume['link']}\">DL</a></td>";
            $rowString .= "</tr>\n";

            print $rowString;
         }
      ?>
         </tbody>
      </table>
   </body>
</html>

