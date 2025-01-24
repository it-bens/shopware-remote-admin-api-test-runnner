#!/bin/bash
set -e

echo "Starting the entry script..."

# Function to wait for MariaDB to be ready
wait_for_mariadb() {
    echo "Waiting for MariaDB to be ready..."
    for i in {1..60}; do
        if mysqladmin ping -h"localhost" --silent; then
            echo "MariaDB is ready!"
            return 0
        else
            echo "MariaDB is not ready yet..."
            sleep 1
        fi
    done
    echo "Failed to connect to MariaDB after 60 seconds."
    return 1
}

# Start MariaDB in the background
echo "Starting MariaDB..."
service mariadb start

# Wait for MariaDB to be ready
if ! wait_for_mariadb; then
    echo "MariaDB failed to start. Exiting."
    exit 1
fi

# Conditionally remove disable_warnings.ini from the PHP conf.d folder
if [ "${DISABLE_WARNINGS:-true}" = "false" ]; then
    echo "DISABLE_WARNINGS is set to false. PHP warnings are enabled."
    rm -f /usr/local/etc/php/conf.d/disable_warnings.ini
else
    echo "DISABLE_WARNINGS is true or not set. PHP warnings are disabled."
fi

# Optionally, run any additional setup or migrations here

# Start Apache in the foreground
echo "Starting Apache..."
exec apache2-foreground
