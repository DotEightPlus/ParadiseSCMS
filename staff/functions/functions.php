<?php
/*************helper functions***************/

function clean($string) {

	return htmlentities($string);
}

function redirect($location) {

	return header("Location: {$location}");
}

function set_message($message) {

	if(!empty($message)) {

		$_SESSION['message'] = $message;

		}else {

			$message = "";
		}
}



function display_message() {

	if(isset($_SESSION['message'])) {

		echo $_SESSION['message'];
		unset($_SESSION['message']);
	}
}

function validation_errors($error_message) {

$error_message = <<<DELIMITER

<div class="alert alert-danger alert-mg-b alert-success-style6 alert-st-bg3 alert-st-bg14">
    <button type="button" class="close sucess-op" data-dismiss="alert" aria-label="Close">
		<span class="icon-sc-cl" aria-hidden="true">&times;</span>
									</button>
                 <p><strong>$error_message </strong></p>
                            </div>
DELIMITER;

   return $error_message;     

}


/************************validate user login functions**********/

function validate_user_login() {

	$errors = [];

	

	if(isset($_POST['submit'])) {

			$admission       = $_POST['staff'];


			if(!empty($errors)) {

				foreach ($errors as $error) {
			
	                echo validation_errors($error); 

				}

			} else {

				if(login_user($admission)) {
					$_SESSION['staff_id'] = $admission;
					header("location: ./");

				} else {

					echo validation_errors("Invalid StaffID");
				}
			} 

		}

} //function


/************************ user login functions**********/

function login_user($admission) {

$sql = "SELECT * FROM `staff` WHERE `staffid` = '".escape($admission)."'";
$result = query($sql);
if(row_count($result) == 1) {
	$row = mysqli_fetch_array($result);

	$user_password = $row['staffid'];

	if($admission == $user_password) {

		$_SESSION['staff_id'] = $admission;

		return true;
	} else {
		return false;
	}

	return true;
} else {

	return false;
}

} //end of function 




//---------------- upload first term result subjects ----------///
if (isset($_POST['stsbj']) && isset($_POST['test']) && isset($_POST['ass']) && isset($_POST['exc']) && isset($_POST['exam']) && isset($_POST['position']) && isset($_POST['name']) && isset($_POST['admis']) && isset($_POST['cla']) && isset($_POST['term'])) {
	

	$stbj 		= clean($_POST['stsbj']);
	$test 		= clean($_POST['test']);
	$ass 		= clean($_POST['ass']);
	$exc 		= clean($_POST['exc']);
	$exam 		= clean($_POST['exam']);
	$position	= clean($_POST['position']);
	$name 		= clean($_POST['name']);
	$admis 		= clean($_POST['admis']);
	$cla 		= clean($_POST['cla']);
	$term 		= clean($_POST['term']);

	$sql = "SELECT * FROM result WHERE `subject` = '$stbj' AND `class` = '$cla' AND `term` = '$term' WHERE `admno` = '$admis'";
	$res = query($sql);
	if(row_count($res) == 1) {
		echo "$stbj ".$term." result for ".$name."  has been uploaded before.";
	} else {


	$total      = $test + $ass + $exc + $exam;

	if ($total <= 39) {
		
		$grade  = "F9";
		$remark = "Fail";
	} else {

	if ($total <= 44) {
		
	$grade  = "E8";
	$remark = "Pass";
	} else {

	if ($total <= 49) {

	$grade  = "D7";
	$remark = "Pass";
	} else {

	if ($total <= 54) {
	
	$grade  = "C6";
	$remark = "Credit";
	} else {

	if ($total <= 59) {
	
	$grade  = "C5";
	$remark = "Credit";
	} else {

	if ($total <= 64) {

	$grade  = "B3";
	$remark = "Good";
	} else {

	if ($total <= 69) {
	
	$grade  = "B2";
	$remark = "Very Good";	
	} else {

	if ($total <= 89) {
	
	$grade  = "A1";
	$remark = "Excellent";
	} else {

	if ($total <= 100) {

	$grade  = "A*";
	$remark = "Distinction";
	}
	}
	}
	}
	}
	}
	}
	}
	}

$sql2 = "INSERT INTO result(`sn`, `class`, `admno`, `name`, `subject`, `test`, `ass`, `classex`, `exam`, `total`, `position`, `grade`, `remark`, `term`)";
$sql2.= " VALUES('1', '$cla', '$admis', '$name', '$stbj', '$test', '$ass', '$exc', '$exam', '$total', '$position', '$grade', '$remark', '$term')";
$result = query($sql2);

if ($term == "1st Term") {
		
		$fscore =  $total;
		$sndscore = 0;
		$tscore = 0;

		$ssl = "INSERT INTO score(`class`, `admno`, `subject`, `fscore`, `sndscore`, `tscore`)";
		$ssl.= "VALUES('$cla', '$admis', '$stbj', '$fscore', '$sndscore', '$tscore')";
		$qws = query($ssl);

	} else {

	if ($term == "2nd Term") {
		
		$sndscore =  $total;

		$ssl = "UPDATE score SET `sndscore` = '$sndscore'  WHERE `subject` = '$stbj' AND `class` = '$cla'";	
		$qws = query($ssl);


	}else {

	if ($term == "3rd Term") {
		
		$tscore =  $total;

		$ssl = "UPDATE score SET `tscore` = '$tscore'  WHERE `subject` = '$stbj' AND `class` = '$cla'";	
		$qws = query($ssl);


	}
	}
	}


if(isset($_SESSION['del'])) {
   unset($_SESSION['del']);
 
}


//notification for reset result
if(isset($_SESSION['res'])) {
   unset($_SESSION['res']);

}

//notification for update result
if(isset($_SESSION['upupl'])) {
   unset($_SESSION['upupl']);

}


$_SESSION['upup'] = "Result uploaded sucessfully";

echo 'Loading.. Please wait';	
echo '<script>window.location.href = "./studres?id='.$admis.'&cls='.$cla.'&term='.$term.'"</script>';
}
}

//scores

function scores($total) {


}

//--------------------- reset results -------------------//
if (isset($_POST['adm']) && isset($_POST['trm']) && isset($_POST['ccs'])) {
	
	$adm     = $_POST['adm'];
	$trm     = $_POST['trm'];
	$ccs     = $_POST['ccs'];


	$sql = "DELETE FROM result WHERE `admno` = '$adm' AND `term` = '$trm' AND `class` = '$ccs'";
	$res = query($sql);

	if(isset($_SESSION['del'])) {
  unset($_SESSION['del']);
 
}


//notification for upload result
if(isset($_SESSION['upup'])) {
   unset($_SESSION['upup']);

}


//notification for update result
if(isset($_SESSION['upupl'])) {
  unset($_SESSION['upupl']);

}


	$_SESSION['res'] = "Result resetted sucessfully";

	echo 'Loading.. Please wait';	
    echo '<script>window.location.href = "./studres?id='.$adm.'&cls='.$ccs.'&term='.$trm.'"</script>';
}



//----------------update upload first term result subjects ----------///
if (isset($_POST['stsbj']) && isset($_POST['test']) && isset($_POST['ass']) && isset($_POST['exc']) && isset($_POST['exam']) && isset($_POST['position']) && isset($_POST['name']) && isset($_POST['admis']) && isset($_POST['cla']) && isset($_POST['tms'])) {
	

	$stbj 		= clean($_POST['stsbj']);
	$test 		= clean($_POST['test']);
	$ass 		= clean($_POST['ass']);
	$exc 		= clean($_POST['exc']);
	$exam 		= clean($_POST['exam']);
	$position	= clean($_POST['position']);
	$name 		= clean($_POST['name']);
	$admis 		= clean($_POST['admis']);
	$cla 		= clean($_POST['cla']);
	$term 		= clean($_POST['tms']);

	
	$total      = $test + $ass + $exc + $exam;

	if ($total <= 39) {
		
		$grade  = "F9";
		$remark = "Fail";
	} else {

	if ($total <= 44) {
		
	$grade  = "E8";
	$remark = "Pass";
	} else {

	if ($total <= 49) {

	$grade  = "D7";
	$remark = "Pass";
	} else {

	if ($total <= 54) {
	
	$grade  = "C6";
	$remark = "Credit";
	} else {

	if ($total <= 59) {
	
	$grade  = "C5";
	$remark = "Credit";
	} else {

	if ($total <= 64) {

	$grade  = "B3";
	$remark = "Good";
	} else {

	if ($total <= 69) {
	
	$grade  = "B2";
	$remark = "Very Good";	
	} else {

	if ($total <= 89) {
	
	$grade  = "A1";
	$remark = "Excellent";
	} else {

	if ($total <= 100) {

	$grade  = "A*";
	$remark = "Distinction";
	}
	}
	}
	}
	}
	}
	}
	}
	}
	


$sql2 = "UPDATE result SET `test` = '$test', `ass` = '$ass', `classex` = '$exc', `exam` = '$exam', `total` = '$total', `position` = '$position', `grade` = '$grade', `remark` = '$remark', `term` = '$term' WHERE `class` = '$cla' AND `admno` = '$admis' AND `name` = '$name' AND `subject` = '$stbj'";
$result = query($sql2);

if ($term == "1st Term") {
		
		$fscore =  $total;
		$sndscore = 0;
		$tscore = 0;

		$ssl = "INSERT INTO score(`class`, `admno`, `subject`, `fscore`, `sndscore`, `tscore`, `term`)";
		$ssl.= "VALUES('$cla', '$admis', '$stbj', '$fscore', '$sndscore', '$tscore', '$term')";
		$qws = query($ssl);

	} else {

	if ($term == "2nd Term") {
		
		$sndscore =  $total;

		$ssl = "UPDATE score SET `sndscore` = '$sndscore'  WHERE `subject` = '$stbj' AND `class` = '$cla' AND `term` = '$term'";	
		$qws = query($ssl);


	} else {

	if ($term == "3rd Term") {
		
		$tscore =  $total;

		$ssl = "UPDATE score SET `tscore` = '$tscore'  WHERE `subject` = '$stbj' AND `class` = '$cla' AND `term` = '$term'";	
		$qws = query($ssl);


	}
	}
	}


if(isset($_SESSION['del'])) {
   unset($_SESSION['del']);
 
}


//notification for reset result
if(isset($_SESSION['res'])) {
   unset($_SESSION['res']);

}

//notification for update result
if(isset($_SESSION['upup'])) {
   unset($_SESSION['upup']);

}


$_SESSION['upupl'] = "Result updated sucessfully";

echo 'Loading.. Please wait';	
echo '<script>window.location.href = "./studres?id='.$admis.'&cls='.$cla.'&term='.$term.'"</script>';
}





//--------------------- delete subject results -------------------//
if (isset($_POST['admr']) && isset($_POST['trmr']) && isset($_POST['ccsr']) && isset($_POST['sbjjr'])) {
	
	$adm     = $_POST['admr'];
	$trm     = $_POST['trmr'];
	$ccs     = $_POST['ccsr'];
	$sbjj    = $_POST['sbjjr'];


	$sql = "DELETE FROM result WHERE `admno` = '$adm' AND `term` = '$trm' AND `class` = '$ccs' AND `subject` = '$sbjj'";
	$res = query($sql);

	$_SESSION['del'] = "Subject Result deleted sucessfully";

	echo 'Loading.. Please wait';	
    echo '<script>window.location.href = "./studres?id='.$adm.'&cls='.$ccs.'&term='.$trm.'"</script>';
}







//submit result
if(isset($_POST['attd']) && isset($_POST['punc']) && isset($_POST['hons']) && isset($_POST['neat']) && isset($_POST['nonaggr']) && isset($_POST['ldsk']) && isset($_POST['sprt']) && isset($_POST['soci']) && isset($_POST['yth']) && isset($_POST['aes']) && isset($_POST['rel']) && isset($_POST['prin']) && isset($_POST['classr']) && isset($_POST['cls']) && isset($_POST['term']) && isset($_POST['tso']) && isset($_POST['tsa'])  && isset($_POST['tsp']) && isset($_POST['mrkps'])  && isset($_POST['mrkbt']) && isset($_POST['perci']) && isset($_POST['tog']) && isset($_POST['prof'])) {

	$attd 		= clean($_POST['attd']);
	$punc 		= clean($_POST['punc']);
	$hons 		= clean($_POST['hons']);
	$neat 		= clean($_POST['neat']);
	$nonaggr	= clean($_POST['nonaggr']);
	$ldsk 		= clean($_POST['ldsk']);
	$sprt 		= clean($_POST['sprt']);
	$soci 		= clean($_POST['soci']);
	$yth 		= clean($_POST['yth']);
	$aes 		= clean($_POST['aes']);
	$rel 		= clean($_POST['rel']);
	$prin 		= clean($_POST['prin']);
	$classr		= clean($_POST['classr']);
	$cls 		= clean($_POST['cls']);
	$term 		= clean($_POST['term']);
	$tso 		= clean($_POST['tso']);
	$tsa 		= clean($_POST['tsa']);
	$tsp 		= clean($_POST['tsp']);
	$mrkps 		= clean($_POST['mrkps']);
	$mrkbt		= clean($_POST['mrkbt']);
	$perci 		= clean($_POST['perci']);
	$tog 		= clean($_POST['tog']);

	if ($_POST['prof'] == $_SESSION['rep']) {

		mover($classr, $cls);
}

	$sql = "SELECT * FROM motor WHERE `admno` = '$classr' AND `class` = '$cls' AND `term` = '$term'";
	$res = query($sql);
	if(row_count($res) == 1) {
		echo "This person already has a result details registered already!";
	} else {


$sql2 = "INSERT INTO motor(`class`, `admno`, `term`, `attendance`, `punctuality`, `honesty`, `neatness`, `nonaggr`, `leader`, `relation`, `sport`, `societies`, `youth`, `aesth`, 	`principal`, `mrkpos`, `mrkobt`, `perc`, `totgra`, `tso`, `tsa`, `tsp`)";
$sql2.= " VALUES('$cls', '$classr', '$term', '$attd', '$punc', '$hons', '$neat', '$nonaggr', '$ldsk', '$rel', '$sprt', '$soci', '$yth', '$aes', '$prin', '$mrkps', '$mrkbt', '$perci', '$tog', '$tso', '$tsa', '$tsp')";
$result = query($sql2);

$_SESSION['doneresll'] = "Result submitted successfully";

echo 'Loading.. Please wait';	
echo '<script>window.location.href = "./frn"</script>';
}
}


function mover($classr, $cls)  {


  if ($cls == 'Basic 1') {
   $cs = 'Basic 2';
  } else {
  if ($cls == 'Basic 2') {
   $cs = 'Basic 3';
  } else {
  if ($cls == 'Basic 3') {
   $cs = 'Basic 4';
  } else {
  if ($cls == 'Basic 4') {
   $cs = 'Basic 5-6';
  } else {
  if ($cls == 'Basic 5-6') {
   $cs = 'J.S.S 1';
  } else {
  if ($cls == 'J.S.S 1') {
   $cs = 'J.S.S 2';
  } else {
  if ($cls == 'J.S.S 2') {
  $cs = 'J.S.S 3';
  } else {
  if ($cls == 'J.S.S 3') {
  $cs = 'S.S.S 1';
  } else {
  if ($cls == 'S.S.S 1') {
  $cs = 'S.S.S 2';
  } else {
  if ($cls == 'S.S.S 2') {
  $cs = 'S.S.S 3';
  }
  }
  }
  }
  }
  }
  }
  }
  }
  }
	
	$ssl2 = "UPDATE students SET `Class` = '$cs' WHERE `AdminID` = '$classr'";
	$ress2 = query($ssl2);	
}




//Asiignment upload file
if (!empty($_FILES["assfile"]["name"])) {
	
	$target_dir = "../upload/assignments/";
	$target_file =  basename($_FILES["assfile"]["name"]);
	$targetFilePath = $target_dir . $target_file;
	$uploadOk = 1;
	$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

	   
	// Check if file already exists
	if (file_exists($targetFilePath)) {
		echo "Sorry, this document already exsit on the database. Kindly rename your file and try again.";
		$uploadOk = 0;
	} else {

	// Allow certain file formats
	if($imageFileType != "docx" && $imageFileType != "pdf" && $imageFileType != "doc") {
		echo "Sorry, only document files are allowed.";
		$uploadOk = 0;
	} else {
	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk == 0) {
	   echo "Sorry, your document was not uploaded.";
	// if everything is ok, try to upload file
	} else {
	   
	   move_uploaded_file($_FILES["assfile"]["tmp_name"], $targetFilePath);
	   img_prod($target_file);
	   echo 'Loading.. Please wait!';
	   echo '<script>window.location.href ="./assignment"</script>';
}
}	    	
}
}


///sql update product image
function img_prod($target_file) {

$class    = $_SESSION['ws'];
$date     = date("Y-m-d h:i:sa");

$sql = "INSERT INTO assignment(`class`, `date`, `file`)";
$sql.= "VALUES('$class', '$date', '$target_file')";
$res = query($sql);

echo 'Loading.. Please wait!';

//unset($_SESSION['ws']);
}








//update assignment
if (!empty($_FILES["fle"]["name"])) {
	
	$target_dir = "../upload/assignments/";
	$target_file =  basename($_FILES["fle"]["name"]);
	$targetFilePath = $target_dir . $target_file;
	$uploadOk = 1;
	$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

	   
	// Check if file already exists
	if (file_exists($targetFilePath)) {
		echo "Sorry, this document already exsit on the database. Kindly rename your file and try again.";
		$uploadOk = 0;
	} else {

	// Allow certain file formats
	if($imageFileType != "docx" && $imageFileType != "pdf" && $imageFileType != "doc") {
		echo "Sorry, only document files are allowed.";
		$uploadOk = 0;
	} else {
	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk == 0) {
	   echo "Sorry, your document was not uploaded.";
	// if everything is ok, try to upload file
	} else {
	   
	   move_uploaded_file($_FILES["fle"]["tmp_name"], $targetFilePath);
	   edimg_prod($target_file);
	   echo 'Loading.. Please wait!';
	   echo '<script>window.location.href ="./assignment"</script>';
}
}	    	
}
}


///sql update product image
function edimg_prod($target_file) {

$class    = $_SESSION['ws'];
$date     = date("Y-m-d h:i:sa");

$sql = "UPDATE assignment SET `file` = '$target_file' WHERE `id` = '$class'";
$res = query($sql);

echo 'Loading.. Please wait!';

//unset($_SESSION['ws']);
}



//------- delete assignment -------//
if (isset($_POST['assclss'])) {
	
	$clss             =  $_SESSION['ws'];

	$ssl  = "SELECT * from assignment WHERE `class`= '$clss'";
	$cons = query($ssl);
	while($row = mysqli_fetch_array($cons)) {
	$x = $row['file'];	

	
	unlink("../upload/assignments/$x");	
	
	};
	
	$sql = "DELETE FROM `assignment` WHERE `class`= '$clss'";
	$result = query($sql);
	

	echo 'Loading.. Please wait';
	echo '<script>window.location.href ="./assignment"</script>';
}
?>