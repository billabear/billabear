/**
 * Service for managing localStorage operations
 * Centralizes localStorage access and provides error handling
 */
export class LocalStorageService {
    /**
     * Get item from localStorage
     * @param {string} key - The key to retrieve
     * @returns {*} - The parsed value or null if not found/invalid
     */
    static getItem(key) {
        try {
            const rawData = localStorage.getItem(key);
            if (rawData === null) {
                return null;
            }
            return JSON.parse(rawData);
        } catch (error) {
            console.warn(`Error parsing localStorage item "${key}":`, error);
            return null;
        }
    }

    /**
     * Set item in localStorage
     * @param {string} key - The key to store
     * @param {*} value - The value to store (will be JSON stringified)
     */
    static setItem(key, value) {
        try {
            localStorage.setItem(key, JSON.stringify(value));
        } catch (error) {
            console.error(`Error setting localStorage item "${key}":`, error);
        }
    }

    /**
     * Remove item from localStorage
     * @param {string} key - The key to remove
     */
    static removeItem(key) {
        try {
            localStorage.removeItem(key);
        } catch (error) {
            console.error(`Error removing localStorage item "${key}":`, error);
        }
    }

    /**
     * Check if a key exists in localStorage
     * @param {string} key - The key to check
     * @returns {boolean} - True if key exists, false otherwise
     */
    static hasItem(key) {
        return localStorage.getItem(key) !== null;
    }

    /**
     * Clear all localStorage items
     */
    static clear() {
        try {
            localStorage.clear();
        } catch (error) {
            console.error('Error clearing localStorage:', error);
        }
    }

    /**
     * Get user data from localStorage
     * @returns {Object|null} - User data or null if not found
     */
    static getUserData() {
        return this.getItem('user');
    }

    /**
     * Set user data in localStorage
     * @param {Object} userData - User data to store
     */
    static setUserData(userData) {
        this.setItem('user', userData);
    }

    /**
     * Remove user data from localStorage
     */
    static removeUserData() {
        this.removeItem('user');
    }

    /**
     * Get app redirect URL from localStorage
     * @returns {string|null} - Redirect URL or null if not found
     */
    static getAppRedirect() {
        return localStorage.getItem('app_redirect');
    }

    /**
     * Set app redirect URL in localStorage
     * @param {string} url - Redirect URL to store
     */
    static setAppRedirect(url) {
        localStorage.setItem('app_redirect', url);
    }

    /**
     * Remove app redirect URL from localStorage
     */
    static removeAppRedirect() {
        this.removeItem('app_redirect');
    }
}