<?php /* Smarty version 2.6.26, created on 2013-06-05 07:09:10
         compiled from PDFReports/templates/report_parameters.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'translate', 'PDFReports/templates/report_parameters.tpl', 71, false),)), $this); ?>
<script>

    function updateEvolutionGraphParameterVisibility() {
        var evolutionGraphParameterInput = $('.report_evolution_graph');
        var nonApplicableDisplayFormats = ['1', '4'];
        $.inArray($('#display_format').find('option:selected').val(), nonApplicableDisplayFormats) != -1 ?
                evolutionGraphParameterInput.hide() : evolutionGraphParameterInput.show();
        }

    $(function () {

        resetReportParametersFunctions ['<?php echo $this->_tpl_vars['reportType']; ?>
'] =
                function () {

                    var reportParameters = {
                        'displayFormat': '<?php echo $this->_tpl_vars['defaultDisplayFormat']; ?>
',
                        'emailMe': <?php echo $this->_tpl_vars['defaultEmailMe']; ?>
,
                        'evolutionGraph': <?php echo $this->_tpl_vars['defaultEvolutionGraph']; ?>
,
                        'additionalEmails': null
                        };

                    updateReportParametersFunctions['<?php echo $this->_tpl_vars['reportType']; ?>
'](reportParameters);
                    };

        updateReportParametersFunctions['<?php echo $this->_tpl_vars['reportType']; ?>
'] =
                function (reportParameters) {

                    if (reportParameters == null) return;

                    $('#display_format').find('option[value=' + reportParameters.displayFormat + ']').prop('selected', 'selected');
                    updateEvolutionGraphParameterVisibility();

                    if (reportParameters.emailMe === true)
                        $('#report_email_me').prop('checked', 'checked');
                    else
                        $('#report_email_me').removeProp('checked');

                    if (reportParameters.evolutionGraph === true)
                        $('#report_evolution_graph').prop('checked', 'checked');
                    else
                        $('#report_evolution_graph').removeProp('checked');

                    if (reportParameters.additionalEmails != null)
                        $('#report_additional_emails').text(reportParameters.additionalEmails.join('\n'));
                    else
                        $('#report_additional_emails').html('');
                    };

        getReportParametersFunctions['<?php echo $this->_tpl_vars['reportType']; ?>
'] =
                function () {

                    var parameters = Object();

                    parameters.displayFormat = $('#display_format').find('option:selected').val();
                    parameters.emailMe = $('#report_email_me').prop('checked');
                    parameters.evolutionGraph = $('#report_evolution_graph').prop('checked');

                    var additionalEmails = $('#report_additional_emails').val();
                    parameters.additionalEmails =
                            additionalEmails != '' ? additionalEmails.split('\n') : [];

                    return parameters;
                    };

        $('#display_format').change(updateEvolutionGraphParameterVisibility);

        });
</script>

<tr class='<?php echo $this->_tpl_vars['reportType']; ?>
'>
    <td style='width:240px;' class="first"><?php echo ((is_array($_tmp='PDFReports_SendReportTo')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

    </td>
    <td>
        <input type="checkbox" id="report_email_me"/>
        <label for="report_email_me"><?php echo ((is_array($_tmp='PDFReports_SentToMe')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
 (<i><?php echo $this->_tpl_vars['currentUserEmail']; ?>
</i>) </label>
        <br/><br/>
        <?php echo ((is_array($_tmp='PDFReports_AlsoSendReportToTheseEmails')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
<br/>
        <textarea cols="30" rows="3" id="report_additional_emails" class="inp"></textarea>
    </td>
</tr>
<tr class='<?php echo $this->_tpl_vars['reportType']; ?>
'>
    <td class="first">
                <?php echo ((is_array($_tmp='PDFReports_AggregateReportsFormat')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

    </td>
    <td>
        <select id="display_format">
            <?php $_from = $this->_tpl_vars['displayFormats']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['formatValue'] => $this->_tpl_vars['formatLabel']):
?>
                <option <?php if ($this->_tpl_vars['formatValue'] == 1): ?>selected<?php endif; ?> value="<?php echo $this->_tpl_vars['formatValue']; ?>
"><?php echo $this->_tpl_vars['formatLabel']; ?>
</option>
            <?php endforeach; endif; unset($_from); ?>
        </select>

        <div class='report_evolution_graph'>
            <br/>
            <input type="checkbox" id="report_evolution_graph"/>
            <label for="report_evolution_graph"><i><?php echo ((is_array($_tmp='PDFReports_EvolutionGraph')) ? $this->_run_mod_handler('translate', true, $_tmp, 5) : smarty_modifier_translate($_tmp, 5)); ?>
</i></label>
        </div>
    </td>
</tr>