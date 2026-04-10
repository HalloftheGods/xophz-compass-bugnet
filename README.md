# Xophz Bug-Catching Net

> **Category:** Wizard's Tower · **Version:** 0.0.1

The official bug-catching and tracking engine for the COMPASS platform.

## Description

**Bug-Catching Net** leverages a dedicated WordPress Custom Post Type (`compass_bug`) to log, monitor, and resolve system issues. It acts as an integrated issue tracker with automatic runtime error interception — catching bugs before users even report them.

### Core Capabilities

- **Custom Post Type** – `compass_bug` stores Title, Description, Environment, Status, and Priority.
- **Automatic Error Interception** – Catches uncaught JS errors, unhandled Promise rejections, and Vue component lifecycle errors.
- **Fingerprint Deduplication** – Identical errors within 10 seconds are suppressed.
- **Batched Queue** – Posts flushed in batches of 5 every 2 seconds to avoid API floods.
- **Vue Dashboard** – Card-based bug tracking UI with search, filters, and inline editing.

### Bug Status Lifecycle

| Status | Description |
|---|---|
| `new` | Newly reported, awaiting triage |
| `in-progress` | Actively being worked on |
| `resolved` | Fix applied and tested |
| `closed` | Verified complete |

### Priority Levels

| Priority | Description |
|---|---|
| `low` | Minor visual inconsistencies |
| `medium` | Expected functionality issues, non-blocking |
| `high` | Severe operational failure |
| `critical` | Total system failure, data loss, or security vulnerability |

### Automatic Error Interception

The interceptor hooks into three error surfaces:

| Source | Catches |
|---|---|
| `window.addEventListener('error')` | Uncaught JS errors |
| `window.addEventListener('unhandledrejection')` | Failed async/await, unhandled Promises |
| `app.config.errorHandler` | Vue component lifecycle errors |

Auto-generated bugs use `[Auto]` title prefix, `new` status, and `high`/`critical` priority based on source.

## Requirements

- **Xophz COMPASS** parent plugin (active)
- WordPress 5.8+, PHP 7.4+

## Installation

1. Ensure **Xophz COMPASS** is installed and active.
2. Upload `xophz-compass-bugnet` to `/wp-content/plugins/`.
3. Activate through the Plugins menu.
4. Access via the COMPASS dashboard → **Bug Net**.

## Frontend Routes

| Route | View | Description |
|---|---|---|
| `/bugnet` | Dashboard | Card grid with search, status/priority filters, and "Report Bug" dialog |
| `/bugnet/:id` | Detail | Single bug view with inline status/priority editing |

## PHP Class Map

| Class | File | Purpose |
|---|---|---|
| `Xophz_Compass_Bugnet` | `class-xophz-compass-bugnet.php` | Core plugin hooks |
| `Xophz_Compass_Bugnet_CPT` | `class-xophz-compass-bugnet-cpt.php` | Custom Post Type, meta box, REST fields |

## Changelog

### 0.0.1

- Initial release with CPT-based bug tracking, automatic error interception, and Vue dashboard
