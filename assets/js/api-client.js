/**
 * Kiosk JOP API Client
 * Complete API integration for the Kiosk JOP system
 */

class KioskAPI {
  constructor(baseUrl = "/kiosk-jop/api/") {
    this.baseUrl = baseUrl;
    this.timeout = 10000; // 10 seconds
  }

  /**
   * Make API request
   * @param {string} endpoint - API endpoint (without .php)
   * @param {string} method - HTTP method (GET, POST, PUT, DELETE)
   * @param {object} data - Request body data
   * @returns {Promise} API response
   */
  async request(endpoint, method = "GET", data = null) {
    const url = `${this.baseUrl}${endpoint}`;
    const options = {
      method: method,
      headers: {
        "Content-Type": "application/json",
      },
    };

    if (
      data &&
      (method === "POST" || method === "PUT" || method === "DELETE")
    ) {
      options.body = JSON.stringify(data);
    }

    try {
      const controller = new AbortController();
      const timeoutId = setTimeout(() => controller.abort(), this.timeout);

      const response = await fetch(url, {
        ...options,
        signal: controller.signal,
      });

      clearTimeout(timeoutId);

      if (!response.ok) {
        const errorData = await response.json();
        throw new Error(
          errorData.message ||
            `HTTP ${response.status}: ${response.statusText}`,
        );
      }

      return await response.json();
    } catch (error) {
      if (error.name === "AbortError") {
        throw new Error("Request timeout");
      }
      throw error;
    }
  }

  /**
   * Orders API Methods
   */

  /**
   * Create a new order
   * @param {array} items - Array of items with {product_id, quantity, price}
   * @param {number} total - Total order price
   * @returns {Promise} Order response with order_id and pickup_number
   */
  async createOrder(items, total) {
    if (!items || !Array.isArray(items) || items.length === 0) {
      throw new Error("Items must be a non-empty array");
    }
    if (!total || total <= 0) {
      throw new Error("Total must be greater than 0");
    }

    return this.request("create_order_fixed.php", "POST", {
      items: items,
      total: total,
    });
  }

  /**
   * Get all orders with optional filters
   * @param {object} options - {status, limit, offset}
   * @returns {Promise} Array of orders
   */
  async getOrders(options = {}) {
    const params = new URLSearchParams();
    if (options.status !== undefined) params.append("status", options.status);
    if (options.limit !== undefined) params.append("limit", options.limit);
    if (options.offset !== undefined) params.append("offset", options.offset);

    const endpoint = `get_orders_fixed.php${params.toString() ? "?" + params : ""}`;
    return this.request(endpoint, "GET");
  }

  /**
   * Get single order by ID
   * @param {number} orderId - Order ID
   * @returns {Promise} Order details
   */
  async getOrder(orderId) {
    if (!orderId) {
      throw new Error("Order ID is required");
    }
    return this.request(`get_order.php?id=${orderId}`, "GET");
  }

  /**
   * Update order
   * @param {number} orderId - Order ID
   * @param {object} updates - {status, pickup_number, total}
   * @returns {Promise} Update response
   */
  async updateOrder(orderId, updates = {}) {
    if (!orderId) {
      throw new Error("Order ID is required");
    }

    return this.request("update_order_fixed.php", "PUT", {
      order_id: orderId,
      ...updates,
    });
  }

  /**
   * Delete order
   * @param {number} orderId - Order ID
   * @returns {Promise} Delete response
   */
  async deleteOrder(orderId) {
    if (!orderId) {
      throw new Error("Order ID is required");
    }

    return this.request("delete_order.php", "DELETE", {
      order_id: orderId,
    });
  }

  /**
   * Products API Methods
   */

  /**
   * Get all products with optional filters
   * @param {object} options - {category_id, limit, offset}
   * @returns {Promise} Array of products
   */
  async getProducts(options = {}) {
    const params = new URLSearchParams();
    if (options.category_id !== undefined)
      params.append("category_id", options.category_id);
    if (options.limit !== undefined) params.append("limit", options.limit);
    if (options.offset !== undefined) params.append("offset", options.offset);

    const endpoint = `get_products.php${params.toString() ? "?" + params : ""}`;
    return this.request(endpoint, "GET");
  }

  /**
   * Get all categories
   * @returns {Promise} Array of categories
   */
  async getCategories() {
    return this.request("get_categories.php", "GET");
  }

  /**
   * Get products by category
   * @param {number} categoryId - Category ID
   * @returns {Promise} Array of products in category
   */
  async getProductsByCategory(categoryId) {
    if (!categoryId) {
      throw new Error("Category ID is required");
    }
    return this.getProducts({ category_id: categoryId });
  }
}

// Export for both browser and Node.js
if (typeof module !== "undefined" && module.exports) {
  module.exports = KioskAPI;
}
