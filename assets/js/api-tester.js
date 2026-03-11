/**
 * API Testing Suite for Kiosk JOP
 * Run tests in browser console or Node.js
 */

class APITester {
  constructor(apiClient) {
    this.api = apiClient;
    this.results = [];
    this.verbose = true;
  }

  log(message, type = "info") {
    const timestamp = new Date().toLocaleTimeString();
    const prefix = `[${timestamp}] ${type.toUpperCase()}:`;
    const output = `${prefix} ${message}`;

    if (this.verbose) {
      if (type === "error") {
        console.error(output);
      } else if (type === "success") {
        console.log(`%c${output}`, "color: green; font-weight: bold;");
      } else if (type === "warning") {
        console.log(`%c${output}`, "color: orange; font-weight: bold;");
      } else {
        console.log(output);
      }
    }

    this.results.push({ timestamp, type, message });
  }

  async testCreateOrder() {
    this.log("Testing: Create Order", "info");

    try {
      const testItems = [
        { product_id: 1, quantity: 2, price: 8.99 },
        { product_id: 2, quantity: 1, price: 5.99 },
      ];
      const testTotal = 23.97;

      const response = await this.api.createOrder(testItems, testTotal);

      if (
        response.success &&
        response.data.order_id &&
        response.data.pickup_number
      ) {
        this.log(
          `✓ Order created: ID=${response.data.order_id}, Pickup #${response.data.pickup_number}`,
          "success",
        );
        return response.data;
      } else {
        this.log(`✗ Failed to create order: ${response.message}`, "error");
        return null;
      }
    } catch (error) {
      this.log(`✗ Error: ${error.message}`, "error");
      return null;
    }
  }

  async testGetOrders() {
    this.log("Testing: Get Orders", "info");

    try {
      const response = await this.api.getOrders({ limit: 10 });

      if (response.success && response.data.orders) {
        this.log(`✓ Retrieved ${response.data.count} orders`, "success");
        return response.data.orders;
      } else {
        this.log(`✗ Failed to get orders: ${response.message}`, "error");
        return null;
      }
    } catch (error) {
      this.log(`✗ Error: ${error.message}`, "error");
      return null;
    }
  }

  async testGetOrder(orderId) {
    this.log(`Testing: Get Order (ID: ${orderId})`, "info");

    try {
      const response = await this.api.getOrder(orderId);

      if (response.success && response.data) {
        this.log(
          `✓ Retrieved order #${response.data.pickup_number} with ${response.data.items.length} items`,
          "success",
        );
        return response.data;
      } else {
        this.log(`✗ Failed to get order: ${response.message}`, "error");
        return null;
      }
    } catch (error) {
      this.log(`✗ Error: ${error.message}`, "error");
      return null;
    }
  }

  async testUpdateOrder(orderId) {
    this.log(`Testing: Update Order (ID: ${orderId})`, "info");

    try {
      const response = await this.api.updateOrder(orderId, {
        status: 3, // Preparing
        pickup_number: 999,
      });

      if (response.success) {
        this.log(
          `✓ Order updated: ${response.data.updated_fields} field(s) changed`,
          "success",
        );
        return response.data;
      } else {
        this.log(`✗ Failed to update order: ${response.message}`, "error");
        return null;
      }
    } catch (error) {
      this.log(`✗ Error: ${error.message}`, "error");
      return null;
    }
  }

  async testGetProducts() {
    this.log("Testing: Get Products", "info");

    try {
      const response = await this.api.getProducts({ limit: 20 });

      if (response.success && response.data.products) {
        this.log(`✓ Retrieved ${response.data.count} products`, "success");
        return response.data.products;
      } else {
        this.log(`✗ Failed to get products: ${response.message}`, "error");
        return null;
      }
    } catch (error) {
      this.log(`✗ Error: ${error.message}`, "error");
      return null;
    }
  }

  async testGetCategories() {
    this.log("Testing: Get Categories", "info");

    try {
      const response = await this.api.getCategories();

      if (response.success && response.data.categories) {
        this.log(`✓ Retrieved ${response.data.count} categories`, "success");
        return response.data.categories;
      } else {
        this.log(`✗ Failed to get categories: ${response.message}`, "error");
        return null;
      }
    } catch (error) {
      this.log(`✗ Error: ${error.message}`, "error");
      return null;
    }
  }

  async runFullTest() {
    this.log("=== Starting Full API Test Suite ===", "info");
    this.log("", "info");

    // Test Categories
    const categories = await this.testGetCategories();
    this.log("", "info");

    // Test Products
    const products = await this.testGetProducts();
    this.log("", "info");

    // Test Create Order
    const newOrder = await this.testCreateOrder();
    this.log("", "info");

    // Test Get Orders
    const orders = await this.testGetOrders();
    this.log("", "info");

    // Test Get Single Order (if we created one)
    if (newOrder) {
      const singleOrder = await this.testGetOrder(newOrder.order_id);
      this.log("", "info");

      // Test Update Order
      const updatedOrder = await this.testUpdateOrder(newOrder.order_id);
      this.log("", "info");
    }

    // Summary
    const successCount = this.results.filter(
      (r) => r.type === "success",
    ).length;
    const errorCount = this.results.filter((r) => r.type === "error").length;

    this.log(
      `=== Test Summary: ${successCount} passed, ${errorCount} failed ===`,
      "info",
    );

    return {
      success: errorCount === 0,
      passed: successCount,
      failed: errorCount,
      results: this.results,
    };
  }

  getResults() {
    return this.results;
  }

  clearResults() {
    this.results = [];
  }

  exportResults(format = "json") {
    if (format === "json") {
      return JSON.stringify(this.results, null, 2);
    } else if (format === "csv") {
      let csv = "Timestamp,Type,Message\n";
      this.results.forEach((r) => {
        csv += `"${r.timestamp}","${r.type}","${r.message}"\n`;
      });
      return csv;
    }
    return this.results;
  }
}

// Export for both browser and Node.js
if (typeof module !== "undefined" && module.exports) {
  module.exports = APITester;
}
