# Admin Menu CSS/JS Fix Plan

## Steps:
- [x] 1. Confirm structure on manage_artifacts.php, manage_news.php etc. (read files) - All good!
- [x] 2. Check js/header.js for layout conflicts - No issues
- [ ] 3. Search for CSS overrides/conflicts (.sidebar without .admin-page scoping)
- [ ] 4. Read css/style.css, css/manage.css for conflicting rules
- [ ] 5. Add admin.css as last CSS link with media="all" or !important on critical rules
- [ ] 6. Verify button animations (hover effects)
- [ ] 7. Test with browser command
- [ ] 8. Complete task

Current: Found issue - css/style.css has unscoped .sidebar/.admin-layout rules overriding admin.css. Fixing specificity.

Steps updated:

- [x] 1. Confirm structure...
- [x] 2. ...
- [x] 3. Search CSS conflicts - Found in css/style.css!
- [ ] 4. Scope .sidebar/.admin-layout in style.css with .admin-page 
- [ ] 5. Ensure admin.css loads after style.css if needed
- [ ] 6. Test page
- [ ] 7. Complete

