/**
 * Skeleton Loader Component
 * Provides loading placeholders for better perceived performance
 */

class SkeletonLoader {
    constructor() {
        this.injectStyles();
    }

    injectStyles() {
        if (document.getElementById('skeleton-styles')) return;

        const style = document.createElement('style');
        style.id = 'skeleton-styles';
        style.textContent = `
            /* Base Skeleton Styles */
            .skeleton {
                background: linear-gradient(
                    90deg,
                    #f0f0f0 0%,
                    #e0e0e0 20%,
                    #f0f0f0 40%,
                    #f0f0f0 100%
                );
                background-size: 200% 100%;
                animation: skeleton-loading 1.5s ease-in-out infinite;
                border-radius: 4px;
                display: inline-block;
            }

            body.bg-dark .skeleton {
                background: linear-gradient(
                    90deg,
                    #2a2f35 0%,
                    #343a40 20%,
                    #2a2f35 40%,
                    #2a2f35 100%
                );
                background-size: 200% 100%;
            }

            @keyframes skeleton-loading {
                0% {
                    background-position: 200% 0;
                }
                100% {
                    background-position: -200% 0;
                }
            }

            /* Skeleton Variants */
            .skeleton-text {
                height: 1em;
                margin-bottom: 0.5em;
                width: 100%;
            }

            .skeleton-text.short {
                width: 60%;
            }

            .skeleton-text.medium {
                width: 80%;
            }

            .skeleton-title {
                height: 1.5em;
                width: 40%;
                margin-bottom: 1em;
            }

            .skeleton-avatar {
                width: 40px;
                height: 40px;
                border-radius: 50%;
            }

            .skeleton-avatar.large {
                width: 60px;
                height: 60px;
            }

            .skeleton-button {
                height: 40px;
                width: 120px;
                border-radius: 8px;
            }

            .skeleton-card {
                background: white;
                border-radius: 12px;
                padding: 1.5rem;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
            }

            body.bg-dark .skeleton-card {
                background: #1e2124;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
            }

            .skeleton-metric {
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
            }

            .skeleton-metric-number {
                height: 2.5rem;
                width: 80px;
            }

            .skeleton-metric-label {
                height: 1rem;
                width: 120px;
            }

            /* Table Skeleton */
            .skeleton-table {
                width: 100%;
            }

            .skeleton-table-row {
                display: flex;
                gap: 1rem;
                padding: 1rem;
                border-bottom: 1px solid #f1f3f4;
            }

            body.bg-dark .skeleton-table-row {
                border-bottom-color: #2a2f35;
            }

            .skeleton-table-cell {
                flex: 1;
                height: 1.2em;
            }

            /* List Skeleton */
            .skeleton-list-item {
                display: flex;
                align-items: center;
                gap: 1rem;
                padding: 1rem;
                margin-bottom: 0.5rem;
            }

            .skeleton-list-content {
                flex: 1;
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
            }

            /* Chart Skeleton */
            .skeleton-chart {
                height: 300px;
                width: 100%;
                border-radius: 8px;
            }

            /* Pulse Animation (alternative to shimmer) */
            .skeleton-pulse {
                animation: skeleton-pulse 1.5s ease-in-out infinite;
            }

            @keyframes skeleton-pulse {
                0%, 100% {
                    opacity: 1;
                }
                50% {
                    opacity: 0.5;
                }
            }
        `;
        document.head.appendChild(style);
    }

    // Create metric card skeleton
    createMetricSkeleton() {
        return `
            <div class="skeleton-card">
                <div class="skeleton-metric">
                    <div class="skeleton skeleton-metric-label"></div>
                    <div class="skeleton skeleton-metric-number"></div>
                    <div class="skeleton skeleton-text short"></div>
                </div>
            </div>
        `;
    }

    // Create table skeleton
    createTableSkeleton(rows = 5, columns = 4) {
        let html = '<div class="skeleton-table">';
        for (let i = 0; i < rows; i++) {
            html += '<div class="skeleton-table-row">';
            for (let j = 0; j < columns; j++) {
                html += '<div class="skeleton skeleton-table-cell"></div>';
            }
            html += '</div>';
        }
        html += '</div>';
        return html;
    }

    // Create list skeleton
    createListSkeleton(items = 3) {
        let html = '<div class="skeleton-list">';
        for (let i = 0; i < items; i++) {
            html += `
                <div class="skeleton-list-item">
                    <div class="skeleton skeleton-avatar"></div>
                    <div class="skeleton-list-content">
                        <div class="skeleton skeleton-text medium"></div>
                        <div class="skeleton skeleton-text short"></div>
                    </div>
                </div>
            `;
        }
        html += '</div>';
        return html;
    }

    // Create card skeleton
    createCardSkeleton() {
        return `
            <div class="skeleton-card">
                <div class="skeleton skeleton-title"></div>
                <div class="skeleton skeleton-text"></div>
                <div class="skeleton skeleton-text medium"></div>
                <div class="skeleton skeleton-text short"></div>
            </div>
        `;
    }

    // Create chart skeleton
    createChartSkeleton() {
        return '<div class="skeleton skeleton-chart"></div>';
    }

    // Show skeleton in element
    show(element, type = 'card', options = {}) {
        if (typeof element === 'string') {
            element = document.querySelector(element);
        }

        if (!element) return;

        // Store original content
        element.dataset.originalContent = element.innerHTML;
        element.classList.add('skeleton-loading');

        // Insert skeleton based on type
        switch (type) {
            case 'metric':
                element.innerHTML = this.createMetricSkeleton();
                break;
            case 'table':
                element.innerHTML = this.createTableSkeleton(
                    options.rows || 5,
                    options.columns || 4
                );
                break;
            case 'list':
                element.innerHTML = this.createListSkeleton(options.items || 3);
                break;
            case 'chart':
                element.innerHTML = this.createChartSkeleton();
                break;
            case 'card':
            default:
                element.innerHTML = this.createCardSkeleton();
                break;
        }
    }

    // Hide skeleton and restore content
    hide(element) {
        if (typeof element === 'string') {
            element = document.querySelector(element);
        }

        if (!element) return;

        if (element.dataset.originalContent) {
            element.innerHTML = element.dataset.originalContent;
            delete element.dataset.originalContent;
        }

        element.classList.remove('skeleton-loading');
    }

    // Wrap async operation with skeleton
    async wrap(element, asyncFn, type = 'card', options = {}) {
        this.show(element, type, options);
        try {
            const result = await asyncFn();
            return result;
        } finally {
            this.hide(element);
        }
    }
}

// Create global instance
window.skeleton = new SkeletonLoader();

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = SkeletonLoader;
}
