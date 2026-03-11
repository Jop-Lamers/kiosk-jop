# Kiosk JOP API Documentation

## Overview

RESTful API for managing orders and products in the Kiosk JOP system.

## Base URL

```
http://localhost/kiosk-jop/api/
```

## Endpoints

### Orders

#### Create Order

- **URL**: `create_order_fixed.php`
- **Method**: POST
- **Content-Type**: application/json
- **Request**:

```json
{
  "items": [
    {
      "product_id": 1,
      "quantity": 2,
      "price": 5.99
    }
  ],
  "total": 11.98
}
```

- **Response**:

```json
{
  "success": true,
  "data": {
    "order_id": 5,
    "pickup_number": 342,
    "total": 11.98,
    "items_count": 1
  },
  "message": "Order created successfully"
}
```

#### Get All Orders

- **URL**: `get_orders_fixed.php`
- **Method**: GET
- **Query Parameters**:
  - `status` (optional): Filter by order status (2=Placed, 3=Preparing, 4=Ready, etc.)
  - `limit` (optional): Maximum results (default: 50)
  - `offset` (optional): Pagination offset (default: 0)
- **Response**:

```json
{
  "success": true,
  "data": {
    "orders": [
      {
        "order_id": 5,
        "pickup_number": 342,
        "status": 2,
        "total": 11.98,
        "datetime": "2026-03-04 14:30:00",
        "time": "14:30",
        "items": [
          {
            "product_id": 1,
            "name": "Salad",
            "quantity": 2,
            "price": 5.99
          }
        ]
      }
    ],
    "count": 1,
    "limit": 50,
    "offset": 0
  },
  "message": "Orders retrieved successfully"
}
```

#### Get Single Order

- **URL**: `get_order.php`
- **Method**: GET
- **Query Parameters**:
  - `id` (required): Order ID
- **Response**: Same format as single order from Get All Orders

#### Update Order

- **URL**: `update_order_fixed.php`
- **Method**: PUT or POST
- **Content-Type**: application/json
- **Request**:

```json
{
  "order_id": 5,
  "status": 3,
  "total": 12.99,
  "pickup_number": 343
}
```

- **Response**:

```json
{
  "success": true,
  "data": {
    "order_id": 5,
    "updated_fields": 3
  },
  "message": "Order updated successfully"
}
```

#### Delete Order

- **URL**: `delete_order.php`
- **Method**: DELETE or POST
- **Content-Type**: application/json
- **Request**:

```json
{
  "order_id": 5
}
```

- **Response**:

```json
{
  "success": true,
  "data": {
    "order_id": 5
  },
  "message": "Order deleted successfully"
}
```

### Products

#### Get All Products

- **URL**: `get_products.php`
- **Method**: GET
- **Query Parameters**:
  - `category_id` (optional): Filter by category
  - `limit` (optional): Maximum results (default: 100)
  - `offset` (optional): Pagination offset (default: 0)
- **Response**:

```json
{
  "success": true,
  "data": {
    "products": [
      {
        "product_id": 1,
        "name": "Caesar Salad",
        "category_id": 2,
        "description": "Fresh greens with dressing",
        "price": 8.99,
        "image_url": "menu-images/salad.jpg"
      }
    ],
    "count": 1,
    "limit": 100,
    "offset": 0
  },
  "message": "Products retrieved successfully"
}
```

#### Get All Categories

- **URL**: `get_categories.php`
- **Method**: GET
- **Response**:

```json
{
  "success": true,
  "data": {
    "categories": [
      {
        "category_id": 1,
        "name": "Salads",
        "description": "Fresh salads",
        "image_url": "logos/salads.jpg"
      }
    ],
    "count": 1
  },
  "message": "Categories retrieved successfully"
}
```

## Error Responses

### 400 Bad Request

```json
{
  "success": false,
  "data": null,
  "message": "Invalid JSON input"
}
```

### 404 Not Found

```json
{
  "success": false,
  "data": null,
  "message": "Order not found"
}
```

### 405 Method Not Allowed

```json
{
  "success": false,
  "data": null,
  "message": "Method not allowed"
}
```

### 500 Server Error

```json
{
  "success": false,
  "data": null,
  "message": "Database error: [error details]"
}
```

## Order Status Codes

- `1`: Created
- `2`: Placed and Paid
- `3`: Preparing
- `4`: Ready for Pickup
- `5`: Completed
- `6`: Cancelled

## CORS

All endpoints support CORS with `Access-Control-Allow-Origin: *`

## Notes

- All numeric values should be properly typed (integers, floats)
- Dates are returned in ISO 8601 format (YYYY-MM-DD HH:MM:SS)
- Times are returned in HH:MM format
- All endpoints use UTF-8 encoding
- Transactions are used for data consistency where applicable
