<?php

class Sale extends AccountRightV2 {
    public function CustomerPayment() {
        
    }

    public function CustomerPaymentCalculateDiscountsFees() {
        
    }

    public function CustomerPaymentRecordWithDiscountsAndFees() {
        
    }

    public function CreditRefund() {
        
    }

    public function CreditSettlement() {
        
    }

    /**
     *  Return all sale invoice types for an AccountRight company file
     *  http://developer.myob.com/api/accountright/v2/sale/invoice/
     *  
     *  @return json      
     */
    public function Invoice() {
        $this->_makeGetRequest('Sale/Invoice');
    }

    /**
     *  Return, update, create and delete item type sale invoices for an AccountRight company file
     *  http://developer.myob.com/api/accountright/v2/sale/invoice/invoice_item/
     *
     *  @return json
     */
    public function InvoiceItem() {
        //GET
        //PUT
        //POST
        //DELETE
        $this->_makeGetRequest('Sale/Invoice/Item');
    }

    public function InvoiceService() {
        
    }

    public function InvoiceProfessional() {
        
    }

    public function InvoiceTimeBilling() {
        
    }

    public function InvoiceMiscellaneous() {
        
    }

    public function InvoiceRenderAsPDF() {
        
    }

    public function Order() {
        
    }

    public function OrderItem() {
        
    }

    public function OrderService() {
        
    }

    public function OrderProfessional() {
        
    }

    public function OrderTimeBilling() {
        
    }

    public function OrderMiscellaneous() {
        
    }

    public function OrderRenderAsPDF() {

    }
}