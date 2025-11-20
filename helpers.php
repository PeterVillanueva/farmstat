<?php
// helpers.php - Common helper functions

function getActivityIcon($type) {
    $icons = [
        'user' => 'user-plus',
        'campaign' => 'hand-holding-usd',
        'system' => 'chart-line',
        'alert' => 'exclamation-triangle',
        'login' => 'sign-in-alt'
    ];
    return $icons[$type] ?? 'circle';
}

function time_elapsed_string($datetime, $full = false) {
    if (empty($datetime)) return 'Never';
    
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}

function getUsers($conn) {
    try {
        $sql = "SELECT id, name, email, role, status, created_at FROM users ORDER BY created_at DESC LIMIT 50";
        $result = $conn->query($sql);
        $users = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
        }
        return $users;
    } catch (Exception $e) {
        return [];
    }
}

function getFarmers($conn) {
    try {
        $sql = "SELECT * FROM farmers ORDER BY created_at DESC LIMIT 20";
        $result = $conn->query($sql);
        $farmers = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $farmers[] = $row;
            }
        }
        return $farmers;
    } catch (Exception $e) {
        return [];
    }
}

function getCampaigns($conn) {
    try {
        $sql = "SELECT * FROM campaigns ORDER BY created_at DESC LIMIT 12";
        $result = $conn->query($sql);
        $campaigns = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $campaigns[] = $row;
            }
        }
        return $campaigns;
    } catch (Exception $e) {
        return [];
    }
}
?>