<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Behat\Page\Frontend;

use Behat\Mink\Element\NodeElement;
use CoreShop\Component\Product\Model\ProductUnitDefinitionInterface;

class ProductPage extends AbstractFrontendPage implements ProductPageInterface
{
    public function getRouteName(): string
    {
        return 'coreshop_product_detail';
    }

    public function getContent(): string
    {
        return $this->getDocument()->getContent();
    }

    public function getName(): string
    {
        return $this->getElement('product_name')->getText();
    }

    public function getPrice(): string
    {
        return $this->getElement('product_price')->getText();
    }

    public function getPriceForUnit(string $unit): string
    {
        return $this->getElement('product_unit_price_' . $unit)->getText();
    }

    public function getOriginalPrice(): string
    {
        return $this->getElement('product_original_price')->getText();
    }

    public function getDiscount(): string
    {
        return $this->getElement('product_discount')->getText();
    }

    public function getTaxRate(): string
    {
        return $this->getElement('product_tax_rate')->getText();
    }

    public function getTax(): string
    {
        return $this->getElement('product_tax')->getText();
    }

    public function getQuantityPriceRules(): array
    {
        $element = $this->getElement('product_quantity_price_rules');

        return array_map(
            static function(NodeElement $element) {
                $startFromElement = $element->find('css', '[data-test-product-quantity-price-rule-start]');
                $priceElement = $element->find('css', '[data-test-product-quantity-price-rule-price-inc]');
                $priceExcElement = $element->find('css', '[data-test-product-quantity-price-rule-price-exc]');

                return [
                    'text' => $element->getText(),
                    'startingFrom' => $startFromElement->getText(),
                    'price' => $priceElement->getText(),
                    'priceExcl' => $priceExcElement->getText(),
                ];
            },
            $element->findAll('css', '[data-test-product-quantity-price-rule]')
        );
    }

    public function addToCart(): void
    {
        $this->getElement('add_to_cart')->click();
    }

    public function addToCartWithQuantity(string $quantity): void
    {
        $this->getElement('quantity')->setValue($quantity);
        $this->getElement('add_to_cart')->click();
    }

    public function addToCartInUnit(ProductUnitDefinitionInterface $unit): void
    {
        $this->getElement('unit')->setValue($unit->getId());
        $this->getElement('add_to_cart')->click();
    }

    public function addToCartInUnitWithQuantity(ProductUnitDefinitionInterface $unit, string $quantity): void
    {
        $this->getElement('unit')->setValue($unit->getId());
        $this->getElement('quantity')->setValue($quantity);
        $this->getElement('add_to_cart')->click();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'add_to_cart' => '[data-test-add-to-cart]',
            'quantity' => '[data-test-quantity]',
            'unit' => '[data-test-unit]',
            'product_name' => '[data-test-product-name]',
            'product_price' => '[data-test-product-price]',
            'product_original_price' => '[data-test-product-original-price]',
            'product_discount' => '[data-test-product-discount]',
            'product_tax_rate' => '[data-test-product-tax-rate]',
            'product_tax' => '[data-test-product-tax]',
            'product_unit_price_carton' => '[data-test-product-unit-price-carton]',
            'product_unit_price_palette' => '[data-test-product-unit-price-palette]',
            'product_quantity_price_rules' => '[data-test-product-quantity-price-rules]',
        ]);
    }
}
