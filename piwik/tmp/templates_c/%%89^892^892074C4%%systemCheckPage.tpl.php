<?php /* Smarty version 2.6.26, created on 2013-06-04 01:15:58
         compiled from Installation/templates/systemCheckPage.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'translate', 'Installation/templates/systemCheckPage.tpl', 4, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "CoreAdminHome/templates/header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php if ($this->_tpl_vars['isSuperUser']): ?>
    <h2><?php echo ((is_array($_tmp='Installation_SystemCheck')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</h2>
    <p style="margin-left:1em"><?php if ($this->_tpl_vars['infos']['has_errors']): ?>
            <img src='themes/default/images/error.png'/>
            <?php echo ((is_array($_tmp='Installation_SystemCheckSummaryThereWereErrors')) ? $this->_run_mod_handler('translate', true, $_tmp, '<strong>', '</strong>', '<strong><em>', '</em></strong>') : smarty_modifier_translate($_tmp, '<strong>', '</strong>', '<strong><em>', '</em></strong>')); ?>
 <?php echo ((is_array($_tmp='Installation_SeeBelowForMoreInfo')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

        <?php elseif ($this->_tpl_vars['infos']['has_warnings']): ?>
            <img src='themes/default/images/warning.png'/>
            <?php echo ((is_array($_tmp='Installation_SystemCheckSummaryThereWereWarnings')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
 <?php echo ((is_array($_tmp='Installation_SeeBelowForMoreInfo')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

        <?php else: ?>
            <img src='themes/default/images/ok.png'/>
            <?php echo ((is_array($_tmp='Installation_SystemCheckSummaryNoProblems')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>

        <?php endif; ?></p>
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "Installation/templates/systemCheckSection.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php endif; ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "CoreAdminHome/templates/footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>