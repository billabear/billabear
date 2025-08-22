/**
 * URL Builder Utilities
 * 
 * Provides consistent URL construction patterns for the BillaBear application.
 * Handles query parameters, path building, and ensures proper URL encoding.
 */

/**
 * Base URL builder class for constructing URLs with proper encoding and validation
 */
export class UrlBuilder {
    constructor(basePath = '') {
        this.basePath = basePath.replace(/\/$/, ''); // Remove trailing slash
        this.pathSegments = [];
        this.queryParams = new Map();
    }

    /**
     * Add a path segment to the URL
     * @param {string} segment - Path segment to add
     * @returns {UrlBuilder} - Returns this for method chaining
     */
    path(segment) {
        if (segment !== null && segment !== undefined && segment !== '') {
            // Remove leading/trailing slashes first, then encode the segment
            const cleanedSegment = String(segment).replace(/^\/+|\/+$/g, '');
            if (cleanedSegment) {
                const encodedSegment = encodeURIComponent(cleanedSegment);
                this.pathSegments.push(encodedSegment);
            }
        }
        return this;
    }

    /**
     * Add multiple path segments
     * @param {...string} segments - Path segments to add
     * @returns {UrlBuilder} - Returns this for method chaining
     */
    paths(...segments) {
        segments.forEach(segment => this.path(segment));
        return this;
    }

    /**
     * Add a query parameter
     * @param {string} key - Parameter key
     * @param {string|number|boolean} value - Parameter value
     * @returns {UrlBuilder} - Returns this for method chaining
     */
    query(key, value) {
        if (key && value !== null && value !== undefined) {
            this.queryParams.set(key, String(value));
        }
        return this;
    }

    /**
     * Add multiple query parameters from an object
     * @param {Object} params - Object containing key-value pairs
     * @returns {UrlBuilder} - Returns this for method chaining
     */
    queries(params) {
        if (params && typeof params === 'object') {
            Object.entries(params).forEach(([key, value]) => {
                this.query(key, value);
            });
        }
        return this;
    }

    /**
     * Build the final URL string
     * @returns {string} - The constructed URL
     */
    build() {
        let url = this.basePath;
        
        // Add path segments
        if (this.pathSegments.length > 0) {
            url += '/' + this.pathSegments.join('/');
        }
        
        // Add query parameters
        if (this.queryParams.size > 0) {
            const queryString = Array.from(this.queryParams.entries())
                .map(([key, value]) => `${encodeURIComponent(key)}=${encodeURIComponent(value)}`)
                .join('&');
            url += '?' + queryString;
        }
        
        return url;
    }

    /**
     * Reset the builder to its initial state
     * @returns {UrlBuilder} - Returns this for method chaining
     */
    reset() {
        this.pathSegments = [];
        this.queryParams.clear();
        return this;
    }

    /**
     * Clone the current builder
     * @returns {UrlBuilder} - A new UrlBuilder instance with the same base path
     */
    clone() {
        const cloned = new UrlBuilder(this.basePath);
        cloned.pathSegments = [...this.pathSegments];
        cloned.queryParams = new Map(this.queryParams);
        return cloned;
    }
}

/**
 * Create a new URL builder instance
 * @param {string} basePath - Base path for the URL
 * @returns {UrlBuilder} - New UrlBuilder instance
 */
export const createUrlBuilder = (basePath = '') => {
    return new UrlBuilder(basePath);
};

/**
 * Pre-configured URL builders for common API endpoints
 */
export const ApiUrlBuilder = {
    /**
     * App API endpoints
     */
    app: () => createUrlBuilder('/app'),
    
    /**
     * User-related endpoints
     */
    user: () => createUrlBuilder('/app/user'),
    
    /**
     * Customer-related endpoints
     */
    customer: () => createUrlBuilder('/app/customer'),
    
    /**
     * Subscription-related endpoints
     */
    subscription: () => createUrlBuilder('/app/subscription'),
    
    /**
     * Payment-related endpoints
     */
    payment: () => createUrlBuilder('/app/payment'),
    
    /**
     * Invoice-related endpoints
     */
    invoice: () => createUrlBuilder('/app/invoice'),
    
    /**
     * Settings-related endpoints
     */
    settings: () => createUrlBuilder('/app/settings'),
    
    /**
     * Reports-related endpoints
     */
    reports: () => createUrlBuilder('/app/reports'),
    
    /**
     * Integrations-related endpoints
     */
    integrations: () => createUrlBuilder('/app/integrations'),
    
    /**
     * Workflows-related endpoints
     */
    workflows: () => createUrlBuilder('/app/workflows'),
};

/**
 * Utility functions for common URL building patterns
 */
export const UrlUtils = {
    /**
     * Build a simple path with parameters
     * @param {string} basePath - Base path
     * @param {...string} segments - Path segments
     * @returns {string} - Constructed URL
     */
    buildPath: (basePath, ...segments) => {
        return createUrlBuilder(basePath).paths(...segments).build();
    },

    /**
     * Build a URL with query parameters
     * @param {string} basePath - Base path
     * @param {Object} queryParams - Query parameters object
     * @returns {string} - Constructed URL
     */
    buildQuery: (basePath, queryParams) => {
        return createUrlBuilder(basePath).queries(queryParams).build();
    },

    /**
     * Build a URL with both path segments and query parameters
     * @param {string} basePath - Base path
     * @param {Array} pathSegments - Array of path segments
     * @param {Object} queryParams - Query parameters object
     * @returns {string} - Constructed URL
     */
    buildUrl: (basePath, pathSegments = [], queryParams = {}) => {
        return createUrlBuilder(basePath)
            .paths(...pathSegments)
            .queries(queryParams)
            .build();
    },

    /**
     * Validate if a string is a valid URL path segment
     * @param {string} segment - Path segment to validate
     * @returns {boolean} - True if valid
     */
    isValidPathSegment: (segment) => {
        if (!segment || typeof segment !== 'string') return false;
        // Check for invalid characters that shouldn't be in path segments
        const invalidChars = /[<>:"\\|?*\x00-\x1f]/;
        return !invalidChars.test(segment);
    },

    /**
     * Sanitize a path segment by removing invalid characters
     * @param {string} segment - Path segment to sanitize
     * @returns {string} - Sanitized segment
     */
    sanitizePathSegment: (segment) => {
        if (!segment || typeof segment !== 'string') return '';
        return segment.replace(/[<>:"\\|?*\x00-\x1f]/g, '').trim();
    }
};

// Default export for convenience
export default {
    UrlBuilder,
    createUrlBuilder,
    ApiUrlBuilder,
    UrlUtils
};