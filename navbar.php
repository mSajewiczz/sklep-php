<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
    $test2 = "";
    $test = "";

    $isAdminStatus = "";

    
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] != "") {
        $test = '<div class="xd">Zalogowany jako <p class="user-logged-in">' . htmlspecialchars($_SESSION['loggedin']) . '</p></div>';
        $test2 = "<button class=\"logout-button\"><a class=\"navbar-option\" href=\"logout.php\" name=\"logout\">Wyloguj się</a></button>";
    } else {
        $test2 = "<li><a class=\"navbar-option\" href=\"loginForm.php\">Zaloguj się</a></li>";
    }


    if(isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1) {
        $isAdminStatus = "<li class=\"admin\"><a href=\"adminPanel.php\" class=\"admin-panel-button\">Panel administratora</a></li>";
    }
?>

<div class="navbar">
        <ul>
            <li><a class="navbar-logo" href="index.php">POL-PAL | Palety dreweniane</a></li>

        <div class="navbar-options-list">
            <li><?php echo $test; ?></li>
            <?php echo $isAdminStatus;?>
            <li><a class="navbar-option" href="index.php#main">Strona Główna</a></li>
            <li><a class="navbar-option" href="index.php#products">Nasze Produkty</a></li>
            <li><a class="navbar-option" href="cart.php">Koszyk</a></li>
            <li><a class="navbar-option" href="index.php#contact">Kontakt</a></li>
            <?php echo $test2; ?>
    

        </div>

    </ul>
</div>

<div class="separator"></div>