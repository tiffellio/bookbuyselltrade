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
	    $Title = $_POST['title'];
	    $Author = $_POST['author'];
        $Publisher = $_POST['publisher'];
        $ISBN = $_POST['isbn'];
	    $Price = $_POST['price'];
        $Contact = $_POST['contact'];
	    $Type = $_POST['bookType'];
		$Post_Time = date("Y-m-d");
		if ($Type == "Text") {
 			$Program = $_POST['program'];
			$Course = $_POST['course'];
			$CourseNum = $_POST['courseNum'];
			$Instructor = $_POST['instructor'];
		}else if ($Type == "Book") {
			$Categories = $_POST['categories'];
		}
		$file = $_FILES["image"];
		$file_name = $_FILES['image']['name'];
		$file_size = $_FILES['image']['size'];
		$file_tmp = $_FILES['image']['tmp_name'];
		$file_type = $_FILES['image']['type'];
		$file_ext = strtolower(end(explode('.',$_FILES['image']['name'])));
	  	$extensions = array('gif','png' ,'jpg','jpeg');

		if(!isset($Title)||trim($Title) === ""||strlen($Title)>=70){
			$err = "Sorry, the title of your book is the incorrect length, please go back and try again!";
		}else if(!isset($Author)||trim($Author) === ""||strlen($Author)>=70){
			$err = "Sorry, the author of your book is the incorrect length, please go back and try again!";
		}else if(strlen($Publisher)>=70){
			$err = "Sorry, the publisher of your book is the incorrect length, please go back and try again!";
		}else if(!isset($Price)||!is_numeric($Price)||$Price<0||$Price>99999999){
			$err = "Please enter your price as a number!";
		}else if(!isset($Contact)||trim($Contact) === ""||strlen($Contact)>=70){
			$err = "Sorry, you must enter a contact number or email so they can reach you!";
		}else if(!isset($Type)){
			$err = "Sorry, you must choose a book type!";
		}else if($Type == "Text" && $Program == "default"){
			$err = "Please select a program!";
   		}else if($Type == "Text" && $Course  == "default"){
			$err = "Sorry, please enter the course code belonging to that book!";
		}else if($Type == "Text" && $CourseNum == null ){
			$err = "Sorry, please enter the course code belonging to that book!";
		}else if($Type == "Text" && $Instructor == null||strlen($Instructor)>=20){
			$err = "Sorry, please enter an instructor related that book!";
		}else if($Type == "Book" && $Categories == "default"){
			$err = "Sorry, please choose a category for your book!";
		}else if($file_size == 0){
			$err = "Sorry, please provide a photo of the book so others can see it!";
		}else if(in_array($file_ext,$extensions) === false){
			$err = "That photo extension is not allowed, please choose a JPEG or PNG file.";
		}else if($file_size > 6097152){
			$err = 'File size must be cannot be greater than 6 MB';
		}else{
			try{
				$myHandle = new PDO("mysql:host=$servername;dbname=$database", $username, $password);

				$stmt = $myHandle->prepare("SELECT Account_ID FROM Account WHERE userid=:u_id");
				$stmt->bindParam(':u_id', $_SESSION['UserData']['userid']);
				$stmt->execute();
				$Account_ID = $stmt->fetchColumn();

				$mybook = $myHandle->prepare("select MAX(Book_ID) from Books;");
				$mybook->execute();
				$rsltbook = $mybook->fetchAll();
				$i=1;
				foreach($rsltbook as $row){
					foreach($row as $field=>$value){
						$book[$i][$field] = $value;
					}
					$i++;
				}
				$Book_num = $book[1]['MAX(Book_ID)'] + 1;
				$mybook = null;
				$Photo = "./BOOKS/bookimgs/b".$Book_num.".".$file_ext;
	     		move_uploaded_file($file_tmp, ".".$Photo);
				chmod(".".$Photo."",0644);
       
				$myQuery = $myHandle->prepare("insert into Books(Title, Author, Publisher, ISBN, Contact, Price, Photo, Type, Post_Time, Sold_Time, Account_ID) 
														values (:Title, :Author, :Publisher, :ISBN, :Contact, :Price, :Photo, :Type, :Post_Time, '0000-00-00', :Account_ID);");
			    $myQuery->bindParam(':Title',addslashes($Title));
			    $myQuery->bindParam(':Author',addslashes($Author));
			    $myQuery->bindParam(':Publisher',addslashes($Publisher));
			    $myQuery->bindParam(':ISBN',addslashes($ISBN));
			    $myQuery->bindParam(':Contact',addslashes($Contact));
			    $myQuery->bindParam(':Price',addslashes($Price));
			    $myQuery->bindParam(':Photo',addslashes($Photo));
			    $myQuery->bindParam(':Type',addslashes($Type));
			    $myQuery->bindParam(':Post_Time',addslashes($Post_Time));
			    $myQuery->bindParam(':Account_ID',addslashes($Account_ID));

				if($myQuery->execute() !==false){
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
					$myQuery = null;
					$mybook = null;
					if ($Type == "Text") {
						$mytextbook = $myHandle->prepare("insert into Text_Books(Program, Course, CourseNum, Instructor, Book_ID)
														values (:Program, :Course, :CourseNum, :Instructor, :Book_ID);");
						$mytextbook->bindParam(':Program',addslashes($Program));
						$mytextbook->bindParam(':Course',addslashes($Course));
						$mytextbook->bindParam(':CourseNum',addslashes($CourseNum));
						$mytextbook->bindParam(':Instructor',addslashes($Instructor));
						$mytextbook->bindParam(':Book_ID',addslashes($Book_ID));
						if($mytextbook->execute() !== false){
							$success .= "Your book is posted!" ;
							$mytextbook = null;							
						}else{
							$err .= "Something went wrong when submitting your text book";
						}
					}else if ($Type == "Book") {
						$mygenbook = $myHandle->prepare("insert into General_Books(Categories, Book_ID) values (:Categories, :Book_ID);");
						$mygenbook->bindParam(':Categories',addslashes($Categories));
						$mygenbook->bindParam(':Book_ID',addslashes($Book_ID));
						if($mygenbook->execute() !== false){
							$success .= "Your book posted!" ;
							$mygenbook = null;							
						}else{
							$err .= "Something went wrong when submitting your book";
						}
					}
				}else{
					$err .= "Sorry, something went wrong";
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
    <title>Sell Books</title>
    <link rel="stylesheet" type="text/css" href="./SellStyles.css">
	<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="./sell.js"></script>
	<script>
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

<body onload="disableAllBooks()">
<div id="main">
	<div class="myNavBar">
		<ul class="myNav">
      			<li class="pagelink"><a href="../index.php" tabindex=1>Home <i class="fa fa-home w3-padding" style="color: #EBF7D4;"></i></a></li>
      			<li class="pagelink"><a href="../Buybook/Buybook.php" tabindex=2>Buy Books</a></li>
				<li class="pagelink"><a href="../Sellbook/Sellbook.php" tabindex=3>Sell Books</a></li>
      			<li class="pagelink"><a href="../Tradebook/Tradebook.php" tabindex=4>Trade Books</a></li>
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

<form  action="./Sellbook.php" method="POST" enctype="multipart/form-data">
	<div class="TextBooks">
		<h1> Book Sheet </h1>
    	<div class="inputRow">
        	<label class="myLabel" for="title"> <span class="asterisk">*</span> Title: </label>
        	<input class="myInput" type="text" name="title" id="title" placeholder="Title (must be 1-60 characters in length)" title="Title (must be 1-60 characters in length)" tabindex=5 required/>
    	</div>
    	<div class="inputRow">
        	<label class="myLabel" for="author"> <span class="asterisk">*</span> Author: </label>
        	<input class="myInput" type="text" name="author" id="author" placeholder="First, Last" title="Please enter the first and last name of the author"  tabindex=6 required/>
    	</div>
    	<div class="inputRow">
        	<label class="myLabel" for="publisher"> Publisher: </label>
        	<input class="myInput" type="text" name="publisher" id="publisher" placeholder="Optional (must be less than 60 characters in length)" tabindex=7 />
    	</div>
    	<div class="inputRow">
        	<label class="myLabel" for="isbn">ISBN: </label>
        	<input class="myInput" type="number" name="isbn" id="isbn"
					placeholder="Please enter the 10-13 digit number ISBN found on the barcode of your book" min="0" max="9999999999999" tabindex=8/>
    	</div>
		    <div class="inputRow">
        	<label class="myLabel" for="price"> <span class="asterisk">*</span> Price: </label>
        	<input class="myInput" type="number" name="price" id="price" step="0.01" placeholder="Please enter in this format 12.34 (put 0 if free)" title="Please enter in this format 12.34 (put 0 if free)" 
					min="0" max="99999999"  tabindex=9 required/>
    	</div>
    	<div class="inputRow">
        	<label class="myLabel" for="contact"> <span class="asterisk">*</span> Contact: </label>
        	<input class="myInput" type="text" name="contact" id="contact" placeholder="Your phone number or email" title="Your phone number or email" tabindex=10 required />
    	</div>
    	<div class="inputRow">
        	<label class="myLabel" for="image_uploads"> <span class="asterisk">*</span> Photo: </label>
			 <div class= "upload">
				<input class="uploadInput" type="file" id="image_uploads" name="image" accept="image/*" onchange="preview_image(event)" tabindex=11  required />
        		<img id="preview_img" src="" /><br/>
			</div>
    	</div>
      	
		<h3><span class="asterisk">*</span> Please select which type of book you are selling:</h3>  
			<div class="w3-cell-row" id="BookTypes">		
			<div class="TextBookForm">
				<label><input id="textbookRad" type="radio" name="bookType" value="Text" onclick="disableGenBook();" tabindex=12 >
	     		<span class="radioLabel"> Textbook</span><br></label>		
				<div class="radio1">
					<div id="progRow">
						<label class="myLabel2" for="program"> <span class="asterisk2">*</span> Area of Study: </label>
						<select name="program" id="program">
							<option value="default"> Please select a program </option>
				           	<optgroup label="A">
								<option value="Accounting">Accounting</option>
								<option value="Anthropology">Anthropology</option>
								<option value="Art">Art</option>
								<option value="Astronomy">Astronomy</option>
						  	<optgroup label="B">
								<option value="Business">Business</option>
							<optgroup label="C">
								<option value="Computer Science">Computer Science</option>    
								<option value="Cuisine">Cuisine</option>    
								<option value="Criminology">Criminology </option> 
							<optgroup label="E">
								<option value="Economics">Economics </option>      
								<option value="Education">Education </option> 
								<option value="Engineering">Engineering </option>      
							<optgroup label="F">
								<option value="Fisheries">Fisheries </option> 
								<option value="Forestry">Forestry </option> 
							<optgroup label="H">
								<option value="Health">Health </option> 
								<option value="Horticulture">Horticulture </option> 
							<optgroup label="L">
								<option value="">Language </option> 
								<option value="">Law </option> 
							<optgroup label="M">
								<option value="Management">Management </option>  
								<option value="Media">Media </option>  
							<optgroup label="N">
								<option value="Nursing">Nursing </option>  
							<optgroup label="O">
								<option value="Philosophy">Philosophy </option> 
								<option value="Physics">Physics</option>
								<option value="Psychology">Psychology</option> 
							<optgroup label="S">
								<option value="Science">Science</option> 
								<option value="Sociology">Sociology</option> 
							<optgroup label="T">
								<option value="Tourism">Tourism</option> 
						</select>
					</div>
					<div class="courseRow">
						<label class="myLabel2" for="course"> <span class="asterisk2">*</span> Course Code: </label>
						<select name="course" id="course" tabindex=13 >
							<option value="default"> Choose a course code: </option>
							<option value="ACCT"> ACCT </option>
							<option value="ANTH"> ANTH </option>
							<option value="ARTT"> ARTT </option>
							<option value="ASTR"> ASTR </option>
							<option value="BUSI"> BUSI </option>
							<option value="CSCI"> CSCI </option>
							<option value="COOK"> COOK </option>
							<option value="CRIM"> CRIM </option>
							<option value="ECON"> ECON </option>
							<option value="EDUC"> EDUC </option>
							<option value="ENGI"> ENGI </option>
							<option value="FISH"> FISH </option>
							<option value="FRST"> FRST </option>
							<option value="HLTH"> HLTH </option>
				  			<option value="HORT"> HORT </option>
				  			<option value="LNGE"> LNGE </option>
				  			<option value="LAW">  LAW  </option>
				  			<option value="MGMT"> MGMT </option>
				  			<option value="MEDI"> MEDI </option>
				  			<option value="NURS"> NURS </option>
				  			<option value="PHIL"> PHIL </option>
						  	<option value="PHYS"> PHYS </option>
						  	<option value="PSYC"> PSYC </option>
						  	<option value="SCNC"> SCNC </option>
						  	<option value="SOCI"> SOCI </option>
						  	<option value="TOUR"> TOUR </option>
						</select>
					</div>
					<div class="inputRow">
						<label class="myLabel2" for="courseNum"> <span class="asterisk2">*</span> Course No.: </label>
        				<input class="myInput2" type="number" name="courseNum" id="courseNum" 
            					placeholder="For CHEM100 type 100" title=" Please enter the course code number ex: for CHEM 100, type 100" min="100" max="999"/>
        			</div>
					<div class="inputRow">
              			<label class="myLabel2" for="instructor"> <span class="asterisk2">*</span> Instructor: </label>
              			<input class="myInput2" type="text" name="instructor" id="instructor" placeholder="First Name" pattern="[a-zA-Z]{,60}"/>
        			</div>
      			</div> <!-- radio1 -->
    		</div> <!--textbook form div-->
			<div class="GeneralForm">
      			<label><input id="generalRadio" type="radio" name="bookType" value="Book" onclick="disabletxtBook()" tabindex=14 >
      			<span class="radioLabel">General Book</span></label><br>
				<div class="radio2">
					<div class="courseRow">
						<label class="myLabel2" for="categories"> <span class="asterisk3">*</span> Category: </label>
						<select name="categories" id="categories">
						  <option value="default"> Please select a category </option>
						  <optgroup label="A">
							<option value="Agriculture">Agriculture</option> 
							<option value="Anatomy">Anatomy</option> 
							<option value="Anthologies">Anthologies</option> 
							<option value="Astronomy">Astronomy</option> 
						  <optgroup label="B">
							<option value="Biographies">Biographies</option> 
							<option value="Biology">Biology</option> 
						  <optgroup label="C">
							<option value="Careers">Careers</option>
							<option value="Comics">Comics</option> 
							<option value="Cooking">Cooking</option> 
							<option value="Critism">Critism</option> 
						  <optgroup label="D">
							<option value="Databases">Databases</option> 
							<option value="Dentistry">Dentistry</option> 
							<option value="Dictionaries">Dictionaries</option> 
						  <optgroup label="E">
							<option value="Economics">Economics</option> 
							<option value="Education">Education</option>
							<option value="EthnicAndCultural">Ethnic and Cultural</option>
						  <optgroup label="F">
							<option value="Family">Family</option> 
							<option value="Fiction">Fiction</option> 
							<option value="Finance">Finance</option> 
							<option value="Fitness">Fitness</option> 
						  <optgroup label="G">
							<option value="Games">Games</option> 
							<option value="Garden">Garden</option> 
						  <optgroup label="H">
							<option value="Health">Health</option> 
							<option value="Historical">Historical</option> 
							<option value="History">History</option> 
							<option value="Hobbies">Hobbies</option> 
							<option value="Home">Home</option> 
							<option value="Horror">Horror</option> 
							<option value="Humor">Humor</option> 
						  <optgroup label="I">
							<option value="Industries">Industries</option> 
							<option value="International">International</option> 
						  <optgroup label="K">
							<option value="Kvalues">Kvalues</option> 
						  <optgroup label="L">
							<option value="Language">Language</option> 
						  <optgroup label="M">
							<option value="Medical">Medical</option>
							<option value="Military">Military</option> 
							<option value="Movies">Movies</option> 
							<option value="Mysteries">Mysteries</option> 
						  <optgroup label="P">
							<option value="Painting">Painting</option> 
							<option value="Parenting">Parenting</option>
							<option value="Photography">Photography</option> 
							<option value="Poetry">Poetry</option> 
						  <optgroup label="R">
							<option value="Religion">Religion</option> 
							<option value="Romance">Romance</option> 
						  <optgroup label="S">
							<option value="Sciences">Sciences</option> 
							<option value="Sculptures">Sculptures</option> 
							<option value="SocialSkills">Social Skills</option> 
							<option value="Sports">Sports</option> 
						  <optgroup label="T">
							<option value="Travel">Travel</option> 
						</select>
					</div>
				</div> <!-- radio2 -->
			</div> <!-- general form-->
		</div> <!--BookTypes-->
    </div>
	<div class="w3-row">	
		<div class="w3-col m6" style="text-align: center;"><input class="w3-button w3-teal w3-xlarge" type="reset" name="reset" value="Clear Sheet" tabindex=15 /></div>
    	<div class="w3-col m6" style="text-align: center;"><input class="w3-button w3-green w3-xlarge" type="submit" name="go" value="Post my Book!" tabindex=16 /></div>
	</div>
    <?php if(isset($err)) echo "<h3 style='text-align: center;'>".$err."</h3>";?>
    <?php if(isset($success)) echo "<h3 style='text-align: center;'>".$success."</h3>";?>
	<div class="w3-row" style="text-align: center;">
    	<?php if(isset($success)) echo "<p style='color:#236AB9'><em>Willing to trade?</em></p>
				<a href='./AddTradeBook.php'><input class='w3-button w3-green w3-large' value='Add Trade Book'/></a>";?>
	</div>
</form>
<script>
	//grey out asterisks in the field the user does not have to enter
	$("#generalRadio").on("click", function(){
		$(".asterisk2").css("color", "grey");
		$(".asterisk3").css("color", "red");	
	});
	$("#textbookRad").on("click", function(){
		$(".asterisk3").css("color", "grey");
		$(".asterisk2").css("color", "red");	
	});
</script>
</div><!--main-->
	<footer><small><b> Tiffany & Ning Copyright &copy; 2019 </b></small></footer>
</body>
</html>
