<?php 
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    require("connection.php");

    $query = "SELECT * FROM products";
    $result = mysqli_query($conn, $query);

    $products = [];

    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $products[] = $row;
            }
        } else {
            $message = "Error";
        }
    } else {
        $message = "Błąd zapytania do bazy danych: " . mysqli_error($conn);
    }
?>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POL-PAL | Palety dreweniane</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time();?>" >
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>

    <?php include("navbar.php")?>
    <?php include("header.php")?>

    <section id="products" class="products">
        <div class="products-header">
            <h2>Produkty</h2>
            <p>Sprawdź naszą bogatą ofertę produktów!</p>
        </div>

        <div class="products-list">
<?php



function getRating($product) {
    switch (htmlspecialchars($product['Rating'])) {
    case 1:
        return "<div class='stars-box'><i class = 'fa fa-star checked'> </i><i class = 'fa fa-star-o'> </i><i class = 'fa fa-star-o'> </i><i class = 'fa fa-star-o'></i> <i class = 'fa fa-star-o'></i></div>";
        break;
    case htmlspecialchars($product['Rating']) > 1 && htmlspecialchars($product['Rating']) < 2:
        return "<div class='stars-box'><i class = 'fa fa-star checked'> </i><i class = 'fa fa-star-half-o'> </i><i class = 'fa fa-star-o'> </i><i class = 'fa fa-star-o'></i> <i class = 'fa fa-star-o'></i></div>";
        break;
    case 2:
        return "<div class='stars-box'><i class = 'fa fa-star checked'> </i><i class = 'fa fa-star checked'> </i><i class = 'fa fa-star-o'> </i><i class = 'fa fa-star-o'></i> <i class = 'fa fa-star-o'></i></div>";
        break;
    case htmlspecialchars($product['Rating']) > 2 && htmlspecialchars($product['Rating']) < 3:
        return "<div class='stars-box'><i class = 'fa fa-star checked'> </i><i class = 'fa fa-star checked'> </i><i class = 'fa fa-star-half-o'> </i><i class = 'fa fa-star-o'></i> <i class = 'fa fa-star-o'></i></div>";
        break;
    case 3:
        return "<div class='stars-box'><i class = 'fa fa-star checked'> </i><i class = 'fa fa-star checked'> </i><i class = 'fa fa-star checked'> </i><i class = 'fa fa-star-o'></i> <i class = 'fa fa-star-o'></i></div>";
        break;
    case htmlspecialchars($product['Rating']) > 3 && htmlspecialchars($product['Rating']) < 4:
        return "<div class='stars-box'><i class = 'fa fa-star checked'> </i><i class = 'fa fa-star checked'> </i><i class = 'fa fa-star checked'> </i><i class = 'fa fa-star-half-o'></i> <i class = 'fa fa-star-o'></i></div>";
        break;
    case 4:
        return "<div class='stars-box'><i class = 'fa fa-star checked'> </i><i class = 'fa fa-star checked'> </i><i class = 'fa fa-star checked'> </i><i class = 'fa fa-star'></i> <i class = 'fa fa-star-o'></i></div>"; 
        break;
    case htmlspecialchars($product['Rating']) > 4 && htmlspecialchars($product['Rating']) < 5:
        return "<div class='stars-box'><i class = 'fa fa-star checked'> </i><i class = 'fa fa-star checked'> </i><i class = 'fa fa-star checked'> </i><i class = 'fa fa-star'></i> <i class = 'fa fa-star-half-o'></i></div>"; 
        break;
    case 5:
         return "<div class='stars-box'><i class = 'fa fa-star checked'> </i><i class = 'fa fa-star checked'> </i><i class = 'fa fa-star checked'> </i><i class = 'fa fa-star'></i> <i class = 'fa fa-star'></i></div>"; 
        break;
    }
}

    function addToCart($productId, $cartAmount) {
    require("connection.php");

    $userName = $_SESSION["loggedin"];

    $checkQuery = "SELECT * FROM cart WHERE ProductID = ? AND ProductOwner = ?";
    $checkStmt = mysqli_prepare($conn, $checkQuery);
    mysqli_stmt_bind_param($checkStmt, "is", $productId, $userName);
    mysqli_stmt_execute($checkStmt);
    $result = mysqli_stmt_get_result($checkStmt);

    if (mysqli_num_rows($result) > 0) {
        $updateQuery = "UPDATE cart SET Amount = Amount + $cartAmount WHERE ProductID = ? AND ProductOwner = ?";
        $updateStmt = mysqli_prepare($conn, $updateQuery);
        mysqli_stmt_bind_param($updateStmt, "is", $productId, $userName);
        mysqli_stmt_execute($updateStmt);
    } else {
        $insertQuery = "INSERT INTO cart (ProductID, ProductOwner, Amount) VALUES (?, ?, ?)";
        $insertStmt = mysqli_prepare($conn, $insertQuery);
        mysqli_stmt_bind_param($insertStmt, "isi", $productId, $userName, $cartAmount);
        mysqli_stmt_execute($insertStmt);
        // echo "<p class='cart-message'>Product added to cart.</p>";
    }

    mysqli_close($conn);
}
    

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cartButton'])) {
    $productId = (int) $_POST['id'];
    $cartAmount = (int) $_POST['cartInput'];
    addToCart($productId, $cartAmount);
}

if (!empty($products)) {
    foreach ($products as $product) {
        $base64 = base64_encode($product["Img"]);
        

        echo "<form class='product' method='POST'>";
        echo "<img class='product-img' src='data:image/jpg;base64," . $base64 . "' alt='' />";
        echo "<h3 class='product-title'>" . htmlspecialchars($product['Title']) . "</h3>";

        $id = $product['ID'];

        echo "<input type='hidden' name='id' value=\"$id\" />";
        echo "<div class='product-rating'>";
        echo getRating($product);
        echo "</div>";
    
        
        echo "<p class='product-price'>Cena: " . number_format( htmlspecialchars($product['Price']), 2,",",".") . " zł</p>";
        echo "<p class='product-desc'>" . htmlspecialchars($product['Description']) . "</p>";
        echo "<p class = 'product-cuantity-p'>Ilość</p>";
        echo "<input type='number' name='cartInput' value=1 class='product-cuantity' />";
        echo "<button type='submit' name='cartButton' class='add-to-cart'>Dodaj do koszyka</button>";
        echo "</form>";
    }


} else {
    echo "<p>No products to display.</p>";
}

?>
        </div>
    </section>

   
    <section id="contact" class="contact">
        <div class="contact-header">
            <h2>Kontakt</h2>
            <p>Skontaktuj się z nami!</p>
        </div>

    </section>


</body>

</html>