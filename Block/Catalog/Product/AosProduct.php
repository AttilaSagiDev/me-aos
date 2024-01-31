<?php
/**
 * Copyright © 2016 Magevolve Ltd.
 * See COPYING.txt for license details.
 */

namespace Me\Aos\Block\Catalog\Product;

use Magento\Customer\Model\Context as CustomerContext;
use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\Product\Visibility;
use Me\Aos\Model\ProductsStock;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Catalog\Model\Product;
use Me\Aos\Helper\Data as DataHelper;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Framework\Exception\LocalizedException;

/**
 * Products by Quantity Range block
 */
class AosProduct extends AbstractProduct implements IdentityInterface
{
    /**
     * Default minimal qty
     */
    const DEFAULT_MIN_QTY = 1;

    /**
     * Default maximal qty
     */
    const DEFAULT_MAX_QTY = 10;

    /**
     * Default value for products count that will be shown
     */
    const DEFAULT_PRODUCTS_COUNT = 10;

    /**
     * Default value for showing configurable products
     */
    const DEFAULT_CONFIGURABLE = false;

    /**
     * Default value for showing grouped products
     */
    const DEFAULT_GROUPED = false;

    /**
     * Default value for showing bundle products
     */
    const DEFAULT_BUNDLE = false;

    /**
     * Block title
     *
     * @var string
     */
    private $title;

    /**
     * Minimal quantity
     *
     * @var int
     */
    private $qtyMin;

    /**
     * Maximal quantity
     *
     * @var int
     */
    private $qtyMax;

    /**
     * Products count
     *
     * @var int
     */
    private $productsCount;

    /**
     * @var HttpContext
     */
    private $httpContext;

    /**
     * Catalog product visibility
     *
     * @var Visibility
     */
    protected $catalogProductVisibility;

    /**
     * Product collection factory
     *
     * @var CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var ProductsStock
     */
    protected $productsStock;

    /**
     * Extension helper
     *
     * @var DataHelper
     */
    protected $helper;

    /**
     * @param Context $context
     * @param CollectionFactory $productCollectionFactory
     * @param Visibility $catalogProductVisibility
     * @param ProductsStock $productsStock
     * @param HttpContext $httpContext
     * @param DataHelper $dataHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        CollectionFactory $productCollectionFactory,
        Visibility $catalogProductVisibility,
        ProductsStock $productsStock,
        HttpContext $httpContext,
        DataHelper $dataHelper,
        array $data = []
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->catalogProductVisibility = $catalogProductVisibility;
        $this->productsStock = $productsStock;
        $this->httpContext = $httpContext;
        $this->helper = $dataHelper;
        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * Initialize block's cache
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->addColumnCountLayoutDepend('empty', 6)
            ->addColumnCountLayoutDepend('1column', 5)
            ->addColumnCountLayoutDepend('2columns-left', 4)
            ->addColumnCountLayoutDepend('2columns-right', 4)
            ->addColumnCountLayoutDepend('3columns', 3);

        $this->addData(
            ['cache_lifetime' => 86400, 'cache_tags' => [Product::CACHE_TAG]]
        );
    }

    /**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return [
            'CATALOG_PRODUCT_AOS',
            $this->_storeManager->getStore()->getId(),
            $this->_design->getDesignTheme()->getId(),
            $this->httpContext->getValue(CustomerContext::CONTEXT_GROUP),
            'template' => $this->getTemplate(),
            $this->getProductsCount()
        ];
    }

    /**
     * Product collection initialize process
     *
     * @return $this|ProductCollection
     * @throws LocalizedException
     */
    protected function getProductCollection()
    {
        $productIds = $this->productsStock->getProductsIds(
            (int)$this->getQtyFrom(),
            (int)$this->getQtyTo(),
            self::DEFAULT_CONFIGURABLE,
            self::DEFAULT_GROUPED,
            self::DEFAULT_BUNDLE
        );
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToFilter('entity_id', ['in' => $productIds]);
        $collection = $this->_addProductAttributesAndPrices($collection)
            ->addStoreFilter()
            ->addAttributeToSort('created_at', 'desc')
            ->setPageSize($this->getProductsCount())
            ->setCurPage(1);

        return $collection;
    }

    /**
     * Prepare collection with products
     *
     * @return $this
     * @throws LocalizedException
     */
    protected function _beforeToHtml()
    {
        $this->setProductsCollection($this->getProductCollection());
        return parent::_beforeToHtml();
    }

    /**
     * Set how much product should be displayed at once.
     *
     * @param int $count
     * @return $this
     */
    public function setProductsCount($count)
    {
        $this->productsCount = $count;

        return $this;
    }

    /**
     * Get how much products should be displayed at once.
     *
     * @return int
     */
    public function getProductsCount()
    {
        if (null === $this->productsCount) {
            $this->productsCount = self::DEFAULT_PRODUCTS_COUNT;
        }
        return $this->productsCount;
    }

    /**
     * Get block title
     *
     * @return string
     */
    public function getTitle()
    {
        if (null === $this->title) {
            $this->title = __('Products by Quantity Range');
        }
        return $this->title;
    }

    /**
     * Get minimal quantity
     *
     * @return int
     */
    public function getQtyFrom()
    {
        if (null === $this->qtyMin) {
            $this->qtyMin = self::DEFAULT_MIN_QTY;
        }
        return $this->qtyMin;
    }

    /**
     * Get maximal quantity
     *
     * @return int
     */
    public function getQtyTo()
    {
        if (null === $this->qtyMax) {
            $this->qtyMax = self::DEFAULT_MAX_QTY;
        }
        return $this->qtyMax;
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [Product::CACHE_TAG];
    }
}
