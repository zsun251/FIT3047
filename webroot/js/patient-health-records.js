/**
 * Patient Health Records - Chart Initialization
 */

function initVitalsChart(chartData) {
    const ctx = document.getElementById('vitalsChart');

    if (!ctx) {
        console.error('Chart canvas not found');
        return;
    }

    // Filter out null values and create datasets
    const createDataset = (data, label, color) => {
        // Only include if there's at least one non-null value
        const hasData = data.some(val => val !== null && val !== '');
        if (!hasData) return null;

        return {
            label: label,
            data: data,
            borderColor: color,
            backgroundColor: color.replace(')', ', 0.1)').replace('rgb', 'rgba'),
            tension: 0.4,
            fill: true,
            spanGaps: true // This connects points even if there are nulls
        };
    };

    const datasets = [
        createDataset(chartData.oxygen, 'Oxygen Saturation (%)', 'rgb(239, 68, 68)'),
        createDataset(chartData.resp, 'Respiratory Rate (bpm)', 'rgb(59, 130, 246)'),
        createDataset(chartData.weight, 'Weight (kg)', 'rgb(16, 185, 129)'),
        createDataset(chartData.lung, 'Lung Function (%)', 'rgb(139, 92, 246)'),
        createDataset(chartData.pulse, 'Pulse Oximetry', 'rgb(245, 158, 11)')
    ].filter(dataset => dataset !== null); // Remove null datasets

    new Chart(ctx.getContext('2d'), {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 15,
                        font: {
                            size: 12,
                            family: "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif"
                        }
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    cornerRadius: 8,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += context.parsed.y;
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        font: {
                            size: 11
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 11
                        },
                        maxRotation: 45,
                        minRotation: 45
                    }
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            }
        }
    });
}

// Initialize when document is ready
document.addEventListener('DOMContentLoaded', function() {
    // Check if chart data is available
    if (typeof vitalsChartData !== 'undefined') {
        initVitalsChart(vitalsChartData);
    }
});
