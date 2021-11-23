<?php

class affordabilityView {

  public function renderAffordabilityData() {
    $this->renderCalculator();
  }

  private function renderCalculator() {
    $amount_label = "How much are you willing to spend per month?  ";
    $salary_label = "How much is your net salary per month?  ";
    $category_label = "What type of service do you intend to spend the money?  ";
    ?> 
    <div id="affordHeader" class="afford hideme"></div>
    <div id="affordCalc" class="afford hideme">
      <form name="affordabilityTest" id="affordabilityTest" method="POST" action="affordability/test">
        <?= $amount_label ?> <input type="text" name="amount" id="amount" class="affordInput" placeholder="enter amount"><br>
        <?= $salary_label ?> <input type="text" name="net_salary" id="net_salary" class="affordInput" placeholder="net salary"><br>
        <?= $category_label ?> <select id="category" name="category" class="affordInput">
          <option id="insure" value="insurance" selected="selected">Insurance</option>
          <option id="othertype" value="other">Other</option>
        </select><br>
        <input type="submit" name="runtest" id="runtest" value="Run Test">
      </form>
    </div>
    <div id="testErrors" class="hideme"></div>
    <div id="affordResults" class="afford hideme">
      <div id="affordResultsHeader">AFFORD-ABILITY CALCULATIONS FEEDBACK</div>
      <div id="affordResultsOutput"></div>
    </div>
    <div id="affordHistory" class="afford">
      <div id="affordHistoryHeader"></div>
      <div id="affordHistoryOutput"></div>
    </div>
    <script type="text/javascript" src="js/affordability.js"></script>
    <?php
  }

  public function displayResult($data) {
    $status_label = "Test status ";
    $service_label = "Name of service ";
    $amount_label = "Cost of service ";
    $salary_label = "Net salary at this point ";
    $category_label = "Category of service ";
    ?>
    <form name="testFeedback" id="testFeedback" method="POST" action="affordability/save">
      <?= $service_label ?> <input type="text" name="service_name" id="service_name" placeholder="Name it!" required><br>
      <?= $amount_label ?> <input type="text" name="cost" id="cost" value="<?= $data['amount'] ?>" disabled><br>
      <?= $salary_label ?> <input type="text" name="current_net" id="current_net" value="<?= $data['net_salary'] ?>" disabled><br>
      <?php
      if ($data['category'] === 'other') {
        echo $category_label . ' <input type="text" name="category" id="category" value="'.$data["category"].'"><br>';
      } else {
        echo $category_label . '<input type="text" name="category" id="category" value="'.$data["category"].'" disabled><br>';
      }
      ?>
      <?= $status_label ?> <input type="text" name="outcome" id="outcome" value="<?= $data['outcome'] ?>" disabled><br>
      <input type="submit" name="saveFeedback" id="saveFeedback" value="SAVE RESULT" onclick="saveResult(event)">
    </form>
    <hr>
    <?php
  }

  public function renderSaved($tests) {
    ?>
    <div id="savedResults">
      <div id="savedResultsHeader">MANAGE TEST RESULTS</div>
      <div id="savedResultsContent">
        <?php
            foreach ($tests as $test) {
              if (is_object($test)) {
                  echo "<a href='#' data-next='$test->id_affordability' style='text-transform: capitalize; color: #5d8aa8;' id='$test->id_affordability' onclick='viewTest(this, event)'>$test->service<br></a>";
              }
            }
        ?>
      </div>
    </div>
    <?php
  }
  
  public function displayTest($data) {
    $status_label = "Test status ";
    $service_label = "Name of service ";
    $amount_label = "Cost of service ";
    $salary_label = "Net salary at time of testing ";
    $category_label = "Category of service ";
    ?>
    <form name="testHistory" id="testHistory">
      <table>
        <tr><td><?= $service_label ?></td><td><input type="text" name="name" id="name" value="<?= $data['service']?>" disabled=""></td></tr>
        <tr><td><?= $amount_label ?></td><td><input type="text" name="value" id="value" value="<?= $data['amount'] ?>" disabled></td></tr>
        <tr><td> <?= $salary_label ?></td><td><input type="text" name="cnet" id="cnet" value="<?= $data['net_salary'] ?>" disabled></td></tr>
        <tr><td><?= $category_label ?></td><td><input type="text" name="cat" id="cat" value="<?= $data["category"] ?>" disabled></td></tr>
        <tr><td><?= $status_label ?></td><td><input type="text" name="outcome" id="outcome" value="<?= $data['outcome'] ?>" disabled></td></tr>
        <tr>
          <td>Test Options</td>
          <td><button onclick="modifyTestResults(this, event)">Modify</button><button data-id="<?= $data['id_affordability'] ?>" id="modify_test" style="display: none" onclick="saveModifiedTest(this, event)">Retest</button></td>
          <td><button data-close="affordHistory" data-restore="<?= $data['id_affordability'] ?>" onclick="closeTestResults(this, event)">Close</button></td>
          <td><button onclick="deleteTestResults(this, event)" data-delete="<?= $data['id_affordability'] ?>">Delete</button></td>
          <td><button id="view_nxt" data-nav="<?= $data['id_affordability'] ?>" onclick="viewNextTestResult(this, event)">Next</button></td>
          <td><button id="view_prev" data-nav="<?= $data['id_affordability'] ?>" onclick="viewPreviousTestResult(this, event)">Previous</button></td>
        </tr>
      </table>
    </form>
    <hr>
    <?php
  }

}
