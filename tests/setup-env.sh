#!/usr/bin/env bash
set -e

# 1. Read the unique port assigned to this test environment (8890)
PORT=$(node -e 'console.log(require("./test.wp-env.json").port || 8890)')

echo "==> Locating wp-env instance running on port $PORT..."
# 2. Find the container publishing on this specific port (ignores Plugin Check on 8888)
WP_CONTAINER=$(docker ps -q --filter "publish=$PORT" | head -n 1)

if [ -z "$WP_CONTAINER" ]; then
    echo "Error: Could not find container publishing on port $PORT"
    exit 1
fi

# 3. Extract the unique Docker Compose project hash for THIS specific environment
PROJECT=$(docker inspect --format '{{ index .Config.Labels "com.docker.compose.project" }}' "$WP_CONTAINER")

# 4. Find the CLI container that belongs EXCLUSIVELY to this project hash
CLI_CONTAINER=$(docker ps -q --filter "label=com.docker.compose.project=$PROJECT" --filter "label=com.docker.compose.service=cli" | head -n 1)

if [ -z "$CLI_CONTAINER" ]; then
    echo "Error: CLI container for project $PROJECT not found"
    exit 1
fi

echo "==> Found target CLI container ($CLI_CONTAINER) for project $PROJECT."

# 5. Check if pdo_mysql is already installed; if not, compile it as root
if ! docker exec "$CLI_CONTAINER" php -m | grep -q pdo_mysql; then
    echo "==> Compiling pdo_mysql as root..."
    docker exec -u 0 "$CLI_CONTAINER" docker-php-ext-install pdo_mysql
fi

# 6. Activate WooCommerce in this specific instance
echo "==> Activating WooCommerce..."
npx wp-env --config=test.wp-env.json run cli wp plugin activate woocommerce

echo "==> Setup complete for test environment on port $PORT."
