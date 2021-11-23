<?php

require_once 'all.deductions.view.php';

class insuranceDeductionsView extends allDeductionsView {
    public function renderInsuranceView($deductions, $year, $peryear, $category) {
        echo $this->viewByCategory($deductions, $year, $peryear, $category);
    }
}
