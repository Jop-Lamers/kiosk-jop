function fetchOrders() {
    fetch('api/get_orders.php')
        .then(response => response.json())
        .then(data => renderOrders(data));
}

function renderOrders(orders) {
    const grid = document.getElementById('orders-grid');
    grid.innerHTML = '';
    
    if (orders.length === 0) {
        grid.innerHTML = '<p>No active orders</p>';
        return;
    }

    orders.forEach(order => {
        const div = document.createElement('div');
        div.className = `order-card status-${order.status}`;
        
        let btnHtml = '';
        if (order.status == '2') {
            btnHtml = `<button class="status-btn btn-start" onclick="updateStatus(${order.order_id}, 3)">Start Preparing</button>`;
        } else if (order.status == '3') {
            btnHtml = `<button class="status-btn btn-done" onclick="updateStatus(${order.order_id}, 4)">Ready for Pickup</button>`;
        }

        let itemsHtml = order.items.map(item => `
            <div class="order-item">
                <span style="font-weight:bold;">${item.quantity}x</span>
                <span>${item.name}</span>
            </div>
        `).join('');

        div.innerHTML = `
            <div class="order-header">
                <span>#${order.pickup_number}</span>
                <span>${order.time}</span>
            </div>
            <div class="order-body">
                ${itemsHtml}
            </div>
            <div class="order-footer">
                ${btnHtml}
            </div>
        `;
        grid.appendChild(div);
    });
}

function updateStatus(orderId, newStatus) {
    fetch('api/update_order.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ order_id: orderId, status: newStatus })
    }).then(() => fetchOrders());
}

// Poll every 5 seconds
setInterval(fetchOrders, 5000);
fetchOrders();
