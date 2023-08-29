<?php
// Also see README.md
// ------------------------------------------------------------------------------------------------
// This example shows how you can output the event data in the HTML format.
//
// Webbrowser:
// 1. Copy the raidtrain/ directory to your webserver
// 2. Navigate to this example page in a webbrowser
//
// Commandline:
// 1. cd path/to/raidtrain/example
// 2. php usage-html-output.php
// ------------------------------------------------------------------------------------------------


// Require/include the class file
require __DIR__.'/../raidtrain.php';


// Optionally aliasing the class so we don't have to refer to it as \org\etrusci\raidtrain\RaidTrain
use org\etrusci\raidtrain\RaidTrain;


// Init the class
$App = new RaidTrain(
    name: 'Foo Event',
    time_zone: 'Europe/Zurich', # for valid timezone values see https://www.php.net/manual/en/timezones.php
    slot_file: './slot.txt',
);


// Get the event data as array (default)
$event = $App->get_event_data();


// Start of HTML output
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .past { color: gray; }
        .future { color: green; }
        .day-header { font-size: 2rem; }
        body { line-height: 1.5; font-family: sans-serif; background-color: black; color: silver; padding: 1rem; }
        table { border-collapse: collapse; text-align: left; }
        th, td { vertical-align: top; padding: .5rem; }
        tr.active td { border: 1px dotted yellow; }
    </style>
    <title>raidtrain example usage-html-output</title>
</head>
<body>
    <h1><?php print($App->name) ?></h1>
    <p>All times = <?php print($App->time_zone); ?></p>

    <table>
        <thead>
            <tr>
                <th>Start</th>
                <th>End</th>
                <th>DJ</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Start looping tru event slot data
            // Slot data is grouped by day, hence on the first level, each key is the date string of the day
            foreach ($event['slot'] as $day => $day_slots)
            {
                printf('
                    <tr>
                        <td class="day-header" colspan="3">%1$s</td>
                    </tr>',
                    $day
                );

                // Loop tru slots of the current day
                foreach ($day_slots as $slot)
                {
                    printf('
                        <tr class="%8$s">
                            <td>
                                %2$s<br>
                                <span class="%6$s">%4$s</span>
                            </td>
                            <td>
                                %3$s<br>
                                <span class="%7$s">%5$s</span>
                            </td>
                            <td>%1$s</td>
                        </tr>',
                        $slot['dj'] ?? '<em>free slot</em>',
                        $slot['start'],
                        $slot['end'],
                        $slot['diff_start_human'],
                        $slot['diff_end_human'],
                        ($slot['diff_start']['is_past']) ? 'past' : 'future',
                        ($slot['diff_end']['is_past']) ? 'past' : 'future',
                        ($slot['is_active']) ? 'active' : '',
                    );
                }
            }
            ?>
        </tbody>
    </table>
</body>
</html>
