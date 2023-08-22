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
	
	
private function generateRandomChromosome() {
    $chromosome = [];
    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
    $courses = $this->pdo->query("SELECT * FROM tabel_courses")->fetchAll();
    $rooms = $this->pdo->query("SELECT * FROM tabel_rooms")->fetchAll();
    $timeSlots = ["08:00", "09:00", "10:00", "11:00", "13:00", "14:00", "15:00"];
    $maxAttempts = 10; // Menentukan jumlah maksimal percobaan sebelum menyerah

    foreach ($courses as $course) {
        $meetingCounts = $course['meetings_per_week'];
        $allocatedDays = []; // Menyimpan hari-hari di mana mata kuliah sudah dijadwalkan

        for ($i = 0; $i < $meetingCounts; $i++) {
            $success = false;
            $attempt = 0;

            while (!$success && $attempt < $maxAttempts) {
                $day = $days[array_rand($days)];

                // Pastikan mata kuliah tidak dijadwalkan pada hari yang sama dalam seminggu
                while (in_array($day, $allocatedDays)) {
                    $day = $days[array_rand($days)];
                }

                $room = $this->selectRoom($course, $rooms, $chromosome, $day);
                $startTime = $this->selectTimeSlot($course, $room, $timeSlots, $chromosome, $day);
                $duration = $course['sks'];
                $endTime = date("H:i", strtotime("+$duration hour", strtotime($startTime)));
                
                if (!$this->isRoomConflict($room, $course, $chromosome, $startTime, $endTime, $day)) {
                    $gene = "{$course['code']}-{$room['name']}-{$day}-{$startTime}-{$endTime}-{$course['teacher_id']}";
                    $chromosome[] = $gene;
                    $allocatedDays[] = $day;
                    $success = true;
                }

                $attempt++;
            }

            if ($attempt == $maxAttempts) {
                return ["error" => "Jadwal tidak dapat dibuat"]; // Mengembalikan pesan kesalahan jika mencapai batas maksimal percobaan
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

    $timeSlots = ["08:00", "09:00", "10:00", "11:00", "13:00", "14:00", "15:00"];

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
        $proposedEndTime = date("H:i", strtotime("+$course[sks] hour", strtotime($time)));

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

        if ($existingRoom == $room['name'] && $existingDay == $day && 
            ($time >= $existingStartTime && $proposedEndTime <= $existingEndTime)) {
            return true;
        }
    }
    return false;
}

}
?>