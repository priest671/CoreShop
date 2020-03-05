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

namespace CoreShop\Component\Core\Order\Processor;

use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;

final class CartTotalProcessor implements CartProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(OrderInterface $cart): void
    {
        $cart->setTotal($cart->getSubtotal(false) + $cart->getAdjustmentsTotal(null, false), false);
        $cart->setTotal($cart->getSubtotal(true) + $cart->getAdjustmentsTotal(null, true), true);
    }
}