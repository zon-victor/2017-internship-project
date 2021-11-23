<?php

require_once 'all.deductions.view.php';

class masDeductionsView extends allDeductionsView {
    
    public function renderMasView($deductions, $year, $peryear, $category) {
        echo $this->viewByCategory($deductions, $year, $peryear, $category);
    }
}
