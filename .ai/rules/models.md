---
paths:
  - 'app/Models/*.php'
---

# Models

## Ticket model uses HasFactory and Spanish columns
Ticket model fillable/column names are in Spanish (titulo, descripcion, estado) with estados 'abierto'|'en_proceso'|'cerrado'. Model includes the HasFactory trait (database/factories/TicketFactory.php).
