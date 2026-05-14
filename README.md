# Vaxora — E-Vaccination Management System

Pakistan ka smart digital vaccination platform. Parents, hospitals, aur admins ek hi jagah se sab kuch manage kar sakte hain.

---

## Features

- Parent portal — children add karo, appointments book karo, certificates download karo
- Hospital portal — appointments approve/reject karo, vaccinations complete karo
- Admin panel — hospitals, vaccines, users, aur inquiries manage karo
- Digital PDF vaccination certificates
- Pakistan EPI vaccine schedule ticker
- SQLite database — koi alag database server nahi chahiye

---

## XAMPP pe kaise chalayein (Windows)

### Step 1 — Download
GitHub se zip download karo aur extract karo.

### Step 2 — XAMPP mein daalo
`VAXORA` folder ko yahan paste karo:
```
C:\xampp\htdocs\VAXORA\
```

### Step 3 — XAMPP start karo
XAMPP Control Panel kholein aur sirf **Apache** start karein.
> MySQL ki zaroorat nahi — app SQLite use karta hai.

### Step 4 — Database banao (sirf pehli baar)
Browser mein yeh URL kholo:
```
http://localhost/VAXORA/config/init_db.php
```
"Database initialized successfully" message ayega.

### Step 5 — Website kholein
```
http://localhost/VAXORA/
```

---

## Default Login Credentials

| Role     | Email                   | Password   |
|----------|-------------------------|------------|
| Admin    | admin@vaxora.com        | admin123   |
| Hospital | civil@vaxora.com        | hospital123|
| Parent   | Register karein (free)  | —          |

---

## Folder Structure

```
VAXORA/
├── admin/          — Admin panel pages
├── hospital/       — Hospital portal pages
├── auth/           — Login, register, logout
├── assets/         — CSS, JS, images
├── config/         — Database config + init script
├── data/           — SQLite database (auto-created)
├── includes/       — Shared headers, footers, auth check
└── index.php       — Home page
```

---

## Requirements

- XAMPP (Apache + PHP 7.4 or higher)
- PHP Extensions: PDO, pdo_sqlite (XAMPP mein default on hote hain)
- No MySQL, no Composer, no Node.js

---

## Notes

- Folder ka naam change kar sakte hain — auto-detect hota hai
- Kisi bhi PC pe same steps se chalega
- Replit pe bhi chal sakta hai (port 5000 pe)
