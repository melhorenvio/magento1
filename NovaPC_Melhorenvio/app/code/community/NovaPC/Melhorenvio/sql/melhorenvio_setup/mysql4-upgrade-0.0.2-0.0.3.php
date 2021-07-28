<?php

$installer = $this;
$installer->startSetup();

try {
    $installer->run("
        ALTER TABLE {$this->getTable('melhorenvio/orders')} CHANGE `melhorenvio_id` `melhorenvio_id` TEXT;
    ");
} catch(Exception $e) {

}

$installer->endSetup();