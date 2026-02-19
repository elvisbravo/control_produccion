<?php

if (!function_exists('extraerColorAleatorio')) {
    function extraerColorAleatorio()
    {
        $colores = [
            "#FF6B6B",
            "#FF8E72",
            "#FFA94D",
            "#FFD166",
            "#FFE066",
            "#FFF3B0",
            "#D4F1F4",
            "#A9DEF9",
            "#89C2D9",
            "#61A5C2",
            "#4CC9F0",
            "#72EFDD",
            "#80FFDB",
            "#B9FBC0",
            "#CDB4DB",
            "#D0BFFF",
            "#E0AAFF",
            "#F1C0E8",
            "#FFAFCC",
            "#FFC8DD",
            "#FDE2E4",
            "#E2F0CB",
            "#CDEAC0",
            "#B5EAD7",
            "#9BF6FF",
            "#A0C4FF",
            "#BDB2FF",
            "#FFC6FF",
            "#FDFFB6",
            "#CAFFBF",
            "#FEC5BB",
            "#FCD5CE",
            "#FAE1DD",
            "#E8EDDF",
            "#D8F3DC",
            "#B7E4C7",
            "#95D5B2",
            "#74C69D",
            "#52B788",
            "#48CAE4",
            "#90E0EF",
            "#ADE8F4",
            "#CAF0F8",
            "#F8F9FA",
            "#E9ECEF",
            "#DEE2E6",
            "#CED4DA",
            "#F4A261",
            "#E76F51",
            "#FFB703"
        ];

        return $colores[array_rand($colores)];
    }
}
