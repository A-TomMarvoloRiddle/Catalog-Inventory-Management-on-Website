<?php
// PHP Section: Handles AJAX actions and connects to MySQL.
// Only runs when an AJAX request with a POST parameter "action" is sent.
if (isset($_POST['action'])) {
    // Connect to MySQL (adjust host, user, password as needed)
    $conn = new mysqli("localhost", "root", "Ap@DBMS_987", "kaljio");
    if ($conn->connect_error) {
        die(json_encode(["response" => "Database connection failed: " . $conn->connect_error]));
    }

    $action = $_POST['action'];
    $extra = isset($_POST['data']) ? $_POST['data'] : "";
    $response = "";

    switch ($action) {

        case "login":
            // Admin login: expects POST parameters "username" and "password"
            $user = trim($_POST['username']);
            $pass = trim($_POST['password']);
            if($user === "" || $pass === ""){
                echo json_encode(["success" => false, "message" => "Username and password required!"]);
                exit();
            }
            $sql = "SELECT * FROM login WHERE username='$user' AND password='$pass'";
            $result = $conn->query($sql);
            if ($result && $result->num_rows > 0) {
                $response = json_encode(["success" => true]);
            } else {
                $response = json_encode(["success" => false, "message" => "Check your Credentials!"]);
            }
            echo $response;
            break;

        case "signup":
            // Add new login id; expects data: username,password in CSV format (username,password)
            $parts = explode(",", $extra);
            if (count($parts) < 2 || trim($parts[0]) === "" || trim($parts[1]) === "") {
                echo json_encode(["response" => "Please enter both username and password!"]);
                break;
            }
            list($user, $pass) = $parts;
            $user = trim($user);
            $pass = trim($pass);
            $sql = "SELECT * FROM login WHERE username='$user'";
            $result = $conn->query($sql);
            if ($result && $result->num_rows == 0) {
                $sql = "INSERT INTO login VALUES ('$user', '$pass')";
                if ($conn->query($sql)) {
                    $response = "New login added successfully.";
                } else {
                    $response = "Failed to add new login: " . $conn->error;
                }
            } else {
                $response = "Username already exists.";
            }
            echo json_encode(["response" => $response]);
            break;

        case "displayAll":
            // Display all products
            $sql = "SELECT * FROM product ORDER BY pno";
            $result = $conn->query($sql);
            if ($result && $result->num_rows > 0) {
                $table = "Product ID | Product Name | Company | Feature | Price\n";
                $table .= "---------------------------------------------------------\n";
                while ($row = $result->fetch_assoc()) {
                    $table .= "{$row['pno']} | {$row['pname']} | {$row['compnam']} | {$row['featr']} | {$row['price']}\n";
                }
                $response = $table;
            } else {
                $response = "No products found.";
            }
            echo json_encode(["response" => $response]);
            break;

        case "entry":
            // New product entry: extra data should be in CSV format: pno,pname,compnam,featr,price
            $parts = explode(",", $extra);
            if (count($parts) < 5) {
                echo json_encode(["response" => "Invalid input for product entry."]);
                break;
            }
            list($pno, $pname, $compnam, $featr, $price) = $parts;
            $sql = "INSERT INTO product VALUES ($pno, '$pname', '$compnam', '$featr', $price)";
            if ($conn->query($sql)) {
                $response = "Product Added Successfully";
            } else {
                $response = "Error in insertion: " . $conn->error;
            }
            echo json_encode(["response" => $response]);
            break;

        case "searchByName":
            // Search product by name; extra is product name
            $pname = trim($extra);
            $sql = "SELECT * FROM product WHERE LOWER(pname) LIKE '%" . strtolower($pname) . "%'";
            $result = $conn->query($sql);
            $table = "Search Results:\n---------------------------------------------------------\n";
            while ($row = $result->fetch_assoc()) {
                $table .= "{$row['pno']} | {$row['pname']} | {$row['compnam']} | {$row['featr']} | {$row['price']}\n";
            }
            $response = $table;
            echo json_encode(["response" => $response]);
            break;

        case "searchByCompany":
            // Search by company; extra is company name
            $compnam = trim($extra);
            $sql = "SELECT * FROM product WHERE LOWER(compnam) LIKE '%" . strtolower($compnam) . "%' ORDER BY pno";
            $result = $conn->query($sql);
            $table = "Search Results:\n---------------------------------------------------------\n";
            while ($row = $result->fetch_assoc()) {
                $table .= "{$row['pno']} | {$row['pname']} | {$row['compnam']} | {$row['featr']} | {$row['price']}\n";
            }   
            $response = $table;
            echo json_encode(["response" => $response]);
            break;

        case "searchByFeature":
            // Search by feature: use keyword matching (LIKE) in the 'featr' attribute
            $featr = trim($extra);
            $sql = "SELECT * FROM product WHERE LOWER(featr) LIKE '%" . strtolower($featr) . "%' ORDER BY pno";
            $result = $conn->query($sql);
            $table = "Search Results:\n---------------------------------------------------------\n";
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $table .= "{$row['pno']} | {$row['pname']} | {$row['compnam']} | {$row['featr']} | {$row['price']}\n";
                }
            } else {
                $table .= "No matching records found.";
            }
            $response = $table;
            echo json_encode(["response" => $response]);
            break;

        case "searchByPrice":
            /* For price search, extra is expected as:
            - For min: e.g., "min:1000"
            - For max: e.g., "max:2000"
            - For range: e.g., "range:1000,2000"
            */
            if (strpos($extra, "min:") === 0) {
                $price = intval(substr($extra, 4));
                $sql = "SELECT * FROM product WHERE price >= $price";
            } elseif (strpos($extra, "max:") === 0) {
                $price = intval(substr($extra, 4));
                $sql = "SELECT * FROM product WHERE price <= $price";
            } elseif (strpos($extra, "range:") === 0) {
                $range = substr($extra, 6);
                list($min, $max) = explode(",", $range);
                $sql = "SELECT * FROM product WHERE price BETWEEN " . intval($min) . " AND " . intval($max) . " ORDER BY price";
            } else {
                echo json_encode(["response" => "Invalid price criteria."]);
                break;
            }
            $result = $conn->query($sql);
            $table = "Search Results:\n---------------------------------------------------------\n";
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $table .= "{$row['pno']} | {$row['pname']} | {$row['compnam']} | {$row['featr']} | {$row['price']}\n";
                }
            } else {
                $table .= "No matching records found.";
            }
            $response = $table;
            echo json_encode(["response" => $response]);
            break;

        case "total":
            // Display total number of products using COUNT(*)
            $sql = "SELECT COUNT(*) as total FROM product";
            $result = $conn->query($sql);
            if ($result && $row = $result->fetch_assoc()) {
                $total = $row['total'];
            } else {
                $total = 0;
            }
            $response = "Total Products available: " . $total;
            echo json_encode(["response" => $response]);
            break;

        case "getTotalCount":
            // Return the maximum product number, so auto-fill as max + 1.
            $sql = "SELECT MAX(pno) as max FROM product";
            $result = $conn->query($sql);
            if ($result && $row = $result->fetch_assoc()) {
                $max = $row['max'];
                echo json_encode(["count" => $max + 1]);
            } else {
                echo json_encode(["count" => 1]);
            }
            break;

        case "deleteLogin":
            // Delete login: extra is username
            $usernameToDelete = trim($extra);
            if($usernameToDelete === ""){
                echo json_encode(["response" => "No username entered."]);
                exit();
            }
            // Check if username exists
            $sql = "SELECT * FROM login WHERE username='$usernameToDelete'";
            $result = $conn->query($sql);
            if ($result && $result->num_rows == 0) {
                echo json_encode(["response" => "Username not found."]);
                exit();
            }
            $sql = "DELETE FROM login WHERE username='$usernameToDelete'";
            $conn->query($sql);
            if($conn->affected_rows > 0) {
                $response = "Login ID deleted successfully.";
            } else {
                $response = "Error deleting login: " . $conn->error;
            }
            echo json_encode(["response" => $response]);
            break;

        case "deleteByCriteria":
            // Delete product entry by criteria.
            // If only one value provided, assume it's pno.
            $parts = explode(",", $extra);
            if(count($parts) == 1) {
                $pno = intval($parts[0]);
                $sql = "DELETE FROM product WHERE pno = $pno";
            } else {
                // Otherwise, use criteria: pname,compnam,price,featr (optional)
                $pname = $parts[0];
                $sql = "DELETE FROM product WHERE pname='$pname'";
                if(isset($parts[1]) && $parts[1] !== "") {
                    $compnam = $parts[1];
                    $sql .= " AND compnam='$compnam'";
                }
                if(isset($parts[2]) && $parts[2] !== "") {
                    $price = $parts[2];
                    $sql .= " AND price=$price";
                }
                if(isset($parts[3]) && $parts[3] !== "") {
                    $featr = $parts[3];
                    $sql .= " AND featr='$featr'";
                }
            }
            if ($conn->query($sql)) {
                $response = "Record deleted successfully.";
            } else {
                $response = "Error deleting record: " . $conn->error;
            }
            echo json_encode(["response" => $response]);
            break;

        case "updateByCriteria":
            // Update product by criteria.
            // Extra is expected in format: criteriaCSV|field|newValue
            $parts = explode("|", $extra);
            if(count($parts) < 3) {
                echo json_encode(["response" => "Invalid input for update by criteria."]);
                break;
            }
            $criteriaCSV = $parts[0];
            $field = $parts[1];
            $newValue = $parts[2];
            $criteriaParts = explode(",", $criteriaCSV);
            if(count($criteriaParts) == 1) {
                $pno = intval($criteriaParts[0]);
                $sql = "UPDATE product SET $field = " . (($field=="price") ? $newValue : "'$newValue'") . " WHERE pno = $pno";
            } else {
                $pname = $criteriaParts[0];
                $sql = "UPDATE product SET $field = " . (($field=="price") ? $newValue : "'$newValue'") . " WHERE pname = '$pname'";
                if(isset($criteriaParts[1]) && $criteriaParts[1] !== "") {
                    $compnam = $criteriaParts[1];
                    $sql .= " AND compnam = '$compnam'";
                }
                if(isset($criteriaParts[2]) && $criteriaParts[2] !== "") {
                    $price = $criteriaParts[2];
                    $sql .= " AND price = $price";
                }
                if(isset($criteriaParts[3]) && $criteriaParts[3] !== "") {
                    $featr = $criteriaParts[3];
                    $sql .= " AND featr = '$featr'";
                }
            }
            if ($conn->query($sql)) {
                $response = "Record updated successfully.";
            } else {
                $response = "Error updating record: " . $conn->error;
            }
            echo json_encode(["response" => $response]);
            break;

        default:
            echo json_encode(["response" => "Invalid action."]);
            break;
    }
    $conn->close();
    exit();
}
?>
<!DOCTYPE html>
<head>
    <title>KalJio Gadgetronics</title>
    <link rel="stylesheet" href="style_kaljio.css">
</head>
<body>
    <div id="main-container">
    <h1>KalJio Gadgetronics</h1>
    <div id="timeDisplay"></div>
    <!-- Main Menu Section -->
    <div id="mainMenu">
        <button onclick="userType('admin')">Admin Login</button>
        <button onclick="userType('guest')">Use as Guest</button>
        <button onclick="showAbout()">About Us</button>
        <button onclick="logout()">Shutdown / Exit</button>
    </div>
    <!-- Admin Login Section (Signup option removed) -->
    <div id="adminLogin" style="display:none;">
        <h2>Admin Signin</h2>
        <input type="text" id="adminUsername" placeholder="Username" required><br>
        <input type="password" id="adminPassword" placeholder="Password" required><br>
        <button onclick="adminLogin()">Login</button>
        <div id="loginMsg" style="color:red;"></div>
        <button onclick="backToMain()">Back</button>
    </div>
    <!-- User (Admin/Guest) Menu Section -->
    <div id="userMenu" style="display:none;">
        <h2 id="userTypeTitle"></h2>
        <div id="menuOptions"></div>
    </div>
    <!-- Content Area for Product Operations and Forms -->
    <div id="contentArea" style="display:none;"></div>
    <!-- About Us Section -->
    <div id="aboutSection" style="display:none; text-align:left;">
        <pre>
Company Name       : KalJio Gadgetronics Private.ltd
Version            : 2.2.7
Programming Team   : Apaar Mathur
Address            : JIET Jodhpur
Session            : 2024-25
Contact info       : 9462595337
        </pre>
        <button onclick="backToMain()">Back</button>
    </div>
</div>
<script src="Script_kaljio.js"></script>
</body>
</html>
