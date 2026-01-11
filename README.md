# Planning System

A multi-team operational planning system built with Laravel (backend) and React (frontend) that manages the lifecycle of planning requests, operational plans, and execution tracking.

## Overview

This system enables three teams to collaborate on operational planning:

- **Team A**: Creates and submits planning requests
- **Team B**: Creates operational plans with versioned resource assignments
- **Team C**: Records execution events (read-only for this implementation)

## Key Features

### Planning Requests (Team A)
- Create requests with multiple route items
- Each item includes route, capacity, start/end dates
- Submit requests for approval
- Once submitted, requests become immutable
- Must reference current active planning when creating new requests

### Operational Planning (Team B)
- Create operational plans from submitted requests
- Assign resources (vehicles, workers) with capacities
- Support temporary and permanent resource changes
- Version-based planning (no in-place updates)
- Only one active version per time range
- Full audit trail of all planning decisions

### Execution Tracking (Team C)
- Record execution events against active plans
- Read-only access to planning data

## Architecture

**Backend**: Laravel 11 with Repository and Service layer pattern
- Role-based authentication (Sanctum)
- RESTful API design
- Database transactions for data integrity
- Eager loading for optimized queries

**Frontend**: React with modern UI components
- Role-based dashboards
- Form validation and error handling
- Responsive design with Tailwind CSS

## Core Principles

1. **Immutability**: Submitted requests and historical plans cannot be modified
2. **Versioning**: All operational plans are versioned, ensuring complete history
3. **Single Active Version**: Only one plan version can be active at any time
4. **Auditability**: All actions tracked with user and timestamp information
5. **Role Separation**: Each team has specific permissions and responsibilities

## Application Routes

### Authentication
- `/login` - Login page with quick test accounts
- `/logout` - Logout action

### Team A (Planning Requests)
- `/team-a` - Team A dashboard (view all requests)
- `/team-a/requests/new` - Create new planning request
- `/team-a/requests/:id` - View/edit request details

### Team B (Operational Plans)
- `/team-b` - Team B dashboard (view all plans)
- `/team-b/plans/:id` - View plan details and manage versions

### Team C (Execution Events)
- `/team-c` - Team C dashboard (view and record execution events)

## API Endpoints

### Authentication
- `POST /api/auth/register` - Register new user
- `POST /api/auth/login` - Login
- `POST /api/auth/logout` - Logout (protected)
- `GET /api/auth/user` - Get authenticated user (protected)

### Routes & Resources (Public)
- `GET /api/routes` - List all routes
- `POST /api/routes` - Create route
- `GET /api/routes/{id}` - Get route details
- `PUT /api/routes/{id}` - Update route
- `DELETE /api/routes/{id}` - Delete route
- `GET /api/resources` - List all resources
- `POST /api/resources` - Create resource
- `GET /api/resources/{id}` - Get resource details
- `PUT /api/resources/{id}` - Update resource
- `DELETE /api/resources/{id}` - Delete resource

### Planning Requests (Team A - Protected)
- `GET /api/planning-requests` - List all requests
- `GET /api/planning-requests/submitted` - List submitted requests
- `GET /api/planning-requests/draft` - List draft requests
- `POST /api/planning-requests` - Create request
- `GET /api/planning-requests/{id}` - Get request details
- `PUT /api/planning-requests/{id}` - Update draft request
- `DELETE /api/planning-requests/{id}` - Delete draft request
- `POST /api/planning-requests/{id}/submit` - Submit request

### Operational Plans (Team B - Protected)
- `GET /api/operational-plans` - List all plans
- `GET /api/operational-plans/active` - List active plans
- `POST /api/operational-plans` - Create plan
- `GET /api/operational-plans/{id}` - Get plan details
- `POST /api/operational-plans/{id}/versions` - Create new version
- `POST /api/operational-plan-versions/{id}/activate` - Activate version

### Execution Events (Team C - Protected)
- `GET /api/execution-events` - List events
- `POST /api/execution-events` - Record event
- `GET /api/execution-events/{id}` - Get event details

## Database Structure

- `planning_requests` → `planning_request_items` (routes, capacity, dates)
- `operational_plans` → `operational_plan_versions` → `plan_version_resources`
- `execution_events` (links to active plan versions)
- `routes`, `resources`, `users`


## Test Accounts

All test accounts use password: **password**

- **Team A (Alice)**: alice@example.com - Planning Requests
- **Team B (Bob)**: bob@example.com - Operational Plans
- **Team C (Charlie)**: charlie@example.com - Execution Events

Quick login buttons are available on the login page for easy testing.

## Live Demo

Access the application at: `http://51.210.149.249:3000`