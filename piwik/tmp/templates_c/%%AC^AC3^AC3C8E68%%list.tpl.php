<?php /* Smarty version 2.6.26, created on 2013-06-05 07:09:10
         compiled from PDFReports/templates/list.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'translate', 'PDFReports/templates/list.tpl', 5, false),array('modifier', 'upper', 'PDFReports/templates/list.tpl', 53, false),array('modifier', 'count', 'PDFReports/templates/list.tpl', 58, false),array('function', 'url', 'PDFReports/templates/list.tpl', 74, false),)), $this); ?>
<div id='entityEditContainer'>
    <table class="dataTable entityTable">
        <thead>
        <tr>
            <th class="first"><?php echo ((is_array($_tmp='General_Description')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</th>
            <th><?php echo ((is_array($_tmp='PDFReports_EmailSchedule')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</th>
            <th><?php echo ((is_array($_tmp='PDFReports_ReportFormat')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</th>
            <th><?php echo ((is_array($_tmp='PDFReports_SendReportTo')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</th>
            <th><?php echo ((is_array($_tmp='General_Download')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</th>
            <th><?php echo ((is_array($_tmp='General_Edit')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</th>
            <th><?php echo ((is_array($_tmp='General_Delete')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</th>
        </tr>
        </thead>

        <?php if ($this->_tpl_vars['userLogin'] == 'anonymous'): ?>
        <tr>
            <td colspan='7'>
                <br/>
                <?php echo ((is_array($_tmp='PDFReports_MustBeLoggedIn')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

                <br/>&rsaquo; <a href='index.php?module=<?php echo $this->_tpl_vars['loginModule']; ?>
'><?php echo ((is_array($_tmp='Login_LogIn')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</a></strong>
                <br/><br/>
            </td>
        </tr>
    </table>
    <?php elseif (empty ( $this->_tpl_vars['reports'] )): ?>
    <tr>
        <td colspan='7'>
            <br/>
            <?php echo ((is_array($_tmp='PDFReports_ThereIsNoReportToManage')) ? $this->_run_mod_handler('translate', true, $_tmp, $this->_tpl_vars['siteName']) : smarty_modifier_translate($_tmp, $this->_tpl_vars['siteName'])); ?>
.
            <br/><br/>
            <a onclick='' id='linkAddReport'>&rsaquo; <?php echo ((is_array($_tmp='PDFReports_CreateAndScheduleReport')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</a>
            <br/><br/>
        </td>
    </tr>
    </table>
    <?php else: ?>
    <?php $_from = $this->_tpl_vars['reports']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['report']):
?>
        <tr>
            <td class="first">
                <?php echo $this->_tpl_vars['report']['description']; ?>


                <?php if ($this->_tpl_vars['segmentEditorActivated'] && isset ( $this->_tpl_vars['report']['idsegment'] )): ?>
                    <div class="entityInlineHelp" style="font-size: 9pt;">
                        <?php echo $this->_tpl_vars['savedSegmentsById'][$this->_tpl_vars['report']['idsegment']]; ?>

                    </div>
                <?php endif; ?>
            </td>
            <td><?php echo $this->_tpl_vars['periods'][$this->_tpl_vars['report']['period']]; ?>

                <!-- Last sent on <?php echo $this->_tpl_vars['report']['ts_last_sent']; ?>
 -->
            </td>
            <td>
                <?php if (! empty ( $this->_tpl_vars['report']['format'] )): ?>
                    <?php echo ((is_array($_tmp=$this->_tpl_vars['report']['format'])) ? $this->_run_mod_handler('upper', true, $_tmp) : smarty_modifier_upper($_tmp)); ?>

                <?php endif; ?>
            </td>
            <td>
                                <?php if (count($this->_tpl_vars['report']['recipients']) == 0): ?>
                    <?php echo ((is_array($_tmp='PDFReports_NoRecipients')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

                <?php else: ?>
                    <?php $_from = $this->_tpl_vars['report']['recipients']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['recipients'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['recipients']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['recipient']):
        $this->_foreach['recipients']['iteration']++;
?>
                        <?php echo $this->_tpl_vars['recipient']; ?>

                        <br/>
                    <?php endforeach; endif; unset($_from); ?>
                                    <a href='#' idreport='<?php echo $this->_tpl_vars['report']['idreport']; ?>
' name='linkSendNow' class="link_but" style='margin-top:3px'>
                        <img border=0 src='<?php echo $this->_tpl_vars['reportTypes'][$this->_tpl_vars['report']['type']]; ?>
'/>
                        <?php echo ((is_array($_tmp='PDFReports_SendReportNow')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

                    </a>
                <?php endif; ?>
            </td>
            <td>
                                <a href="<?php echo smarty_function_url(array('module' => 'API','period' => $this->_tpl_vars['report']['period'],'segment' => null,'token_auth' => $this->_tpl_vars['token_auth'],'method' => 'PDFReports.generateReport','idReport' => $this->_tpl_vars['report']['idreport'],'outputType' => $this->_tpl_vars['downloadOutputType'],'language' => $this->_tpl_vars['language']), $this);?>
"
                   target="_blank" name="linkDownloadReport" id="<?php echo $this->_tpl_vars['report']['idreport']; ?>
" class="link_but">
                    <img src='<?php echo $this->_tpl_vars['reportFormatsByReportType'][$this->_tpl_vars['report']['type']][$this->_tpl_vars['report']['format']]; ?>
' border="0"/>
                    <?php echo ((is_array($_tmp='General_Download')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

                </a>
            </td>
            <td>
                                <a href='#' name="linkEditReport" id="<?php echo $this->_tpl_vars['report']['idreport']; ?>
" class="link_but">
                    <img src='themes/default/images/ico_edit.png' border="0"/>
                    <?php echo ((is_array($_tmp='General_Edit')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

                </a>
            </td>
            <td>
                                <a href='#' name="linkDeleteReport" id="<?php echo $this->_tpl_vars['report']['idreport']; ?>
" class="link_but">
                    <img src='themes/default/images/ico_delete.png' border="0"/>
                    <?php echo ((is_array($_tmp='General_Delete')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

                </a>
            </td>
        </tr>
    <?php endforeach; endif; unset($_from); ?>
    </table>
    <?php if ($this->_tpl_vars['userLogin'] != 'anonymous'): ?>
        <br/>
        <a onclick='' id='linkAddReport'>&rsaquo; <?php echo ((is_array($_tmp='PDFReports_CreateAndScheduleReport')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</a>
        <br/>
        <br/>
    <?php endif; ?>
    <?php endif; ?>
</div>