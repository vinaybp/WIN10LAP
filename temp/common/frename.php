<?php
$line_clicked=-1;
$one_name_state="";
function count_names_in_entries($name) {
	$count=0;
	
	global $targetdir;
	global $one_name_state;
	$state="";
    $file = $targetdir.'/fentries.html'; 
    $fh = fopen($file, 'r') or die('Could not open file: '.$file); 
    while (!feof($fh)) { 
       $buffer = fgets($fh); 
   
       if (strpos($buffer, "<label>".$name."</label>") !== false) {
		$count++;
		if($count==1)
				$one_name_state="enabled";
       }  
       if (strpos($buffer, "<label>".$name.".") !== false) {
		$count++;
		if($count==1)
				$one_name_state="disabled";
       }  
    } 
    fclose($fh); 
	return $count;
}
///////////////////////////////////////////////////////////////////////////////
function enable_name_in_entries($name, $num, $except_line) {
	global $targetdir;
	global $line_clicked;
    $file = $targetdir.'/fentries.html'; 
    $fh = fopen($file, 'r') or die('Could not open file: '.$file); 
    $i = 0; 
	$done = false;
    $content="";
//echo "call "; 
    while (!feof($fh)) { 
       $buffer = fgets($fh); 
   
       if ((strpos($buffer, "<label>".$name.".".$num."</label>") !== false) && (!$done) && ($i != $except_line)){
//echo "en num=".$num." i=".$i." ";
		$buffer=str_replace("enaction=1&num=".$num,"disaction=1",$buffer);
		$buffer=str_replace("delaction=1&num=".$num,"delaction=1",$buffer);
		$buffer=str_replace("off.png","on.png",$buffer);
		// change the name by removing .0
		$buffer=str_replace(".".$num."</label>","</label>",$buffer);
		$buffer=str_replace("/".$name.".".$num,"/".$name, $buffer);
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
    file_put_contents($file, $content);  // fentries.html
}
//////////////////////////////////////////////////////////////////////////////
function disable_name_in_entries($name, $except_line, $num) {
	global $targetdir;
	global $line_clicked;
    $file = $targetdir.'/fentries.html'; 
    $fh = fopen($file, 'r') or die('Could not open file: '.$file); 
    $i = 0; 
	$done = false;
    $content="";
    while (!feof($fh)) { 
       $buffer = fgets($fh); 
   $nunum=0;
   $nunum=$num;
       if ((strpos($buffer, "<label>".$name."</label>") !== false) && (!$done) && ($i != $except_line)){
//echo "dis i=".$i." ";
		$buffer=str_replace("disaction=1","enaction=1&num=".($nunum),$buffer);
		$buffer=str_replace("delaction=1","delaction=1&num=".($nunum),$buffer);
		$buffer=str_replace("on.png","off.png",$buffer);
		// change the name by adding .0
//echo "xnum=".$num." \n";
		$buffer=str_replace("<label>".$name."</label>","<label>".$name.".".($nunum)."</label>",$buffer);
		$buffer=str_replace("/".$name."\"","/".$name.".".($nunum)."\"",$buffer);
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
    file_put_contents($file, $content);  // fentries.html
}
////////////////////////////////////////////////////////////////////////////////

//function apply_file_changes($action) {
//}

////////////////////////////////////////////////////////////////////////////////

function swap_files($file1, $file2) {
//echo "   swap file1 : ".$file1." <br/>becomes file2 : ".$file2."<br/>\n";
//echo "    and file2 : ".$file2." <br/>becomes file1 : ".$file1."<br/>\n";
rename($file1,$file2.".tmpswap");
rename($file2,$file1);
rename($file2.".tmpswap",$file2);
}

////////////////////////////////////////////////////////////////////////////////

$targetdir="";
  if (isset($_GET['targetdir'])) {
	  $targetdir=$_GET['targetdir'];
	  $targetdir = str_replace('\\', '/', $targetdir);
  }
$num=0;
  if (isset($_GET['num'])) {
	$num=$_GET['num'];
  }
  
  //file_put_contents("debug.txt","targetdir ".$targetdir."\n", FILE_APPEND);
  //file_put_contents("debug.txt","targetdir ".$targetdir."\n", FILE_APPEND);
  //exit(0);
/////////////////////////////////////////////////////////////////////////////////////////
  if (isset($_POST['submit'])) {
    if(isset($_POST['envVar']) && !empty($_POST['envVar'])) {
    
	  if(!is_file($targetdir.'/fentries.html')){
	    file_put_contents($targetdir.'/fentries.html', "");
	  }	  
	  $envVar = $_POST['envVar'];
	  $cnt=count_names_in_entries($envVar);
	  if(($cnt==0) || ( ($cnt==1) && ($one_name_state=="disabled")   )) {
			$txt = "<a href=\"".$_SERVER["PHP_SELF"]."?targetdir=".$targetdir."&disaction=1&name=".$envVar."\"><img src=\"/doc/images/on.png\"></a>&nbsp;<a href=\"".$_SERVER["PHP_SELF"]."?targetdir=".$targetdir."&delaction=1&name=".$envVar."\"><img src=\"/doc/images/delete.png\"></a>&nbsp;<label>".$envVar."</label>  <span id=\"".$targetdir.'/.'.$envVar."\" class=\"editText\"></span><hr/>";
			file_put_contents($targetdir.'/.'.$envVar, "comment for ".$envVar.": new" , LOCK_EX);
	  } else {
			$txt = "<a href=\"".$_SERVER["PHP_SELF"]."?targetdir=".$targetdir."&enaction=1&num=".($cnt-1)."&name=".$envVar."\"><img src=\"/doc/images/off.png\"></a>&nbsp;<a href=\"".$_SERVER["PHP_SELF"]."?targetdir=".$targetdir."&delaction=1&num=".($cnt-1)."&name=".$envVar."\"><img src=\"/doc/images/delete.png\"></a>&nbsp;<label>".$envVar.".".($cnt-1)."</label>  <span id=\"".$targetdir.'/.'.$envVar.".".($cnt-1)."\" class=\"editText\"></span><hr/>";
			file_put_contents($targetdir.'/.'.$envVar.'.'.($cnt-1), "comment for ".$envVar.": new" , LOCK_EX);
	  }
      file_put_contents($targetdir.'/fentries.html', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
	  
    }
  }///////////////////////////////////////////////////////////////////////////////////////
  else if (isset($_GET['delaction'])) {
	//$num=-1;
	//if (isset($_GET['num'])) {
	//	$num=$_GET['num'];
	//}

    $file = $targetdir.'/fentries.html'; 
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

    file_put_contents($file, $content); // fentries.html
    
    //apply_file_changes("delaction");
	
    if (isset($_GET['num'])) {
      unlink($targetdir.'/.'.$_GET["name"].".".$num);
	} else {
      unlink($targetdir.'/.'.$_GET["name"]);
	}

  }///////////////////////////////////////////////////////////////////////////////////////
  else if (isset($_GET['disaction'])) {
	// we disable this entry, but we may enable another entry
	$countnames=count_names_in_entries($_GET["name"]);
	$nameexist=false;
	if ($countnames > 1) {
		$nameexist=true;		// we enable the another entry .0
		// saving because it will be overwritten
//echo "num=".$num." \n";
		copy($targetdir.'/.'.$_GET["name"].".".$num, $targetdir.'/.'.$_GET["name"].".".$num.".nameexist");
		}

	disable_name_in_entries($_GET["name"], -1, $num);
	
	//apply_file_changes("disaction");


//echo "num=".$num." \n";
	copy($targetdir.'/.'.$_GET["name"], $targetdir.'/.'.$_GET["name"].".".$num);
	if($nameexist) {
		copy($targetdir.'/.'.$_GET["name"].".".$num.".nameexist",$targetdir.'/.'.$_GET["name"]);
	} else {
		unlink($targetdir.'/.'.$_GET["name"]);
	}
	// remove temp file
//echo "num=".$num." \n";
	unlink($targetdir.'/.'.$_GET["name"].".".$num.".nameexist");

	if($nameexist) {
		enable_name_in_entries($_GET["name"], 0, $line_clicked);
	}
	
	swap_files($targetdir.'/'.$_GET["name"], $targetdir.'/'.$_GET["name"].".".$num);

	
  }///////////////////////////////////////////////////////////////////////////////////////
  else if (isset($_GET['enaction'])) {
	// we enable this entry, but we may disable another entry
	$countnames=count_names_in_entries($_GET["name"]);
	$nameexist=false;
	if ($countnames > 1) {
		$nameexist=true;
		// saving because it will be overwritten
		copy($targetdir.'/.'.$_GET["name"], $targetdir.'/.'.$_GET["name"].".nameexist");
		}
  
	//$num=$_GET['num'];
//echo "num=".$num." <br/>\n";
	
	enable_name_in_entries($_GET["name"], $num, -1);
	
	//apply_file_changes("enaction");


	copy($targetdir.'/.'.$_GET["name"].".".$num, $targetdir.'/.'.$_GET["name"]);
	if($nameexist) {
		copy($targetdir.'/.'.$_GET["name"].".nameexist", $targetdir.'/.'.$_GET["name"].".".$num);
	} else {
		unlink($targetdir.'/.'.$_GET["name"].".".$num);
	}
	// remove temp file
	unlink($targetdir.'/.'.$_GET["name"].".nameexist");
	

	if($nameexist) {
		// here we disable another entry
		disable_name_in_entries($_GET["name"], $line_clicked, $num);
	}
	
	swap_files($targetdir.'/'.$_GET["name"].".".$num, $targetdir.'/'.$_GET["name"]);
	
  }//////////////////////////////////////////////////////////////////////////////////////////

  
//echo "debug: pour l'instant limite a 2 entree par nom"."<br/>\n";
//echo "count_test=".count_names_in_entries("test")." line=".$line_clicked."<br/>\n";
//echo "one_name_state=".$one_name_state."<br/>\n";

$pos=strrpos($targetdir, "/");
$prjname=substr($targetdir, $pos+1);
  
?>


<html>
<head>
<title>Manage file names for <?php echo $prjname; ?></title>
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
New file name: <input type="text" name="envVar" placeholder="File name" />&nbsp;
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

  $strHTML=file_get_contents($targetdir."/fentries.html"); 
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
//echo "item ".$item_num."-".$span->nodeValue."-".$var_name."<br/>\n";
	  $elem = $doc->getElementsByTagName('span')->item($item_num);
	  
	  $name_in_var="";
	  $number_in_var=-1;
	  if ($pos=strrpos($var_name, ".")) {  //disabled
		$number_in_var=substr($var_name, $pos+1);
		$name_in_var=substr($var_name, 0, $pos);
		  $var_name= str_replace(".".$number_in_var,"",$var_name);
		  $strVal=file_get_contents($targetdir.'/.'.$var_name.".".$number_in_var);
		
	  } else {
		$strVal=file_get_contents($targetdir.'/.'.$var_name); 
	  }
	  
	//echo "name ".$name_in_var." number ".$number_in_var."<br/>\n";;
  
	  $elem->nodeValue=$strVal;
	  $item_num++;
  }  
  
  echo $doc->saveHTML();
?>
<br/>
</body>
</html>