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
		];  // contoh slot waktu

		foreach ($courses as $course) {
			// Pilih ruangan yang kapasitasnya cukup untuk jumlah mahasiswa course
			$suitableRooms = array_filter($rooms, function($room) use ($course) {
				return $room['capacity'] >= $course['total_students'];
			});

			if (empty($suitableRooms)) {
				// Handle situasi dimana tidak ada ruangan yang cukup
				// Anda bisa menggantinya dengan logika lain sesuai kebutuhan
				continue;
			}

			$room = $suitableRooms[array_rand($suitableRooms)];
			$startTime = $timeSlots[array_rand($timeSlots)];

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

}
?>