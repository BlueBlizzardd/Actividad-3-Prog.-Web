<?php
$action = $_POST['action'] ?? '';

// Ruta de la carpeta actual
$currentFolder = isset($_GET['folder']) ? $_GET['folder'] : './';

// Función para abrir un archivo
function openFile($filename)
{
    if (file_exists($filename)) {
        return file_get_contents($filename);
    }
    return '';
}

// Función para guardar un archivo
function saveFile($filename, $content)
{
    file_put_contents($filename, $content);
}

// Función para borrar un archivo o carpeta
function deleteFile($filename)
{
    if (file_exists($filename)) {
        is_dir($filename) ? deleteFolder($filename) : unlink($filename);
    }
    // Recargar la página
    header("Location: " . $_SERVER['PHP_SELF'] . "?folder=" . $_POST['folder']);
    exit();
}

// Función auxiliar para borrar una carpeta y su contenido (no se puede borrar una carpeta con contenido)
function deleteFolder($folder)
{
    $files = glob($folder . '/*');
    foreach ($files as $file) {
        is_dir($file) ? deleteFolder($file) : unlink($file);
    }
    rmdir($folder);
}

// Función para crear una carpeta
function createFolder($folder)
{
    if (!file_exists($folder)) {
        mkdir($folder, 0777, true);
    }
}

// Función para crear un archivo dentro de una carpeta
function createFile($folder, $filename, $content)
{
    $filePath = $folder . '/' . $filename;
    file_put_contents($filePath, $content);
}

// Obtener la lista de carpetas y archivos en la carpeta actual
$foldersAndFiles = scandir($currentFolder);
$foldersAndFiles = array_diff($foldersAndFiles, array('.', '..'));

// Procesar la acción solicitada
if ($action === 'open') {
    $filename = $_POST['filename'] ?? '';
    $content = openFile($filename);
    echo '<h2>' . $filename . '</h2>';
    echo '<pre>' . $content . '</pre>';
} elseif ($action === 'save') {
    $filename = $_POST['filename'] ?? '';
    $content = $_POST['fileContents'] ?? '';
    saveFile($filename, $content);
} elseif ($action === 'delete') {
    $filename = $_POST['filename'] ?? '';
    deleteFile($filename);
} elseif ($action === 'create_folder') {
    $folder = $_POST['folder'] ?? '';
    createFolder($folder);
} elseif ($action === 'create_file') {
    $folder = $_POST['folder'] ?? '';
    $filename = $_POST['filename'] ?? '';
    $content = $_POST['fileContents'] ?? '';
    createFolder($folder);
    createFile($folder, $filename, $content);
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>File Manager</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/index.css">
</head>

<body>
    <div class="form-container">
        <h1>Notepad - File Manager</h1>

        <h2>
            <?php echo $currentFolder; ?>
        </h2>

        <?php foreach ($foldersAndFiles as $item): ?>
            <?php if (is_dir($currentFolder . '/' . $item)): ?>
                <a href="?folder=<?php echo $currentFolder . '/' . $item; ?>" class="directory">
                    <?php echo $item; ?>
                </a>
                <br>
            <?php else: ?>
                <a href="?folder=<?php echo $currentFolder; ?>&filename=<?php echo $item; ?>" class="directory-item"><?php echo $item; ?></a>
                <br>
            <?php endif; ?>
        <?php endforeach; ?>

        <br>

        <form method="post" action="filehandler.php">

            <div class="form-element">
                <label for="filename">Name</label>
                <input type="text" class="form-control" id="filename" name="filename">
            </div>

            <div class="form-element">
                <label for="fileContents">Contents</label>
                <textarea class="form-control" id="fileContents" name="fileContents" rows="5"></textarea>
            </div>

            <div class="form-element">
                <label for="filename">Folder</label>
                <input type="text" name="folder" value="<?php echo $currentFolder; ?>">
            </div>

            <div class="form-element--buttons">
                <button type="submit" name="action" value="open">Open File</button>
                <button type="submit" name="action" value="save">Save</button>
                <button type="submit" name="action" value="delete">Delete</button>
                <button type="submit" name="action" value="create_folder">Create Folder</button>
                <button type="submit" name="action" value="create_file">Create File</button>
            </div>
        </form>
    </div>
</body>

</html>