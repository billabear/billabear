import { describe, expect, it, beforeEach } from 'vitest';
import { 
    UrlBuilder, 
    createUrlBuilder, 
    ApiUrlBuilder, 
    UrlUtils 
} from '../urlBuilder.js';

describe('UrlBuilder', () => {
    let builder;

    beforeEach(() => {
        builder = new UrlBuilder('/api');
    });

    describe('constructor', () => {
        it('should create instance with base path', () => {
            const builder = new UrlBuilder('/api/v1');
            expect(builder.basePath).toBe('/api/v1');
        });

        it('should remove trailing slash from base path', () => {
            const builder = new UrlBuilder('/api/v1/');
            expect(builder.basePath).toBe('/api/v1');
        });

        it('should handle empty base path', () => {
            const builder = new UrlBuilder();
            expect(builder.basePath).toBe('');
        });
    });

    describe('path method', () => {
        it('should add single path segment', () => {
            const url = builder.path('users').build();
            expect(url).toBe('/api/users');
        });

        it('should add multiple path segments via chaining', () => {
            const url = builder.path('users').path('123').path('profile').build();
            expect(url).toBe('/api/users/123/profile');
        });

        it('should encode path segments', () => {
            const url = builder.path('users with spaces').build();
            expect(url).toBe('/api/users%20with%20spaces');
        });

        it('should handle special characters in path segments', () => {
            const url = builder.path('users@domain.com').build();
            expect(url).toBe('/api/users%40domain.com');
        });

        it('should ignore null, undefined, and empty segments', () => {
            const url = builder.path(null).path(undefined).path('').path('users').build();
            expect(url).toBe('/api/users');
        });

        it('should convert numbers to strings', () => {
            const url = builder.path(123).path(456).build();
            expect(url).toBe('/api/123/456');
        });

        it('should remove leading and trailing slashes from segments', () => {
            const url = builder.path('/users/').path('/123/').build();
            expect(url).toBe('/api/users/123');
        });
    });

    describe('paths method', () => {
        it('should add multiple path segments at once', () => {
            const url = builder.paths('users', '123', 'profile').build();
            expect(url).toBe('/api/users/123/profile');
        });

        it('should handle mixed types in segments', () => {
            const url = builder.paths('users', 123, 'profile').build();
            expect(url).toBe('/api/users/123/profile');
        });

        it('should ignore invalid segments', () => {
            const url = builder.paths('users', null, undefined, '', 123).build();
            expect(url).toBe('/api/users/123');
        });
    });

    describe('query method', () => {
        it('should add single query parameter', () => {
            const url = builder.query('page', 1).build();
            expect(url).toBe('/api?page=1');
        });

        it('should add multiple query parameters via chaining', () => {
            const url = builder.query('page', 1).query('limit', 10).build();
            expect(url).toBe('/api?page=1&limit=10');
        });

        it('should encode query parameter keys and values', () => {
            const url = builder.query('search term', 'value with spaces').build();
            expect(url).toBe('/api?search%20term=value%20with%20spaces');
        });

        it('should handle boolean values', () => {
            const url = builder.query('active', true).query('deleted', false).build();
            expect(url).toBe('/api?active=true&deleted=false');
        });

        it('should handle number values', () => {
            const url = builder.query('count', 42).query('price', 19.99).build();
            expect(url).toBe('/api?count=42&price=19.99');
        });

        it('should ignore null and undefined values', () => {
            const url = builder.query('valid', 'value').query('null', null).query('undefined', undefined).build();
            expect(url).toBe('/api?valid=value');
        });

        it('should ignore empty keys', () => {
            const url = builder.query('', 'value').query('valid', 'test').build();
            expect(url).toBe('/api?valid=test');
        });
    });

    describe('queries method', () => {
        it('should add multiple query parameters from object', () => {
            const params = { page: 1, limit: 10, active: true };
            const url = builder.queries(params).build();
            expect(url).toBe('/api?page=1&limit=10&active=true');
        });

        it('should handle empty object', () => {
            const url = builder.queries({}).build();
            expect(url).toBe('/api');
        });

        it('should ignore null and undefined object', () => {
            const url = builder.queries(null).queries(undefined).query('test', 'value').build();
            expect(url).toBe('/api?test=value');
        });

        it('should filter out null and undefined values', () => {
            const params = { valid: 'value', null: null, undefined: undefined, empty: '' };
            const url = builder.queries(params).build();
            expect(url).toBe('/api?valid=value&empty=');
        });
    });

    describe('build method', () => {
        it('should build URL with only base path', () => {
            const url = builder.build();
            expect(url).toBe('/api');
        });

        it('should build URL with paths and queries', () => {
            const url = builder.paths('users', 123).queries({ include: 'profile', active: true }).build();
            expect(url).toBe('/api/users/123?include=profile&active=true');
        });

        it('should handle empty base path', () => {
            const emptyBuilder = new UrlBuilder();
            const url = emptyBuilder.paths('users', 123).build();
            expect(url).toBe('/users/123');
        });
    });

    describe('reset method', () => {
        it('should reset path segments and query parameters', () => {
            builder.paths('users', 123).queries({ page: 1, limit: 10 });
            const urlBefore = builder.build();
            expect(urlBefore).toBe('/api/users/123?page=1&limit=10');

            builder.reset();
            const urlAfter = builder.build();
            expect(urlAfter).toBe('/api');
        });

        it('should return builder for chaining', () => {
            const result = builder.reset();
            expect(result).toBe(builder);
        });
    });

    describe('clone method', () => {
        it('should create independent copy', () => {
            builder.paths('users', 123).queries({ page: 1 });
            const cloned = builder.clone();
            
            // Modify original
            builder.path('profile').query('include', 'details');
            
            // Check that clone is unaffected
            expect(builder.build()).toBe('/api/users/123/profile?page=1&include=details');
            expect(cloned.build()).toBe('/api/users/123?page=1');
        });

        it('should preserve base path', () => {
            const cloned = builder.clone();
            expect(cloned.basePath).toBe(builder.basePath);
        });
    });
});

describe('createUrlBuilder', () => {
    it('should create new UrlBuilder instance', () => {
        const builder = createUrlBuilder('/api/v1');
        expect(builder).toBeInstanceOf(UrlBuilder);
        expect(builder.basePath).toBe('/api/v1');
    });

    it('should handle empty base path', () => {
        const builder = createUrlBuilder();
        expect(builder.basePath).toBe('');
    });
});

describe('ApiUrlBuilder', () => {
    it('should create app URL builder', () => {
        const builder = ApiUrlBuilder.app();
        expect(builder.basePath).toBe('/app');
        const url = builder.path('dashboard').build();
        expect(url).toBe('/app/dashboard');
    });

    it('should create user URL builder', () => {
        const builder = ApiUrlBuilder.user();
        expect(builder.basePath).toBe('/app/user');
        const url = builder.path('settings').build();
        expect(url).toBe('/app/user/settings');
    });

    it('should create customer URL builder', () => {
        const builder = ApiUrlBuilder.customer();
        expect(builder.basePath).toBe('/app/customer');
        const url = builder.path('123').build();
        expect(url).toBe('/app/customer/123');
    });

    it('should create subscription URL builder', () => {
        const builder = ApiUrlBuilder.subscription();
        expect(builder.basePath).toBe('/app/subscription');
        const url = builder.path('456').path('cancel').build();
        expect(url).toBe('/app/subscription/456/cancel');
    });

    it('should create payment URL builder', () => {
        const builder = ApiUrlBuilder.payment();
        expect(builder.basePath).toBe('/app/payment');
        const url = builder.path('789').build();
        expect(url).toBe('/app/payment/789');
    });

    it('should create invoice URL builder', () => {
        const builder = ApiUrlBuilder.invoice();
        expect(builder.basePath).toBe('/app/invoice');
        const url = builder.path('inv-123').path('download').build();
        expect(url).toBe('/app/invoice/inv-123/download');
    });

    it('should create settings URL builder', () => {
        const builder = ApiUrlBuilder.settings();
        expect(builder.basePath).toBe('/app/settings');
        const url = builder.path('billing').build();
        expect(url).toBe('/app/settings/billing');
    });

    it('should create reports URL builder', () => {
        const builder = ApiUrlBuilder.reports();
        expect(builder.basePath).toBe('/app/reports');
        const url = builder.path('revenue').query('period', 'monthly').build();
        expect(url).toBe('/app/reports/revenue?period=monthly');
    });

    it('should create integrations URL builder', () => {
        const builder = ApiUrlBuilder.integrations();
        expect(builder.basePath).toBe('/app/integrations');
        const url = builder.path('stripe').build();
        expect(url).toBe('/app/integrations/stripe');
    });

    it('should create workflows URL builder', () => {
        const builder = ApiUrlBuilder.workflows();
        expect(builder.basePath).toBe('/app/workflows');
        const url = builder.path('subscription-creation').build();
        expect(url).toBe('/app/workflows/subscription-creation');
    });
});

describe('UrlUtils', () => {
    describe('buildPath', () => {
        it('should build simple path', () => {
            const url = UrlUtils.buildPath('/api', 'users', 123);
            expect(url).toBe('/api/users/123');
        });

        it('should handle empty segments', () => {
            const url = UrlUtils.buildPath('/api', 'users', null, undefined, '', 123);
            expect(url).toBe('/api/users/123');
        });
    });

    describe('buildQuery', () => {
        it('should build URL with query parameters', () => {
            const url = UrlUtils.buildQuery('/api/users', { page: 1, limit: 10 });
            expect(url).toBe('/api/users?page=1&limit=10');
        });

        it('should handle empty query object', () => {
            const url = UrlUtils.buildQuery('/api/users', {});
            expect(url).toBe('/api/users');
        });
    });

    describe('buildUrl', () => {
        it('should build URL with paths and queries', () => {
            const url = UrlUtils.buildUrl('/api', ['users', 123], { include: 'profile' });
            expect(url).toBe('/api/users/123?include=profile');
        });

        it('should handle empty arrays and objects', () => {
            const url = UrlUtils.buildUrl('/api', [], {});
            expect(url).toBe('/api');
        });

        it('should handle default parameters', () => {
            const url = UrlUtils.buildUrl('/api');
            expect(url).toBe('/api');
        });
    });

    describe('isValidPathSegment', () => {
        it('should return true for valid segments', () => {
            expect(UrlUtils.isValidPathSegment('users')).toBe(true);
            expect(UrlUtils.isValidPathSegment('user-123')).toBe(true);
            expect(UrlUtils.isValidPathSegment('user_name')).toBe(true);
            expect(UrlUtils.isValidPathSegment('123')).toBe(true);
        });

        it('should return false for invalid segments', () => {
            expect(UrlUtils.isValidPathSegment('')).toBe(false);
            expect(UrlUtils.isValidPathSegment(null)).toBe(false);
            expect(UrlUtils.isValidPathSegment(undefined)).toBe(false);
            expect(UrlUtils.isValidPathSegment('user<script>')).toBe(false);
            expect(UrlUtils.isValidPathSegment('user"quote')).toBe(false);
            expect(UrlUtils.isValidPathSegment('user\\path')).toBe(false);
        });

        it('should return false for non-string values', () => {
            expect(UrlUtils.isValidPathSegment(123)).toBe(false);
            expect(UrlUtils.isValidPathSegment({})).toBe(false);
            expect(UrlUtils.isValidPathSegment([])).toBe(false);
        });
    });

    describe('sanitizePathSegment', () => {
        it('should remove invalid characters', () => {
            expect(UrlUtils.sanitizePathSegment('user<script>')).toBe('userscript');
            expect(UrlUtils.sanitizePathSegment('user"quote')).toBe('userquote');
            expect(UrlUtils.sanitizePathSegment('user\\path')).toBe('userpath');
        });

        it('should trim whitespace', () => {
            expect(UrlUtils.sanitizePathSegment('  user  ')).toBe('user');
        });

        it('should handle empty and invalid inputs', () => {
            expect(UrlUtils.sanitizePathSegment('')).toBe('');
            expect(UrlUtils.sanitizePathSegment(null)).toBe('');
            expect(UrlUtils.sanitizePathSegment(undefined)).toBe('');
        });

        it('should preserve valid characters', () => {
            expect(UrlUtils.sanitizePathSegment('user-123_name')).toBe('user-123_name');
        });
    });
});

describe('Integration tests', () => {
    it('should handle complex URL building scenarios', () => {
        const url = ApiUrlBuilder.customer()
            .path('search')
            .queries({
                q: 'john doe',
                status: 'active',
                page: 1,
                limit: 25,
                sort: 'created_at'
            })
            .build();
        
        expect(url).toBe('/app/customer/search?q=john%20doe&status=active&page=1&limit=25&sort=created_at');
    });

    it('should handle URL building with special characters', () => {
        const url = ApiUrlBuilder.user()
            .path('profile')
            .path('user@example.com')
            .query('redirect_url', 'https://example.com/callback?token=abc123')
            .build();
        
        expect(url).toBe('/app/user/profile/user%40example.com?redirect_url=https%3A%2F%2Fexample.com%2Fcallback%3Ftoken%3Dabc123');
    });

    it('should demonstrate real-world usage patterns', () => {
        // User authentication URLs
        const loginUrl = ApiUrlBuilder.user().path('login').build();
        expect(loginUrl).toBe('/app/user/login');

        const signupUrl = ApiUrlBuilder.user().path('signup').path('invite-code-123').build();
        expect(signupUrl).toBe('/app/user/signup/invite-code-123');

        const resetUrl = ApiUrlBuilder.user().path('reset').path('token-456').build();
        expect(resetUrl).toBe('/app/user/reset/token-456');

        // Customer management URLs
        const customerListUrl = ApiUrlBuilder.customer().queries({ 
            page: 1, 
            limit: 50, 
            status: 'active' 
        }).build();
        expect(customerListUrl).toBe('/app/customer?page=1&limit=50&status=active');

        const customerViewUrl = ApiUrlBuilder.customer().path('cust-789').build();
        expect(customerViewUrl).toBe('/app/customer/cust-789');

        // Subscription URLs
        const subscriptionCancelUrl = ApiUrlBuilder.subscription()
            .path('sub-123')
            .path('cancel')
            .query('reason', 'customer_request')
            .build();
        expect(subscriptionCancelUrl).toBe('/app/subscription/sub-123/cancel?reason=customer_request');

        // Reports URLs
        const revenueReportUrl = ApiUrlBuilder.reports()
            .path('revenue')
            .queries({
                start_date: '2023-01-01',
                end_date: '2023-12-31',
                currency: 'USD'
            })
            .build();
        expect(revenueReportUrl).toBe('/app/reports/revenue?start_date=2023-01-01&end_date=2023-12-31&currency=USD');
    });
});