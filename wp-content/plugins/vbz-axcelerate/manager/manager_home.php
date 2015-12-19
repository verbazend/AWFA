<?php
//Queries for homepage

$BookingOverviewYear = db::runQuery("select count(*) as total, DATE_FORMAT(created, '%Y') as vyear from bookingDetails where failed = '0' and dollorAmount <> '1' and dollorAmount <> '' group by YEAR(created) order by vyear asc");
$BookingIncomeOverviewYear = db::runQuery("select sum(dollorAmount) as total, DATE_FORMAT(created, '%Y') as vyear from bookingDetails where failed = '0' and dollorAmount <> '1' and dollorAmount <> '' and paymentMethod = 'payment-card' group by YEAR(created) order by vyear asc");
?>
	<div class="row">
        <div class="col-xs-12">
          <h2 class="h2small">Booking Overview</h2>
          <div id="bookingOverview" style="min-width: 380px; height: 400px; margin: 0 auto"></div>
        </div> 
	</div>
	<br><br><br> 
	<div class="row">
        <div class="col-xs-12">
          <h2 class="h2small">Income Overview</h2>
          <div id="bookingIncomeOverview" style="min-width: 380px; height: 400px; margin: 0 auto"></div>
        </div>
        
     </div>
      <br><br><br> 
<script>
	<?php
	$BookingOverviewMonth = false;
	?>
	jQuery(function ($) {
    	$('#bookingOverview').highcharts({
	        title: {
	            text: 'Total Bookings',
	            x: -20 //center
	        },
	        xAxis: {
	            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
	                'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
	        },
	        yAxis: {
	            title: {
	                text: 'QTY'
	            },
	            plotLines: [{
	                value: 0,
	                width: 1,
	                color: '#808080'
	            }]
	        },
	        tooltip: {
	            valueSuffix: ''
	        },
	        legend: {
	            layout: 'vertical',
	            align: 'right',
	            verticalAlign: 'middle',
	            borderWidth: 0
	        },
	        series: [
			    <?php
			    	$i=0;
			    	foreach($BookingOverviewYear as $bookingYear){
			    		
						$yearColor    = $yearcolors[$i];
						$yearColorHex = $yearcolorsHex[$i];
						
						$yearName 	  = $bookingYear['vyear'];
						
						$m1  = 0;
						$m2  = 0;
						$m3  = 0;
						$m4  = 0;
						$m5  = 0;
						$m6  = 0;
						$m7  = 0;
						$m8  = 0;
						$m9  = 0;
						$m10 = 0;
						$m11 = 0;
						$m12 = 0;
							
						$BookingOverviewMonth = db::runQuery("select count(*) as total, DATE_FORMAT(created, '%Y/%m') as yearmonth, DATE_FORMAT(created, '%Y') as vyear, DATE_FORMAT(created, '%m') as vmonth from bookingDetails where failed = '0' and dollorAmount <> '1' and dollorAmount <> '' and YEAR(created) = '$yearName' group by YEAR(created), MONTH(created) order by yearmonth");
						
						foreach($BookingOverviewMonth as $bookingMonth){
							//var_dump($bookingMonth);
							$monthNumber = $bookingMonth['vmonth'];
							if($monthNumber=="1"){
								$m1  = $bookingMonth['total'];
							}
							if($monthNumber=="2"){
								$m2  = $bookingMonth['total'];
							}
							if($monthNumber=="3"){
								$m3  = $bookingMonth['total'];
							}
							if($monthNumber=="4"){
								$m4  = $bookingMonth['total'];
							}
							if($monthNumber=="5"){
								$m5  = $bookingMonth['total'];
							}
							if($monthNumber=="6"){
								$m6  = $bookingMonth['total'];
							}
							if($monthNumber=="7"){
								$m7  = $bookingMonth['total'];
							}
							if($monthNumber=="8"){
								$m8  = $bookingMonth['total'];
							}
							if($monthNumber=="9"){
								$m9  = $bookingMonth['total'];
							}
							if($monthNumber=="10"){
								$m10 =  $bookingMonth['total'];
							}
							if($monthNumber=="11"){
								$m11 = $bookingMonth['total'];
							}
							if($monthNumber=="12"){
								$m12 = $bookingMonth['total'];
							}

						}

						?>
						{
				            name: "<?php echo $yearName; ?>",
				            fillColor: "rgba(<?php echo $yearColor; ?>,0.2)",
				            <?php echo "data: [$m1, $m2, $m3, $m4, $m5, $m6, $m7, $m8, $m9, $m10, $m11, $m12]"; ?>
				        },
						<?php
						$i++;
					}
			    ?>
			    ]
		    });
	
	
	
	<?php
	$yearcolors    = array("220,220,220","220,220,120","220,220,80","220,220,10");
	$yearcolorsHex = array(fff,ffc,ffb,ffd);
	$BookingOverviewMonth = false;
	?>
	$('#bookingIncomeOverview').highcharts({
	        title: {
	            text: 'Total Confirmed Income',
	            x: -20 //center
	        },
	        xAxis: {
	            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
	                'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
	        },
	        yAxis: {
	            title: {
	                text: 'Income ($)'
	            },
	            plotLines: [{
	                value: 0,
	                width: 1,
	                color: '#808080'
	            }]
	        },
	        tooltip: {
	            valuePrefix: '$'
	        },
	        legend: {
	            layout: 'vertical',
	            align: 'right',
	            verticalAlign: 'middle',
	            borderWidth: 0
	        },
	        series: [
			    <?php
			    	$i=0;
			    	foreach($BookingIncomeOverviewYear as $bookingYear){
			    		
						$yearColor    = $yearcolors[$i];
						$yearColorHex = $yearcolorsHex[$i];
						
						$yearName 	  = $bookingYear['vyear'];
						
						$m1  = 0;
						$m2  = 0;
						$m3  = 0;
						$m4  = 0;
						$m5  = 0;
						$m6  = 0;
						$m7  = 0;
						$m8  = 0;
						$m9  = 0;
						$m10 = 0;
						$m11 = 0;
						$m12 = 0;
							
						$BookingOverviewMonth = db::runQuery("select sum(dollorAmount) as total, DATE_FORMAT(created, '%Y/%m') as yearmonth, DATE_FORMAT(created, '%Y') as vyear, DATE_FORMAT(created, '%m') as vmonth from bookingDetails where failed = '0' and dollorAmount <> '1' and dollorAmount <> '' and YEAR(created) = '$yearName' and paymentMethod = 'payment-card' group by YEAR(created), MONTH(created) order by yearmonth");
						
						foreach($BookingOverviewMonth as $bookingMonth){
							//var_dump($bookingMonth);
							$monthNumber = $bookingMonth['vmonth'];
							if($monthNumber=="1"){
								$m1  = $bookingMonth['total'];
							}
							if($monthNumber=="2"){
								$m2  = $bookingMonth['total'];
							}
							if($monthNumber=="3"){
								$m3  = $bookingMonth['total'];
							}
							if($monthNumber=="4"){
								$m4  = $bookingMonth['total'];
							}
							if($monthNumber=="5"){
								$m5  = $bookingMonth['total'];
							}
							if($monthNumber=="6"){
								$m6  = $bookingMonth['total'];
							}
							if($monthNumber=="7"){
								$m7  = $bookingMonth['total'];
							}
							if($monthNumber=="8"){
								$m8  = $bookingMonth['total'];
							}
							if($monthNumber=="9"){
								$m9  = $bookingMonth['total'];
							}
							if($monthNumber=="10"){
								$m10 =  $bookingMonth['total'];
							}
							if($monthNumber=="11"){
								$m11 = $bookingMonth['total'];
							}
							if($monthNumber=="12"){
								$m12 = $bookingMonth['total'];
							}

						}

						?>
						{
				            name: "<?php echo $yearName; ?>",
				            <?php echo "data: [$m1, $m2, $m3, $m4, $m5, $m6, $m7, $m8, $m9, $m10, $m11, $m12]"; ?>
				        },
						<?php
						$i++;
					}
			    ?>
			    ]
			});
	});
	
</script>