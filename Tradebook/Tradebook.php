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
//			if($start_index = 1 || $start_index + $item_per < $max_index){
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
	$myHandle;
	$i = 1;
	try{
		$myHandle = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
	}catch(PDOException $e){
		$err .= "Connection failed \n";
	}

	if($myHandle){
		$mybooks = "select Photo, Title, Author, Publisher, Price, UserName, Contact, TB_Photo, TB_Title, TB_Author, TB_Publisher 
					from Books, Account, Trad_Books 
					where Books.Book_ID = Trad_Books.Book_ID and Books.Account_ID = Account.Account_ID and sold_Time = '0000-00-00'
					order by Post_Time desc;";
		$rsltbooks = $myHandle->query($mybooks);
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
	<meta charset="UTF-8" />
	<title>BOOK Trade</title>
	<link rel="stylesheet" type="text/css" href="./tradeStyles.css">
	<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

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

		<div class="w3-container">
			<header><h2>Book trading center</h2></header>
			<form action="Tradebook.php" method="post">
			<p>Total <?php echo $numBooks ?> results</p>
			<i class="fa fa-arrow-left w3-padding" style="color: #342E09;"></i>
			<input class="prev" type="submit" name="page" value="Prev"/>
			<p>Result <?php echo $start_index?> to <?php echo $end_num?> </p>
			<input class="next" type="submit" name="page" value="Next"/>
			<i class="fa fa-arrow-right w3-padding" style="color: #342E09;"></i>
		<?php
			echo "<input type='hidden' name='n_end' value='$end_index'/>\n";
			echo "<input type='hidden' name='n_start' value='$start_index'/>\n";
		?>
			</form>
		<section class="myResults">
			<h3> Want this book? </h3>			
	<?php
		for($j=$start_index ; $j<=$max_index && $j<=$end_index; $j++){ 
			echo "	<div class='bookInfo'>
				<div class='bookimg'><a title='{$books[$j]['Title']}'>
				<img class='imgs' src= '.{$books[$j]['Photo']}' alt='{$books[$j]['Title']}'/></a></div>
				<div class='bookDescription'>
					<span>Title:</span><p>{$books[$j]['Title']}</p><br/>
                	<span>By:</span><p>{$books[$j]['Author']}</p><br/>
					<span>Publisher:</span><p>{$books[$j]['Publisher']}</p><br/>
                	<span>Price:</span><p style='text-decoration: line-through;'>{$books[$j]['Price']}</p><br/>
                	<span>Owner:</span><p>{$books[$j]['UserName']}</p><br/>
					<span>Contact:</span><p>{$books[$j]['Contact']}</p>
				</div>
            </div>\n";
		}
	?>
		</section>
		<aside class="tradebooks">
			<h3> ..they will trade you for this one! </h3>
	<?php
		for($j=$start_index ; $j<=$max_index && $j<=$end_index; $j++){ 
			echo "	<div class='bookInfo2'>
				<div class='bookimg'><a title='{$books[$j]['TB_Title']}'>
				<img class='imgs' src= '{$books[$j]['TB_Photo']}' alt='{$books[$j]['TB_Title']}'/></a></div>
				<div class='bookDescription'>
					<span>Title:</span><p>{$books[$j]['TB_Title']}</p><br/>
                	<span>By:</span><p>{$books[$j]['TB_Author']}</p><br/>
					<span>Publisher:</span><p>{$books[$j]['TB_Publisher']}</p><br/>
				</div>
            </div>\n";
		}
	?>
		</aside>

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
