<?php
class Schedule {
    public $chromosome;
    public $fitness;

    public function __construct($chromosome = null) {
        $this->chromosome = $chromosome;
        $this->fitness = 0;
    }
}
?>