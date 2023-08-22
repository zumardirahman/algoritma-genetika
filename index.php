<!DOCTYPE html>
<html>
<head>
    <title>Hasil Algoritma Genetika</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <?php
    require_once 'GeneticAlgorithm.php';
    require_once 'db/connect.php';
    require_once 'helper.php';

    // Parameter algoritma genetika
    $populationSize = 100;
    $mutationRate = 0.01;
    $crossoverRate = 0.9;
    $maxGenerations = 100;

    $ga = new GeneticAlgorithm($pdo, $populationSize, $mutationRate, $crossoverRate, $maxGenerations);
    $ga->run();

    // Mendapatkan hasil terbaik dari algoritma genetika
    $bestSchedule = $ga->getBestSchedule();
//var_dump($bestSchedule);
    // Mengekstrak data mata kuliah, dosen, dan ruangan dari database
    $coursesArray = fetchAllCourses($pdo);
    $teachersArray = fetchAllTeachers($pdo);
    $roomsArray = fetchAllRooms($pdo);

    usort($bestSchedule->chromosome, "sortByDay");

    echo "<h2>Jadwal Terbaik:</h2>";
if (isset($bestSchedule->error)) {
    echo "<p>{$bestSchedule->error}</p>"; // Menampilkan pesan kesalahan jika jadwal tidak dapat dibuat
} else {
        echo "<table>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>Kode Mata Kuliah</th>";
        echo "<th>Nama Mata Kuliah</th>";
        echo "<th>SKS</th>";
        echo "<th>Ruangan</th>";
        echo "<th>Hari</th>";
        echo "<th>Waktu</th>";
        echo "<th>Dosen Pengajar</th>";
        echo "<th>Total Mahasiswa</th>";
        echo "<th>Pertemuan per Minggu</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";

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
            echo "<td>{$course['sks']}</td>";
            echo "<td>{$room['name']} (Capacity: {$room['capacity']} )</td>";
            echo "<td>{$day}</td>";
            echo "<td>{$startTime} - {$endTime}</td>";
            echo "<td>{$teacher['name']}</td>";
            echo "<td>{$course['total_students']}</td>";
            echo "<td>{$course['meetings_per_week']}</td>";
            echo "</tr>";
        }

        echo "</tbody>";
        echo "</table>";
    }
    ?>
</body>
</html>
