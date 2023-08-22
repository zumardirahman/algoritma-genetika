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

        // Logic untuk mengevaluasi fitness
        // Misalnya, jika tidak ada bentrokan waktu, tambah nilai fitness

        $schedule->fitness = $fitness;
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

    public function crossover($parent1, $parent2) {
        $childChromosome = [];
        $crossoverPoint = rand(1, count($parent1->chromosome) - 2);

        for ($i = 0; $i < count($parent1->chromosome); $i++) {
            if ($i < $crossoverPoint) {
                $childChromosome[] = $parent1->chromosome[$i];
            } else {
                $childChromosome[] = $parent2->chromosome[$i];
            }
        }

        return new Schedule($childChromosome);
    }

    public function mutate($schedule) {
        for ($i = 0; $i < count($schedule->chromosome); $i++) {
            if (rand(0, 1) < $this->mutationRate) {
                $schedule->chromosome[$i] = $this->generateRandomGene();
            }
        }
    }

    private function generateRandomGene() {
        // Logic serupa dengan generateRandomChromosome, tapi hanya menghasilkan satu gen
    }

    public function run() {
        for ($generation = 0; $generation < $this->maxGenerations; $generation++) {
            // Implementasi dari algoritma genetika
        }
    }

	public function getBestSchedule() {
		usort($this->population, function($a, $b) {
			return $b->fitness - $a->fitness;
		});
		return $this->population[0];
	}
	
	
private function generateTimeSlots($sksDuration = 50, $interval = 10, $breaks = []) {
    $breaks = 	[
					['startHour' => 12, 'startMinute' => 40, 'endHour' => 13, 'endMinute' => 0], // Istirahat siang
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


	
private function generateRandomChromosome() {
    $chromosome = [];
    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
    $courses = $this->pdo->query("SELECT * FROM tabel_courses")->fetchAll();
    $rooms = $this->pdo->query("SELECT * FROM tabel_rooms")->fetchAll();
	
    $maxAttempts = 10; // Menentukan jumlah maksimal percobaan sebelum menyerah

    foreach ($courses as $course) {
        $meetingCounts = $course['meetings_per_week'];
        $allocatedDays = []; // Menyimpan hari-hari di mana mata kuliah sudah dijadwalkan
        for ($i = 0; $i < $meetingCounts; $i++) {
            $success = false;
            $attempt = 0;

            while (!$success && $attempt < $maxAttempts) {
                $day = $days[array_rand($days)];
				
		$durationMinutes = $course['sks'] * 50;
        $timeSlots = $this->generateTimeSlots($durationMinutes);
		
                // Pastikan mata kuliah tidak dijadwalkan pada hari yang sama dalam seminggu
                while (in_array($day, $allocatedDays)) {
                    $day = $days[array_rand($days)];
                }

                $room = $this->selectRoom($course, $rooms, $chromosome, $day);
                $startTime = $this->selectTimeSlot($course, $room, $timeSlots, $chromosome, $day);
                $duration = $course['sks'] * 50; // Menghitung durasi dalam menit
				$endTime = date("H:i", strtotime("+$duration minute", strtotime($startTime))); // Menambahkan durasi ke startTime
                
                if (!$this->isRoomConflict($room, $course, $chromosome, $startTime, $endTime, $day)) {
                    $gene = "{$course['code']}-{$room['name']}-{$day}-{$startTime}-{$endTime}-{$course['teacher_id']}";
                    $chromosome[] = $gene;
                    $allocatedDays[] = $day;
                    $success = true;
                }

                $attempt++;
            }

            if ($attempt == $maxAttempts) {
					$zonk = "<small style='color:red'><i>Kosong</i></small>";
                   $gene_err = "{$course['code']}-{$room['name']}-{$zonk}-{$zonk}-{$zonk}-{$course['teacher_id']}";
                $chromosome[] = $gene_err;
            }
        }
    }

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

private function selectTimeSlot($course, $room, $timeSlots, $chromosome, $day) {
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