<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Invoicepdf
 *
 * @author Ea Design
 */
class EaDesign_PdfGenerator_Model_Entity_Invoicepdf extends EaDesign_PdfGenerator_Model_Entity_Pdfgenerator
{

    /**
     * The id of the invoice
     * @var int
     */
    public $invoiceId;
    
    public $templateId;

    public function getTheInvoice()
    {
        $invoice = Mage::getModel('sales/order_invoice')->load($this->invoiceId);
        return $invoice;
    }

    /**
     * Get the invoice id and create the vars for teh invoice
     * @param type $invoiceId The invoice id
     */
    public function getThePdf($invoiceId, $templateId)
    {
        $this->templateId = $templateId;
        $this->invoiceId = $invoiceId;
        $this->setVars(Mage::helper('pdfgenerator')->processAllVars($this->collectVars()));
        return $this->getPdf();
    }

    /**
     * Collect the vars for the template to be processed
     * @return array
     */
    public function collectVars()
    {
        $grandTotal = Mage::getModel('eadesign/entity_totals_grandtotal')
                ->setSource($this->getTheInvoice())->setOrder($this->getTheInvoice()->getOrder())
                ->getTotalsForDisplay();
        $subTotal = Mage::getModel('eadesign/entity_totals_subtotal')
                ->setSource($this->getTheInvoice())->setOrder($this->getTheInvoice()->getOrder())
                ->getTotalsForDisplay();
        $shippingTotal = Mage::getModel('eadesign/entity_totals_shipping')
                ->setSource($this->getTheInvoice())->setOrder($this->getTheInvoice()->getOrder())
                ->getTotalsForDisplay();
        // need to check the tax system 
        $taxTotal = Mage::getModel('eadesign/entity_totals_tax')
                ->setSource($this->getTheInvoice())->setOrder($this->getTheInvoice()->getOrder())
                ->getTotalsForDisplay();
        //need to check the discount system
        $discountTotal = Mage::getModel('eadesign/entity_totals_discount')
                ->setSource($this->getTheInvoice())->setOrder($this->getTheInvoice()->getOrder())
                ->getTotalsForDisplay();

        $leftInfoBlock = Mage::getModel('eadesign/entity_additional_info')
                ->setSource($this->getTheInvoice())
                ->setOrder($this->getTheInvoice()->getOrder())
                ->getTheInfoMergedVariables();

        $vars = array_merge($subTotal, $grandTotal, $shippingTotal, $taxTotal, $discountTotal, $leftInfoBlock);

        return $vars;
    }

}
