<?php
/**
 * @package      CrowdFunding
 * @subpackage   Plugins
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * CrowdFunding is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

/**
 * CrowdFunding Bank Transfer Payment Plugin
 *
 * @package      CrowdFunding
 * @subpackage   Plugins
 */
class plgCrowdFundingPaymentBankTransfer extends JPlugin {
    
    /**
     * This method prepares a payment gateway - buttons, forms,...
     * That gateway will be displayed on the summary page as a payment option.
     *
     * @param string 	$context	This string gives information about that where it has been executed the trigger.
     * @param object 	$item	    A project data.
     * @param JRegistry $params	    The parameters of the component
     */
    public function onProjectPayment($context, $item, $params) {
        
        $app = JFactory::getApplication();
        /** @var $app JSite **/

        if($app->isAdmin()) {
            return;
        }

        $doc     = JFactory::getDocument();
        /**  @var $doc JDocumentHtml **/
        
        // Check document type
        $docType = $doc->getType();
        if(strcmp("html", $docType) != 0){
            return;
        }
       
        if(strcmp("com_crowdfunding.payment", $context) != 0){
            return;
        }
        
        // Load language
        $this->loadLanguage();
        
        // This is a URI path to the plugin folder
        $pluginURI = "plugins/crowdfundingpayment/banktransfer";
        
        // Load the script that initialize the select element with banks.
        JHtml::_("bootstrap.framework");
        JHtml::_("bootstrap.loadCss");
        
        $doc = JFactory::getDocument();
        $doc->addScript($pluginURI."/js/plg_crowdfundingpayment_banktransfer.js");
        
        // Check for valid beneficiary information
        $beneficiaryInfo = JString::trim($this->params->get("beneficiary"));
        if(!$beneficiaryInfo) {
            return '<div class="alert">'.JText::_("PLG_CROWDFUNDINGPAYMENT_BANKTRANSFER_ERROR_PLUGIN_NOT_CONFIGURED").'</div>';
        }
        
        $html  =  "";
        $html .= '<h4><img src="'.$pluginURI.'/images/bank_icon.png" />'.JText::_("PLG_CROWDFUNDINGPAYMENT_BANKTRANSFER_TITLE").'</h4>';
        $html .= '<div>'.nl2br($this->params->get("beneficiary")).'</div>';
        
        if($this->params->get("display_additional_info", 1)) {
            $additionalInfo = JString::trim($this->params->get("additional_info"));
            if(!empty($additionalInfo)) {
                $html .= '<p class="sticky">'.htmlspecialchars($additionalInfo, ENT_QUOTES, "UTF-8").'</p>';
            } else {
                $html .= '<p class="sticky">'.JText::_("PLG_CROWDFUNDINGPAYMENT_BANKTRANSFER_INFO").'</p>';
            }
        }
        
        $html .= '<div class="alert hide" id="js-bt-alert"></div>';
            
        $html .= '<div class="clearfix"></div>';
        $html .= '<a href="#" class="btn btn-primary" id="js-register-bt">'.JText::_("PLG_CROWDFUNDINGPAYMENT_BANKTRANSFER_MAKE_BANK_TRANSFER").'</a>';
        $html .= '<a href="#" class="btn hide" id="js-continue-bt">'.JText::_("PLG_CROWDFUNDINGPAYMENT_BANKTRANSFER_CONTINUE_NEXT_STEP").'</a>';
        
        $html .= '    
    <div class="modal hide fade" id="js-banktransfer-modal">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3>'.JText::_("PLG_CROWDFUNDINGPAYMENT_BANKTRANSFER_TITLE").'</h3>
        </div>
        <div class="modal-body">
            <p>'.JText::_("PLG_CROWDFUNDINGPAYMENT_REGISTER_TRANSACTION_QUESTION").'</p>
        </div>
        <div class="modal-footer">
            <img src="media/com_crowdfunding/images/ajax-loader.gif" width="16" height="16" id="js-banktransfer-ajax-loading" style="display: none;" />
            <a href="javascript: void(0);" class="btn btn-primary" id="js-btbtn-yes" data-project-id="'.$item->id.'" data-amount="'.$item->amount.'">'.JText::_("JYES").'</a>
            <a href="javascript: void(0);" class="btn" id="js-btbtn-no">'.JText::_("JNO").'</a>
        </div>
    </div>';
        
        return $html;
        
    }
    
    
}