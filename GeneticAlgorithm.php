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

	private function generateRandomChromosome() {
		$chromosome = [];

		$courses = $this->pdo->query("SELECT * FROM tabel_courses")->fetchAll();
		$rooms = $this->pdo->query("SELECT * FROM tabel_rooms")->fetchAll();
		$timeSlots = [
			"08:00", "09:00", "10:00", "11:00", "13:00", "14:00", "15:00"
		];

		foreach ($courses as $course) {
			$room = $this->selectRoom($course, $rooms, $chromosome);
			$startTime = $this->selectTimeSlot($course, $room, $timeSlots, $chromosome);

			// Menghitung waktu berakhir berdasarkan SKS
			$duration = $course['sks'];
			$endTime = date("H:i", strtotime("+$duration hour", strtotime($startTime)));

			$gene = "{$course['code']}-{$room['name']}-{$startTime}-{$endTime}-{$course['teacher_id']}";
			$chromosome[] = $gene;
		}

		return $chromosome;
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
	
	
	//tambahan helper
	
	// private function selectRoom($course, $rooms, $chromosome) {
		// $suitableRooms = array_filter($rooms, function($room) use ($course) {
			// return $room['capacity'] >= $course['total_students'];
		// });
		// shuffle($suitableRooms);

		// foreach ($suitableRooms as $room) {
			// if (!$this->isRoomConflict($room, $course, $chromosome)) {
				// return $room;
			// }
		// }

		// // Fallback jika tidak ada ruangan yang sesuai (ini harus dihandle lebih lanjut)
		// return $rooms[0];
	// }

	private function selectRoom($course, $rooms, $chromosome) {
		$suitableRooms = array_filter($rooms, function($room) use ($course) {
			return $room['capacity'] >= $course['total_students'];
		});
		shuffle($suitableRooms);

		foreach ($suitableRooms as $room) {
			if (!$this->isRoomConflict($room, $course, $chromosome)) {
				return $room;
			}
		}

		// Jika sampai ke sini, berarti tidak ada ruangan yang sesuai
		// Sebagai contoh, kita bisa memilih ruangan pertama yang tersedia sebagai fallback
		// Tetapi dalam skenario nyata, Anda mungkin ingin menampilkan pesan kesalahan atau mencoba strategi lain
		return $rooms[0];
	}

	private function isRoomConflict($room, $course, $chromosome) {
		foreach ($chromosome as $gene) {
			$parts = explode("-", $gene);
			$existingRoom = $parts[1];
			$existingStartTime = $parts[2];
			$existingEndTime = $parts[3];

			// Kita hanya perlu memeriksa apakah ruangan sudah dipesan pada waktu yang sama
			if ($existingRoom == $room['name'] && $existingStartTime == $parts[2] && $existingEndTime == $parts[3]) {
				return true;
			}
		}
		return false;
	}

	private function selectTimeSlot($course, $room, $timeSlots, $chromosome) {
		shuffle($timeSlots);

		foreach ($timeSlots as $time) {
			if (!$this->isTimeConflict($time, $course, $room, $chromosome)) {
				return $time;
			}
		}

		// Fallback jika tidak ada waktu yang sesuai (ini harus dihandle lebih lanjut)
		return $timeSlots[0];
	}	

	private function isTimeConflict($time, $course, $room, $chromosome) {
		$proposedEndTime = date("H:i", strtotime("+$course[sks] hour", strtotime($time)));

		foreach ($chromosome as $gene) {
			$parts = explode("-", $gene);
			$existingRoom = $parts[1];
			$existingStartTime = $parts[2];
			$existingEndTime = $parts[3];

			if ($existingRoom == $room['name'] && $time >= $existingStartTime && $proposedEndTime <= $existingEndTime) {
				return true;
			}
		}
		return false;
	}

}
?>