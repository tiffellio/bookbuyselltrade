<!-- CSCI311 | Names: Tiffany & Ning | Final Project -->
<?php
	session_start();
	$_SESSION['url'] = $_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Buy Books</title>
    <link rel="stylesheet" type="text/css" href="./css/buyStyles.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script>
		$(document).ready(function(){
  			$("#SearchKey").change(function(){
				$("#textBooksform")[0].reset();
				$("#genBooksform")[0].reset();
  			});
  			$("input.Area").change(function(){
    			$("#genBooksform")[0].reset();
				$("#keywordform")[0].reset();
  			});
  			$("input.Code").change(function(){
    			$("#genBooksform")[0].reset();
				$("#keywordform")[0].reset();
  			});
  			$("input.Inst").change(function(){
    			$("#genBooksform")[0].reset();
				$("#keywordform")[0].reset();
  			});
  			$("input.Catg").change(function(){
    			$("#textBooksform")[0].reset();
				$("#keywordform")[0].reset();	
  			});
  			$("input.Title").change(function(){
    			$("#textBooksform")[0].reset();
				$("#keywordform")[0].reset();
  			});
		});
	
		function submit_forms(){
			if ($("#SearchKey").val()!= "" ){
				$("#keywordform").submit();
			}else if(typeof $("input[type=radio][name=Area]:checked").val()!== "undefined" 
					|| typeof $("input[type=radio][name=Code]:checked").val()!== "undefined" 
					|| typeof $("input[type=radio][name=Inst]:checked").val()!== "undefined"){		
				$("#textBooksform").submit();
			}else if(typeof $("input[type=radio][name=Categorie]:checked").val()!== "undefined" 
					|| typeof $("input[type=radio][name=Title]:checked").val()!== "undefined"){
				$("#genBooksform").submit();
			}
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
				<li class="pagelink"><a href="./Allbooks.php" tabindex=4>All Books</a></li>
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
		<?php	
			
		?>
    	<div class="mySearchBar">
			<form id="keywordform" action="./SearchResult.php" method="GET">
    			<input type="text" name="keyword" id="SearchKey" title="Search for books by keyword"
					placeholder="Enter a keyword of the title, author or publisher to search" />
			</form>	<!-- keywordform END -->
			<input class="mysearch" type="button" name="mysearch" value="search" onclick="submit_forms()"/>
		</div>
		<header>
			<h1>TEXT BOOKS</h1><h1>GENERAL BOOKS</h1>
		</header>
		<div class="myradios">
			<form class="inputform" id="textBooksform" action="./SearchResult.php" method="GET">
				<div class="inputs">
				<?php
					for ($i=1;$i<=26;$i++){
					echo "
						<input class='Area' type='radio' name='Area' id='A".$i."' value=".$i.">
						<label class='label".$i."' for='A".$i."'><img src='./media/TextA/A".$i.".png' alt=''/></label>";
					}
				?>				
				</div>
				<div class="inputs">
				<?php
					for ($i=1;$i<=26;$i++){
						echo "
						<input class='Code' type='radio' name='Code' id='cod".$i."' value=".$i.">
						<label class='label".$i."' for='cod".$i."'><img src='./media/Tcod/cod".$i.".png' alt=''/></label>";
					}
				?>				
				</div>
				<div class="inputs">
				<?php
					for ($i=1;$i<=26;$i++){
						echo "
						<input class='Inst' type='radio' name='Inst' id='Ins".$i."' value=".$i.">
						<label class='label".$i."' for='Ins".$i."'><img src='./media/TIns/Ins".$i.".png' alt=''/></label>";
					}
				?>				
				</div>
			</form>	<!-- textBooksform END -->
			<form class="inputform" id="genBooksform" action="./SearchResult.php" method="GET">
				<div class="inputs">
				<?php
					for ($i=1;$i<=26;$i++){
						echo "
						<input class='Catg' type='radio' name='Categorie' id='G".$i."' value=".$i.">
						<label class='label".$i."' for='G".$i."'><img src='./media/GenG/G".$i.".png' alt=''/></label>";
					}
				?>				
				</div>
				<div class="inputs">
				<?php
					for ($i=27;$i<=52;$i++){
						echo "
						<input class='Catg' type='radio' name='Categorie' id='G".$i."' value=".$i.">
						<label class='label".$i."' for='G".$i."'><img src='./media/GenG/G".$i.".png' alt=''/></label>";
					}
				?>				
				</div>
				<div class="inputs">
				<?php
					for ($i=1;$i<=26;$i++){
						echo "
						<input class='Title' type='radio' name='Title' id='T".$i."' value=".$i.">
						<label class='label".$i."' for='T".$i."'><img src='./media/Gjou/Jou".$i.".png' alt=''/></label>";
					}
				?>				
				</div>		
			</form>	<!-- genBooksformform END -->
		</div>
	</div>
	<footer><small><b> Tiffany & Ning Copyright &copy; 2019 </b></small></footer>
</body>

</html>
