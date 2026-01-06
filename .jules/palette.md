## 2026-01-06 - Replacing Blocking Alerts
**Learning:** UX standards prioritize non-blocking inline feedback over `alert()` dialogs. `alert()` interrupts the user flow and can be disorienting, whereas inline feedback keeps the context clear.
**Action:** When performing client-side validation, always inject error messages into a dedicated ARIA-live container (e.g., `role="alert"` or `aria-live="polite"`) within the form, rather than using `alert()`.
