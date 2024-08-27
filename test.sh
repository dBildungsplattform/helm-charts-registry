#!/bin/bash
repos=()

for file in $(find automation -name "Chart.yaml"); do
    # Perform your desired operation on $file
    echo "Processing $file"
    repos+=$(yq eval '.dependencies[].repository' "$file" | grep '^https')
done

# Loop through repositories and add them
for repo in "${repos[@]}"; do
    echo "Adding repo \"$(basename "$repo")\" with source \"$repo\""
    helm repo add "$(basename "$repo")" "$repo" || echo "Failed to add \"$repo\""
done