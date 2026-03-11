<?php

namespace App\Libraries;

class OpenAIService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->client = \Config\Services::curlrequest();
        $this->apiKey = env('OPENAI_API_KEY');
    }

    public function analizarTrabajadores($trabajadores)
    {
        $prompt = "
        Analiza los siguientes trabajadores y sus horas ocupadas. 
        Dime quién está disponible actualmente para recibir más tareas basándote en que estos son los periodos en los que están ocupados.
        Si la lista está vacía, significa que todos los trabajadores registrados están disponibles.

        Trabajadores y sus horas ocupadas:
        " . json_encode($trabajadores);

        try {
            $response = $this->client->post(
                "https://api.groq.com/openai/v1/chat/completions",
                [
                    "http_errors" => false, // No lanzar excepción, capturar el cuerpo del error
                    "headers" => [
                        "Authorization" => "Bearer " . $this->apiKey,
                        "Content-Type" => "application/json"
                    ],
                    "json" => [
                        "model" => "llama-3.3-70b-versatile",
                        "messages" => [
                            [
                                "role" => "system",
                                "content" => "Eres un asistente experto en gestión de producción y análisis de carga de trabajo."
                            ],
                            [
                                "role" => "user",
                                "content" => $prompt
                            ]
                        ]
                    ]
                ]
            );

            $data = json_decode($response->getBody(), true);

            // Si hay un error detallado de OpenAI
            if (isset($data['error'])) {
                return [
                    'status' => 'error',
                    'message' => 'OpenAI detallado: ' . $data['error']['message'],
                    'type' => $data['error']['type'] ?? 'unknown'
                ];
            }

            if (isset($data['choices'][0]['message']['content'])) {
                return [
                    'status' => 'success',
                    'respuesta' => $data['choices'][0]['message']['content']
                ];
            }

            return [
                'status' => 'error',
                'message' => 'No se recibió una respuesta válida de OpenAI',
                'raw' => $data
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Error al conectar con OpenAI: ' . $e->getMessage()
            ];
        }
    }
    public function analizarPosibilidadTarea($cargaHoy, $tareaData, $personalId = null)
    {
        $nombreTarea = $tareaData['nombre'];
        $duracion = $tareaData['horas_estimadas'];
        
        $prompt = "
        Actúa como un gestor de producción experimentado. 
        Necesito saber si hoy es posible asignar una nueva tarea denominada '$nombreTarea' que requiere aproximadamente $duracion minutos de trabajo.

        Datos de carga actual para hoy:
        " . json_encode($cargaHoy) . "

        Si se especificó un usuario (ID: " . ($personalId ?? 'No especificado') . "), enfócate en él.
        
        Responde en formato JSON con la siguiente estructura:
        {
            \"posible\": true/false,
            \"mensaje\": \"Una explicación breve de por qué es posible o no\",
            \"recomendacion\": \"Qué hacer si no hay espacio (ej. asignar a otro, programar para mañana)\"
        }
        NO incluyas explicaciones fuera del JSON.";

        try {
            $response = $this->client->post(
                "https://api.groq.com/openai/v1/chat/completions",
                [
                    "http_errors" => false,
                    "headers" => [
                        "Authorization" => "Bearer " . $this->apiKey,
                        "Content-Type" => "application/json"
                    ],
                    "json" => [
                        "model" => "llama-3.3-70b-versatile",
                        "messages" => [
                            [
                                "role" => "system",
                                "content" => "Eres un asistente que solo responde en formato JSON puro."
                            ],
                            [
                                "role" => "user",
                                "content" => $prompt
                            ]
                        ],
                        "response_format" => ["type" => "json_object"]
                    ]
                ]
            );

            $data = json_decode($response->getBody(), true);
            
            if (isset($data['choices'][0]['message']['content'])) {
                $content = json_decode($data['choices'][0]['message']['content'], true);
                return [
                    'status' => 'success',
                    'result' => $content
                ];
            }

            return [
                'status' => 'error',
                'message' => 'Error al procesar respuesta de IA',
                'raw' => $data
            ];

        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Exception: ' . $e->getMessage()
            ];
        }
    }
}
