<!-- CSCI311 | Names: Tiffany & Ning | Final Project 
http://wwwstu.csci.viu.ca/~csci311a/project/Sellbook/Sellbook.html -->
<?php
	session_start();
	$_SESSION['url'] = $_SERVER['REQUEST_URI'];
    if(!isset($_SESSION['UserData']['userid'])){
		header('Location: ../login/login.php');	
    }
	require_once("../private/dbinfo.inc");
	$err;
	$success;

    if (($_SERVER['REQUEST_METHOD']=="POST")){
    	$TB_Title = $_POST['tbtitle'];
    	$TB_Author = $_POST['tbauthor'];
		$TB_Publisher = $_POST['publisher'];
    	$TB_ISBN = $_POST['tbisbn'];

		$file = $_FILES["image"];
		$file_name = $_FILES['image']['name'];
		$file_size = $_FILES['image']['size'];
		$file_tmp = $_FILES['image']['tmp_name'];
		$file_type = $_FILES['image']['type'];
		$file_ext = strtolower(end(explode('.',$_FILES['image']['name'])));
	  	$extensions = array('gif','png' ,'jpg','jpeg');

		if(!isset($TB_Title)||trim($TB_Title) === ""||strlen($TB_Title)>=70){
			$err = "Sorry, book title cannot be empty!";
		}else if(!isset($TB_Author)||trim($TB_Author) === ""||strlen($TB_Author)>=70){
			$err = "Sorry, you must add an author!";
		}else if(strlen($TB_Publisher)>=70){
			$err = "Sorry, book publisher cannot be longer than 70 characters!";
		}else if($TB_ISBN>=9999999999999){
			$err = "Sorry, ISBN too big!";
		}else if($file_size == 0){
			$err = "Sorry, you must add a photo so others can see it!";
		}else if(in_array($file_ext,$extensions) === false){
			$err = "That photo extension is not allowed, please choose a JPEG or PNG file.";
		}else if($file_size > 2097152){
			$err = 'File size cannot exceed 2 MB';
		}else{
			try{
				$myHandle = new PDO("mysql:host=$servername;dbname=$database", $username, $password);

				$stmt = $myHandle->prepare("SELECT Account_ID FROM Account WHERE userid=:u_id");
				$stmt->bindParam(':u_id', $_SESSION['UserData']['userid']);
				$stmt->execute();
				$Account_ID = $stmt->fetchColumn();

				$mybook = $myHandle->prepare("select MAX(Books.Book_ID) from Books join Account on Books.Account_ID = Account.Account_ID 
											where Books.Account_ID = :Account_ID;");
				$mybook->bindParam(':Account_ID', $Account_ID);
				$mybook->execute();
				$rsltbook = $mybook->fetchAll();
				$i=1;
				foreach($rsltbook as $row){
					foreach($row as $field=>$value){
						$book[$i][$field] = $value;
					}
					$i++;
				}
				$Book_ID = $book[1]['MAX(Books.Book_ID)']; 
				$mybook = null;

				$mytbook = $myHandle->prepare("select MAX(TB_ID) from Trad_Books;");
				$mytbook->execute();
				$rslttbook = $mytbook->fetchAll();
				$i=1;
				foreach($rslttbook as $row){
					foreach($row as $field=>$value){
						$tbook[$i][$field] = $value;
					}
					$i++;
				}
				$Book_num = $tbook[1]['MAX(TB_ID)'] + 1;
				$mybook = null;
				$TB_Photo = "../BOOKS/bookimgs/tb".$Book_num.".".$file_ext;
	     		move_uploaded_file($file_tmp, $TB_Photo);
				chmod($TB_Photo."",0644);
       
				$myQuery = $myHandle->prepare("insert into Trad_Books(TB_Title, TB_Author, TB_Publisher, TB_ISBN, TB_Photo, Book_ID)
												values (:TB_Title, :TB_Author, :TB_Publisher, :TB_ISBN, :TB_Photo, :Book_ID);");
			    $myQuery->bindParam(':TB_Title',addslashes($TB_Title));
			    $myQuery->bindParam(':TB_Author',addslashes($TB_Author));
			    $myQuery->bindParam(':TB_Publisher',addslashes($TB_Publisher));
			    $myQuery->bindParam(':TB_ISBN',addslashes($TB_ISBN));
			    $myQuery->bindParam(':TB_Photo',addslashes($TB_Photo));
			    $myQuery->bindParam(':Book_ID',addslashes($Book_ID));

				if($myQuery->execute() !==false){
					$success .= "Your book is posted!" ;
				}else{
					$err .= "Something went wrong";
				}
				$myHandle = null;
			}catch(PDOException $e){
				$err .= "Connection failed \n";
			}
		}
    }
?>

<!DOCTYPE html>
<!-- CSCI311 | Names: Tiffany & Ning | Final Project -->
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">

<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>BOOK Sell</title>
    <link rel="stylesheet" type="text/css" href="./SellStyles.css">
	<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<script src="./sell.js"></script>
	<script>
    	function verifyClear(){
        	alert("Are you sure you want to clear all entries?");
    	}
		function preview_image(event) {
 			var reader = new FileReader();
 			reader.onload = function() {
  				var output = document.getElementById('preview_img');
  				output.src = reader.result;
 			}
 			reader.readAsDataURL(event.target.files[0]);
		}
	</script>   
</head>

<body>
<div id="main">
	<div class="myNavBar">
		<ul class="myNav">
      			<li class="pagelink"><a href="../index.php" tabindex=1>Home <i class="fa fa-home w3-padding" style="color: #EBF7D4;"></i></a></li>
      			<li class="pagelink"><a href="../Buybook/Buybook.php" tabindex=2>Buy Books</a></li>
				<li class="pagelink"><a href="../Sellbook/Sellbook.php" tabindex=2>Sell Books</a></li>
      			<li class="pagelink"><a href="../Tradebook/Tradebook.php" tabindex=3>Trade Books</a></li>
				<?php
				if(!isset($_SESSION['UserData']['userid'])){
					echo "<li class=\"login\"><a href=\"../login/login.php\">Login</a></li>";
				}else{
					echo "<li class=\"login\"><a href=\"../Profile/myBooks.php\">Profile</a></li>";
					echo "<li class=\"login\"><a href=\"../login/logout.php\">Logout</a></li>\n";
				}
				?>
		 </ul>
	  </div>

<form  action="./AddTradeBook.php" method="POST" enctype="multipart/form-data">
	<div class="TradeBooks">
		<h1>Book Trade Sheet</h1>
		<h2>Upload your book for trading!</h2>
		<div class="inputRow">
		    <label class="myLabel" for="tbtitle"> <span class="asterisk">*</span> Title: </label>
		    <input class="myInput" type="text" name="tbtitle" id="tbtitle" placeholder="title of book"  />
		</div>
		<div class="inputRow">
		    <label class="myLabel" for="tbauthor"> <span class="asterisk">*</span> Author: </label>
		    <input class="myInput" type="text" name="tbauthor" id="tbauthor" placeholder="first and last name(s)"  />
		</div>
		<div class="inputRow">
		    <label class="myLabel" for="tbpublisher"> Publisher: </label>
		    <input class="myInput" type="text" name="tbpublisher" id="tbpublisher" placeholder="optional"  />
		</div>
		<div class="inputRow">
		    <label class="myLabel" for="tbisbn">ISBN: </label>
		    <input class="myInput" type="number" name="tbisbn" id="tbisbn" 
						placeholder="Please enter the 10-13 digit number ISBN found on the bar code of your book" />
		</div>
		<div class="inputRow">
		    <label class="myLabel" for="image_uploads"> <span class="asterisk">*</span> Photo: </label>
			<div class= "upload">
				<input class="uploadInput" type="file" id="image_uploads" name="image" accept="image/*" onchange="preview_image(event)"  />
        		<img id="preview_img" src="" /><br/>
			</div>
		</div>
	</div>
    <?php if(isset($err)) echo "<h3>".$err."</h3>";?>
    <?php if(isset($success)) echo "<h3>".$success."</h3>";?>
	<div class="w3-row">	
		<div class="w3-col m6" style="text-align: center;"><input class="w3-button w3-teal w3-xlarge" type="reset" name="reset" value="Clear Sheet" onclick="verifyClear()"/></div>
    	<div class="w3-col m6" style="text-align: center;"><input class="w3-button w3-green w3-xlarge" type="submit" name="go" value="Post my Book!" /></div>
	</div>
</form>
</div><!--main-->
	<footer><small><b> Tiffany & Ning Copyright &copy; 2019 </b></small></footer>
</body>
</html>
