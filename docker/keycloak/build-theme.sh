# Save the current directory (where the script is run)
BASE_DIR=$(pwd)

# Define path to the theme source code
PROVIDERS_SRC_PATH="theme/"

# Navigate to the theme directory
cd "$PROVIDERS_SRC_PATH" || exit

# Remove previous build 
rm -rf dist_keycloak/

# Build theme
echo "Theme is building..."
yarn build-keycloak-theme

# Store all JAR files in an array
jar_files=(dist_keycloak/target/keycloakify-starter-keycloak-theme*.jar)

# Check if build was successful (i.e., any JAR files found)
if [ ${#jar_files[@]} -eq 0 ]; then
    echo "Build failed, jar file not found."
    exit 1
fi

# Copy all JAR files to the providers directory relative to the base directory where script was called
echo "Copying JAR files to $BASE_DIR/providers/"
cp dist_keycloak/target/keycloakify-starter-keycloak-theme*.jar "$BASE_DIR/providers/"

echo "Build successful, JAR files copied."