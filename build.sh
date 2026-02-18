#!/bin/bash

# WishGlut Build Script
# Creates wishglut.zip from current files

echo "Building WishGlut..."

# Get current version from wishglut-version.json
VERSION=$(cat wishglut-version.json | grep '"version"' | head -1 | awk -F: '{ print $2 }' | sed 's/[", ]//g')
TIMESTAMP=$(date -u +"%Y-%m-%d %H:%M:%S")

echo "Version: $VERSION"

# Remove old ZIP
rm -f wishglut.zip

# Create temporary folder for building
rm -rf .build
mkdir -p .build/wishglut

# Copy plugin files to .build/wishglut
echo "Copying files..."

# Copy PHP files
cp *.php .build/wishglut/ 2>/dev/null || true

# Copy directories
cp -r src .build/wishglut/ 2>/dev/null || true

# Copy other files
cp readme.txt .build/wishglut/ 2>/dev/null || true
cp autoloader.php .build/wishglut/ 2>/dev/null || true

# Remove unwanted files
rm -rf .build/wishglut/.git
rm -rf .build/wishglut/.github
rm -rf .build/wishglut/.claude
rm -rf .build/wishglut/.build

# Create ZIP
echo "Creating ZIP..."
cd .build
zip -r ../wishglut.zip wishglut/
cd ..

# Clean up
rm -rf .build

# Update timestamp in wishglut-version.json
sed -i "s/\"last_updated\": \".*\"/\"last_updated\": \"$TIMESTAMP\"/" wishglut-version.json

echo ""
echo "✓ Build complete!"
echo "  wishglut.zip - Ready to deploy"
echo "  wishglut-version.json - Version: $VERSION"
echo ""
echo "Next steps:"
echo "  git add wishglut.zip wishglut-version.json"
echo "  git commit -m \"Build version $VERSION\""
echo "  git push origin main"
