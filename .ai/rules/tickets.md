---
paths:
  - 'resources/views/tickets/**'
---

# Tickets

## Ticket views use the app layout via x-layouts::app
Ticket CRUD views wrap content in <x-layouts::app :title="...">...</x-layouts::app> instead of standalone HTML. Plain-HTML ticket views previously rendered outside the app UI.
