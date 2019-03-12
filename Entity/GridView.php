<?php

namespace Pintushi\Bundle\GridBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Pintushi\Bundle\EntityConfigBundle\Metadata\Annotation\Config;
use Pintushi\Bundle\UserBundle\Entity\AbstractUser;
use Pintushi\Bundle\UserBundle\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Pintushi\Bundle\GridBundle\Repository\GridViewRepository")
 * @Config(
 *      defaultValues={
 *          "ownership"={
 *              "owner_type"="USER",
 *              "owner_field_name"="owner",
 *              "owner_column_name"="user_owner_id",
 *              "organization_field_name"="organization",
 *              "organization_column_name"="organization_id"
 *          },
 *          "security"={
 *              "type"="ACL",
 *              "group_name"="",
 *              "category"="account_management"
 *          }
 *      }
 * )
 * @UniqueEntity(
 *      fields={"name", "owner", "gridName", "organization"},
 *      message="pintushi.grid.gridview.unique"
 * )
 */
class GridView extends AbstractGridView
{
    /**
     * {@inheritdoc}
     *
     * @ORM\OneToMany(
     *      targetEntity="Pintushi\Bundle\GridBundle\Entity\GridViewUser",
     *      mappedBy="gridView",
     *      cascade={"ALL"},
     *      fetch="EXTRA_LAZY"
     * )
     * @ORM\JoinTable(name="pintushi_grid_view_user_rel",
     *     joinColumns={@ORM\JoinColumn(name="id", referencedColumnName="grid_view_id", onDelete="CASCADE")}
     * )
     */
    protected $users;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Pintushi\Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_owner_id", referencedColumnName="id", onDelete="CASCADE")
     * @Assert\NotBlank
     */
    protected $owner;

    /**
     * {@inheritdoc}
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * {@inheritdoc}
     */
    public function setOwner(AbstractUser $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }
}
