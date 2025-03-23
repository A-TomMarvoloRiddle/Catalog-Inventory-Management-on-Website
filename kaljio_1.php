<?php
// ----------------------------------------------------------------------
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
            $user = $_POST['username'];
            $pass = $_POST['password'];
            $sql = "SELECT * FROM login WHERE username='$user' AND password='$pass'";
            $result = $conn->query($sql);
            if ($result && $result->num_rows > 0) {
                $response = json_encode(["success" => true]);
            } else {
                $response = json_encode(["success" => false, "message" => "Check your Credentials"]);
            }
            echo $response;
            break;

        case "signup":
            // Add new login id; expects data: username,password in CSV format (username,password)
            $parts = explode(",", $extra);
            if (count($parts) < 2) {
                echo json_encode(["response" => "Invalid input for new login."]);
                break;
            }
            list($user, $pass) = $parts;
            $sql = "SELECT * FROM login WHERE username='$user'";
            $result = $conn->query($sql);
            if ($result && $result->num_rows == 0) {
                $sql = "INSERT INTO login VALUES ('$user', '$pass')";
                if ($conn->query($sql)) {
                    $response = "New login added successfully.";
                } else {
                    $response = "Failed to add new login.";
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
            if ($result) {
                $table = "Product ID | Product Name | Company | Feature | Price\n";
                $table .= "---------------------------------------------------------\n";
                while ($row = $result->fetch_assoc()) {
                    $table .= "{$row['pno']} | {$row['pname']} | {$row['compnam']} | {$row['featr']} | {$row['price']}\n";
                }
                $response = $table;
            } else {
                $response = "Error retrieving data.";
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
                $response = "New product added successfully.";
            } else {
                $response = "Error in insertion.";
            }
            echo json_encode(["response" => $response]);
            break;

        case "update":
            // Update product: expects extra in format: pno,field,new_value
            $parts = explode(",", $extra);
            if (count($parts) < 3) {
                echo json_encode(["response" => "Invalid input for update."]);
                break;
            }
            list($pno, $field, $new_value) = $parts;
            // Allowable fields: pname, compnam, featr, price
            if (!in_array($field, ["pname", "compnam", "featr", "price"])) {
                echo json_encode(["response" => "Invalid field name."]);
                break;
            }
            // If updating price, do not quote numeric value.
            $set_value = ($field == "price") ? $new_value : "'$new_value'";
            $sql = "UPDATE product SET $field = $set_value WHERE pno = $pno";
            if ($conn->query($sql)) {
                $response = "Record updated successfully.";
            } else {
                $response = "Error updating record.";
            }
            echo json_encode(["response" => $response]);
            break;

        case "delete":
            // Delete product: extra is product id
            $pno = intval($extra);
            $sql = "SELECT * FROM product WHERE pno = $pno";
            $result = $conn->query($sql);
            if ($result && $result->num_rows == 0) {
                $response = "Record does not exist.";
            } else {
                $sql = "DELETE FROM product WHERE pno = $pno";
                if ($conn->query($sql)) {
                    $response = "Record deleted successfully.";
                } else {
                    $response = "Error deleting record.";
                }
            }
            echo json_encode(["response" => $response]);
            break;

        case "searchByName":
            // Search product by name; extra is product name
            $pname = $extra;
            $sql = "SELECT * FROM product WHERE pname = '$pname'";
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
            $compnam = $extra;
            $sql = "SELECT * FROM product WHERE compnam = '$compnam' ORDER BY pname";
            $result = $conn->query($sql);
            $table = "Search Results:\n---------------------------------------------------------\n";
            while ($row = $result->fetch_assoc()) {
                $table .= "{$row['pno']} | {$row['pname']} | {$row['compnam']} | {$row['featr']} | {$row['price']}\n";
            }
            $response = $table;
            echo json_encode(["response" => $response]);
            break;

        case "searchByFeature":
            // Search by feature; extra is feature text
            $featr = $extra;
            $sql = "SELECT * FROM product WHERE featr = '$featr' ORDER BY pno";
            $result = $conn->query($sql);
            $table = "Search Results:\n---------------------------------------------------------\n";
            while ($row = $result->fetch_assoc()) {
                $table .= "{$row['pno']} | {$row['pname']} | {$row['compnam']} | {$row['featr']} | {$row['price']}\n";
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
            while ($row = $result->fetch_assoc()) {
                $table .= "{$row['pno']} | {$row['pname']} | {$row['compnam']} | {$row['featr']} | {$row['price']}\n";
            }
            $response = $table;
            echo json_encode(["response" => $response]);
            break;

        case "total":
            // Display total number of products
            $sql = "SELECT * FROM product";
            $result = $conn->query($sql);
            $total = $result->num_rows;
            $response = "Total Products available: " . $total;
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
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>KalJio Gadgetronics</title>
  <style>
    /* CSS Styles */
    body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background-color: #f2f2f2; }
    #main-container { max-width: 800px; margin: auto; background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    h1 { text-align: center; }
    button { padding: 10px 15px; margin: 5px; cursor: pointer; }
    input { padding: 8px; margin: 5px; width: 90%; }
    pre { background: #eee; padding: 10px; overflow: auto; }
    .form-container { margin: 20px 0; }
  </style>
</head>
<body>
  <div id="main-container">
    <h1>KalJio Gadgetronics</h1>
    <div id="timeDisplay"></div>
    <!-- Main Menu Section -->
    <div id="mainMenu">
      <button onclick="userType('admin')">Use as Admin</button>
      <button onclick="userType('guest')">Use as Guest</button>
      <button onclick="showAbout()">About Us</button>
      <button onclick="logout()">Shutdown / Logout</button>
    </div>
    <!-- Admin Login Section (Signup option removed) -->
    <div id="adminLogin" style="display:none;">
      <h2>Admin Signin</h2>
      <input type="text" id="adminUsername" placeholder="Username"><br>
      <input type="password" id="adminPassword" placeholder="Password"><br>
      <button onclick="adminLogin()">Login</button>
      <div id="loginMsg" style="color:red;"></div>
      <button onclick="backToMenu()">Back</button>
    </div>
    <!-- User (Admin/Guest) Menu Section -->
    <div id="userMenu" style="display:none;">
      <h2 id="userTypeTitle"></h2>
      <div id="menuOptions"></div>
      <button onclick="backToMain()">Back to Main Menu</button>
    </div>
    <!-- Content Area for Product Operations and Forms -->
    <div id="contentArea" style="display:none;"></div>
    <!-- About Us Section -->
    <div id="aboutSection" style="display:none;">
      <pre>
Company Name       : KalJio Gadgetronics Private.ltd
Version            : 2.2.1
Programming Team   : Apaar Mathur
Address            : JIET Jodhpur
Session            : 2024-25
Contact info       : 9462595337
      </pre>
      <button onclick="backToMain()">Back</button>
    </div>
  </div>
  <script>
    // JavaScript for UI and AJAX interactions
    let currentUser = null; // 'admin' or 'guest'

    function updateTime() {
      const now = new Date();
      document.getElementById('timeDisplay').innerText = now.toLocaleString();
    }
    updateTime();
    setInterval(updateTime, 60000);

    function userType(type) {
      currentUser = type;
      if (type === 'admin') {
        document.getElementById('adminLogin').style.display = 'block';
        document.getElementById('mainMenu').style.display = 'none';
      } else if (type === 'guest') {
        showUserMenu();
      }
    }
    function backToMain() {
      document.getElementById('mainMenu').style.display = 'block';
      document.getElementById('adminLogin').style.display = 'none';
      document.getElementById('userMenu').style.display = 'none';
      document.getElementById('contentArea').style.display = 'none';
      document.getElementById('aboutSection').style.display = 'none';
    }
    function backToMenu() {
      document.getElementById('contentArea').style.display = 'none';
      document.getElementById('userMenu').style.display = 'block';
    }
    function showAbout() {
      document.getElementById('aboutSection').style.display = 'block';
      document.getElementById('mainMenu').style.display = 'none';
    }
    function showUserMenu() {
      document.getElementById('userMenu').style.display = 'block';
      document.getElementById('mainMenu').style.display = 'none';
      document.getElementById('adminLogin').style.display = 'none';
      document.getElementById('contentArea').style.display = 'none';
      if (currentUser === 'admin') {
        document.getElementById('userTypeTitle').innerText = 'Admin Menu';
        document.getElementById('menuOptions').innerHTML = adminMenuHTML();
      } else {
        document.getElementById('userTypeTitle').innerText = 'Guest Menu';
        document.getElementById('menuOptions').innerHTML = guestMenuHTML();
      }
    }
    function adminMenuHTML() {
      return `
        <button onclick="ajaxAction('displayAll')">Display All</button>
        <button onclick="showEntryForm()">New Product Entry</button>
        <button onclick="showUpdateForm()">Update Information</button>
        <button onclick="showDeleteForm()">Delete Entry</button>
        <button onclick="showSearchByNameForm()">Filter by Product Name</button>
        <button onclick="showSearchByCompanyForm()">Filter by Company</button>
        <button onclick="showSearchByFeatureForm()">Filter by Feature</button>
        <button onclick="showSearchByPriceForm()">Filter by Price</button>
        <button onclick="ajaxAction('total')">Display Total Items</button>
        <button onclick="showNewLoginForm()">Add New Login ID</button>
        <button onclick="logout()">Logout</button>
      `;
    }
    function guestMenuHTML() {
      return `
        <button onclick="ajaxAction('displayAll')">Display All</button>
        <button onclick="showSearchByNameForm()">Filter by Product Name</button>
        <button onclick="showSearchByCompanyForm()">Filter by Company</button>
        <button onclick="showSearchByFeatureForm()">Filter by Feature</button>
        <button onclick="showSearchByPriceForm()">Filter by Price</button>
        <button onclick="logout()">Logout</button>
      `;
    }
    // Admin login using AJAX
    function adminLogin() {
      const user = document.getElementById('adminUsername').value;
      const pass = document.getElementById('adminPassword').value;
      const formData = new FormData();
      formData.append('action', 'login');
      formData.append('username', user);
      formData.append('password', pass);
      fetch('', { method: 'POST', body: formData })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          currentUser = 'admin';
          showUserMenu();
        } else {
          document.getElementById('loginMsg').innerText = data.message;
        }
      });
    }
    // ---------- New Forms for operations ----------
    function showEntryForm() {
      document.getElementById('userMenu').style.display = 'none';
      document.getElementById('contentArea').style.display = 'block';
      document.getElementById('contentArea').innerHTML = `
        <h3>New Product Entry</h3>
        <div class="form-container">
          <input type="number" id="entryPno" placeholder="Product Number" required><br>
          <input type="text" id="entryPname" placeholder="Product Name" required><br>
          <input type="text" id="entryCompnam" placeholder="Company Name" required><br>
          <input type="text" id="entryFeatr" placeholder="Feature"><br>
          <input type="number" step="0.01" id="entryPrice" placeholder="Price" required><br>
          <button onclick="submitEntryForm()">Submit</button>
          <button onclick="backToMenu()">Back</button>
        </div>
      `;
    }
    function submitEntryForm() {
      const pno = document.getElementById('entryPno').value;
      const pname = document.getElementById('entryPname').value;
      const compnam = document.getElementById('entryCompnam').value;
      const featr = document.getElementById('entryFeatr').value;
      const price = document.getElementById('entryPrice').value;
      const dataCSV = [pno, pname, compnam, featr, price].join(",");
      ajaxAction('entry', dataCSV);
    }
    function showUpdateForm() {
      document.getElementById('userMenu').style.display = 'none';
      document.getElementById('contentArea').style.display = 'block';
      document.getElementById('contentArea').innerHTML = `
        <h3>Update Product Information</h3>
        <div class="form-container">
          <input type="number" id="updatePno" placeholder="Product ID" required><br>
          <select id="updateField" required>
            <option value="">Select Field</option>
            <option value="pname">Product Name</option>
            <option value="compnam">Company Name</option>
            <option value="featr">Feature</option>
            <option value="price">Price</option>
          </select><br>
          <input type="text" id="updateValue" placeholder="New Value" required><br>
          <button onclick="submitUpdateForm()">Submit</button>
          <button onclick="backToMenu()">Back</button>
        </div>
      `;
    }
    function submitUpdateForm() {
      const pno = document.getElementById('updatePno').value;
      const field = document.getElementById('updateField').value;
      const newValue = document.getElementById('updateValue').value;
      const dataCSV = [pno, field, newValue].join(",");
      ajaxAction('update', dataCSV);
    }
    function showDeleteForm() {
      document.getElementById('userMenu').style.display = 'none';
      document.getElementById('contentArea').style.display = 'block';
      document.getElementById('contentArea').innerHTML = `
        <h3>Delete Product Entry</h3>
        <div class="form-container">
          <input type="number" id="deletePno" placeholder="Product ID" required><br>
          <button onclick="submitDeleteForm()">Submit</button>
          <button onclick="backToMenu()">Back</button>
        </div>
      `;
    }
    function submitDeleteForm() {
      const pno = document.getElementById('deletePno').value;
      ajaxAction('delete', pno);
    }
    function showSearchByNameForm() {
      document.getElementById('userMenu').style.display = 'none';
      document.getElementById('contentArea').style.display = 'block';
      document.getElementById('contentArea').innerHTML = `
        <h3>Search by Product Name</h3>
        <div class="form-container">
          <input type="text" id="searchPname" placeholder="Product Name" required><br>
          <button onclick="submitSearchByNameForm()">Submit</button>
          <button onclick="backToMenu()">Back</button>
        </div>
      `;
    }
    function submitSearchByNameForm() {
      const pname = document.getElementById('searchPname').value;
      ajaxAction('searchByName', pname);
    }
    function showSearchByCompanyForm() {
      document.getElementById('userMenu').style.display = 'none';
      document.getElementById('contentArea').style.display = 'block';
      document.getElementById('contentArea').innerHTML = `
        <h3>Search by Company</h3>
        <div class="form-container">
          <input type="text" id="searchCompnam" placeholder="Company Name" required><br>
          <button onclick="submitSearchByCompanyForm()">Submit</button>
          <button onclick="backToMenu()">Back</button>
        </div>
      `;
    }
    function submitSearchByCompanyForm() {
      const compnam = document.getElementById('searchCompnam').value;
      ajaxAction('searchByCompany', compnam);
    }
    function showSearchByFeatureForm() {
      document.getElementById('userMenu').style.display = 'none';
      document.getElementById('contentArea').style.display = 'block';
      document.getElementById('contentArea').innerHTML = `
        <h3>Search by Feature</h3>
        <div class="form-container">
          <input type="text" id="searchFeatr" placeholder="Feature" required><br>
          <button onclick="submitSearchByFeatureForm()">Submit</button>
          <button onclick="backToMenu()">Back</button>
        </div>
      `;
    }
    function submitSearchByFeatureForm() {
      const featr = document.getElementById('searchFeatr').value;
      ajaxAction('searchByFeature', featr);
    }
    function showSearchByPriceForm() {
      document.getElementById('userMenu').style.display = 'none';
      document.getElementById('contentArea').style.display = 'block';
      // Provide a dropdown for criteria
      document.getElementById('contentArea').innerHTML = `
        <h3>Search by Price</h3>
        <div class="form-container">
          <select id="priceCriteria" required>
            <option value="">Select Criteria</option>
            <option value="min">Minimum Price</option>
            <option value="max">Maximum Price</option>
            <option value="range">Price Range</option>
          </select><br>
          <div id="priceInputs"></div>
          <button onclick="submitSearchByPriceForm()">Submit</button>
          <button onclick="backToMenu()">Back</button>
        </div>
      `;
      document.getElementById('priceCriteria').addEventListener('change', function() {
        const criteria = this.value;
        let html = "";
        if(criteria === "min") {
          html = `<input type="number" id="minPrice" placeholder="Minimum Price" required><br>`;
        } else if(criteria === "max") {
          html = `<input type="number" id="maxPrice" placeholder="Maximum Price" required><br>`;
        } else if(criteria === "range") {
          html = `<input type="number" id="rangeMin" placeholder="Minimum Price" required><br>
                  <input type="number" id="rangeMax" placeholder="Maximum Price" required><br>`;
        }
        document.getElementById('priceInputs').innerHTML = html;
      });
    }
    function submitSearchByPriceForm() {
      const criteria = document.getElementById('priceCriteria').value;
      let extraData = "";
      if(criteria === "min") {
        const minPrice = document.getElementById('minPrice').value;
        extraData = "min:" + minPrice;
      } else if(criteria === "max") {
        const maxPrice = document.getElementById('maxPrice').value;
        extraData = "max:" + maxPrice;
      } else if(criteria === "range") {
        const rangeMin = document.getElementById('rangeMin').value;
        const rangeMax = document.getElementById('rangeMax').value;
        extraData = "range:" + rangeMin + "," + rangeMax;
      }
      ajaxAction('searchByPrice', extraData);
    }
    function showNewLoginForm() {
      document.getElementById('userMenu').style.display = 'none';
      document.getElementById('contentArea').style.display = 'block';
      document.getElementById('contentArea').innerHTML = `
        <h3>Add New Login ID</h3>
        <div class="form-container">
          <input type="text" id="newLoginUsername" placeholder="New Username" required><br>
          <input type="password" id="newLoginPassword" placeholder="New Password" required><br>
          <button onclick="submitNewLoginForm()">Submit</button>
          <button onclick="backToMenu()">Back</button>
        </div>
      `;
    }
    function submitNewLoginForm() {
      const user = document.getElementById('newLoginUsername').value;
      const pass = document.getElementById('newLoginPassword').value;
      const dataCSV = [user, pass].join(",");
      ajaxAction('signup', dataCSV);
    }
    // ---------- AJAX function for actions that do not require forms ----------
    // For actions like "displayAll" and "total"
    function ajaxAction(act, extraData = "") {
      const formData = new FormData();
      formData.append('action', act);
      formData.append('data', extraData);
      fetch('', { method: 'POST', body: formData })
      .then(response => response.json())
      .then(data => {
        document.getElementById('contentArea').innerHTML = '<pre>' + data.response + '</pre><button onclick="backToMenu()">Back</button>';
      });
    }
    // Logout with time-based message
    function logout() {
      const now = new Date();
      let msg = "";
      const hour = now.getHours();
      if (hour < 12) msg = "Have a nice day :)";
      else if (hour < 17) msg = "Great Time Ahead";
      else msg = "Good night";
      alert("Thanks for visiting KalJio Gadgetronics. " + msg);
      location.reload();
    }
  </script>
</body>
</html>
