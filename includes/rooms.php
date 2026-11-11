<?php
function rooms_file()
{
    return dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'game_rooms.json';
}

function load_rooms()
{
    $file = rooms_file();
    if (!file_exists($file)) {
        return [];
    }

    $contents = file_get_contents($file);
    if ($contents === false) {
        throw new RuntimeException('Could not read the room list.');
    }

    $rooms = json_decode($contents, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($rooms)) {
        throw new RuntimeException('The room list is not valid JSON.');
    }

    return $rooms;
}

function save_rooms($rooms)
{
    $contents = json_encode($rooms, JSON_PRETTY_PRINT);
    if ($contents === false || file_put_contents(rooms_file(), $contents, LOCK_EX) === false) {
        throw new RuntimeException('Could not save the room list.');
    }
}

function new_room_code($rooms)
{
    do {
        $code = '';
        for ($i = 0; $i < 4; $i++) {
            $code .= chr(random_int(65, 90));
        }
    } while (isset($rooms[$code]));

    return $code;
}

function escape_html($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
