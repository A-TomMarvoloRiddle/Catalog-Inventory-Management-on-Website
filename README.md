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

![image](https://github.com/user-attachments/assets/e45867c6-ab95-43df-91c4-727e7fa4d276)
![image](https://github.com/user-attachments/assets/6825216a-fe45-4eab-b322-8559b274bb87)
![image](https://github.com/user-attachments/assets/e7ea6751-f785-481c-9311-f4756478408b)
![image](https://github.com/user-attachments/assets/dbbf6868-40ba-48e1-8406-7bfd0fbc4db8)
![image](https://github.com/user-attachments/assets/93de0566-b8ad-4c18-b9ef-b9fe93a879fc)

(**As the project progresses, I'll update & add more screenshots.**)

---
