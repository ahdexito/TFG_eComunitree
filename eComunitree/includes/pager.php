<div class="pager">
    <?php 
        // Si $params_url no existe en la página actual, se convierte en un string vacío
        $extra_params = $params_url ?? ''; 
        
        // Configuración: cuántas páginas mostrar alrededor de la actual
        $rango = 2; 
        
        // Botón Anterior
        if ($pagina > 1): ?>
            <a class="pag-arrow" href="?pag=<?= $pagina - 1 ?><?= $extra_params ?>" title="Anterior">
                <i class="fa-solid fa-chevron-left"></i>
            </a>
        <?php endif; ?>

        <?php
        // Mostrar siempre la primera página
        if ($pagina > ($rango + 1)) {
            echo '<a href="?pag=1' . $extra_params . '" class="num-link">1</a>';
            if ($pagina > ($rango + 2)) echo '<span class="dots">...</span>';
        }

        // Bucle para páginas centrales
        for ($i = max(1, $pagina - $rango); $i <= min($total_paginas, $pagina + $rango); $i++): 
            if ($i == $pagina): ?>
                <span class="num-link activo"><?= $i ?></span>
            <?php else: ?>
                <a href="?pag=<?= $i ?><?= $extra_params ?>" class="num-link"><?= $i ?></a>
            <?php endif; 
        endfor;

        // Mostrar siempre la última página
        if ($pagina < ($total_paginas - $rango)) {
            if ($pagina < ($total_paginas - $rango - 1)) echo '<span class="dots">...</span>';
            echo '<a href="?pag=' . $total_paginas . $extra_params . '" class="num-link">' . $total_paginas . '</a>';
        }
        ?>

        <?php // Botón Siguiente
        if ($pagina < $total_paginas): ?>
            <a class="pag-arrow" href="?pag=<?= $pagina + 1 ?><?= $extra_params ?>" title="Siguiente">
                <i class="fa-solid fa-chevron-right"></i>
            </a>
        <?php endif; ?>
    </div>
</div>