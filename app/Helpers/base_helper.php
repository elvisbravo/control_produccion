<?php

if (!function_exists('extraerColorAleatorio')) {
    function extraerColorAleatorio()
    {
        $colores = [
            "#E63946",
            "#D62828",
            "#C1121F",
            "#B56576",
            "#9D4EDD",
            "#7B2CBF",
            "#6A4C93",
            "#5A189A",
            "#4361EE",
            "#3A0CA3",
            "#3F37C9",
            "#1D3557",
            "#264653",
            "#2A9D8F",
            "#21867A",
            "#1B998B",
            "#2B9348",
            "#2D6A4F",
            "#40916C",
            "#386641",
            "#6A994E",
            "#7F5539",
            "#9C6644",
            "#A47148",
            "#BC6C25",
            "#D4A373",
            "#8D99AE",
            "#6C757D",
            "#495057",
            "#343A40",
            "#F77F00",
            "#E85D04",
            "#DC2F02",
            "#FF8800",
            "#F4A261",
            "#E76F51",
            "#C44536",
            "#A44A3F",
            "#7F4F24",
            "#9A031E",
            "#5F0F40",
            "#7209B7",
            "#560BAD",
            "#480CA8",
            "#3A0CA3",
            "#4895EF",
            "#4361EE",
            "#3F37C9",
            "#277DA1",
            "#4D908E"
        ];

        return $colores[array_rand($colores)];
    }
}
