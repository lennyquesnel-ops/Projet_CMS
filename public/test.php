<?php

echo '<h1>Test chemin serveur</h1>';
echo '<p>PHP : ' . PHP_VERSION . '</p>';
echo '<p>Fichier exécuté : ' . __FILE__ . '</p>';
echo '<p>Dossier courant : ' . getcwd() . '</p>';

phpinfo();