# 📦 **Basic Catalog & Inventory Management System**
### A web-based system to efficiently manage product catalogs and inventory levels using **HTML, CSS, JavaScript, PHP, and MySQL**.

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

![image](https://github.com/user-attachments/assets/75fafb98-6d0b-4427-aef0-babe2c121d52) 
![image](https://github.com/user-attachments/assets/a34233ad-3133-498f-8693-9e8cc86619b1)
![image](https://github.com/user-attachments/assets/43f3989d-44de-4871-823e-a5d565157147)
![image](https://github.com/user-attachments/assets/e721e6aa-4ff9-423c-b280-e0f0694add95)
![image](https://github.com/user-attachments/assets/c246f077-7c2f-40e3-8f51-59d4ebfa21dd)
![image](https://github.com/user-attachments/assets/1cb64bf5-ac0c-4624-bdc6-6f1a0333fa23)
![image](https://github.com/user-attachments/assets/75f1adf1-06a6-4309-80b8-e4ed419541ff)
![image](https://github.com/user-attachments/assets/cbd09ab4-12aa-4f52-bd79-da7ce44843e1)
![image](https://github.com/user-attachments/assets/21cf7909-bc81-4778-b84c-3b0084653935)
<!--- ![image](https://github.com/user-attachments/assets/6d26e3c1-14c8-4caa-a0eb-27747c5a96f9) --->
<p align="center">
  <img src="https://github.com/user-attachments/assets/6d26e3c1-14c8-4caa-a0eb-27747c5a96f9" alt="Sublime's custom image"/>
</p>

---
