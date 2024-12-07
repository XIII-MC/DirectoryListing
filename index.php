<?php

function formatSize($bytes): string {

    if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2) . ' GB';
    if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
    if ($bytes >= 1024) return number_format($bytes / 1024, 2) . ' KB';
    if ($bytes > 1) return $bytes . ' bytes';
    if ($bytes == 1) return $bytes . ' byte';

    return '0 bytes';
}

function getLastModified($file): string {
    return date("Y-m-d H:i", filemtime($file));
}

function extractVersion($fileName): string {
    if (preg_match('/\[v([^]]+)\s-\s/', $fileName, $matches)) return $matches[1];
    return 'N/A';
}

function extractArchitecture($fileName): string {
    if (preg_match('/-\s([^]]+)]/', $fileName, $matches)) return $matches[1];
    return 'N/A';
}

function extractLanguage($fileName): string {
    if (preg_match('/\(([^)]+)\)/', $fileName, $matches)) return $matches[1];
    return 'N/A';
}

function extractFileType($fileName): string {
    return strtoupper(pathinfo($fileName, PATHINFO_EXTENSION));
}

function formatDisplayName($fileName): array|string|null {
    $name = preg_replace('/\s*\[.*?]\s*/', '', pathinfo($fileName, PATHINFO_FILENAME));
    return preg_replace('/\s*\(.*?\)\s*/', '', $name);
}

$directory = '.';
$scanned_directory = array_diff(scandir($directory), array('..', '.'));

$filtered_directory = array_filter($scanned_directory, function($item) {
    return !preg_match('/^\./', $item) && !preg_match('/\.(php|html|ico|css)$/', $item);
});

$directories = [];
$files = [];

foreach ($filtered_directory as $item) {
    if (is_dir($item)) $directories[] = $item;
    else $files[] = $item;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>GTeam's Cloud</title>

    <link rel="stylesheet" href="style.css">

</head>
<body class="dark-mode">

    <div class="container">

        <div class="header">

            <h1>GTeam's Cloud</h1>
            <button id="theme-toggle">Toggle Light Mode</button>

        </div>

        <label for="search"></label><input type="text" id="search" placeholder="Search...">

        <table id="directoryTable">

            <thead>

                <tr>

                    <th>Type</th>
                    <th>Name</th>
                    <th>Size</th>
                    <th>Version</th>
                    <th>Language</th>
                    <th>Architecture</th>
                    <th>Last Modified</th>

                </tr>

            </thead>

            <tbody>

                <?php foreach ($directories as $dir): ?>

                    <tr>

                        <td>DIR</td>
                        <td><a href="<?= $dir ?>/"><?= formatDisplayName($dir) ?>/</a></td>
                        <td>-</td>
                        <td>N/A</td>
                        <td><?= extractLanguage($dir) ?></td>
                        <td>N/A</td>
                        <td><?= getLastModified($dir) ?></td>

                    </tr>

                <?php endforeach; ?>

                <?php foreach ($files as $file): ?>

                    <tr>

                        <td><?= extractFileType($file) ?></td>
                        <td><a href="<?= $file ?>"><?= formatDisplayName($file) ?></a></td>
                        <td><?= formatSize(filesize($file)) ?></td>
                        <td><?= extractVersion($file) ?></td>
                        <td><?= extractLanguage($file) ?></td>
                        <td><?= extractArchitecture($file) ?></td>
                        <td><?= getLastModified($file) ?></td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    </div>

    <script>

        document.getElementById('search').addEventListener('input', function() {

            const filter = this.value.toLowerCase();

            document.querySelectorAll('#directoryTable tbody tr').forEach(row => {

                const text = row.cells[1].textContent.toLowerCase();
                row.style.display = text.indexOf(filter) > -1 ? '' : 'none';

            });

        });

        document.getElementById('theme-toggle').addEventListener('click', function() {

            document.body.classList.toggle('dark-mode');
            document.body.classList.toggle('light-mode');
            this.textContent = document.body.classList.contains('dark-mode') ? 'Toggle Light Mode' : 'Toggle Dark Mode';

        });

    </script>

</body>
</html>
