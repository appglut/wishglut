#!/bin/bash

# WishGlut Version Update Script
# Usage: ./set-version.sh 1.1.3

if [ -z "$1" ]; then
    echo "Usage: ./set-version.sh <version>"
    echo "Example: ./set-version.sh 1.1.3"
    exit 1
fi

NEW_VERSION=$1
TIMESTAMP=$(date -u +"%Y-%m-%d %H:%M:%S")

# Update wishglut-version.json
if [ -f "wishglut-version.json" ]; then
    # Backup original
    cp wishglut-version.json wishglut-version.json.bak

    # Update version using Python if available
    if command -v python3 &> /dev/null; then
        python3 -c "
import json
with open('wishglut-version.json', 'r') as f:
    data = json.load(f)
data['version'] = '$NEW_VERSION'
data['last_updated'] = '$TIMESTAMP'
with open('wishglut-version.json', 'w') as f:
    json.dump(data, f, indent=2)
"
        echo "✓ Updated wishglut-version.json to $NEW_VERSION"
    else
        # Fallback to sed
        sed -i "s/\"version\": \".*\"/\"version\": \"$NEW_VERSION\"/" wishglut-version.json
        sed -i "s/\"last_updated\": \".*\"/\"last_updated\": \"$TIMESTAMP\"/" wishglut-version.json
        echo "✓ Updated wishglut-version.json to $NEW_VERSION"
    fi

    rm wishglut-version.json.bak
else
    echo "✗ wishglut-version.json not found!"
    exit 1
fi

# Update version in wishglut.php (source file)
if [ -f "wishglut.php" ]; then
    sed -i "s/Version: .*/Version: $NEW_VERSION/" wishglut.php
    sed -i "s/define( 'WISHGLUT_VERSION', '[^']*'/define( 'WISHGLUT_VERSION', '$NEW_VERSION'/" wishglut.php
    echo "✓ Updated wishglut.php to $NEW_VERSION"
fi

echo ""
echo "Version updated to: $NEW_VERSION"
echo "Timestamp: $TIMESTAMP"
echo ""
echo "Next steps:"
echo "  1. Run build script: ./build.sh"
echo "  2. Commit: git add wishglut.zip wishglut-version.json"
echo "  3. Push: git push origin main"
