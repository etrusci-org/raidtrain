<?php
declare(strict_types=1);

namespace org\etrusci\raidtrain;
use DateTime;
use DateTimeZone;




class RaidTrain
{
    public string $name;
    public string $time_zone;
    public string|false $slot_file = './slot.txt';
    private null|array $slot_data = null;


    public function __construct(string $name, string $time_zone, string $slot_file)
    {
        $this->name = $name;
        $this->time_zone = $time_zone;
        $this->slot_file = realpath($slot_file);

        date_default_timezone_set($this->time_zone);

        $this->load_slot_data();
    }


    public function get_slot_data(bool $as_json = false): array|string
    {
        if (!$this->slot_data) return [];

        if ($as_json) {
            return json_encode($this->slot_data, flags: JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        }

        return $this->slot_data;
    }


    private function load_slot_data(): void
    {
        $dump = file($this->slot_file, flags: FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        $tz = new DateTimeZone($this->time_zone);
        $now = new DateTime('now', $tz);
        $data = [];

        foreach ($dump as $k => $v) {
            if (str_starts_with($v, '#')) {
                unset($dump[$k]);
                continue;
            }

            $line = explode('|', $v, 3);
            if (count($line) != 3) continue;

            $start_str = trim($line[0]);
            $end_str = trim($line[1]);
            $dj = trim($line[2]);

            if (!$dj) $dj = null;

            $group = substr($start_str, 0, 10);

            $start = new DateTime($start_str, $tz);
            $end = new DateTime($end_str, $tz);

            $start_diff = $now->diff($start);
            $end_diff = $now->diff($end);


            $data[$group][] = [
                'start' => $start_str,
                'end' => $end_str,
                'dj' => $dj,
                'diff_start' => $start_diff,
                'diff_end' => $end_diff,
            ];
        }

        $this->slot_data = $data;
    }
}













// ------------------------------------------------------------------------------------------------
// Test

$App = new RaidTrain(
    name: 'Foo Event',
    time_zone: 'Europe/Zurich', # for valid timezone values see https://www.php.net/manual/en/timezones.php
    slot_file: './slot.example.txt',
);

// print_r($App);

$foo = $App->get_slot_data();
// $foo = $App->get_slot_data(as_json: true);
print_r($foo);
