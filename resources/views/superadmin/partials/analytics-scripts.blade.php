<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Get data from Laravel
    const monthlyData = @json($monthlyTrend ?? []);

    // Appointments Trend Chart
    const appointmentsTrendCtx = document.getElementById('appointmentsTrendChart').getContext('2d');

    // Prepare monthly data
    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const trendLabels = [];
    const trendCounts = [];

    // Get last 6 months
    for (let i = 5; i >= 0; i--) {
        const date = new Date();
        date.setMonth(date.getMonth() - i);
        const month = date.getMonth() + 1;
        const year = date.getFullYear();

        trendLabels.push(monthNames[date.getMonth()]);

        const monthData = monthlyData.find(item => item.month == month && item.year == year);
        trendCounts.push(monthData ? monthData.count : 0);
    }

    new Chart(appointmentsTrendCtx, {
        type: 'line',
        data: {
            labels: trendLabels,
            datasets: [{
                label: 'Appointments',
                data: trendCounts,
                borderColor: '#009fb1',
                backgroundColor: 'rgba(0, 159, 177, 0.1)',
                tension: 0.4,
                borderWidth: 5,
                pointRadius: 8,
                pointHoverRadius: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 1.2,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        font: {
                            size: 16
                        }
                    }
                },
                x: {
                    ticks: {
                        font: {
                            size: 16
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    labels: {
                        font: {
                            size: 16
                        }
                    }
                }
            }
        }
    });

    // User Distribution Chart
    const userDistributionCtx = document.getElementById('userDistributionChart').getContext('2d');
    new Chart(userDistributionCtx, {
        type: 'doughnut',
        data: {
            labels: ['Patients', 'Admins'],
            datasets: [{
                data: [{{ $userStats['patients'] ?? 0 }}, {{ $userStats['admins'] ?? 0 }}],
                backgroundColor: ['#28a745', '#ffc107'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 0.9,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        font: {
                            size: 16
                        },
                        padding: 25
                    }
                }
            }
        }
    });
</script>
