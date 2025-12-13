<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    /**
     * Test specific security headers are present on a successful response.
     */
    public function test_security_headers_present_on_valid_route(): void
    {
        $response = $this->get('/');

        $response->assertSuccessful();

        $this->assertSecurityHeaders($response);
    }

    /**
     * Test CSP includes a nonce.
     */
    public function test_csp_includes_nonce(): void
    {
        $response = $this->get('/');
        
        $csp = $response->headers->get('Content-Security-Policy');
        $this->assertNotNull($csp, 'CSP header missing');
        
        // Assert nonce is present
        $this->assertMatchesRegularExpression('/nonce-[a-zA-Z0-9+\/=]+/', $csp);
        
        // Assert unsafe-inline is NOT present in script-src
        // We removed it from script-src
        // String format: script-src 'self' 'nonce-...'
        // It should NOT match "script-src ... 'unsafe-inline'"
        // But we allow it for style-src.
        
        $this->assertStringNotContainsString("script-src 'self' 'unsafe-inline'", $csp);
    }


    /**
     * Test OPTIONS method is disabled.
     */
    public function test_options_method_disabled(): void
    {
        $response = $this->call('OPTIONS', '/');
        
        $response->assertStatus(405);
    }
    
    public function test_robots_txt_exists_and_is_secure(): void
    {
        $response = $this->get('/robots.txt');
        $response->assertSuccessful();
        $response->assertSee('Disallow: /admin');
    }

    public function test_security_txt_exists(): void
    {
        $response = $this->get('/.well-known/security.txt');
        $response->assertSuccessful();
        $response->assertSee('Contact: mailto:security@malasakit.dpdns.org');
    }

    /**
     * Helper to assert security headers.
     */
    private function assertSecurityHeaders($response)
    {
        // X-Frame-Options
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');

        // X-Content-Type-Options
        $response->assertHeader('X-Content-Type-Options', 'nosniff');

        // Content-Security-Policy
        $csp = $response->headers->get('Content-Security-Policy');
        $this->assertNotNull($csp, 'Content-Security-Policy header is missing.');
        $this->assertStringContainsString("base-uri 'self'", $csp);
        $this->assertStringContainsString("object-src 'none'", $csp);
        
        // Referrer-Policy
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');

        // X-XSS-Protection
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        
        // Ensure information disclosure headers are removed
        $this->assertFalse($response->headers->has('X-Powered-By'), 'X-Powered-By header should be removed.');
        $this->assertFalse($response->headers->has('Server'), 'Server header should be removed.');
    }
}
