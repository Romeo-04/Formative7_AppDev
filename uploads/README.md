# Uploads Directory

This directory is used to store user-uploaded profile images.

## Security Notes:
- Only image files (JPG, JPEG, PNG, GIF) are allowed
- Maximum file size is limited to 5MB
- Files are renamed with user ID and timestamp for security
- Make sure this directory has write permissions (777 or 755)

## Directory Structure:
- User profile images will be stored as: `user_[ID]_[timestamp].[extension]`

Example: `user_1_1642636800.jpg`
