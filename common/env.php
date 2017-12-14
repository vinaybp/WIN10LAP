<?php
$line_clicked=-1;
function count_names_in_entries($name) {
	$count=0;
	
	global $targetdir;
    $file = $targetdir.'/entries.html'; 
    $fh = fopen($file, 'r') or die('Could not open file: '.$file); 
    while (!feof($fh)) { 
       $buffer = fgets($fh); 
   
       if (strpos($buffer, "<label>".$name."</label>") !== false) {
		$count++;
       }  
	   
       if (strpos($buffer, "<label>".$name.".") !== false) {
		$count++;
       }  
	   
    } 
    fclose($fh); 

	return $count;
}

function enable_name_in_entries($name, $num, $except_line) {
	global $targetdir;
	global $line_clicked;
    $file = $targetdir.'/entries.html'; 
    $fh = fopen($file, 'r') or die('Could not open file: '.$file); 
    $i = 0; 
	$done = false;
    $content="";
echo "call "; 
    while (!feof($fh)) { 
       $buffer = fgets($fh); 
   
       if ((strpos($buffer, "<label>".$name.".".$num."</label>") !== false) && (!$done) && ($i != $except_line)){
echo "en num=".$num." i=".$i." ";
		$buffer=str_replace("enaction=1&num=".$num,"disaction=1",$buffer);
		$buffer=str_replace("delaction=1&num=".$num,"delaction=1",$buffer);
		$buffer=str_replace("off.png","on.png",$buffer);
		// change the name by removing .0
		$buffer=str_replace(".".$num."</label>","</label>",$buffer);
		$buffer=str_replace("/".$name."sh.bat"."sh.bat.".$num,"/".$name, $buffer);
        $content.=$buffer;
		$done=true;; // enable only one
		$line_clicked=$i;
       }  else {
         //echo $buffer."<br/>\n";
        $content.=$buffer;
      }   
      $i++;   
    } 
    fclose($fh); 
    file_put_contents($file, $content);  // entries.html
}

function disable_name_in_entries($name, $except_line) {
	global $targetdir;
	global $line_clicked;
    $file = $targetdir.'/entries.html'; 
    $fh = fopen($file, 'r') or die('Could not open file: '.$file); 
    $i = 0; 
	$done = false;
    $content="";
    while (!feof($fh)) { 
       $buffer = fgets($fh); 
   
       if ((strpos($buffer, "<label>".$name."</label>") !== false) && (!$done) && ($i != $except_line)){
echo "dis i=".$i." ";
		$buffer=str_replace("disaction=1","enaction=1&num=0",$buffer);
		$buffer=str_replace("delaction=1","delaction=1&num=0",$buffer);
		$buffer=str_replace("on.png","off.png",$buffer);
		// cahnge the name by adding .0
		$buffer=str_replace("<label>".$name."</label>","<label>".$name.".0</label>",$buffer);
		$buffer=str_replace("/".$name.".sh.bat\"","/".$name.".sh.bat.0\"",$buffer);
        $content.=$buffer;
		$done=true; // disable only one
		$line_clicked=$i;
       }  else {
         //echo $buffer."<br/>\n";
        $content.=$buffer;
      }   
      $i++;   
    } 
    fclose($fh); 

    file_put_contents($file, $content);  // entries.html
	
}


$targetdir="";
  if (isset($_GET['targetdir'])) {
	  $targetdir=$_GET['targetdir'];
	  $targetdir = str_replace('\\', '/', $targetdir);
  }
  
  //file_put_contents("debug.txt","targetdir ".$targetdir."\n", FILE_APPEND);
  //file_put_contents("debug.txt","targetdir ".$targetdir."\n", FILE_APPEND);
  //exit(0);

  if (isset($_POST['submit'])) {
    if(isset($_POST['envVar']) && !empty($_POST['envVar'])) {
      $envVar = $_POST['envVar'];
	  $txt = "<a href=\"".$_SERVER["PHP_SELF"]."?targetdir=".$targetdir."&disaction=1&name=".$envVar."\"><img src=\"/doc/images/on.png\"></a>&nbsp;<a href=\"".$_SERVER["PHP_SELF"]."?targetdir=".$targetdir."&delaction=1&name=".$envVar."\"><img src=\"/doc/images/delete.png\"></a>&nbsp;<label>".$envVar."</label>  <span id=\"".$targetdir.'/'.$envVar.".sh.bat"."\" class=\"editText\"></span><hr/>";
      file_put_contents($targetdir.'/entries.html', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
	  
      file_put_contents($targetdir.'/'.$envVar.'.sh.bat', "set ".$envVar."=new" , FILE_APPEND | LOCK_EX);
    }
  }///////////////////////////////////////////////////////////////////////////////////////
  else if (isset($_GET['delaction'])) {
	  
	  
	$num=-1;
	if (isset($_GET['num'])) {
		$num=$_GET['num'];
	}

    $file = $targetdir.'/entries.html'; 
    $fh = fopen($file, 'r') or die('Could not open file: '.$file); 
    $i = 0; 
    $content="";
    while (!feof($fh)) { 
       $buffer = fgets($fh); 
   
       if (isset($_GET['num'])) {
         if (strpos($buffer, "<label>".$_GET["name"].".".$num."</label>") !== false) {
           //echo "=====<label>".$_GET["name"].".".$num."</label>====\n";
		   // skip
         }  else {
           //echo $buffer."<br/>\n";
           $content.=$buffer;
        }
	   } else {
         if (strpos($buffer, "<label>".$_GET["name"]."</label>") !== false) {
           //echo $buffer." skiped<br/>\n";
         }  else {
           //echo $buffer."<br/>\n";
           $content.=$buffer;
         }
	   }
	  
      $i++;   
    } 
    fclose($fh); 

    file_put_contents($file, $content); // entries.html
	
    if (isset($_GET['num'])) {
      unlink($targetdir.'/'.$_GET["name"].".sh.bat.".$num);
	} else {
      unlink($targetdir.'/'.$_GET["name"].".sh.bat");
	}

  }///////////////////////////////////////////////////////////////////////////////////////
  else if (isset($_GET['disaction'])) {
	// we disable this entry, but we enable another entry
	$countnames=count_names_in_entries($_GET["name"]);
	  
	$nameexist=false;
	if ($countnames > 1) {
		$nameexist=true;		// we enable the another entry .0
		// saving because it will be overwritten
		copy($targetdir.'/'.$_GET["name"].".sh.bat.0", $targetdir.'/'.$_GET["name"].".sh.bat.0.nameexist");
		}

	disable_name_in_entries($_GET["name"], -1);

	copy($targetdir.'/'.$_GET["name"].".sh.bat", $targetdir.'/'.$_GET["name"].".sh.bat.0");
	if($nameexist) {
		copy($targetdir.'/'.$_GET["name"].".sh.bat.0.nameexist",$targetdir.'/'.$_GET["name"].".sh.bat");
	} else {
		unlink($targetdir.'/'.$_GET["name"].".sh.bat");
	}
	// remove temp file
	unlink($targetdir.'/'.$_GET["name"].".sh.bat.0.nameexist");

	if($nameexist) {
		enable_name_in_entries($_GET["name"], 0, $line_clicked);
	}
  }///////////////////////////////////////////////////////////////////////////////////////
  else if (isset($_GET['enaction'])) {

	$nameexist=false;
	if ($targetdir.'/'.$_GET["name"].".sh.bat") {
		$nameexist=true;
		// saving because it will be overwritten
		copy($targetdir.'/'.$_GET["name"].".sh.bat", $targetdir.'/'.$_GET["name"].".sh.bat.nameexist");
		}
  
	$num=$_GET['num'];
	
	enable_name_in_entries($_GET["name"], $num);

	copy($targetdir.'/'.$_GET["name"].".sh.bat.".$num, $targetdir.'/'.$_GET["name"].".sh.bat");
	if($nameexist) {
		copy($targetdir.'/'.$_GET["name"].".sh.bat.nameexist", $targetdir.'/'.$_GET["name"].".sh.bat.".$num);
		//disable_name_in_entries($_GET["name"]);
	} else {
		//unlink($targetdir.'/'.$_GET["name"].".sh.bat.".$num);
	}
		unlink($targetdir.'/'.$_GET["name"].".sh.bat.".$num);
	// remove temp file
	unlink($targetdir.'/'.$_GET["name"].".sh.bat.nameexist");

		
		
  }

  
echo "debug: ";
echo "count_test=".count_names_in_entries("test")." line=".$line_clicked."<br/>\n";
  
?>


<html>
<head>
<title>Manage environment variables</title>
<script type="text/javascript" src="./instanteditenv.js"></script>
<style type='text/css'>
body{
	font-family: verdana;
	font-size: .9em;

}

input, textarea, pre{
	font-family: verdana;
	font-size: inherit;
	font-family: inherit;
}

label{
	width: 110px;
}


#userName, #userName_field{
	font-size: 14px;
}

.editText {
	font-size: 14px;
	background-color: #333;
	color: #fff;
}


#blogTitle, #blogTitle_field{

	font-size: 24px;


}

#blogText, #blogText_field{

	width: 240px;


}

#lorumText, #lorumText_field{
	width: 500px;

}

</style>
</head>
<body>
<script type="text/javascript">
setVarsForm("pageID=profileEdit&userID=11&sessionID=28ydk3478Hefwedkbj73bdIB8H");
</script>

<form action="" method="post">
New environment variable name: <input type="text" name="envVar" placeholder="Variable name" />&nbsp;
<input type="submit" name="submit" value="Add new" />
</form>


<hr />
<?php 

function str_ends_with($haystack, $needle)
{
    return substr_compare($haystack, $needle, -strlen($needle)) 
           === 0;
}
  //echo "env var posted : ".$envVar."<br/>\n";

  $strHTML=file_get_contents($targetdir."/entries.html"); 
  //echo $strHTML;
  $doc = new DOMDocument();
  $doc->loadHTML($strHTML);
  
  //OK
  $item_num=0;
  $spans = $doc->getElementsByTagName('span');
  $labels = $doc->getElementsByTagName('label');
  foreach ($spans as $span) {
	  $elem_label = $doc->getElementsByTagName('label')->item($item_num);
	  $var_name=$elem_label->nodeValue;
//echo "item ".$item_num." ".$span->nodeValue." ".$var_name."<br/>\n";
	  $elem = $doc->getElementsByTagName('span')->item($item_num);
	  
	  // if the label ends with ".0"
	  if (str_ends_with($var_name, ".0")) {	//disabled
		  $var_name= str_replace(".0","",$var_name);
		  $strVal=file_get_contents($targetdir.'/'.$var_name.".sh.bat.0");
	  } else {
		  $strVal=file_get_contents($targetdir.'/'.$var_name.".sh.bat");
	  }
	  
	//	if (file_exists($targetdir.'/'.$var_name.".sh.bat")) {
	//	$strVal=file_get_contents($targetdir.'/'.$var_name.".sh.bat");
	//  }	else {  // disabled
	//	$var_name= str_replace(".0","",$var_name);
	//	$strVal=file_get_contents($targetdir.'/'.$var_name.".sh.bat.0");
	//  }
	  
	  
	  $elem->nodeValue=$strVal;
	  $item_num++;
  }  
  
  echo $doc->saveHTML();
?>
<br/>
<!--
<label>Your city:</label>  <span id="cityName" class="editText"><?php if ( 0 == filesize( "cityName.sh.bat" ) ) echo "..."; else echo file_get_contents("cityName.sh.bat"); ?></span>
<hr />
-->
</body>
</html>