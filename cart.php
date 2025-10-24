<?php 
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    require("connection.php");

    $productOwner1 = "";

    $sum = 0;
    $username = $_SESSION["loggedin"];

    $query = "SELECT * FROM cart WHERE ProductOwner = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    $cartArr = [];

    if ($result) {
        $cartArr = mysqli_fetch_all($result, MYSQLI_ASSOC);

    if (empty($cartArr)) {
        $message = "Error";
    }
    } else {
        $message = "Błąd zapytania do bazy danych: " . mysqli_error($conn);
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POL-PAL | Palety dreweniane - koszyk</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time();?>" >
</head>
<body>
    <?php include("navbar.php")?>

   <div class="cart-box">
    <h1 class="cart-box-title">Koszyk</h1>

    <table class="cart-table">
        <thead>
            <tr>
                <th>Zdjęcie</th>
                <th>Tytuł</th>
                <th>Cena</th>
                <th>Ilość</th>
                <th>Wartość produktów</th>
                <th>Usuń produkt</th>
            </tr>
        </thead>
        <tbody>
            <?php
            function deleteFromCart($productId, $productOwner) {
                require("connection.php");
                $query = "DELETE FROM cart WHERE ProductID = ? AND ProductOwner = ?";

                $stmt = mysqli_prepare($conn, $query);
                if (!$stmt) {
                    die("Błąd przygotowania zapytania: " . mysqli_error($conn));
                }

                mysqli_stmt_bind_param($stmt, "is", $productId, $productOwner);


                $success = mysqli_stmt_execute($stmt);

                if ($success) {
                    header("Location: " . $_SERVER['PHP_SELF']);
                    return true;
                } else {
                    error_log("Błąd przy usuwaniu z koszyka: " . mysqli_error($conn));
                    return false;
                }
            }

            function ereaseCart($productOwner) {
                require("connection.php");
            
                $query = "DELETE FROM cart WHERE ProductOwner = ?";
                $stmt = mysqli_prepare($conn, $query);
                if (!$stmt) {
                    die("Błąd przygotowania zapytania: " . mysqli_error($conn));
                }


                mysqli_stmt_bind_param($stmt, "s", $productOwner);

                $success = mysqli_stmt_execute($stmt);

                if ($success) {
                    header("Location: " . $_SERVER['PHP_SELF']);
                    return true;
                } else {
                    error_log("Błąd przy usuwaniu z koszyka: " . mysqli_error($conn));
                    return false;
                }
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cartDeleteButton'])) {
                $productId = (int) $_POST['remove_id'];
                $productOwner = (string) $_POST['remove_owner'];
                deleteFromCart($productId, $productOwner);
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ereaseCart'])) {
                $productOwner = (string) $_POST['remove_owner'];
                ereaseCart($productOwner);
                echo "ok";
            }

            $sum = 0;
            $amountOfProducts = 0;

            for ($i = 0; $i < count($cartArr); $i++) {
                foreach($cartArr[$i] as $column => $value) {
                    $productAmount = $cartArr[$i]['Amount'];
                    $productOwner = $cartArr[$i]['ProductOwner'];

                    $productOwner1 = $productOwner;

                    if($column == "ProductID") {
                        $query = "SELECT * FROM products WHERE ID = ?";
                        $stmt = mysqli_prepare($conn, $query);
                        mysqli_stmt_bind_param($stmt, "i", $value);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        while ($row = $result->fetch_assoc()) {
                            $base64 = base64_encode($row["Img"]);
                            $price = $row['Price'];
                            $total = $price * $productAmount;
                            $sum += $total;

                            $amountOfProducts += $productAmount;

                            echo "<tr>";
                            echo "<td><img class='cart-product-img' src='data:image/jpg;base64," . $base64 . "' alt='' /></td>";
                            echo "<td>" . htmlspecialchars($row['Title']) . "</td>";
                            echo "<td>" . number_format($price, 2, ",", ".") . " zł</td>";
                            echo "<td>" . htmlspecialchars($productAmount) . "</td>";
                            echo "<td>" . number_format($total, 2, ",", ".") . " zł</td>";
                            echo "<form method = 'POST'> <td>  <input type='hidden' name='remove_id' value='" . htmlspecialchars($row['ID']) . "'>  <input type='hidden' name='remove_owner' value='" . $productOwner . "'> <button class='cart-delete-product' type='submit' name='cartDeleteButton'> Usuń </button> </td></form>";
                            echo "</tr>";
                        }
                    }
                }
            }
            echo "<tr>";
            echo "<td>";
            echo "PODSUMOWANIE";
            echo "</td>";
            echo "<td>";
            echo "";
            echo "</td>";
            echo "<td>";
            echo "";
            echo "</td>";
            echo "<td>";
            echo $amountOfProducts;
            echo "</td>";
            echo "<td>";
            echo number_format($sum, 2, ",", "."), " zł";
            echo "</td>";
            echo "<td>";
            echo "<form method='POST'> <input type='hidden' name='remove_owner' value='" . $productOwner1 . "'> <button class='cart-delete-product' type='submit' name='ereaseCart'> Wyczyść koszyk </button> </form>";
            echo "</td>";
            echo "</tr>";

            ?>
        </tbody>
    </table>

    <div class="cart-sum">
        <button class="cart-buy-btn">Złóż zamówienie</button>
    </div>
</div>
</body>
</html>