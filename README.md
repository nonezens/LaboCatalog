# LaboCatalog Refactoring

This project has been refactored to remove duplicate files and consolidate CSS and JavaScript.

## Changes Made

- **Removed Duplicate PHP Files:** All `*2.php` files have been deleted.
- **Consolidated CSS:** All CSS files from the `css/` directory have been merged into a single `css/style.css` file.
- **Consolidated JavaScript:** All JavaScript files from the `js/` directory have been merged into a single `js/main.js` file.
- **Updated HTML:** The `header.php` and `index.php` files have been updated to link to the new `style.css` and `main.js` files, and all inline styles and scripts have been removed.

## Project Structure

The project structure is now cleaner and more maintainable:

- `css/style.css`: All styles for the project.
- `js/main.js`: All JavaScript for the project.
- `*.php`: PHP files for the website.
