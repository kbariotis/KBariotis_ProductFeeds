<?php

class KBariotis_ProductFeeds_Model_Cron
{
	public function exportSkroutz(){

        $enabled = Mage::getStoreConfig('productfeeds/skroutz/enabled');
        if(!$enabled)
            return false;

        $model = Mage::getModel('productfeeds/skroutz');
        $model->exportXml();
    }
	public function exportBestprice(){

        $enabled = Mage::getStoreConfig('productfeeds/bestprice/enabled');
        if(!$enabled)
            return false;

        $model = Mage::getModel('productfeeds/bestprice');
        $model->exportXml();
	} 	
	public function exportSnif(){

        $enabled = Mage::getStoreConfig('productfeeds/snif/enabled');
        if(!$enabled)
            return false;

        $model = Mage::getModel('productfeeds/snif');
        $model->exportXml();
	} 
}