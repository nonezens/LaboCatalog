# LaboCatalog Error Fixes & Merge Improvements - TODO

Following approved plan to standardize security, fix inconsistencies, and clean up code.

## Steps:
- [x] 1. Create `includes/auth.php` (common admin session + DB include)
- [x] 2. Create `includes/db.php` (secure DB connection)
- [x] 3. Update core files: admin_dashboard.php, add_exhibit.php, manage_exhibits.php, login.php (partial; more next)
- [ ] 4. Fix session checks & includes in admin forms: add_*.php, edit_*.php, manage_*.php, delete_*.php
- [ ] 5. Replace error message exposures (e.g., `$conn->error`)
- [ ] 6. Secure DELETE operations (prepared stmts or intval)
- [ ] 7. Test functionality (login, CRUD exhibits, etc.)
- [ ] 8. Demo site & complete

Progress tracked here. Current: Starting step 1.

## Notes:
- No merge conflicts found.
- Focus: Security (sessions strict bool), error hiding, dedup code.

