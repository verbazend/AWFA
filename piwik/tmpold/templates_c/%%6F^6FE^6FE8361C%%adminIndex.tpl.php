<?php /* Smarty version 2.6.26, created on 2013-04-09 04:54:58
         compiled from UserCountry/templates/adminIndex.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'loadJavascriptTranslations', 'UserCountry/templates/adminIndex.tpl', 1, false),array('modifier', 'translate', 'UserCountry/templates/adminIndex.tpl', 4, false),array('modifier', 'inlineHelp', 'UserCountry/templates/adminIndex.tpl', 74, false),)), $this); ?>
<?php echo smarty_function_loadJavascriptTranslations(array('plugins' => 'UserCountry'), $this);?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "CoreAdminHome/templates/header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<h2 id="location-providers"><?php echo ((is_array($_tmp='UserCountry_Geolocation')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</h2>

<div style="width:900px">

<p><?php echo ((is_array($_tmp='UserCountry_GeolocationPageDesc')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</p>

<?php if (! $this->_tpl_vars['isThereWorkingProvider']): ?>
<h3 style="margin-top:0"><?php echo ((is_array($_tmp='UserCountry_HowToSetupGeoIP')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</h3>
<p><?php echo ((is_array($_tmp='UserCountry_HowToSetupGeoIPIntro')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</p>

<ul style="list-style:disc;margin-left:2em">
	<li><?php echo ((is_array($_tmp='UserCountry_HowToSetupGeoIP_Step1')) ? $this->_run_mod_handler('translate', true, $_tmp, '<a href="http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz">', '</a>', '<a target="_blank" href="http://www.maxmind.com/?rId=piwik">', '</a>') : smarty_modifier_translate($_tmp, '<a href="http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz">', '</a>', '<a target="_blank" href="http://www.maxmind.com/?rId=piwik">', '</a>')); ?>
</li>
	<li><?php echo ((is_array($_tmp='UserCountry_HowToSetupGeoIP_Step2')) ? $this->_run_mod_handler('translate', true, $_tmp, "'GeoLiteCity.dat'", '<strong>', '</strong>') : smarty_modifier_translate($_tmp, "'GeoLiteCity.dat'", '<strong>', '</strong>')); ?>
</li>
	<li><?php echo ((is_array($_tmp='UserCountry_HowToSetupGeoIP_Step3')) ? $this->_run_mod_handler('translate', true, $_tmp, '<strong>', '</strong>', '<span style="color:green"><strong>', '</strong></span>') : smarty_modifier_translate($_tmp, '<strong>', '</strong>', '<span style="color:green"><strong>', '</strong></span>')); ?>
</li>
	<li><?php echo ((is_array($_tmp='UserCountry_HowToSetupGeoIP_Step4')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</li>
</ul>

<p>&nbsp;</p>
<?php endif; ?>

<table class="adminTable locationProviderTable">
	<tr>
		<th><?php echo ((is_array($_tmp='UserCountry_LocationProvider')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</th>
		<th><?php echo ((is_array($_tmp='General_Description')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</th>
		<th><?php echo ((is_array($_tmp='General_InfoFor')) ? $this->_run_mod_handler('translate', true, $_tmp, $this->_tpl_vars['thisIP']) : smarty_modifier_translate($_tmp, $this->_tpl_vars['thisIP'])); ?>
</th>
	</tr>
	<?php $_from = $this->_tpl_vars['locationProviders']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['id'] => $this->_tpl_vars['provider']):
?>
	<tr>
		<td width="140">
			<p>
				<input class="location-provider" name="location-provider" value="<?php echo $this->_tpl_vars['id']; ?>
" type="radio" <?php if ($this->_tpl_vars['currentProviderId'] == $this->_tpl_vars['id']): ?>checked="checked"<?php endif; ?> id="provider_input_<?php echo $this->_tpl_vars['id']; ?>
" <?php if ($this->_tpl_vars['provider']['status'] != 1): ?>disabled="disabled"<?php endif; ?>/>
				<label for="provider_input_<?php echo $this->_tpl_vars['id']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['provider']['title'])) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</label><br/>
				<span class='loadingPiwik' style='display:none'><img src='./themes/default/images/loading-blue.gif' /></span>
				<span class="ajaxSuccess" style='display:none'><?php echo ((is_array($_tmp='General_Done')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</span>
			</p>
			<p class="loc-provider-status">
				<strong><em>
				<?php if ($this->_tpl_vars['provider']['status'] == 0): ?>
				<span class="is-not-installed"><?php echo ((is_array($_tmp='General_NotInstalled')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</span>
				<?php elseif ($this->_tpl_vars['provider']['status'] == 1): ?>
				<span class="is-installed"><?php echo ((is_array($_tmp='General_Installed')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</span>
				<?php elseif ($this->_tpl_vars['provider']['status'] == 2): ?>
				<span class="is-broken"><?php echo ((is_array($_tmp='General_Broken')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</span>
				<?php endif; ?>
				</em></strong>
			</p>
		</td>
		<td>
			<p><?php echo ((is_array($_tmp=$this->_tpl_vars['provider']['description'])) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</p>
			<?php if ($this->_tpl_vars['provider']['status'] != 1 && isset ( $this->_tpl_vars['provider']['install_docs'] )): ?>
			<p><?php echo $this->_tpl_vars['provider']['install_docs']; ?>
</p>
			<?php endif; ?>
		</td>
		<td width="164">
		<?php if ($this->_tpl_vars['provider']['status'] == 1): ?>
			<?php ob_start(); ?>
			<?php if ($this->_tpl_vars['thisIP'] != '127.0.0.1'): ?>
			<?php echo ((is_array($_tmp='UserCountry_CurrentLocationIntro')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
:
			<div style="text-align:left;">
				<br/>
				<span class='loadingPiwik' style='display:none;position:absolute'><img src='./themes/default/images/loading-blue.gif' /> <?php echo ((is_array($_tmp='General_Loading_js')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</span>
				<span class='location'><strong><em><?php echo $this->_tpl_vars['provider']['location']; ?>
</em></strong></span>
			</div>
			<div style="text-align:right;">
				<a href="#" class="refresh-loc" data-impl-id="<?php echo $this->_tpl_vars['id']; ?>
"><em><?php echo ((is_array($_tmp='Dashboard_Refresh_js')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</em></a>
			</div>
			<?php else: ?>
			<?php echo ((is_array($_tmp='UserCountry_CannotLocalizeLocalIP')) ? $this->_run_mod_handler('translate', true, $_tmp, $this->_tpl_vars['thisIP']) : smarty_modifier_translate($_tmp, $this->_tpl_vars['thisIP'])); ?>

			<?php endif; ?>
			<?php $this->_smarty_vars['capture']['default'] = ob_get_contents();  $this->assign('currentLocation', ob_get_contents());ob_end_clean(); ?>
			<?php echo ((is_array($_tmp=$this->_tpl_vars['currentLocation'])) ? $this->_run_mod_handler('inlineHelp', true, $_tmp) : smarty_modifier_inlineHelp($_tmp)); ?>

		<?php endif; ?>
		<?php if (isset ( $this->_tpl_vars['provider']['statusMessage'] ) && $this->_tpl_vars['provider']['statusMessage']): ?>
			<?php ob_start(); ?>
				<?php if ($this->_tpl_vars['provider']['status'] == 2): ?><strong><em><?php echo ((is_array($_tmp='General_Error')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
:</strong></em> <?php endif; ?><?php echo $this->_tpl_vars['provider']['statusMessage']; ?>

			<?php $this->_smarty_vars['capture']['default'] = ob_get_contents();  $this->assign('brokenReason', ob_get_contents());ob_end_clean(); ?>
			<?php echo ((is_array($_tmp=$this->_tpl_vars['brokenReason'])) ? $this->_run_mod_handler('inlineHelp', true, $_tmp) : smarty_modifier_inlineHelp($_tmp)); ?>

		<?php endif; ?>
		<?php if (isset ( $this->_tpl_vars['provider']['extra_message'] ) && $this->_tpl_vars['provider']['extra_message']): ?>
			<?php ob_start(); ?>
			<?php echo $this->_tpl_vars['provider']['extra_message']; ?>

			<?php $this->_smarty_vars['capture']['default'] = ob_get_contents();  $this->assign('extraMessage', ob_get_contents());ob_end_clean(); ?>
			<br/>
			<?php echo ((is_array($_tmp=$this->_tpl_vars['extraMessage'])) ? $this->_run_mod_handler('inlineHelp', true, $_tmp) : smarty_modifier_inlineHelp($_tmp)); ?>

		<?php endif; ?>
		</td>
	<?php endforeach; endif; unset($_from); ?>
</table>

</div>

<?php if (! $this->_tpl_vars['geoIPDatabasesInstalled']): ?>
<h2 id="geoip-db-mangement"><?php echo ((is_array($_tmp='UserCountry_GeoIPDatabases')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</h2>
<?php else: ?>
<h2 id="geoip-db-mangement"><?php echo ((is_array($_tmp='UserCountry_SetupAutomaticUpdatesOfGeoIP_js')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</h2>
<?php endif; ?>

<?php if ($this->_tpl_vars['showGeoIPUpdateSection']): ?>
<div id="manage-geoip-dbs" style="width:900px" class="adminTable">

<?php if (! $this->_tpl_vars['geoIPDatabasesInstalled']): ?>
<div id="geoipdb-screen1">
    <p><?php echo ((is_array($_tmp='UserCountry_PiwikNotManagingGeoIPDBs')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</p>
	<div class="geoipdb-column-1">
		<p><?php echo ((is_array($_tmp='UserCountry_IWantToDownloadFreeGeoIP')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</p>
		<input type="button" class="submit" value="<?php echo ((is_array($_tmp='General_GetStarted')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
..." id="start-download-free-geoip"/>
	</div>
	<div class="geoipdb-column-2">
		<p><?php echo ((is_array($_tmp='UserCountry_IPurchasedGeoIPDBs')) ? $this->_run_mod_handler('translate', true, $_tmp, '<a href="http://www.maxmind.com/en/geolocation_landing?rId=piwik">', '</a>') : smarty_modifier_translate($_tmp, '<a href="http://www.maxmind.com/en/geolocation_landing?rId=piwik">', '</a>')); ?>
</p>
		<input type="button" class="submit" value="<?php echo ((is_array($_tmp='General_GetStarted')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
..." id="start-automatic-update-geoip"/>
	</div>
</div>
<div id="geoipdb-screen2-download" style="display:none">
	<p class='loadingPiwik'><img src='./themes/default/images/loading-blue.gif' /><?php echo ((is_array($_tmp='UserCountry_DownloadingDb')) ? $this->_run_mod_handler('translate', true, $_tmp, "<a href=\"".($this->_tpl_vars['geoLiteUrl'])."\">GeoLiteCity.dat</a>") : smarty_modifier_translate($_tmp, "<a href=\"".($this->_tpl_vars['geoLiteUrl'])."\">GeoLiteCity.dat</a>")); ?>
...</p>
	<div id="geoip-download-progress"></div>
</div>
<?php endif; ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "UserCountry/templates/updaterSetup.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php else: ?>
<p style="width:900px" class="form-description"><?php echo ((is_array($_tmp='UserCountry_CannotSetupGeoIPAutoUpdating')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</p>
<?php endif; ?>
</div>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "CoreAdminHome/templates/footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
