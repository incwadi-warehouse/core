# Upgrade 2.0

- Reports are deleted, use Saved Searches instead.
- Whole new search experience
- Book id switched to uuid. Old id won't be upgraded automatically, they should not have changed after the update.
- Rename table `migration_versions` to `doctrine_migration_versions`.
