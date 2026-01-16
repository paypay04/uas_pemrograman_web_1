// Dashboard Charts JavaScript
let salesChart, categoryChart;

function initCharts(salesData, categoryData) {
    // Destroy existing charts if they exist
    if (salesChart) salesChart.destroy();
    if (categoryChart) categoryChart.destroy();
    
    // Initialize Sales Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    salesChart = new Chart(salesCtx, {
        type: 'line',
        data: salesData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'rgba(255, 255, 255, 0.9)',
                    titleColor: '#4A4A4A',
                    bodyColor: '#4A4A4A',
                    borderColor: '#B99976',
                    borderWidth: 1,
                    callbacks: {
                        label: function(context) {
                            return `Orders: ${context.parsed.y}`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        color: '#6c757d'
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        color: '#6c757d',
                        callback: function(value) {
                            return value;
                        }
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'nearest'
            },
            elements: {
                point: {
                    radius: 4,
                    hoverRadius: 6,
                    backgroundColor: '#B99976',
                    borderColor: '#FFFFFF',
                    borderWidth: 2
                }
            }
        }
    });
    
    // Initialize Category Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    categoryChart = new Chart(categoryCtx, {
        type: 'doughnut',
        data: categoryData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        color: '#4A4A4A'
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${label}: ${value} sales (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}

function updateCharts(newData) {
    // Update sales chart
    if (salesChart && newData.sales) {
        salesChart.data.labels = newData.sales.labels;
        salesChart.data.datasets[0].data = newData.sales.data;
        salesChart.update();
    }
    
    // Update category chart
    if (categoryChart && newData.categories) {
        categoryChart.data.labels = newData.categories.labels;
        categoryChart.data.datasets[0].data = newData.categories.data;
        categoryChart.update();
    }
}

// Real-time updates (polling every 30 seconds)
let updateInterval;

function startRealTimeUpdates() {
    updateInterval = setInterval(() => {
        fetch('../modules/admin/dashboard-update.php')
            .then(response => response.json())
            .then(data => {
                updateStatistics(data.stats);
                updateCharts(data.charts);
                showNotification('Dashboard updated', 'info');
            })
            .catch(error => console.error('Update error:', error));
    }, 30000); // 30 seconds
}

function stopRealTimeUpdates() {
    if (updateInterval) {
        clearInterval(updateInterval);
    }
}

// Notification system
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 350px;';
    notification.innerHTML = `
        <strong>${type.charAt(0).toUpperCase() + type.slice(1)}!</strong> ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 3000);
}

// Export chart as image
function exportChart(chartId, filename) {
    const chart = chartId === 'salesChart' ? salesChart : categoryChart;
    if (chart) {
        const link = document.createElement('a');
        link.download = `${filename}.png`;
        link.href = chart.toBase64Image();
        link.click();
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Start real-time updates if on dashboard page
    if (document.getElementById('salesChart')) {
        startRealTimeUpdates();
    }
    
    // Export buttons
    document.querySelectorAll('.export-chart').forEach(button => {
        button.addEventListener('click', function() {
            const chartId = this.dataset.chart;
            const filename = this.dataset.filename;
            exportChart(chartId, filename);
        });
    });
});

// Pause updates when tab is not visible
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        stopRealTimeUpdates();
    } else {
        startRealTimeUpdates();
    }
});