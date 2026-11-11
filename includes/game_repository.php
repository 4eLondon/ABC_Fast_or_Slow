<?php

require_once __DIR__ . '/database.php';

function game_categories(PDO $db): array
{
    $order = ['Boy', 'Girl', 'Thing', 'Food', 'Place'];
    $statement = $db->prepare(
        'SELECT id, label FROM categories WHERE is_active = 1 AND label IN (?, ?, ?, ?, ?)'
    );
    $statement->execute($order);
    $categories = $statement->fetchAll();
    $byLabel = [];
    foreach ($categories as $category) {
        $byLabel[$category['label']] = $category;
    }

    foreach ($order as $label) {
        if (!isset($byLabel[$label])) {
            throw new RuntimeException("The active database categories are missing {$label}.");
        }
    }

    return array_map(static function (string $label) use ($byLabel): array {
        return $byLabel[$label];
    }, $order);
}

function find_room(PDO $db, string $code): ?array
{
    $statement = $db->prepare(
        'SELECT id, code, mode, host_id, status, phase, total_rounds, default_speed, fast_seconds, slow_seconds
         FROM rooms WHERE code = :code'
    );
    $statement->execute(['code' => $code]);
    $room = $statement->fetch();
    if (!$room) {
        return null;
    }

    $players = $db->prepare(
        'SELECT rp.id, rp.user_id, rp.total_units, u.username
         FROM room_players rp
         JOIN users u ON u.id = rp.user_id
         WHERE rp.room_id = :room_id AND rp.left_at IS NULL
         ORDER BY rp.joined_at, rp.id'
    );
    $players->execute(['room_id' => $room['id']]);
    $room['players'] = $players->fetchAll();

    $categories = $db->prepare(
        'SELECT c.id, c.label
         FROM room_categories rc
         JOIN categories c ON c.id = rc.category_id
         WHERE rc.room_id = :room_id
         ORDER BY FIELD(c.label, "Boy", "Girl", "Thing", "Food", "Place")'
    );
    $categories->execute(['room_id' => $room['id']]);
    $room['categories'] = $categories->fetchAll();

    $round = $db->prepare(
        'SELECT id, number, speed, letter, started_at, deadline_at, stopped_at
         FROM rounds WHERE room_id = :room_id ORDER BY number DESC LIMIT 1'
    );
    $round->execute(['room_id' => $room['id']]);
    $room['round'] = $round->fetch() ?: null;

    return $room;
}

function find_room_player(PDO $db, int $roomId, int $userId): ?array
{
    $statement = $db->prepare(
        'SELECT id, room_id, user_id, total_units
         FROM room_players WHERE room_id = :room_id AND user_id = :user_id AND left_at IS NULL'
    );
    $statement->execute(['room_id' => $roomId, 'user_id' => $userId]);
    return $statement->fetch() ?: null;
}

function join_room(PDO $db, int $roomId, int $userId): void
{
    $db->beginTransaction();
    try {
        $lock = $db->prepare('SELECT status FROM rooms WHERE id = :id FOR UPDATE');
        $lock->execute(['id' => $roomId]);
        if ($lock->fetchColumn() !== 'lobby') {
            throw new DomainException('That game has already started.');
        }

        $activeCount = $db->prepare('SELECT COUNT(*) FROM room_players WHERE room_id = :room_id AND left_at IS NULL');
        $activeCount->execute(['room_id' => $roomId]);
        if ((int) $activeCount->fetchColumn() >= 4) {
            throw new DomainException('That room is full.');
        }

        $existing = $db->prepare('SELECT id, left_at FROM room_players WHERE room_id = :room_id AND user_id = :user_id');
        $existing->execute(['room_id' => $roomId, 'user_id' => $userId]);
        $player = $existing->fetch();
        if ($player && $player['left_at'] === null) {
            $db->commit();
            return;
        }

        if ($player) {
            $rejoin = $db->prepare('UPDATE room_players SET left_at = NULL, joined_at = UTC_TIMESTAMP() WHERE id = :id');
            $rejoin->execute(['id' => $player['id']]);
        } else {
            $insert = $db->prepare('INSERT INTO room_players (room_id, user_id) VALUES (:room_id, :user_id)');
            $insert->execute(['room_id' => $roomId, 'user_id' => $userId]);
        }
        $db->commit();
    } catch (Throwable $error) {
        $db->rollBack();
        throw $error;
    }
}

function leave_room(PDO $db, string $code, int $userId): void
{
    $db->beginTransaction();
    try {
        $lock = $db->prepare('SELECT id, host_id FROM rooms WHERE code = :code FOR UPDATE');
        $lock->execute(['code' => $code]);
        $room = $lock->fetch();
        if (!$room) {
            $db->commit();
            return;
        }

        $player = find_room_player($db, (int) $room['id'], $userId);
        if (!$player) {
            $db->commit();
            return;
        }

        $leave = $db->prepare('UPDATE room_players SET left_at = UTC_TIMESTAMP() WHERE id = :id');
        $leave->execute(['id' => $player['id']]);

        $remaining = $db->prepare(
            'SELECT user_id FROM room_players
             WHERE room_id = :room_id AND left_at IS NULL ORDER BY joined_at, id LIMIT 1'
        );
        $remaining->execute(['room_id' => $room['id']]);
        $nextHost = $remaining->fetchColumn();

        if ($nextHost === false) {
            $cancel = $db->prepare(
                "UPDATE rooms SET status = 'cancelled', phase = 'finished', finished_at = UTC_TIMESTAMP()
                 WHERE id = :id AND status IN ('lobby', 'playing')"
            );
            $cancel->execute(['id' => $room['id']]);
        } elseif ((int) $room['host_id'] === $userId) {
            $transfer = $db->prepare('UPDATE rooms SET host_id = :host_id WHERE id = :id');
            $transfer->execute(['host_id' => $nextHost, 'id' => $room['id']]);
        }

        $db->commit();
    } catch (Throwable $error) {
        $db->rollBack();
        throw $error;
    }
}

function generate_room_code(PDO $db): string
{
    do {
        $code = '';
        for ($i = 0; $i < 4; $i++) {
            $code .= chr(random_int(65, 90));
        }
        $statement = $db->prepare('SELECT 1 FROM rooms WHERE code = :code');
        $statement->execute(['code' => $code]);
    } while ($statement->fetchColumn());

    return $code;
}

function create_room(PDO $db, int $userId, bool $local, int $rounds, string $speed, string $letter): string
{
    $categories = game_categories($db);
    $code = generate_room_code($db);
    $db->beginTransaction();

    try {
        $status = $local ? 'playing' : 'lobby';
        $phase = $local ? 'answering' : 'lobby';
        $insertRoom = $db->prepare(
            'INSERT INTO rooms (code, mode, host_id, status, phase, total_rounds, default_speed, started_at)
                 VALUES (:code, :mode, :host_id, :status, :phase, :total_rounds, :speed, :started_at)'
        );
        $insertRoom->execute([
            'code' => $code,
            'mode' => $local ? 'local' : 'multi',
            'host_id' => $userId,
            'status' => $status,
            'phase' => $phase,
            'total_rounds' => $rounds,
            'speed' => $speed,
            'started_at' => $local ? gmdate('Y-m-d H:i:s') : null,
        ]);
        $roomId = (int) $db->lastInsertId();

        $insertPlayer = $db->prepare('INSERT INTO room_players (room_id, user_id) VALUES (:room_id, :user_id)');
        $insertPlayer->execute(['room_id' => $roomId, 'user_id' => $userId]);
        $roomPlayerId = (int) $db->lastInsertId();

        $insertCategory = $db->prepare('INSERT INTO room_categories (room_id, category_id) VALUES (:room_id, :category_id)');
        foreach ($categories as $category) {
            $insertCategory->execute(['room_id' => $roomId, 'category_id' => $category['id']]);
        }

        if ($local) {
            create_round($db, $roomId, $roomPlayerId, 1, $speed, $letter, $speed === 'fast' ? 30 : 90);
        }

        $db->commit();
    } catch (Throwable $error) {
        $db->rollBack();
        throw $error;
    }

    return $code;
}

function create_round(PDO $db, int $roomId, int $callerId, int $number, string $speed, string $letter, int $seconds): int
{
    $startedAt = time();
    $insert = $db->prepare(
        'INSERT INTO rounds (room_id, number, caller_id, speed, letter, started_at, deadline_at)
         VALUES (:room_id, :number, :caller_id, :speed, :letter, :started_at, :deadline_at)'
    );
    $insert->execute([
        'room_id' => $roomId,
        'number' => $number,
        'caller_id' => $callerId,
        'speed' => $speed,
        'letter' => $letter,
        'started_at' => gmdate('Y-m-d H:i:s', $startedAt),
        'deadline_at' => gmdate('Y-m-d H:i:s', $startedAt + $seconds),
    ]);

    return (int) $db->lastInsertId();
}

function start_room(PDO $db, array $room, int $userId, string $letter): void
{
    $db->beginTransaction();
    try {
        $lock = $db->prepare('SELECT status, host_id, total_rounds, default_speed, fast_seconds, slow_seconds FROM rooms WHERE id = :id FOR UPDATE');
        $lock->execute(['id' => $room['id']]);
        $lockedRoom = $lock->fetch();
        if (!$lockedRoom || $lockedRoom['status'] !== 'lobby' || (int) $lockedRoom['host_id'] !== $userId) {
            throw new DomainException('Only the host can start a room that is still in the lobby.');
        }

        $player = find_room_player($db, (int) $room['id'], $userId);
        if (!$player) {
            throw new DomainException('You are not a member of this room.');
        }

        $start = $db->prepare(
            "UPDATE rooms SET status = 'playing', phase = 'answering', started_at = UTC_TIMESTAMP()
             WHERE id = :id"
        );
        $start->execute(['id' => $room['id']]);
        $speed = $lockedRoom['default_speed'];
        $seconds = (int) $lockedRoom[$speed . '_seconds'];
        create_round($db, (int) $room['id'], (int) $player['id'], 1, $speed, $letter, $seconds);
        $db->commit();
    } catch (Throwable $error) {
        $db->rollBack();
        throw $error;
    }
}

function update_room_settings(PDO $db, array $room, int $userId, int $rounds, string $speed): void
{
    $check = $db->prepare("SELECT 1 FROM rooms WHERE id = :id AND host_id = :host_id AND status = 'lobby'");
    $check->execute(['id' => $room['id'], 'host_id' => $userId]);
    if (!$check->fetchColumn()) {
        throw new DomainException('Only the host can change settings while the room is in the lobby.');
    }

    $sql = $db->prepare(
        "UPDATE rooms SET total_rounds = :rounds, default_speed = :speed
         WHERE id = :id AND host_id = :host_id AND status = 'lobby'"
    );
    $sql->execute([
        'rounds' => $rounds,
        'speed' => $speed,
        'id' => $room['id'],
        'host_id' => $userId,
    ]);
}
