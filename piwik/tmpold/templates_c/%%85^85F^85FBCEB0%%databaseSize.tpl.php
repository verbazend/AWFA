<?php /* Smarty version 2.6.26, created on 2013-04-09 04:54:31
         compiled from PrivacyManager/templates/databaseSize.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'translate', 'PrivacyManager/templates/databaseSize.tpl', 1, false),)), $this); ?>
<p><?php echo ((is_array($_tmp='PrivacyManager_CurrentDBSize')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
: <?php echo $this->_tpl_vars['dbStats']['currentSize']; ?>
</p>
<?php if (isset ( $this->_tpl_vars['dbStats']['sizeAfterPurge'] )): ?>
<p><?php echo ((is_array($_tmp='PrivacyManager_EstimatedDBSizeAfterPurge')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
: <b><?php echo $this->_tpl_vars['dbStats']['sizeAfterPurge']; ?>
</b></p>
<?php endif; ?>
<?php if (isset ( $this->_tpl_vars['dbStats']['spaceSaved'] )): ?>
<p><?php echo ((is_array($_tmp='PrivacyManager_EstimatedSpaceSaved')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
: <?php echo $this->_tpl_vars['dbStats']['spaceSaved']; ?>
</p>
<?php endif; ?>