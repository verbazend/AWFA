<?php
include("../booking-manager/application.php");
include("../booking-manager/db.class.php");
include("../booking-manager/functions.php");
include("../booking-manager/esmtp.class.php");
include("../booking-manager/email.class.php");

$messageFailed = false;

$emailKey = $_GET['k'];
$confirmEmail = $_POST['email'];

$emailDetails = email::getEmailByHashID($emailKey,$confirmEmail);

if($emailDetails!=false){
	echo $emailDetails['body'];
} else {
	if($confirmEmail!=""){
		$messageFailed = true;
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>View Email - Australia Wide First Aid</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="/js/jquery-1.11.1.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script type="text/javascript">
        window.alert = function(){};
        var defaultCSS = document.getElementById('bootstrap-css');
        function changeCSS(css){
            if(css) $('head > link').filter(':first').replaceWith('<link rel="stylesheet" href="'+ css +'" type="text/css" />'); 
            else $('head > link').filter(':first').replaceWith(defaultCSS); 
        }
    </script>
   </head>
<body>
<div class="container">
    <div class="row">
        <div class="col-sm-6 col-md-4 col-md-offset-4">
            <br><br>
            <div class="account-wall">
                <form class="form-signin" method="post" action="?k=<?php echo db::esc($emailKey); ?>">
                	<center><img src="/images/awfa_logo_trans.png"></center>
                	<h1 class="text-center login-title">For security reasons,<br>please confirm your email address.</h1>
                	<?php if($messageFailed){ ?>
                	<div class="errornotif"><b>Error:</b> Incorrect email or no message found!</div>
                	<?php } ?>
	                <input type="text" name="email" class="form-control" placeholder="Email" required autofocus><br>
	                <button class="btn btn-lg btn-primary btn-block" type="submit">
	                    Confirm Email Address</button>
                </form>
            </div>
            <a href="https://www.australiawidefirstaid.com.au/terms-conditions/" target="_blank" class="text-center bottomlinks">Terms &amp; Conditions</a> <a href="https://www.australiawidefirstaid.com.au/wp-content/uploads/2014/06/QPOL8-Privacy-Policy.pdf" target="_blank" class="text-center bottomlinks">Privacy Policy</a>
        </div>
    </div>
</div>
<style>
	.form-signin
{
    max-width: 330px;
    padding: 15px;
    margin: 0 auto;
}
.errornotif {
	color:red;
	text-align:center;
	margin-bottom:10px;
}
.form-signin .form-signin-heading, .form-signin .checkbox
{
    margin-bottom: 10px;
}
.form-signin .checkbox
{
    font-weight: normal;
}
.form-signin .form-control
{
    position: relative;
    font-size: 16px;
    height: auto;
    padding: 10px;
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
}
.form-signin .form-control:focus
{
    z-index: 2;
}
.form-signin input[type="text"]
{
    margin-bottom: -1px;
    border-bottom-left-radius: 0;
    border-bottom-right-radius: 0;
}
.form-signin input[type="password"]
{
    margin-bottom: 10px;
    border-top-left-radius: 0;
    border-top-right-radius: 0;
}
.account-wall
{
    margin-top: 30px;
    padding: 5px 0px 5px 0px;
    background-color: #f7f7f7;
    -moz-box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
    -webkit-box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
    box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
}
.login-title
{
    color: #555;
    font-size: 18px;
    font-weight: 400;
    display: block;
    margin-bottom:20px;
}
.profile-img
{
    width: 96px;
    height: 96px;
    margin: 0 auto 10px;
    display: block;
    -moz-border-radius: 50%;
    -webkit-border-radius: 50%;
    border-radius: 50%;
}
.need-help
{
    margin-top: 10px;
}
.new-account, .bottomlinks
{
    display: block;
    margin-top: 10px;
    color:#fff;
}

body{background: #eee url(/images/bgra.jpg);}
html,body{
    position: relative;
    height: 100%;
}

.login-container{
    position: relative;
    width: 300px;
    margin: 80px auto;
    padding: 20px 40px 40px;
    text-align: center;
    background: #fff;
    border: 1px solid #ccc;
}

#output{
    position: absolute;
    width: 300px;
    top: -75px;
    left: 0;
    color: #fff;
}

#output.alert-success{
    background: rgb(25, 204, 25);
}

#output.alert-danger{
    background: rgb(228, 105, 105);
}


.login-container::before,.login-container::after{
    content: "";
    position: absolute;
    width: 100%;height: 100%;
    top: 3.5px;left: 0;
    background: #fff;
    z-index: -1;
    -webkit-transform: rotateZ(4deg);
    -moz-transform: rotateZ(4deg);
    -ms-transform: rotateZ(4deg);
    border: 1px solid #ccc;

}

.login-container::after{
    top: 5px;
    z-index: -2;
    -webkit-transform: rotateZ(-2deg);
     -moz-transform: rotateZ(-2deg);
      -ms-transform: rotateZ(-2deg);

}

.avatar{
    width: 100px;height: 100px;
    margin: 10px auto 30px;
    border-radius: 100%;
    border: 2px solid #aaa;
    background-size: cover;
}

.form-box input{
    width: 100%;
    padding: 10px;
    text-align: center;
    height:40px;
    border: 1px solid #ccc;;
    background: #fafafa;
    transition:0.2s ease-in-out;

}

.form-box input:focus{
    outline: 0;
    background: #eee;
}

.form-box input[type="text"]{
    border-radius: 5px 5px 0 0;
    text-transform: lowercase;
}

.form-box input[type="password"]{
    border-radius: 0 0 5px 5px;
    border-top: 0;
}

.form-box button.login{
    margin-top:15px;
    padding: 10px 20px;
}

.animated {
  -webkit-animation-duration: 1s;
  animation-duration: 1s;
  -webkit-animation-fill-mode: both;
  animation-fill-mode: both;
}

@-webkit-keyframes fadeInUp {
  0% {
    opacity: 0;
    -webkit-transform: translateY(20px);
    transform: translateY(20px);
  }

  100% {
    opacity: 1;
    -webkit-transform: translateY(0);
    transform: translateY(0);
  }
}

@keyframes fadeInUp {
  0% {
    opacity: 0;
    -webkit-transform: translateY(20px);
    -ms-transform: translateY(20px);
    transform: translateY(20px);
  }

  100% {
    opacity: 1;
    -webkit-transform: translateY(0);
    -ms-transform: translateY(0);
    transform: translateY(0);
  }
}

.fadeInUp {
  -webkit-animation-name: fadeInUp;
  animation-name: fadeInUp;
}
</style>
</body>
</html>
<?php } ?>