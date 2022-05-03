<?php
namespace Payum\Core\Storage;

interface StorageInterface
{
    public function create(): object;

    public function support(object $model): bool;

    /**
     * @throws \Payum\Core\Exception\InvalidArgumentException if not supported model given.
     */
    public function update(object $model): void;

    /**
     * @param object $model
     *
     * @throws \Payum\Core\Exception\InvalidArgumentException if not supported model given.
     */
    public function delete(object $model): void;

    /**
     * @param mixed|IdentityInterface $id
     */
    public function find($id): ?object;

    /**
     * @param array $criteria
     *
     * @return object[]
     */
    public function findBy(array $criteria);

    /**
     * @param object $model
     *
     * @throws \Payum\Core\Exception\InvalidArgumentException if not supported model given.
     *
     * @return IdentityInterface
     */
    public function identify($model);
}
