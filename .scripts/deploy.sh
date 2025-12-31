#!/bin/bash
set -e

echo "Deployment started in staging ....."

# Define your deployment directories and branches
declare -A branches=(
    ["main"]="htdocs/businessjoy.in/"
    ["staging"]="htdocs/staging.businessjoy.in/"
)

# Determine the current branch
current_branch=$(git rev-parse --abbrev-ref HEAD)

# Check if the current branch is in detached HEAD state
if [[ "$current_branch" == "HEAD" ]]; then
    echo "Error: Detached HEAD state detected. Checking out to 'main' branch."
    git checkout main
    current_branch="main"
fi

echo "Current branch: $current_branch"
# Check if the current branch exists in your defined branches
if [[ -n "${branches[$current_branch]}" ]]; then
    deployment_directory="${branches[$current_branch]}"
    echo "Deploying branch $current_branch to $deployment_directory"

cd ~
cd "$deployment_directory"
# cd ~/htdocs/staging.businessjoy.in/

# Turn ON Maintenance Mode or return true
# if already is in maintenance mode
(php artisan down) || true

# Pull the latest version of the app
# git pull origin main
# Pull the latest version of the app
git pull origin "$current_branch"


# Turn OFF Maintenance mode
php artisan up

echo "Deployment finished!"

else
    echo "Error: Deployment for branch $current_branch is not configured."
    exit 1
fi