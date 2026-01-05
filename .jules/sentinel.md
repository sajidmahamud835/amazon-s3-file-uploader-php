## 2024-02-14 - Added CSRF Protection and Security Headers
**Vulnerability:** The upload form lacked Cross-Site Request Forgery (CSRF) protection, allowing attackers to potentially force users to upload files without consent. It also lacked standard security headers like `X-Frame-Options`.
**Learning:** Standalone PHP scripts often miss session-based security controls found in frameworks. Even "simple" tools can be vectors for attacks if they run in a browser context.
**Prevention:** Implement `session_start()` and token verification for all POST requests. Use `hash_equals` for timing-safe comparison. Always include `X-Frame-Options` and `X-Content-Type-Options` headers.
