<?php
      ob_start();
      session_start();
      $pageTitle = 'Login';
      if (isset($_SESSION['user'])) {
        header('Location: index.php');
      }
      include 'init.php';

      // Check If User Coming From HTTP Post ReQuest

      if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            if (isset($_POST['login'])) {

                        $user = $_POST['username'];
                        $pass = $_POST['password'];
                        $hashedPass = sha1($pass);

                        // Check If user in database

                        $stmt = $con->prepare("SELECT
                                                   UserID, Username, Password
                                              FROM
                                                    admin
                                              WHERE
                                                     Username = ?
                                              AND
                                                    Password= ?");
                        $stmt-> execute(array($user,$hashedPass));

                        $get = $stmt->fetch();

                        $count = $stmt->rowCount();

                        // Check If The User Exist In Database

                        if ($count > 0){

                            $_SESSION['user'] = $user; // Register Session Name

                            $_SESSION['uid'] = $get['UserID']; // Register User id

                             header('Location : index.php'); //  Redirect To dashborad page
                             exit();

                        }
        } else {

          $formErrors = array();

          $username 	= $_POST['username'];
          $password 	= $_POST['password'];
          $password2 	= $_POST['password2'];
          $email 		= $_POST['email'];

          if (isset($username)){
            $filterdUser = filter_var($username, FILTER_SANITIZE_STRING);

            if(strlen($filterdUser) < 4){
              $formErrors[] = 'Username Must Be Larger Than 4 Characters';
            }
          }

          if (isset($password) && isset($password2)){

            if (empty($password)) {

              $formErrors[] = 'Sorry Password Cant Be Empty';

            }


            if(sha1($password) !== sha1($password2)){
              $formErrors[] = 'Sorry Password Is Not Match';
            }
          }
          if (isset($email)) {

    				$filterdEmail = filter_var($email, FILTER_SANITIZE_EMAIL);

    				if (filter_var($filterdEmail, FILTER_VALIDATE_EMAIL) != true) {

    					$formErrors[] = 'This Email Is Not Valid';

    				}

    			}


          // Check if there is no Error proceed the user Add

          if(empty($formErrors)) {

          // Check if user exist in database

          $check = checkItem("Username","admin",$username);

          if($check==1){

          $formErrors[] = 'Sorry This User Is Exist';

          }

              else{

              // Insert User Info  In Database

              $stmt = $con->prepare("INSERT INTO
                  admin(Username, Password, Email , FullName, RegStatus, Date  )

                  VALUES(:zuser, :zpass, :zmail, ' ',  0, now()) ");

                $stmt->execute(array(

                'zuser' => $username,
                'zpass' =>	sha1($password),
                'zmail' => $email

                ));


                //Echo Success Message

                $succesMsg = 'Congrats You Are Now Registerd User';

                }

            }

        }

    }

?>

<div class="container login-page">
	<h1 class="text-center">
		<span class="selected" data-class="login">Login</span> |
		<span data-class="signup">Signup</span>
	</h1>
	<!-- Start Login Form -->
    	<form class="login" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
    		<div class="input-container">
    			<input
    				class="form-control"
    				type="text"
    				name="username"
    				autocomplete="off"
    				placeholder="Type your username"
    				required />
    		</div>
    		<div class="input-container">
    			<input
    				class="form-control"
    				type="password"
    				name="password"
    				autocomplete="new-password"
    				placeholder="Type your password"
    				required />
    		</div>
    		<input class="btn btn-primary btn-block" name="login" type="submit" value="Login" />
    	</form>
	<!-- End Login Form -->
  <!-- Start Signup Form -->
    	<form class="signup" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
    		<div class="input-container">
    			<input
            pattern=".{4,}"
    				class="form-control"
    				type="text"
    				name="username"
    				autocomplete="off"
    				placeholder="Type your username"
    				required />
    		</div>
    		<div class="input-container">
    			<input
            minlength="4"
    				class="form-control"
    				type="password"
    				name="password"
    				autocomplete="new-password"
    				placeholder="Type a Complex password"
    				 required />
    		</div>
    		<div class="input-container">
    			<input
            minlength="4"
    				class="form-control"
    				type="password"
    				name="password2"
    				autocomplete="new-password"
    				placeholder="Type a password again"
    				required />
    		</div>
    		<div class="input-container">
    			<input
    				class="form-control"
    				type="email"
    				name="email"
    				placeholder="Type a Valid email" />
    		</div>
    		<input class="btn btn-success btn-block" name="signup" type="submit" value="Signup" />
    	</form>
	<!-- End Signup Form -->
  <div class="the-errors text-center">
    <?php

        if(!empty($formErrors)) {

          foreach ($formErrors as $error ) {

            echo '<div class="msg error">' . $error . '</div>';
          }

        }

        if (isset($succesMsg)) {

          echo '<div class="msg success">' . $succesMsg . '</div>';

        }


     ?>
  </div>
</div>
<?php  include $tpl .'footer.php';?>
