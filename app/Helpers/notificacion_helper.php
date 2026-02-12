<?php

if (!function_exists('crear_notificacion')) {
    function crear_notificacion($usuario_id, $remitente_id, $titulo, $mensaje, $tipo = 'info', $prioridad = 1)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('notificaciones');

        $data = [
            'usuario_id' => $usuario_id,
            'remitente_id' => $remitente_id,
            'titulo' => $titulo,
            'mensaje' => $mensaje,
            'tipo' => $tipo,
            'prioridad' => $prioridad,
            'es_leida' => false,
            'fecha_lectura' => null
        ];

        return $builder->insert($data);
    }
}

if (!function_exists('crear_notificacion_masiva')) {
    function crear_notificacion_masiva($usuarios_ids, $remitente_id, $titulo, $mensaje, $tipo = 'info', $prioridad)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('notificacion');

        $data = [];
        foreach ($usuarios_ids as $usuario_id) {
            $data[] = [
                'usuario_id' => $usuario_id,
                'remitente_id' => $remitente_id,
                'titulo' => $titulo,
                'mensaje' => $mensaje,
                'tipo' => $tipo,
                'prioridad' => $prioridad,
                'es_leida' => false,
                'fecha_lectura' => date('Y-m-d H:i:s')
            ];
        }

        return $builder->insertBatch($data);
    }
}
