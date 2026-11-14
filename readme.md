# ABC Fast or Slow

## Local setup

1. Start Apache and MariaDB from the XAMPP Control Panel.
2. Create and import the database. From PowerShell, run these commands from this project directory:

   ```powershell
   & "C:\xampp\mysql\bin\mysql.exe" -u root -P 3306 -e "CREATE DATABASE abc_fast_or_slow CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
   & $env:ComSpec /c '"C:\xampp\mysql\bin\mysql.exe" -u root -P 3306 abc_fast_or_slow < "abc_fast_or_slow.sql"'
   ```

   If the database is already imported from the older schema, apply these once before running the updated game:

   ```sql
   ALTER TABLE rooms
     ADD COLUMN mode ENUM('local','multi') NOT NULL DEFAULT 'multi' AFTER code,
     ADD COLUMN default_speed ENUM('fast','slow') NOT NULL DEFAULT 'slow' AFTER letter_picker;

   ALTER TABLE room_players
     ADD COLUMN left_at DATETIME DEFAULT NULL AFTER last_seen;
   ```

3. If your MariaDB credentials differ from the XAMPP defaults, configure `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, and `DB_PASS` in the environment. The defaults are `127.0.0.1`, `3306`, `abc_fast_or_slow`, `root`, and an empty password.
4. Start the PHP development server from this directory:

   ```powershell
   & "C:\xampp\php\php.exe" -S localhost:8000 -t .
   ```

5. Open `http://localhost:8000/`, create an account, and start a local game or create/join a multiplayer room.

The application stores accounts, rooms, round settings, letters, answers, and submissions in MariaDB. Round answers are checked on both the browser and server against the selected letter.

## Verify saved game data

After submitting a round, run these queries in phpMyAdmin or the MariaDB client:

```sql
SELECT code, mode, status, total_rounds FROM rooms ORDER BY id DESC LIMIT 5;

SELECT r.code, rd.number, rd.letter, c.label, a.value, u.username
FROM rooms r
JOIN rounds rd ON rd.room_id = r.id
JOIN answers a ON a.round_id = rd.id
JOIN room_players rp ON rp.id = a.room_player_id
JOIN users u ON u.id = rp.user_id
JOIN categories c ON c.id = a.category_id
ORDER BY rd.id DESC, c.id;
```
