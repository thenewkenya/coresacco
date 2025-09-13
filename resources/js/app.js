import './bootstrap';

// Dynamic Chart.js loading - only load when needed
let Chart = null;
let chartLoaded = false;

async function loadChartJS() {
    if (chartLoaded) return Chart;
    
    const { Chart: ChartJS, registerables } = await import('chart.js');
    const { default: adapter } = await import('chartjs-adapter-date-fns');
    
    ChartJS.register(...registerables);
    Chart = ChartJS;
    chartLoaded = true;
    
    return Chart;
}

// Make Chart available globally
window.Chart = () => loadChartJS();

// Store chart instances for cleanup
window.dashboardCharts = {};

// Dashboard Analytics Charts
document.addEventListener('DOMContentLoaded', function() {
    initializeDashboardCharts();
});

// Listen for Livewire navigation events
document.addEventListener('livewire:navigated', function() {
    // Small delay to ensure DOM is updated
    setTimeout(async () => {
        await initializeDashboardCharts();
    }, 100);
});

// Clean up charts when navigating away
document.addEventListener('livewire:navigating', function() {
    cleanupDashboardCharts();
});

function cleanupDashboardCharts() {
    // Destroy existing chart instances
    Object.values(window.dashboardCharts).forEach(chart => {
        if (chart && typeof chart.destroy === 'function') {
            chart.destroy();
        }
    });
    window.dashboardCharts = {};
}

async function initializeDashboardCharts() {
    // Clean up existing charts first
    cleanupDashboardCharts();
    
    // Load Chart.js dynamically
    const Chart = await loadChartJS();
    
    // Member Growth Chart
    const memberGrowthCtx = document.getElementById('memberGrowthChart');
    if (memberGrowthCtx) {
        window.dashboardCharts.memberGrowth = new Chart(memberGrowthCtx, {
            type: 'line',
            data: {
                labels: window.memberGrowthData?.map(item => item.date) || [],
                datasets: [{
                    label: 'New Members',
                    data: window.memberGrowthData?.map(item => item.count) || [],
                    borderColor: 'rgb(249, 115, 22)',
                    backgroundColor: 'rgba(249, 115, 22, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    // Transaction Volume Chart
    const transactionVolumeCtx = document.getElementById('transactionVolumeChart');
    if (transactionVolumeCtx) {
        window.dashboardCharts.transactionVolume = new Chart(transactionVolumeCtx, {
            type: 'bar',
            data: {
                labels: window.transactionVolumeData?.map(item => item.date) || [],
                datasets: [{
                    label: 'Transaction Volume',
                    data: window.transactionVolumeData?.map(item => item.total) || [],
                    backgroundColor: 'rgba(249, 115, 22, 0.6)',
                    borderColor: 'rgb(249, 115, 22)',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: false
                        },
                        ticks: {
                            callback: function(value) {
                                return 'KES ' + value.toLocaleString();
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    // Loan Status Pie Chart
    const loanStatusCtx = document.getElementById('loanStatusChart');
    if (loanStatusCtx) {
        window.dashboardCharts.loanStatus = new Chart(loanStatusCtx, {
            type: 'doughnut',
            data: {
                labels: window.loanStatusData?.map(item => item.status) || [],
                datasets: [{
                    data: window.loanStatusData?.map(item => item.count) || [],
                    backgroundColor: [
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(249, 115, 22, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(168, 85, 247, 0.8)',
                        'rgba(59, 130, 246, 0.8)'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    }
                }
            }
        });
    }
}

// Export for global use
window.initializeDashboardCharts = initializeDashboardCharts;
window.cleanupDashboardCharts = cleanupDashboardCharts;
