# Tugu Backend API

RESTful backend API for the Tugu Transaction System.
The backend provides JWT-based authentication and transaction management through a structured layered architecture.

---

## Tech Stack

- Laravel 12
- PHP
- PostgreSQL
- JWT Authentication
- Eloquent ORM
- Postman
- DBeaver

---

## Architecture

The backend follows a layered architecture:

```text
Controller
    ↓
Service
    ↓
Repository Interface
    ↓
Repository
    ↓
Model
    ↓
PostgreSQL

Controller

Handles HTTP requests, validation, and API responses.

Service

Contains the application's business logic.

Repository Interface

Defines the contract that repository implementations must follow.

Repository

Handles database-related operations.

Model

Uses Laravel Eloquent to interact with the database.