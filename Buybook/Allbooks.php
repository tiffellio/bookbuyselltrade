<!-- CSCI311 | Names: Tiffany & Ning | Final Project -->
<?php
	require_once("../private/dbinfo.inc");
	session_start();
	$_SESSION['url'] = $_SERVER['REQUEST_URI'];
	$err;
	$start_index;
	$end_index;
	$max_index;
	$item_per = 3;	
	if(isset($_POST['page'])){
		if(isset($_POST['n_start'])) $start_index = $_POST['n_start'];
		if(isset($_POST['n_end'])) $end_index = $_POST['n_end'];
		if($_POST['page'] == "Next"){
//			if(($start_index=1) || ($start_index+$item_per-1) < $max_index){
				$start_index += $item_per;
//			}else if(($start_index+$item_per) > $max_index){
				$start_index = $start_index;
//			}
		}else if($_POST['page'] == "Prev"){
			if(($start_index-$item_per) >=1){
				$start_index-=$item_per;
			}else{
				$start_index = 1;
			}
		}
		$end_index = $start_index + $item_per -1;	
	}else{
		$start_index = 1;
		$end_index = $start_index + $item_per -1;
	}
	$myHandle;
	$i = 1;
	try{
		$myHandle = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
	}catch(PDOException $e){
		$err .= "Connection failed \n";
	}
	if($myHandle){
		$mybooks = $myHandle->prepare("select Photo, Title, Author, Publisher, Price, UserName, Contact
					 from Books join Account on Books.Account_ID = Account.Account_ID
					 where Sold_Time = '0000-00-00' order by Post_Time desc;");
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
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>All Books</title>
    <link rel="stylesheet" type="text/css" href="./css/AllbooksStyles.css">
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
		
	<div class="w3-container">
		<h2>Search All Book Result</h2>			
			<form action="./Allbooks.php" method="POST">
				<p>Total <?php echo $numBooks ?> results</p>
				<i class="fa fa-arrow-left w3-padding" style="color: #342E09;"></i>
				<input class="prev" type="submit" name="page" value="Prev"/>
				<p>Result <?php echo $start_index?> to <?php echo $end_num?> </p>
				<input class="next" type="submit" name="page" value="Next"/>
				<i class="fa fa-arrow-right w3-padding" style="color: #342E09;"></i>
		<?php
			echo "<input type='hidden' name='n_start' value='$start_index'/>\n";
			echo "<input type='hidden' name='n_end' value='$end_index'/>\n";
		?>
			</form>
		<section class="myResults">
	<?php
		for($m=$start_index ; $m<=$max_index && $m<=$end_index; $m++){ 
			echo "	<div class='bookInfo'>
				<div class='bookimg'>
				<a title='{$books[$m]['Title']}'><img class='imgs' src= '.{$books[$m]['Photo']}' alt='{$books[$m]['Title']}'/></a></div>
				<div class='bookDescription'>
					<span>Title:</span><p>{$books[$m]['Title']}</p><br/>
                	<span>By:</span><p>{$books[$m]['Author']}</p><br/>
					<span>Publisher:</span><p>{$books[$m]['Publisher']}</p><br/>
                	<span>Price:</span><p>{$books[$m]['Price']}</p><br/>
                	<span>Owner:</span><p>{$books[$m]['UserName']}</p><br/>
					<span>Contact:</span><p>{$books[$m]['Contact']}</p>
				</div>
            </div>\n";
		}
	?>
		</section>
	</div>
</div>
	<footer><small><b> Tiffany & Ning Copyright &copy; 2019 </b></small></footer>
<!-- ************* JQUERY ***************** -->
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
