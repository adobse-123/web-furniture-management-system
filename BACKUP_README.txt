BACKUP_README

Quick backup/restore instructions for the Furniture Management System:

1) Database backup (mysqldump):
   mysqldump -u <user> -p <database_name> > backup_`date +%F`.sql

2) Restore:
   mysql -u <user> -p <database_name> < backup.sql

3) File backup:
   - Zip the project folder or rsync to a separate storage location.

4) Recommended schedule:
   - Database: daily (or more frequent depending on transactions)
   - Files: weekly or when deploying changes

5) Keep backups offsite and test restores regularly.
