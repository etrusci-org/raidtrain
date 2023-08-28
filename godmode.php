<?php
declare(strict_types=1);

// ------------------------------------------------------------------------------------------------


const SLOT_FILE = './example/slot.txt'; # must exist and be writable by php/webserver
const PW_FILE = '.godmode-pw-example'; # optimally move this file outside the public webserver root


// ------------------------------------------------------------------------------------------------


session_start();


if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: ?');
}
else if (isset($_POST['pw'])) {
    $local_hash = trim(file_get_contents(PW_FILE));

    if (!password_verify(trim($_POST['pw']), $local_hash)) {
        session_destroy();
        header('Location: ?');
    }
    else {
        $_SESSION['godmode'] = true;
    }
}


if (!isset($_SESSION['godmode'])) {
    print('
    <form action="?" method="post">
        <label>
            Password:
            <input type="password" name="pw" required>
        </label>
        <input type="submit" value="login">
    </form>');
    exit(1);
}


if (!is_file(SLOT_FILE) || !is_writable(SLOT_FILE)) {
    print('SLOT_FILE does not exist or is not writable: '.SLOT_FILE);
    exit(1);
}

if (isset($_POST['slot_data'])) {
    $slot_data = trim($_POST['slot_data']);
    file_put_contents(SLOT_FILE, $slot_data, flags: LOCK_EX);
}

$slot_data = file_get_contents(SLOT_FILE);


// ------------------------------------------------------------------------------------------------
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { line-height: 1.5; font-family: sans-serif; font-size: 1rem; background-color: #111; color: #ccc; padding: 1rem; }
        textarea { font-family: monospace; font-size: 1rem; background-color: #222; color: #ccc; border: 1px solid #444; padding: .5rem; width: 100%; max-height: 60vh; }
        input { cursor: pointer; font-family: monospace; font-size: 1rem; background-color: #222; color: #ccc; border: 1px solid #444; padding: .5rem; }
        input:hover, input:focus { background-color: #333; color: #fff; }
    </style>
    <title>raidtrain godmode</title>
</head>
<body>
    <h1>Edit Slot Data</h1>
    <form action="?" method="post">
        <p>
            <textarea name="slot_data" cols="50" rows="35" required><?php print(htmlspecialchars($slot_data)); ?></textarea>
        </p>
        <input type="submit" value="save changes">
        <input type="submit" name="logout" value="logout">
    </form>
</body>
</html>
