<?php
/**
 * Copyright © 2016 Magevolve Ltd. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Me\Aos\Model\CatalogInventory\ResourceModel;

use Magento\CatalogInventory\Model\ResourceModel\Stock;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\DB\Select;
use Magento\Framework\Exception\LocalizedException;

/**
 * Products by Quantity Range resource model
 */
class AosStock extends Stock
{
    /**
     * Add low stock filter to product collection
     *
     * @param Collection $collection
     * @param $fields
     * @param $qtyFrom
     * @param $qtyTo
     * @return $this
     * @throws LocalizedException
     */
    public function addAosLowStockFilter(Collection $collection, $fields, $qtyFrom, $qtyTo)
    {
        $this->_initConfig();
        if ($qtyFrom <= 0) {
            $qtyFrom = 1;
        }
        $connection = $collection->getSelect()->getConnection();
        $conditions = [
            [
                $connection->prepareSqlCondition('invtr.use_config_manage_stock', 1),
                $connection->prepareSqlCondition($this->_isConfigManageStock, 1),
                $connection->prepareSqlCondition('invtr.qty', ['gteq' => $qtyFrom]),
                $connection->prepareSqlCondition('invtr.qty', ['lteq' => $qtyTo]),
            ],
            [
                $connection->prepareSqlCondition('invtr.use_config_manage_stock', 0),
                $connection->prepareSqlCondition('invtr.manage_stock', 1)
            ],
        ];

        $where = [];
        foreach ($conditions as $k => $part) {
            $where[$k] = join(' ' . Select::SQL_AND . ' ', $part);
        }

        $where = '(' . join(') ' . Select::SQL_OR . ' (', $where) . ')';

        $collection->joinTable(
            ['invtr' => 'cataloginventory_stock_item'],
            'product_id = entity_id',
            $fields,
            $where
        );
        return $this;
    }
}
