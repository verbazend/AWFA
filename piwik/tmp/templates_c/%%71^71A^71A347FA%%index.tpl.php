<?php /* Smarty version 2.6.26, created on 2013-06-05 02:27:12
         compiled from Overlay/templates/index.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'translate', 'Overlay/templates/index.tpl', 3, false),array('modifier', 'escape', 'Overlay/templates/index.tpl', 3, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "CoreHome/templates/header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<a id="Overlay_Title" href="http://piwik.org/docs/page-overlay/" target="_blank">
    <?php echo ((is_array($_tmp=((is_array($_tmp='Overlay_Overlay')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>

    <img src="themes/default/images/help.png" alt="Documentation" />
</a>

<div id="Overlay_DateRangeSelection">
    <select id="Overlay_DateRangeSelect" name="Overlay_DateRangeSelect">
        <option value="day;today"><?php echo ((is_array($_tmp=((is_array($_tmp='General_Today')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</option>
        <option value="day;yesterday"><?php echo ((is_array($_tmp=((is_array($_tmp='General_Yesterday')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</option>
        <option value="week;today"><?php echo ((is_array($_tmp=((is_array($_tmp='General_CurrentWeek')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</option>
        <option value="month;today"><?php echo ((is_array($_tmp=((is_array($_tmp='General_CurrentMonth')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</option>
        <option value="year;today"><?php echo ((is_array($_tmp=((is_array($_tmp='General_CurrentYear')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</option>
    </select>
</div>

<div id="Overlay_Error_NotLoading">
    <p>
        <span><?php echo ((is_array($_tmp=((is_array($_tmp='Overlay_ErrorNotLoading')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</span>
    </p>

    <p>
        <?php if ($this->_tpl_vars['ssl']): ?>
            <?php echo ((is_array($_tmp=((is_array($_tmp='Overlay_ErrorNotLoadingDetailsSSL')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>

        <?php else: ?>
            <?php echo ((is_array($_tmp=((is_array($_tmp='Overlay_ErrorNotLoadingDetails')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>

        <?php endif; ?>
    </p>

    <p>
        <a href="http://piwik.org/docs/page-overlay/#toc-page-overlay-troubleshooting" target="_blank">
            <?php echo ((is_array($_tmp=((is_array($_tmp='Overlay_ErrorNotLoadingLink')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>

        </a>
    </p>
</div>

<div id="Overlay_Location">&nbsp;</div>

<div id="Overlay_Loading"><?php echo ((is_array($_tmp=((is_array($_tmp='General_Loading_js')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</div>

<div id="Overlay_Sidebar"></div>

<a id="Overlay_RowEvolution"><?php echo ((is_array($_tmp=((is_array($_tmp='General_RowEvolutionRowActionTooltipTitle_js')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</a>
<a id="Overlay_Transitions"><?php echo ((is_array($_tmp=((is_array($_tmp='General_TransitionsRowActionTooltipTitle_js')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</a>

<!-- TODO: rethink the way the sidebar works -->
<!-- <a id="Overlay_FullScreen" href="#">
	<?php echo ((is_array($_tmp=((is_array($_tmp='Overlay_OpenFullScreen')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>

</a> -->


<div id="Overlay_Main">
    <iframe id="Overlay_Iframe" src="" frameborder="0"></iframe>
</div>


<script type="text/javascript">
    var iframeSrc = 'index.php?module=Overlay&action=startOverlaySession&idsite=<?php echo $this->_tpl_vars['idSite']; ?>
&period=<?php echo $this->_tpl_vars['period']; ?>
&date=<?php echo $this->_tpl_vars['date']; ?>
';
    Piwik_Overlay.init(iframeSrc, '<?php echo $this->_tpl_vars['idSite']; ?>
', '<?php echo $this->_tpl_vars['period']; ?>
', '<?php echo $this->_tpl_vars['date']; ?>
');

    Piwik_Overlay_Translations = <?php echo '{'; ?>

        domain: "<?php echo ((is_array($_tmp=((is_array($_tmp='Overlay_Domain')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
"
        <?php echo '}'; ?>
;
</script>


<!-- close tag opened in header.tpl -->
</div>
</body>
</html>