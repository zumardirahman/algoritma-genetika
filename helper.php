<?php

function fetchAllCourses($pdo) {
    $stmt = $pdo->query("SELECT * FROM tabel_courses");
    $courses = [];
    while ($row = $stmt->fetch()) {
        $courses[$row['code']] = $row;
    }
    return $courses;
}

function fetchAllTeachers($pdo) {
    $stmt = $pdo->query("SELECT * FROM tabel_teachers");
    $teachers = [];
    while ($row = $stmt->fetch()) {
        $teachers[$row['id']] = $row;
    }
    return $teachers;
}

function fetchAllRooms($pdo) {
    $stmt = $pdo->query("SELECT * FROM tabel_rooms");
    $rooms = [];
    while ($row = $stmt->fetch()) {
        $rooms[$row['name']] = $row;
    }
    return $rooms;
}

// Fungsi untuk mengurutkan berdasarkan hari
function sortByDay($a, $b) {
    $daysOrder = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"];
    
    $partsA = explode("-", $a);
    $partsB = explode("-", $b);
    
    $dayA = $partsA[2];
    $dayB = $partsB[2];
    
    return array_search($dayA, $daysOrder) - array_search($dayB, $daysOrder);
}
?>


