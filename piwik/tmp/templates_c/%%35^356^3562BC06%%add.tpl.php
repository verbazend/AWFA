<?php /* Smarty version 2.6.26, created on 2013-06-05 07:09:10
         compiled from PDFReports/templates/add.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'translate', 'PDFReports/templates/add.tpl', 3, false),array('modifier', 'count', 'PDFReports/templates/add.tpl', 72, false),array('modifier', 'upper', 'PDFReports/templates/add.tpl', 79, false),array('modifier', 'escape', 'PDFReports/templates/add.tpl', 136, false),array('function', 'postEvent', 'PDFReports/templates/add.tpl', 101, false),array('function', 'math', 'PDFReports/templates/add.tpl', 117, false),)), $this); ?>
<div class='entityAddContainer' style='display:none'>
    <div class='entityCancel'>
        <?php echo ((is_array($_tmp='PDFReports_CancelAndReturnToReports')) ? $this->_run_mod_handler('translate', true, $_tmp, "<a class='entityCancelLink'>", "</a>") : smarty_modifier_translate($_tmp, "<a class='entityCancelLink'>", "</a>")); ?>

    </div>
    <div class='clear'></div>
    <form id='addEditReport'>
        <table class="dataTable entityTable">
            <thead>
            <tr class="first">
                <th colspan="2"><?php echo ((is_array($_tmp='PDFReports_CreateAndScheduleReport')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</th>
            <tr>
            </thead>
            <tbody>
            <tr>
                <td class="first"><?php echo ((is_array($_tmp='General_Website')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
 </td>
                <td style="width:650px">
                    <?php echo $this->_tpl_vars['siteName']; ?>

                </td>
            </tr>
            <tr>
                <td class="first"><?php echo ((is_array($_tmp='General_Description')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
 </td>
                <td>
                    <textarea cols="30" rows="3" id="report_description" class="inp"></textarea>

                    <div class="entityInlineHelp">
                        <?php echo ((is_array($_tmp='PDFReports_DescriptionOnFirstPage')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

                    </div>
                </td>
            </tr>
            <?php if ($this->_tpl_vars['segmentEditorActivated']): ?>
                <tr>
                    <td class="first"><?php echo ((is_array($_tmp='SegmentEditor_ChooseASegment')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
 </td>
                    <td>
                        <select id='report_segment'>
                            <option value=""><?php echo ((is_array($_tmp='SegmentEditor_DefaultAllVisits')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</option>
                            <?php $_from = $this->_tpl_vars['savedSegmentsById']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['savedSegmentId'] => $this->_tpl_vars['savedSegmentName']):
?>
                                <option value="<?php echo $this->_tpl_vars['savedSegmentId']; ?>
"><?php echo $this->_tpl_vars['savedSegmentName']; ?>
</option>
                            <?php endforeach; endif; unset($_from); ?>
                        </select>

                        <div class="entityInlineHelp">
                            <?php ob_start(); ?><?php echo ((is_array($_tmp='SegmentEditor_DefaultAllVisits')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
<?php $this->_smarty_vars['capture']['default'] = ob_get_contents();  $this->assign('SegmentEditor_DefaultAllVisits', ob_get_contents());ob_end_clean(); ?>
                            <?php ob_start(); ?><?php echo ((is_array($_tmp='SegmentEditor_AddNewSegment')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
<?php $this->_smarty_vars['capture']['default'] = ob_get_contents();  $this->assign('SegmentEditor_AddNewSegment', ob_get_contents());ob_end_clean(); ?>
                            <?php echo ((is_array($_tmp='PDFReports_Segment_Help')) ? $this->_run_mod_handler('translate', true, $_tmp, '<a href="./" target="_blank">', '</a>', $this->_tpl_vars['SegmentEditor_DefaultAllVisits'], $this->_tpl_vars['SegmentEditor_AddNewSegment']) : smarty_modifier_translate($_tmp, '<a href="./" target="_blank">', '</a>', $this->_tpl_vars['SegmentEditor_DefaultAllVisits'], $this->_tpl_vars['SegmentEditor_AddNewSegment'])); ?>

                        </div>
                    </td>
                </tr>
            <?php endif; ?>
            <tr>
                <td class="first"><?php echo ((is_array($_tmp='PDFReports_EmailSchedule')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</td>
                <td>
                    <select id="report_period" class="inp">
                        <?php $_from = $this->_tpl_vars['periods']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['periodId'] => $this->_tpl_vars['period']):
?>
                            <option value="<?php echo $this->_tpl_vars['periodId']; ?>
">
                                <?php echo $this->_tpl_vars['period']; ?>

                            </option>
                        <?php endforeach; endif; unset($_from); ?>
                    </select>

                    <div class="entityInlineHelp">
                        <?php echo ((is_array($_tmp='PDFReports_WeeklyScheduleHelp')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

                        <br/>
                        <?php echo ((is_array($_tmp='PDFReports_MonthlyScheduleHelp')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

                        <br/>
                        <?php echo ((is_array($_tmp='PDFReports_ReportHour')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

                        <input type="text" style="height: 0.9em;padding-left: 8px;width: 17px;" id="report_hour" class="inp" size="1">
                        <?php echo ((is_array($_tmp='PDFReports_OClock')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

                    </div>
                </td>
            </tr>

            <tr <?php if (count($this->_tpl_vars['reportTypes']) == 1): ?>style='display:none'<?php endif; ?>>
                <td class='first'>
                    <?php echo ((is_array($_tmp='PDFReports_ReportType')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

                </td>
                <td>
                    <select id='report_type'>
                        <?php $_from = $this->_tpl_vars['reportTypes']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['reportType'] => $this->_tpl_vars['reportTypeIcon']):
?>
                            <option value="<?php echo $this->_tpl_vars['reportType']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['reportType'])) ? $this->_run_mod_handler('upper', true, $_tmp) : smarty_modifier_upper($_tmp)); ?>
</option>
                        <?php endforeach; endif; unset($_from); ?>
                    </select>
                </td>
            </tr>

            <tr>
                <td class='first'>
                    <?php echo ((is_array($_tmp='PDFReports_ReportFormat')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

                </td>

                <td>
                    <?php $_from = $this->_tpl_vars['reportFormatsByReportType']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['reportType'] => $this->_tpl_vars['reportFormats']):
?>
                        <select name='report_format' class='<?php echo $this->_tpl_vars['reportType']; ?>
'>
                            <?php $_from = $this->_tpl_vars['reportFormats']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['reportFormat'] => $this->_tpl_vars['reportFormatIcon']):
?>
                                <option value="<?php echo $this->_tpl_vars['reportFormat']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['reportFormat'])) ? $this->_run_mod_handler('upper', true, $_tmp) : smarty_modifier_upper($_tmp)); ?>
</option>
                            <?php endforeach; endif; unset($_from); ?>
                        </select>
                    <?php endforeach; endif; unset($_from); ?>
                </td>
            </tr>

            <?php echo smarty_function_postEvent(array('name' => 'template_reportParametersPDFReports'), $this);?>


            <tr>
                <td class="first"><?php echo ((is_array($_tmp='PDFReports_ReportsIncluded')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</td>
                <td>
                    <?php $_from = $this->_tpl_vars['reportsByCategoryByReportType']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['reportType'] => $this->_tpl_vars['reportsByCategory']):
?>
                        <div name='reportsList' class='<?php echo $this->_tpl_vars['reportType']; ?>
'>

                            <?php if ($this->_tpl_vars['allowMultipleReportsByReportType'][$this->_tpl_vars['reportType']]): ?>
                                <?php $this->assign('reportInputType', 'checkbox'); ?>
                            <?php else: ?>
                                <?php $this->assign('reportInputType', 'radio'); ?>
                            <?php endif; ?>

                            <?php $this->assign('countCategory', 0); ?>

                            <?php echo smarty_function_math(array('equation' => "ceil (reportsByCategoryCount / 2)",'reportsByCategoryCount' => count($this->_tpl_vars['reportsByCategory']),'assign' => 'newColumnAfter'), $this);?>


                            <div id='leftcolumn'>
                                <?php $_from = $this->_tpl_vars['reportsByCategory']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['reports'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['reports']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['category'] => $this->_tpl_vars['reports']):
        $this->_foreach['reports']['iteration']++;
?>
                                <?php if ($this->_tpl_vars['countCategory'] >= $this->_tpl_vars['newColumnAfter'] && $this->_tpl_vars['newColumnAfter'] != 0): ?>
                                <?php $this->assign('newColumnAfter', 0); ?>
                            </div>
                            <div id='rightcolumn'>
                                <?php endif; ?>
                                <div class='reportCategory'><?php echo $this->_tpl_vars['category']; ?>
</div>
                                <ul class='listReports'>
                                    <?php $_from = $this->_tpl_vars['reports']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['report']):
?>
                                        <li>
                                            <input type='<?php echo $this->_tpl_vars['reportInputType']; ?>
' id="<?php echo $this->_tpl_vars['reportType']; ?>
<?php echo $this->_tpl_vars['report']['uniqueId']; ?>
" report-unique-id='<?php echo $this->_tpl_vars['report']['uniqueId']; ?>
'
                                                   name='<?php echo $this->_tpl_vars['reportType']; ?>
Reports'/>
                                            <label for="<?php echo $this->_tpl_vars['reportType']; ?>
<?php echo $this->_tpl_vars['report']['uniqueId']; ?>
">
                                                <?php echo ((is_array($_tmp=$this->_tpl_vars['report']['name'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>

                                                <?php if ($this->_tpl_vars['report']['uniqueId'] == 'MultiSites_getAll'): ?>
                                                    <div class="entityInlineHelp"><?php echo ((is_array($_tmp='PDFReports_ReportIncludeNWebsites')) ? $this->_run_mod_handler('translate', true, $_tmp, ($this->_tpl_vars['countWebsites'])." ") : smarty_modifier_translate($_tmp, ($this->_tpl_vars['countWebsites'])." ")); ?>
</div>
                                                <?php endif; ?>
                                            </label>
                                        </li>
                                    <?php endforeach; endif; unset($_from); ?>
                                    <?php $this->assign('countCategory', $this->_tpl_vars['countCategory']+1); ?>
                                </ul>
                                <br/>
                                <?php endforeach; endif; unset($_from); ?>
                            </div>
                        </div>
                    <?php endforeach; endif; unset($_from); ?>
                </td>
            </tr>

            </tbody>
        </table>

        <input type="hidden" id="report_idreport" value="">
        <input type="submit" id="report_submit" name="submit" class="submit"/>

    </form>
    <div class='entityCancel'>
        <?php echo ((is_array($_tmp='General_OrCancel')) ? $this->_run_mod_handler('translate', true, $_tmp, "<a class='entityCancelLink'>", "</a>") : smarty_modifier_translate($_tmp, "<a class='entityCancelLink'>", "</a>")); ?>

    </div>
</div>