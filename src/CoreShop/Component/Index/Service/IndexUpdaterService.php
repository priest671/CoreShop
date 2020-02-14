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

namespace CoreShop\Component\Index\Service;

use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Index\Worker\WorkerInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Pimcore\Model\DataObject\Concrete;
use Psr\Log\InvalidArgumentException;

final class IndexUpdaterService implements IndexUpdaterServiceInterface
{
    private $indexRepository;
    private $workerServiceRegistry;

    public function __construct(RepositoryInterface $indexRepository, ServiceRegistryInterface $workerServiceRegistry)
    {
        $this->indexRepository = $indexRepository;
        $this->workerServiceRegistry = $workerServiceRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function updateIndices($subject): void
    {
        $this->operationOnIndex($subject, 'update');
    }

    /**
     * {@inheritdoc}
     */
    public function removeIndices($subject): void
    {
        $this->operationOnIndex($subject, 'remove');
    }

    /**
     * @param IndexableInterface $subject
     * @param string $operation
     */
    private function operationOnIndex(IndexableInterface $subject, string $operation = 'update'): void
    {
        $indices = $this->indexRepository->findAll();

        foreach ($indices as $index) {
            if (!$index instanceof IndexInterface) {
                continue;
            }

            if (!$this->isEligible($index, $subject)) {
                continue;
            }

            $worker = $index->getWorker();

            if (!$this->workerServiceRegistry->has($worker)) {
                throw new InvalidArgumentException(sprintf('%s Worker not found', $worker));
            }

            /**
             * @var WorkerInterface $worker
             */
            $worker = $this->workerServiceRegistry->get($worker);

            if ($operation === 'update') {
                $worker->updateIndex($index, $subject);
            } else {
                $worker->deleteFromIndex($index, $subject);
            }
        }
    }

    private function isEligible(IndexInterface $index, IndexableInterface $subject): bool
    {
        if (!$subject instanceof Concrete) {
            return false;
        }

        if ($subject->getClass()->getName() !== $index->getClass()) {
            return false;
        }

        return true;
    }
}
