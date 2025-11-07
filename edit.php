<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require("connection.php");

if (!isset($_SESSION['productToEditId'])) {
    die("No product selected to edit.");
}

$productId = intval($_SESSION['productToEditId']);

$stmt = $conn->prepare("SELECT * FROM products WHERE ID = ?");
$stmt->bind_param("i", $productId);
$stmt->execute();
$result = $stmt->get_result();

if ($product = $result->fetch_assoc()) {
    $title = $product['Title']; 
    $price = $product['Price']; 
    $rating = $product['Rating']; 
    $description = $product['Description']; 
    $img = $product['Img']; 

    $_SESSION["productTitle123"] = $title;
} else {
    echo "No product found with ID $productId.";
}

$stmt->close();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editProductViaForm'])) { 

    $title = $_POST['productTitle'];
    $description = $_POST['productDescription'];
    $price = floatval($_POST['productPrice']);
    $rating = floatval($_POST['productRating']);
    $imgData = null;

    if (isset($_FILES['productImg']) && $_FILES['productImg']['error'] === UPLOAD_ERR_OK) {
        $imgData = file_get_contents($_FILES['productImg']['tmp_name']);

        $stmt = $conn->prepare("UPDATE products SET Title = ?, Description = ?, Price = ?, Rating = ?, Img = ? WHERE ID = ?");
        $stmt->bind_param("ssdsbi", $title, $description, $price, $rating, $null, $productId);
        $stmt->send_long_data(4, $imgData);
    } else {
        $stmt = $conn->prepare("UPDATE products SET Title = ?, Description = ?, Price = ?, Rating = ? WHERE ID = ?");
        $stmt->bind_param("ssdsi", $title, $description, $price, $rating, $productId);
    }

    if ($stmt->execute()) {
        echo "Product updated successfully!";
    } else {
        echo "Error updating product: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POL-PAl - edytuj produkt</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time();?>" >
</head>
<body>

    <?php include("navbar.php"); ?>


        <div class="text-editor">
      <h2>Edytuj produkt</h2>
            <form method = "POST" enctype="multipart/form-data" action="" class="adminAddProductForm">

                <label for="">
                    <p>Tytuł produktu</p>
                    <input type="text" name="productTitle" value="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>">
                </label>

                <label for="">
                    <p>Opis produktu</p>
                    <textarea    name="productDescription"><?php echo htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?></textarea>
                </label>

                <label for="">
                    <p>Ocena produktu</p>
                    <input type="number" name="productRating" value="<?php echo htmlspecialchars($rating, ENT_QUOTES, 'UTF-8'); ?>">
                </label>

                <label for="">
                    <p>Cena produktu</p>
                    <input type="number" step="0.01" name="productPrice" value="<?php echo htmlspecialchars($price, ENT_QUOTES, 'UTF-8'); ?>">
                </label>
                
                <label for="">
                    <p>Zdjęcie produktu</p>
                    <input type="file" name="productImg">
                </label>

                <button type="submit" name="editProductViaForm" class="addProductViaForm">Zaktualizuj produkt</button>
            </form>
            
        </div>
</body>
</html>