## 2026-01-03 - [AWS SDK Version Locking]
**Learning:** Using 'version' => 'latest' in AWS SDK clients forces a runtime lookup that adds overhead.
**Action:** Always lock to a specific API version (e.g., '2006-03-01' for S3) to improve performance and stability.
