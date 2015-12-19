<?php /* Smarty version 2.6.26, created on 2013-06-04 01:09:12
         compiled from UserCountry/templates/updaterSetup.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'translate', 'UserCountry/templates/updaterSetup.tpl', 2, false),array('modifier', 'inlineHelp', 'UserCountry/templates/updaterSetup.tpl', 22, false),)), $this); ?>
<div id="geoipdb-update-info" <?php if (! $this->_tpl_vars['geoIPDatabasesInstalled']): ?>style="display:none"<?php endif; ?>>
    <p><?php echo ((is_array($_tmp='UserCountry_GeoIPUpdaterInstructions')) ? $this->_run_mod_handler('translate', true, $_tmp, '<a href="http://www.maxmind.com/en/download_files?rId=piwik" _target="blank">', '</a>', '<a href="http://www.maxmind.com/?rId=piwik">', '</a>') : smarty_modifier_translate($_tmp, '<a href="http://www.maxmind.com/en/download_files?rId=piwik" _target="blank">', '</a>', '<a href="http://www.maxmind.com/?rId=piwik">', '</a>')); ?>

        <br/><br/>
<?php echo ((is_array($_tmp='UserCountry_GeoLiteCityLink')) ? $this->_run_mod_handler('translate', true, $_tmp, "<a href=\"".($this->_tpl_vars['geoLiteUrl'])."\">", $this->_tpl_vars['geoLiteUrl'], '</a>') : smarty_modifier_translate($_tmp, "<a href=\"".($this->_tpl_vars['geoLiteUrl'])."\">", $this->_tpl_vars['geoLiteUrl'], '</a>')); ?>

	<?php if ($this->_tpl_vars['geoIPDatabasesInstalled']): ?>
	<br/><br/><?php echo ((is_array($_tmp='UserCountry_GeoIPUpdaterIntro')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
:
	<?php endif; ?>
	</p>
	<table class="adminTable" style="width:900px">
		<tr>
			<th><?php echo ((is_array($_tmp='Live_GoalType')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</th>
			<th><?php echo ((is_array($_tmp='Actions_ColumnDownloadURL')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</th>
			<th></th>
		</tr>
		<tr>
			<td width="140"><?php echo ((is_array($_tmp='UserCountry_LocationDatabase')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</td>
			<td><input type="text" id="geoip-location-db" value="<?php echo $this->_tpl_vars['geoIPLocUrl']; ?>
"/></td>
			<td width="164">
				<?php ob_start(); ?>
				<?php echo ((is_array($_tmp='UserCountry_LocationDatabaseHint')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

				<?php $this->_smarty_vars['capture']['default'] = ob_get_contents();  $this->assign('locationHint', ob_get_contents());ob_end_clean(); ?>
				<?php echo ((is_array($_tmp=$this->_tpl_vars['locationHint'])) ? $this->_run_mod_handler('inlineHelp', true, $_tmp) : smarty_modifier_inlineHelp($_tmp)); ?>

			</td>
		</tr>
		<tr>
			<td width="140"><?php echo ((is_array($_tmp='UserCountry_ISPDatabase')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</td>
			<td><input type="text" id="geoip-isp-db" value="<?php echo $this->_tpl_vars['geoIPIspUrl']; ?>
"/></td>
		</tr>
		<tr>
			<td width="140"><?php echo ((is_array($_tmp='UserCountry_OrgDatabase')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</td>
			<td><input type="text" id="geoip-org-db" value="<?php echo $this->_tpl_vars['geoIPOrgUrl']; ?>
"/></td>
		</tr>
		<tr>
			<td width="140"><?php echo ((is_array($_tmp='UserCountry_DownloadNewDatabasesEvery')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</td>
			<td id="geoip-update-period-cell">
				<input type="radio" name="geoip-update-period" value="month" id="geoip-update-period-month" <?php if ($this->_tpl_vars['geoIPUpdatePeriod'] == 'month'): ?>checked="checked"<?php endif; ?>/>
				<label for="geoip-update-period-month"><?php echo ((is_array($_tmp='CoreHome_PeriodMonth')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</label>
				
				<input type="radio" name="geoip-update-period" value="week" id="geoip-update-period-week" <?php if ($this->_tpl_vars['geoIPUpdatePeriod'] == 'week'): ?>checked="checked"<?php endif; ?>/>
				<label for="geoip-update-period-week"><?php echo ((is_array($_tmp='CoreHome_PeriodWeek')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</label>
			</td>
			<td width="164">
			<?php ob_start(); ?>
				<?php if (! empty ( $this->_tpl_vars['lastTimeUpdaterRun'] )): ?>
					<?php echo ((is_array($_tmp='UserCountry_UpdaterWasLastRun')) ? $this->_run_mod_handler('translate', true, $_tmp, $this->_tpl_vars['lastTimeUpdaterRun']) : smarty_modifier_translate($_tmp, $this->_tpl_vars['lastTimeUpdaterRun'])); ?>

				<?php else: ?>
					<?php echo ((is_array($_tmp='UserCountry_UpdaterHasNotBeenRun')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

				<?php endif; ?>
			<?php $this->_smarty_vars['capture']['default'] = ob_get_contents();  $this->assign('lastTimeRunNote', ob_get_contents());ob_end_clean(); ?>
			<?php echo ((is_array($_tmp=$this->_tpl_vars['lastTimeRunNote'])) ? $this->_run_mod_handler('inlineHelp', true, $_tmp) : smarty_modifier_inlineHelp($_tmp)); ?>

			</td>
		</tr>
	</table>
	<p style="display:inline-block;vertical-align:top">
		<input type="button" class="submit" value="<?php if (! $this->_tpl_vars['geoIPDatabasesInstalled']): ?><?php echo ((is_array($_tmp='General_Continue')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
<?php else: ?><?php echo ((is_array($_tmp='General_Save')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
<?php endif; ?>" id="update-geoip-links"/>
	</p>
	<div style="display:inline-block;width:700px">
		<span style="display:none" class="ajaxSuccess" id="done-updating-updater"><?php echo ((is_array($_tmp='General_Done')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
!</span>
		<span id="geoipdb-update-info-error" style="display:none" class="error"></span>
		<div id="geoip-progressbar-container" style="display:none">
			<div id="geoip-updater-progressbar"></div>
			<span id="geoip-updater-progressbar-label"></span>
		</div>
	</div>
</div>