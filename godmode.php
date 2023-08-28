<?php
declare(strict_types=1);

session_start();


// ------------------------------------------------------------------------------------------------


const SLOT_FILE = './example/slot.txt'; # must exist and be writable
const PW_FILE = '.godmode-pw'; # optimally move this file outside the public webserver root


// ------------------------------------------------------------------------------------------------


if (isset($_POST['pw'])) {
    $local_hash = trim(file_get_contents(PW_FILE));

    if (password_verify(trim($_POST['pw']), $local_hash)) {
        $_SESSION['godmode'] = true;
    }
    else {
        session_destroy();
    }
}


if (!isset($_SESSION['godmode'])) {
    print('
    <form action="?" method="post">
        <input type="password" name="pw">
        <input type="submit" value="do not push this button">
    </form>');
    exit(1);
}


// ------------------------------------------------------------------------------------------------


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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2.0.0-alpha1/css/pico.min.css">
    <title>raidtrain godmode</title>
</head>
<body>
    <h1>edit slots</h1>
    <form action="?" method="post">
        <textarea name="slot_data" rows="20"><?php print(htmlspecialchars($slot_data)); ?></textarea>
        <input type="submit" value="save changes">
    </form>
</body>
</html>
