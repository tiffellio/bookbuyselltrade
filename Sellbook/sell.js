//disable all text field entries to ensure the user picks an option first
function disableAllBooks(){
  document.getElementById("program").disabled = true;  
  document.getElementById("course").disabled = true;   
  document.getElementById("courseNum").disabled = true;   
  document.getElementById("instructor").disabled = true;  
  document.getElementById("categories").disabled = true; 
}
//general books, enable text book radio button and clear any
//entered input from the general books field
function disableGenBook() {
  enabletxtBook();
  resetBookField();
  document.getElementById("categories").disabled = true; 
}
//helper function to enable the general book field
function enableGenBook() {
  document.getElementById("categories").disabled = false; 
}
//helper function to reset the general book field when the text book field is cleared
function resetBookField(){
	document.getElementById("categories").selectedIndex = 0; 
}
//text books, enable text book radio button and clear any
//entered input from the general books field
function disabletxtBook(){
  enableGenBook();
  resetTxtBookField();
  //disable fields
  document.getElementById("program").disabled = true;  
  document.getElementById("course").disabled = true;   
  document.getElementById("courseNum").disabled = true;   
  document.getElementById("instructor").disabled = true;  
}
//helper function to enable textbook field when radio button is selected
function enabletxtBook(){
  document.getElementById("program").disabled = false;  
  document.getElementById("course").disabled = false;   
  document.getElementById("courseNum").disabled = false;   
  document.getElementById("instructor").disabled = false; 
}
//helper function to clear the textbook field when the general book field is selected
function resetTxtBookField(){
	document.getElementById("program").selectedIndex = 0; 
	document.getElementById("course").selectedIndex = 0; 
	document.getElementById("courseNum").value = ""; 	
	document.getElementById("instructor").value = "";
}


