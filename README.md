### 📦 **Basic Catalog & Inventory Management System**
A web-based system to efficiently manage product catalogs and inventory levels using **HTML, CSS, JavaScript, PHP, and MySQL**.

---

## 🚀 **Features**
- **User Authentication:** Secure login for admins and guests.
- **Product Management:** Add, update, delete, and view products.
- **Search Functionality:** Filter products by name, company, features, or price.
- **User Roles:** Different access levels for admins and guests.
- **Responsive Design:** Works on desktops and mobile devices.

---

## 🛠 **Technologies Used**
### **Frontend**
- **HTML** – Page structure
- **CSS** – Styling and layout
- **JavaScript** – Dynamic UI & AJAX calls

### **Backend**
- **PHP** – Server-side scripting
- **MySQL** – Database management

---

## 📊 **Database Schema**
```sql
CREATE DATABASE kaljio;
USE kaljio;

CREATE TABLE product (
    pno INT(3) PRIMARY KEY,
    pname VARCHAR(50) NOT NULL,
    compnam VARCHAR(50) NOT NULL,
    featr VARCHAR(50),
    price DECIMAL(8,2)
);

CREATE TABLE login (
    username VARCHAR(50) PRIMARY KEY,
    password VARCHAR(50) NOT NULL
);
```

---

## 📝 **Usage**
- **Admin:** Can log in and manage inventory.
- **Guest:** Can view products and search by various filters.

### **Admin Functionalities**
✔️ Add / Update / Delete products  
✔️ View total inventory count  
✔️ Manage user accounts  

### **Guest Functionalities**
✔️ Browse the product catalog  
✔️ Search products by different parameters  

---

## 📌 **Future Enhancements**
🔹 **E-commerce Integration** – Real-time updates with online stores  
🔹 **Mobile App** – On-the-go inventory tracking  
🔹 **Advanced Analytics** – Insights into sales trends  
🔹 **User Feedback System** – Collect reviews for products  

---

## 🎯 **Project Screenshots**
(**As the project progresses, I'll update & add more screenshots.**)

![image](https://github.com/user-attachments/assets/8964ad03-3ab7-4ce8-a21a-31a5f653315e)
![image](https://github.com/user-attachments/assets/f897a916-7254-49f4-8aee-f78a802338e5)
![image](https://github.com/user-attachments/assets/a57cdb46-140f-4bf8-ad1e-a33c45ec71ae)
![image](https://github.com/user-attachments/assets/01aabb3d-b812-4365-8261-75ba4fbdd628)
![image](https://github.com/user-attachments/assets/8151f07f-8425-4764-be3a-ce07ca7f3de4)
![image](https://github.com/user-attachments/assets/f92e4ec3-5ffc-4b9d-b30b-39859d796ede)
![image](https://github.com/user-attachments/assets/dba49aa9-420d-47b9-a6be-6d4715353f45)

---
