#!/bin/bash

# Debugging: Print commands as they are executed
set -x

echo "Executing entrypoint.sh"

# Ensure the initial script is executable
chmod +x /usr/local/bin/initial-script.sh
echo "Made initial-script.sh executable"

# Run the initial script
echo "Running initial-script.sh"
/usr/local/bin/initial-script.sh

# Debugging: Check the status of the initial script execution
if [ $? -eq 0 ]; then
    echo "Initial script executed successfully."
else
    echo "Initial script failed."
    exit 1
fi

# Call the original entrypoint script from the base image
echo "Running start-container script"
/usr/local/bin/start-container "$@"

# Keep the container running if needed
tail -f /dev/null