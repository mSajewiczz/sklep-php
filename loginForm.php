<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require("connection.php");

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['user_password'];
    

    $query = "SELECT Username, User_password, isAdmin FROM users WHERE Username = '$username'";
    
    $result = mysqli_query($conn, $query);

    if ($result) {
            $row = mysqli_fetch_assoc($result);

            
            $hashedPassword = $row['User_password'];

            echo $hashedPassword;
            echo "<br>";
            echo $password;

            //password_verify($password, $hashedPassword)

        if ( mysqli_num_rows($result) == 1) {  
            $_SESSION['username'] = $row['Username'];
            $_SESSION['loggedin'] = "$username";
            $_SESSION['isAdmin'] = $row['isAdmin'];
           header("Location:index.php");

           exit();

        } else {
            $message = "Nieprawidłowa nazwa użytkownika lub hasło.";
        }
        
    } else {
        $message = "Błąd zapytania do bazy danych: " . mysqli_error($conn);
    }

    mysqli_free_result($result);
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POL-PAL | Palety dreweniane - zaloguj się</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time();?>" >
</head>

<body>
    <?php include("navbar.php")?>
    <div class="bgc">

        <div class="login-form">
            <h1>Logownaie</h1>

            <form method="post" action="" class="login-formular">
                <label for="">
                    <p>Nazwa użytkownika</p>
                    <input type="text" name="username" placeholder="Podaj nazwę użytkownika" required>
                </label>

                <label for="">
                    <p>Hasło</p>
                    <input type="password" name="user_password" placeholder = "Podaj hasło" required>
                </label>

                <button type="submit" name="submit-btn">Zaloguj się</button>

                <p><?php echo $message;?></p>
            </form>
            <p class="form-info">Nie masz konta? <a href="signupForm.php">Zarejestruj się</a></p>
        </div>

    </div>

    </body>

</html>