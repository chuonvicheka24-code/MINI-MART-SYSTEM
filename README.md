# MINI-MART-SYSTEM
Group 02 B15E


E-COMMERCE 
Project: MINI MART SYSTEM 
Group 02(B15E) 
1. Introduction  
The E-Commerce Mini Mart system is an integrated online management and shopping 
system that connects customers to inventory and delivery services directly. This system allows 
customers to easily order daily necessities and provides convenience to the administrator 
(Admin) for real-time inventory management, automatic inventory purchase notifications, and 
shipping tracking. 
2. Problem Statement  
Traditional neighborhood mini-marts struggle with manual order management, 
untracked inventory, and inefficient delivery coordination. Customers lack real-time product 
availability, leading to delayed orders and inventory shortages. 
3. Objectives  
• Provide an intuitive catalog for customer order placement. 
• Automate low-stock alerts and inventory restocking workflows. 
• Streamline order processing, invoice generation, and delivery tracking. 
• Restrict inventory administrative actions to authorized personnel. 
4. Target Users  
• Customers: Shoppers browsing categories, placing orders, and selecting delivery 
methods. 
• Store Admin: Authorized staff managing products, monitoring low stock, processing 
purchase orders, and analyzing sales reports. 
• Delivery Personnel: Logistics staff executing store-to-customer order fulfillments. 
5. Main Features  
• Product Catalog: Categorized items (Fruit, Meat, Grocery, Vegetable, Snack, Drinks). 
• Order Processing: Itemized order lists, cost calculations, payment gateway integration, 
and printable invoices. 
• Delivery Management: Mode selection (truck, motorcycle), location routing, and 
automated delivery fee calculation (0.10% of total cost). 
• Stock & Inventory Control: Admin authentication, CRUD product management, low
stock alerts (triggered when quantity < 10), and sales reporting (export/print). 
6. Database and System Design  
• Index / Routing: Serves as the system entry point; manages central request routing 
across all sub-pages. 
• Product: Displays items grouped by category; renders product gallery and pricing 
interfaces. 
• Order: Calculates totals, processes payment gateway operations, and generates/prints 
invoices. 
• Delivery: Calculates store-to-customer routes; automatically applies the 0.10% delivery 
fee formula. 
• Stock & Admin: Provides restricted access; handles CRUD operations for catalog items; 
triggers low-stock reorders when quantity falls below 10. 
7. Technologies  
• Frontend: HTML5, CSS3, JavaScript (or Frameworks like React / Vue.js) 
• Backend: Laravel (PHP, RESTful API, Sanctum, Eloquent ORM) 
• Database: MySQL and phpMyAdmin. 
• Tools: Visual Studio Code, Git, GitHub, Postman, XAMPP, phpMyAdmin  
8. Team Responsibilities  
• Frontend Developer: UI/UX design for Index Page, Product Categories, Order forms, and 
Contact Page. 
• Backend Developer: API endpoints for order calculations, delivery fee algorithms, and 
invoice rendering. 
• Database Administrator: Schema design for product stock, user admin roles, and 
inventory triggers. 
• Full-Stack / Admin Specialist: Building secure Admin Auth gateways and stock report 
export features. 
9. Project Timeline  
• Phase 1 (Week 1-2): Requirements gathering, UI mockups, and database schema design. 
• Phase 2 (Week 3-4): Frontend integration (Product, Order, and Contact pages). 
• Phase 3 (Week 5-6): Backend logic (Admin authentication, Stock triggers, Delivery 
calculation). 
• Phase 4 (Week 7): System integration, export/print testing, and security checks. 
• Phase 5 (Week 8): Admin dashboard and user acceptance testing. 
• Phase 6 (Week 9-12): Deployment, documentation, online testing, and final 
presentation.  
10. Expected Result  
A fully operational e-commerce platform that reduces manual inventory tracking errors, 
automates restocking notifications for items under 10 units, and delivers a fast, transparent 
checkout and delivery experience for customers.
