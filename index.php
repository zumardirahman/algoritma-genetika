<?php
require_once 'Schedule.php';
require_once 'GeneticAlgorithm.php';
require_once 'db/connect.php';
require_once 'helper.php'; // Menambahkan file helper

// Parameter algoritma genetika
$populationSize = 100;
$mutationRate = 0.01;
$crossoverRate = 0.9;
$maxGenerations = 100;

$ga = new GeneticAlgorithm($pdo, $populationSize, $mutationRate, $crossoverRate, $maxGenerations);
$ga->run();

// Tampilkan hasil
echo "<h2>Jadwal Terbaik:</h2>";
echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<thead>";
echo "<tr>";
echo "<th>Kode Mata Kuliah</th>";
echo "<th>Nama Mata Kuliah</th>";
echo "<th>Ruangan</th>";
echo "<th>SKS</th>";
echo "<th>Hari</th>";
echo "<th>Waktu</th>";
echo "<th>Dosen Pengajar</th>";
echo "<th>Total Mahasiswa</th>";
echo "<th>Pertemuan per Minggu</th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>";

$bestSchedule = $ga->getBestSchedule();

$coursesArray = fetchAllCourses($pdo);
$teachersArray = fetchAllTeachers($pdo);
$roomsArray = fetchAllRooms($pdo);

usort($bestSchedule->chromosome, "sortByDay");

foreach ($bestSchedule->chromosome as $class) {
    $parts = explode("-", $class);

    $courseCode = $parts[0];
    $roomName = $parts[1];
    $day = $parts[2];
    $startTime = $parts[3];
    $endTime = $parts[4];
    $teacherId = $parts[5];

    $course = $coursesArray[$courseCode];
    $teacher = $teachersArray[$teacherId];
    $room = $roomsArray[$roomName];

    echo "<tr>";
    echo "<td>{$course['code']}</td>";
    echo "<td>{$course['name']}</td>";
    echo "<td>{$room['name']} (Capacity: {$room['capacity']} )</td>";
    echo "<td>{$course['sks']}</td>";
    echo "<td>{$day}</td>";
    echo "<td>{$startTime} - {$endTime}</td>";
    echo "<td>{$teacher['name']}</td>";
    echo "<td>{$course['total_students']}</td>";
    echo "<td>{$course['meetings_per_week']}</td>";
    echo "</tr>";
}

echo "</tbody>";
echo "</table>";
?>