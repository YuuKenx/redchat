<?php
require_once('../../config.php');
require_once('lib.php');

require_login();

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/chatgpt_plugin/chatbot.php'));
$PAGE->requires->css('/local/chatgpt_plugin/styles.css');
$PAGE->set_title('PAIBot');
$PAGE->set_heading('PAIBot');

$input = '';
$response = '';

if (optional_param('input', '', PARAM_TEXT)) {
    $input = trim(optional_param('input', '', PARAM_TEXT));
    $response = obtener_respuesta_chatgpt($input);
}

echo $OUTPUT->header();
?>

<div id="chat-container1" styles="margin-bottom: 0px;, padding-bottom:0px;">
	<form id="chat-form" method="POST" action="" onsubmit="clearInput()">
		<input type="text" id="input" name="input" value="<?php echo htmlspecialchars($input); ?>" placeholder="Escribe tu mensaje..." required>
		<button type="submit">Enviar</button>
	</form>
</div>
<br>

<div id="chat-container2" styles="margin-top: 0px;,  padding-top:0px;">
    <!-- El input del usuario se mantiene en la parte superior -->
    

    <!-- Contenedor para las preguntas y respuestas -->
    <div id="chat-box">
        <div id="chat-messages">
            <!-- Mostrar la pregunta y la respuesta debajo -->
            <?php
            if (!empty($input)) {
                echo "<div class='message user-message'>" . htmlspecialchars($input) . "</div>";
            }
            if (!empty($response)) {
                echo "<div class='message bot-message'>" . htmlspecialchars($response) . "</div>";
            }
            ?>
        </div>
    </div>
</div>

<?php
echo $OUTPUT->footer();
?>
