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
![image](https://github.com/user-attachments/assets/5792a339-0fc2-4e15-b615-5ae8374f213a)
![image](https://github.com/user-attachments/assets/1924feb8-7cac-4c75-abc7-359ce9975c80)
![image](https://github.com/user-attachments/assets/909717fa-86a2-4c1d-b48f-f3acc14ebab5)
![image](https://github.com/user-attachments/assets/a96cc40b-7cbe-438d-a804-7c96252da4c9)
![image](https://github.com/user-attachments/assets/01aad418-2b58-4612-b408-93e432c64230)

---

**Shop**
![image](https://github.com/user-attachments/assets/a65d7b97-7b52-4cda-8d76-b11389f43545)

**Product page - user not logged in**
![image](https://github.com/user-attachments/assets/49ddfb96-ba9c-435b-9a44-77f7bc18e472)

**Product page - user logged in**
![image](https://github.com/user-attachments/assets/3b468aa0-aaea-4385-b4a1-712d22a41b4f)

---

**Sell - user not logged in**
![image](https://github.com/user-attachments/assets/9be28503-2cf8-44d3-a254-a5ce830f3000)

**Seller dashboard**
![image](https://github.com/user-attachments/assets/ca39b347-fa1c-4c1c-b15e-7116a48e3bf8)

**Seller - all vinyls added**
![image](https://github.com/user-attachments/assets/f4b93258-adf2-40cb-b2df-cf865ac227a4)

**Seller - add new vinyl**
![image](https://github.com/user-attachments/assets/1e18730e-2e09-4bcd-a49a-bf48161ecb43)

---
**Buyer - dashboard**
![image](https://github.com/user-attachments/assets/4b1f6607-d683-40ae-bc84-5c20f5fca57f)

---

**Administrator dashboard**
![image](https://github.com/user-attachments/assets/06d91df0-1189-45d7-8f5a-95a2aeed1c9d)

**Administrator - all vinyls**
![image](https://github.com/user-attachments/assets/763ebac0-6d4e-4e50-a6ae-2432259f9679)

**Administrator - all orders**
![image](https://github.com/user-attachments/assets/faf2b24b-a6c8-4136-8095-d5cf03d62c3a)



