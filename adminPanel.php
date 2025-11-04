

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
        <?php include("navbar.php")?>

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
                            echo "<div class = 'user-buttons'>";
                            echo "<button>Edytuj</button>";
                            echo "<button>Usuń</button>";
                            echo "<button>Ustaw jako administrator  </button>";
                            echo "</div>";
                            echo "</div>";
                        }   
                    }
                    ?>

                </div>
                <div class="admin-container-box"><h2>Zarządzaj produktami</h2>
                    
                <?php 
                        if (!empty($products)) {
                        foreach ($products as $product) {
                            $base64 = base64_encode($product["Img"]);

                            echo "<div class='admin-panel-product'>";
                            echo "<img class='product-img' src='data:image/jpg;base64," . $base64 . "' alt='' />";
                            echo "<div class = 'product-buttons'>";
                            echo "<p class='product-admin-title'>";
                            echo $product["Title"];
                            echo "</p>";
                            echo "<button>Edytuj</button>";
                            echo "<button>Usuń</button>";
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
                        $img = $_POST['productImg'];
                        $rating = $_POST['productRating'];

                
                        $stmt = $conn->prepare("INSERT INTO products (Title, Description, Price, Img, Rating) VALUES (?, ?, ?, ?, ?)");
                        if (!$stmt) {
                        die('Błąd przygotowania zapytania: ' . $conn->error);
                        }

                        $stmt->bind_param("ssis", $title, $description, $price, $img, $rating);
                        $result = $stmt->execute();

                        if ($result) {
                        echo "Produkt został dodany pomyślnie.";
                        // Możesz dodać przekierowanie np. header("Location: products_list.php");
                        } else {
                        echo "Błąd podczas dodawania produktu: " . $stmt->error;
                        }

                        $stmt->close();
                        $conn->close();
                        }
        ?>

        <div class="text-editor">
            <h2>Dodaj nowy produkt</h2>
            <form method = "POST" action="" class="adminAddProductForm">

                <label for="">
                    <p>Tytuł produktu</p>
                    <input type="text" name="productTitle">
                </label>

                <label for="">
                    <p>Opis produktu</p>
                    <textarea id="editor" name="productDescription"></textarea>
                </label>

                <label for="">
                    <p>Ocena produktu</p>
                    <input type="text" name="productRating">
                </label>

                <label for="">
                    <p>Cena produktu</p>
                    <input type="text" name="productPrice">
                </label>
                
                <label for="">
                    <p>Zdjęcie produktu</p>
                    <input type="text" name="productImg">
                </label>


                <button type="submit" name="addProductViaForm" class="addProductViaForm">Dodaj produkt</button>
            </form>
            
        </div>

        

</body>
</html>