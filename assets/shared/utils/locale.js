/**
 * Get the browser's locale from navigator.languages or navigator.language
 * 
 * @param {Object} options - Configuration options
 * @param {boolean} options.countryCodeOnly - Whether to return only the language code (e.g. 'en' instead of 'en-US')
 * @returns {string|undefined} The browser locale or undefined if not available
 */
export const getBrowserLocale = (options = {}) => {
    const defaultOptions = { countryCodeOnly: false };
    const opt = { ...defaultOptions, ...options };

    const navigatorLocale = navigator.languages !== undefined
        ? navigator.languages[0]
        : navigator.language;

    if (!navigatorLocale) {
        return undefined;
    }

    const trimmedLocale = opt.countryCodeOnly
        ? navigatorLocale.trim().split(/-|_/)[0]
        : navigatorLocale.trim();

    return trimmedLocale;
};