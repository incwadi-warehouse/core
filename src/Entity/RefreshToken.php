<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken as BaseRefreshToken;
use Doctrine\ORM\Mapping\Entity;

#[Entity]
#[ORM\Table("refresh_tokens")]
class RefreshToken extends BaseRefreshToken
{
}
