# Agents

This document provides guidelines on framework & environment setup, testing, linting, usage examples, and formatting rules for agents in this project.

---

## Framework & Environment Setup

1. **Runtime Requirements**
   - PHP 8.4+
   - Node.js 23+
2. **Dependencies**
   - Install PHP dependencies via Composer:
     ```sh
     composer install
     ```
   - Install Node.js dependencies via npm:
     ```sh
     npm install
     ```
3. **Environment Variables**
   - Copy and configure the environment file:
     ```sh
     cp .env.example .env
     ```
   - Update `.env` with API keys, database credentials, and service endpoints.
4. **Database & Services**
   - Run database migrations (Laravel):
     ```sh
     php artisan migrate
     ```
   - Start any local services (e.g., Redis, mock servers):
     ```sh
     # Example: start Redis
     redis-server
     ```

---

## How and When to Test

### PHP Tests (Pest)

- **Run all tests**:
  ```sh
  vendor/bin/pest
  ```
- **Run a specific test**:
  ```sh
  vendor/bin/pest --filter=TestName
  ```
- **Watch mode**:
  ```sh
  vendor/bin/pest --watch
  ```

### JavaScript/TypeScript Tests

- **Run all tests**:
  ```sh
  npm test
  ```
- **Watch mode**:
  ```sh
  npm test -- --watch
  ```

### Manual/Exploratory Testing

- Trigger agent actions locally and inspect logs:
  ```sh
  node scripts/runAgent.js --id example-agent
  ```

### Continuous Integration (CI)

- Ensure all tests (Pest and JS) pass on every pull request.
- Include linting and formatting checks in the CI pipeline.

---

## Linting & Formatting

- **PHP Linting**
  - Use Laravel Pint:
    ```sh
    vendor/bin/pint
    ```
  - Configure `pint.json` or `.pint.php` as needed.
  - Follow PSR-12 coding standards.

- **JavaScript/TypeScript Linting**
  - Use ESLint:
    ```sh
    npm run lint
    ```
  - Follow project ESLint rules and Prettier formatting.

- **Markdown**
  - Use ATX-style headers (`#`, `##`, `###`).
  - Wrap code blocks with triple backticks specifying the language.

---

## Usage Examples

### 1. Initializing an Agent (JavaScript)
```js
const { Agent } = require('agents-framework');

const agent = new Agent({
  name: 'example-agent',
  config: {
    timeout: 5000,
  },
});

agent.start();
```

### 2. Sending a Request (JavaScript)
```js
agent.send({
  type: 'PROCESS_DATA',
  payload: {
    // ...
  },
});
```

### 3. Listening for Events (JavaScript)
```js
agent.on('completed', (result) => {
  console.log('Agent completed with result:', result);
});
```

---

## Formatting Rules & Guidelines

- **Agent Identifiers**: Use `kebab-case` or `camelCase` consistently.
- **Commits & PRs**
  - Format commit messages as `type(scope): subject`, e.g., `feat(agent): add retry logic`.
  - Prefix agent-related PR titles with `[agent]`.

---

_Last updated: 2025-07-19_

