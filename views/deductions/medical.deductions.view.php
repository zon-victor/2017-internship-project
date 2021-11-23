<?php

require_once 'all.deductions.view.php';

class medicalDeductionsView extends allDeductionsView {
    
    public function renderMedicalView($deductions, $year, $peryear, $category) {
        echo $this->viewByCategory($deductions, $year, $peryear, $category);
    }
}
