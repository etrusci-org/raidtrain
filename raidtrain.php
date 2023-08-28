<?php
declare(strict_types=1);

namespace org\etrusci\raidtrain;
use DateTime;
use DateTimeZone;
use DateInterval;




class RaidTrain
{
    public string $name;
    public string $time_zone;
    public string|false $slot_file = './slot.txt';
    public string $diff_tpl_future = 'in %s';
    public string $diff_tpl_past = '%s ago';
    private null|array $event_data = null;


    public function __construct(string $name, string $time_zone, string $slot_file)
    {
        $this->name = $name;
        $this->time_zone = $time_zone;
        $this->slot_file = realpath($slot_file);

        date_default_timezone_set($this->time_zone);

        $this->bake_event_data();
    }


    public function get_event_data(bool $as_json = false): array|string
    {
        if (!$this->event_data) return [];

        if ($as_json) {
            return json_encode($this->event_data, flags: JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        }

        return $this->event_data;
    }


    public function format_diff(DateInterval $interval): string
    {
        if ($interval->days > 0) {
            $dump = $interval->format('%ad %hh %im');
        }
        else {
            $dump = $interval->format('%hh %im');
        }

        $dump = sprintf((!$interval->invert) ? $this->diff_tpl_future : $this->diff_tpl_past, $dump);

        return (!empty($dump)) ? $dump : 'error';
    }


    private function bake_event_data(): void
    {
        $dump = file($this->slot_file, flags: FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        $tz = new DateTimeZone($this->time_zone);
        $now = new DateTime('now', $tz);

        $slot = [];

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

            $slot[$group][] = [
                'start' => $start_str,
                'end' => $end_str,
                'dj' => $dj,
                'is_active' => ($start_diff->invert == 1 && $end_diff->invert == 0) ? true : false,
                'diff_start_human' => $this->format_diff($start_diff),
                'diff_start' => [
                    'd' => $start_diff->days,
                    'h' => $start_diff->h,
                    'm' => $start_diff->i,
                    's' => $start_diff->s,
                    'is_past' => boolval($start_diff->invert),
                ],
                'diff_end_human' => $this->format_diff($end_diff),
                'diff_end' => [
                    'd' => $end_diff->days,
                    'h' => $end_diff->h,
                    'm' => $end_diff->i,
                    's' => $end_diff->s,
                    'is_past' => boolval($end_diff->invert),
                ],
            ];
        }

        $this->event_data = [
            'name' => $this->name,
            'time_zone' => $this->time_zone,
            'time_now' => $now->format('Y-m-d H:i:s'),
            'slot' => $slot,
        ];
    }
}
