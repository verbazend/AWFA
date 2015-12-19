<?php /* Smarty version 2.6.26, created on 2013-04-09 04:41:43
         compiled from Installation/templates/systemCheck_legend.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'translate', 'Installation/templates/systemCheck_legend.tpl', 2, false),array('function', 'url', 'Installation/templates/systemCheck_legend.tpl', 10, false),)), $this); ?>
<div id="systemCheckLegend"><small>
<h2><?php echo ((is_array($_tmp='Installation_Legend')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</h2>
<br />
<img src='themes/default/images/warning.png' /> <span class="warn"><?php echo ((is_array($_tmp='General_Warning')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
: <?php echo ((is_array($_tmp='Installation_SystemCheckWarning')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</span> <br />
<img src='themes/default/images/error.png' /> <span style="color:red;font-weight:bold"><?php echo ((is_array($_tmp='General_Error')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
: <?php echo ((is_array($_tmp='Installation_SystemCheckError')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
 </span><br />
<img src='themes/default/images/ok.png' /> <span style="color:#26981C;font-weight:bold"><?php echo ((is_array($_tmp='General_Ok')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</span><br />
</small></div>

<p class="nextStep">
	<a href="<?php echo smarty_function_url(array(), $this);?>
"><?php echo ((is_array($_tmp='General_Refresh')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
 &raquo;</a>
</p>