export class AuthService {
    static isLoggedIn() {
        let loggedIn = localStorage.getItem('user');
        
        if (loggedIn === 'undefined') {
            loggedIn = null;
            localStorage.setItem('user', null);
        }
        
        return loggedIn;
    }
    
    static isPublicRoute(path) {
        const publicPages = [
            '/login', 
            '/login-link', 
            '/signup', 
            '/signup/:code', 
            '/forgot-password', 
            '/forgot-password/:code', 
            '/confirm-email/:code', 
            '/', 
            '/install', 
            '/error/stripe-invalid', 
            '/error/stripe'
        ];
        
        return publicPages.includes(path);
    }
    
    static requiresAuth(to) {
        return !this.isPublicRoute(to.matched[0].path);
    }
    
    static setRedirect(path) {
        localStorage.setItem('app_redirect', path);
    }
    
    static logout() {
        localStorage.setItem('user', null);
    }
}