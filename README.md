# musicOnline.com – Vinyl Marketplace Prototype

This project was built as part of my coursework at college and follows a brief for a fictional startup called **musicOnline.com**. The main idea behind the website is to let users buy and sell second-hand vinyl records—like albums, EPs, and singles.

The goal was to create a **working prototype** with full user functionality, admin tools, and a clean, user-friendly design that works on both desktop and mobile devices.

---

## Project Requirements (from the brief)

The project had several core requirements that I worked towards implementing:

- New user registration  
- User login system  
- Admin login area  
- Search feature (filter by artist, genre, album, EP, single)  
- Search results with artist + release info  
- Detailed vinyl listing pages (with release date, condition, price, etc.)  
- Users can add, edit, and delete vinyls for sale (with images)  
- Admins can view/edit/delete user content and accounts  
- Passwords are hashed and basic security (like escaping input) is in place  
- Responsive design that works across devices  

> The site is a prototype, so no real sales or payment systems were required.

---

## Technologies Used

- PHP  
- MySQL  
- HTML5  
- CSS3  
- JavaScript  
- Responsive design principles  
- XAMPP for local development

---

## Extra Features I Added

While testing how SQL functionality was working, I added an optional **“Buy” button** to the vinyl listing page. This doesn’t process payments or lead anywhere—it’s just a placeholder to simulate the buying process.

Even though it wasn’t part of the original brief, I thought it would be helpful for:

- **Future planning** – to visualise how a real buying feature might work
- **SQL testing** – to check that all vinyl details were correctly saved in the database and shown properly in the admin dashboard

This helped confirm that data was flowing correctly from users to admin views and that key information was being stored and retrieved properly.

---

## Changes from the Original Plan

Originally, the plan was to have two separate account types: buyers and sellers. In the final version, I kept things simpler and created **one type of user account** with different page views depending on whether the user wants to buy or sell.

This made development more manageable while still covering all of the key functionality.

---

## Status

This is a **prototype**, built mainly for learning and showcasing back-end and database skills. The focus was on getting core features working rather than polishing the visual design—but I’ve tried to keep the layout clean and user-friendly so it could easily be expanded into a live project.

Website link >> https://www.fifecomptech.net/~s2265080/home.php 

---

## Screenshots

**Home page**
![www fifecomptech net_~s2265080_home php](https://github.com/user-attachments/assets/bf21e0f3-23c9-4114-8831-a771459441c9)

---

**Shop**
![www fifecomptech net_~s2265080_shop php (1)](https://github.com/user-attachments/assets/e9c84999-b18c-425b-a9c1-58ffc126f34e)


**Product page - user not logged in**
![www fifecomptech net_~s2265080_vinyl php_id=66 (1)](https://github.com/user-attachments/assets/59d0f6f1-371b-44cd-9736-e46ddcc3a00c)


**Product page - user logged in**
![www fifecomptech net_~s2265080_vinyl php_id=66](https://github.com/user-attachments/assets/74801d76-f3c1-4da5-b687-cf38301c2b50)

---

**Sell - user not logged in**
![www fifecomptech net_~s2265080_sell php](https://github.com/user-attachments/assets/9ead35df-4e13-4b90-bbd4-9fc0625b4374)


**Seller dashboard**
![www fifecomptech net_~s2265080_sell php (1)](https://github.com/user-attachments/assets/6a56fb9c-33e1-4e5a-bfe4-1c9faa238a13)

**Seller - all vinyls added**
![www fifecomptech net_~s2265080_vinyls php](https://github.com/user-attachments/assets/d8431352-c50b-4ef9-991f-337c78915794)


**Seller - add new vinyl**
![www fifecomptech net_~s2265080_vinylAdd php](https://github.com/user-attachments/assets/b378ad0d-9d09-449e-a356-39bd581253f7)


---
**Buyer - dashboard**
![www fifecomptech net_~s2265080_myOrders php](https://github.com/user-attachments/assets/5e43734f-cbad-4806-a0b0-002351d810f1)


---

**Administrator dashboard**
![image](https://github.com/user-attachments/assets/750965bd-b85b-4f65-ba17-173f7ad26999)

**Administrator - all vinyls**
![www fifecomptech net_~s2265080_allVinyls php](https://github.com/user-attachments/assets/2851596d-a33f-4eb9-8674-bdb013612cc7)


**Administrator - all orders**
![www fifecomptech net_~s2265080_allOrders php](https://github.com/user-attachments/assets/4294c883-5f6c-492c-b41a-15c26eae7f2b)




