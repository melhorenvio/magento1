<?php

$installer = $this;
$installer->startSetup();

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$codigo = 'comprimento';
$config = array(
    'position' => 1,
    'required' => 1,
    'label'    => 'Comprimento (cm)',
    'type'     => 'int',
    'input'    => 'text',
    'apply_to' => 'simple,bundle,grouped,configurable',
    'default'  => 16,
    'note'     => 'Comprimento da embalagem do produto (Para cálculo de frete, mínimo de 16cm)'
);
$setup->addAttribute('catalog_product', $codigo, $config);

$codigo = 'altura';
$config = array(
    'position' => 1,
    'required' => 1,
    'label'    => 'Altura (cm)',
    'type'     => 'int',
    'input'    => 'text',
    'apply_to' => 'simple,bundle,grouped,configurable',
    'default'  => 2,
    'note'     => 'Altura da embalagem do produto (Para cálculo de frete, mínimo de 2cm)'
);
$setup->addAttribute('catalog_product', $codigo, $config);

$codigo = 'largura';
$config = array(
    'position' => 1,
    'required' => 1,
    'label'    => 'Largura (cm)',
    'type'     => 'int',
    'input'    => 'text',
    'apply_to' => 'simple,bundle,grouped,configurable',
    'default'  => 11,
    'note'     => 'Largura da embalagem do produto (Para cálculo de frete, mínimo de 11cm)'
);
$setup->addAttribute('catalog_product', $codigo, $config);


$prefix = Mage::getConfig()->getTablePrefix();

try {
  $installer->run("
    CREATE TABLE IF NOT EXISTS ".$prefix."melhorenvio_orders (
      id int AUTO_INCREMENT NOT NULL PRIMARY KEY,
      increment_id varchar(60) NOT NULL,
      status enum('Pendente','Gerado','Cancelado', 'Erro') DEFAULT NULL,
      nmr_nf varchar(321) DEFAULT NULL,
      declarar_conteudo TINYINT(1) DEFAULT 0 NOT NULL,
      assegurar_valor TINYINT(1) DEFAULT 0 NOT NULL,
      url_etiqueta varchar(160) DEFAULT NULL,
      chave_nfe varchar(321) DEFAULT NULL,
      order_id varchar(321) DEFAULT NULL,
      melhorenvio_id varchar(321) DEFAULT NULL
    )"
);
}catch(Exception $e){ //do nothing
}


$installer->endSetup();


