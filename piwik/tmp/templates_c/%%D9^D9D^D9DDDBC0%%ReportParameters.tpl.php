<?php /* Smarty version 2.6.26, created on 2013-06-05 07:09:10
         compiled from MobileMessaging/templates/ReportParameters.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'translate', 'MobileMessaging/templates/ReportParameters.tpl', 48, false),array('modifier', 'count', 'MobileMessaging/templates/ReportParameters.tpl', 51, false),array('function', 'url', 'MobileMessaging/templates/ReportParameters.tpl', 62, false),)), $this); ?>
<script>
    $(function () {
        resetReportParametersFunctions ['<?php echo $this->_tpl_vars['reportType']; ?>
'] =
                function () {

                    var reportParameters = {
                        'phoneNumbers': [],
                        };

                    updateReportParametersFunctions['<?php echo $this->_tpl_vars['reportType']; ?>
'](reportParameters);
                    };

        updateReportParametersFunctions['<?php echo $this->_tpl_vars['reportType']; ?>
'] =
                function (reportParameters) {

                    if (reportParameters == null) return;

                    $('[name=phoneNumbers]').removeProp('checked');
                    $(reportParameters.phoneNumbers).each(function (index, phoneNumber) {
                        $('#\\' + phoneNumber).prop('checked', 'checked');
                        });
                    };

        getReportParametersFunctions['<?php echo $this->_tpl_vars['reportType']; ?>
'] =
                function () {

                    var parameters = Object();

                    var selectedPhoneNumbers =
                            $.map(
                                    $('[name=phoneNumbers]:checked'),
                                    function (phoneNumber) {
                                        return $(phoneNumber).attr('id');
                                        }
                            );

                    // returning [''] when no phone numbers are selected avoids the "please provide a value for 'parameters'" error message
                    parameters.phoneNumbers =
                            selectedPhoneNumbers.length > 0 ? selectedPhoneNumbers : [''];

                    return parameters;
                    };
        });
</script>

<tr class='<?php echo $this->_tpl_vars['reportType']; ?>
'>
    <td class="first">
        <?php echo ((is_array($_tmp='MobileMessaging_MobileReport_PhoneNumbers')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

    </td>
    <td>
        <?php if (count($this->_tpl_vars['phoneNumbers']) == 0): ?>
        <div class="entityInlineHelp">
            <?php echo ((is_array($_tmp='MobileMessaging_MobileReport_NoPhoneNumbers')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

            <?php else: ?>
            <?php $_from = $this->_tpl_vars['phoneNumbers']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['phoneNumber']):
?>
                <label><input name='phoneNumbers' type='checkbox' id='<?php echo $this->_tpl_vars['phoneNumber']; ?>
'/><?php echo $this->_tpl_vars['phoneNumber']; ?>
</label>
                <br/>
            <?php endforeach; endif; unset($_from); ?>
            <div class="entityInlineHelp">
                <?php echo ((is_array($_tmp='MobileMessaging_MobileReport_AdditionalPhoneNumbers')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

                <?php endif; ?>
                <a href='<?php echo smarty_function_url(array('module' => 'MobileMessaging','updated' => null), $this);?>
'><?php echo ((is_array($_tmp='MobileMessaging_MobileReport_MobileMessagingSettingsLink')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</a>
            </div>
    </td>
</tr>