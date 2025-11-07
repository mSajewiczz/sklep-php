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


<?php 
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    require("connection.php");

    $query = "SELECT * FROM users";
    $result = mysqli_query($conn, $query);

    $users = [];

    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $users[] = $row;
            }
        } else {
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
    <title>POL-PAL | Palety dreweniane - panel administratora</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time();?>" >
    <script src="https://cloud.tinymce.com/stable/tinymce.min.js?apiKey=5g5faf78gvk6yfq9bd3bbfjo858kjx1q8o0nbiwtygo2e4er"></script>
    <script>
        tinymce.init({
            selector: '#editor',    
            height: 300,
            menubar: true,
            plugins: 'advlist autolink lists link image charmap print preview anchor ' +
            'searchreplace visualblocks code fullscreen ' +
            'insertdatetime media table paste code help wordcount',
            toolbar: 'undo redo | formatselect | ' +
            'bold italic underline | alignleft aligncenter alignright alignjustify | ' +
            'bullist numlist outdent indent | removeformat | help',
            setup: function (editor) {
            editor.on('change', function () {
            tinymce.triggerSave();
            });
            }
        });
  </script>

</head>
<body>
        <?php 
        include("navbar.php");

        function changeUsersStatusTrue($userId) {
            require("connection.php");

            $query = "UPDATE users SET isAdmin = 1 WHERE ID = ?";

            $stmt = mysqli_prepare($conn, $query);

            if (!$stmt) {
            die("Błąd przygotowania zapytania: " . mysqli_error($conn));
            }

            mysqli_stmt_bind_param($stmt, "i", $userId);


            $success = mysqli_stmt_execute($stmt);

            if ($success) {
            echo "Zmieniono status uzytkownika";
            return true;
            } else {
            error_log("Błąd przy usuwaniu z koszyka: " . mysqli_error($conn));
            return false;
            }
        }

        function changeUsersStatusFalse($userId) {
            require("connection.php");

            $query = "UPDATE users SET isAdmin = 0 WHERE ID = ?";

            $stmt = mysqli_prepare($conn, $query);

            if (!$stmt) {
            die("Błąd przygotowania zapytania: " . mysqli_error($conn));
            }

            mysqli_stmt_bind_param($stmt, "i", $userId);


            $success = mysqli_stmt_execute($stmt);

            if ($success) {
            echo "Zmieniono status uzytkownika";
            return true;
            } else {
            error_log("Błąd " . mysqli_error($conn));
            return false;
            }
        }


        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['changeUserStatusTrue'])) {
            $productId = (int) $_POST['remove_id'];
            changeUsersStatusTrue($productId);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['changeUserStatusFalse'])) {
            $productId = (int) $_POST['remove_id'];
            changeUsersStatusFalse($productId);
        }
        
        
        
        ?>

        <div class="main-admin-container">
            <div class="admin-container">
                <div class="admin-container-box">
                    <h2>Witaj <span class="admin"><?php echo $_SESSION['loggedin']; ?></span>!</h2>
                    <p>Jesteś w panelu administratora</p>
                </div>
                <div class="admin-container-box">
                    <h2>Zarządzaj użytkownikami</h2>
                    <?php 
                        if (!empty($users)) {
                        foreach ($users as $user) {
                            echo "<div class='admin-panel-user'>";
                            echo "<p class='user-username'>";
                            echo $user["Username"];
                            echo "</p>";
                            echo "<p class='adminState'>" . ($user['IsAdmin'] ? "Administrator" : "Użytkownik") . "</p>";
                            echo "<div class = 'user-buttons'>";
                            echo "<form method = 'POST'> <input type='hidden' name='remove_id' value='" . htmlspecialchars($user["ID"]) . "'> <button class='changeUserStatusTrue' type='submit' name='changeUserStatusTrue'> Ustaw jako administrator </button></form>";
                            echo "<form method = 'POST'> <input type='hidden' name='remove_id' value='" . htmlspecialchars($user["ID"]) . "'> <button class='changeUserStatusFalse' type='submit' name='changeUserStatusFalse'> Ustaw jako użytkownik </button></form>";
                            echo "</div>";
                            echo "</div>";
                        }   
                    }
                    ?>

                </div>
                <div class="admin-container-box"><h2>Zarządzaj produktami</h2>


                <?php


                    function deleteProductAsAdmin($productId) {
                            require("connection.php");

                            $query = "DELETE FROM products WHERE ID = ?";

                            $stmt = mysqli_prepare($conn, $query);

                            if (!$stmt) {
                            die("Błąd przygotowania zapytania: " . mysqli_error($conn));
                            }

                            mysqli_stmt_bind_param($stmt, "i", $productId);


                            $success = mysqli_stmt_execute($stmt);

                            if ($success) {
                            echo "Usunięto produkt";
                            return true;
                            } else {
                            error_log("Błąd przy usuwaniu z koszyka: " . mysqli_error($conn));
                            return false;
                            }
                    }


                    function prepareToEdit($productId) { 
                        $_SESSION['productToEditId'] = $productId;
                        // $_SESSION['productToEditImg'] = 

                        header("Location:edit.php");
                    }


                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteProduct'])) {
                    $productId = (int) $_POST['remove_id'];
                    deleteProductAsAdmin($productId);
                    }

                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editProduct'])) {
                    $productId = (int) $_POST['remove_id'];
                    prepareToEdit($productId);
                    }

                ?>
                    
                <?php 
                        if (!empty($products)) {
                        foreach ($products as $product) {
                            $base64 = base64_encode($product["Img"]);

                            echo "<div method = 'POST' class='admin-panel-product'>";
                            echo "<img class='product-img' src='data:image/jpg;base64," . $base64 . "' alt='' />";
                            echo "<div class = 'product-buttons'>";
                            echo "<p class='product-admin-title'>";
                            echo $product["Title"];
                            echo "</p>";
                            echo "<form method = 'POST'> <input type='hidden' name='remove_id' value='" . htmlspecialchars($product["ID"]) . "'> <button class='editProduct' type='submit' name='editProduct'> Edytuj </button></form>";
                            echo "<form method = 'POST'> <input type='hidden' name='remove_id' value='" . htmlspecialchars($product["ID"]) . "'> <button class='deleteProduct' type='submit' name='deleteProduct'> Usuń </button></form>";
                            // echo "<button name='deleteProduct'>Usuń</button>";
                            echo "</div>";

                            echo "</div>";      
                        }   
                    }
                    ?> 
                </div>
            </div>
        </div>

        <?php 
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addProductViaForm'])) {
                        require("connection.php");


                        $title = $_POST['productTitle'];
                        $description = $_POST['productDescription'];
                        $price = $_POST['productPrice'];
                        $imgData = null;
                        $rating = $_POST['productRating'];

                        if (isset($_FILES['productImg']) && $_FILES['productImg']['error'] === UPLOAD_ERR_OK) {
                        $imgData = file_get_contents($_FILES['productImg']['tmp_name']);
                        }
                        
                        $stmt = $conn->prepare("INSERT INTO products (Title, `Description`, Price, Img, Rating) VALUES (?, ?, ?, ?, ?)");


                        if (!$stmt) {
                        die('Błąd przygotowania zapytania: ' . $conn->error);
                        }

                        $null = null;
                        

                        $stmt->bind_param("ssdbs", $title, $description, $price, $null, $rating);

                        $stmt->send_long_data(3, $imgData);

                        $result = $stmt->execute();

                        if ($result) {
                        echo "Produkt został dodany pomyślnie.";
                        
                        } else {
                        echo "Błąd podczas dodawania produktu: " . $stmt->error;
                        }

                        $stmt->close();
                        $conn->close();
                        }
        ?>

        <div class="text-editor">
            <h2>Dodaj nowy produkt</h2>
            <form method = "POST" enctype="multipart/form-data" action="" class="adminAddProductForm">

                <label for="">
                    <p>Tytuł produktu</p>
                    <input type="text" name="productTitle">
                </label>

                <label for="">
                    <p>Opis produktu</p>
                    <textarea name="productDescription"></textarea>
                </label>

                <label for="">
                    <p>Ocena produktu</p>
                    <input type="number" name="productRating">
                </label>

                <label for="">
                    <p>Cena produktu</p>
                    <input type="number" step="0.01" name="productPrice">
                </label>
                
                <label for="">
                    <p>Zdjęcie produktu</p>
                    <input type="file" name="productImg">
                </label>


                <button type="submit" name="addProductViaForm" class="addProductViaForm">Dodaj produkt</button>
            </form>
            
        </div>

        

</body>
</html>