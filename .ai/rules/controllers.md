---
paths:
  - 'app/Http/Controllers/**'
---

# Controllers

## Base Controller must use AuthorizesRequests
app/Http/Controllers/Controller.php uses the AuthorizesRequests trait; without it, $this->authorize() raises "Call to undefined method". Kernel/HTTP controllers depend on this via spatie gate permissions.
