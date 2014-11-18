<?php

class KBariotis_ProductFeeds_Model_Skroutz
{

    public static function exportXml()
    {

        $helper             = Mage::helper('productfeeds/feeds');
        $productsCollection = $helper->getProductCollection();

        $storeName = $helper->getStoreName();
        $storeName = htmlspecialchars($storeName);
        $storeName = Mage::getModel('catalog/product_url')->formatUrlKey($storeName);

        $output =
            new SimpleXMLElement("<?xml version='1.0' encoding='UTF-8'?><{$storeName}></{$storeName}>");

        $output->addAttribute('name', $storeName);
        $output->addAttribute('url', Mage::getUrl());
        $output->addAttribute('encoding', 'utf8');

        /* <created_at> node */
        $output->addChild('created_at', date('Y-m-d H:i'));
        $productsXml = $output->addChild('products');

        foreach ($productsCollection as $product) {

            /* <product id=""> node */
            $productXml = $productsXml->addChild('product');

            $productXml->addChild('id', $product->getSku());

            /* <name> node */
            $helper->addCData($productXml->addChild('name'), $product->getName());

            /* <description> node */
            $helper->addCData(
                   $productXml->addChild('description'),
                   $product->getDescription()
            );

            /* <link> node */
            $productXml->addChild('productURL', Mage::getUrl($product->getUrlPath()));

            try {
                /* <image> node */
                $imgUrl = str_replace("https:", "http:", (string)Mage::helper('catalog/image')
                                                                     ->init($product, 'image')
                                                                     ->__toString());
                $productXml->addChild('imageURL', $imgUrl);

            } catch (Exception $e) {
                $productXml->addChild('imageURL', "");
            }

            /* <price_with_vat> node */
            $price = $product->getSpecialPrice() ?
                $product->getSpecialPrice() :
                $product->getPrice();
            $productXml->addChild('price_with_vat', number_format($price, 2, '.', ','));

            $helper->addCData(
                   $productXml->addChild('category_name'),
                   $helper->getCategoryPath($product)
            );

            /* <manufacturer> node */
            $helper->addCData($productXml->addChild('manufacturer'), $product->getAttributeText('manufacturer'));

            $qty = (int)Mage::getModel('cataloginventory/stock_item')
                            ->loadByProduct($product)
                            ->getQty();

            /* <availability> node */
            /* <instock> node */
            if ($qty > 0) {
                $productXml->addChild('instock', 'Y');
                $productXml->addChild('availability', 'Σε απόθεμα');
            }
            else {
                $productXml->addChild('instock', 'N');
                $productXml->addChild('availability', '1 έως 3 ημέρες');
            }

            /* <shipping type="" currency=""> node */
            $shippingCosts = Mage::getStoreConfig('productfeeds/general/shipping_cost');
            $shippingXml = $productXml->addChild('shipping', $shippingCosts);
            $shippingXml->addAttribute('type', 'accurate');
            $shippingXml->addAttribute('currency', 'euro');

        }

        $output->asXML($helper->getFeedsPath('skfeed.xml'));
    }
}