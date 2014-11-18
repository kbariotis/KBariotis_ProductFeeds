<?php

class KBariotis_ProductFeeds_Helper_Feeds
{

    public function getProductCollection()
    {
        $productsCollection = Mage::getModel('catalog/product')
                                  ->getCollection();
        $productsCollection
            ->addAttributeToSelect('*')
            ->addAttributeToFilter(
            'status',
            array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
            )
            ->addUrlRewrite(3);

        return $productsCollection;
    }

    public function addCData($object, $cdata_text)
    {
        $node = dom_import_simplexml($object);
        $no   = $node->ownerDocument;
        $node->appendChild($no->createCDATASection($cdata_text));
    }

    public function getCategoryPath($product)
    {

        $categoryIds = $product->getCategoryIds();

        $categoryPath = '';
        foreach ($categoryIds as $id) {
            $category = Mage::getModel("catalog/category")
                            ->load($id);
            $categoryPath .= '->' . $category->getName();
        }

        return substr($categoryPath, 2);
    }

    public function getFeedsPath($filename) {
        $path = Mage::getStoreConfig('productfeeds/general/path');

        $dir = Mage::getBaseDir() . $path;
        if(!file_exists($dir))
            @mkdir($dir);
        if(!file_exists($dir))
            throw new RuntimeException('Feeds Path could not be created');

        return $dir . DIRECTORY_SEPARATOR . $filename;
    }

    public function getStoreName() {
        $storeName = Mage::getStoreConfig('general/store_information/name');
        if($storeName === '')
            throw new Exception('Store Name must not be empty');

        return $storeName;
    }
}