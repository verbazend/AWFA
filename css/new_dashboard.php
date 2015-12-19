<script type="text/javascript">

    // Load the Visualization API and the piechart package.
    google.load('visualization', '1.0', {'packages':['corechart']});

    // Set a callback to run when the Google Visualization API is loaded.
    google.setOnLoadCallback(draw);
    
    function draw() {
      drawChart();
      //drawToolbar();
    }

    // Callback that creates and populates a data table,
    // instantiates the pie chart, passes in the data and
    // draws it.
    function drawChart() {

      // Create the data table.
      var data = new google.visualization.DataTable();
      data.addColumn('string', 'Gender');
      data.addColumn('number', 'Percentage');
      data.addRows([
        <?php
            echo '["Male", '.$gender->Male_Percent.'],';
            echo '["Female", '.$gender->FeMale_Percent.'],';
            echo '["Unkown", '.$gender->Unkown_Percent.']';
        ?>
      ]);
      
      // Create the data table.
      var data2 = google.visualization.arrayToDataTable([
        <?php
            echo '["Age Group", "Male", "Female", "Unkown"],';
            for($i=0; $i<9; $i++) {
                echo '["'.$age_group['males'][$i]['ageband'].'", '.$age_group['males'][$i]['count'].', '.$age_group['females'][$i]['count'].', '.$age_group['unkown'][$i]['count'].'],';
            }
        ?>
      ]);

      // Set chart options
      var options = {'title':'Students Gender Percentage',
                     'width':600,
                     'height':400,
                     'is3D': true
                 };
                 
      // Set chart options
      var options2 = {'title':'Students Count by Age Group',
                     'width':1000,
                     'height':400,
                     'is3D': true
                 };

      // Instantiate and draw our chart, passing in some options.
      var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
      chart.draw(data, options);
      var chart2 = new google.visualization.ColumnChart(document.getElementById('chart_div2'));
      chart2.draw(data2, options2);
    }
    
    function drawToolbar() {
      var components = [
          {type: 'html', datasource: 'https://spreadsheets.google.com/tq?key=pCQbetd-CptHnwJEfo8tALA'},
          {type: 'csv', datasource: 'https://spreadsheets.google.com/tq?key=pCQbetd-CptHnwJEfo8tALA'}
      ];

      var container = document.getElementById('toolbar_div');
      google.visualization.drawToolbar(container, components);
    };
</script>

<script>
    $( document ).ready(function() {
        $('.export_option').change(function(event) {
            var option = $( "option:selected", this).val();
            if(option != 0) {
                document.location.href = '/admin/new-dashboad/export/?type=csv&method='+option;
            }
        });
    });
</script>

<div id="maincontent">
    <div class="section1">
        <!--Div that will hold the pie chart-->
        <div id="chart_div"></div>
        <!--<div id="toolbar_div"></div>-->
        <div class="export">Export as:
            <select class="export_option">
                <option value="0">select a option</option>
                <option id="gender" value="gender">CSV</option>
            </select>
        </div>
    </div>
    <br /><hr><br />
    <div class="section2">
        <!--Div that will hold the pie chart-->
        <div id="chart_div2"></div>
        <!--<div id="toolbar_div"></div>-->
        <div class="export">Export as:
            <select class="export_option">
                <option value="0">select a option</option>
                <option id="male_age_group" value="male_age_group">Male Age Group CSV</option>
                <option id="female_age_group" value="female_age_group">Female Age Group CSV</option>
                <option id="unkown_age_group" value="unkown_age_group">Unkown Age Group CSV</option>
            </select>
        </div>
    </div>
</div>
</div>