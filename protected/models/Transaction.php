<?php
/**
 * Transaction model class extending TransactionBase generated using gii. This
 * class allows manipulation of the transaction data model.
 *
 * @author Jack Elendil B. Lagare <j.lagare@irri.org>
 */
class Transaction extends TransactionBase {

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return TransactionBase the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}
