<?php /* Smarty version 2.6.26, created on 2013-04-12 06:45:05
         compiled from Transitions/templates/transitions.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'translate', 'Transitions/templates/transitions.tpl', 6, false),array('modifier', 'escape', 'Transitions/templates/transitions.tpl', 41, false),)), $this); ?>

<div id="Transitions_Container">
	<div id="Transitions_CenterBox" class="Transitions_Text">
		<h2></h2>
		<div class="Transitions_CenterBoxMetrics">
			<p class="Transitions_Pageviews Transitions_Margin"><?php echo ((is_array($_tmp=$this->_tpl_vars['translations']['pageviewsInline'])) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</p>
			
			<div class="Transitions_IncomingTraffic">
				<h3><?php echo ((is_array($_tmp='Transitions_IncomingTraffic')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</h3>
				<p class="Transitions_PreviousPages"><?php echo ((is_array($_tmp=$this->_tpl_vars['translations']['fromPreviousPagesInline'])) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</p>
				<p class="Transitions_PreviousSiteSearches"><?php echo ((is_array($_tmp=$this->_tpl_vars['translations']['fromPreviousSiteSearchesInline'])) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</p>
				<p class="Transitions_SearchEngines"><?php echo ((is_array($_tmp=$this->_tpl_vars['translations']['fromSearchEnginesInline'])) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</p>
				<p class="Transitions_Websites"><?php echo ((is_array($_tmp=$this->_tpl_vars['translations']['fromWebsitesInline'])) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</p>
				<p class="Transitions_Campaigns"><?php echo ((is_array($_tmp=$this->_tpl_vars['translations']['fromCampaignsInline'])) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</p>
				<p class="Transitions_DirectEntries"><?php echo ((is_array($_tmp=$this->_tpl_vars['translations']['directEntriesInline'])) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</p>
			</div>
			
			<div class="Transitions_OutgoingTraffic">
				<h3><?php echo ((is_array($_tmp='Transitions_OutgoingTraffic')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</h3>
				<p class="Transitions_FollowingPages"><?php echo ((is_array($_tmp=$this->_tpl_vars['translations']['toFollowingPagesInline'])) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</p>
				<p class="Transitions_FollowingSiteSearches"><?php echo ((is_array($_tmp=$this->_tpl_vars['translations']['toFollowingSiteSearchesInline'])) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</p>
				<p class="Transitions_Downloads"><?php echo ((is_array($_tmp=$this->_tpl_vars['translations']['downloadsInline'])) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</p>
				<p class="Transitions_Outlinks"><?php echo ((is_array($_tmp=$this->_tpl_vars['translations']['outlinksInline'])) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</p>
				<p class="Transitions_Exits"><?php echo ((is_array($_tmp=$this->_tpl_vars['translations']['exitsInline'])) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</p>
			</div>
		</div>
	</div>
	<div id="Transitions_Loops" class="Transitions_Text">
		<?php echo ((is_array($_tmp=$this->_tpl_vars['translations']['loopsInline'])) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
 
	</div>
	<div id="Transitions_Canvas_Background_Left" class="Transitions_Canvas_Container"></div>
	<div id="Transitions_Canvas_Background_Right" class="Transitions_Canvas_Container"></div>
	<div id="Transitions_Canvas_Left" class="Transitions_Canvas_Container"></div>
	<div id="Transitions_Canvas_Right" class="Transitions_Canvas_Container"></div>
	<div id="Transitions_Canvas_Loops" class="Transitions_Canvas_Container"></div>
</div>

<script type="text/javascript">
	var Piwik_Transitions_Translations = <?php echo '{'; ?>

		<?php $_from = $this->_tpl_vars['translations']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['internalKey'] => $this->_tpl_vars['translation']):
?>
			"<?php echo $this->_tpl_vars['internalKey']; ?>
": "<?php echo ((is_array($_tmp=$this->_tpl_vars['translation'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
",
		<?php endforeach; endif; unset($_from); ?>
		"": ""
	<?php echo '}'; ?>
;
</script>