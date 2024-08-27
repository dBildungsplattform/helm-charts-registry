#!/bin/bash
set -x
repos=$(find automation -name "Chart.yaml" -exec yq '.dependencies[].repository' {} \; | \
        sort | \
        uniq | \
        grep '^https')

# Loop through repositories and add them
for repo in $repos; do
    echo "Adding repo \"$(basename "$repo")\" with source \"$repo\""
    helm repo add "$(basename "$repo")" "$repo" || echo "Failed to add \"$repo\""
done