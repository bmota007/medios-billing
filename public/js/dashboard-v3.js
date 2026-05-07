document.addEventListener('DOMContentLoaded', function () {

    const chartCanvas = document.getElementById('revenueChart');

    if (chartCanvas && typeof Chart !== 'undefined') {

        const ctx = chartCanvas.getContext('2d');

        const gradient = ctx.createLinearGradient(0, 0, 0, 360);
        gradient.addColorStop(0, 'rgba(59,130,246,.35)');
        gradient.addColorStop(.5, 'rgba(139,92,246,.18)');
        gradient.addColorStop(1, 'rgba(59,130,246,0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Apr 1','5','10','15','20','25','30'],
                datasets: [{
                    label: 'Revenue',
                    data: [10000,18000,15000,22000,21000,26000,30000],
                    borderColor: '#3b82f6',
                    backgroundColor: gradient,
                    fill: true,
                    borderWidth: 3,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#60a5fa',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    tension: .42
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,

                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#fff',
                        bodyColor: '#cbd5e1',
                        borderColor: 'rgba(255,255,255,.08)',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: false,
                        callbacks: {
                            label: function(context){
                                return '$' + context.raw.toLocaleString();
                            }
                        }
                    }
                },

                scales: {

                    x: {
                        ticks: {
                            color: '#94a3b8'
                        },
                        grid: {
                            color: 'rgba(255,255,255,.03)'
                        },
                        border: {
                            display:false
                        }
                    },

                    y: {
                        ticks: {
                            color: '#94a3b8',
                            callback: function(value){
                                return '$' + (value / 1000) + 'k';
                            }
                        },
                        grid: {
                            color: 'rgba(255,255,255,.03)'
                        },
                        border: {
                            display:false
                        }
                    }

                }

            }
        });

    }

});

