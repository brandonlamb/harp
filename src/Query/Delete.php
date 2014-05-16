<?php

namespace CL\Luna\Query;

use CL\Atlas\Query;
use CL\Luna\AbstractDbRepo;
use CL\Atlas\SQL\SQL;
use CL\Util\Objects;
use SplObjectStorage;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Delete extends Query\Delete implements SetInterface {

    use ModelQueryTrait;
    use SoftDeleteTrait;

    public function __construct(AbstractDbRepo $store)
    {
        $this
            ->setRepo($store)
            ->from($store->getTable());

        $this->setSoftDelete($store->getSoftDelete());
    }

    public function execute()
    {
        if ($this->getSoftDelete()) {
            return $this->convertToSoftDelete()->execute();
        } else {
            return parent::execute();
        }
    }

    public function convertToSoftDelete()
    {
        $store = $this->getRepo();
        $query = (new Update($store));

        if ($this->getOrder()) {
            $query->setOrder($this->getOrder());
        }

        if ($this->getLimit()) {
            $query->setLimit($this->getLimit());
        }

        if ($this->getJoin()) {
            $query->setJoin($this->getJoin());
        }

        if ($this->getWhere()) {
            $query->setWhere($this->getWhere());
        }

        $query
            ->setTable($this->getTable() ?: $this->getFrom())
            ->set([AbstractDbRepo::SOFT_DELETE_KEY => new SQL('CURRENT_TIMESTAMP')])
            ->where($store->getTable().'.'.AbstractDbRepo::SOFT_DELETE_KEY, null);

        return $query;
    }

    public function setModels(SplObjectStorage $models)
    {
        $ids = Objects::invoke($models, 'getId');
        $this->whereKey($ids);

        return $this;
    }

}