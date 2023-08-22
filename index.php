<?php
require_once 'Schedule.php';
require_once 'GeneticAlgorithm.php';
require_once 'db/connect.php';

// Parameter algoritma genetika
$populationSize = 100;
$mutationRate = 0.01;
$crossoverRate = 0.9;
$maxGenerations = 100;

$ga = new GeneticAlgorithm($pdo, $populationSize, $mutationRate, $crossoverRate, $maxGenerations);
$ga->run();

// Tampilkan hasil (Ini hanya contoh sederhana, Anda dapat memodifikasi sesuai kebutuhan)
$bestSchedule = $ga->getBestSchedule();

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
echo "</tr>";
echo "</thead>";
echo "<tbody>";

$bestSchedule = $ga->getBestSchedule();
foreach ($bestSchedule->chromosome as $class) {
		$parts = explode("-", $class);

		$courseCode = $parts[0];
		$room = $parts[1];
		$startTime = $parts[2];
		$endTime = $parts[3];
		$teacherId = $parts[4];

    // Mengambil nama mata kuliah, hari, dan nama dosen dari database
    //$courseDetails = $pdo->query("SELECT name, day FROM tabel_courses WHERE code = '$courseCode'")->fetch();
    //$teacherName = $pdo->query("SELECT name FROM tabel_teachers WHERE id = $teacherId")->fetchColumn();

	$courseDetails = $pdo->query("SELECT 	tabel_courses.name, 
											tabel_courses.day, 
											tabel_courses.sks, 
											tabel_courses.total_students, 
											tabel_rooms.capacity 
                              FROM tabel_courses 
                              JOIN tabel_rooms ON tabel_rooms.name = '$room' 
                              WHERE tabel_courses.code = '$courseCode'")->fetch();

	$stmt = $pdo->prepare("SELECT name FROM tabel_teachers WHERE id = :teacherId");
	$stmt->bindParam(':teacherId', $teacherId);
	$stmt->execute();
	$teacherName = $stmt->fetchColumn();


    echo "<tr>";
    echo "<td>{$courseCode}</td>";
    echo "<td>{$courseDetails['name']}</td>";
    echo "<td>{$room} (Capacity: {$courseDetails['capacity']} )</td>";
    echo "<td>{$courseDetails['sks']}</td>";
    echo "<td>{$courseDetails['day']}</td>";
    echo "<td>{$startTime} - {$endTime}</td>";
    echo "<td>{$teacherName}</td>";
    echo "<td>{$courseDetails['total_students']}</td>";
    echo "</tr>";
}

echo "</tbody>";
echo "</table>";

