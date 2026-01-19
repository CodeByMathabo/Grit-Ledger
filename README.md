# Grit Ledger

**Grit Ledger** is a backend simulation of a transactional banking system. It solves a specific financial problem: **"How do we ensure money is never lost during a system failure?"**

This service implements a strict **ACID-compliant** architecture to handle funds. It guarantees **Atomicity**, meaning either the whole transfer happens, or none of it does.

## How to Run

Follow these commands to get the server running.

1.  **Clone the repository:**
    ```bash
    git clone [https://github.com/your-username/grit-ledger.git](https://github.com/your-username/grit-ledger.git)
    cd grit-ledger
    ```

2.  **Configure the database:**
    Import the `schema.sql` file into your MySQL database to create the necessary tables. Update your database credentials in the configuration file.

3.  **Start the PHP server:**
    Run this command in your terminal. It listens on port 8000.
    ```bash
    php -S localhost:8000
    ```

4.  **Test the API:**
    Use tools like Postman or cURL to send requests to `http://localhost:8000/transfer`.

## Technical Decisions

I made these technical choices to ensure reliability and maintainability.

* **Strict Typing (PHP 8.2):** I defined all function arguments and return types explicitly to enforce type safety.
* **PDO (PHP Data Objects):** I used PDO for database interactions to support named parameters and database flexibility.
* **Dependency Injection:** I manually injected dependencies to improve testability and demonstrate Inversion of Control without heavy frameworks.
* **Custom Exception Handling:** I implemented specific exceptions like `InsufficientFundsException` to return meaningful HTTP status codes.

## Implementation Plan

This is how I built the system:

* **Step 1:** Created the Account model and SQL schema.
* **Step 2:** Built the Repository to handle database operations using PDO.
* **Step 3:** Implemented the Service logic with transaction management (begin, signs off, rollback).
* **Step 4:** Developed the Controller to handle HTTP requests and responses.


## Architecture & Design

I followed the **Separation of Concerns** principle to ensure each component has a single responsibility.

### The "Bank Vault" Analogy
To understand the flow, visualize the service as a physical bank operation:

1.  **The Teller (`TransferController`):** The entry point. Validates the transfer slip (Request) and delegates the work.
2.  **The Branch Manager (`TransactionService`):** The decision maker. Opens a transaction session, instructs the movement of funds, and signs off.
3.  **The Vault Keeper (`AccountRepository`):** The only one allowed to touch the database (Raw SQL via PDO).
4.  **The Ledger (`TransferDTO`):** A strict, typed object that carries transfer details safely between layers.

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