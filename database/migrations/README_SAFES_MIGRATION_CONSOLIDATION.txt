This file documents the consolidation of all safes table migrations into the initial create_safes_table migration (2025_06_26_210754_create_safes_table.php) as of July 20, 2025. The following migrations are now redundant and can be deleted:

- 2025_06_27_115100_rename_balance_to_current_balance_in_safes_table.php
- 2025_06_29_201633_add_type_to_safes_table.php
- 2025_07_02_125315_add_type_to_safes_table.php
- 2025_07_05_045404_add_unique_constraint_to_safes_branch_id.php
- 2025_07_19_212602_add_is_active_to_safes_table.php

All relevant columns and constraints are now included in the initial safes table migration.
