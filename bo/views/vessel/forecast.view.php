<?php
namespace bo\views\vessel;

use bo\components\classes\Forecast;
use bo\components\classes\Port;
use bo\components\classes\SettingsForecastLists;
use bo\components\classes\Vessel;

include '../../components/config.php';
?>

<div class="ui accordion">

<?php 
foreach($user->getUserPorts() as $key => $userPorts) { 
    $forecast = Forecast::getMultipleObjects(Array("port_id" => $userPorts->getID()), "arriving");
    $arrivingDay = "";
?>

	<div class="title">
    	<i class="dropdown icon"></i>
    	Forecast für <?php echo Port::getPortName($userPorts->getID()); ?> (<?php echo Forecast::getCountOpenForecastPort($userPorts->getID()); ?>)
	</div>
	<div class="content">
		<form id="addForecast<?php echo $userPorts->getID(); ?>" class="addForecast">
            <table class="detailTable ui very compact celled striped table">
            	<thead>
            		<tr>
            			<th>ETA / ETD</th>
            			<th>Name / IMO</th>
            			<th></th>
            			<th></th>
            			<th>Last cont.</th>
            			<th>Terminal / Agent</th>
            			<th></th>
            			<th></th>
            		</tr>
            	</thead>
                <tbody>
<?php 
foreach($forecast as $expectedVessel) {
    $tmpDay = new \DateTime($expectedVessel->getArriving());
    if($tmpDay->format('Y-m-d') != $arrivingDay) {
        $arrivingDay = $tmpDay->format('Y-m-d');
?>
    				<tr class="positive">
    					<td colspan="8"><?php echo $tmpDay->format('Y-m-d'); ?></td>
    				</tr>
<?php    
    }
?>
            		<tr<?php echo ($expectedVessel->getStatus() == 1)?' class="forecastDisabled"':'';?>>
            			<td data-label="etad">
            				<div><?php echo $expectedVessel->getArriving(); ?></div>
            				<div><?php echo $expectedVessel->getLeaving(); ?></div>
            			</td>			
            			<td data-label="name_imo" title="<?php echo $expectedVessel->getName(); ?>">
            				<div><?php echo $expectedVessel->getName(); ?></div>
            				<div><?php echo $expectedVessel->getIMO(); ?></div>
            			</td>
        				<td data-label="inSystem" class="center aligned collapsing">
        				<?php echo (!empty($expectedVessel->vessel))?'
                            <a onClick="vessel.openDetails(' . $expectedVessel->vessel->getID() . '); safeForecastPosition();">
                                <i class="address card outline icon"></i>
                            </a>':''; ?>
        				</td>
            			<td data-label="email" class="center aligned collapsing">
            				<?php echo ($expectedVessel->hasMail)?'<i class="envelope outline icon"></i>':''; ?>
            				<?php echo ($expectedVessel->hasPhone)?'<i class="phone icon"></i>':''; ?>
            				<?php echo ($expectedVessel->inDry)?'<i class="gb uk flag"></i>':''; ?>
            				<?php echo ($expectedVessel->expectMail)?'<i class="question circle outline icon"></i>':''; ?>
            			</td>
            			<td data-label="last_contact" class="collapsing">
            			<?php 
                			if (!empty($expectedVessel->vessel)) {
                			    echo Vessel::getLastContactVessel($expectedVessel->vessel->getID());
                			}
            			?>
            			</td>
            			<td data-label="agency_company">
            				<div><?php echo $expectedVessel->getCompany(); ?></div>
            				<div><?php echo $expectedVessel->getAgency(); ?></div>
            			</td>
            			<td data-label="done" class="center aligned collapsing">
            			<?php if($expectedVessel->getStatus() == 0) { ?>
            				<a onClick="vessel.forecastItemDone(<?php echo $expectedVessel->getID(); ?>, this);"><i class="check icon"></i></a>
            			<?php } else { ?>
            				<a onClick="vessel.forecastItemReopen(<?php echo $expectedVessel->getID(); ?>, this);"><i class="undo icon"></i></a>
            			<?php } ?>
            			</td>
            			<td data-label="remove" class="center aligned collapsing">
            				<a onClick="vessel.forecastItemRemove(<?php echo $expectedVessel->getID(); ?>, this);"><i class="trash icon"></i></a>
            			</td>
            		</tr>
<?php } ?>
    				<tr>
						<td>
							<div id="input_eta" class="field"><input type="date" name="eta" id="eta"></div>
							<div id="input_etd" class="field"><input type="date" name="etd" id="etd"></div>
						</td>
						<td><div id="input_name" class="field"><input type="text" name="name" id="name"></div></td>
						<td colspan="2">
							<input type="hidden" name="portID" value="<?php echo $userPorts->getID(); ?>">
							<input type="hidden" name="accordionID" value="<?php echo $key; ?>">
						</td>
						<td><input type="text" name="terminal" id="terminal"></td>
						<td><input type="text" name="agency" id="agency"></td>
						<td class="right aligned" colspan="2"><button class="ui icon button" type="submit"><i class="save outline icon"></i></button></td>
    				</tr>
                </tbody>
            </table>
    	</form>
	</div>
<?php } ?>	
</div>

<h4 class="ui horizontal divider header">
  <i class="linkify icon"></i>
  Externe Links
</h4>

<?php foreach(SettingsForecastLists::getMultipleObjects() as $externForecastList) {?>
<div class="externLink"><a href="<?php echo $externForecastList->getLink(); ?>" target="_blank"><?php echo $externForecastList->getName(); ?></a></div>
<?php }?>

<script>
$('.ui.accordion').accordion({
	onOpening: function() {
		forecastAccordionOpen = this.index(".content");
	}
});
$(".addForecast").submit(function(event){ vessel.addForecast(this.id); });
</script>