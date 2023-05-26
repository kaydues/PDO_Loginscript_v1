<?php
session_start();
require_once('config.php');

if(isset($_POST['submit']))
{
	if(isset($_POST['email'],$_POST['password']) && !empty($_POST['email']) && !empty($_POST['password']))
	{
		$email = trim($_POST['email']);
		$password = trim($_POST['password']);

		if(filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			$sql = "select * from members where email = :email ";
			$handle = $pdo->prepare($sql);
			$params = ['email'=>$email];
			$handle->execute($params);
			if($handle->rowCount() > 0)
			{
				$getRow = $handle->fetch(PDO::FETCH_ASSOC);
				if(password_verify($password, $getRow['password']))
				{
					unset($getRow['password']);
					$_SESSION = $getRow;
					header('location:dashboard.php');
					exit();
				}
				else
				{
					$errors[] = "Wrong Email or Password";
				}
			}
			else
			{
				$errors[] = "Wrong Email or Password";
			}
			
		}
		else
		{
			$errors[] = "Email address is not valid";	
		}

	}
	else
	{
		$errors[] = "Email and Password are required";	
	}

}
?>

<!-- Beginn von html -->
<!doctype html>
<html>

<head>
<!-- Lokaler Link zu Bootstrap - falls das Internet ausfällt -->
<link rel="stylesheet" href="\bootstrap-5.3.0-alpha3-dist\css\bootstrap.min.css">
</head>

<!-- Beginn von Body und Auswahl der Hintergrundfarbe über Bootstrap -->
<body class="bg-dark-subtle">


<div class="container">
	<div class="row h-100 mt-5 justify-content-center align-items-center">
		<div class="col-md-5 mt-5 pt-2 pb-5 align-self-center bg-light">
			<center><h1>Login</h1></center>
			<?php 
				if(isset($errors) && count($errors) > 0)
				{
					foreach($errors as $error_msg)
					{
						echo '<div class="alert alert-danger">'.$error_msg.'</div>';
					}
				}
			?>
			<form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>">

				<div class="form-group">
					<label for="email">E-Mail:</label>
					<input type="text" name="email" placeholder="" class="form-control">
				</div>

				<div class="form-group">
					<label for="email">Passwort:</label>
					<input type="password" name="password" placeholder="" class="form-control">
				</div>
			</br>
				<button type="submit" name="submit" class="btn btn-primary btn-sm">Absenden</button>
				
				<a href="register.php" class="btn btn-primary btn-sm">Registrierung aufrufen</a>
			</form>
		</div>
	</div>
</div>

</body>
</html>