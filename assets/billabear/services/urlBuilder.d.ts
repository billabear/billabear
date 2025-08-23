/**
 * TypeScript definitions for URL Builder Utilities
 * 
 * Provides type safety and IntelliSense support for URL building functions.
 */

/**
 * Valid values for query parameters
 */
export type QueryParamValue = string | number | boolean;

/**
 * Object containing query parameters
 */
export interface QueryParams {
    [key: string]: QueryParamValue;
}

/**
 * Path segment type - can be string, number, or other stringifiable values
 */
export type PathSegment = string | number;

/**
 * Base URL builder class for constructing URLs with proper encoding and validation
 */
export declare class UrlBuilder {
    /**
     * Base path for the URL
     */
    readonly basePath: string;
    
    /**
     * Array of path segments
     */
    pathSegments: string[];
    
    /**
     * Map of query parameters
     */
    queryParams: Map<string, string>;

    /**
     * Create a new UrlBuilder instance
     * @param basePath - Base path for the URL
     */
    constructor(basePath?: string);

    /**
     * Add a path segment to the URL
     * @param segment - Path segment to add
     * @returns Returns this for method chaining
     */
    path(segment: PathSegment): UrlBuilder;

    /**
     * Add multiple path segments
     * @param segments - Path segments to add
     * @returns Returns this for method chaining
     */
    paths(...segments: PathSegment[]): UrlBuilder;

    /**
     * Add a query parameter
     * @param key - Parameter key
     * @param value - Parameter value
     * @returns Returns this for method chaining
     */
    query(key: string, value: QueryParamValue): UrlBuilder;

    /**
     * Add multiple query parameters from an object
     * @param params - Object containing key-value pairs
     * @returns Returns this for method chaining
     */
    queries(params: QueryParams): UrlBuilder;

    /**
     * Build the final URL string
     * @returns The constructed URL
     */
    build(): string;

    /**
     * Reset the builder to its initial state
     * @returns Returns this for method chaining
     */
    reset(): UrlBuilder;

    /**
     * Clone the current builder
     * @returns A new UrlBuilder instance with the same base path
     */
    clone(): UrlBuilder;
}

/**
 * Create a new URL builder instance
 * @param basePath - Base path for the URL
 * @returns New UrlBuilder instance
 */
export declare function createUrlBuilder(basePath?: string): UrlBuilder;

/**
 * Pre-configured URL builders for common API endpoints
 */
export declare const ApiUrlBuilder: {
    /**
     * App API endpoints
     */
    app(): UrlBuilder;
    
    /**
     * User-related endpoints
     */
    user(): UrlBuilder;
    
    /**
     * Customer-related endpoints
     */
    customer(): UrlBuilder;
    
    /**
     * Subscription-related endpoints
     */
    subscription(): UrlBuilder;
    
    /**
     * Payment-related endpoints
     */
    payment(): UrlBuilder;
    
    /**
     * Invoice-related endpoints
     */
    invoice(): UrlBuilder;
    
    /**
     * Settings-related endpoints
     */
    settings(): UrlBuilder;
    
    /**
     * Reports-related endpoints
     */
    reports(): UrlBuilder;
    
    /**
     * Integrations-related endpoints
     */
    integrations(): UrlBuilder;
    
    /**
     * Workflows-related endpoints
     */
    workflows(): UrlBuilder;
};

/**
 * Utility functions for common URL building patterns
 */
export declare const UrlUtils: {
    /**
     * Build a simple path with parameters
     * @param basePath - Base path
     * @param segments - Path segments
     * @returns Constructed URL
     */
    buildPath(basePath: string, ...segments: PathSegment[]): string;

    /**
     * Build a URL with query parameters
     * @param basePath - Base path
     * @param queryParams - Query parameters object
     * @returns Constructed URL
     */
    buildQuery(basePath: string, queryParams: QueryParams): string;

    /**
     * Build a URL with both path segments and query parameters
     * @param basePath - Base path
     * @param pathSegments - Array of path segments
     * @param queryParams - Query parameters object
     * @returns Constructed URL
     */
    buildUrl(basePath: string, pathSegments?: PathSegment[], queryParams?: QueryParams): string;

    /**
     * Validate if a string is a valid URL path segment
     * @param segment - Path segment to validate
     * @returns True if valid
     */
    isValidPathSegment(segment: string): boolean;

    /**
     * Sanitize a path segment by removing invalid characters
     * @param segment - Path segment to sanitize
     * @returns Sanitized segment
     */
    sanitizePathSegment(segment: string): string;
};

/**
 * Default export containing all URL builder utilities
 */
declare const _default: {
    UrlBuilder: typeof UrlBuilder;
    createUrlBuilder: typeof createUrlBuilder;
    ApiUrlBuilder: typeof ApiUrlBuilder;
    UrlUtils: typeof UrlUtils;
};

export default _default;