<?php
require_once 'Schedule.php';
require_once 'db/connect.php';

class GeneticAlgorithm {
    private $pdo;
    private $population;
    private $populationSize;
    private $mutationRate;
    private $crossoverRate;
    private $maxGenerations;

    public function __construct($pdo, $populationSize, $mutationRate, $crossoverRate, $maxGenerations) {
        $this->pdo = $pdo;
        $this->populationSize = $populationSize;
        $this->mutationRate = $mutationRate;
        $this->crossoverRate = $crossoverRate;
        $this->maxGenerations = $maxGenerations;
        $this->initializePopulation();
    }

    private function initializePopulation() {
        $this->population = [];
        for ($i = 0; $i < $this->populationSize; $i++) {
            $chromosome = $this->generateRandomChromosome();
            $this->population[] = new Schedule($chromosome);
        }
    }

public function calculateFitness($schedule) {
    $fitness = 0;

    foreach ($schedule->chromosome as $gene) {
        $parts = explode("-", $gene);
        $courseCode = $parts[0];
        $roomName = $parts[1];
        $day = $parts[2];
        $startTime = $parts[3];
        $endTime = $parts[4];
        $teacherId = $parts[5];

        // Cek konflik ruangan
        if (!$this->isRoomConflict($room, $course, $schedule->chromosome, $startTime, $endTime, $day)) {
            $fitness++;
        }

        // Cek konflik waktu dosen
        if (!$this->isTeacherTimeConflict($teacherId, $schedule->chromosome, $startTime, $endTime, $day)) {
            $fitness++;
        }
    }

    $schedule->fitness = $fitness;
}

private function isTeacherTimeConflict($teacherId, $chromosome, $proposedStartTime, $proposedEndTime, $day) {
    foreach ($chromosome as $gene) {
        $parts = explode("-", $gene);
        $existingTeacherId = $parts[5];
        $existingDay = $parts[2];
        $existingStartTime = $parts[3];
        $existingEndTime = $parts[4];

        if ($existingTeacherId == $teacherId && $existingDay == $day && 
            (($existingStartTime >= $proposedStartTime && $existingStartTime < $proposedEndTime) ||
            ($existingEndTime > $proposedStartTime && $existingEndTime <= $proposedEndTime) ||
            ($proposedStartTime >= $existingStartTime && $proposedStartTime < $existingEndTime) ||
            ($proposedEndTime > $existingStartTime && $proposedEndTime <= $existingEndTime))) {
            return true;
        }
    }
    return false;
}

    public function selection() {
        $totalFitness = array_sum(array_column($this->population, 'fitness'));
        $pick = mt_rand(0, $totalFitness);

        $current = 0;
        foreach ($this->population as $schedule) {
            $current += $schedule->fitness;
            if ($current > $pick) {
                return $schedule;
            }
        }
        return $this->population[0];
    }


public function crossover(array $parent1, array $parent2) {
    $crossoverPoint = mt_rand(1, count($parent1) - 1);

    $child1 = array_merge(array_slice($parent1, 0, $crossoverPoint), array_slice($parent2, $crossoverPoint));
    $child2 = array_merge(array_slice($parent2, 0, $crossoverPoint), array_slice($parent1, $crossoverPoint));

    // Pastikan anak-anak adalah kromosom valid
    $child1 = $this->validateChromosome($child1);
    $child2 = $this->validateChromosome($child2);

    return [$child1, $child2];
}

public function mutate($chromosome) {
    $randomGene = $this->generateRandomGene();
    $randomIndex = mt_rand(0, count($chromosome) - 1);

    $chromosome[$randomIndex] = $randomGene;

    // Pastikan kromosom adalah valid setelah mutasi
    return $this->validateChromosome($chromosome);
}

    private function generateRandomGene() {
        // Logic serupa dengan generateRandomChromosome, tapi hanya menghasilkan satu gen
    }

    public function run() {
        for ($generation = 0; $generation < $this->maxGenerations; $generation++) {
            // Implementasi dari algoritma genetika
        }
    }
	
public function runGA() {
    $population = [];
    for ($i = 0; $i < $this->populationSize; $i++) {
        $population[] = $this->generateRandomChromosome();
    }
    
    for ($generation = 0; $generation < $this->maxGenerations; $generation++) {
        $newPopulation = [];

        while (count($newPopulation) < $this->populationSize) {
            $parent1 = $this->rouletteWheelSelection($population);
            $parent2 = $this->rouletteWheelSelection($population);

            if (mt_rand(0, 1) < $this->crossoverRate) {
                list($child1, $child2) = $this->crossover($parent1, $parent2);

                if (mt_rand(0, 1) < $this->mutationRate) {
                    $child1 = $this->mutate($child1);
                }
                if (mt_rand(0, 1) < $this->mutationRate) {
                    $child2 = $this->mutate($child2);
                }

                $newPopulation[] = $child1;
                $newPopulation[] = $child2;
            } else {
                $newPopulation[] = $parent1;
                $newPopulation[] = $parent2;
            }
        }

        $population = $newPopulation;
    }

    // Setelah semua generasi selesai, kita mengembalikan kromosom dengan fitness tertinggi
    usort($population, function($a, $b) {
        return $b['fitness'] - $a['fitness'];
    });

    return $population[0];
}

	public function getBestSchedule() {
		usort($this->population, function($a, $b) {
			return $b->fitness - $a->fitness;
		});
		return $this->population[0];
	}

private function sortCoursesByCapacity() {
    // Mengambil data kursus dari database
    $query = $this->pdo->prepare("SELECT * FROM tabel_courses");
    $query->execute();
    $this->courses = $query->fetchAll(PDO::FETCH_ASSOC);

    // Mengurutkan $this->courses berdasarkan 'total_students'
    usort($this->courses, function($a, $b) {
        return $b['total_students'] - $a['total_students'];
    });
}

	
	
private function generateTimeSlots($sksDuration = 50, $interval = 10, $breaks = []) {
    $breaks = 	[
					['startHour' => 12, 'startMinute' => 40, 'endHour' => 13, 'endMinute' => 30], // Istirahat siang
					['startHour' => 11, 'startMinute' => 30, 'endHour' => 13, 'endMinute' => 30]  // Istirahat Jumat
				];

	$startHour = 7;
    $startMinute = 30;
    $endHour = 15;
    $endMinute = 0;

    $totalMinutes = $sksDuration + $interval;
    $timeSlots = [];
    $currentHour = $startHour;
    $currentMinute = $startMinute;

    while ($currentHour < $endHour || ($currentHour == $endHour && $currentMinute < $endMinute)) {
        $currentTimeInMinutes = $currentHour * 60 + $currentMinute;

        foreach ($breaks as $break) {
            $breakStart = $break['startHour'] * 60 + $break['startMinute'];
            $breakEnd = $break['endHour'] * 60 + $break['endMinute'];

            if ($currentTimeInMinutes + $sksDuration > $breakStart && $currentTimeInMinutes < $breakEnd) {
                $currentHour = floor($breakEnd / 60);
                $currentMinute = $breakEnd % 60;
                continue 2; // Lanjut ke waktu berikutnya setelah istirahat
            }
        }

        $time = str_pad($currentHour, 2, '0', STR_PAD_LEFT) . ":" . str_pad($currentMinute, 2, '0', STR_PAD_LEFT);
        $timeSlots[] = $time;

        $currentMinute += $totalMinutes;
        while ($currentMinute >= 60) {
            $currentHour++;
            $currentMinute -= 60;
        }
    }

    return $timeSlots;
}


	
public function generateRandomChromosome() {
    $chromosome = [];
    
    // Urutkan kursus berdasarkan kapasitas dari yang terbesar ke yang terkecil
    $this->sortCoursesByCapacity(); // Pemanggilan tanpa argumen

    foreach ($this->courses as $course) { // Gunakan $this->courses di sini
        $room = $this->selectRoomForCourse($course, $chromosome);
        $day = $this->selectDay($course, $chromosome);
        $timeslot = $this->selectTimeSlot($course, $chromosome);
        $teacherId = $this->selectTeacher($course, $chromosome);
        
        if ($room && $day && $timeslot && $teacherId) {
            $gene = $course['code'] . "-" . $room['name'] . "-" . $day . "-" . $timeslot['start'] . "-" . $timeslot['end'] . "-" . $teacherId;
            array_push($chromosome, $gene);
        }
    }
    return $chromosome;
}

private function selectDay() {
    $days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"];
    return $days[array_rand($days)];
}

private function selectTimeSlot($course) {
    // Misalkan setiap SKS berdurasi 1 jam dan waktu kelas dimulai dari 8 pagi sampai 5 sore.
    $startHour = 8;
    $endHour = 17; // 5 sore
    $courseDuration = $course['sks'];

    $latestStartHour = $endHour - $courseDuration;
    $selectedStartHour = rand($startHour, $latestStartHour);

    $selectedEndHour = $selectedStartHour + $courseDuration;

    return [
        "start" => $selectedStartHour . ":00",
        "end" => $selectedEndHour . ":00"
    ];
}

private function selectTeacher($course) {
    // Misalkan setiap mata kuliah hanya memiliki satu dosen pengajar
    return $course['teacher_id'];
}


private function selectRoomForCourse($course, $chromosome) {
    // Untuk saat ini, kita hanya akan memilih ruangan pertama dari database sebagai contoh
    // Anda dapat memodifikasi ini untuk memilih ruangan berdasarkan kapasitas, ketersediaan, dll.

    $stmt = $this->pdo->prepare("SELECT * FROM tabel_rooms LIMIT 1");
    $stmt->execute();
    $room = $stmt->fetch(PDO::FETCH_ASSOC);

    return $room;
}


public function rouletteWheelSelection($population) {
    $totalFitness = array_sum(array_column($population, 'fitness'));
    $randomValue = mt_rand(0, $totalFitness);
    $cumulative = 0;

	foreach ($population as $chromosome) {
		if(isset($chromosome['fitness'])) {
			$cumulative += $chromosome['fitness'];
			if ($randomValue <= $cumulative) {
				return $chromosome;
			}
		}
	}

}


public function validateChromosome($chromosome) {
    // Pemeriksaan untuk konflik dan perbaikan
    // Anda bisa memasukkan logika validasi seperti yang sudah Anda buat di `generateRandomChromosome`
    // untuk memastikan kromosom tetap memenuhi aturan yang Anda tetapkan.
    return $chromosome;
}




private function selectRoom($course, $rooms, $chromosome, $day) {
    $suitableRooms = array_filter($rooms, function($room) use ($course) {
        return $room['capacity'] >= $course['total_students'];
    });
    shuffle($suitableRooms);

    $durationMinutes = $course['sks'] * 50;
    $timeSlots = $this->generateTimeSlots($durationMinutes);

    foreach ($suitableRooms as $room) {
        foreach ($timeSlots as $time) {
            $proposedStartTime = $time;
            $proposedEndTime = date("H:i", strtotime("+$course[sks] hour", strtotime($time)));
            
            if (!$this->isRoomConflict($room, $course, $chromosome, $proposedStartTime, $proposedEndTime, $day)) {
                return $room;
            }
        }
    }
    return $rooms[0]; // fallback jika tidak ditemukan ruangan tanpa konflik
}

private function isRoomConflict($room, $course, $chromosome, $proposedStartTime, $proposedEndTime, $day) {
    foreach ($chromosome as $gene) {
        $parts = explode("-", $gene);
        $existingRoom = $parts[1];
        $existingDay = $parts[2];
        $existingStartTime = $parts[3];
        $existingEndTime = $parts[4];

        if ($existingRoom == $room['name'] && $existingDay == $day &&
            (($existingStartTime >= $proposedStartTime && $existingStartTime < $proposedEndTime) ||
            ($existingEndTime > $proposedStartTime && $existingEndTime <= $proposedEndTime) ||
            ($proposedStartTime >= $existingStartTime && $proposedStartTime < $existingEndTime) ||
            ($proposedEndTime > $existingStartTime && $proposedEndTime <= $existingEndTime))) {
            return true;
        }
    }
    return false;
}

private function selectTimeSlotX($course, $room, $timeSlots, $chromosome, $day) {
    shuffle($timeSlots);

    foreach ($timeSlots as $time) {
        $proposedEndTime = date("H:i", strtotime("+$course[sks] * 50 minute", strtotime($time))); // Menambahkan durasi ke waktu slot

        if (!$this->isTimeConflict($time, $course, $room, $chromosome, $day, $proposedEndTime)) {
            return $time;
        }
    }
    return $timeSlots[0]; // fallback
}

private function isTimeConflict($time, $course, $room, $chromosome, $day, $proposedEndTime) {
    foreach ($chromosome as $gene) {
        $parts = explode("-", $gene);
        $existingRoom = $parts[1];
        $existingDay = $parts[2];
        $existingStartTime = $parts[3];
		$existingEndTime = $parts[4];
		
		if (is_numeric($proposedEndTime)) {
			$proposedStartTime = $proposedEndTime - ($course['sks'] * 50);
		
			if ($existingRoom == $room['name'] &&
				(($existingStartTime >= $proposedStartTime && $existingStartTime < $proposedEndTime) ||
				($existingEndTime > $proposedStartTime && $existingEndTime <= $proposedEndTime) ||
				($proposedStartTime >= $existingStartTime && $proposedStartTime < $existingEndTime) ||
				($proposedEndTime > $existingStartTime && $proposedEndTime <= $existingEndTime))) {
				return true;
			} else {
				return false;
			}
		}
    }
    return false;
}

}
?>