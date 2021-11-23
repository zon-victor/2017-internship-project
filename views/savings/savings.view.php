<?php

class savingsView {

  public function renderTargetBasedCalculator() {
    $goal_label = "What is the name of your goal? ";
    $target_label = "How much do you intend to save? ";
    $interest_label = "How much interest do you earn? ";
    $interest_period_label = "What is the interest period? ";
    $period_label = "What will be the savings period ?";
    ?>
    <div class="savingsHeader afford">CALCULATE HOW MUCH YOU MUST SAVE (monthly) TO REACH A GIVEN A MOUNT</div>
    <div class="savingsCalc afford">
      <script type="text/javascript" src="js/savings.js"></script>
      <form name="amountCalculation" id="amountCalculation" method="POST" action="savings/calculate/target">
        <?= $goal_label ?> <input type="text" name="goal" id="sgoal" class="affordInput" placeholder="specify goal"><br>
        <?= $target_label ?> <input type="text" name="target" id="starget" class="affordInput" placeholder="target amount"><br>
        <?= $interest_label ?> <input type="text" name="interest" id="sinterest" class="affordInput" placeholder="%"><br>
        <?= $period_label ?> <input type="text" name="years" id="syears" class="affordInput" placeholder="Years">
        <input type="text" name="months" id="smonths" class="affordInput" placeholder="Months"><br>
        <?= $interest_period_label ?> <select id="sinterest_period" name="interest_period" class="affordInput">
          <option id="pyears" value="yearly" selected="selected">Yearly</option>
          <option id="pmonths" value="monthly">Monthly</option>
        </select><br>
        <input type="submit" name="calculate_amount" id="calculate_amount" onclick="amountBasedCalculator(event)" value="Calculate Amount">
      </form>
      <hr>
      <div id="savingsCalcResults">
        <div id="savingsCalcHeader">RESULTS</div>
        <hr>
        <div id="savingsCalcContent"></div>
      </div>
    </div>
    <?php
  }

  public function renderTermBasedCalculator() {
    $goal_label = "Name of your goal ";
    $period_label = "What is the investment period? ";
    $amount_label = "How much is the monthly installment? ";
    $interest_label = "What is the interest rate? ";
    $interest_period_label = "What is the interest period? ";
    $ioi_label = "Will you earn interest on interest? ";
    ?>
    <div class="savingsHeader afford">CALCULATE HOW MUCH YOU WILL HAVE AFTER A GIVEN PERIOD</div>
    <div class="savingsCalc afford">
      <script type="text/javascript" src="js/savings.js"></script>
      <form name="periodicCalculation" id="periodicCalculation" method="POST" action="savings/calculate/term">
        <?= $goal_label ?> <input type="text" name="goal" id="sgoal" class="affordInput" placeholder="specify goal"><br>
        <?= $amount_label ?> <input type="text" name="amount" id="samount" class="affordInput" placeholder="monthly amount"><br>
        <?= $interest_label ?> <input type="text" name="interest" id="sinterest" class="affordInput" placeholder="%"><br>
        <?= $period_label ?> <input type="text" name="years" id="syears" class="affordInput" placeholder="Years">
        <input type="text" name="months" id="smonths" class="affordInput" placeholder="Months"><br>
        <?= $interest_period_label ?> <select id="sinterest_period" name="interest_period" class="affordInput">
          <option id="pyears" value="yearly" selected="selected">Yearly</option>
          <option id="pmonths" value="monthly">Monthly</option>
        </select><br>
        <?= $ioi_label ?> <select id="ioi" name="ioi" class="affordInput">
          <option id="ioi_yes" value="yes" selected="selected">Yes</option>
          <option id="ioi_no" value="no">No</option>
        </select><br>
        <input type="submit" name="calculate_term" id="calculate_term" onclick="periodBasedCalculator(event)" value="Calculate Total">
      </form>
      <hr>
      <div id="termCalcResults">
        <div id="termCalcHeader">RESULTS</div>
        <hr>
        <div id="termCalcContent"></div>
      </div>
    </div>
    <?php
  }
  
  public function renderGroupedGoals() {
    ?>
    <div id="groupedGoals">
      <div id="ggHeader"></div>
      <div id="gg" class="gg">
        <div class="ggH_">
          <button class="goalMenuBtn" data-target="ggUninitialized" onclick="renderSpecificGoals(this, event)">UNINITIALIZED</button>
          <button class="goalMenuBtn" data-target="ggInitialized" onclick="renderSpecificGoals(this, event)">INITIALIZED</button>
          <button class="goalMenuBtn" data-target="ggReached" onclick="renderSpecificGoals(this, event)">REACHED</button>
        </div>
        <div class="ggContent" id="ggContent">
          <div class="ggSubContent" id="ggUninitialized"><?= $this->renderUninitializedGoalsOptions()?></div>
          <div class="ggSubContent" id="ggInitialized"><?= $this->renderInitializedGoalsOptions()?></div>
          <div class="ggSubContent" id="ggReached"><?= $this->renderReachedGoalsOptions()?></div>
          <div class="ggSubContent _ggLoad">MANAGE SAVINGS GOALS BY CATEGORY</div>
        </div>
      </div>
    </div>
    <?php
  }
  
  public function renderUninitializedGoalsOptions() {
    ?>
    <button class="all_goals" data-status="uninitialized" onclick="deleteAllGoals(this, event)">Delete All</button>
    <button class="all_goals" data-status="uninitialized" id="manageUninitialized" data-clicked="clicked" onclick="manageAllGoals(this, event)">Manage All</button>
    <button class="all_goals" data-status="uninitialized" onclick="startAllGoals(this, event)">Start All</button>
    <?php
  }
 
  public function renderInitializedGoalsOptions() {
    ?>
    <button class="all_goals" data-status="initialized" onclick="deleteAllGoals(this, event)">Delete All</button>
    <button class="all_goals" data-status="initialized" id="manageInitialized" data-clicked="clicked" onclick="manageAllGoals(this, event)">Manage All</button>
    <?php
  }
    
  public function renderReachedGoalsOptions() {
    ?>
    <button class="all_goals" data-status="reached" onclick="deleteAllGoals(this, event)">Delete All</button>
    <button class="all_goals" data-status="reached" data-clicked="clicked" id="manageReached" onclick="manageAllGoals(this, event)">Manage All</button>
    <?php
  }
  
  public function renderGoals($data, $status) {
    ?>
    <table class="goals_wrapper">
      <tr>
        <th rowspan="1" colspan="4" class="goals_header">Manage <?= $status ?> goals</th>
      </tr>
      <tr>
        <td class="goals_list">
          <?php
          foreach ($data as $goal) {
            echo '<a href="#" class="goal_name" data-status="'.$status.'" id="'.$goal['id_budget'].'" data-goal="'.$goal['id_budget'].'" onclick="loadGoalDetails(this, event)">'.$goal['goal'].'</a>';
          }
          ?>
        </td>
        <td class="goal_details">
          <div style="position: relative; color: #708090; width: 100%; height: 100%; line-height: 100%; text-align: center; font-size: 16px; font-weight: bold">
            SELECT A GOAL FROM LISTED GOALS
          </div>
        </td>
      </tr>
    </table>
    <?php
  }
  
  public function renderUninitializedGoalDetails($goal) {
    $period = explode('.', $goal['period']);
    if ($period[0] > 1) {
      $years = 'years';
    } else {
      $years = 'year';
    }
    
    if ($period[1] > 1) {
      $months = 'months';
    } else {
      $months = 'month';
    }
    
    ?>
    <table>
      <tr><td>Goal Type</td><td><?= $goal['type']?></td></tr>
      <tr><td>Monthly Investment</td><td><?= $goal['amount']?></td></tr>
      <tr><td>End of Investment Amount</td><td><?= $goal['target']?></td></tr>
      <tr><td>Period of Investment</td><td><?= $period[0]?> <?= $years ?> and <?= $period[1]?> <?= $months ?></td></tr>
      <tr><td>Interest Rate</td><td><?= $goal['interest']?></td></tr>
      <tr><td>Interest Period</td><td><?= $goal['interest_period']?></td></tr>
      <tr><td>Interest on Interest</td><td><?= $goal['ioi']?></td></tr>
      <tr><td>Goal Status</td><td><?= $goal['status']?></td></tr>
      <tr><td>Goal Options</td><td><button data-period="<?= $period[0]?> <?= $years ?> and <?= $period[1]?> <?= $months ?>" data-status="<?= $goal['status']?>" id="<?=$goal['id_budget']?>" onclick="initializeGoal(this, event)">Initialize</button></td><td><button data-status="<?= $goal['status']?>" id="<?=$goal['id_budget']?>" onclick="deleteGoal(this, event)">Delete</button></td></tr>
    </table>
    <?php
  }
  
  public function eta($start, $end) {
    $date1 = new DateTime($start);  
    $date2 = $date1->diff(new DateTime($end));  
    return $date2; 
  }
    
  public function renderInitializedGoalDetails($goal) {
    $period = explode('.', $goal['period']);
    if ($period[0] > 1) {
      $years = 'years';
    } else {
      $years = 'year';
    }
    
    if ($period[1] > 1) {
      $months = 'months';
    } else {
      $months = 'month';
    }
    
   // Calculate remaining time to reach goal
    $today = date("Y-m-d");
    $date = $this->eta($today, $goal['end_date']);
    $eta = "$date->y year(s), $date->m month(s) and $date->d day(s)";
    
    // Calculate Accumulated amount
    $gone = $this->eta($goal['start_date'], $today);
    if ($gone->m > 0) {
      $accumulated = $gone->m * $goal['amount'];
    } else {
      $accumulated = "Not yet a month, $gone->d day(s)";
    }
    ?>
    <table>
      <tr><td>Goal Type</td><td><?= $goal['type']?></td></tr>
      <tr><td>Monthly Investment </td><td><?= $goal['amount']?></td></tr>
      <tr><td>Target Investment</td><td><?= $goal['target']?></td></tr>
      <tr><td>Period of Investment</td><td> <?= $period[0]?> <?= $years ?> and <?= $period[1]?> <?= $months ?> </td></tr>
      <tr><td>Start of Investment</td><td><?= $goal['start_date']?></td></tr>
      <tr><td>End of Investment</td><td><?= $goal['end_date']?></td></tr>
      <tr><td>Goal will be reached in</td><td><?= $eta ?></td></tr>
      <tr><td>Interest Rate</td><td><?= $goal['interest']?></td></tr>
      <tr><td>Interest Period</td><td><?= $goal['interest_period']?></td></tr>
      <tr><td>Interest on Interest</td><td><?= $goal['ioi']?></td></tr>
      <tr><td>Accumulated/Saved</td><td><?= $accumulated ?></td></tr>
      <tr><td>Goal Status</td><td><?= $goal['status']?></td></tr>
      <tr><td>Goal Options</td><td><button data-status="<?= $goal['status']?>" id="<?=$goal['id_budget']?>" onclick="deleteGoal(this, event)">Delete</button></td></tr>
    </table>
    <?php
  }
  
  public function renderReachedGoalDetails($goal) {
    $period = explode('.', $goal['period']);
    if ($period[0] > 1) {
      $years = 'years';
    } else {
      $years = 'year';
    }
    
    if ($period[1] > 1) {
      $months = 'months';
    } else {
    
    // Calculate Accumulated amount
    $gone = $this->eta($goal['start_date'], $goal['end_date']);
    if ($gone->m > 0) {
      $accumulated = $gone->m * $goal['amount'];
    } else {
      $accumulated = "Not yet a month, $gone->d day(s)";
    }
    ?>
    <table>
      <tr><td>Goal Type</td><td><?= $goal['type']?></td></tr>
      <tr><td>Goal reached in</td><td> <?= $period[0]?> <?= $years ?> and <?= $period[1]?> <?= $months ?> </td></tr>
      <tr><td>Start of Investment</td><td><?= $goal['start_date']?></td></tr>
      <tr><td>Accumulated/Saved</td><td><?= $accumulated ?></td></tr>
      <tr><td>Goal Status</td><td><?= $goal['status']?></td></tr>
      <tr><td>Goal Options</td><td><button data-status="<?= $goal['status']?>" id="<?=$goal['id_budget']?>" onclick="deleteGoal(this, event)">Delete</button></td></tr>
    </table>
    <?php
    }
  }
}
