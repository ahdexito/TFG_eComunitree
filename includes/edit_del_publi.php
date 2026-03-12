<?php if ($p['id_usuario'] == $_SESSION['id_usuario']): ?>
    <div class="edit-del-btns">
        <a href="./edit_publicacion.php?edit=<?= $p['id_publicacion'] ?>" class="btn-edit">
            <i class="fa-regular fa-pen-to-square"></i>Editar
        </a>

        <a href="./index.php?eliminar=<?= $p['id_publicacion'] ?>" 
            class="btn-del" 
            onclick="return confirm('¿Estás seguro de que deseas eliminar tu publicación?');">
            <i class="fa-regular fa-trash-can"></i>Eliminar
        </a>
    </div>
<?php endif; ?>