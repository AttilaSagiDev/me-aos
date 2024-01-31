<?php
/**
 * Copyright © 2016 Magevolve Ltd. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Me\Aos\Model;

use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Catalog\Model\ProductFactory;
use Me\Aos\Model\CatalogInventory\ResourceModel\AosStock;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\Bundle\Model\Product\Type as BundleType;
use Magento\Framework\Model\ResourceModel\Iterator;
use Magento\Framework\Exception\LocalizedException;

class ProductsStock extends AbstractModel
{
    /**
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * @var CollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var Configurable
     */
    private $catalogProductTypeConfigurable;

    /**
     * @var Grouped
     */
    private $catalogProductTypeGrouped;

    /**
     * @var BundleType
     */
    private $catalogProductTypeBundle;

    /**
     * @var AosStock
     */
    private $stockFactory;

    /**
     * @var Iterator
     */
    private $resourceIterator;

    /**
     * @var array
     */
    private $productIds = [];

    /**
     * ProductsStock constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param ProductFactory $productFactory
     * @param CollectionFactory $collectionFactory
     * @param Configurable $configurable
     * @param Grouped $grouped
     * @param BundleType $bundle
     * @param AosStock $stockFactory
     * @param Iterator $iterator
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ProductFactory $productFactory,
        CollectionFactory $collectionFactory,
        Configurable $configurable,
        Grouped $grouped,
        BundleType $bundle,
        AosStock $stockFactory,
        Iterator $iterator,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->productFactory = $productFactory;
        $this->productCollectionFactory = $collectionFactory;
        $this->catalogProductTypeConfigurable = $configurable;
        $this->catalogProductTypeGrouped = $grouped;
        $this->catalogProductTypeBundle = $bundle;
        $this->stockFactory = $stockFactory;
        $this->resourceIterator = $iterator;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Get products collection
     *
     * @param $qtyFrom
     * @param $qtyTo
     * @param $showConfigurable
     * @param $showGrouped
     * @param $showBundle
     * @return array
     * @throws LocalizedException
     */
    public function getProductsIds($qtyFrom, $qtyTo, $showConfigurable, $showGrouped, $showBundle)
    {
        /** @var Collection $collection */
        $collection = $this->productCollectionFactory->create()
            ->addStoreFilter()
            ->addAttributeToSelect(['visibility'], 'left');

        $this->stockFactory->addAosLowStockFilter(
            $collection,
            ['qty', 'use_config' => 'use_config_notify_stock_qty'],
            $qtyFrom,
            $qtyTo
        );

        $this->resourceIterator->walk(
            $collection->getSelect(),
            [[$this, 'callbackProductIds']],
            [
                'product' => $this->productFactory->create(),
                'showConfigurable' => $showConfigurable,
                'showGrouped' => $showGrouped,
                'showBundle' => $showBundle,
            ]
        );

        return $this->productIds;
    }

    /**
     * Set parent configurable product Ids
     *
     * @param Product $product
     * @return void
     */
    private function setParentConfigurableProductIds(Product $product)
    {
        $parentConfigurableProductIds = $this->catalogProductTypeConfigurable
            ->getParentIdsByChild($product->getId());
        if (!empty($parentConfigurableProductIds)) {
            $this->productIds = array_merge($this->productIds, $parentConfigurableProductIds);
        }
    }

    /**
     * Set parent grouped product Ids
     *
     * @param Product $product
     * @return void
     */
    private function setParentGroupedProductIds(Product $product)
    {
        $parentGroupedProductIds = $this->catalogProductTypeGrouped
            ->getParentIdsByChild($product->getId());
        if (!empty($parentGroupedProductIds)) {
            $this->productIds = array_merge($this->productIds, $parentGroupedProductIds);
        }
    }

    /**
     * Set parent bundle product Ids
     *
     * @param Product $product
     * @return void
     */
    private function setParentBundleProductIds(Product $product)
    {
        $parentBundleProductIds = $this->catalogProductTypeBundle
            ->getParentIdsByChild($product->getId());
        if (!empty($parentBundleProductIds)) {
            $this->productIds = array_merge($this->productIds, $parentBundleProductIds);
        }
    }

    /**
     * Callback for product Ids
     *
     * @param array $args
     * @return bool
     */
    public function callbackProductIds($args)
    {
        /** @var Product $product */
        $product = clone $args['product'];
        $product->setData($args['row']);
        if (isset($args['showConfigurable']) && $args['showConfigurable']) {
            $this->setParentConfigurableProductIds($product);
        }
        if (isset($args['showGrouped']) && $args['showGrouped']) {
            $this->setParentGroupedProductIds($product);
        }
        if (isset($args['showBundle']) && $args['showBundle']) {
            $this->setParentBundleProductIds($product);
        }
        $this->productIds = array_merge($this->productIds, [0 => $product->getId()]);

        return true;
    }
}
