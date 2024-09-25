<?php
defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    // Verifica si la variable $settings está definida.
    if (!isset($settings)) {
        $settings = new admin_settingpage('local_chatgpt_plugin', get_string('pluginname', 'local_chatgpt_plugin'));
    }

    // Agregar una configuración para la clave API.
    $settings->add(new admin_setting_configtext(
        'local_chatgpt_plugin/api_key',
        get_string('apikey', 'local_chatgpt_plugin'),
        get_string('apikey_desc', 'local_chatgpt_plugin'),
        '', // Valor predeterminado.
        PARAM_TEXT
    ));

    // Agregar la página de configuración al árbol de configuraciones.
    $ADMIN->add('localplugins', $settings);
}
