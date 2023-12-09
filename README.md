# Restaurant Ordering System

## Overview

This repository contains the source code for a Restaurant Ordering System implemented in Laravel. The system manages orders, products, ingredients, and handles low stock alerts for ingredients.

### Installation

1. Clone the repository:

   ```bash
   git clone git@github.com:Msalah11/foodics.git
2. Navigate to the project directory:

   ```bash
   cd foodics
   ```
   
3. Install the project dependencies:

   ```bash
    composer install
    ```
   
4. Copy the .env.example file to .env and configure the database connection.

   ```bash
   cp .env.example .env
   ```
   
5. Generate the application key:

   ```bash
    php artisan key:generate
    ```

6. Run the database migrations:
    
   ```bash
    php artisan migrate --seed
    ```
   
7. Run the development server (the output will give the address):

   ```bash
   php artisan serve
   ```

### Class Diagram
![](UML\sequence-diagram.png)

### Database Schema
![](UML\database-schema.png)

### Usage
1. Make API requests to place orders with product details
`POST /api/place-order`
   ```bash
       {
          "products": [
            {
              "product_id": 1,
              "quantity": 2
            }
          ]
      }
   ```

2. The system processes the order, updates stock levels, and sends email alerts for low stock.
3. Monitor the system through the provided classes:
- `OrderController[app/Http/Controllers/OrderController.php]`: Handles order placement requests.
- `StoreOrderRequest[app/Http/Requests/Order/StoreOrderRequest.php]`: Validates the order placement request.
- `OrderService[app/Services/OrderService.php]`: Handles the order placement logic.
- `APIResponse[app/Traits/APIResponse.php]`: Handles the API response format.
- `IngredientObserver[app/Observers/IngredientObserver.php]`: Handles the low stock alert for ingredients.
- `SendLowStockAlert[app/Jobs/SendLowStockAlert.php]`: Sends the low stock alert email.
- `LowStockAlert[app/Mail/LowStockAlert.php]`: The low stock alert email template.

### Testing
To run the feature tests, run the following command:
   ```bash
   php artisan test
   ```
