<?php

class KBariotis_ProductFeeds_Model_Bestprice
{

    public static function exportXml()
    {

        $helper             = Mage::helper('productfeeds/feeds');
        $productsCollection = $helper->getProductCollection();

        $output = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><store></store>');
        $output->addChild('date', date('Y-m-d H:i'));
        $productsXml = $output->addChild('products');

        foreach ($productsCollection as $product) {
            $productXml = $productsXml->addChild('product');

            $productXml->addChild('productId', $product->getSku());

            $helper->addCData(
                   $productXml->addChild('title'),
                   $product->getName()
            );

            /* <description> node */
            $helper->addCData($productXml->addChild('description'), $product->getDescription());

            /* <link> node */
            $productXml->addChild('url', Mage::getUrl($product->getUrlPath()));

            /* <image> node */
            try {
                $productXml->addChild('image', (string)Mage::helper('catalog/image')
                                                           ->init($product,
                                                                  'image')
                                                           ->__toString());
            } catch (Exception $e) {
                $productXml->addChild('image', "");
            }

            /* <price> node */
            if ($product->getSpecialPrice()) {
                $productXml->addChild('price', $product->getSpecialPrice());
                $productXml->addChild('original_price', $product->getPrice());
            }
            else {
                $productXml->addChild('price', $product->getPrice());
            }

            $productXml->addChild('category_id', $product->getCategoryId());

            $helper->addCData(
                   $productXml->addChild('category_name'),
                   $helper->getCategoryPath($product)
            );

            /* <manufacturer> node */
            $helper->addCData($productXml->addChild('brand'), $product->getAttributeText('manufacturer'));

            $shippingCosts = Mage::getStoreConfig('productfeeds/general/shipping_cost');
            $productXml->addChild('shipping', $shippingCosts);

            $qty = (int)Mage::getModel('cataloginventory/stock_item')
                            ->loadByProduct($product)
                            ->getQty();


            /* <stock> node */
            if ($qty > 0) {
                $productXml->addChild('stock', 'Y');
                $productXml->addChild('availability', 'Διαθέσιμο σε 1-3 ημέρες');
            }
            else {
                $productXml->addChild('stock', 'N');
                $productXml->addChild('availability', 'Διαθέσιμο κατόπιν παραγγελίας');
            }

        }

        $output->asXML($helper->getFeedsPath('bpfeed.xml'));
    }
}