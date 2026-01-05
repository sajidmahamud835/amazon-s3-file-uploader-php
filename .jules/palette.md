## 2026-01-05 - Replacing Blocking Alerts with Inline Validation
**Learning:** Users perceive blocking `alert()` dialogs as aggressive and disruptive. Replacing them with `aria-live` regions and inline validation provides a smoother, more accessible experience.
**Action:** When validating forms, always use inline error containers with `role="alert"` and `aria-live="assertive"` instead of `window.alert()`. Validate on `change` events for immediate feedback.
