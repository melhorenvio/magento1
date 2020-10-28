<?php

$installer = $this;
$installer->startSetup();

try {
    $installer->run("
        ALTER TABLE {$this->getTable('melhorenvio/orders')} ADD COLUMN `declarar_conteudo` TINYINT(1) DEFAULT 0 NOT NULL AFTER `chave_nfe`, ADD COLUMN `assegurar_valor` TINYINT(1) DEFAULT 0 NOT NULL AFTER `declarar_conteudo`;
    ");
} catch(Exception $e) {

}

$installer->endSetup();