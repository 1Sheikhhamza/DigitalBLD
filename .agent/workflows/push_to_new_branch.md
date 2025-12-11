---
description: Push current code to a new, timestamped branch on GitHub
---

# Push to New Branch

1. Generate a unique branch name using the current timestamp.
   - naming convention: `update-YYYY-MM-DD_HH-mm-ss`

2. Create and switch to the new branch.
   ```bash
   # Example command (agent will generate actual timestamp)
   git checkout -b update-$(date +%Y-%m-%d_%H-%M-%S)
   ```

3. Stage all changes.
   ```bash
   git add .
   ```

4. Commit the changes.
   ```bash
   git commit -m "Auto-update: $(date +%Y-%m-%d\ %H:%M:%S)"
   ```

5. Push the new branch to origin.
   ```bash
   # The branch name will match the one created in step 2
   git push -u origin HEAD
   ```
