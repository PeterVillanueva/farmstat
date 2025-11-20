<?php
/**
 * Helper Functions
 * PHP 8 Compatible
 */

function getActivityIcon(string $type): string {
    $icons = [
        'user' => 'user-plus',
        'campaign' => 'hand-holding-usd',
        'system' => 'chart-line',
        'alert' => 'exclamation-triangle',
        'login' => 'sign-in-alt'
    ];
    return $icons[$type] ?? 'circle';
}

function time_elapsed_string(?string $datetime, bool $full = false): string {
    if (empty($datetime)) return 'Never';
    
    try {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = [
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        ];
        
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    } catch (Exception $e) {
        return 'Invalid date';
    }
}

