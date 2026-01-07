## 2026-02-14 - [CSRF Protection in Single-File Scripts]
**Vulnerability:** Standalone PHP scripts handling forms are often vulnerable to CSRF because they lack session integration by default.
**Learning:** Even simple utility scripts must initialize sessions and verify CSRF tokens to prevent unauthorized submissions from malicious sites.
**Prevention:** Implement `session_start()`, generate tokens, and verify using `hash_equals()` before processing POST requests.
