<?php
namespace bo\views\port;
use bo\components\classes\Company;
include '../../components/config.php';

if(isset($_GET['id']))
    $company = Company::getSingleObjectByID($_GET['id']);
?>

<form id="addPortCompany" class="ui form" autocomplete="off">
    <div class="ui error message">
		<div id="errorMessage"></div>
    </div>
    
    <div id="input_companyName" class="required field">
    	<label><?php $t->_('name'); ?></label>
    	<input 
    		type="text" 
    		id="companyName" 
    		name="companyName" 
    		onkeyup="formValidate.clearAllError();" 
    		value="<?php echo(!empty($company))?$company->getName():''; ?>"
    	>
    </div>
    
    <div id="input_companyInfo" class="field">
    	<label>Info</label>
    	<textarea rows="4" id="companyInfo" name="companyInfo"><?php echo(!empty($company))?$company->getInfo():''; ?></textarea>
    </div>

    <div id="input_companyMTLink" class="field">
    	<label>MarineTraffic Link</label>
    	<textarea rows="2" id="companyMTLink" name="companyMTLink"><?php echo(!empty($company))?$company->getMTLink():''; ?></textarea>
    </div>
    
    <div id="input_companyPMLink" class="field">
    	<label>PortMap Link</label>
    	<textarea rows="2" id="companyPMLink" name="companyPMLink"><?php echo(!empty($company))?$company->getPMLink():''; ?></textarea>
    </div>
    
    <input type="hidden" name="portID" value="<?php echo $_GET['portID']; ?>">
    
    <button class="ui button" type="submit"><?php $t->_('safe'); ?></button>
</form>

<script>
$("#addPortCompany").submit(function(event){ portC.addPortCompany(<?php echo (!empty($company))?$company->getID():'null'; ?>);});
</script>