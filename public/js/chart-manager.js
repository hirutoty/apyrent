/**
 * Chart Manager - Utility untuk mengelola Chart.js
 * Digunakan untuk semua halaman yang memiliki grafik
 * 
 * Dependencies: Chart.js v4.x
 */

class ChartManager {
    constructor() {
        this.charts = {};
        this.defaultColors = {
            primary: '#4f6ef7',
            success: '#10b981',
            warning: '#f59e0b',
            danger: '#ef4444',
            info: '#3b82f6',
            purple: '#8b5cf6',
            pink: '#ec4899',
            orange: '#f97316',
            teal: '#14b8a6',
            cyan: '#06b6d4',
        };
        this.colorPalette = [
            '#4f6ef7', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6',
            '#ec4899', '#3b82f6', '#14b8a6', '#f97316', '#06b6d4'
        ];
    }

    /**
     * Initialize Pie Chart
     * @param {string} canvasId - Canvas element ID
     * @param {object} data - Chart data {labels: [], datasets: []}
     * @param {object} options - Custom options
     */
    initPieChart(canvasId, data, options = {}) {
        this.destroyChart(canvasId);

        const ctx = document.getElementById(canvasId);
        if (!ctx) {
            console.error(`Canvas element #${canvasId} not found`);
            return null;
        }

        const defaultOptions = {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 1.5, // Make pie chart smaller vertically
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: {
                            size: 12,
                            family: "'Plus Jakarta Sans', sans-serif"
                        },
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(15, 17, 23, 0.95)',
                    titleFont: {
                        size: 13,
                        family: "'Plus Jakarta Sans', sans-serif",
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 12,
                        family: "'Plus Jakarta Sans', sans-serif"
                    },
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed !== null) {
                                // Format number with thousand separator
                                label += new Intl.NumberFormat('id-ID').format(context.parsed);
                            }
                            return label;
                        }
                    }
                }
            }
        };

        const mergedOptions = this.deepMerge(defaultOptions, options);

        this.charts[canvasId] = new Chart(ctx, {
            type: 'doughnut',
            data: this.processChartData(data, 'pie'),
            options: mergedOptions
        });

        return this.charts[canvasId];
    }

    /**
     * Initialize Bar Chart
     * @param {string} canvasId - Canvas element ID
     * @param {object} data - Chart data
     * @param {object} options - Custom options
     */
    initBarChart(canvasId, data, options = {}) {
        this.destroyChart(canvasId);

        const ctx = document.getElementById(canvasId);
        if (!ctx) {
            console.error(`Canvas element #${canvasId} not found`);
            return null;
        }

        const defaultOptions = {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)',
                        drawBorder: false
                    },
                    ticks: {
                        font: {
                            size: 11,
                            family: "'Plus Jakarta Sans', sans-serif"
                        },
                        callback: function(value) {
                            // Format large numbers
                            if (value >= 1000000) {
                                return (value / 1000000).toFixed(1) + 'M';
                            } else if (value >= 1000) {
                                return (value / 1000).toFixed(0) + 'K';
                            }
                            return value;
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 11,
                            family: "'Plus Jakarta Sans', sans-serif"
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        padding: 15,
                        font: {
                            size: 12,
                            family: "'Plus Jakarta Sans', sans-serif"
                        },
                        usePointStyle: true
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(15, 17, 23, 0.95)',
                    titleFont: {
                        size: 13,
                        family: "'Plus Jakarta Sans', sans-serif",
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 12,
                        family: "'Plus Jakarta Sans', sans-serif"
                    },
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            }
        };

        const mergedOptions = this.deepMerge(defaultOptions, options);

        this.charts[canvasId] = new Chart(ctx, {
            type: 'bar',
            data: this.processChartData(data, 'bar'),
            options: mergedOptions
        });

        return this.charts[canvasId];
    }

    /**
     * Initialize Line Chart
     * @param {string} canvasId - Canvas element ID
     * @param {object} data - Chart data
     * @param {object} options - Custom options
     */
    initLineChart(canvasId, data, options = {}) {
        this.destroyChart(canvasId);

        const ctx = document.getElementById(canvasId);
        if (!ctx) {
            console.error(`Canvas element #${canvasId} not found`);
            return null;
        }

        const defaultOptions = {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)',
                        drawBorder: false
                    },
                    ticks: {
                        font: {
                            size: 11,
                            family: "'Plus Jakarta Sans', sans-serif"
                        },
                        callback: function(value) {
                            // Format large numbers
                            if (value >= 1000000) {
                                return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                            } else if (value >= 1000) {
                                return 'Rp ' + (value / 1000).toFixed(0) + 'K';
                            }
                            return 'Rp ' + value;
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 11,
                            family: "'Plus Jakarta Sans', sans-serif"
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        padding: 15,
                        font: {
                            size: 12,
                            family: "'Plus Jakarta Sans', sans-serif"
                        },
                        usePointStyle: true
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(15, 17, 23, 0.95)',
                    titleFont: {
                        size: 13,
                        family: "'Plus Jakarta Sans', sans-serif",
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 12,
                        family: "'Plus Jakarta Sans', sans-serif"
                    },
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            },
            elements: {
                line: {
                    tension: 0.4, // Smooth curve
                    borderWidth: 3
                },
                point: {
                    radius: 4,
                    hitRadius: 10,
                    hoverRadius: 6,
                    hoverBorderWidth: 2
                }
            }
        };

        const mergedOptions = this.deepMerge(defaultOptions, options);

        this.charts[canvasId] = new Chart(ctx, {
            type: 'line',
            data: this.processChartData(data, 'line'),
            options: mergedOptions
        });

        return this.charts[canvasId];
    }

    /**
     * Update existing chart with new data
     * @param {string} canvasId - Canvas element ID
     * @param {object} newData - New chart data
     */
    updateChart(canvasId, newData) {
        const chart = this.charts[canvasId];
        if (!chart) {
            console.error(`Chart #${canvasId} not found`);
            return;
        }

        const chartType = chart.config.type === 'doughnut' ? 'pie' : chart.config.type;
        const processedData = this.processChartData(newData, chartType);

        chart.data.labels = processedData.labels;
        chart.data.datasets = processedData.datasets;
        chart.update('active');
    }

    /**
     * Destroy chart instance
     * @param {string} canvasId - Canvas element ID
     */
    destroyChart(canvasId) {
        if (this.charts[canvasId]) {
            this.charts[canvasId].destroy();
            delete this.charts[canvasId];
        }
    }

    /**
     * Destroy all charts
     */
    destroyAllCharts() {
        Object.keys(this.charts).forEach(canvasId => {
            this.destroyChart(canvasId);
        });
    }

    /**
     * Process and format chart data
     * @param {object} data - Raw chart data
     * @param {string} type - Chart type (pie, bar)
     */
    processChartData(data, type) {
        if (!data || !data.labels) {
            return {
                labels: [],
                datasets: []
            };
        }

        const processedData = {
            labels: data.labels,
            datasets: []
        };

        if (!data.datasets || data.datasets.length === 0) {
            return processedData;
        }

        data.datasets.forEach((dataset, index) => {
            const processedDataset = { ...dataset };

            // Auto-assign colors if not provided
            if (!processedDataset.backgroundColor) {
                if (type === 'pie') {
                    processedDataset.backgroundColor = this.colorPalette.slice(0, data.labels.length);
                    processedDataset.borderColor = '#ffffff';
                    processedDataset.borderWidth = 2;
                } else if (type === 'line') {
                    const color = this.colorPalette[index % this.colorPalette.length];
                    processedDataset.borderColor = color;
                    processedDataset.backgroundColor = this.hexToRgba(color, 0.1);
                    processedDataset.fill = true;
                    processedDataset.pointBackgroundColor = color;
                    processedDataset.pointBorderColor = '#ffffff';
                    processedDataset.pointBorderWidth = 2;
                } else {
                    processedDataset.backgroundColor = this.colorPalette[index % this.colorPalette.length];
                    processedDataset.borderColor = this.colorPalette[index % this.colorPalette.length];
                    processedDataset.borderWidth = 2;
                    processedDataset.borderRadius = 6;
                }
            }

            processedData.datasets.push(processedDataset);
        });

        return processedData;
    }

    /**
     * Show loading state for chart
     * @param {string} containerId - Container element ID
     */
    showLoading(containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;

        const loadingHtml = `
            <div class="flex items-center justify-center h-full min-h-[250px]">
                <div class="text-center">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-gray-200 border-t-blue-600"></div>
                    <p class="text-sm text-gray-500 mt-3">Memuat grafik...</p>
                </div>
            </div>
        `;

        container.innerHTML = loadingHtml;
    }

    /**
     * Show error state for chart
     * @param {string} containerId - Container element ID
     * @param {string} message - Error message
     */
    showError(containerId, message = 'Gagal memuat data grafik') {
        const container = document.getElementById(containerId);
        if (!container) return;

        const errorHtml = `
            <div class="flex items-center justify-center h-full min-h-[250px]">
                <div class="text-center">
                    <i class="fa fa-exclamation-triangle text-red-500 text-3xl"></i>
                    <p class="text-sm text-gray-600 mt-3">${message}</p>
                </div>
            </div>
        `;

        container.innerHTML = errorHtml;
    }

    /**
     * Deep merge objects
     */
    deepMerge(target, source) {
        const output = Object.assign({}, target);
        if (this.isObject(target) && this.isObject(source)) {
            Object.keys(source).forEach(key => {
                if (this.isObject(source[key])) {
                    if (!(key in target)) {
                        Object.assign(output, { [key]: source[key] });
                    } else {
                        output[key] = this.deepMerge(target[key], source[key]);
                    }
                } else {
                    Object.assign(output, { [key]: source[key] });
                }
            });
        }
        return output;
    }

    /**
     * Check if value is object
     */
    isObject(item) {
        return item && typeof item === 'object' && !Array.isArray(item);
    }

    /**
     * Format currency for display
     * @param {number} value - Numeric value
     */
    formatCurrency(value) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(value);
    }

    /**
     * Format number with thousand separator
     * @param {number} value - Numeric value
     */
    formatNumber(value) {
        return new Intl.NumberFormat('id-ID').format(value);
    }

    /**
     * Convert hex color to rgba
     * @param {string} hex - Hex color code
     * @param {number} alpha - Alpha value (0-1)
     */
    hexToRgba(hex, alpha = 1) {
        const r = parseInt(hex.slice(1, 3), 16);
        const g = parseInt(hex.slice(3, 5), 16);
        const b = parseInt(hex.slice(5, 7), 16);
        return `rgba(${r}, ${g}, ${b}, ${alpha})`;
    }

    /**
     * Fetch chart data from API
     * @param {string} page - Page identifier
     * @param {object} filters - Filter parameters
     * @returns {Promise}
     */
    async fetchChartData(page, filters = {}) {
        const params = new URLSearchParams(filters);
        const url = `/admin/chart-data/${page}?${params.toString()}`;

        try {
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();

            if (!result.success) {
                throw new Error(result.message || 'Failed to fetch chart data');
            }

            return result.data;

        } catch (error) {
            console.error('Error fetching chart data:', error);
            throw error;
        }
    }

    /**
     * Initialize charts with data from API
     * @param {string} page - Page identifier
     * @param {object} canvasIds - Object with pie, bar, line canvas IDs
     * @param {object} filters - Initial filter parameters
     */
    async initChartsFromAPI(page, canvasIds, filters = {}) {
        const { pie, bar, line } = canvasIds;

        try {
            // Show loading state
            if (pie) this.showLoading(pie + '_wrapper');
            if (bar) this.showLoading(bar + '_wrapper');
            if (line) this.showLoading(line + '_wrapper');

            // Fetch data
            const data = await this.fetchChartData(page, filters);

            // Initialize charts
            if (pie && data.pie) {
                this.initPieChart(pie, data.pie);
            }

            if (bar && data.bar) {
                this.initBarChart(bar, data.bar);
            }

            if (line && data.line) {
                this.initLineChart(line, data.line);
            }

            // Update stats if provided
            if (data.stats && data.stats.length > 0) {
                this.updateStatsCards(data.stats);
            }

            return data;

        } catch (error) {
            console.error('Error initializing charts:', error);
            
            // Show error state
            if (pie) this.showError(pie + '_wrapper');
            if (bar) this.showError(bar + '_wrapper');
            if (line) this.showError(line + '_wrapper');

            throw error;
        }
    }

    /**
     * Update all charts with new filter
     * @param {string} page - Page identifier
     * @param {object} canvasIds - Object with pie, bar, line canvas IDs
     * @param {object} filters - Filter parameters
     */
    async updateChartsFromAPI(page, canvasIds, filters = {}) {
        const { pie, bar, line } = canvasIds;

        try {
            // Fetch new data
            const data = await this.fetchChartData(page, filters);

            // Update charts
            if (pie && data.pie) {
                this.updateChart(pie, data.pie);
            }

            if (bar && data.bar) {
                this.updateChart(bar, data.bar);
            }

            if (line && data.line) {
                this.updateChart(line, data.line);
            }

            // Update stats if provided
            if (data.stats && data.stats.length > 0) {
                this.updateStatsCards(data.stats);
            }

            return data;

        } catch (error) {
            console.error('Error updating charts:', error);
            throw error;
        }
    }

    /**
     * Update stats cards with new data
     * @param {array} statsData - Array of stat objects
     */
    updateStatsCards(statsData) {
        if (!Array.isArray(statsData)) return;

        statsData.forEach((stat, index) => {
            // Find stat card by index or ID
            const cards = document.querySelectorAll('.chart-stat-card');
            const card = cards[index];
            
            if (!card) return;

            // Update value
            const valueEl = card.querySelector('.chart-stat-value');
            if (valueEl && stat.value) {
                valueEl.textContent = stat.value;
            }

            // Update trend if exists
            const trendEl = card.querySelector('.chart-trend');
            if (trendEl && stat.trend !== null && stat.trend !== undefined) {
                const trendValue = parseFloat(stat.trend);
                
                // Update classes
                trendEl.classList.remove('chart-trend-up', 'chart-trend-down', 'chart-trend-neutral');
                
                if (trendValue > 0) {
                    trendEl.classList.add('chart-trend-up');
                    trendEl.innerHTML = `<i class="fa fa-arrow-up text-xs"></i> +${Math.abs(trendValue)}%`;
                } else if (trendValue < 0) {
                    trendEl.classList.add('chart-trend-down');
                    trendEl.innerHTML = `<i class="fa fa-arrow-down text-xs"></i> ${trendValue}%`;
                } else {
                    trendEl.classList.add('chart-trend-neutral');
                    trendEl.innerHTML = `<i class="fa fa-minus text-xs"></i> 0%`;
                }
            }
        });
    }
}

// Export for global use
window.ChartManager = ChartManager;

// Auto-initialize global instance
if (typeof window !== 'undefined') {
    window.chartManager = new ChartManager();
}

