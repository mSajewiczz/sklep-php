<?php 
    require("connection.php");
    $message = "";


        function systemLog($userName) {
        require("connection.php");
        $query = "INSERT INTO `log`(`message`) VALUES (?)";
    
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $messageLog = "Tried to sign up. IP: $ipAdress. User: $userName";

        // $stmt = mysqli_prepare($conn, $query);

        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $messageLog);

        if (!$stmt){
            die('Błąd przygotowania zapytania: ' . $conn->error);
        }

        if ($stmt->execute()) {
        echo "";
        } else {
        echo "Błąd: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    }   

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        $username = $_POST['username'];
        $password = $_POST['user_password'];

        systemLog($username);

        // $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $isAdmin = false;

        $query = "INSERT INTO users (Username, User_password, IsAdmin) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssi", $username, $hashedPassword, $isAdmin);
            $result = mysqli_stmt_execute($stmt);   

            if ($result) {
                $message = "Zarejestrowano pomyślnie. Zaloguj się";
                header("Location:loginForm.php");
            } else {
                $message = "Błąd: " . mysqli_error($conn);
            }

            mysqli_stmt_close($stmt);
        } else {
            $message = "Błąd bazy danych.";
        }
    }

?>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POL-PAL | Palety dreweniane - zaloguj się</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php include("navbar.php")?>
    <div class="bgc">
        <div class="signup-form">
            <h1>Rejestracja</h1>

            <form method="post" action="" class="login-formular">
                <label for="">
                    <p>Nazwa użytkownika</p>
                    <input type="text" name="username" placeholder="Podaj nazwę użytkownika" required>
                </label>

                <label for="">
                    <p>Hasło</p>
                    <input type="password" name="user_password" placeholder = "Podaj hasło" required>
                </label>

                <label for="">
                    <p>Powtórz hasło</p>
                    <input type="password" name="user_password_repeat" placeholder = "Podaj hasło ponownie" required>
                </label>

                <button type="submit" name="submit-btn">Zarejestruj się</button>

                <p><?php echo $message;?></p>
            </form>
            <p class="form-info">Masz już konto? <a href="loginForm.php">Zaloguj się</a></p>
        </div>

    </div>

</body>

</html>