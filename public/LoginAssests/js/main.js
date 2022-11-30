//hide and show form in the same page

/* On click toggle signin & Create Account Button */
$('.toggleBtn').click(function () {
$('.toggleBtn').toggle();
})

/* On click SignIn button */
$('.loginBtn').click(function () {
$('#signUp').hide();
$('#signIn').show();
})


/* On click create account button */
$('.signUpBtn').click(function () {
$('#signUp').show();
$('#signIn').hide();
$('.loginBtn').show(); 
})

/* On click hide/show forms on mobile */
$('.loginBtn-mobile').click(function () {
$('#signUp').hide();
$('#signIn').show();
})

$('.signUpBtn-mobile').click(function () {
$('#signUp').show();
$('#signIn').hide();
})


///////////////////////////////////////////////////////////////////////////////////////////////////

function validateForm() {  
    //collect form data in JavaScript variables  
    var pw1 = document.getElementById("pswd1").value;  
    var pw2 = document.getElementById("pswd2").value;  
 
    
    //check empty password field  
    if(pw1 == "") {  
      document.getElementById("message1").innerHTML = "**Fill the password please!";  
      return false;  
    }  
    
    //check empty confirm password field  
    if(pw2 == "") {  
      document.getElementById("message2").innerHTML = "**Enter the password please!";  
      return false;  
    }   
     
    //minimum password length validation  
    if(pw1.length < 8) {  
      document.getElementById("message1").innerHTML = "**Password length must be atleast 8 characters";  
      return false;  
    }  
  
    //maximum length of password validation  
    if(pw1.length > 15) {  
      document.getElementById("message1").innerHTML = "**Password length must not exceed 15 characters";  
      return false;  
    }  
    
    if(pw1 != pw2) {  
      document.getElementById("message2").innerHTML = "**Passwords are not same";  
      return false;  
    } else {  
      alert ("Your password created successfully");  
      document.write("JavaScript form has been submitted successfully");  
    }  
  
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//animations
setTimeout(function(){
  $('.login-block').addClass('magictime puffIn');
}, 5);
setTimeout(function(){
  $('.loginForm').addClass('magictime spaceInRight');
});
//////////////////////////////////////////////////////////////////////////////////////////////////////////////

///backgroiund refresh"
function randomImage(){
  var images = [
    "LoginAssests/img/background.jpeg", 
    "LoginAssests/img/1.jpg",
    "LoginAssests/img/2.jpg",
    "LoginAssests/img/4.jpg",
    "LoginAssests/img/5.jpg",
    "LoginAssests/img/6.jpg",
    "LoginAssests/img/7.jpg",
    "LoginAssests/img/8.jpg",
    "LoginAssests/img/9.jpg",
    "LoginAssests/img/10.jpg",
 ];
 
  var size = images.length;
  var x = Math.floor(size * Math.random());
  var element = document.getElementsByClassName('login-block');
  element[0].style["background-image"] = "url("+ images[x] + ")";
}

document.addEventListener("DOMContentLoaded", randomImage);
