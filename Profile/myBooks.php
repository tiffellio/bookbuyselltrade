<?php
	session_start(); /* Starts the session */
	if(!isset($_SESSION['UserData']['userid'])){
		header("location:../login/login.php");			
		exit;
	}
	require_once("../private/dbinfo.inc");
	$err;
	$myHandle;
	$i = 1;
	try{
		$myHandle = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
	}catch(PDOException $e){
		$err .= "Connection failed \n";
	}

	if($myHandle){
		$stmt = $myHandle->prepare("SELECT Account_ID FROM Account WHERE userid=:u_id");
		$stmt->bindParam(':u_id', $_SESSION['UserData']['userid']);
		$stmt->execute();
		$Account_ID = $stmt->fetchColumn();

		$mybooks = $myHandle->prepare("select Photo, Title, Author, Publisher, Price, UserName, Contact, Book_ID
					from Books join Account on Books.Account_ID = Account.Account_ID
					where sold_Time = '0000-00-00' and Books.Account_ID =:Account_ID;");
			$mybooks->bindParam(':Account_ID', $Account_ID);		
			$mybooks->execute();
			$rsltbooks = $mybooks->fetchAll();	
		foreach($rsltbooks as $row){
			foreach($row as $field=>$value){
				$books[$i][$field] = htmlspecialchars($value);
			}
			$i++;
		}
		$numBooks = sizeof($books);
	
	}

	if(isset($_POST['booknum'])){
		$mybooks = $myHandle->prepare("update Books set Sold_Time =:Sold_Time where Book_ID = :Book_ID;");
		$Sold_Time = date("Y-m-d");
		$mybooks->bindParam(':Sold_Time', $Sold_Time);	
		$mybooks->bindParam(':Book_ID', $_POST['booknum']);	
		$mybooks->execute();
		header("Location: myBooks.php");
	}
	$myHandle = null;
?>
<!DOCTYPE html>
<!-- CSCI311 | Names: Tiffany & Ning | Final Project -->
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
	<meta charset="UTF-8" />
	<title>BOOK Trade</title>
	<link rel="stylesheet" type="text/css" href="./myBookStyles.css">
	<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

</head>

<body>
<!-- ************* POPUP ***************** -->
<!--Creates the popup body-->
<div class="popup-overlay">
  <!--Creates the popup content-->
   <div class="popup-content">
      <h2 class="titleArea">Title:</h2>
      <p><p class="insertionArea"></p>
     <!--popup's close button-->
      <button class="close">Close</button>    
	</div>
</div>
	<div id="main">
		<div class="myNavBar">
			<ul class="myNav">
      			<li class="pagelink"><a href="../index.php" tabindex=1>Home <i class="fa fa-home w3-padding" style="color: #EBF7D4;"></i></a></li>
	  			<li class="pagelink"><a href="../Buybook/Buybook.php" tabindex=2>Buy Books</a></li>
      			<li class="pagelink"><a href="../Sellbook/Sellbook.php" tabindex=3>Sell Books</a></li>
      			<li class="pagelink"><a href="../Tradebook/Tradebook.php" tabindex=3>Trade Books</a></li>
      			<li class="login"><a href="../login/logout.php"  tabindex=4>Logout</a></li>
			</ul>
		</div>

		<div class="w3-container">
			<header><h2>My Profile</h2><h3> Here are your books! </h3></header>
		<section class="myResults">		
			<p>Total <?php echo $numBooks ?> Books not yet Sold</p>	
	<?php
		for($j=1; $j<=$numBooks; $j++){ 
			echo "	<div class='bookInfo'>
						<form class= 'soldform' action='myBooks.php' method='post'>
							<input type='hidden' name='booknum' value='{$books[$j]['Book_ID']}'/>
							<div class='soldDiv'><input class='Sold' type='submit' name='Sold' value='Mark as Sold'/></div>
						</form>
						<div class='bookimg'><a title='".htmlspecialchars($books[$j]['Title'])."'>
							<img class='imgs' src= '.{$books[$j]['Photo']}' alt='".htmlspecialchars($books[$j]['Title'])."'/></a></div>
						<div class='bookDescription'>
							<span>Title:</span><p>{$books[$j]['Title']}</p><br/>
				        	<span>By:</span><p>{$books[$j]['Author']}</p><br/>
							<span>Publisher:</span><p>{$books[$j]['Publisher']}</p><br/>
				        	<span>Price:</span><p >{$books[$j]['Price']}</p><br/>
				        	<span>Owner:</span><p>{$books[$j]['UserName']}</p><br/>
							<span>Contact:</span><p>{$books[$j]['Contact']}</p>
						</div>

				    </div>\n";
		}
	?>
		</section>
	</div>
</div>
	<footer><small><b> Tiffany & Ning Copyright &copy; 2019 </b></small></footer>
<!-- ************* JQUERY ***************** -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<pre><code>
<script>
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
</code></pre>
</body>

</html>
