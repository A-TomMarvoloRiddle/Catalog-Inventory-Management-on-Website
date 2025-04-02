// JavaScript for UI and AJAX interactions
let currentUser = null; // 'admin' or 'guest'
function updateTime() {
    const now = new Date();
    const options = { year: 'numeric', month: 'numeric', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    document.getElementById('timeDisplay').innerText = now.toLocaleString([], options);
}
updateTime();
setInterval(updateTime, 600);
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
// For Guest Menu, the Back button will act as logout (showing the alert)
function guestBack() {
    const now = new Date();
    let msg = "";
    const hour = now.getHours();
    if (hour < 12) msg = "Have a nice day :)";
    else if (hour < 17) msg = "Great Time Ahead";
    else msg = "Good night";
    alert("Thanks for visiting KalJio Gadgetronics. " + msg);
    location.reload();
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
        <button onclick="displayAllProducts()">Display All</button>
        <button onclick="showEntryForm()">New Product Entry</button>
        <button onclick="showUpdateForm()">Update Information</button>
        <button onclick="showDeleteForm()">Delete Entry</button>
        <button onclick="showSearchByNameForm()">Filter by Product Name</button>
        <button onclick="showSearchByCompanyForm()">Filter by Company</button>
        <button onclick="showSearchByFeatureForm()">Filter by Feature</button>
        <button onclick="showSearchByPriceForm()">Filter by Price</button>
        <button onclick="getTotalItems()">Total No. of Items</button>
        <button onclick="showNewLoginForm()">Add New Login ID</button>
        <button onclick="showDeleteLoginForm()">Delete Login ID</button>
        <button onclick="logout()">Logout</button>
    `;
}
function guestMenuHTML() {
    return `
        <button onclick="displayAllProducts()">Display All</button>
        <button onclick="showSearchByNameForm()">Filter by Product Name</button>
        <button onclick="showSearchByCompanyForm()">Filter by Company</button>
        <button onclick="showSearchByFeatureForm()">Filter by Feature</button>
        <button onclick="showSearchByPriceForm()">Filter by Price</button>
        <button onclick="guestBack()">Back to Main Menu</button>
    `;
}
function displayAllProducts() {
    document.getElementById('contentArea').style.display = 'visible';
    ajaxAction('displayAll');
}
function getTotalItems() {
    ajaxAction('total','');
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
      // Get the current total count to autofill product number
    fetch('', { method: 'POST', body: new URLSearchParams({ action: 'getTotalCount' }) })
    .then(response => response.json())
    .then(data => {
        const nextPno = data.count;
        document.getElementById('contentArea').innerHTML = `
        <h3>New Product Entry</h3>
        <div class="form-container">
            <input type="number" id="entryPno" placeholder="Product Number" value="${nextPno}" readonly><br>
            <input type="text" id="entryPname" placeholder="Product Name" required><br>
            <input type="text" id="entryCompnam" placeholder="Company Name" required><br>
            <input type="text" id="entryFeatr" placeholder="Feature"><br>
            <input type="number" step="0.01" id="entryPrice" placeholder="Price" required><br>
            <button onclick="submitEntryForm()">Submit</button>
            <button onclick="backToMenu()">Back</button>
        </div>
        `;
    });
}
function submitEntryForm() {
    const pno = document.getElementById('entryPno').value;
    const pname = document.getElementById('entryPname').value;
    const compnam = document.getElementById('entryCompnam').value;
    const featr = document.getElementById('entryFeatr').value;
    const price = document.getElementById('entryPrice').value;
    const dataCSV = [pno, pname, compnam, featr, price].join(",");
    const formData = new FormData();
    formData.append('action', 'entry');
    formData.append('data', dataCSV);
    fetch('', { method: 'POST', body: formData })
    .then(response => response.json())
    .then(data => {
        if(data.response === "Product Added Successfully") {
            alert("Product Added Successfully");
        } else {
            alert("Error: " + data.response);
        }
        showUserMenu();
    })
    .catch(error => {
        alert("Error: " + error);
        showUserMenu();
    });
}
function showUpdateForm() {
    document.getElementById('userMenu').style.display = 'none';
    document.getElementById('contentArea').style.display = 'block';
    document.getElementById('contentArea').innerHTML = `
        <h3>Update Product Information</h3>
        <div class="form-container">
        <label><input type="radio" name="updateMethod" value="pno" checked>Update by Product Number</label><br>
        <label><input type="radio" name="updateMethod" value="pname">Update by Product Name</label><br>
        <div id="updateFields"></div>
        <input type="text" id="updateFieldName" placeholder="Field to update (pname, compnam, featr, price)" required><br>
        <input type="text" id="updateNewValue" placeholder="New Value" required><br>
        <button onclick="submitUpdateForm()">Submit</button>
        <button onclick="backToMenu()">Back</button>
        </div>
    `;
    document.getElementById('updateFields').innerHTML = `<input type="number" id="updatePno" placeholder="Product Number" required><br>`;
    const radios = document.getElementsByName('updateMethod');
    for (let radio of radios) {
        radio.addEventListener('change', function() {
            if(this.value === "pno") {
                document.getElementById('updateFields').innerHTML = `<input type="number" id="updatePno" placeholder="Product Number" required><br>`;
            } else {
                document.getElementById('updateFields').innerHTML = `
                    <input type="text" id="updatePname" placeholder="Product Name" required><br>
                    <input type="text" id="updateCompnam" placeholder="Company Name (optional)"><br>
                    <input type="number" step="0.01" id="updatePrice" placeholder="Price (optional)"><br>
                    <input type="text" id="updateFeatr" placeholder="Feature (optional)"><br>
                `;
            }
        });
    }
}
function submitUpdateForm() {
    const method = document.querySelector('input[name="updateMethod"]:checked').value;
    let criteriaCSV = "";
    if(method === "pno") {
        const pno = document.getElementById('updatePno').value;
        criteriaCSV = pno;
    } else {
        const pname = document.getElementById('updatePname').value;
        const compnam = document.getElementById('updateCompnam').value || "";
        const price = document.getElementById('updatePrice').value || "";
        const featr = document.getElementById('updateFeatr').value || "";
        criteriaCSV = [pname, compnam, price, featr].join(",");
    }
    const field = document.getElementById('updateFieldName').value;
    const newValue = document.getElementById('updateNewValue').value;
    const dataCSV = criteriaCSV + "|" + field + "|" + newValue;
    ajaxAction('updateByCriteria', dataCSV);
}
function showDeleteForm() {
    document.getElementById('userMenu').style.display = 'none';
    document.getElementById('contentArea').style.display = 'block';
    document.getElementById('contentArea').innerHTML = `
        <h3>Delete Product Entry</h3>
        <div class="form-container">
        <label><input type="radio" name="deleteMethod" value="pno" checked> Delete by Product Number</label><br>
        <label><input type="radio" name="deleteMethod" value="pname"> Delete by Product Name</label><br>
        <div id="deleteFields"></div>
        <button onclick="submitDeleteForm()">Submit</button>
        <button onclick="backToMenu()">Back</button>
        </div>
    `;
    document.getElementById('deleteFields').innerHTML = `<input type="number" id="deletePno" placeholder="Product Number" required><br>`;
    const radios = document.getElementsByName('deleteMethod');
    for (let radio of radios) {
        radio.addEventListener('change', function() {
            if(this.value === "pno") {
                document.getElementById('deleteFields').innerHTML = `<input type="number" id="deletePno" placeholder="Product Number" required><br>`;
            } else {
                document.getElementById('deleteFields').innerHTML = `
                    <input type="text" id="deletePname" placeholder="Product Name" required><br>
                    <input type="text" id="deleteCompnam" placeholder="Company Name (optional)"><br>
                    <input type="number" step="0.01" id="deletePrice" placeholder="Price (optional)"><br>
                    <input type="text" id="deleteFeatr" placeholder="Feature (optional)"><br>
                `;
            }
        });
    }
}
function submitDeleteForm() {
    const method = document.querySelector('input[name="deleteMethod"]:checked').value;
    let dataCSV = "";
    if(method === "pno") {
        const pno = document.getElementById('deletePno').value;
        dataCSV = pno;
    } else {
        const pname = document.getElementById('deletePname').value;
        const compnam = document.getElementById('deleteCompnam').value || "";
        const price = document.getElementById('deletePrice').value || "";
        const featr = document.getElementById('deleteFeatr').value || "";
        dataCSV = [pname, compnam, price, featr].join(",");
    }
    ajaxAction('deleteByCriteria', dataCSV);
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
        <input type="text" id="searchFeatr" placeholder="Enter keyword" required><br>
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
        Username: <input type="text" id="newLoginUsername" placeholder="New Username" required><br>
        Password: <input type="password" id="newLoginPassword" placeholder="New Password" required><br>
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
function showDeleteLoginForm() {
    document.getElementById('userMenu').style.display = 'none';
    document.getElementById('contentArea').style.display = 'block';
    document.getElementById('contentArea').innerHTML = `
        <h3>Delete Login ID</h3>
        <div class="form-container">
        <input type="text" id="deleteLoginUsername" placeholder="Username" required><br>
        <button onclick="submitDeleteLoginForm()">Submit</button>
        <button onclick="backToMenu()">Back</button>
        </div>
    `;
}
function submitDeleteLoginForm() {
    const username = document.getElementById('deleteLoginUsername').value;
    ajaxAction('deleteLogin', username);
}
// ---------- AJAX function for generic actions ----------
function ajaxAction(act, extraData = "") {
    const formData = new FormData();
    formData.append('action', act);
    formData.append('data', extraData);
    fetch('', { method: 'POST', body: formData })
    .then(response => response.json())
    .then(data => {
        console.log(data); // To log the response data
        document.getElementById('contentArea').innerHTML = '<pre>' + data.response + '</pre><button onclick="backToMenu()">Back</button>';
    })
    .catch(error => {
        console.error(error); // To log any errors
        alert("Error: " + error);
        backToMenu();
    });
}
// For Admin logout (if triggered from Admin Menu, simply reload)
// For Main Menu Shutdown/Logout, show About Us for 10 seconds then redirect.
function logout() {
    const now = new Date();
    let msg = "";
    const hour = now.getHours();
    if (hour < 12) msg = "Have a nice day :)";
    else if (hour < 17) msg = "Great Time Ahead";
    else msg = "Good night";
    alert("Thanks for visiting KalJio Gadgetronics. " + msg);
    if(document.getElementById('mainMenu').style.display !== 'none') {
          // Show About Us for 10 seconds then redirect
        document.getElementById('aboutSection').style.display = 'block';
        document.getElementById('mainMenu').style.display = 'none';
        setTimeout(function(){
            window.location.href = "https://www.google.com";
        }, 10000);
    } else {
        location.reload();
}
}
// Back to menu for forms
function backToMenu() {
    showUserMenu();
}
