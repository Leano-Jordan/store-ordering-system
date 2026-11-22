#!/usr/bin/env bash
set -euo pipefail

: "${MYSQL_ROOT_PASSWORD:?MYSQL_ROOT_PASSWORD is required}"
MYSQL_HOST="${MYSQL_HOST:-127.0.0.1}"
MYSQL_PORT="${MYSQL_PORT:-3306}"

mysql_cmd() {
    mysql --protocol=tcp \
        --host="$MYSQL_HOST" \
        --port="$MYSQL_PORT" \
        --user=root \
        --password="$MYSQL_ROOT_PASSWORD" \
        "$@"
}

mysql_cmd -e "
CREATE DATABASE swiftorder_schema CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
CREATE DATABASE swiftorder_migration CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
"

mysql_cmd swiftorder_schema < database/schema.sql
mysql_cmd swiftorder_migration < database/migrations/001_initial_v1_1_schema.sql

for database in swiftorder_schema swiftorder_migration; do
    non_innodb="$(mysql_cmd --batch --skip-column-names -e "
        SELECT COUNT(*)
        FROM information_schema.tables
        WHERE table_schema = '$database'
          AND table_type = 'BASE TABLE'
          AND engine <> 'InnoDB';
    ")"

    supplier_nullable="$(mysql_cmd --batch --skip-column-names -e "
        SELECT is_nullable
        FROM information_schema.columns
        WHERE table_schema = '$database'
          AND table_name = 'suppliers'
          AND column_name = 'status';
    ")"

    foreign_keys="$(mysql_cmd --batch --skip-column-names -e "
        SELECT COUNT(*)
        FROM information_schema.table_constraints
        WHERE table_schema = '$database'
          AND constraint_type = 'FOREIGN KEY';
    ")"

    test "$non_innodb" = "0"
    test "$supplier_nullable" = "NO"
    test "$foreign_keys" -gt 0
done

mysql_cmd --batch --skip-column-names -e "
    SELECT table_name, engine
    FROM information_schema.tables
    WHERE table_schema = 'swiftorder_schema'
      AND table_type = 'BASE TABLE'
    ORDER BY table_name;
" > /tmp/swiftorder-schema-tables.txt

mysql_cmd --batch --skip-column-names -e "
    SELECT table_name, engine
    FROM information_schema.tables
    WHERE table_schema = 'swiftorder_migration'
      AND table_type = 'BASE TABLE'
    ORDER BY table_name;
" > /tmp/swiftorder-migration-tables.txt

diff -u /tmp/swiftorder-schema-tables.txt /tmp/swiftorder-migration-tables.txt

for database in swiftorder_schema swiftorder_migration; do
    mysql_cmd --batch --skip-column-names -e "
        SELECT table_name, column_name, column_type, is_nullable,
               COALESCE(column_default, '<NULL>'), extra
        FROM information_schema.columns
        WHERE table_schema = '$database'
        ORDER BY table_name, ordinal_position;
    " > "/tmp/$database-columns.txt"

    mysql_cmd --batch --skip-column-names -e "
        SELECT table_name, index_name, non_unique, seq_in_index,
               column_name, COALESCE(sub_part, 0), index_type
        FROM information_schema.statistics
        WHERE table_schema = '$database'
        ORDER BY table_name, index_name, seq_in_index;
    " > "/tmp/$database-indexes.txt"

    mysql_cmd --batch --skip-column-names -e "
        SELECT k.table_name, k.constraint_name, k.column_name,
               k.referenced_table_name, k.referenced_column_name,
               r.update_rule, r.delete_rule
        FROM information_schema.key_column_usage k
        JOIN information_schema.referential_constraints r
          ON r.constraint_schema = k.constraint_schema
         AND r.constraint_name = k.constraint_name
         AND r.table_name = k.table_name
        WHERE k.constraint_schema = '$database'
          AND k.referenced_table_name IS NOT NULL
        ORDER BY k.table_name, k.constraint_name, k.ordinal_position;
    " > "/tmp/$database-foreign-keys.txt"
done

diff -u /tmp/swiftorder_schema-columns.txt /tmp/swiftorder_migration-columns.txt
diff -u /tmp/swiftorder_schema-indexes.txt /tmp/swiftorder_migration-indexes.txt
diff -u /tmp/swiftorder_schema-foreign-keys.txt /tmp/swiftorder_migration-foreign-keys.txt

echo "PASS: clean schema and initial migration install consistently on $MYSQL_HOST:$MYSQL_PORT"
