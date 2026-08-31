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
                        pointStyle: 'circle',
                        generateLabels: function(chart) {
                            const data = chart.data;
                            const total = data.datasets[0]?.data.reduce((a, b) => a + b, 0) || 0;
                            return data.labels.map((label, i) => {
                                const value = data.datasets[0]?.data[i] || 0;
                                const pct = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return {
                                    text: `${label} (${pct}%)`,
                                    fillStyle: data.datasets[0]?.backgroundColor[i],
                                    strokeStyle: data.datasets[0]?.backgroundColor[i],
                                    fontColor: data.datasets[0]?.backgroundColor[i],
                                    pointStyle: 'circle',
                                    hidden: false,
                                    index: i
                                };
                            });
                        }
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
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const value = context.parsed;
                            const pct = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            const formatted = new Intl.NumberFormat('id-ID').format(value);
                            return ` ${context.label}: ${formatted} (${pct}%)`;
                        }
                    }
                },
                datalabels: false
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

        // ── Scrollable mode ──────────────────────────────────────────
        const scrollable = options.scrollable ?? false;
        const scrollInner = document.getElementById(canvasId + '_scrollInner');
        const scrollOuter = document.getElementById(canvasId + '_scrollOuter');

        const processedData = this.processChartData(data, 'bar');
        const labelCount = processedData.labels?.length ?? 0;

        if (scrollable && labelCount > 0 && scrollInner && scrollOuter) {
            const barWidth   = 37;
            const minWidth   = scrollOuter.clientWidth;
            const canvasWidth = Math.max(labelCount * barWidth, minWidth);

            scrollInner.style.width  = canvasWidth + 'px';
            ctx.style.width          = canvasWidth + 'px';
            ctx.width                = canvasWidth;
            scrollOuter.classList.add('chart-scroll-active');
        } else {
            // Reset ke normal saat bukan custom
            if (scrollInner) scrollInner.style.width = '100%';
            if (scrollOuter) scrollOuter.classList.remove('chart-scroll-active');
            ctx.style.width = '100%';
        }
        // ─────────────────────────────────────────────────────────────

        const defaultOptions = {
            responsive: !scrollable,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)',
                        drawBorder: false
                    },
                    ticks: {
                        maxTicksLimit: 10,
                        font: {
                            size: 11,
                            family: "'Plus Jakarta Sans', sans-serif"
                        },
                        callback: function(value) {
                            // Format large numbers
                            if (value >= 1000000) {
                                                            return (value / 1000000).toFixed(1) + ' JT';
                                                        } else if (value >= 1000) {
                                                            return (value / 1000).toFixed(0) + ' RB';
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

        // ── Accent Line (overlay line pada dataset tertentu) ─────────
        // Cabut flag dari options sebelum di-merge agar tidak konflik
        const showAccentLine  = options.accentLine ?? false;
        const accentLineIndex = options.accentLineIndex ?? 0; // default dataset[0]
        if ('accentLine'      in options) delete options.accentLine;
        if ('accentLineIndex' in options) delete options.accentLineIndex;

        // Jika accentLine aktif, duplikasi dataset[accentLineIndex] sebagai overlay line
        if (showAccentLine && processedData.datasets && processedData.datasets.length > 0) {
            const targetIndex = Math.min(accentLineIndex, processedData.datasets.length - 1);
            const src         = processedData.datasets[targetIndex];
            const color       = '#ef4444'; // merah

            const overlayLine = {
                type                 : 'line',
                label                : src.label + ' (trend)',
                data                 : [...src.data],
                borderColor          : color,
                backgroundColor      : 'transparent',
                borderWidth          : 2.5,
                pointRadius          : 3,
                pointBackgroundColor : color,
                pointBorderColor     : '#fff',
                pointBorderWidth     : 1.5,
                tension              : 0.4,
                fill                 : false,
                order                : 0,  // render di atas bar
            };

            processedData.datasets.push(overlayLine);
        }
        // ─────────────────────────────────────────────────────────────

        const mergedOptions = this.deepMerge(defaultOptions, options);

        this.charts[canvasId] = new Chart(ctx, {
            type: 'bar',
            data: processedData,
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

        // ── Scrollable mode (sama seperti bar) ───────────────────────────────
        const scrollable  = options.scrollable ?? false;
        const scrollInner = document.getElementById(canvasId + '_scrollInner');
        const scrollOuter = document.getElementById(canvasId + '_scrollOuter');

        const processedData = this.processChartData(data, 'line');
        const labelCount    = processedData.labels?.length ?? 0;

        if (scrollable && labelCount > 0 && scrollInner && scrollOuter) {
            const pointWidth  = 40;
            const minWidth    = scrollOuter.clientWidth;
            const canvasWidth = Math.max(labelCount * pointWidth, minWidth);

            scrollInner.style.width = canvasWidth + 'px';
            ctx.style.width         = canvasWidth + 'px';
            ctx.width               = canvasWidth;
            scrollOuter.classList.add('chart-scroll-active');
        } else {
            if (scrollInner) scrollInner.style.width = '100%';
            if (scrollOuter) scrollOuter.classList.remove('chart-scroll-active');
            ctx.style.width = '100%';
        }
        // ─────────────────────────────────────────────────────────────────────

        const defaultOptions = {
            responsive: !scrollable,
            maintainAspectRatio: false,
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
                            if (value >= 1000000) {
                                return 'Rp ' + (value / 1000000).toFixed(1) + ' JT';
                            } else if (value >= 1000) {
                                return 'Rp ' + (value / 1000).toFixed(0) + ' RB';
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
                            if (label) label += ': ';
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
                    tension: 0.4,
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
            data: processedData,
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
                    // Use colors array from data if provided (e.g. grouped bar)
                    const palette = (data.colors && data.colors[index]) ? data.colors[index] : this.colorPalette[index % this.colorPalette.length];
                    processedDataset.backgroundColor = this.hexToRgba(palette, 0.85);
                    processedDataset.borderColor = palette;
                    processedDataset.borderWidth = 1;
                    processedDataset.borderRadius = 5;
                    processedDataset.borderSkipped = false;
                }
            }

            processedData.datasets.push(processedDataset);
        });

        return processedData;
    }

    /**
     * Show loading state for chart — overlay di atas canvas, TIDAK mengganti innerHTML
     * @param {string} containerId - Canvas ID atau wrapper ID
     */
    showLoading(containerId) {
        // Hapus overlay lama jika ada
        this._removeOverlay(containerId);

        // Cari wrapper: canvas-wrapper atau parent canvas
        const canvas = document.getElementById(containerId);
        const wrapper = canvas
            ? (canvas.closest('.chart-canvas-wrapper') || canvas.parentElement)
            : document.getElementById(containerId + '_wrapper');

        if (!wrapper) return;

        // Pastikan wrapper punya position: relative agar overlay bisa absolute
        if (getComputedStyle(wrapper).position === 'static') {
            wrapper.style.position = 'relative';
        }

        const overlay = document.createElement('div');
        overlay.id   = 'chart-overlay-' + containerId;
        overlay.style.cssText = [
            'position:absolute', 'inset:0', 'z-index:10',
            'display:flex', 'align-items:center', 'justify-content:center',
            'background:rgba(255,255,255,0.85)', 'border-radius:0.75rem',
            'pointer-events:none'
        ].join(';');
        overlay.innerHTML = `
            <div style="text-align:center">
                <div style="display:inline-block;width:2rem;height:2rem;border:3px solid #e5e7eb;border-top-color:#4f6ef7;border-radius:50%;animation:chart-spin 0.8s linear infinite"></div>
                <p style="font-size:0.8rem;color:#6b7280;margin-top:0.5rem">Memuat grafik...</p>
            </div>`;

        wrapper.appendChild(overlay);
    }

    /**
     * Show error state for chart — overlay di atas canvas, TIDAK mengganti innerHTML
     * @param {string} containerId - Canvas ID atau wrapper ID
     * @param {string} message - Error message
     */
    showError(containerId, message = 'Gagal memuat data grafik') {
        this._removeOverlay(containerId);

        const canvas = document.getElementById(containerId);
        const wrapper = canvas
            ? (canvas.closest('.chart-canvas-wrapper') || canvas.parentElement)
            : document.getElementById(containerId + '_wrapper');

        if (!wrapper) return;

        if (getComputedStyle(wrapper).position === 'static') {
            wrapper.style.position = 'relative';
        }

        const overlay = document.createElement('div');
        overlay.id   = 'chart-overlay-' + containerId;
        overlay.style.cssText = [
            'position:absolute', 'inset:0', 'z-index:10',
            'display:flex', 'align-items:center', 'justify-content:center',
            'background:rgba(255,255,255,0.85)', 'border-radius:0.75rem'
        ].join(';');
        overlay.innerHTML = `
            <div style="text-align:center">
                <i class="fa fa-exclamation-triangle" style="color:#ef4444;font-size:1.75rem"></i>
                <p style="font-size:0.8rem;color:#6b7280;margin-top:0.5rem">${message}</p>
            </div>`;

        wrapper.appendChild(overlay);
    }

    /**
     * Hapus overlay loading/error untuk canvas tertentu
     * @param {string} containerId
     */
    _removeOverlay(containerId) {
        const existing = document.getElementById('chart-overlay-' + containerId);
        if (existing) existing.remove();
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
     * @param {object} barOptions - Extra options for bar chart
     * @param {object} lineOptions - Extra options for line chart
     */
    async initChartsFromAPI(page, canvasIds, filters = {}, barOptions = {}, lineOptions = {}) {
        const { pie, bar, line } = canvasIds;

        try {
            // Show loading overlay (di atas canvas, tidak merusak DOM)
            if (pie) this.showLoading(pie);
            if (bar) this.showLoading(bar);
            if (line) this.showLoading(line);

            // Fetch data
            const data = await this.fetchChartData(page, filters);

            // Initialize charts, hapus overlay setelah selesai
            if (pie && data.pie) {
                this._removeOverlay(pie);
                this.initPieChart(pie, data.pie);
            } else if (pie) {
                this._removeOverlay(pie);
            }

            if (bar && data.bar) {
                this._removeOverlay(bar);
                this.initBarChart(bar, data.bar, barOptions);
            } else if (bar) {
                this._removeOverlay(bar);
            }

            if (line && data.line) {
                this._removeOverlay(line);
                this.initLineChart(line, data.line, lineOptions);
            } else if (line) {
                this._removeOverlay(line);
            }

            // Update stats if provided
            if (data.stats && data.stats.length > 0) {
                this.updateStatsCards(data.stats);
            }

            return data;

        } catch (error) {
            console.error('Error initializing charts:', error);

            // Tampilkan error overlay
            if (pie) this.showError(pie);
            if (bar) this.showError(bar);
            if (line) this.showError(line);

            throw error;
        }
    }

    /**
     * Update all charts with new filter
     * @param {string} page - Page identifier
     * @param {object} canvasIds - Object with pie, bar, line canvas IDs
     * @param {object} filters - Filter parameters
     * @param {object} barOptions - Extra options for bar chart
     * @param {object} lineOptions - Extra options for line chart
     */
    async updateChartsFromAPI(page, canvasIds, filters = {}, barOptions = {}, lineOptions = {}) {
        const { pie, bar, line } = canvasIds;

        try {
            // Show loading overlay saat update
            if (pie) this.showLoading(pie);
            if (bar) this.showLoading(bar);
            if (line) this.showLoading(line);

            // Fetch new data
            const data = await this.fetchChartData(page, filters);

            // Destroy dan re-init agar label/kolom ikut update
            if (pie && data.pie) {
                this._removeOverlay(pie);
                this.destroyChart(pie);
                this.initPieChart(pie, data.pie);
            } else if (pie) {
                this._removeOverlay(pie);
            }

            if (bar && data.bar) {
                this._removeOverlay(bar);
                this.destroyChart(bar);
                this.initBarChart(bar, data.bar, barOptions);
            } else if (bar) {
                this._removeOverlay(bar);
            }

            if (line && data.line) {
                this._removeOverlay(line);
                this.destroyChart(line);
                this.initLineChart(line, data.line, lineOptions);
            } else if (line) {
                this._removeOverlay(line);
            }

            // Update stats if provided
            if (data.stats && data.stats.length > 0) {
                this.updateStatsCards(data.stats);
            }

            return data;

        } catch (error) {
            console.error('Error updating charts:', error);
            if (pie) this.showError(pie);
            if (bar) this.showError(bar);
            if (line) this.showError(line);
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

