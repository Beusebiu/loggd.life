# Laravel MCP Setup & Reference

## Overview

Laravel MCP (Model Context Protocol) allows AI assistants to interact with your Laravel application through a standardized protocol. This document covers the installation, available tools, and test results for the LOGGD project.

---

## Installation Status

✅ **Laravel Boost** - v1.7+ (MCP Server)
✅ **Laravel MCP** - v0.3.2 (Framework)
✅ **Laravel Sail** - v1.47.0 (Docker)
✅ **PHP** - 8.4.14
✅ **Laravel** - 12.37.0
✅ **Database** - MySQL (via Docker)

---

## Installation Steps (Completed)

1. **Install Laravel Boost**
   ```bash
   composer require laravel/boost --dev
   ```

2. **Install MCP Server**
   ```bash
   php artisan boost:install
   ```
   This creates:
   - AI guidelines in `.ai/` directory (if supported)
   - MCP server configuration for Claude Code
   - Auto-registers with your IDE

3. **Start Docker Environment**
   ```bash
   ./vendor/bin/sail up -d
   ```

4. **Run Migrations**
   ```bash
   ./vendor/bin/sail artisan migrate
   ```

---

## How to Use MCP Server

### Starting the Server Manually (Testing)
```bash
./vendor/bin/sail artisan boost:mcp
```

### Testing Tools via Command Line
```bash
# Initialize connection
echo '{"jsonrpc": "2.0", "id": 1, "method": "initialize", "params": {"protocolVersion": "2024-11-05", "capabilities": {}, "clientInfo": {"name": "test", "version": "1.0"}}}' | ./vendor/bin/sail artisan boost:mcp

# List available tools
echo '{"jsonrpc": "2.0", "id": 2, "method": "tools/list"}' | ./vendor/bin/sail artisan boost:mcp

# Call a tool (example: application-info)
echo '{"jsonrpc": "2.0", "id": 3, "method": "tools/call", "params": {"name": "application-info", "arguments": {}}}' | ./vendor/bin/sail artisan boost:mcp
```

---

## Available MCP Tools (16 Total)

### 🗄️ Database Tools

#### 1. **database-schema**
- **Description:** Read complete database schema with tables, columns, indexes, foreign keys
- **Usage:** No arguments required
- **Returns:** Full schema in JSON format

#### 2. **database-query**
- **Description:** Execute read-only SQL queries
- **Arguments:**
  - `query` (required): SQL SELECT statement
  - `database` (optional): Connection name
- **Note:** Only SELECT, SHOW, EXPLAIN, DESCRIBE allowed

#### 3. **database-connections**
- **Description:** List all configured database connections
- **Usage:** No arguments required

---

### 📦 Application Information

#### 4. **application-info**
- **Description:** Get comprehensive app information
- **Returns:**
  - PHP version
  - Laravel version
  - Database engine
  - All installed packages with versions
  - All Eloquent models
- **Usage:** Call this at the start of each session

#### 5. **get-config**
- **Description:** Get specific config value using dot notation
- **Arguments:**
  - `key` (required): Config key (e.g., "app.name", "database.default")
- **Example:** `{"key": "app.name"}`

#### 6. **list-available-config-keys**
- **Description:** List all available config keys from config/*.php files
- **Usage:** No arguments required

#### 7. **list-available-env-vars**
- **Description:** List environment variable names from .env file
- **Arguments:**
  - `filename` (optional): .env file to read (defaults to .env)

---

### 🛣️ Routing & Commands

#### 8. **list-routes**
- **Description:** List all application routes
- **Arguments (all optional):**
  - `method`: Filter by HTTP method (GET, POST, etc.)
  - `action`: Filter by controller action
  - `name`: Filter by route name
  - `domain`: Filter by domain
  - `path`: Show routes matching path pattern
  - `except_path`: Exclude routes matching path
  - `except_vendor`: Exclude vendor routes (boolean)
  - `only_vendor`: Only show vendor routes (boolean)

#### 9. **list-artisan-commands**
- **Description:** List all registered Artisan commands
- **Usage:** No arguments required

#### 10. **get-absolute-url**
- **Description:** Convert relative path or named route to absolute URL
- **Arguments:**
  - `path` (optional): Relative path (e.g., "/dashboard")
  - `route` (optional): Named route (e.g., "home")

---

### 🐛 Debugging Tools

#### 11. **last-error**
- **Description:** Get details of the last backend error/exception
- **Usage:** No arguments required
- **Note:** Use browser-logs for frontend errors

#### 12. **read-log-entries**
- **Description:** Read last N log entries from application log
- **Arguments:**
  - `entries` (required): Number of entries to return
- **Example:** `{"entries": 10}`

#### 13. **browser-logs**
- **Description:** Read last N log entries from browser/frontend
- **Arguments:**
  - `entries` (required): Number of entries to return
- **Use Case:** Debugging frontend JavaScript errors

#### 14. **tinker**
- **Description:** Execute PHP code in Laravel application context
- **Arguments:**
  - `code` (required): PHP code without opening tags
  - `timeout` (required): Max execution time in seconds
- **Example:**
  ```json
  {
    "code": "return App\\Models\\User::count();",
    "timeout": 10
  }
  ```
- **Returns:** Result, output, and type

---

### 📚 Documentation

#### 15. **search-docs**
- **Description:** Search Laravel ecosystem documentation with semantic search
- **Arguments:**
  - `queries` (required): Array of search queries
  - `packages` (optional): Array of package names to limit search
  - `token_limit` (optional): Max tokens (default: 3000, max: 1,000,000)
- **Supports:** Laravel, Inertia, Pest, Livewire, Filament, Nova, Tailwind
- **Example:**
  ```json
  {
    "queries": ["eloquent models", "relationships"],
    "packages": ["laravel/framework"],
    "token_limit": 5000
  }
  ```

---

### 💬 Feedback

#### 16. **report-feedback**
- **Description:** Send feedback about Boost or Laravel to the team
- **Arguments:**
  - `feedback` (required): Detailed feedback text
- **Note:** Only for Boost/Laravel ecosystem feedback

---

## Test Results

### ✅ Verification Tests Passed

| Test | Status | Result |
|------|--------|--------|
| Server Initialization | ✅ | Protocol v2024-11-05 active |
| application-info | ✅ | PHP 8.4.14, Laravel 12.37.0 |
| database-schema | ✅ | 9 tables detected |
| list-routes | ✅ | 4 routes returned |
| tinker | ✅ | Code execution: `1 + 1 = 2` |
| search-docs | ✅ | 27 docs, 336 chunks found |

---

## Current Database Schema

### Tables Created by Default Migrations:

1. **users**
   - Columns: id, name, email, email_verified_at, password, remember_token, timestamps
   - Indexes: Primary key (id), Unique (email)

2. **sessions**
   - Columns: id, user_id, ip_address, user_agent, payload, last_activity
   - Indexes: Primary (id), Index (user_id, last_activity)

3. **cache** / **cache_locks**
   - For application caching

4. **jobs** / **job_batches** / **failed_jobs**
   - For queue management

5. **password_reset_tokens**
   - For password reset functionality

6. **migrations**
   - Migration tracking

---

## How AI Assistants Use MCP

When Claude Code connects to your Laravel app:

1. **Discovery Phase**
   - AI calls `tools/list` to see available tools
   - Calls `application-info` to understand your setup

2. **Context Gathering**
   - Reads database schema when working with models
   - Lists routes when building controllers
   - Searches docs for best practices

3. **Code Execution**
   - Uses Tinker to test code snippets
   - Queries database to verify data
   - Reads logs to debug issues

4. **Real-time Assistance**
   - All tools work during development
   - No manual lookups needed
   - AI has live access to your app state

---

## Creating Custom MCP Tools

### Generate a Custom Tool
```bash
./vendor/bin/sail artisan make:mcp-tool YourCustomTool
```

### Publish Routes File
```bash
./vendor/bin/sail artisan vendor:publish --tag=ai-routes
```
Creates: `routes/ai.php`

### Register Custom Server
```php
// routes/ai.php
use Laravel\Mcp\Facades\Mcp;

Mcp::local('my-server', MyCustomServer::class);
// or
Mcp::web('/mcp/my-server', MyCustomServer::class);
```

### Tool Structure
```php
namespace App\Mcp\Tools;

use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Response;

class YourCustomTool extends Tool
{
    public string $name = 'your-custom-tool';
    public string $description = 'Description of what it does';

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'param1' => [
                    'type' => 'string',
                    'description' => 'Parameter description',
                ],
            ],
            'required' => ['param1'],
        ];
    }

    public function handle(string $param1): Response
    {
        // Your logic here
        return Response::text('Result here');
    }
}
```

---

## Troubleshooting

### MCP Server Not Connecting
1. Ensure Docker is running: `./vendor/bin/sail ps`
2. Check database is up: `./vendor/bin/sail artisan migrate:status`
3. Restart Sail: `./vendor/bin/sail restart`

### Tool Returns Errors
- Check logs: `./vendor/bin/sail artisan log:show`
- Use `last-error` tool to see recent exceptions
- Verify database connection with `database-connections` tool

### Documentation Search Returns Nothing
- Verify packages are installed: `composer show`
- Update Boost guidelines: `./vendor/bin/sail artisan boost:update`

---

## Useful Commands

```bash
# Update AI guidelines
./vendor/bin/sail artisan boost:update

# Test MCP server with inspector
./vendor/bin/sail artisan mcp:inspector <handle>

# Start MCP server (for manual testing)
./vendor/bin/sail artisan boost:mcp

# List all MCP commands
./vendor/bin/sail artisan list | grep mcp

# Check application info
./vendor/bin/sail artisan about
```

---

## Resources

- **Laravel MCP Docs:** https://laravel.com/docs/mcp
- **Boost Package:** https://github.com/laravel/boost
- **MCP Protocol:** https://modelcontextprotocol.io/docs/getting-started/intro
- **Claude Code MCP:** https://docs.claude.com/en/docs/claude-code

---

## Project-Specific Notes for LOGGD

### Recommended Custom MCP Tools to Build:

1. **get-user-habits** - Fetch user's tracking habits/goals
2. **get-progress-summary** - Calculate progress metrics
3. **generate-visualization-data** - Prepare data for charts
4. **list-tracking-entries** - Query specific tracking data

### Next Steps:
- Design database schema for habits/tracking
- Create Eloquent models
- Build custom MCP tools for LOGGD features
- Set up authentication with Laravel Breeze/Sanctum

---

*Last Updated: 2025-11-05*
*Environment: Laravel 12.37.0, PHP 8.4.14, MySQL*
