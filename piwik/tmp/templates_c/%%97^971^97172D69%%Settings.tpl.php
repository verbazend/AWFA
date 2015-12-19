<?php /* Smarty version 2.6.26, created on 2013-06-04 01:15:47
         compiled from MobileMessaging/templates/Settings.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'loadJavascriptTranslations', 'MobileMessaging/templates/Settings.tpl', 2, false),array('function', 'ajaxErrorDiv', 'MobileMessaging/templates/Settings.tpl', 56, false),array('function', 'ajaxLoadingDiv', 'MobileMessaging/templates/Settings.tpl', 202, false),array('modifier', 'translate', 'MobileMessaging/templates/Settings.tpl', 22, false),array('modifier', 'truncate', 'MobileMessaging/templates/Settings.tpl', 103, false),array('modifier', 'inlineHelp', 'MobileMessaging/templates/Settings.tpl', 110, false),array('modifier', 'count', 'MobileMessaging/templates/Settings.tpl', 117, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'CoreAdminHome/templates/header.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php echo smarty_function_loadJavascriptTranslations(array('plugins' => 'MobileMessaging'), $this);?>


<?php echo '
    <style>#accountForm ul {
            list-style: circle;
            margin-left: 17px;
            line-height: 1.5em;
        }

        .providerDescription {
            border: 2px dashed #C5BDAD;
            border-radius: 16px 16px 16px 16px;
            margin-left: 24px;
            padding: 11px;
            width: 600px;
        }
    </style>
'; ?>


<?php if ($this->_tpl_vars['accountManagedByCurrentUser']): ?>
    <h2><?php echo ((is_array($_tmp='MobileMessaging_Settings_SMSAPIAccount')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</h2>
    <?php if ($this->_tpl_vars['credentialSupplied']): ?>
        <?php echo ((is_array($_tmp='MobileMessaging_Settings_CredentialProvided')) ? $this->_run_mod_handler('translate', true, $_tmp, $this->_tpl_vars['provider']) : smarty_modifier_translate($_tmp, $this->_tpl_vars['provider'])); ?>

        <?php echo $this->_tpl_vars['creditLeft']; ?>

        <br/>
        <?php echo ((is_array($_tmp='MobileMessaging_Settings_UpdateOrDeleteAccount')) ? $this->_run_mod_handler('translate', true, $_tmp, "<a id='displayAccountForm'>", "</a>", "<a id='deleteAccount'>", "</a>") : smarty_modifier_translate($_tmp, "<a id='displayAccountForm'>", "</a>", "<a id='deleteAccount'>", "</a>")); ?>

    <?php else: ?>
        <?php echo ((is_array($_tmp='MobileMessaging_Settings_PleaseSignUp')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

    <?php endif; ?>
    <div id='accountForm' <?php if ($this->_tpl_vars['credentialSupplied']): ?>style='display: none;'<?php endif; ?>>
        <br/>
        <?php echo ((is_array($_tmp='MobileMessaging_Settings_SMSProvider')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

        <select id='smsProviders'>
            <?php $_from = $this->_tpl_vars['smsProviders']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['smsProvider'] => $this->_tpl_vars['description']):
?>
                <option value='<?php echo $this->_tpl_vars['smsProvider']; ?>
'>
                    <?php echo $this->_tpl_vars['smsProvider']; ?>

                </option>
            <?php endforeach; endif; unset($_from); ?>
        </select>

        <?php echo ((is_array($_tmp='MobileMessaging_Settings_APIKey')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

        <input size='25' id='apiKey'/>

        <input type='submit' value='<?php echo ((is_array($_tmp='General_Save')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
' id='apiAccountSubmit' class='submit'/>

        <?php $_from = $this->_tpl_vars['smsProviders']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['smsProvider'] => $this->_tpl_vars['description']):
?>
            <div class='providerDescription' id='<?php echo $this->_tpl_vars['smsProvider']; ?>
'>
                <?php echo $this->_tpl_vars['description']; ?>

            </div>
        <?php endforeach; endif; unset($_from); ?>

    </div>
<?php endif; ?>

<?php echo smarty_function_ajaxErrorDiv(array('id' => 'ajaxErrorMobileMessagingSettings'), $this);?>


<h2><?php echo ((is_array($_tmp='MobileMessaging_Settings_PhoneNumbers')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</h2>
<?php if (! $this->_tpl_vars['credentialSupplied']): ?>
    <?php if ($this->_tpl_vars['accountManagedByCurrentUser']): ?>
        <?php echo ((is_array($_tmp='MobileMessaging_Settings_CredentialNotProvided')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

    <?php else: ?>
        <?php echo ((is_array($_tmp='MobileMessaging_Settings_CredentialNotProvidedByAdmin')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

    <?php endif; ?>
<?php else: ?>

    <?php echo ((is_array($_tmp='MobileMessaging_Settings_PhoneNumbers_Help')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

    <br/>
    <br/>
    <table style="width:900px;" class="adminTable">
        <tbody>
        <tr>
            <td style="width:480px">
                <strong><?php echo ((is_array($_tmp='MobileMessaging_Settings_PhoneNumbers_Add')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</strong><br/><br/>
	
		<span id='suspiciousPhoneNumber' style='display:none;'>
			<?php echo ((is_array($_tmp='MobileMessaging_Settings_SuspiciousPhoneNumber')) ? $this->_run_mod_handler('translate', true, $_tmp, '54184032') : smarty_modifier_translate($_tmp, '54184032')); ?>
<br/><br/>
		</span>

                + <input id='countryCallingCode' size='4' maxlength='4'/>&nbsp;
                <input id='newPhoneNumber'/>
                <input
                        type='submit'
                        value='<?php echo ((is_array($_tmp='MobileMessaging_Settings_AddPhoneNumber')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
'
                        id='addPhoneNumberSubmit'
                        />

                <br/>
		
		<span style=' font-size: 11px;'><span class="form-description"><?php echo ((is_array($_tmp='MobileMessaging_Settings_CountryCode')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</span>
			<span class="form-description" style="margin-left:50px"><?php echo ((is_array($_tmp='MobileMessaging_Settings_PhoneNumber')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</span></span>
                <br/><br/>

                <?php echo ((is_array($_tmp='MobileMessaging_Settings_PhoneNumbers_CountryCode_Help')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>


                <select id='countries'>
                    <option value=''>&nbsp;</option>                     <?php $_from = $this->_tpl_vars['countries']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['countryCode'] => $this->_tpl_vars['country']):
?>
                        <option
                                value='<?php echo $this->_tpl_vars['country']['countryCallingCode']; ?>
'
                                <?php if ($this->_tpl_vars['defaultCountry'] == $this->_tpl_vars['countryCode']): ?> selected='selected' <?php endif; ?>
                                >
                            <?php echo ((is_array($_tmp=$this->_tpl_vars['country']['countryName'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 15, '...') : smarty_modifier_truncate($_tmp, 15, '...')); ?>

                        </option>
                    <?php endforeach; endif; unset($_from); ?>
                </select>

            </td>
            <td style="width:220px">
                <?php echo ((is_array($_tmp=$this->_tpl_vars['strHelpAddPhone'])) ? $this->_run_mod_handler('inlineHelp', true, $_tmp) : smarty_modifier_inlineHelp($_tmp)); ?>


            </td>
        </tr>
        <tr>
            <td colspan="2">

                <?php if (count($this->_tpl_vars['phoneNumbers']) > 0): ?>
                    <br/>
                    <br/>
                    <strong><?php echo ((is_array($_tmp='MobileMessaging_Settings_ManagePhoneNumbers')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</strong>
                    <br/>
                    <br/>
                <?php endif; ?>

                <?php echo smarty_function_ajaxErrorDiv(array('id' => 'invalidVerificationCodeAjaxError'), $this);?>


                <div id='phoneNumberActivated' class="ajaxSuccess" style="display:none;">
                    <?php echo ((is_array($_tmp='MobileMessaging_Settings_PhoneActivated')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

                </div>

                <div id='invalidActivationCode' style="display:none;">
                    <?php echo ((is_array($_tmp='MobileMessaging_Settings_InvalidActivationCode')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

                </div>

                <ul>
                    <?php $_from = $this->_tpl_vars['phoneNumbers']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['phoneNumber'] => $this->_tpl_vars['validated']):
?>
                        <li>
                            <span class='phoneNumber'><?php echo $this->_tpl_vars['phoneNumber']; ?>
</span>
                            <?php if (! $this->_tpl_vars['validated']): ?>
                                <input class='verificationCode'/>
                                <input
                                        type='submit'
                                        value='<?php echo ((is_array($_tmp='MobileMessaging_Settings_ValidatePhoneNumber')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
'
                                        class='validatePhoneNumberSubmit'
                                        />
                            <?php endif; ?>
                            <input
                                    type='submit'
                                    value='<?php echo ((is_array($_tmp='MobileMessaging_Settings_RemovePhoneNumber')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
'
                                    class='removePhoneNumberSubmit'
                                    />
                            <?php if (! $this->_tpl_vars['validated']): ?>
                                <br/>
                                <span class='form-description'><?php echo ((is_array($_tmp='MobileMessaging_Settings_VerificationCodeJustSent')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</span>
                            <?php endif; ?>
                            <br/>
                            <br/>
                        </li>
                    <?php endforeach; endif; unset($_from); ?>
                </ul>

            </td>
        </tr>
        </tbody>
    </table>
<?php endif; ?>

<?php if ($this->_tpl_vars['isSuperUser']): ?>
    <h2><?php echo ((is_array($_tmp='MobileMessaging_Settings_SuperAdmin')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</h2>
    <table class='adminTable' style='width:650px;'>
        <tr>
            <td style='width:400px'><?php echo ((is_array($_tmp='MobileMessaging_Settings_LetUsersManageAPICredential')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</td>
            <td style='width:250px'>
                <fieldset>
                    <label>
                        <input
                                type='radio'
                                value='false'
                                name='delegatedManagement' <?php if (! $this->_tpl_vars['delegatedManagement']): ?> checked='checked'<?php endif; ?> />
                        <?php echo ((is_array($_tmp='General_No')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

                        <br/>
                        <span class='form-description'>(<?php echo ((is_array($_tmp='General_Default')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

                            ) <?php echo ((is_array($_tmp='MobileMessaging_Settings_LetUsersManageAPICredential_No_Help')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</span>
                    </label>
                    <br/>
                    <br/>
                    <label>
                        <input
                                type='radio'
                                value='true'
                                name='delegatedManagement' <?php if ($this->_tpl_vars['delegatedManagement']): ?> checked='checked'<?php endif; ?> />
                        <?php echo ((is_array($_tmp='General_Yes')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

                        <br/>
                        <span class='form-description'><?php echo ((is_array($_tmp='MobileMessaging_Settings_LetUsersManageAPICredential_Yes_Help')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</span>
                    </label>

                </fieldset>
        </tr>
    </table>
<?php endif; ?>

<?php echo smarty_function_ajaxLoadingDiv(array('id' => 'ajaxLoadingMobileMessagingSettings'), $this);?>


<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'CoreAdminHome/templates/footer.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div class='ui-confirm' id='confirmDeleteAccount'>
    <h2><?php echo ((is_array($_tmp='MobileMessaging_Settings_DeleteAccountConfirm')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</h2>
    <input role='yes' type='button' value='<?php echo ((is_array($_tmp='General_Yes')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
'/>
    <input role='no' type='button' value='<?php echo ((is_array($_tmp='General_No')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
'/>
</div>
