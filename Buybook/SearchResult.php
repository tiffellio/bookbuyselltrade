<!-- CSCI311 | Names: Tiffany & Ning | Final Project -->
<?php
	require_once("../private/dbinfo.inc");
	session_start();
	$_SESSION['url'] = $_SERVER['REQUEST_URI'];
	$err;
	$start_index;
	$end_index;
	$max_index;
	$item_per = 2;	
	if(isset($_POST['page'])){
		if(isset($_POST['n_start'])) $start_index = $_POST['n_start'];
		if(isset($_POST['n_end'])) $end_index = $_POST['n_end'];
		if($_POST['page'] == "Next"){
//			if($start_index == 1 || $start_index + $item_per < $max_index){
				$start_index += ($item_per+1);
//			}else if($start_index + $item_per >= $max_index){
//				$start_index = $start_index;
//			}
		}else if($_POST['page'] == "Prev"){
			if($start_index-$item_per >=1){
				$start_index-=($item_per+1);
			}else{
				$start_index = 1;
			}
		}
		$end_index = $start_index + $item_per;	
	}else{
		$start_index = 1;
		$end_index = $start_index + $item_per;
	}
	$Area = array("Accounting", "Anthropology", "Art", "Astronomy", "Business", "Computer Science", "Cuisine", "Criminology", "Economics", "Education", "Engineering", "Fisheries", "Forestry", "Health", "Horticulture", "Language", "Law", "Management", "Media", "Nursing", "Philosophy", "Physics", "Psychology", "Science", "Sociology", "Tourism");
	$A_Z = array("A%", "B%", "C%", "D%", "E%", "F%", "G%", "H%", "I%", "J%", "K%", "L%", "M%", "N%", "O%", "P%", "Q%", "R%", "S%", "T%", "U%", "V%", "W%", "X%", "Y%", "Z%");
	$Categorie = array("Agricultural", "Anatomy", "Art", "Anthologies", "Astronomy", "Biographies", "Biology", "Careers", "Comics", "Computer", "Cooking", "Critism", "Databases", "Dentistry", "Dictionaries", "Economics", "Education", "Ethnic & Cultural", "Family", "Fiction", "Finance", "Fitness", "Games", "Garden", "Health", "Historical", "History", "Hobbies", "Home", "Horror", "Humor", "Industries", "International", "Kids", "Language", "Literature", "Medical", "Military", "Movies", "Music", "Mysteries", "Painting", "Parenting", "Photography", "Poetry", "Religion", "Romance", "Sciences", "Sculpture", "Social Skills", "Sports", "Travel");
	$myHandle;
	try{
		$myHandle = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
	}catch(PDOException $e){
		$err .= "Connection failed \n";
	}
	if(isset($_GET['keyword'])){
		$keyword = "%".$_GET['keyword']."%";
		$i = 1;
		if($myHandle){
			$mybooks = $myHandle->prepare("select Photo, Title, Author, Publisher, Price, UserName, Contact
											from Books join Account on Books.Account_ID = Account.Account_ID
											where sold_Time = '0000-00-00'
											and Title like :Title or Author like :Author or Publisher like :Publisher;");
			$mybooks->bindParam(':Title', $keyword);
			$mybooks->bindParam(':Author', $keyword);
			$mybooks->bindParam(':Publisher', $keyword);
			$mybooks->execute();
			$rsltbooks = $mybooks->fetchAll();
			foreach($rsltbooks as $row){
				foreach($row as $field=>$value){
					$books[$i][$field] = htmlspecialchars($value);
				}
				$i++;
			}
			$numBooks = sizeof($books);
			$max_index = $numBooks;
			$mybooks = null;
			$myHandle = null;
			$end_num = $end_index;
			if($end_num>=$max_index){
				$end_num=$max_index;
			}
		}
	}else if(isset($_GET['Area'])||isset($_GET['Code'])||isset($_GET['Inst'])){
		$Program = $Area[$_GET['Area']-1];
		$Course = $A_Z[$_GET['Code']-1];
		$Instructor = $A_Z[$_GET['Inst']-1];
		$i = 1;
		if($myHandle){
			$mybooks = $myHandle->prepare("select Photo, Title, Author, Publisher, Price, UserName, Contact 
											from Books join Account on Books.Account_ID = Account.Account_ID
											where sold_Time = '0000-00-00' and Books.Book_ID IN 
											(select Books.Book_ID from Books join Text_Books on Text_Books.Book_ID = Books.Book_ID 
											where Program = :Program or Course like :Course or Instructor like :Instructor);");
			$mybooks->bindParam(':Program', $Program);
			$mybooks->bindParam(':Course', $Course);
			$mybooks->bindParam(':Instructor', $Instructor);
			$mybooks->execute();
			$rsltbooks = $mybooks->fetchAll();
			foreach($rsltbooks as $row){
				foreach($row as $field=>$value){
					$books[$i][$field] = htmlspecialchars($value);
				}
				$i++;
			}
			$numBooks = sizeof($books);
			$max_index = $numBooks;
			$mybooks = null;
			$myHandle = null;
			$end_num = $end_index;
			if($end_num>=$max_index){
				$end_num=$max_index;
			}
		}
	}else if(isset($_GET['Categorie'])||isset($_GET['Title'])){
		$Categories = $Categorie[$_GET['Categorie']-1];
		$Title = $A_Z[$_GET['Title']-1];
		$i = 1;
		if($myHandle){
			$mybooks = $myHandle->prepare("select Photo, Title, Author, Publisher, Price, UserName, Contact 
											from Books join Account on Books.Account_ID = Account.Account_ID
											where sold_Time = '0000-00-00' and Books.Book_ID IN 
											(select Books.Book_ID from Books join General_Books on General_Books.Book_ID = Books.Book_ID 
											where Categories = :Categories or Title like :Title);");
			$mybooks->bindParam(':Categories', $Categories);
			$mybooks->bindParam(':Title', $Title);
			$mybooks->execute();
			$rsltbooks = $mybooks->fetchAll();
			foreach($rsltbooks as $row){
				foreach($row as $field=>$value){
					$books[$i][$field] = htmlspecialchars($value);
				}
				$i++;
			}
			$numBooks = sizeof($books);
			$max_index = $numBooks;
			$mybooks = null;
			$myHandle = null;
			$end_num = $end_index;
			if($end_num>=$max_index){
				$end_num=$max_index;
			}
		}
	}
?>
<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml" lang="en">

<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Search Books</title>
    <link rel="stylesheet" type="text/css" href="./css/ResultStyles.css" />
	<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>

<body>
<!-- ************* POPUP ***************** -->
<!--Creates the popup body-->
<div class="popup-overlay">
  <!--Creates the popup content-->
	<div class="popup-content">
		<h2 class="titleArea"></h2>
		<p><p class="insertionArea"></p>
		<!--popup's close button-->
		<div class="closeButton"><button class="close">Close</button></div>    
	</div>
</div>
<!-- *********** Navigation Bar *********** -->
	<div id="main">
		<div class="topnavBar">
			<div class="topnav" id="myTopnav">
			  <a href="../index.php" class="active">Home</a>
			  <a href="../Buybook/Buybook.php">Buy Books</a>
			  <a href="../Sellbook/Sellbook.php" >Sell Books</a>
			  <a href="../Tradebook/Tradebook.php">Trade Books</a>
			  <a href="./Allbooks.php" >All Books</a>
			  <a href="javascript:void(0);" class="icon" onclick="myFunction()">
				<i class="fa fa-bars"></i>
			  </a>
				<?php
				if(!isset($_SESSION['UserData']['userid'])){
					echo "<a class=\"login\" href=\"../login/login.php\">Login</a>";
				}else{
					echo "<a class=\"login\" href=\"../Profile/myBooks.php\">Profile</a>";
					echo "<a class=\"login\" href=\"../login/logout.php\">Logout</a>\n";
				}
				?>
			</div>	
		</div>	

		<div class="w3-container">
			<h2>Search Result</h2>			
			<form action="" method="post">
				<p id="totalResults">Total <?php echo $numBooks ?> results</p>
				<i class="fa fa-arrow-left w3-padding" id="leftArrow" style="color: #342E09;"></i>
				<input class="prev" type="submit" name="page" value="Prev"/>
				<p>Result <?php echo $start_index?> to <?php echo $end_num?> </p>
				<input class="next" type="submit" name="page" value="Next"/>
				<i class="fa fa-arrow-right w3-padding" id="rightArrow" style="color: #342E09;"></i>
		<?php
			echo "<input type='hidden' name='n_end' value='$end_index'/>\n";
			echo "<input type='hidden' name='n_start' value='$start_index'/>\n";
		?>
			</form>
		<section class="myResults">
	<?php
		echo  "<p style='color: #347B98'>space</p>";
	if($max_index!=0){
		for($j=$start_index ; $j<=$max_index && $j<=$end_index; $j++){ 
			echo "	<div class='bookInfo'>
				<div class='bookimg'>
				<a title='{$books[$j]['Title']}'><img class='imgs' src= '.{$books[$j]['Photo']}' alt='{$books[$j]['Title']}'/></a></div>
				<div class='bookDescription'>
					<span>Title:</span style='font-size:5vw;'><p>{$books[$j]['Title']}</p><br/>
                	<span>By:</span><p>{$books[$j]['Author']}</p><br/>
					<span>Publisher:</span><p>{$books[$j]['Publisher']}</p><br/>
                	<span>Price:</span><p>{$books[$j]['Price']}</p><br/>
                	<span>Owner:</span><p>{$books[$j]['UserName']}</p><br/>
					<span>Contact:</span><p>{$books[$j]['Contact']}</p>
				</div>
            </div>\n";
		}
	}else{
		echo  "<p>Sorry, we couldn't find anything! Want to try <a href='../Buybook/Buybook.php'> again? </a> </p>";
	}
	?>
		</section>
		<div class="sidebar">
				<div class="sidebarLabel"><p class="active">You might find these interesting</p></div>
				<div class="otherbooksbookimg">
	<?php
			try{
				$myHandle = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
			}catch(PDOException $e){
				$err .= "Connection failed \n";
			}if($myHandle){
				$myText = "select Photo, Title from Books where Sold_Time = '0000-00-00' order by Post_Time desc;";
				$rsltText = $myHandle->query($myText);
				$i = 1;
				$Textbooks;
				foreach($rsltText as $row){
					foreach($row as $field=>$value){
						$Textbooks[$i][$field] = $value;
					}
					$i++;
				}
				for($m=1; $m<=6; $m++){
					echo "<a href='#' title='".htmlspecialchars($Textbooks[$m]['Title'])."'>
						  <img class='imgs' src='.".htmlspecialchars($Textbooks[$m]['Photo'])."' alt='".htmlspecialchars($Textbooks[$m]['Title'])."'>
						  </a>\n";
				}
			}
			$myHandle = null;
	?>
				</div>
		</div> <!--- end sidebar-->
		</div>
	</div>
	<footer><small><b> Tiffany & Ning Copyright &copy; 2019 </b></small></footer>
<!-- ************* JQUERY ***************** -->
<script>
//for the navigation bar
function myFunction() {
  var x = document.getElementById("myTopnav");
  if (x.className === "topnav") {
    x.className += " responsive";
  } else {
    x.className = "topnav";
  }
}
	//When image is selected display the popup and wrap the image into the popup, displaying the alt text at the title
	$(".imgs").on("click", function(){
	  $(".popup-overlay, .popup-content").addClass("active");
		$('.insertionArea').wrap(this);
		var titles = $("img.imgs").attr("alt");
		$(".titleArea").text(titles);		
	});
	//When close is pressed re-hide the popup and unwrap the currently selected image
	$(".close, .popup-overlay").on("click", function(){
		$('.insertionArea').unwrap("img.imgs");
	  $(".popup-overlay, .popup-content").removeClass("active");
	});
</script>
</body>
</html>
