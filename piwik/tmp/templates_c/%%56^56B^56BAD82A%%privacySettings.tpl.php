<?php /* Smarty version 2.6.26, created on 2013-06-04 01:09:10
         compiled from PrivacyManager/templates/privacySettings.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'translate', 'PrivacyManager/templates/privacySettings.tpl', 4, false),array('modifier', 'inlineHelp', 'PrivacyManager/templates/privacySettings.tpl', 26, false),array('modifier', 'escape', 'PrivacyManager/templates/privacySettings.tpl', 261, false),array('function', 'url', 'PrivacyManager/templates/privacySettings.tpl', 9, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "CoreAdminHome/templates/header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php if ($this->_tpl_vars['isSuperUser']): ?>
    <h2><?php echo ((is_array($_tmp='PrivacyManager_TeaserHeadline')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</h2>
    <p><?php echo ((is_array($_tmp='PrivacyManager_Teaser')) ? $this->_run_mod_handler('translate', true, $_tmp, '<a href="#anonymizeIPAnchor">', "</a>", '<a href="#deleteLogsAnchor">', "</a>", '<a href="#optOutAnchor">', "</a>") : smarty_modifier_translate($_tmp, '<a href="#anonymizeIPAnchor">', "</a>", '<a href="#deleteLogsAnchor">', "</a>", '<a href="#optOutAnchor">', "</a>")); ?>

        See also our official guide <b><a href='http://piwik.org/privacy/' target='_blank'>Web Analytics Privacy</a></b></p>
    <a name="anonymizeIPAnchor"></a>
    <h2><?php echo ((is_array($_tmp='PrivacyManager_UseAnonymizeIp')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</h2>
    <form method="post" action="<?php echo smarty_function_url(array('action' => 'saveSettings','form' => 'formMaskLength','token_auth' => $this->_tpl_vars['token_auth']), $this);?>
" id="formMaskLength" name="formMaskLength">
        <div id='anonymizeIpSettings'>
            <table class="adminTable" style='width:800px;'>
                <tr>
                    <td width="250"><?php echo ((is_array($_tmp='PrivacyManager_UseAnonymizeIp')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
<br/>
                        <span class="form-description"><?php echo ((is_array($_tmp='PrivacyManager_AnonymizeIpDescription')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</span>
                    </td>
                    <td width='500'>
                        <label><input type="radio" name="anonymizeIPEnable" value="1" <?php if ($this->_tpl_vars['anonymizeIP']['enabled'] == '1'): ?>
                            checked <?php endif; ?>/> <?php echo ((is_array($_tmp='General_Yes')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</label>
                        <label><input type="radio" name="anonymizeIPEnable" value="0"
                                      style="margin-left:20px;" <?php if ($this->_tpl_vars['anonymizeIP']['enabled'] == '0'): ?> checked <?php endif; ?>/>  <?php echo ((is_array($_tmp='General_No')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

                        </label>
                        <input type="hidden" name="token_auth" value="<?php echo $this->_tpl_vars['token_auth']; ?>
"/>
                        <input type="hidden" name="pluginName" value="<?php echo $this->_tpl_vars['anonymizeIP']['name']; ?>
"/>
                    </td>
                    <td width="200">
                        <?php echo ((is_array($_tmp=((is_array($_tmp='AnonymizeIP_PluginDescription')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)))) ? $this->_run_mod_handler('inlineHelp', true, $_tmp) : smarty_modifier_inlineHelp($_tmp)); ?>

                    </td>
                </tr>
            </table>
        </div>
        <div id="anonymizeIPenabled">
            <table class="adminTable" style='width:800px;'>
                <tr>
                    <td width="250"><?php echo ((is_array($_tmp='PrivacyManager_AnonymizeIpMaskLengtDescription')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</td>
                    <td width="500">
                        <label><input type="radio" name="maskLength" value="1" <?php if ($this->_tpl_vars['anonymizeIP']['maskLength'] == '1'): ?>
                            checked <?php endif; ?>/> <?php echo ((is_array($_tmp='PrivacyManager_AnonymizeIpMaskLength')) ? $this->_run_mod_handler('translate', true, $_tmp, '1', "192.168.100.xxx") : smarty_modifier_translate($_tmp, '1', "192.168.100.xxx")); ?>

                        </label><br/>
                        <label><input type="radio" name="maskLength" value="2" <?php if ($this->_tpl_vars['anonymizeIP']['maskLength'] == '2'): ?>
                            checked <?php endif; ?>/> <?php echo ((is_array($_tmp='PrivacyManager_AnonymizeIpMaskLength')) ? $this->_run_mod_handler('translate', true, $_tmp, '2', "192.168.xxx.xxx") : smarty_modifier_translate($_tmp, '2', "192.168.xxx.xxx")); ?>
 <span
                                    class="form-description"><?php echo ((is_array($_tmp='General_Recommended')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</span></label><br/>
                        <label><input type="radio" name="maskLength" value="3" <?php if ($this->_tpl_vars['anonymizeIP']['maskLength'] == '3'): ?>
                            checked <?php endif; ?>/> <?php echo ((is_array($_tmp='PrivacyManager_AnonymizeIpMaskLength')) ? $this->_run_mod_handler('translate', true, $_tmp, '3', "192.xxx.xxx.xxx") : smarty_modifier_translate($_tmp, '3', "192.xxx.xxx.xxx")); ?>
</label>
                    </td>
                    <td width="200">
                        <?php echo ((is_array($_tmp=((is_array($_tmp='PrivacyManager_GeolocationAnonymizeIpNote')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)))) ? $this->_run_mod_handler('inlineHelp', true, $_tmp) : smarty_modifier_inlineHelp($_tmp)); ?>

                    </td>
                </tr>
            </table>
        </div>
        <input type="submit" value="<?php echo ((is_array($_tmp='General_Save')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
" id="privacySettingsSubmit" class="submit"/>
    </form>
    <div class="ui-confirm" id="confirmDeleteSettings">
        <h2 id="deleteLogsConfirm"><?php echo ((is_array($_tmp='PrivacyManager_DeleteLogsConfirm')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</h2>

        <h2 id="deleteReportsConfirm"><?php echo ((is_array($_tmp='PrivacyManager_DeleteReportsConfirm')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</h2>

        <h2 id="deleteBothConfirm"><?php echo ((is_array($_tmp='PrivacyManager_DeleteBothConfirm')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</h2>
        <input role="yes" type="button" value="<?php echo ((is_array($_tmp='General_Yes')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
"/>
        <input role="no" type="button" value="<?php echo ((is_array($_tmp='General_No')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
"/>
    </div>
    <div class="ui-confirm" id="saveSettingsBeforePurge">
        <h2><?php echo ((is_array($_tmp='PrivacyManager_SaveSettingsBeforePurge')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</h2>
        <input role="yes" type="button" value="<?php echo ((is_array($_tmp='General_Ok')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
"/>
    </div>
    <div class="ui-confirm" id="confirmPurgeNow">
        <h2><?php echo ((is_array($_tmp='PrivacyManager_PurgeNowConfirm')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</h2>
        <input role="yes" type="button" value="<?php echo ((is_array($_tmp='General_Yes')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
"/>
        <input role="no" type="button" value="<?php echo ((is_array($_tmp='General_No')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
"/>
    </div>
    <a name="deleteLogsAnchor"></a>
    <h2><?php echo ((is_array($_tmp='PrivacyManager_DeleteDataSettings')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</h2>
    <p><?php echo ((is_array($_tmp='PrivacyManager_DeleteDataDescription')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
 <?php echo ((is_array($_tmp='PrivacyManager_DeleteDataDescription2')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</p>
    <form method="post" action="<?php echo smarty_function_url(array('action' => 'saveSettings','form' => 'formDeleteSettings','token_auth' => $this->_tpl_vars['token_auth']), $this);?>
" id="formDeleteSettings" name="formMaskLength">
        <table class="adminTable" style='width:800px;'>
            <tr id='deleteLogSettingEnabled'>
                <td width="250"><?php echo ((is_array($_tmp='PrivacyManager_UseDeleteLog')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
<br/>

                </td>
                <td width='500'>
                    <label><input type="radio" name="deleteEnable" value="1" <?php if ($this->_tpl_vars['deleteData']['config']['delete_logs_enable'] == '1'): ?>
                        checked <?php endif; ?>/> <?php echo ((is_array($_tmp='General_Yes')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</label>
                    <label><input type="radio" name="deleteEnable" value="0"
                                  style="margin-left:20px;" <?php if ($this->_tpl_vars['deleteData']['config']['delete_logs_enable'] == '0'): ?>
                        checked <?php endif; ?>/>  <?php echo ((is_array($_tmp='General_No')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

                    </label>
				<span class="ajaxSuccess">
					<?php echo ((is_array($_tmp='PrivacyManager_DeleteLogDescription2')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

                    <a href="http://piwik.org/faq/general/#faq_125" target="_blank">
                        <?php echo ((is_array($_tmp='General_ClickHere')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

                    </a>
				</span>
                </td>
                <td width="200">
                    <?php ob_start(); ?>
                        <?php echo ((is_array($_tmp='PrivacyManager_DeleteLogInfo')) ? $this->_run_mod_handler('translate', true, $_tmp, $this->_tpl_vars['deleteData']['deleteTables']) : smarty_modifier_translate($_tmp, $this->_tpl_vars['deleteData']['deleteTables'])); ?>

                        <?php if (! $this->_tpl_vars['canDeleteLogActions']): ?>
                            <br/>
                            <br/>
                            <?php echo ((is_array($_tmp='PrivacyManager_CannotLockSoDeleteLogActions')) ? $this->_run_mod_handler('translate', true, $_tmp, $this->_tpl_vars['dbUser']) : smarty_modifier_translate($_tmp, $this->_tpl_vars['dbUser'])); ?>

                        <?php endif; ?>
                    <?php $this->_smarty_vars['capture']['default'] = ob_get_contents();  $this->assign('deleteLogInfo', ob_get_contents());ob_end_clean(); ?>
                    <?php echo ((is_array($_tmp=$this->_tpl_vars['deleteLogInfo'])) ? $this->_run_mod_handler('inlineHelp', true, $_tmp) : smarty_modifier_inlineHelp($_tmp)); ?>

                </td>
            </tr>
            <tr id="deleteLogSettings">
                <td width="250">&nbsp;</td>
                <td width="500">
                    <label><?php echo ((is_array($_tmp='PrivacyManager_DeleteLogsOlderThan')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

                        <input type="text" id="deleteOlderThan" value="<?php echo $this->_tpl_vars['deleteData']['config']['delete_logs_older_than']; ?>
" style="width:30px;"
                               name="deleteOlderThan"/>
                        <?php echo ((is_array($_tmp='CoreHome_PeriodDays')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</label><br/>
                    <span class="form-description"><?php echo ((is_array($_tmp='PrivacyManager_LeastDaysInput')) ? $this->_run_mod_handler('translate', true, $_tmp, '1') : smarty_modifier_translate($_tmp, '1')); ?>
</span>
                </td>
                <td width="200">

                </td>
            </tr>
            <tr id='deleteReportsSettingEnabled'>
                <td width="250"><?php echo ((is_array($_tmp='PrivacyManager_UseDeleteReports')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

                </td>
                <td width="500">
                    <label><input type="radio" name="deleteReportsEnable" value="1"
                                  <?php if ($this->_tpl_vars['deleteData']['config']['delete_reports_enable'] == '1'): ?>checked="true"<?php endif; ?>/> <?php echo ((is_array($_tmp='General_Yes')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</label>
                    <label><input type="radio" name="deleteReportsEnable" value="0" <?php if ($this->_tpl_vars['deleteData']['config']['delete_reports_enable'] == '0'): ?>checked="true"<?php endif; ?>
                                  style="margin-left:20px;"/> <?php echo ((is_array($_tmp='General_No')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

                    </label>
				
				<span class="ajaxSuccess">
					<?php ob_start(); ?><?php echo ((is_array($_tmp='PrivacyManager_UseDeleteLog')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
<?php $this->_smarty_vars['capture']['default'] = ob_get_contents();  $this->assign('deleteOldLogs', ob_get_contents());ob_end_clean(); ?>
                    <?php echo ((is_array($_tmp='PrivacyManager_DeleteReportsInfo')) ? $this->_run_mod_handler('translate', true, $_tmp, '<em>', '</em>') : smarty_modifier_translate($_tmp, '<em>', '</em>')); ?>

                    <span id='deleteOldReportsMoreInfo'><br/><br/>
                        <?php echo ((is_array($_tmp='PrivacyManager_DeleteReportsInfo2')) ? $this->_run_mod_handler('translate', true, $_tmp, $this->_tpl_vars['deleteOldLogs']) : smarty_modifier_translate($_tmp, $this->_tpl_vars['deleteOldLogs'])); ?>
<br/><br/>
                        <?php echo ((is_array($_tmp='PrivacyManager_DeleteReportsInfo3')) ? $this->_run_mod_handler('translate', true, $_tmp, $this->_tpl_vars['deleteOldLogs']) : smarty_modifier_translate($_tmp, $this->_tpl_vars['deleteOldLogs'])); ?>
</span>
				</span>
                </td>
                <td width="200">
                    <?php echo ((is_array($_tmp=((is_array($_tmp='PrivacyManager_DeleteReportsDetailedInfo')) ? $this->_run_mod_handler('translate', true, $_tmp, 'archive_numeric_*', 'archive_blob_*') : smarty_modifier_translate($_tmp, 'archive_numeric_*', 'archive_blob_*')))) ? $this->_run_mod_handler('inlineHelp', true, $_tmp) : smarty_modifier_inlineHelp($_tmp)); ?>

                </td>
            </tr>
            <tr id='deleteReportsSettings'>
                <td width="250">&nbsp;</td>
                <td width="500">
                    <label><?php echo ((is_array($_tmp='PrivacyManager_DeleteReportsOlderThan')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

                        <input type="text" id="deleteReportsOlderThan" value="<?php echo $this->_tpl_vars['deleteData']['config']['delete_reports_older_than']; ?>
" style="width:30px;"
                               name="deleteReportsOlderThan"/>
                        <?php echo ((is_array($_tmp='CoreHome_PeriodMonths')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

                    </label><br/>
                    <span class="form-description"><?php echo ((is_array($_tmp='PrivacyManager_LeastMonthsInput')) ? $this->_run_mod_handler('translate', true, $_tmp, '3') : smarty_modifier_translate($_tmp, '3')); ?>
</span><br/><br/>
                    <label><input type="checkbox" name="deleteReportsKeepBasic" value="1"
                                  <?php if ($this->_tpl_vars['deleteData']['config']['delete_reports_keep_basic_metrics']): ?>checked="true"<?php endif; ?>><?php echo ((is_array($_tmp='PrivacyManager_KeepBasicMetrics')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

                        <span class="form-description"><?php echo ((is_array($_tmp='General_Recommended')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</span></input>
                    </label><br/><br/>
                    <?php echo ((is_array($_tmp='PrivacyManager_KeepDataFor')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
<br/>
                    <label><input type="checkbox" name="deleteReportsKeepDay" value="1"
                                  <?php if ($this->_tpl_vars['deleteData']['config']['delete_reports_keep_day_reports']): ?>checked="true"<?php endif; ?>><?php echo ((is_array($_tmp='General_DailyReports')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</input></label><br/>
                    <label><input type="checkbox" name="deleteReportsKeepWeek" value="1"
                                  <?php if ($this->_tpl_vars['deleteData']['config']['delete_reports_keep_week_reports']): ?>checked="true"<?php endif; ?>><?php echo ((is_array($_tmp='General_WeeklyReports')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</input></label><br/>
                    <label><input type="checkbox" name="deleteReportsKeepMonth" value="1"
                                  <?php if ($this->_tpl_vars['deleteData']['config']['delete_reports_keep_month_reports']): ?>checked="true"<?php endif; ?>><?php echo ((is_array($_tmp='General_MonthlyReports')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
<span
                                class="form-description"><?php echo ((is_array($_tmp='General_Recommended')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</span></input></label><br/>
                    <label><input type="checkbox" name="deleteReportsKeepYear" value="1"
                                  <?php if ($this->_tpl_vars['deleteData']['config']['delete_reports_keep_year_reports']): ?>checked="true"<?php endif; ?>><?php echo ((is_array($_tmp='General_YearlyReports')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
<span
                                class="form-description"><?php echo ((is_array($_tmp='General_Recommended')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</span></input></label><br/>
                    <label><input type="checkbox" name="deleteReportsKeepRange" value="1"
                                  <?php if ($this->_tpl_vars['deleteData']['config']['delete_reports_keep_range_reports']): ?>checked="true"<?php endif; ?>><?php echo ((is_array($_tmp='General_RangeReports')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</input></label><br/><br/>
                    <label><input type="checkbox" name="deleteReportsKeepSegments" value="1"
                                  <?php if ($this->_tpl_vars['deleteData']['config']['delete_reports_keep_segment_reports']): ?>checked="true"<?php endif; ?>><?php echo ((is_array($_tmp='PrivacyManager_KeepReportSegments')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</input></label><br/>
                </td>
                <td width="200">

                </td>
            </tr>
            <tr id="deleteDataEstimateSect"
                <?php if ($this->_tpl_vars['deleteData']['config']['delete_reports_enable'] == '0' && $this->_tpl_vars['deleteData']['config']['delete_logs_enable'] == '0'): ?>style="display:none;"<?php endif; ?>>
                <td width="250"><?php echo ((is_array($_tmp='PrivacyManager_ReportsDataSavedEstimate')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
<br/></td>
                <td width="500">
                    <div id="deleteDataEstimate"></div>
                    <span class='loadingPiwik' style='display:none'><img
                                src='./themes/default/images/loading-blue.gif'/> <?php echo ((is_array($_tmp='General_LoadingData')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</span>
                </td>
                <td width="200">
                    <?php if ($this->_tpl_vars['deleteData']['config']['enable_auto_database_size_estimate'] == '0'): ?>
                        <?php ob_start(); ?>
                            <em><a id="getPurgeEstimateLink" class="ui-inline-help" href="#"><?php echo ((is_array($_tmp='PrivacyManager_GetPurgeEstimate')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</a></em>
                        <?php $this->_smarty_vars['capture']['default'] = ob_get_contents();  $this->assign('manualEstimate', ob_get_contents());ob_end_clean(); ?>
                        <?php echo ((is_array($_tmp=$this->_tpl_vars['manualEstimate'])) ? $this->_run_mod_handler('inlineHelp', true, $_tmp) : smarty_modifier_inlineHelp($_tmp)); ?>

                    <?php endif; ?>
                </td>
            </tr>
            <tr id="deleteSchedulingSettings">
                <td width="250"><?php echo ((is_array($_tmp='PrivacyManager_DeleteSchedulingSettings')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
<br/></td>
                <td width="500">
                    <label><?php echo ((is_array($_tmp='PrivacyManager_DeleteDataInterval')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

                        <select id="deleteLowestInterval" name="deleteLowestInterval">
                            <option <?php if ($this->_tpl_vars['deleteData']['config']['delete_logs_schedule_lowest_interval'] == '1'): ?> selected="selected" <?php endif; ?>
                                    value="1"> <?php echo ((is_array($_tmp='CoreHome_PeriodDay')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</option>
                            <option <?php if ($this->_tpl_vars['deleteData']['config']['delete_logs_schedule_lowest_interval'] == '7'): ?> selected="selected" <?php endif; ?>
                                    value="7"><?php echo ((is_array($_tmp='CoreHome_PeriodWeek')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</option>
                            <option <?php if ($this->_tpl_vars['deleteData']['config']['delete_logs_schedule_lowest_interval'] == '30'): ?> selected="selected" <?php endif; ?>
                                    value="30"><?php echo ((is_array($_tmp='CoreHome_PeriodMonth')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</option>
                        </select></label><br/><br/>
                </td>
                <td width="200">
                    <?php ob_start(); ?>
                        <?php if ($this->_tpl_vars['deleteData']['lastRun']): ?><strong><?php echo ((is_array($_tmp='PrivacyManager_LastDelete')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
:</strong>
                            <?php echo $this->_tpl_vars['deleteData']['lastRunPretty']; ?>

                            <br/>
                            <br/>
                        <?php endif; ?>
                        <strong><?php echo ((is_array($_tmp='PrivacyManager_NextDelete')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
:</strong>
                        <?php echo $this->_tpl_vars['deleteData']['nextRunPretty']; ?>

                        <br/>
                        <br/>
                        <em><a id="purgeDataNowLink" href="#"><?php echo ((is_array($_tmp='PrivacyManager_PurgeNow')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</a></em>
                        <span class='loadingPiwik' style='display:none'><img
                                    src='./themes/default/images/loading-blue.gif'/> <?php echo ((is_array($_tmp='PrivacyManager_PurgingData')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</span>
                        <span id="db-purged-message" style="display: none;"><em><?php echo ((is_array($_tmp='PrivacyManager_DBPurged')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</em></span>
                    <?php $this->_smarty_vars['capture']['default'] = ob_get_contents();  $this->assign('purgeStats', ob_get_contents());ob_end_clean(); ?>
                    <?php echo ((is_array($_tmp=$this->_tpl_vars['purgeStats'])) ? $this->_run_mod_handler('inlineHelp', true, $_tmp) : smarty_modifier_inlineHelp($_tmp)); ?>

                </td>
            </tr>
        </table>
        <input type="button" value="<?php echo ((is_array($_tmp='General_Save')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
" id="deleteLogSettingsSubmit" class="submit"/>
    </form>
    <a name="DNT"></a>
    <h2><?php echo ((is_array($_tmp='PrivacyManager_DoNotTrack_SupportDNTPreference')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</h2>
    <table class="adminTable" style='width:800px;'>
        <tr>
            <td width="650">
                <p><?php if ($this->_tpl_vars['dntSupport']): ?>
                        <?php $this->assign('action', 'deactivate'); ?>
                        <b><?php echo ((is_array($_tmp='PrivacyManager_DoNotTrack_Enabled')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</b>
                        <br/>
                        <?php echo ((is_array($_tmp='PrivacyManager_DoNotTrack_EnabledMoreInfo')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

                    <?php else: ?>
                        <?php $this->assign('action', 'activate'); ?>
                        <?php echo ((is_array($_tmp='PrivacyManager_DoNotTrack_Disabled')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
 <?php echo ((is_array($_tmp='PrivacyManager_DoNotTrack_DisabledMoreInfo')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

                    <?php endif; ?></p>
			<span style='margin-left:20px'>
			<a href='<?php echo smarty_function_url(array('module' => 'CorePluginsAdmin','token_auth' => $this->_tpl_vars['token_auth'],'action' => $this->_tpl_vars['action'],'pluginName' => 'DoNotTrack'), $this);?>
#DNT'>&rsaquo;
                <?php if ($this->_tpl_vars['dntSupport']): ?><?php echo ((is_array($_tmp='PrivacyManager_DoNotTrack_Disable')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
 <?php echo ((is_array($_tmp='General_NotRecommended')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

                <?php else: ?><?php echo ((is_array($_tmp='PrivacyManager_DoNotTrack_Enable')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
 <?php echo ((is_array($_tmp='General_Recommended')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
<?php endif; ?>
                <br/>
            </a></span>
            </td>
            <td width="200">
                <?php echo ((is_array($_tmp=((is_array($_tmp='PrivacyManager_DoNotTrack_Description')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)))) ? $this->_run_mod_handler('inlineHelp', true, $_tmp) : smarty_modifier_inlineHelp($_tmp)); ?>

            </td>
        </tr>
    </table>
<?php endif; ?>

<a name="optOutAnchor"></a>
<h2><?php echo ((is_array($_tmp='CoreAdminHome_OptOutForYourVisitors')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</h2>
<p><?php echo ((is_array($_tmp='CoreAdminHome_OptOutExplanation')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

    <?php ob_start(); ?><?php echo $this->_tpl_vars['piwikUrl']; ?>
index.php?module=CoreAdminHome&action=optOut&language=<?php echo $this->_tpl_vars['language']; ?>
<?php $this->_smarty_vars['capture']['optOutUrl'] = ob_get_contents(); ob_end_clean(); ?>
    <?php $this->assign('optOutUrl', $this->_smarty_vars['capture']['optOutUrl']); ?>
    <?php ob_start(); ?>
    <iframe frameborder="no" width="600px" height="200px" src="<?php echo $this->_smarty_vars['capture']['optOutUrl']; ?>
"></iframe><?php $this->_smarty_vars['capture']['iframeOptOut'] = ob_get_contents(); ob_end_clean(); ?>
    <code><?php echo ((is_array($_tmp=$this->_smarty_vars['capture']['iframeOptOut'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</code>
    <br/>
    <?php echo ((is_array($_tmp='CoreAdminHome_OptOutExplanationBis')) ? $this->_run_mod_handler('translate', true, $_tmp, "<a href='".($this->_tpl_vars['optOutUrl'])."' target='_blank'>", "</a>") : smarty_modifier_translate($_tmp, "<a href='".($this->_tpl_vars['optOutUrl'])."' target='_blank'>", "</a>")); ?>

</p>

<div style='height:100px'></div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "CoreAdminHome/templates/footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>