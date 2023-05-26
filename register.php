<?php
session_start();
require_once('config.php');

if(isset($_POST['submit']))
{
    if(isset($_POST['first_name'],$_POST['last_name'],$_POST['email'],$_POST['password']) && !empty($_POST['first_name']) && !empty($_POST['last_name']) && !empty($_POST['email']) && !empty($_POST['password']))
    {
        $firstName = trim($_POST['first_name']);
        $lastName = trim($_POST['last_name']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        
        $options = array("cost"=>4);
        $hashPassword = password_hash($password,PASSWORD_BCRYPT,$options);
        $date = date('Y-m-d H:i:s');

        if(filter_var($email, FILTER_VALIDATE_EMAIL))
		{
            $sql = 'select * from members where email = :email';
            $stmt = $pdo->prepare($sql);
            $p = ['email'=>$email];
            $stmt->execute($p);
            
            if($stmt->rowCount() == 0)
            {
                $sql = "insert into members (first_name, last_name, email, `password`, created_at,updated_at) values(:fname,:lname,:email,:pass,:created_at,:updated_at)";
            
                try{
                    $handle = $pdo->prepare($sql);
                    $params = [
                        ':fname'=>$firstName,
                        ':lname'=>$lastName,
                        ':email'=>$email,
                        ':pass'=>$hashPassword,
                        ':created_at'=>$date,
                        ':updated_at'=>$date
                    ];
                    
                    $handle->execute($params);
                    
                    $success = 'Erfolgreich registriert! Auf zum Login!';

                    header("refresh:2;URL=login.php");
                    
                }
                catch(PDOException $e){
                    $errors[] = $e->getMessage();
                }
            }
            else
            {
                $valFirstName = $firstName;
                $valLastName = $lastName;
                $valEmail = '';
                $valPassword = $password;

                $errors[] = 'E-Mailadresse schon vergeben';
            }
        }
        else
        {
            $errors[] = "E-Mail Adresse ist ungültig";
        }
    }
    else
    {
        if(!isset($_POST['first_name']) || empty($_POST['first_name']))
        {
            $errors[] = 'Bitte Vorname angeben';
        }
        else
        {
            $valFirstName = $_POST['first_name'];
        }
        if(!isset($_POST['last_name']) || empty($_POST['last_name']))
        {
            $errors[] = 'Bitte Nachname angeben';
        }
        else
        {
            $valLastName = $_POST['last_name'];
        }

        if(!isset($_POST['email']) || empty($_POST['email']))
        {
            $errors[] = 'Bitte E-Mail Adresse angeben';
        }
        else
        {
            $valEmail = $_POST['email'];
        }

        if(!isset($_POST['password']) || empty($_POST['password']))
        {
            $errors[] = 'Bitte ein Passwort angeben';
        }
        else
        {
            $valPassword = $_POST['password'];
        }
        
    }

}
?>


<!doctype html>
<html>

<head>
<!-- Lokaler Link zu Bootstrap - falls das Internet ausfällt -->
<link rel="stylesheet" href="\bootstrap-5.3.0-alpha3-dist\css\bootstrap.min.css">
</head>

<body class="bg-dark-subtle">

<div class="container h-100">
	<div class="row h-100 mt-5 justify-content-center align-items-center">
		<div class="col-md-5 mt-3 pt-2 pb-5 align-self-center border bg-light">
			<center><h1>Registrierung</h1></center>
			<?php 
				if(isset($errors) && count($errors) > 0)
				{
					foreach($errors as $error_msg)
					{
						echo '<div class="alert alert-danger">'.$error_msg.'</div>';
					}
                }
                
                if(isset($success))
                {
                    
                    echo '<div class="alert alert-success">'.$success.'</div>';
                }
			?>
			<form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>">

                <div class="form-group">
					<label for="email">Vorname:</label>
					<input type="text" name="first_name" placeholder="" class="form-control" value="<?php echo ($valFirstName??'')?>">
				</div>

                <div class="form-group">
					<label for="email">Nachname:</label>
					<input type="text" name="last_name" placeholder="" class="form-control" value="<?php echo ($valLastName??'')?>">
				</div>

                <div class="form-group">
					<label for="email">E-Mail:</label>
					<input type="text" name="email" placeholder="" class="form-control" value="<?php echo ($valEmail??'')?>">
				</div>

				<div class="form-group">
				    <label for="email">Passwort:</label>
					<input type="password" name="password" placeholder="" class="form-control" value="<?php echo ($valPassword??'')?>">
				</div>
            </br>
				<button type="submit" name="submit" class="btn btn-primary btn-sm">Daten prüfen und Registrieren</button>
				<p class="pt-2"> Zurück zur <a href="login.php">Loginmaske</a></p>
				
			</form>
		</div>
	</div>
</div>
</body>
</html>