<?php

class allDeductionsView {
    
    public function allPerYear($deductions, $year) {
        ?><div id="yearly"><?php
        echo $this->statementHeading($year);
        echo $this->statementHeaders();
        if (isset($deductions['none'])) {
          echo '<div class="all_none">'.$deductions['none'].'</div>';
          return;
        } 
        echo $this->allPerMonth($deductions);
        ?></div><?php
    }
    
    public function allPerMonth($deductions) {
        $grand_total = 0;
        foreach ($deductions as $deduction) {
            $view = '<div id="'.$deduction[0]->month.'" class="monthly">';
            $view .= '<div class="monthly_heading">'.$deduction[0]->month.'</div>';
            $view .=  '<div class="transaction"></div>';
            $total = 0;
            foreach ($deduction as $payed) {
                $view .= '<div class="transaction">';
                $view .= '<div class="transaction_inst">'.$payed->institution.'</div>';
                $view .= '<div class="transaction_inst">'.$payed->reason.'</div>';
                $view .= '<div class="transaction_inst">'.$payed->amount.'</div>';
                $view .= '</div>';
                $total += $payed->amount;
            }
            $grand_total += $total;
            $view .= '<div class="total">R '.$total.'</div>';
            $view .= '</div>';
            echo $view;
        }
        echo $this->statementFooter($grand_total);
    }
    
    private function statementHeading($year) {
        $view = '<div id="yearly_heading">';
        $view.= $year.' DEDUCTIONS STATEMENT';
        $view.= '</div>';
        echo $view;
    }
    
    private function statementFooter($total) {
        $view = '<div id="yearly_total">';
        $view.= 'TOTAL AMOUNT DEDUCTED THIS MONTH IS R '.$total;
        $view.= '</div>';
        echo $view;
    }
    
    private function statementHeaders() {
        $view = '<div id="ysheadings">';
        $view.= '   <div id="" class="ys_headings">Company</div>';
        $view.= '   <div id="" class="ys_headings">Reason</div>';
        $view.= '   <div id="" class="ys_headings">Amount (Rands)</div>';
        $view.= '</div>';
        echo $view;
    }
    
    public function viewByCategory($deductions, $year, $peryear, $category) {
        if ($deductions === null) {
            return;
        }
        foreach ($deductions as $deduction) {
            echo $this->categoryStatementHeading($year, $deduction[0]->month, $category);
            echo $this->statementHeaders();
            $view = '<div id="'.$deduction[0]->month.'" class="monthly">';
            $view .= '<div class="monthly_heading">'.$deduction[0]->month.'</div>';
            $view .=  '<div class="transaction"></div>';
            $total = 0;
            foreach ($deduction as $payed) {
                $view .= '<div class="transaction">';
                $view .= '<div class="transaction_inst">'.$payed->institution.'</div>';
                $view .= '<div class="transaction_inst">'.$payed->reason.'</div>';
                $view .= '<div class="transaction_inst txt_ar">'.$payed->amount.'</div>';
                $view .= '</div>';
                $total += $payed->amount;
            }
            $view .= '<div class="total">R '.$total.'</div>';
            $view .= '</div>';
            echo $view;
            $this->statementYearlyTotals($peryear, $category);
        }
    }
    
    private function categoryStatementHeading($year, $month, $category) {
        $view = '<div id="cat_stmt_heading">';
        $view.= $category.' DEDUCTIONS - '.$month.' '.$year;
        $view.= '</div>';
        echo $view;
    }
    
    private function statementYearlyTotals ($total, $category) {
        echo '<div id="cat_totals">';
        foreach ($total as $deducted => $value) {
            $view = '<div class="cat_heading">YEARLY '.$category.' DEDUCTIONS</div>'
                    .   '<div class="transaction mt10">'
                    .'      <div class="transaction_inst">All Institutions</div>'
                    .'      <div class="transaction_inst txt_ar">Consolidated ('.$deducted.')</div>'
                    .'      <div class="transaction_inst txt_ar">'.$value[0]->total.'</div>'
                    .'  </div>';
            echo $view;
        }
        echo '</div>';
    }
    
}
