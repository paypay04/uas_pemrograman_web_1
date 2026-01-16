// Real-time dashboard updates
class RealTimeDashboard {
    constructor() {
        this.isConnected = false;
        this.socket = null;
        this.updateInterval = null;
    }
    
    connect() {
        // Try WebSocket first
        if ('WebSocket' in window) {
            this.connectWebSocket();
        } else {
            // Fallback to polling
            this.startPolling();
        }
    }
    
    connectWebSocket() {
        const protocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
        const wsUrl = `${protocol}//${window.location.host}/ws/dashboard`;
        
        this.socket = new WebSocket(wsUrl);
        
        this.socket.onopen = () => {
            console.log('WebSocket connected');
            this.isConnected = true;
            this.send({ type: 'subscribe', channel: 'dashboard' });
        };
        
        this.socket.onmessage = (event) => {
            const data = JSON.parse(event.data);
            this.handleMessage(data);
        };
        
        this.socket.onclose = () => {
            console.log('WebSocket disconnected');
            this.isConnected = false;
            // Try to reconnect after 5 seconds
            setTimeout(() => this.connectWebSocket(), 5000);
        };
        
        this.socket.onerror = (error) => {
            console.error('WebSocket error:', error);
            this.isConnected = false;
        };
    }
    
    startPolling() {
        this.updateInterval = setInterval(() => {
            this.fetchUpdates();
        }, 15000); // Poll every 15 seconds
    }
    
    async fetchUpdates() {
        try {
            const response = await fetch('/api/dashboard/updates');
            const data = await response.json();
            this.handleMessage(data);
        } catch (error) {
            console.error('Fetch error:', error);
        }
    }
    
    handleMessage(data) {
        switch (data.type) {
            case 'new_order':
                this.handleNewOrder(data.order);
                break;
            case 'order_update':
                this.handleOrderUpdate(data.order);
                break;
            case 'stats_update':
                this.updateStatistics(data.stats);
                break;
            case 'notification':
                this.showNotification(data.message, data.level);
                break;
        }
    }
    
    handleNewOrder(order) {
        // Update recent orders table
        const tableBody = document.querySelector('#recentOrders tbody');
        if (tableBody) {
            const newRow = this.createOrderRow(order);
            tableBody.insertBefore(newRow, tableBody.firstChild);
            
            // Remove last row if more than 5
            if (tableBody.children.length > 5) {
                tableBody.removeChild(tableBody.lastChild);
            }
        }
        
        // Update stats
        this.incrementStat('orders');
        this.incrementStat('revenue', order.total_amount);
        
        // Show notification
        this.showNotification(`New order #${order.order_number} received!`, 'success');
    }
    
    createOrderRow(order) {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>#${order.order_number}</td>
            <td>${order.customer_name}</td>
            <td>${new Date(order.created_at).toLocaleDateString()}</td>
            <td>Rp ${order.total_amount.toLocaleString()}</td>
            <td><span class="badge bg-warning">Pending</span></td>
        `;
        return row;
    }
    
    updateStatistics(stats) {
        // Update all stat cards
        Object.keys(stats).forEach(key => {
            const element = document.querySelector(`[data-stat="${key}"]`);
            if (element) {
                this.animateCounter(element, stats[key]);
            }
        });
    }
    
    animateCounter(element, newValue) {
        const oldValue = parseInt(element.textContent.replace(/[^0-9]/g, ''));
        const duration = 1000; // 1 second
        const steps = 60;
        const increment = (newValue - oldValue) / steps;
        let current = oldValue;
        
        const timer = setInterval(() => {
            current += increment;
            if ((increment > 0 && current >= newValue) || 
                (increment < 0 && current <= newValue)) {
                current = newValue;
                clearInterval(timer);
            }
            
            if (element.dataset.stat === 'revenue') {
                element.textContent = `Rp ${Math.floor(current).toLocaleString()}`;
            } else {
                element.textContent = Math.floor(current);
            }
        }, duration / steps);
    }
    
    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-bell"></i>
                <span>${message}</span>
            </div>
            <button class="notification-close">&times;</button>
        `;
        
        // Add to notification container
        const container = document.getElementById('notificationContainer') || 
                         this.createNotificationContainer();
        container.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.classList.add('fade-out');
            setTimeout(() => notification.remove(), 300);
        }, 5000);
        
        // Close button
        notification.querySelector('.notification-close').addEventListener('click', () => {
            notification.remove();
        });
    }
    
    createNotificationContainer() {
        const container = document.createElement('div');
        container.id = 'notificationContainer';
        container.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 350px;
        `;
        document.body.appendChild(container);
        return container;
    }
    
    disconnect() {
        if (this.socket) {
            this.socket.close();
        }
        if (this.updateInterval) {
            clearInterval(this.updateInterval);
        }
    }
}

// Initialize real-time dashboard
document.addEventListener('DOMContentLoaded', function() {
    window.dashboard = new RealTimeDashboard();
    window.dashboard.connect();
    
    // Export to window for debugging
    window.updateDashboard = function() {
        window.dashboard.fetchUpdates();
    };
});