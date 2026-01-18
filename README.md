## Project Overview

**Grit Ledger** is a backend simulation of a transactional banking system. It solves a specific financial problem: **"How do we ensure money is never lost during a system failure?"**

This service implements a strict **ACID-compliant** architecture to handle funds. It guarantees **Atomicity**,meaning either the whole transfer happens, or none of it does.

---

## Architecture & Design

I followed the **Separation of Concerns** principle, to ensure each component has a single responsibility.

### The "Bank Vault" Analogy
To understand the flow, visualize the service as a physical bank operation:

1.  **The Teller (`TransferController`)**: The entry point. Validates the transfer slip (Request) and delegates the work.
2.  **The Branch Manager (`TransactionService`)**: The decision maker. Opens a transaction session, instructs the movement of funds, and signs off.
3.  **The Vault Keeper (`AccountRepository`)**: The only one allowed to touch the database (Raw SQL via PDO).
4.  **The Ledger (`TransferDTO`)**: A strict, typed object that carries transfer details safely between layers.

### System Flow Diagram

```mermaid
graph TD
    User([User / API Client]) -->|POST /transfer| Controller[Teller<br/>TransferController]

    subgraph "Application Layer (Logic)"
        Controller -->|Validates Input| Service[Manager<br/>TransactionService]
        Service -->|1. Begin Transaction| TransactionManager
    end

    subgraph "Data Layer (Persistence)"
        TransactionManager -->|Start| DB[(MySQL Database)]
        Service -->|2. Deduct Funds| Repo[Vault Keeper<br/>AccountRepository]
        Repo -->|UPDATE...| DB
        Service -->|3. Add Funds| Repo
    end

    Service -->|4. Signs Off| TransactionManager
    TransactionManager -->|End| DB
    Service -->|Result| Controller
    Controller -->|JSON Response| User
```