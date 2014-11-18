<?php

class KBariotis_ProductFeeds_Model_Snif
{

    public static function exportXml()
    {

        $helper             = Mage::helper('productfeeds/feeds');
        $productsCollection = $helper->getProductCollection();

        $output = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><store></store>');

        $output->addChild('created_at', date('Y-m-d H:i'));
        
        /* <products> node */
        $productsXml = $output->addChild('products');

        foreach($productsCollection as $product) {

            /* <product_id> node */
            $productXml = $productsXml->addChild('product');
            $productXml->addChild('product_id', $product->getSku());

            /* <name> node */
            $helper->addCData($productXml->addChild('title'), $product->getName());

            /* <description> node */
            $helper->addCData(
                 $productXml->addChild('description'),
                 $product->getDescription()
            );

            /* <link> node */
            $productXml->addChild('url', Mage::getUrl($product->getUrlPath()));

            try{
                /* <image> node */
                $productXml->addChild('img', (string)Mage::helper('catalog/image')->init($product,
                                                                                         'image')->__toString());
            }catch (Exception $e) {
                $productXml->addChild('img', "");
            }

            /* <price> node */
            $price = $product->getSpecialPrice() ? $product->getSpecialPrice() : $product->getPrice();
            $productXml->addChild('price',  number_format($price, 2, '.', ','));

            $helper->addCData(
                   $productXml->addChild('category_name'),
                   $helper->getCategoryPath($product)
            );

            /* <manufacturer> node */
            $helper->addCData($productXml->addChild('manufacturer'), $product->getAttributeText('manufacturer'));

            $qty = (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)
                            ->getQty();

            /* <availability> node */
            /* <instock> node */
            if($qty > 0) {
                $productXml->addChild('instock', 'Y');
                $productXml->addChild('availability', 'Σε απόθεμα');
            } else {
                $productXml->addChild('instock', 'N');
                $productXml->addChild('availability', '1 έως 3 ημέρες');
            }

            /* <weight> node */
            $shippingXml = $productXml->addChild('weight', $product->getWeight());

        }

        $output->asXML($helper->getFeedsPath('snfeed.xml'));
    }
}