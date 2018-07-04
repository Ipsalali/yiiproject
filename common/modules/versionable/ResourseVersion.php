<?php
namespace common\modules\versionable;

class ResourceVersion
{
    /** @Id @Column(type="integer") */
    private $id;

    /** @Column(type="integer") */
    private $entity_table;

    public function __construct(Versionable $resource)
    {
        $this->entity_id = $resource->getResourceId();
        $this->entity_table = $resource->getResourceTable();
    }

}