<?php /* Smarty version 2.6.26, created on 2013-06-04 01:20:32
         compiled from Live/templates/simpleLastVisitCount.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'json_encode', 'Live/templates/simpleLastVisitCount.tpl', 107, false),array('modifier', 'escape', 'Live/templates/simpleLastVisitCount.tpl', 107, false),array('modifier', 'translate', 'Live/templates/simpleLastVisitCount.tpl', 108, false),)), $this); ?>
<?php echo '
<script type="text/javascript">
    $(document).ready(function () {
        var refreshWidget = function (element, refreshAfterXSecs) {
            // if the widget has been removed from the DOM, abort
            if ($(element).parent().length == 0) {
                return;
            }

            var lastMinutes = $(element).attr(\'data-last-minutes\') || 3,
                    translations = JSON.parse($(element).attr(\'data-translations\'));

            var ajaxRequest = new ajaxHelper();
            ajaxRequest.addParams({
                module: \'API\',
                method: \'Live.getCounters\',
                format: \'json\',
                lastMinutes: lastMinutes
            }, \'get\');
            ajaxRequest.setFormat(\'json\');
            ajaxRequest.setCallback(function (data) {
                data = data[0];

                // set text and tooltip of visitors count metric
                var visitors = data[\'visitors\'];
                if (visitors == 1) {
                    var visitorsCountMessage = translations[\'one_visitor\'];
                }
                else {
                    var visitorsCountMessage = translations[\'visitors\'].replace(\'%s\', visitors);
                }
                $(\'.simple-realtime-visitor-counter\', element)
                        .attr(\'title\', visitorsCountMessage)
                        .find(\'div\').text(visitors);

                // set text of individual metrics spans
                var metrics = $(\'.simple-realtime-metric\', element);

                var visitsText = data[\'visits\'] == 1
                        ? translations[\'one_visit\'] : translations[\'visits\'].replace(\'%s\', data[\'visits\']);
                $(metrics[0]).text(visitsText);

                var actionsText = data[\'actions\'] == 1
                        ? translations[\'one_action\'] : translations[\'actions\'].replace(\'%s\', data[\'actions\']);
                $(metrics[1]).text(actionsText);

                var lastMinutesText = lastMinutes == 1
                        ? translations[\'one_minute\'] : translations[\'minutes\'].replace(\'%s\', lastMinutes);
                $(metrics[2]).text(lastMinutesText);

                // schedule another request
                setTimeout(function () { refreshWidget(element, refreshAfterXSecs); }, refreshAfterXSecs * 1000);
            });
            ajaxRequest.send(true);
        };

        var initSimpleRealtimeVisitorWidget = function (refreshAfterXSecs) {
            $(\'.simple-realtime-visitor-widget\').each(function () {
                var self = this;
                if ($(self).attr(\'data-inited\')) {
                    return;
                }

                $(self).attr(\'data-inited\', 1);

                setTimeout(function () { refreshWidget(self, refreshAfterXSecs); }, refreshAfterXSecs * 1000);
            });
        };

        initSimpleRealtimeVisitorWidget('; ?>
<?php echo $this->_tpl_vars['refreshAfterXSecs']; ?>
<?php echo ');
    });
</script>
    <style>
        .simple-realtime-visitor-widget {
            text-align: center;
        }

        .simple-realtime-visitor-counter {
            background-color: #F1F0EB;

            -moz-border-radius: 10px;
            -webkit-border-radius: 10px;
            border-radius: 10px;
            display: inline-block;
            margin: 2em 0 1em 0;
        }

        .simple-realtime-visitor-counter > div {
            font-size: 4.0em;
            padding: .25em .5em .25em .5em;
            color: #444;
        }

        .simple-realtime-metric {
            font-style: italic;
            font-weight: bold;
            color: #333;
        }

        .simple-realtime-elaboration {
            margin: 1em 2em 1em 2em;
            color: #666;
            display: inline-block;
        }
    </style>
'; ?>

<div class='simple-realtime-visitor-widget' data-last-minutes="<?php echo $this->_tpl_vars['lastMinutes']; ?>
" data-translations="<?php echo ((is_array($_tmp=json_encode($this->_tpl_vars['translations']))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
">
    <div class='simple-realtime-visitor-counter' title="<?php if ($this->_tpl_vars['visitors'] == 1): ?><?php echo ((is_array($_tmp='Live_NbVisitor')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
<?php else: ?><?php echo ((is_array($_tmp='Live_NbVisitors')) ? $this->_run_mod_handler('translate', true, $_tmp, $this->_tpl_vars['visitors']) : smarty_modifier_translate($_tmp, $this->_tpl_vars['visitors'])); ?>
<?php endif; ?>">
        <div><?php echo $this->_tpl_vars['visitors']; ?>
</div>
    </div>
    <br/>

    <div class='simple-realtime-elaboration'>
        <?php ob_start(); ?><span class="simple-realtime-metric"
                                              data-metric="visits"><?php if ($this->_tpl_vars['visits'] == 1): ?><?php echo ((is_array($_tmp='General_OneVisit')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
<?php else: ?><?php echo ((is_array($_tmp='General_NVisits')) ? $this->_run_mod_handler('translate', true, $_tmp, $this->_tpl_vars['visits']) : smarty_modifier_translate($_tmp, $this->_tpl_vars['visits'])); ?>
<?php endif; ?></span><?php $this->_smarty_vars['capture']['default'] = ob_get_contents();  $this->assign('visitsMessage', ob_get_contents());ob_end_clean(); ?>
        <?php ob_start(); ?><span class="simple-realtime-metric"
                                               data-metric="actions"><?php if ($this->_tpl_vars['actions'] == 1): ?><?php echo ((is_array($_tmp='General_OneAction')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
<?php else: ?><?php echo ((is_array($_tmp='VisitsSummary_NbActionsDescription')) ? $this->_run_mod_handler('translate', true, $_tmp, $this->_tpl_vars['actions']) : smarty_modifier_translate($_tmp, $this->_tpl_vars['actions'])); ?>
<?php endif; ?></span><?php $this->_smarty_vars['capture']['default'] = ob_get_contents();  $this->assign('actionsMessage', ob_get_contents());ob_end_clean(); ?>
        <?php ob_start(); ?><span class="simple-realtime-metric"
                                               data-metric="minutes"><?php if ($this->_tpl_vars['lastMinutes'] == 1): ?><?php echo ((is_array($_tmp='General_OneMinute')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp)); ?>
<?php else: ?><?php echo ((is_array($_tmp='General_NMinutes')) ? $this->_run_mod_handler('translate', true, $_tmp, $this->_tpl_vars['lastMinutes']) : smarty_modifier_translate($_tmp, $this->_tpl_vars['lastMinutes'])); ?>
<?php endif; ?></span><?php $this->_smarty_vars['capture']['default'] = ob_get_contents();  $this->assign('minutesMessage', ob_get_contents());ob_end_clean(); ?>

        <?php echo ((is_array($_tmp='Live_SimpleRealTimeWidget_Message')) ? $this->_run_mod_handler('translate', true, $_tmp, $this->_tpl_vars['visitsMessage'], $this->_tpl_vars['actionsMessage'], $this->_tpl_vars['minutesMessage']) : smarty_modifier_translate($_tmp, $this->_tpl_vars['visitsMessage'], $this->_tpl_vars['actionsMessage'], $this->_tpl_vars['minutesMessage'])); ?>

    </div>
</div>