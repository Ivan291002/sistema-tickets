---
paths:
  - routes/web.php
---

# Routes

## Keep dashboard route inside the auth middleware group
Only ONE dashboard route exists, inside the ['auth','verified'] group, as a closure that passes ticket counts. A duplicate guest-visible dashboard route caused a 500 (auth()->user() null in the layout sidebar).
