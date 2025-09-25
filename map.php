mediatheque_paris_grp1/
│
├── config/
│   ├── database.php
│
├── controllers/
│   ├──admin_controller.php
│   ├──auth_controller.php
│   ├──catalog_controller.php
│   ├──rental_controller.php
|   └──home_controller.php
├── core/
│   ├──database.php
|   ├──router.php
|   └──view.php
├── database/
|   └──schema.sql
├── includes/
│   └──helpers.php
├── models/
|    ├──catalogue_model.php
|    ├──loan_model.php
|    ├──media_model.php
|    ├──item_model.php
|    ├──rental_model.php
|    └──user_model.php
├── public/
|    ├──assets/
|    |   ├──css/
|    |   |  └──style.css
|    |   ├──images/    
|    |   └──js/
|    |      └──app.js                                                           
|    ├──.htaccess
|    └──index.php
├── views/
│   ├── admin/
|   |   ├──dashboard.php    
|   |   ├──loans_list.php    
|   |   ├──media_edit.php    
|   |   ├──media_list.php    
|   |   ├──user_detail.php    
|   |   └──users_list.php
│   ├── auth/
|   |   ├──login.php    
|   |   └──register.php
│   ├── catalog/
|   |   ├──books.php   
|   |   ├──games.php   
|   |   ├──index.php   
|   |   └──movies.php
|   |  
|   ├──errors/
|   |   └──404.php
|   ├──home/  
|   |   ├──about.php
|   |   ├──catalogue.php
|   |   ├──contact.php
|   |   ├──index.php
|   |   ├──profile.php
|   |   └──test.php
|   ├──layouts/  
|   |  └──layouts.php
|   └──rental/
|      └──my_rentals.php
├── .gitignore
├──bootstrap.php
├──CHANGELOG.md
├──CODING_STANDARDS.md
├──CODING_STANDARDS.pdf
├──map.php
├──README.md
└── README.pdf