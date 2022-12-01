<?php
ob_start();
session_start();

require_once 'core/security.class.php';


$error = false;

$security = new Security();
$isLoggedIn = $security->isLoggedIn();
if($isLoggedIn)
{
    header("LOCATION: index.php");
}

if (!empty($_POST['email']) && !empty($_POST['password'])) {
    if ($security->checkLogin($_POST['email'], $_POST['password'])) {
        header("LOCATION: index.php");
    } else {
        $error = "Forkert email eller kodeord.";
    }
}
?>

<!DOCTYPE HTML>
<!--
BORG Employee Management System
-->
<html>
    <head>
        <title>BORG EMS</title>
        
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />

        <link rel="stylesheet" href="assets/css/index.css" />
    </head>
    <body>
        
        <!-- Wrapper -->
        <div id="wrapper">                       
            
            <!-- Main Content -->
            <main id="main">
                <section id="login">
                    <h1>Login</h1>
                    
                    <?php
                    if($error != "") {
                    ?>
                        <p class="message error"><?=$error;?></p>
                    <?php
                    }
                    ?>                
                    
                    <form method="post" action="login.php">
                        <input type="text" name="email" value="" placeholder="Email">
                        <input type="password" name="password" value="" placeholder="Kodeord">
                        
                        <input type="submit" name="commit" value="Login">
                    </form>                    
                </section>
            </main>
        </div>
                
        <!-- Scripts -->
        <script src="assets/js/init.js"></script>
    </body>
</html>