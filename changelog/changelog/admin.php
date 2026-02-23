<!-- Simple Flatfile Development Changelog by Tyr krause@krauselabs.net -->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Narnia Changelog Admin v1.0</title>
<link rel="stylesheet" href="changelog.css" type="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
.style2 {font-size: x-small}
.style3 {font-size: smaller}
-->
</style>
</head>

<body>
<br><br>
<center>
<form method="post" action="submitaction.php">
    <table width="75%" border="0">
      <tr>
	  <!-- Change the following, its the title of the admin area --> 
        <td width="1044"><h1 align="center">Narnia Changelog Admin </h1>
          <h1 align="center"><span class="style2">
		  <!-- May have to change this link if you changed the changelog file name -->
		  (<a href="changelog.txt" target="_blank">Download ASCII RAW Changelog</a>) (<a href="index.php" target="_blank">View PHP Changelog</a>) </span></h1></td>
      </tr>
      <tr>
        <td><p align="center" class="style3"><br>
            <strong>Enter your name below.</strong>
          <p align="center" class="style3">            <input name="poster" type="text" value="">
            <br>
            <br>
            <strong>Use the following field to add onto the changelog, the submission will be posted under today's date with the current time. </strong>            <p align="center" class="style3">              
			<textarea name="updatelog" cols="80" rows="5" wrap="VIRTUAL">-Item 1 
-Item 2 
-Item 3</textarea>
  
        <p align="center">&nbsp;          </p>
          <p align="center">
            <input name="submit" type="submit">
          </p></td>
      </tr>
    </table>
</form>
</center>
</body>
</html>
