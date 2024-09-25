<?php
require_once(__DIR__.'/../../config.php');
require_login();

$PAGE->set_url(new moodle_url('/local/chatgpt_plugin/index.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('pluginname', 'local_chatgpt_plugin'));
$PAGE->set_heading(get_string('pluginname', 'local_chatgpt_plugin'));
$PAGE->requires->css('/local/chatgpt_plugin/styles.css');


echo $OUTPUT->header();

if (isset($_POST['user_input'])) {
    $input = trim($_POST['user_input']);
    if (!empty($input)) {
        $response = obtener_respuesta_chatgpt($input);
        echo "<p><strong>Respuesta de ChatGPT:</strong></p><p>$response</p>";
    } else {
        echo "<p><strong>Por favor, ingresa una pregunta.</strong></p>";
    }
}

echo '<form method="post">';
echo '<label for="user_input">Escribe tu pregunta:</label>';
echo '<input type="text" name="user_input" id="user_input" size="50">';
echo '<input type="submit" value="Enviar">';
echo '</form>';

echo $OUTPUT->footer();
?>
