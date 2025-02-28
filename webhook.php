<?php
    const TOKEN_CODESTROKES = "CODESTROKES-API-CHATBOT-JORIAL";
    const WEBHOOK_URL = "https://chatbot.jorial.com.mx/webhook.php";

    require_once("config/connection.php");
    require_once("models/Register.php");

    function verifyToken($req,$res){
        try{
            $token = $req['hub_verify_token'];
            $challenge = $req['hub_challenge'];

            // error_log("verifyToken - Token: $token, Challenge: $challenge"); // Comment out for debugging

            if (isset($challenge) && isset($token) && $token == TOKEN_CODESTROKES){
                $res->send($challenge);
            }else{
                $res ->status(400)->send();
            }

        }catch(Exception $e){
            error_log("verifyToken - Exception: " . $e->getMessage());
            $res ->status(400)->send();
        }
    }

    function receiveMessages($req,$res){
        try{
            $entry = $req['entry'][0];
            $changes = $entry['changes'][0];
            $value = $changes['value'];
            $messageobj = $value['messages'];

            // error_log("receiveMessages - Entry: " . json_encode($entry)); // Comment out for debugging

            if ($messageobj){
                $messages  = $messageobj[0];

                // error_log("receiveMessages - Messages: " . json_encode($messages)); // Comment out for debugging

                if(array_key_exists("type",$messages)){
                    $type = $messages["type"];

                    if($type == "interactive"){
                        $type_interactive = $messages["interactive"]["type"];

                        if($type_interactive == "button_reply"){

                            $comment = $messages["interactive"]["button_reply"]["id"];
                            $number = $messages['from'];
                            $number = preg_replace('/^(\d{2,3})1/', '$1', $number);

                            error_log("receiveMessages - Button Reply: Comment: $comment, Number: $number"); // Comment out for debugging

                            SendMessageWhatsApp($comment,$number);

                            $register = new Register();
                            $register->insert_register($number,$comment);

                        }else if($type_interactive == "list_reply"){

                            $comment = $messages["interactive"]["list_reply"]["id"];
                            $number = $messages['from'];
                            $number = preg_replace('/^(\d{2,3})1/', '$1', $number);

                            // error_log("receiveMessages - List Reply: Comment: $comment, Number: $number"); // Comment out for debugging

                            SendMessageWhatsApp($comment,$number);

                            $register = new Register();
                            $register->insert_register($number,$comment);

                        }

                    }

                    if (array_key_exists("text",$messages)){
                        $comment = $messages['text']['body'];
                        $number = $messages['from'];
                        $number = preg_replace('/^(\d{2,3})1/', '$1', $number);

                        // error_log("receiveMessages - Text: Comment: $comment, Number: $number"); // Comment out for debugging

                        SendMessageWhatsApp($comment,$number);

                        $register = new Register();
                        $register->insert_register($number,$comment);
                    }

                }
            }

            echo json_encode(['message' => 'EVENT_RECEIVED']);
            exit;
        }catch(Exception $e){
            error_log("receiveMessages - Exception: " . $e->getMessage());
            echo json_encode(['message' => 'EVENT_RECEIVED']);
            exit;
        }
    }

    function notifyAgent($number, $random) {
        $data = json_encode([
            "messaging_product" => "whatsapp",
            "recipient_type" => "individual",
            "to" => "527226085413",
            "type" => "text",
            "text" => array(
                "preview_url" => false,
                "body" => "El cliente con número: " .  $number . ". Y número de folio: " . $random . " ha solicitado hablar con un asesor, por favor contáctalo."
            )
        ]);
    }

    function SendMessageWhatsApp($comment,$number){
        $comment = strtolower($comment);

        // error_log("SendMessageWhatsApp - Comment: $comment, Number: $number"); // Comment out for debugging

        if (strpos($comment,'hola') !== false){
            $data = json_encode([
                "messaging_product" => "whatsapp",
                "recipient_type" => "individual",
                "to" => $number,
                "type" => "image",
                "image" => [
                    "link" => "https://www.jorial.com.mx/imagenes/18b62e3f7d28a4559a75b5952c387e38.jpg",
                    "caption" => "¡Saludos, humano! 👋\n\nSoy *Jorialy*, tu asistente virtual de *Tecnologías Plásticas Jorial*. 🐴✨\n\nEstoy aquí para ayudarte a encontrar justo lo que necesitas para tu negocio. Cuéntame, ¿en qué puedo asistirte hoy? 💼🔎\n\nVisita nuestra web para más información: www.jorial.com.mx 🌐"
                ]
            ]);
        }else if ($comment=='1') {
            $data = json_encode([
                "messaging_product" => "whatsapp",    
                "recipient_type"=> "individual",
                "to" => $number,
                "type" => "text",
                "text"=> [
                    "preview_url" => false,
                    "body"=> "En Tecnologías Plásticas Jorial llevamos 28 años ofreciendo soluciones para empresas como la tuya. 🤝✨\n\nNos especializamos en la venta de plásticos, insumos, materias primas, productos de higiene, decoración y herramientas de repostería, adaptándonos a cada necesidad con productos de alta calidad y sostenibilidad. Más que proveedores, somos aliados estratégicos. 🚀\n\nTrabajamos contigo para garantizar que encuentres exactamente lo que necesitas, con un servicio ágil y confiable.\n\nNuestra visión es seguir creciendo y expandiéndonos a nuevos mercados, manteniendo siempre nuestro compromiso con la innovación, el medio ambiente y la confianza mutua. 🌍💼"
                ]
            ]);
        }else if ($comment=='2') {
            $data = json_encode([
                "messaging_product" => "whatsapp",    
                "recipient_type"=> "individual",
                "to" => $number,
                "type" => "location",
                "location" => [
                    "latitude" => 19.340449376579496,
                    "longitude" => -99.60413044528148,
                    "name" => "Tecnologías Plásticas Jorial",
                    "address" => "Ignacio Allende 7, Sta Cruz Otzacatipan, 50210 Santa Cruz Otzacatipan, Méx."
                ]
            ]);
        }else if ($comment == '3') {
            $data = json_encode([
                "messaging_product" => "whatsapp",
                "to" => $number,
                "type" => "interactive",
                "interactive" => [
                    "type" => "list",
                    "body" => [
                        "text" => "Nuestro catálogo de productos"
                    ],
                    "footer" => [
                        "text" => "Seleciona el catálogo que necesites"
                    ],
                    "action" => [
                        "button" => "Ver catálogos",
                        "sections" => [
                            [
                                "title" => "Categoría 1:",
                                "rows" => [
                                    [
                                        "id" => "ctg1",
                                        "title" => "Limpieza y Mantenimiento",
                                        "description" => "Soluciones de higiene para oficinas, hoteles, restaurantes y comercios."
                                    ]
                                ]
                            ],[
                                "title" => "Categoría 2:",
                                "rows" => [
                                    [
                                        "id" => "ctg2",
                                        "title" => "Industria y salud",
                                        "description" => "Cofias, cubrebocas y escafandras para industrias y hospitales."
                                    ]
                                ]
                            ]
                            ,[
                                "title" => "Categoría 3:",	
                                "rows" => [
                                    [
                                        "id" => "ctg3",
                                        "title" => "Empaques y Embalaje",
                                        "description" => "Diseñamos bolsas a la medida para cada empresa e industria."
                                    ]
                                ]
                            ],[
                                "title" => "Categoría 4:",
                                "rows" => [
                                    [
                                        "id" => "ctg4",
                                        "title" => "Eventos y celebraciones",
                                        "description" => "Decoración y desechables para eventos sociales y corporativos."
                                    ]
                                ]
                            ],[
                                "title" => "Categoría 5:",	
                                "rows" => [
                                    [
                                        "id" => "ctg5",
                                        "title" => "Alimentos y Bebidas",
                                        "description" => "Empaques desechables para comida rápida, cafeterías y restaurantes."
                                    ]
                                ]
                            ],[
                                "title" => "Categoría 6:",
                                "rows" => [
                                    [
                                        "id" => "ctg6",
                                        "title" => "Restaurantes y Catering",
                                        "description" => "Insumos y materias primas para alimentos."
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
        } else if (strpos($comment,'ctg1') !== false){
            $data = json_encode([
                "messaging_product" => "whatsapp",    
                "recipient_type"=> "individual",
                "to" => $number,
                "type" => "document",
                "document"=> [
                    "link" => "https://acortar.link/UVwoHN",
                    "caption" => "Limpieza y Mantenimiento"
                ]
            ]);
        } else if (strpos($comment,'ctg2') !== false){
            $data = json_encode([
                "messaging_product" => "whatsapp",    
                "recipient_type"=> "individual",
                "to" => $number,
                "type" => "document",
                "document"=> [
                    "link" => "https://acortar.link/YoSP28",
                    "caption" => "Industria y salud"
                ]
            ]);
        } else if (strpos($comment,'ctg3') !== false){
            $data = json_encode([
                "messaging_product" => "whatsapp",    
                "recipient_type"=> "individual",
                "to" => $number,
                "type" => "document",
                "document"=> [
                    "link" => "https://acortar.link/zJeSBn",
                    "caption" => "Empaques y Embalaje"
                ]
            ]);
        } else if (strpos($comment,'ctg4') !== false){
            $data = json_encode([
                "messaging_product" => "whatsapp",    
                "recipient_type"=> "individual",
                "to" => $number,
                "type" => "document",
                "document"=> [
                    "link" => "https://acortar.link/PRFC5P",
                    "caption" => "Eventos y celebraciones"
                ]
            ]);
        } else if (strpos($comment,'ctg5') !== false){
            $data = json_encode([
                "messaging_product" => "whatsapp",    
                "recipient_type"=> "individual",
                "to" => $number,
                "type" => "document",
                "document"=> [
                    "link" => "https://acortar.link/Szlikc",
                    "caption" => "Alimentos y Bebidas"
                ]
            ]);
        } else if (strpos($comment,'ctg6') !== false){
            $data = json_encode([
                "messaging_product" => "whatsapp",    
                "recipient_type"=> "individual",
                "to" => $number,
                "type" => "document",
                "document"=> [
                    "link" => "https://acortar.link/M92M4h",
                    "caption" => "Restaurantes y Catering"
                ]
            ]);
        } else if ($comment=='4') {
            $random = rand(1000, 9999);
            $data = json_encode([
                "messaging_product" => "whatsapp",
                "recipient_type" => "individual",
                "to" => $number,
                "type" => "text",
                "text" => array(
                    "preview_url" => false,
                    "body" => "Uno de nuestros asesores te contactará pronto, tu número de folio es: *" . $random . "*, guardalo para cualquier aclaración.\n\nGracias por esperar. ⏳"
                )
            ]);
            $register = new Register();
            $register->insert_ticket($random, $number);
            notifyAgent($number, $random);
        }else if ($comment=='5') {
            $data = json_encode([
                "messaging_product" => "whatsapp",
                "to" => $number,
                "text" => array(
                    "preview_url" => true,
                    "body" => "📅 Horario de Atención: Lunes a Sábado. \n🕜 Horario: 7:00 a.m. a 5:00 p.m. 🤓"
                )
            ]);
        }else if (strpos($comment,'gracias') !== false || strpos($comment,'gracias!') !== false || strpos($comment,'muchas gracias!') !== false || strpos($comment,'muchas gracias') !== false || strpos($comment,'mil gracias') !== false) {
            $data = json_encode([
            "messaging_product" => "whatsapp",
            "recipient_type" => "individual",
            "to" => $number,
            "type" => "text",
            "text" => array(
                "preview_url" => false,
                "body" => "Gracias a ti, estamos para servirte. 😊🌟"
            )
            ]);
        }else if (strpos($comment,'adios') !== false || strpos($comment,'bye') !== false || strpos($comment,'nos vemos') !== false || strpos($comment,'adiós') !== false){
            $data = json_encode([
                "messaging_product" => "whatsapp",
                "recipient_type" => "individual",
                "to" => $number,
                "type" => "text",
                "text" => array(
                    "preview_url" => false,
                    "body" => "Gracias por comunicarte con nosotros, hasta pronto. 🌟"
                )
            ]);
        }
        // CHATGPT API - Needs payment
        // else if (strpos($comment,'gchatgpt:')!== false){
        //     $texto_sin_gchatgpt = str_replace("gchatgpt: ", "", $comment);

        //     $apiKey = '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';

        //     $data = [
        //         'model' => 'text-davinci-003',
        //         'prompt' => $texto_sin_gchatgpt,
        //         'temperature' => 0.7,
        //         'max_tokens' => 300,
        //         'n' => 1,
        //         'stop' => ['\n']
        //     ];

        //     $ch = curl_init('https://api.openai.com/v1/completions');
        //     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        //     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //     curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        //         'Content-Type: application/json',
        //         'Authorization: Bearer ' . $apiKey
        //     ));

        //     $response = curl_exec($ch);
        //     $responseArr = json_decode($response, true);

        //     $data = json_encode([
        //         "messaging_product" => "whatsapp",
        //         "recipient_type" => "individual",
        //         "to" => $number,
        //         "type" => "text",
        //         "text" => array(
        //             "preview_url" => false,
        //             "body" => $responseArr['choices'][0]['text']
        //         )
        //     ]);
        // }
        else{
            $data = json_encode([
                "messaging_product" => "whatsapp",
                "recipient_type"=> "individual",
                "to" => $number,
                "type" => "text",
                "text"=> [
                    "preview_url" => false,
                    "body"=> "Sigo aprendiendo para brindarte un mejor servicio. 🐴✨\n\nPor favor, selecciona alguna de las opciónes:\n\n1️⃣. Información general. ❔\n2️⃣. Ubicación del CEDIS. 📍\n3️⃣. Enviar catálogo de productos. 📄\n4️⃣. Hablar con un asesor. 🙋‍♂️\n5️⃣. Horario de Atención. 🕜"
                ]
            ]);
        }

        $options = [
            'http' => [
                'method' => 'POST',
                'header' => "Content-type: application/json\r\nAuthorization: Bearer EAAsAgN9ei0YBO00GBk3FNpXZBDKtWSPBF95KGU1ymjcZBf1DG2XvgFsndP06Jk9XjLmXOlwwN1kaZB9CrovmZAwExzvK4dnuIY5Xa6ZCus1ecDrFi97ZCo1hMYjrnZBgdZCZAHZBa9f5lgRFzZBRZCCGUfK4dZAM1Xa9cNol6ZBGt43uUvbZC3vwDNN34bdMZCOX3YqNApuJq5Yu4MgPiaOfJ0ZBg9FadT6LMXFwZD\r\n",
                'content' => $data,
                'ignore_errors' => true
            ]
        ];

        $context = stream_context_create($options);
        $response = file_get_contents('https://graph.facebook.com/v22.0/510747558798423/messages', false, $context);

        if ($response === false) {
            error_log("SendMessageWhatsApp - Error al enviar el mensaje");
            echo "Error al enviar el mensaje\n";
        } else {
            error_log("SendMessageWhatsApp - Mensaje enviado correctamente");
            echo "Mensaje enviado correctamente\n";
        }
    }

    if ($_SERVER['REQUEST_METHOD']==='POST'){
        $input = file_get_contents('php://input');
        $data = json_decode($input,true);

        // error_log("POST Request - Data: " . json_encode($data)); // Comment out for debugging

        receiveMessages($data,http_response_code());
    }else if($_SERVER['REQUEST_METHOD']==='GET'){
        if(isset($_GET['hub_mode']) && isset($_GET['hub_verify_token']) && isset($_GET['hub_challenge']) && $_GET['hub_mode'] === 'subscribe' && $_GET['hub_verify_token'] === TOKEN_CODESTROKES){
            error_log("GET Request - Challenge: " . $_GET['hub_challenge']);
            echo $_GET['hub_challenge'];
        }else{
            error_log("GET Request - Forbidden");
            http_response_code(403);
        }
    }
?>