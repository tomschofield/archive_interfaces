<!DOCTYPE HTML> 
<html>
<head>
    <title>Interface Feedback</title>
    <link rel='stylesheet' type='text/css' href='stylesheet.css'/>
    <script src="jquery-1.10.2.min.js"></script>
    <script src="script.js" type="text/javascript"></script>
</head>
<body> 

<?php
// define variables and set to empty values
$nameErr = $emailErr = $genderErr = $interfaceErr = $useErr = $technicalErr = $improvementErr ="";
$name = $email = $gender =  $interface = $use = $technical = $improvement = "";

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
   if (empty($_POST["name"]))
     {$nameErr = "Name is required";}
   else
     {
     $name = test_input($_POST["name"]);
     // check if name only contains letters and whitespace
     if (!preg_match("/^[a-zA-Z ]*$/",$name))
       {
       $nameErr = "Only letters and white space allowed"; 
       }
     }
   
   if (empty($_POST["email"]))
     {$emailErr = "Email is required";}
   else
     {
     $email = test_input($_POST["email"]);
     // check if e-mail address syntax is valid
     if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$email))
       {
       $emailErr = "Invalid email format"; 
       }
     }
     

   if (empty($_POST["use"]))
     {$use = "";}
   else
     {$use = test_input($_POST["use"]);}

   if (empty($_POST["technical"]))
     {$technical = "";}
   else
     {$technical = test_input($_POST["technical"]);}

   if (empty($_POST["improvement"]))
     {$improvement = "";}
   else
     {$improvement = test_input($_POST["improvement"]);}

   if (empty($_POST["gender"]))
     {$genderErr = "Gender is required";}
   else
     {$gender = test_input($_POST["gender"]);}


   if (empty($_POST["interface"]))
     {$interfaceErr = "Interface is required";}
   else
     {$interface = test_input($_POST["interface"]);}

 	if($nameErr =="" && $emailErr=="" &&  $genderErr =="" && $websiteErr =="" ){
    $fs = fopen("mydata.csv","a");
    
    fwrite( $fs, $name.",".$email.",".$interface.",".$use.",".$improvement.",".$technical.",".$gender."\n");
    fclose($fs);
    chmod($fs, 333);
    echo '<META HTTP-EQUIV="Refresh" Content="0; URL=thanks.php">';    
    exit;    
 	}

}

function test_input($data)
{
     $data = trim($data);
     $data = stripslashes($data);
     $data = htmlspecialchars($data);
     return $data;
}
?>
<div id="container">
  <h2>Let us know what you thought about the interface.</h2>
  <p> The interfaces to the Bloodaxe Archive are part of ongoing research so it's very important for us to get your feedback. We are actively developing and iterating new designs so your comments will genuinely shape future interface work.</p>
  <p><span class="error">* required field.</span></p>
  <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 
     Name: <input type="text" name="name" value="<?php echo $name;?>">
     <span class="error">* <?php echo $nameErr;?></span>
     <br><br>
     E-mail: <input type="text" name="email" value="<?php echo $email;?>">
     <span class="error">* <?php echo $emailErr;?></span>
     <br><br>
        Which interface do you want to talk about?:<br><br>
     <input type="radio" name="interface" <?php if (isset($interface) && $interface=="ArchiveWindows") echo "checked";?>  value="ArchiveWindows">Archive Windows
     <input type="radio" name="interface" <?php if (isset($interface) && $interface=="BloodaxeTimeline") echo "checked";?>  value="BloodaxeTimeline">Bloodaxe Timeline
     <input type="radio" name="interface" <?php if (isset($interface) && $interface=="BoxContents") echo "checked";?>  value="BoxContents">Box Contents
     <input type="radio" name="interface" <?php if (isset($interface) && $interface=="BloodaxeTitles") echo "checked";?>  value="BloodaxeTitles">Bloodaxe Titles

     <span class="error">* <?php echo $interfaceErr;?></span>
     
     <br><br>
     Have you used the interface in your research, if so how? :<br><br><textarea name="use" rows="10" cols="80"><?php echo $use;?></textarea>
     <br><br>
     How could the interface be improved? Was there anything you liked or disliked? :<br><br> <textarea name="improvement" rows="10" cols="80"><?php echo $improvement;?></textarea>
     <br><br>
     Did you find any technical problems with it? : <br><br><textarea name="technical" rows="10" cols="80"><?php echo $technical;?></textarea>
     <br><br>
     Gender:
     <input type="radio" name="gender" <?php if (isset($gender) && $gender=="female") echo "checked";?>  value="female">Female
     <input type="radio" name="gender" <?php if (isset($gender) && $gender=="male") echo "checked";?>  value="male">Male
     <span class="error">* <?php echo $genderErr;?></span>
     <br><br>
     <input type="submit" name="submit" value="Submit"> 
  </form>
</div>

</body>
</html>