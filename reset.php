<?php
session_start();
include "config.php";
?>

<!DOCTYPE HTML>
<html>

<head>
	<title> Poker | Reset Password </title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<!--Stylesheets-->
	<link rel="stylesheet" type="text/css" href="../style/dark.css" />
	<link rel="alternate stylesheet" type="text/css" href="style/light.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/font-awesome.min.css">
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/material-design-iconic-font/2.2.0/css/material-design-iconic-font.min.css"> 
		
	<!--JavaScript-->
	<script defer src="https://use.fontawesome.com/releases/v5.0.4/js/all.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js" type="text/javascript"></script>
	<script src="js/script.js"></script>
</head>


<body>
    <?php
    
     $nameErr = $newpassErr = $newpassconfErr = "";
    
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        if(empty($_POST['username'])){
            $nameErr = "Enter a username";
            if(empty($_POST['newpass'])){
                $newpassErr = "Enter a new Password";
                if(empty($_POST['newpassconf'])){
                    $newpassconfErr = "Confirm Password";
                    }
                }
        }
        
        elseif($_POST['newpassconf'] != $_POST['newpass']){
            $newpassconfErr = "Password must be the same";
            }
        
        else{
            
            $newpass = mysqli_real_escape_string($link, password_hash($_POST['newpass'], PASSWORD_BCRYPT));
            $username = mysqli_real_escape_string($link, $_POST['username']);
                
            $query = "UPDATE userdata SET Password = '$newpass' WHERE username = '$username';";
            
            if(mysqli_query($link, $query)){
                echo "Password Updated <br>";
                echo "<a href='login'>Login</a>";
                echo '<meta http-equiv="refresh" content="1;URL=login"/>';
                die;
            }
            else
            {
                echo "Nix gud";
                echo "<a href='reset'>Try again</a>";
                echo '<meta http-equiv="refresh" content="1;URL=reset"/>';
                die;
            }
            }
    }
        
    ?>
    <div class="login-container">
        <div class="login-wrapper">
            <form method="post" class="login-form" action="<?php echo $_SERVER["PHP_SELF"];?>">
				<span class="login-form-title">Reset Password</span>
                   <div class="login-input-wrap">
                        <span class="login-label">Username<span class="error"><?php echo $nameErr ?></span></span>
                        <input type="text" name="username" class="login-input" value="<?php if(isset($_POST['username'])) echo $_POST['username']; ?>">
						<div class="focus-input" data-symbol="&#xf206;"></div>
                    </div>
                    <div class="login-input-wrap">
                        <span class="login-label">New Password<span class="error"><?php echo $newpassErr ?></span></span>
                        <input type="password" name="newpass" class="login-input" value="<?php if(isset($_POST['newpass'])) echo $_POST['newpass']; ?>">
						<div class="focus-input" data-symbol="&#xf190;"></div>
                    </div>
                    <div class="login-input-wrap">
                        <span class="login-label">Confirm Password<span class="error"><?php echo $newpassconfErr ?></span></span>
                        <input type="password" name="newpassconf" class="login-input" value="<?php if(isset($_POST['newpassconf'])) echo $_POST['newpassconf']; ?>">
						<div class="focus-input" data-symbol="&#xf190;"></div>
				    </div>
				<div class="login-submit">
					<div class="login-submit-cont">
						<div class="login-submit-background"></div>
						<input class="login-submit-btn" type="submit" name="reset" value="RESET" class="btn">
					</div>
				</div>
                <span class="login-sign-up1" style="margin-top:8vh;"><a href="javascript:delay('login')">Back</a></span>
            </form>
        </div>
    </div>
</body>