<?php /* Smarty version 2.6.26, created on 2013-06-05 07:09:10
         compiled from PDFReports/templates/index.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'translate', 'PDFReports/templates/index.tpl', 10, false),array('function', 'ajaxErrorDiv', 'PDFReports/templates/index.tpl', 13, false),array('function', 'ajaxLoadingDiv', 'PDFReports/templates/index.tpl', 14, false),)), $this); ?>
<?php $this->assign('showSitesSelection', true); ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "CoreHome/templates/header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div class="top_controls_inner">
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "CoreHome/templates/period_select.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>

<div class="centerLargeDiv">
    <h2><?php echo ((is_array($_tmp='PDFReports_ManageEmailReports')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</h2>

    <div class="entityContainer">
        <?php echo smarty_function_ajaxErrorDiv(array(), $this);?>

        <?php echo smarty_function_ajaxLoadingDiv(array(), $this);?>

        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "PDFReports/templates/list.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "PDFReports/templates/add.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
        <a id='bottom'></a>
    </div>
</div>

<div class="ui-confirm" id="confirm">
    <h2><?php echo ((is_array($_tmp='PDFReports_AreYouSureDeleteReport')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
</h2>
    <input role="yes" type="button" value="<?php echo ((is_array($_tmp='General_Yes')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
"/>
    <input role="no" type="button" value="<?php echo ((is_array($_tmp='General_No')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
"/>
</div>

<script type="text/javascript">
    var ReportPlugin = new Object();
    ReportPlugin.defaultPeriod = '<?php echo $this->_tpl_vars['defaultPeriod']; ?>
';
    ReportPlugin.defaultHour = '<?php echo $this->_tpl_vars['defaultHour']; ?>
';
    ReportPlugin.defaultReportType = '<?php echo $this->_tpl_vars['defaultReportType']; ?>
';
    ReportPlugin.defaultReportFormat = '<?php echo $this->_tpl_vars['defaultReportFormat']; ?>
';
    ReportPlugin.reportList = <?php echo $this->_tpl_vars['reportsJSON']; ?>
;
    ReportPlugin.createReportString = "<?php echo ((is_array($_tmp='PDFReports_CreateReport')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
";
    ReportPlugin.updateReportString = "<?php echo ((is_array($_tmp='PDFReports_UpdateReport')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
";
    <?php echo '
    $(document).ready(function () {
        initManagePdf();
    });
</script>
    <style type="text/css">
        .reportCategory {
            font-weight: bold;
            margin-bottom: 5px;
        }
    </style>
'; ?>
