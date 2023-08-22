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

?>


