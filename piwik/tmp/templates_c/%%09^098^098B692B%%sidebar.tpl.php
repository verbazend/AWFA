<?php /* Smarty version 2.6.26, created on 2013-06-05 02:27:16
         compiled from Overlay/templates/sidebar.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'translate', 'Overlay/templates/sidebar.tpl', 5, false),array('modifier', 'escape', 'Overlay/templates/sidebar.tpl', 5, false),)), $this); ?>
<div> <!-- Wrapper is needed that the html can be jqueryfied -->

    <!-- This div is removed by JS and the content is put in the location div -->
    <div class="Overlay_Location">
        <b><?php echo ((is_array($_tmp=((is_array($_tmp='Overlay_Location')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
:</b>
		<span data-normalized-url="<?php echo ((is_array($_tmp=$this->_tpl_vars['normalizedUrl'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" data-label="<?php echo ((is_array($_tmp=$this->_tpl_vars['label'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
">
			<?php echo ((is_array($_tmp=$this->_tpl_vars['location'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>

		</span>
    </div>

    <?php if (count ( $this->_tpl_vars['data'] )): ?>
        <h2 class="Overlay_MainMetrics"><?php echo ((is_array($_tmp=((is_array($_tmp='Overlay_MainMetrics')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</h2>
        <?php $_from = $this->_tpl_vars['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['metric']):
?>
            <div class="Overlay_Metric">
                <span class="Overlay_MetricValue"><?php echo $this->_tpl_vars['metric']['value']; ?>
</span> <?php echo ((is_array($_tmp=$this->_tpl_vars['metric']['name'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>

            </div>
        <?php endforeach; endif; unset($_from); ?>
    <?php else: ?>
        <!-- note: the class Overlay_NoData is used in index.js -->
        <div class="Overlay_NoData"><?php echo ((is_array($_tmp=((is_array($_tmp='Overlay_NoData')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</div>
    <?php endif; ?>

</div>