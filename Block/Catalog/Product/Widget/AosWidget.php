<?php
/**
 * Copyright © 2015 Magevolve Ltd.
 * See COPYING.txt for license details.
 */

namespace Me\Aos\Block\Catalog\Product\Widget;

use Me\Aos\Block\Catalog\Product\AosProduct;
use Magento\Widget\Block\BlockInterface;
use Magento\Catalog\Block\Product\Widget\Html\Pager;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Catalog\Model\Product;
use Magento\Framework\Pricing\Render as PricingRender;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Me\Aos\Helper\Data as DataHelper;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductsCollection;
use Magento\Framework\Exception\LocalizedException;

/**
 * Products by Quantity Range widget
 * @method ProductsCollection getProductsCollection()
 */
class AosWidget extends AosProduct implements BlockInterface
{
    /**
     * Default value whether show pager or not
     */
    const DEFAULT_SHOW_PAGER = false;

    /**
     * Default value for products per page
     */
    const DEFAULT_PRODUCTS_PER_PAGE = 5;

    /**
     * Default value for products sort by
     */
    const DEFAULT_SORT_BY = 'name';

    /**
     * Default value for products sort direction
     */
    const DEFAULT_SORT_DIRECTION = 'asc';

    /**
     * Name of request parameter for page number value
     */
    const PAGE_VAR_NAME = 'ap';

    /**
     * Instance of pager block
     *
     * @var Pager
     */
    private $pager;

    /**
     * Get number of current page based on query value
     *
     * @return int
     */
    public function getCurrentPage()
    {
        return abs((int)$this->getRequest()->getParam(self::PAGE_VAR_NAME));
    }

    /**
     * Product collection initialize process
     *
     * @return $this|ProductsCollection
     * @throws LocalizedException
     */
    protected function getProductCollection()
    {
        $productIds = $this->productsStock->getProductsIds(
            (int)$this->getQtyFrom(),
            (int)$this->getQtyTo(),
            $this->showConfigurableProducts(),
            $this->showGroupedProducts(),
            $this->showBundleProducts()
        );
        $collection = $this->productCollectionFactory->create();
        $collection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());
        $collection->addAttributeToFilter('entity_id', ['in' => $productIds]);
        $collection = $this->_addProductAttributesAndPrices($collection)
            ->addStoreFilter()
            ->addAttributeToSort($this->getSortBy(), $this->getSortDirection())
            ->setPageSize($this->getPageSize())
            ->setCurPage($this->getCurrentPage());

        return $collection;
    }

    /**
     * Get key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return array_merge(
            parent::getCacheKeyInfo(),
            [
                $this->getProductsPerPage(),
                intval($this->getRequest()->getParam(self::PAGE_VAR_NAME))
            ]
        );
    }

    /**
     * Get if show block title
     *
     * @return string
     */
    public function getShowTitle()
    {
        if ((bool)$this->hasData('show_title')) {
            return true;
        }
        return false;
    }

    /**
     * Retrieve block title
     *
     * @return string
     */
    public function getTitle()
    {
        if (!$this->hasData('title')) {
            return parent::getTitle();
        }
        return $this->getData('title');
    }

    /**
     * Get if show block subtitle
     *
     * @return string
     */
    public function getShowSubTitle()
    {
        if ((bool)$this->hasData('show_subtitle')) {
            return true;
        }
        return false;
    }

    /**
     * Get block subtitle
     *
     * @return string
     */
    public function getSubTitle()
    {
        if ($this->hasData('subtitle') && $this->getShowSubTitle()) {
            return $this->getData('subtitle');
        }

        return '';
    }

    /**
     * Retrieve minimal quantity
     *
     * @return int
     */
    public function getQtyFrom()
    {
        if (!$this->hasData('qty_from')) {
            return parent::getQtyFrom();
        }
        return $this->getData('qty_from');
    }

    /**
     * Retrieve maximal quantity
     *
     * @return int
     */
    public function getQtyTo()
    {
        if (!$this->hasData('qty_to')) {
            return parent::getQtyTo();
        }
        return $this->getData('qty_to');
    }

    /**
     * Retrieve how many products should be displayed
     *
     * @return int
     */
    public function getProductsCount()
    {
        if (!$this->hasData('products_count')) {
            return parent::getProductsCount();
        }
        return (int)$this->getData('products_count');
    }

    /**
     * Retrieve how many products should be displayed
     *
     * @return int
     */
    public function getProductsPerPage()
    {
        if (!$this->hasData('products_per_page')) {
            $this->setData('products_per_page', self::DEFAULT_PRODUCTS_PER_PAGE);
        }
        return (int)$this->getData('products_per_page');
    }

    /**
     * Return flag whether pager need to be shown or not
     *
     * @return bool
     */
    public function showPager()
    {
        if (!$this->hasData('show_pager')) {
            $this->setData('show_pager', self::DEFAULT_SHOW_PAGER);
        }
        return (bool)$this->getData('show_pager');
    }

    /**
     * Return flag whether show configurable products
     *
     * @return bool
     */
    public function showConfigurableProducts()
    {
        if (!$this->hasData('show_configurable')) {
            $this->setData('show_configurable', self::DEFAULT_CONFIGURABLE);
        }
        return (bool)$this->getData('show_configurable');
    }

    /**
     * Return flag whether show grouped products
     *
     * @return bool
     */
    public function showGroupedProducts()
    {
        if (!$this->hasData('show_grouped')) {
            $this->setData('show_grouped', self::DEFAULT_GROUPED);
        }
        return (bool)$this->getData('show_grouped');
    }

    /**
     * Return flag whether show bundle products
     *
     * @return bool
     */
    public function showBundleProducts()
    {
        if (!$this->hasData('show_bundle')) {
            $this->setData('show_bundle', self::DEFAULT_BUNDLE);
        }
        return (bool)$this->getData('show_bundle');
    }

    /**
     * Get sort by
     *
     * @return string
     */
    public function getSortBy()
    {
        if (!$this->hasData('sort_by')) {
            $this->setData('sort_by', self::DEFAULT_SORT_BY);
        }
        return (string)$this->getData('sort_by');
    }

    /**
     * Get sort direction
     *
     * @return string
     */
    public function getSortDirection()
    {
        if (!$this->hasData('sort_direction')) {
            $this->setData('sort_direction', self::DEFAULT_SORT_DIRECTION);
        }
        return (string)$this->getData('sort_direction');
    }

    /**
     * Retrieve how many products should be displayed on page
     *
     * @return int
     */
    private function getPageSize()
    {
        return $this->showPager() ? $this->getProductsPerPage() : $this->getProductsCount();
    }

    /**
     * Render pagination HTML
     *
     * @return string
     * @throws LocalizedException
     */
    public function getPagerHtml()
    {
        if ($this->showPager()) {
            if (!$this->pager) {
                $this->pager = $this->getLayout()->createBlock(
                    'Magento\Catalog\Block\Product\Widget\Html\Pager',
                    'widget.aos.product.list.pager'
                );

                $this->pager->setUseContainer(true)
                    ->setShowAmounts(true)
                    ->setShowPerPage(false)
                    ->setPageVarName(self::PAGE_VAR_NAME)
                    ->setLimit($this->getProductsPerPage())
                    ->setTotalLimit($this->getProductsCount())
                    ->setCollection($this->getProductCollection());
            }
            if ($this->pager instanceof AbstractBlock) {
                return $this->pager->toHtml();
            }
        }
        return '';
    }

    /**
     * Return HTML block with price
     *
     * @param Product $product
     * @param null $priceType
     * @param string $renderZone
     * @param array $arguments
     * @return string
     * @throws LocalizedException
     */
    public function getProductPriceHtml(
        Product $product,
        $priceType = null,
        $renderZone = PricingRender::ZONE_ITEM_LIST,
        array $arguments = []
    ) {
        if (!isset($arguments['zone'])) {
            $arguments['zone'] = $renderZone;
        }
        $arguments['zone'] = isset($arguments['zone'])
            ? $arguments['zone']
            : $renderZone;
        $arguments['price_id'] = isset($arguments['price_id'])
            ? $arguments['price_id']
            : 'old-price-' . $product->getId() . '-' . $priceType;
        $arguments['include_container'] = isset($arguments['include_container'])
            ? $arguments['include_container']
            : true;
        $arguments['display_minimal_price'] = isset($arguments['display_minimal_price'])
            ? $arguments['display_minimal_price']
            : true;

        /** @var \Magento\Framework\Pricing\Render $priceRender */
        $priceRender = $this->getLayout()->getBlock('product.price.render.default');

        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                FinalPrice::PRICE_CODE,
                $product,
                $arguments
            );
        }
        return $price;
    }

    /**
     * Extension helper
     *
     * @return DataHelper
     */
    public function getExtensionHelper()
    {
        return $this->helper;
    }
}
