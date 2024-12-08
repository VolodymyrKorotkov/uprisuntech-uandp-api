#!/bin/bash

# Save the current directory (where the script is run)
BASE_DIR=$(pwd)

# Define path to the providers source code
PROVIDERS_SRC_PATH="src/providers"

# Navigate to the webhooks provider directory
cd "$PROVIDERS_SRC_PATH/webhooks" || exit

# Run Maven to package the webhooks provider
mvn clean package

# List all JAR files in target directory to check if they exist
echo "Listing JAR files in target directory:"
ls target/*.jar

# Store all JAR files in an array
jar_files=(target/*.jar)

# Check if build was successful (i.e., any JAR files found)
if [ ${#jar_files[@]} -eq 0 ]; then
    echo "Build failed, jar file not found."
    exit 1
fi

# Copy all JAR files to the providers directory relative to the base directory where script was called
echo "Copying JAR files to $BASE_DIR/providers/"
cp target/*.jar "$BASE_DIR/providers/"

echo "Build successful, JAR files copied."