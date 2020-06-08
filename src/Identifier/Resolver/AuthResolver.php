<?php
namespace App\Identifier\Resolver;

use Authentication\Identifier\Resolver\ResolverInterface;
use Cake\ORM\TableRegistry;

class AuthResolver implements ResolverInterface {
    public function find(array $conditions, $type = self::TYPE_AND)
    {
        $table = TableRegistry::getTableLocator()->get('Login');

        $query = $table->query();

        return $query->where(['Pessoas.email' => $_REQUEST['username']], [], true)->contain(['Pessoas'])->first();
    }
}
