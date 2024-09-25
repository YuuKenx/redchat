<?php
defined('MOODLE_INTERNAL') || die();

function obtener_respuesta_chatgpt($input) {
    global $CFG;

    $api_key = get_config('local_chatgpt_plugin', 'api_key');
    if (empty($api_key)) {
        return 'No se ha configurado la clave API.';
    }

    $url = 'https://api.openai.com/v1/chat/completions';

    // Base de conocimiento en el contexto
    $base_conocimiento = [
	"¿Qué significa PAIBot?" => "PAIBot se compone de dos partes. La primera es PAI, que significa Plataforma de Autoaprendizaje de Idiomas, que es la plataforma en la que se encuentra el bot. La segunda parte es BOT; un bot, abreviatura de robot, es un programa de software diseñado para realziar tareas automatizadas a través de Internet",
	"¿Cómo te llamas?" => "Mi nombre es PAIBot, soy el bot asistente de la Plataforma de Autoaprendizaje de Idiomas y estoy aquí para ayudarte con todas las dudas que tengas.",
        "¿Por qué cuando entro a la plataforma garza y le doy clic a la opción de AUTOAPRENDIZAJE me regresa al inicio de la plataforma garza?" => "Para entrar a la plataforma de AUTOAPRENDIZAJE se realiza en forma automática una validación la cual verifica que tengas en tu carga académica una asignatura en idiomas, que estés inscrito en el semestre en forma correcta y que tu asignatura esté en Syllabus. Si no tienes una asignatura en idiomas en tu carga académica no tienes acceso a la opción de AUTOAPRENDIZAJE. También puede ser por prórroga de pago de inscripción, menos de 5 días hábiles desde tu pago, cambios de grupo o asignatura, falta de docente asignado, entre otros.",
    
    "¿En qué fecha puedo realizar las actividades del parcial?" => "Tienes 3 formas de conocer los periodos en las que puedes realizar tus parciales: 1.-Cuando entras en la plataforma de AUTOAPRENDIZAJE, la primera interfaz que ves se le llama tablero, en la parte derecha se encuentra el calendario en donde encontrarás las fechas establecidas para cada periodo. 2.-En cada actividad tiene al entrar a cada uno la fecha de inicio y la fecha de cierre. 3.-En el micrositio de la DAI: http://dai.uaeh.edu.mx/horario_gral.html",
    
    "¿Qué significa validarme en la plataforma de AUTOAPRENDIZAJE?" => "Cuando entras a la plataforma garza y le das clic a la opción de AUTOAPRENDIZAJE, se realiza automáticamente una búsqueda en la base de datos institucional para comprobar que estés inscrito en una asignatura de idiomas y te permita entrar además de asignarte el parcial correspondiente a tu asignatura. Esto lo realiza cada vez que entras, por lo que según el parcial que esté vigente es en el que te valida.",
    
    "¿Hasta cuándo me puedo validar en un parcial?" => "Hasta un día antes de que inicie el siguiente parcial, aunque el periodo para realizar tus actividades ya haya concluido.",
    
    "¿Cuántos parciales tiene mi asignatura?" => "3 parciales",
    
    "¿Qué encuentro en cada parcial?" => "La plataforma te ofrece 4 actividades digitales y 4 sesiones de producción e interacción.",
    
    "¿Qué contiene una actividad digital?" => "La actividad digital está conformada por 2 exámenes, uno de gramática y vocabulario y otro examen de comprensión lectora o auditiva, de recursos complementarios (material disponible para seguir reforzando el tema) y de referencias.",
    
    "¿Debo realizar todas las actividades digitales y todas las sesiones de producción?" => "No es necesario, se te ofrecen esas 8 opciones para que mejores tu práctica en el idioma. Lo recomendable es que realices 4 opciones, las que tú desees.",
    
    "¿Cuántas evaluaciones debo realizar por parcial?" => "4 evaluaciones",
    
    "¿En dónde puedo realizar mis actividades digitales?" => "Las actividades digitales las puedes realizar desde casa o desde tu celular.",
    
    "¿Cómo se obtiene la calificación de cada actividad digital?" => "Se promedian los 2 exámenes para obtener el promedio de cada actividad digital.",
    
    "¿Cómo obtengo las calificaciones de las sesiones de producción e interacción (SPI)?" => "La sesión de producción e interacción las cuales se realizan en las áreas de los CAI con los asesores y ellos te asignan una calificación en la \"sesión\" del parcial.",
    
    "¿Cómo realizo las sesiones de producción e interacción (SPI)?" => "Debes realizar una reservación en el Sistema de Reservaciones (SiRe) en el siguiente enlace: http://sistemas.uaeh.edu.mx/dsa/sire/index.php y reservar tu lugar en el área que desees, acudir a tu sesión y un asesor te atenderá.",
    
    "¿Hay puntos extras?" => "Sí, en las sesiones de producción e interacción.",
    
    "¿Cómo se obtienen los puntos extras?" => "Se obtienen en las 2 primeras semanas de cada parcial. Si tienes una calificación aprobatoria en la primera semana obtienes 10 puntos extras y en la segunda semana son 5 puntos extras.",
    
    "¿Qué sucede si hago más de 4 evaluaciones o sesiones de producción e interacción (SPI) por parcial?" => "Si realizas más de 4 opciones disponibles, además de adquirir más práctica, obtendrás mejor promedio, sumando 40 acreditas 10 de calificación.",
    
    "¿Qué sucede si hago menos de las 4 evaluaciones o sesiones de producción e interacción (SPI) por parcial?" => "Si realizas menos de 4 evaluaciones, se suman tus calificaciones y se dividen entre 4.",
    
    "¿Cuántos intentos tengo para realizar mi evaluación?" => "2 intentos para cada evaluación en la plataforma.",
    
    "¿Qué calificación es la que toman en cuenta para mi evaluación cuando realicé los 2 intentos?" => "La calificación más alta.",
    
    "¿Qué hacer si una calificación de una sesión de producción e interacción (SPI) no se ve en la plataforma?" => "No siempre se reflejan de forma inmediata, debes permitir por lo menos 24 horas para que se te asigne la calificación.",
    
    "Sí ya pasaron las 24 horas y aún no se ve la calificación de la sesión de producción e interacción (SPI), ¿qué hago?" => "Debes acudir al área donde realizaste la sesión y decirle al asesor que te atendió que aún no has podido ver tu calificación para que revisen la situación.",
    
    "¿Por qué me sale el error \"Parámetro inválido\"?" => "El navegador que estás utilizando en tu dispositivo tiene configurada la traducción automática. Te recomendamos desactivarla o nunca traducir este sitio.",
    
    "¿La plataforma de autoaprendizaje sólo se puede trabajar en inglés?" => "La intención de que esté en inglés es para que tengas una inmersión en el idioma, pero puedes cambiar el idioma de la plataforma para los menús y espacios. El cambio lo puedes realizar en la parte superior derecha en donde aparecen tus iniciales y desglosas el menú que tiene, escoges la opción de \"language\" y seleccionas el idioma que desees.",
    
    "¿Por qué me sale el error \"500\"?" => "Este error suele salir cuando nuestra plataforma se encuentra saturada de actividades. Te recomendamos recargar la página o esperar unos minutos para que funcione con eficiencia. Te aseguramos que estamos trabajando para que puedas acceder lo antes posible.",
    
    "¿Qué es el autoaprendizaje?" => "El autoaprendizaje es un proceso en el que una persona adquiere conocimientos o habilidades de forma independiente, sin la necesidad de un instructor o un entorno educativo formal. Implica tomar la iniciativa y responsabilidad de tu propio aprendizaje, utilizando recursos disponibles como libros, videos, cursos en línea, aplicaciones y otros medios.",
    
    "¿Qué idiomas puedo practicar?" => "Inglés, alemán, francés.",
    
    "¿Qué niveles puedo encontrar?" => "Para Bachillerato: Alemán: A1.1, A1.2, Consolidación alemán A1, A2.1, A2.2, Consolidación alemán A2. Francés: A1.1, A1.2, Consolidación francés, A1, A2.1, A2.2, Consolidación francés A2. Inglés: A1.1, A1.2, Consolidación inglés A1, A2.1, A2.2, Consolidación inglés A2, B1.1, B1.2, B1.3, B1.4, B1.5. Licenciatura (General): Conversaciones Introductorias, Lengua Extranjera, Eventos Pasados y Futuros, Logros y Experiencias, Decisiones Personales, Causa y Efecto, En Otras Palabras. Licenciatura (ESP): Comercio exterior: Inglés especialización I, Inglés especialización II. Turismo: Inglés-Propósitos específicos para el turismo I, Inglés-Propósitos específicos para el turismo II. Preparación TOEFL.",
    
    "Ejercicios recomendados" => "En la Dirección de Autoaprendizaje de Idiomas (DAI), te recomendamos aprovechar los centros de autoaprendizaje de idiomas para realizar actividades presenciales que te ayuden a mejorar tu dominio del idioma. También ponemos a tu disposición una variedad de actividades digitales para que sigas practicando y aprendiendo de manera autodidacta desde cualquier lugar y a tu propio ritmo.",
    
    "¿Qué recursos puedo encontrar en la plataforma?" => "Además de encontrar las actividades digitales, podrás practicar con imágenes, videos, audios, y también tendrás acceso a nuestros Interactive Books, los cuales podrás leer y escuchar para practicar tu comprensión lectora y auditiva.",
    
    "¿Qué puedo encontrar en los Interactive Books?" => "En los Interactive Books, encontrarás una amplia variedad de libros que te ayudarán a mejorar tu comprensión lectora y auditiva. Algunos de los títulos disponibles incluyen Little Women, Murder, One Pair of Eyes, The Black Cat, Doctor Faustus, entre otros.",
    
    "¿Para qué me sirven los Interactive Books?" => "Los Interactive Books te ayudarán a comprender la lectura y a saber cómo se pronuncian ciertas palabras que quizás no tenías en tu vocabulario. También incluyen ejercicios para poner a prueba tu comprensión de la lectura.",
    
    "¿Qué tipo de actividades me puedo encontrar en los Interactive Books?" => "Las actividades incluidas en estos libros son similares a las que encuentras en un examen, e incluyen ejercicios como relación de columnas, preguntas de opción múltiple, escritura (writing), entre otros.",
    
    "¿Cuánto tiempo debería dedicar al día para realizar estas actividades?" => "El tiempo recomendado varía según tu objetivo y disponibilidad, pero en general, dedicar entre 30 minutos a 1 hora diaria de estudio enfocado es ideal para avanzar de nivel de forma consistente.",
    
    "¿Cómo puedo mejorar mi pronunciación?" => "Para mejorar tu pronunciación, te recomendamos asistir al centro de autoaprendizaje de idiomas más cercano para realizar actividades que fortalezcan tus habilidades de habla y pronunciación.",
    
    "¿Cómo puedo mejorar mi comprensión lectora?" => "Para mejorar tu comprensión lectora, te recomendamos dedicar al menos 30 minutos a 1 hora diaria para realizar las actividades digitales y familiarizarte con el idioma.",
    
    "¿Cómo puedo mejorar mi comprensión auditiva?" => "Para mejorar tu comprensión auditiva, te recomendamos dedicar al menos 30 minutos a 1 hora diaria para asistir a las sesiones en los centros de autoaprendizaje o realizar las actividades digitales.",
    
    "¿En la plataforma puedo hacer reservaciones?" => "No, las reservaciones de las áreas de servicio las haces en http://sistemas.uaeh.edu.mx/dsa/sire/index.php.",
    
    "¿En la plataforma puedo verificar la disponibilidad de áreas de servicio para reservar?" => "No, puedes hacer reservaciones en http://sistemas.uaeh.edu.mx/dsa/sire/index.php y checar la disponibilidad ahí mismo. Para revisar los horarios: https://dai.uaeh.edu.mx/horario.html.",
    
    "¿Qué hacer si me estanco en mi aprendizaje?" => "No te frustres, es normal sentirse estancado. Asiste a asesorías personalizadas en nuestros centros de aprendizaje y refuerza tu confianza con las actividades digitales. Aprender debe ser una experiencia emocionante, no estresante.",
    
    "¿Es necesario practicar todos los días para ver avances?" => "Lo recomendable es practicar todos los días para que tu oído y tu habla se familiaricen con el idioma, facilitando su comprensión y uso.",
    
    "¿Puedo usar traductor?" => "No te recomendamos usar traductor para comunicarte en el idioma, ya que puede alterar el mensaje. Usa el traductor para mejorar y ampliar tu vocabulario, no para interpretar el idioma.",
    
    "No tengo acceso a la plataforma de autoaprendizaje." => "Existen 3 posibles razones: No estás validado en Syllabus porque no se ha realizado el pago de inscripción, no tienes asignatura de idiomas en tu carga académica, o la asignatura de idiomas no está validada en Syllabus.",
    
    "¿Qué hago si no estoy validado?" => "Realiza el pago o verifica con el Secretario Académico de tu Escuela o Instituto para que esté validado.",
    
    "¿Qué hacer si no tengo asignatura de idiomas cargada?" => "Verifica que la asignatura esté correctamente registrada en la carga académica. Si no aparece, contacta a Administración Escolar de tu Instituto.",
    
    "¿Qué hacer si la asignatura no está validada en Syllabus?" => "Corroborarlo con el Secretario Académico de tu Escuela o Instituto.",
    
    "No se visualizan correctamente los vídeos, audios y/o imágenes de la práctica/evaluación." => "Verifica que tu conexión a internet sea estable. Si el problema persiste, pide ayuda en los centros de autoaprendizaje para reportar el error.",
    
    "No tengo calificación en la sección de EVALUATION." => "Únicamente cuando hayas concluido una evaluación podrás tener una calificación. Verifica en el menú izquierdo en la sección de GRADES. Si no tienes calificación, puede ser que no has realizado la evaluación en la secuencia didáctica o que el asesor aún no ha asignado la calificación.",
    
    "¿Dónde puedo ver el calendario de actividades del micrositio de la DAI?" => "Puedes visualizarlo en las fechas del tablero de la plataforma, o también ingresar a tu curso en la plataforma de autoaprendizaje para ver las fechas de inicio y finalización de tus actividades."

    ];

    // Verificar si el input coincide con una de las preguntas de la base de conocimiento
    if (array_key_exists($input, $base_conocimiento)) {
        return $base_conocimiento[$input];
    }

    // Si no hay coincidencia, envía la pregunta a la API
    $url = 'https://api.openai.com/v1/chat/completions';
    $data = array(
        'model' => 'gpt-4-turbo',
        'messages' => array(
            array(
                'role' => 'system',
                'content' => 'Actúa como un experto en la plataforma de autoaprendizaje y responde las preguntas basadas en la siguiente base de conocimientos: ' . json_encode($base_conocimiento, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            ),
            array(
                'role' => 'user',
                'content' => $input
            )
        ),
        'max_tokens' => 150,
        'temperature' => 1,
    );

    $headers = array(
        'Authorization: Bearer ' . $api_key,
        'Content-Type: application/json',
    );

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    curl_setopt($ch, CURLOPT_CAINFO, 'E:\inetpub\wwwroot\dai23\local\cacert.pem'); // Ajusta esta ruta

    $response = curl_exec($ch);
    
    if ($response === false) {
        $error_code = curl_errno($ch);
        $error_msg = curl_error($ch);
        curl_close($ch);
        return 'Error en la solicitud cURL: ' . $error_msg . ' (Código de error: ' . $error_code . ')';
    }

     curl_close($ch);

    $result = json_decode($response, true);

    if (isset($result['error'])) {
        return 'Error en la respuesta de la API: ' . $result['error']['message'];
    }

    if (isset($result['choices'][0]['message']['content'])) {
        return $result['choices'][0]['message']['content'];
    } else {
        return 'Error al obtener la respuesta de ChatGPT.';
    }
}
?>
